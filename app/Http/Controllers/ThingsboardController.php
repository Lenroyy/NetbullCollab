<?php

namespace App\Http\Controllers;

use App\Models\Actions_Assessments_Question;
use App\Models\Actions_Control;
use App\Models\Api;
use App\Models\Control;
use App\Models\Controls_Sites;
use App\Models\Exposure;
use App\Models\History;
use App\Models\Histories_Assessments;
use App\Models\Histories_Check;
use App\Models\Reading_Rules;
use App\Models\Site;
use App\Models\Sites_Map;
use App\Models\Sites_Maps_Zone;
use App\Models\Thingsboards_Device;
use App\Models\Thingsboards_Readings_Type;
use App\Models\Thingsboards_Device_Reading;
use App\Models\Thingsboards_Device_Reading_Types;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\PeopleController;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ThingsboardController extends Controller
{
    public function processRules($history)
    {
        //Go through all the stored readings on the devices used in this history to work out the final result

        //go through all the controls on this history
        $readings = Thingsboards_Device_Reading::where('history_id', '=', $history)->get();
        foreach($readings as $reading)
        {
            //get all the rules for this reading type
            $rules = Reading_Rules::where('reading_type_id', '=', $reading->reading_type_id)
                                    ->where('archived', '=', 0)
                                    ->orderBy('order', 'asc')
                                    ->get();

            $readingOutcome = "Unknown";
            foreach($rules as $rule)
            {
                if($rule->rule_type == "response")
                {
                    //check to see if this reading hits a match on the response
                    //Lets see if the assessment was completed
                    $assessment = Histories_Assessments::where('history_id', '=', $history)
                                                        ->where('assessment_id', '=', $rule->assessment_id)
                                                        ->get();
                    if(count($assessment) > 0)
                    {
                        //This assessment was completed on this history, lets see if the needed question was asked and the appropriate answer given
                        $question = Actions_Assessments_Question::where('history_assessment_id', '=', $assessment[0]->id)
                                                                    ->where('history_id', '=', $history)
                                                                    ->where('question_id', '=', $rule->question_id)
                                                                    ->where('answer', '=', $rule->answer_id)
                                                                    ->get();
                        if(count($question) > 0)
                        {
                            //the right answer was given to the right question on the right assessment, lets now look at the outcome
                            $outcome = $this->processOutcome($reading, $rule);

                            if($outcome == "ok" OR $outcome == "not ok" OR $outcome == "monitor")
                            {
                                $readingOutcome = $outcome;
                                $reading->outcome = $readingOutcome;
                                break;
                            }
                            else {
                                $reading->reading = $outcome;
                            }
                        }
                    }
                }
                elseif($rule->rule_type == "above")
                {
                    if($reading->reading > $rule->above_max)
                    {
                        $readingOutcome = $this->processOutcome($reading, $rule);
                        break;
                    }
                }
                elseif($rule->rule_type == "below")
                {
                    if($reading->reading < $rule->below_min)
                    {
                        $readingOutcome = $this->processOutcome($reading, $rule);
                        break;
                    }
                }
                else
                {
                    if($reading->reading > $rule->within_range_min && $reading->reading < $rule->within_range_max)
                    {
                        $readingOutcome = $this->processOutcome($reading, $rule);
                        break;
                    }

                }
            }
            if($readingOutcome == "Unknown")
            {
                $reading->outcome = $readingOutcome;
                $reading->save();
            }
        }
        return 1;
    }

    public function processOutcome($reading, $rule)
    {
        /*
            The rules processing engine has found a hit, now we either need to save the outcome or apply a forumla to the stored reading.
            $reading is the full $reading object
            $rule is the full rule object
        */

        if($rule->outcome == "formula")
        {
            $rawFormula = $rule->formula;
            $operator = substr($rawFormula, 0, 1);
            $numerator = TRIM(substr($rawFormula, 1, 100));

            if($operator == "/")
            {
                $outcome = $reading->reading / $numerator;
            }
            elseif($operator == "*")
            {
                $outcome = $reading->reading * $numerator;
            }
            elseif($operator == "-")
            {
                $outcome = $reading->reading - $numerator;
            }
            else
            {
                $outcome = $reading->reading + $numerator;
            }

            //echo "<br>Existing reading is " . $reading->reading . ", applying formula of " . $rule->formula . " to give new reading of " . $outcome;
            $reading->reading = $outcome;
            $reading->save();

            return $reading->reading;
        }
        else
        {
            $reading->outcome = $rule->outcome;
            $reading->save();

            if($rule->outcome == "not ok")
            {
                echo "Moving to log a task";
                //need to log a task for someone to look into this.
                //First lets log the task to the actual user
                $standardDisplay = Controller::standardDisplay();
                $activityController = new ActivityController();
                $peopleController = new PeopleController();

                $subject = "Logged reading not ok";
                $currentDate = date('H:i d-m-Y');
                $description = "A sensor monitoring " . $reading->ReadingType->name . " returned a result which was calculated to be NOT OK at " . $reading->reading . "." . PHP_EOL . PHP_EOL . "The activity occurred at " . $currentDate . " on sensor " . $reading->Device->thingsboard_id . " named " . $reading->Device->name . ".";
                $notes = "The sensor was on the " . $reading->Device->Control->Controls_Type->name . " controller in the " . $reading->History->Zone->name . " zone on the site " . $reading->History->Site->name. "." . PHP_EOL . PHP_EOL . "We reccomend that this item be looked into in more detail.";
                $assignedID = $standardDisplay['profile']->id;
                $priority = "high";
                $site = $reading->History->site_id;

                $activityController->createTask($standardDisplay['profile'], $subject, $description, $notes, $assignedID, $priority, $site);

                //Then lets log a task to the sites building owner
                $builder = $reading->History->Site->builder_id;
                $activityController->createTask($standardDisplay['profile'], $subject, $description, $notes, $builder, $priority, $site);

                //Then lets log a task to the organisation the user is a part of as long as its not the builder
                $memberOf = $peopleController->requestActiveMembership($standardDisplay['profile']);
                if(is_object($memberOf))
                {
                    if($builder != $memberOf->organisation_id)
                    {
                        $assignedID = $memberOf->organisation_id;
                        $notes .= "The worker who was using the control when the issue happened was " . $standardDisplay['profile']->name;

                        $activityController->createTask($standardDisplay['profile'], $subject, $description, $notes, $assignedID, $priority, $site);
                    }
                }

            }

            return $reading->outcome;
        }
    }

    public function login()
    {
        $api = Api::where('application_name', '=', 'thingsboard')->first();
        $api->token = NULL;
        $api->refresh_token = NULL;
        $api->save();

        $authentication = array();
        $authentication['username'] = $api->username;
        $authentication['password'] = $api->password;

        $endpoint = "/auth/login";

        $apiController = new ApiController();

        $login = json_decode($apiController->apiPost($api->id, $endpoint, json_encode($authentication)));

        $api->token = $login->token;
        $api->refresh_token = $login->refreshToken;
        $api->save();


        return 1;
    }

    public function checkAuth()
    {
        $api = Api::where('application_name', '=', 'thingsboard')->first();

        $endpoint = "/tenant/devices?pageSize=1&page=0";

        $apiController = new ApiController();

        $response = json_decode($apiController->apiGetOAuth($api->id, $endpoint));

        if(isset($response->message) && $response->message == "Authentication failed")
        {
            //print_r($response);
            $this->login();
        }
        if(isset($response->errorCode))
        {
            if($response->message == "Token has expired")
            {
                //not authenticated, need to reauthenticate
                $this->login();
                $this->checkAuth();
                //print_r($response);
            }
        }
        return 1;
    }

    public function retrieveDevices()
    {
        $checkAuth = $this->checkAuth();
        $apiController = new ApiController();

        $api = Api::where('application_name', '=', 'thingsboard')->first();

        $endpoint = "/tenant/devices?pageSize=100&page=0";
        $pages = $apiController->apiGetTBPages($api->id, $endpoint);

        $x = 0;
        $devices = 0;
        $updated = 0;
        $created = 0;

        while($x < $pages)
        {
            $endpoint = "/tenant/devices?pageSize=100&page=" . $x;

            $response = json_decode($apiController->apiGetOAuth($api->id, $endpoint));

            foreach($response->data as $d)
            {
                //find out if the device already exists
                $checkDevice = Thingsboards_Device::where('thingsboard_id', '=', $d->id->id)->count();
                if($checkDevice == 0)
                {
                    $device = new Thingsboards_Device();
                    $device->archived = 0;
                    $device->thingsboard_id = $d->id->id;
                    $created++;
                }
                else
                {
                    $device = Thingsboards_Device::where('thingsboard_id', '=', $d->id->id)->first();
                    $updated++;
                }
                $device->name = $d->name;
                $device->type = $d->type;

                $device->save();
                $this->retrieveDeviceReadingTypes($device);
                $devices++;
            }
            $x++;
        }

        $return["devices"] = $devices;
        $return["created"] = $created;
        $return["updated"] = $updated;

        return json_encode($return);
    }

    public function testPage()
    {


        return view('people.testLayout', [

        ]);
    }

    public function retrieveDeviceReadingTypes($device)
    {
        $checkDevice = Thingsboards_Device_Reading_Types::where('device_id', '=', $device->id)->count();
        if($checkDevice == 0)
        {
            $apiController = new ApiController();
            $checkAuth = $this->checkAuth();

            $api = Api::where('application_name', '=', 'thingsboard')->first();

            $startTime = abs(strtotime("now"));
            $endTime = abs(strtotime("-1 week"));

            $entries = array();
            $e = 0;
            $endPoint = "/plugins/telemetry/DEVICE/" . $device->thingsboard_id . "/values/timeseries?startTs=" . $startTime . "&endTs=" . $endTime . "&agg=NONE";
            $response = json_decode($apiController->apiGetOAuth($api->id, $endPoint));

            foreach($response as $key => $value)
            {
                if($key != "occupancy")
                {
                    //check to see if this reading type already exists
                    $check = Thingsboards_Readings_Type::where('name', '=', $key)->count();
                    if($check == 0)
                    {
                        //create the reading type
                        $newType = new Thingsboards_Readings_Type();
                        $newType->name = $key;
                        $newType->save();

                    }
                    foreach($value as $v)
                    {
                        $readingType = Thingsboards_Readings_Type::where('name', '=', $key)->first();

                        $check = Thingsboards_Device_Reading_Types::where('device_id', '=', $device->id)
                                                                    ->where('reading_type_id', '=', $readingType->id)
                                                                    ->count();
                        if($check == 0)
                        {

                            $entry = new Thingsboards_Device_Reading_Types();
                            $entry->device_id = $device->id;
                            $entry->reading_type_id = $readingType->id;
                            $entry->calculation = "none";

                            $entry->save();
                        }
                    }
                }
            }
        }
        return 1;
    }

    public function retrieveHistoryReadings($history)
    {
        $apiController = new ApiController();
        $checkAuth = $this->checkAuth();

        $api = Api::where('application_name', '=', 'thingsboard')->first();

        if(!is_object($history))
        {
            $history = History::find($history);
        }

        if(is_object($history))
        {
            $startTime = strtotime($history->time_start)*1000;
            $endTime = strtotime($history->time_end)*1000;

            //go through all the controls on this history
            $controls = Actions_Control::distinct('control_id')->where('history_id', '=', $history->id)->get();
            foreach($controls as $control)
            {
                $devices = Thingsboards_Device::where('control_id', '=', $control->control_id)->get();
                foreach($devices as $device)
                {
                    //get all the keys / reading types on this device
                    $readingTypes = Thingsboards_Device_Reading_Types::where('device_id', '=', $device->id)->get();
                    foreach($readingTypes as $readingType)
                    {
                        if(is_object($readingType->ReadingType))
                        {
                            $endPoint = "/plugins/telemetry/DEVICE/" . $device->thingsboard_id . "/values/timeseries?limit=10000&agg=NONE&orderBy=DESC&useStrictDataTypes=false&keys=" . $readingType->ReadingType->name . "&startTs=" . $startTime . "&endTs=" . $endTime;

                            $response = json_decode($apiController->apiGetOAuth($api->id, $endPoint));

                            foreach($response as $v)
                            {
                                foreach($v as $value)
                                {
                                    $check = Thingsboards_Device_Reading::where('device_id', '=', $device->id)
                                                                            ->where('history_id', '=', $history->id)
                                                                            ->where('reading_timestamp', '=', $value->ts)
                                                                            ->where('reading_type_id', '=', $readingType->reading_type_id)
                                                                            ->count();
                                    if($check == 0)
                                    {
                                        $entry = new Thingsboards_Device_Reading();
                                        $entry->device_id = $device->id;
                                        $entry->history_id = $history->id;
                                        $entry->reading = $value->value;
                                        $entry->reading_type_id = $readingType->id;
                                        $entry->reading_timestamp = $value->ts;
                                        $entry->control_id = $control->control_id;
                                        $entry->type = "raw";

                                        $entry->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $this->activityStorage($history);
            $this->processRules($history->id);
        }

        return 1;
    }

    public function activityStorage($history)
    {
        //work out what type of storage we want on this device / monitor for a particular activity / history

        //go through all the controls on this history
        $controls = Actions_Control::distinct('control_id')->where('history_id', '=', $history->id)->get();
        foreach($controls as $control)
        {
            $devices = Thingsboards_Device::where('control_id', '=', $control->control_id)->get();
            foreach($devices as $device)
            {
                //get all the reading types
                $readingTypes = Thingsboards_Device_Reading_Types::where('device_id', '=', $device->id)->get();
                foreach($readingTypes as $rType)
                {
                    //send off the reading types for calculation based on their type of calculation
                    if($rType->calculation == "none")
                    {
                        $this->readings_none($device, $rType, $history);
                    }
                    if($rType->calculation == "average")
                    {
                        $this->readings_average($device, $rType, $history);
                    }
                    if($rType->calculation == "sum")
                    {
                        $this->readings_sum($device, $rType, $history);
                    }
                    if($rType->calculation == "first")
                    {
                        $this->first_reading($device, $rType, $history);
                    }
                    if($rType->calculation == "last")
                    {
                        $this->last_reading($device, $rType, $history);
                    }
                    if($rType->calculation == "perHour")
                    {
                        $this->per_hour_reading($device, $rType, $history);
                    }
                    if($rType->calculation == "perMinute")
                    {
                        $this->per_minute_reading($device, $rType, $history);
                    }
                    if($rType->calculation == "highest")
                    {
                        $this->highest_reading($device, $rType, $history);
                    }
                    if($rType->calculation == "lowest")
                    {
                        $this->lowest_reading($device, $rType, $history);
                    }
                }
            }
        }
    }

    public function readings_none($device, $readingType, $history)
    {
        $readings = Thingsboards_Device_Reading::where('device_id', '=', $device->id)
                                                ->where('history_id', '=', $history->id)
                                                ->where('reading_type_id', '=', $readingType->reading_type_id)
                                                ->where('type', '=', 'raw')
                                                ->delete();
        return 1;

    }

    public function readings_average($device, $readingType, $history)
    {
        $total = 0;
        $x = 0;
        $readings = Thingsboards_Device_Reading::where('device_id', '=', $device->id)
                                                ->where('history_id', '=', $history->id)
                                                ->where('reading_type_id', '=', $readingType->reading_type_id)
                                                ->where('type', '=', 'raw')
                                                ->get();
        foreach($readings as $reading)
        {
            if($reading->reading == "true")
            {
                $reading->reading = 1;
                $reading->save();
            }
            elseif($reading->reading == "false")
            {
                $reading->reading = 0;
                $reading->save();
            }


            $total = $total + $reading->reading;
            $x++;
        }

        $readings = Thingsboards_Device_Reading::where('device_id', '=', $device->id)
                                                ->where('history_id', '=', $history->id)
                                                ->where('reading_type_id', '=', $readingType->reading_type_id)
                                                ->where('type', '=', 'raw')
                                                ->delete();

        if($x > 0)
        {
            $average = $total / $x;
        }
        else
        {
            $average = 0;
        }


        $storedReading = new Thingsboards_Device_Reading();

        $storedReading->device_id = $device->id;
        $storedReading->history_id = $history->id;
        $storedReading->reading = $average;
        $storedReading->reading_type_id = $readingType->reading_type_id;
        $storedReading->reading_timestamp = time();
        $storedReading->control_id = $device->control_id;
        $storedReading->type = "calculated";
        $storedReading->save();

        return 1;
    }

    public function readings_sum($device, $readingType, $history)
    {
        $total = 0;
        $readings = Thingsboards_Device_Reading::where('device_id', '=', $device->id)
                                                ->where('history_id', '=', $history->id)
                                                ->where('reading_type_id', '=', $readingType->reading_type_id)
                                                ->where('type', '=', 'raw')
                                                ->get();
        foreach($readings as $reading)
        {
            if($reading->reading == "true")
            {
                $reading->reading = 1;
                $reading->save();
            }
            elseif($reading->reading == "false")
            {
                $reading->reading = 0;
                $reading->save();
            }
            $total = $total + $reading->reading;
        }

        $readings = Thingsboards_Device_Reading::where('device_id', '=', $device->id)
                                                ->where('history_id', '=', $history->id)
                                                ->where('reading_type_id', '=', $readingType->reading_type_id)
                                                ->where('type', '=', 'raw')
                                                ->delete();

        $storedReading = new Thingsboards_Device_Reading();

        $storedReading->device_id = $device->id;
        $storedReading->history_id = $history->id;
        $storedReading->reading = $total;
        $storedReading->reading_type_id = $readingType->reading_type_id;
        $storedReading->reading_timestamp = time();
        $storedReading->control_id = $device->control_id;
        $storedReading->type = "calculated";
        $storedReading->save();

        return 1;
    }

    public function first_reading($device, $readingType, $history)
    {
        $reading = Thingsboards_Device_Reading::where('device_id', '=', $device->id)
                                                ->where('history_id', '=', $history->id)
                                                ->where('reading_type_id', '=', $readingType->reading_type_id)
                                                ->where('type', '=', 'raw')
                                                ->orderBy('reading_timestamp', 'asc')
                                                ->first();
        if(is_object($reading))
        {
            if($reading->reading == "true")
            {
                $reading->reading = 1;
                $reading->save();
            }
            elseif($reading->reading == "false")
            {
                $reading->reading = 0;
                $reading->save();
            }

            $storedReading = new Thingsboards_Device_Reading();

            $storedReading->device_id = $device->id;
            $storedReading->history_id = $history->id;
            $storedReading->reading = $reading->reading;
            $storedReading->reading_type_id = $readingType->reading_type_id;
            $storedReading->reading_timestamp = time();
            $storedReading->control_id = $device->control_id;
            $storedReading->type = "calculated";
            $storedReading->save();

            $readings = Thingsboards_Device_Reading::where('device_id', '=', $device->id)
                                                ->where('history_id', '=', $history->id)
                                                ->where('reading_type_id', '=', $readingType->reading_type_id)
                                                ->where('type', '=', 'raw')
                                                ->delete();
        }

        return 1;
    }

    public function last_reading($device, $readingType, $history)
    {
        $reading = Thingsboards_Device_Reading::where('device_id', '=', $device->id)
                                                ->where('history_id', '=', $history->id)
                                                ->where('reading_type_id', '=', $readingType->reading_type_id)
                                                ->where('type', '=', 'raw')
                                                ->orderBy('reading_timestamp', 'desc')
                                                ->first();

        if(is_object($reading))
        {
            if($reading->reading == "true")
            {
                $reading->reading = 1;
                $reading->save();
            }
            elseif($reading->reading == "false")
            {
                $reading->reading = 0;
                $reading->save();
            }

            $storedReading = new Thingsboards_Device_Reading();

            $storedReading->device_id = $device->id;
            $storedReading->history_id = $history->id;
            $storedReading->reading = $reading->reading;
            $storedReading->reading_type_id = $readingType->reading_type_id;
            $storedReading->reading_timestamp = time();
            $storedReading->control_id = $device->control_id;
            $storedReading->type = "calculated";
            $storedReading->save();

            $readings = Thingsboards_Device_Reading::where('device_id', '=', $device->id)
                                                        ->where('history_id', '=', $history->id)
                                                        ->where('reading_type_id', '=', $readingType->reading_type_id)
                                                        ->where('type', '=', 'raw')
                                                        ->delete();
        }

        return 1;
    }

    public function highest_reading($device, $readingType, $history)
    {
        $highest = 0;
        $readings = Thingsboards_Device_Reading::where('device_id', '=', $device->id)
                                                ->where('history_id', '=', $history->id)
                                                ->where('reading_type_id', '=', $readingType->reading_type_id)
                                                ->where('type', '=', 'raw')
                                                ->get();
        foreach($readings as $reading)
        {
            if($reading->reading == "true")
            {
                $reading->reading = 1;
                $reading->save();
            }
            elseif($reading->reading == "false")
            {
                $reading->reading = 0;
                $reading->save();
            }
            if($reading->reading > $highest)
            {
                $highest = $reading->reading;
            }
        }

        $readings = Thingsboards_Device_Reading::where('device_id', '=', $device->id)
                                                ->where('history_id', '=', $history->id)
                                                ->where('reading_type_id', '=', $readingType->reading_type_id)
                                                ->where('type', '=', 'raw')
                                                ->delete();

        $storedReading = new Thingsboards_Device_Reading();

        $storedReading->device_id = $device->id;
        $storedReading->history_id = $history->id;
        $storedReading->reading = $highest;
        $storedReading->reading_type_id = $readingType->reading_type_id;
        $storedReading->reading_timestamp = time();
        $storedReading->control_id = $device->control_id;
        $storedReading->type = "calculated";
        $storedReading->save();

        return 1;
    }

    public function lowest_reading($device, $readingType, $history)
    {
        $lowest = 999999999999;
        $readings = Thingsboards_Device_Reading::where('device_id', '=', $device->id)
                                                ->where('history_id', '=', $history->id)
                                                ->where('reading_type_id', '=', $readingType->reading_type_id)
                                                ->where('type', '=', 'raw')
                                                ->get();
        foreach($readings as $reading)
        {
            if($reading->reading == "true")
            {
                $reading->reading = 1;
                $reading->save();
            }
            elseif($reading->reading == "false")
            {
                $reading->reading = 0;
                $reading->save();
            }
            if($reading->reading < $lowest)
            {
                $lowest = $reading->reading;
            }
        }

        $readings = Thingsboards_Device_Reading::where('device_id', '=', $device->id)
                                                ->where('history_id', '=', $history->id)
                                                ->where('reading_type_id', '=', $readingType->reading_type_id)
                                                ->where('type', '=', 'raw')
                                                ->delete();

        $storedReading = new Thingsboards_Device_Reading();

        $storedReading->device_id = $device->id;
        $storedReading->history_id = $history->id;
        $storedReading->reading = $lowest;
        $storedReading->reading_type_id = $readingType->reading_type_id;
        $storedReading->reading_timestamp = time();
        $storedReading->control_id = $device->control_id;
        $storedReading->type = "calculated";
        $storedReading->save();

        return 1;
    }

    public function per_minute_reading($device, $readingType, $history)
    {
        $total = 0;

        $readings = Thingsboards_Device_Reading::where('device_id', '=', $device->id)
                                                ->where('history_id', '=', $history->id)
                                                ->where('reading_type_id', '=', $readingType->reading_type_id)
                                                ->where('type', '=', 'raw')
                                                ->get();
        foreach($readings as $reading)
        {
            if($reading->reading == "true")
            {
                $reading->reading = 1;
                $reading->save();
            }
            elseif($reading->reading == "false")
            {
                $reading->reading = 0;
                $reading->save();
            }


            $total = $total + $reading->reading;
        }

        //Now we need to work out how many minutes are in this data set.
        $minutes = $this->calcHistoryTime($history->id, "minutes");
        if($minutes == 0)
        {
            $minutes = 1;
        }

        $average = $total / $minutes;

        $readings = Thingsboards_Device_Reading::where('device_id', '=', $device->id)
                                                ->where('history_id', '=', $history->id)
                                                ->where('reading_type_id', '=', $readingType->reading_type_id)
                                                ->where('type', '=', 'raw')
                                                ->delete();




        $storedReading = new Thingsboards_Device_Reading();

        $storedReading->device_id = $device->id;
        $storedReading->history_id = $history->id;
        $storedReading->reading = $average;
        $storedReading->reading_type_id = $readingType->reading_type_id;
        $storedReading->reading_timestamp = time();
        $storedReading->control_id = $device->control_id;
        $storedReading->type = "calculated";
        $storedReading->save();

        return 1;
    }

    public function per_hour_reading($device, $readingType, $history)
    {
        $total = 0;

        $readings = Thingsboards_Device_Reading::where('device_id', '=', $device->id)
                                                ->where('history_id', '=', $history->id)
                                                ->where('reading_type_id', '=', $readingType->reading_type_id)
                                                ->where('type', '=', 'raw')
                                                ->get();
        foreach($readings as $reading)
        {
            if($reading->reading == "true")
            {
                $reading->reading = 1;
                $reading->save();
            }
            elseif($reading->reading == "false")
            {
                $reading->reading = 0;
                $reading->save();
            }


            $total = $total + $reading->reading;
            $x++;
        }

        //Now we need to work out how many minutes are in this data set.
        $hours = $this->calcHistoryTime($history->id, "hours");
        if($hours == 0)
        {
            $hours = 1;
        }

        $average = $total / $hours;

        $readings = Thingsboards_Device_Reading::where('device_id', '=', $device->id)
                                                ->where('history_id', '=', $history->id)
                                                ->where('reading_type_id', '=', $readingType->reading_type_id)
                                                ->where('type', '=', 'raw')
                                                ->delete();




        $storedReading = new Thingsboards_Device_Reading();

        $storedReading->device_id = $device->id;
        $storedReading->history_id = $history->id;
        $storedReading->reading = $average;
        $storedReading->reading_type_id = $readingType->reading_type_id;
        $storedReading->reading_timestamp = time();
        $storedReading->control_id = $device->control_id;
        $storedReading->type = "calculated";
        $storedReading->save();

        return 1;
    }

    public function refreshMonitors()
    {
        $standardDisplay = Controller::standardDisplay();
        $breadcrumbs = Controller::createBreadcrumbs("Run Integration", "Fetch monitors");

        $tasks = array();
        $tasks[1] = "fetchMonitors";

        return view('integrations.runAPICall', [
            'breadcrumbs' => $breadcrumbs,
            'standardDisplay' => $standardDisplay,
            'tasks' => $tasks,
        ]);
    }

    public function getMySites()
    {
        $standardDisplay = $this->standardDisplay();
        $array = array();
        $x = 0;

        //find all the sites where the user is the primary contact
        $sites = Site::where('primary_contact_id', '=', $standardDisplay['profile']->id)
                        ->where('archived', '=', 0)
                        ->orderBy('name', 'asc')
                        ->get();
        foreach($sites as $site)
        {
            //go through all activities in the past 7 days and see if we can find issues in the readings
            $outcome = "ok";
            $activity = "0";

            $today = date('Y-m-d' . " 23:59:59");
            $lastWeek = date("Y-m-d H:i:s", strtotime("-7 days"));

            $histories = History::where('site_id', '=', $site->id)
                                    ->whereBetween('created_at', [$lastWeek, $today])
                                    ->get();
            foreach($histories as $history)
            {
                //go through all the readings on this history
                $readings = Thingsboards_Device_Reading::where('history_id', '=', $history->id)
                                                        ->where('type', '=', 'calculated')
                                                        ->get();
                foreach($readings as $reading)
                {
                    if($reading->outcome == "not ok")
                    {
                        $outcome = "not ok";
                        $activity = $history->id;
                    }
                    if($reading->outcome == "monitor")
                    {
                        $outcome = "monitor";
                        $activity = $history->id;
                    }
                }
            }

            $array[$x]['siteID'] = $site->id;
            $array[$x]['site'] = $site->name;
            $array[$x]['outcome'] = $outcome;
            $array[$x]['history'] = $activity;
            $x++;
        }

        return json_encode($array);
    }

    public function getBuildersSites()
    {
        $standardDisplay = $this->standardDisplay();
        $array = array();
        $x = 0;

        $peopleController = new PeopleController();
        $memberOf = $peopleController->requestActiveMembership($standardDisplay['profile']);

        if(is_object($memberOf))
        {
            //find all the sites where the user is the primary contact
            $sites = Site::where('builder_id', '=', $memberOf->organisation_id)
                            ->where('archived', '=', 0)
                            ->orderBy('name', 'asc')
                            ->get();
            foreach($sites as $site)
            {
                //go through all activities in the past 7 days and see if we can find issues in the readings
                $outcome = "ok";
                $activity = "0";

                $today = date('Y-m-d' . " 23:59:59");
                $lastWeek = date("Y-m-d H:i:s", strtotime("-7 days"));

                $histories = History::where('site_id', '=', $site->id)
                                        ->whereBetween('created_at', [$lastWeek, $today])
                                        ->get();
                foreach($histories as $history)
                {
                    //go through all the readings on this history
                    $readings = Thingsboards_Device_Reading::where('history_id', '=', $history->id)
                                                    ->where('type', '=', 'calculated')
                                                    ->get();
                    foreach($readings as $reading)
                    {
                        if($reading->outcome == "not ok")
                        {
                            $outcome = "not ok";
                            $activity = $history->id;
                        }
                        if($reading->outcome == "monitor")
                        {
                            $outcome = "monitor";
                            $activity = $history->id;
                        }
                    }
                }

                $array[$x]['siteID'] = $site->id;
                $array[$x]['site'] = $site->name;
                $array[$x]['outcome'] = $outcome;
                $array[$x]['history'] = $activity;
                $x++;
            }
        }

        return json_encode($array);
    }

    public function test()
    {
        $this->retrieveHistoryReadings(20);
    }

    public function calculateExposures($profile, $from)
    {
        /*
            This function calculates the total exposures from all readings this person has had
            In the last 7 days.
            $profile is the full Profile object
            $from is finding out where the calculation request has come from as we may or may not need to log tasks as a result of this calculation, it it
            has come logActivity then it will log tasks when needed
        */

        $array = array();
        $x = 0;
        $today = date('Y-m-d' . " 23:59:59");
        $lastWeek = date("Y-m-d H:i:s", strtotime("-7 days"));

        $historiesArray = array();
        $h = 0;
        $histories = History::where('profiles_id', '=', $profile->id)
                                ->whereBetween('created_at', [$lastWeek, $today])
                                ->orderBy('created_at', 'asc')
                                ->get();
        foreach($histories as $history)
        {
            $readingArray = array();
            $r = 0;

            $readings = Thingsboards_Device_Reading::where('history_id', '=', $history->id)->get();
            foreach($readings as $reading)
            {
                $readingArray[$r]['readingType'] = $reading->reading_type_id;
                $readingArray[$r]['reading'] = $reading->reading;
                $readingArray[$r]['timestamp'] = $reading->reading_timestamp;
                $r++;
            }

            $historiesArray[$h]['history'] = $history->id;
            $historiesArray[$h]['readings'] = $readingArray;
            $h++;
        }

        //First we need to loop through all the different reading types
        $readingTypes = Thingsboards_Readings_Type::get();
        foreach($readingTypes as $type)
        {
            //Now go through all histories for this person over the past 7 days and foreach of the reading types, work out whether the exposures were
            //ok in total.  Outcome per reading type is either Ok, Not Ok, Monitor or Unknown
            $worstOutcome = "unknown";
            $exposures = Exposure::where('reading_type_id', '=', $type->id)
                                    ->where('archived', '=', 0)
                                    ->get();
            foreach($exposures as $exposure)
            {
                //now work out from the exposure how to go about calculating the outcome of the total exposures
                if($exposure->time_period == "day")
                {
                    $worstOutcome = $this->calculateDailyExposure($historiesArray, $type->id, $exposure->level);
                }
                if($exposure->time_period == "week")
                {
                    $worstOutcome = $this->calculateWeeklyExposure($historiesArray, $type->id, $exposure->level);
                }
            }
            //include an overall outcome based on the worst result
            $array[$x]['type'] = $type->name;
            $array[$x]['outcome'] = $worstOutcome;
            $x++;

            if($worstOutcome == "not ok" && $from=="logActivity")
            {
                //check the alert timestamp to see whether we need should log an alert
                $time = strtotime("-7 days");
                if($profile->lastAlert < $time)
                {
                    //Then lets log a task to the person
                    $activityController = new ActivityController();

                    $subject = "Detected high exposure";
                    $currentDate = date('H:i d-m-Y');
                    $description = "Whilst monitoring controls that you have been using, we have detected total exposure levels that are too high.  Check your exposures in the system activities.";
                    $notes = NULL;
                    $assignedID = $profile->id;
                    $priority = "high";
                    $site = NULL;

                    $activityController->createTask($profile, $subject, $description, $notes, $assignedID, $priority, $site);

                    //Then lets log a task to the organisation the user is a part of as long as its not the builder
                    $memberOf = $peopleController->requestActiveMembership($profile);
                    if(is_object($memberOf))
                    {
                        $assignedID = $memberOf->organisation_id;
                        $description = "Whilst monitoring controls that $profile->name has been using, we have detected total exposure levels that are too high.  Check total exposures in the system activities.";

                        $activityController->createTask($profile, $subject, $description, $notes, $assignedID, $priority, $site);

                    }
                }
            }
        }

        return $array;
    }

    public function calculateDailyExposure($histories, $readingType, $exposure)
    {
        /*
            This function goes through each day in the histories array, looking for the reading type and summing them up to see what the outcome
            is based on the settings in $exposure
            $histories is the historiesArray generated from calculate exposures
            $readingType is the reading_type_id we are checking against
            $exposure is the level of the Exposure object, so the total maximum allowable limit
        */
        $days = 7;
        $worstOutcome = array();

        while($days > -1)
        {
            $timeStart = strtotime("-" . $days . " days");
            $days = $days-1;
            $timeEnd = strtotime("-" . $days . " days");

            $todaysTotal = 0;

            foreach($histories as $history)
            {
                foreach($history['readings'] as $reading)
                {
                    if($reading['readingType'] == $readingType)
                    {
                        if($reading['timestamp'] > $timeStart && $reading['timestamp'] < $timeEnd)
                        {
                            //add the reading to the total
                            $todaysTotal = $todaysTotal + $reading['reading'];
                        }
                        else
                        {
                            break;
                        }
                    }
                }
            }

            if($todaysTotal > $exposure)
            {
                $worstOutcome = "not ok";
                break;
            }

            if($todaysTotal == $exposure)
            {
                $worstOutcome = "monitor";
            }

            if($todaysTotal < $exposure && $worstOutcome != "monitor")
            {
                $worstOutcome = "ok";
            }

            if($todaysTotal == 0 && $worstOutcome != "monitor" && $worstOutcome != "ok")
            {
                $worstOutcome = "unknown";
            }
        }

        return $worstOutcome;
    }

    public function calculateWeeklyExposure($histories, $readingType, $exposure)
    {
        /*
            This function goes through each day in the histories array, looking for the reading type and summing them up to see what the outcome
            is based on the settings in $exposure
            $histories is the historiesArray generated from calculate exposures
            $readingType is the reading_type_id we are checking against
            $exposure is the level of the Exposure object, so the total maximum allowable limit
        */
        $worstOutcome = "unknown";

        $total = 0;

        foreach($histories as $history)
        {
            foreach($history['readings'] as $reading)
            {
                if($reading['readingType'] == $readingType)
                {
                    //add the reading to the total
                    $total = $total + $reading['reading'];
                }
            }
        }

        if($total > $exposure)
        {
            $worstOutcome = "not ok";
        }
        elseif($total == $exposure)
        {
            $worstOutcome = "monitor";
        }
        elseif($total < $exposure)
        {
            $worstOutcome = "ok";
        }
        else
        {
            $worstOutcome = "unknown";
        }

        return $worstOutcome;
    }

    public function exposureDetails($person, $type, $range)
    {
        /*
            This function returns an array of the full amount of exposure each day for a person for a reading type over a date range
            $person is a Profile->id
            $type is a reading_type_id
            $range is an array with the start date and the end day in format YYYY-mm-dd with a start and end entry
        */

        $array = array();
        $x = 0;

        $readingType = Thingsboards_Readings_Type::where('name', '=', $type)->first();

        $start = $range['start'];
        $end = $range['end'] . " 23:59:59";

        while($start < $end)
        {
            $startTime = $start . " 00:00:00";
            $endTime = $start . " 23:59:59";
            $timestamp = strtotime($startTime);
            $todayTotal = 0;

            if(is_object($readingType))
            {
                $histories = History::where('profiles_id', '=', $person)
                                    ->whereBetween('created_at', [$startTime, $endTime])
                                    ->orderBy('created_at', 'asc')
                                    ->get();

                foreach($histories as $history)
                {
                    //echo"<br>Found a history - $history->id. Type is $type";
                    $readings = Thingsboards_Device_Reading::where('history_id', '=', $history->id)
                                                                ->where('reading_type_id', '=', $readingType->id)
                                                                ->get();
                    foreach($readings as $reading)
                    {
                        $exposures = Exposure::where('reading_type_id', '=', $readingType->id)
                                                ->where('archived', '=', 0)
                                                ->get();
                        foreach($exposures as $exposure)
                        {
                            //now work out from the exposure how to go about calculating the outcome of the total exposures
                            $todayTotal = $todayTotal + $reading->reading;
                        }
                    }
                }
            }
            $array[$x]['timestamp'] = $timestamp;
            $array[$x]['total'] = $todayTotal;

            $x++;
            $start = date('Y-m-d', strtotime($start . " +1 days"));
        }

        return $array;
    }

    public function getMyExposures()
    {
        $profile = $this->standardDisplay();

        $exposures = $this->calculateExposures($profile['profile'], "dashboard");

        return json_encode($exposures);
    }

    public function checkReadings()
    {
        //check to see if there are already readings on this history
        $getChecks = Histories_Check::get();
        foreach($getChecks as $hc)
        {
            $waitTime = $this->calcAgeMinutes($hc->created_at);

            if($waitTime > 4)
            {
                $checkReadings = Thingsboards_Device_Reading::where('history_id', '=', $hc->history_id)->count();
                if($checkReadings == 0)
                {
                    $thingsboardController = new ThingsboardController();
                    $thingsboardController->retrieveHistoryReadings($hc->history_id);
                }

                $hc->delete();
            }
        }
    }

    public function verifyDates($range, $site, $sensor)
    {
        /*
            This function checks and verifies that the control was on that site throughout the date range
            This function returns a date range as a json object
            $range is an array with 'start' and 'end' keys as timestamps
        */

        //first up we need to work out which control this sensor is attached to
        $device = Thingsboards_Device::find($sensor);

        $start = 0;
        $end = 0;
        $return = array();
        $arrival = Controls_Sites::where('control_id', '=', $device->control_id)
                                        ->where('to_site_id', '=', $site)
                                        ->first();

        //first off lets find out when this control was put on site to verify the start date
        if(is_object($arrival))
        {
            $start = strtotime($arrival->created_at);
            if($start > $range['start'])
            {
                $start = $start;
            }
            else
            {
                $start = $range['start'];
            }

            //now lets do the same but for the end date
            $removal = Controls_Sites::where('control_id', '=', $device->control_id)
                                        ->where('from_site_id', '=', $site)
                                        ->orderBy('id', 'desc')
                                        ->first();
            if(is_object($removal))
            {
                $end = strtotime($removal->created_at);
                if($end > $range['end'])
                {
                    $end = $range['end'];
                }
                else
                {
                    $end = $end;
                }
            }
            else
            {
                $end = $range['end'];
            }
        }
        else
        {
            //this control was never put on this site...
            return 0;
        }

        $return['start'] = $start;
        $return['end'] = $end;

        return json_encode($return);
    }

    public function getGraph($sensor, $period, $readingType, $site)
    {
        //this function retrieves a data set from Thingsboard and returns it in jSON format to be ready by Highcharts Graphs
        $apiController = new ApiController();
        $checkAuth = $this->checkAuth();
        $api = Api::where('application_name', '=', 'thingsboard')->first();

        $values = array();
        $x = 0;

        //First off get the timestamps between now and the period ago
        $now = time() * 1000;
        $previous = strtotime("-1 " . $period) * 1000;

        $range = array();
        $range['start'] = $now;
        $range['end'] = $previous;

        $range = $this->verifyDates($range, $site, $sensor);
        if(json_decode($range))
        {
            $range = json_decode($range);

            $device = Thingsboards_Device::find($sensor);

            //$endPoint = "/plugins/telemetry/DEVICE/" . $device->thingsboard_id . "/values/timeseries?limit=10000&agg=NONE&orderBy=DESC&useStrictDataTypes=false&keys=" . $readingType . "&startTs=" . $range->start . "&endTs=" . $range->end . "";

            $endPoint = "/plugins/telemetry/DEVICE/" . $device->thingsboard_id . "/values/timeseries?limit=10000&agg=NONE&orderBy=DESC&useStrictDataTypes=false&keys=" . $readingType . "&startTs=" . $range->end. "&endTs=" . $range->start;

            $response = json_decode($apiController->apiGetOAuth($api->id, $endPoint));

            foreach($response as $key => $value)
            {
                if($key == $readingType)
                {
                    foreach($value as $reading)
                    {
                        $values[$x]['timestamp'] = $reading->ts;
                        $values[$x]['reading'] = $reading->value;
                        $x++;
                    }
                }
            }
        }

        return json_encode($values);
    }

    public function getTrafficLight($sensor, $period, $readingType)
    {
        //this function retrieves a data set from Thingsboard and returns the average as specified by the period
        $apiController = new ApiController();
        $checkAuth = $this->checkAuth();
        $api = Api::where('application_name', '=', 'thingsboard')->first();

        //First off get the timestamps between now and the period ago
        $now = time() * 1000;
        $previous = strtotime("-" . $period . " minutes") * 1000;

        $device = Thingsboards_Device::find($sensor);

        $endPoint = "/plugins/telemetry/DEVICE/" . $device->thingsboard_id . "/values/timeseries?limit=10000&agg=NONE&orderBy=DESC&useStrictDataTypes=false&keys=" . $readingType . "&startTs=" . $previous . "&endTs=" . $now . "";

        $response = json_decode($apiController->apiGetOAuth($api->id, $endPoint));

        //print_r($response);

        $total = 0;
        $x = 0;

        if(is_object($response))
        {
            foreach($response as $key => $value)
            {
                if($key == $readingType)
                {
                    foreach($value as $reading)
                    {
                        $total += $reading->value;
                        $x++;
                    }
                }
            }
        }

        $array = array();
        if($x > 0)
        {
            $array['value'] = $total/$x;
        }
        else
        {
            $array['value'] = $total;
        }

        return json_encode($array);
    }

    public function identifySensor($sensor, $reading)
    {
        $site = "Invalid site";
        $siteID = 0;
        $controlName = "Invalid control";
        $deviceName = "Invalid sensor";
        $readingName = "Invalid reading";
        $map = " - ";
        $zone = " - ";

        $device = Thingsboards_Device::find($sensor);
        if(is_object($device))
        {
            $deviceName = $device->name;
            //find out what control this sensor is on, and what site that control is on
            $control = Control::find($device->control_id);
            if(is_object($control))
            {
                $controlName = $control->Controls_Type->name;

                if($control->current_site > 0)
                {
                    $site = $control->Site->name;
                    $siteID = $control->current_site;

                    //find out the current zone
                    $lastTransfer = Controls_Sites::where('control_id', '=', $control->id)->orderBy('id', 'desc')->first();
                    $cMap = Sites_Map::find($lastTransfer->to_map_id);
                    if(is_object($cMap))
                    {
                        $map = $cMap->name;
                    }
                    $cZone = Sites_Maps_Zone::find($lastTransfer->to_zone_id);
                    if(is_object($cZone))
                    {
                        $zone = $cZone->name;
                    }
                }
            }
        }
        $readingType = Thingsboards_Readings_Type::find($reading);
        if(is_object($readingType))
        {
            $readingName = $readingType->name;
        }

        $return = array();
        $return['site'] = $site;
        $return['map'] = $map;
        $return['zone'] = $zone;
        $return['control'] = $controlName;
        $return['device'] = $deviceName;
        $return['reading'] = $readingName;
        $return['siteID'] = $siteID;

        return json_encode($return);
    }

    public function getReadingTypes($device)
    {
        $return = array();
        $r = 0;

        $device = Thingsboards_Device::find($device);
        $checkDevice = Thingsboards_Device_Reading_Types::where('device_id', '=', $device->id)->get();
        foreach($checkDevice as $cd)
        {
            $return[$r]['id'] = $cd->reading_type_id;
            $return[$r]['name'] = $cd->ReadingType->name;
            $r++;
        }

        return json_encode($return);
    }

    public function getRangeReadings($sensor, $start, $end, $readingType, $interval)
    {
        //this function retrieves a data set from Thingsboard and returns it in jSON format to be ready by Highcharts Graphs
        $apiController = new ApiController();
        $checkAuth = $this->checkAuth();
        $api = Api::where('application_name', '=', 'thingsboard')->first();

        $values = array();
        $x = 0;

        //First off get the timestamps between now and the period ago
        $start = strtotime($start) * 1000;
        $end = strtotime($end) * 1000;
        $readingType = Thingsboards_Readings_Type::find($readingType);

        $device = Thingsboards_Device::find($sensor);

        $endPoint = "/plugins/telemetry/DEVICE/" . $device->thingsboard_id . "/values/timeseries?limit=10000&agg=AVG&orderBy=ASC&useStrictDataTypes=false&keys=" . $readingType->name . "&startTs=" . $start . "&endTs=" . $end;

        $response = json_decode($apiController->apiGetOAuth($api->id, $endPoint));

        if(isset($response))
        {
            foreach($response as $key => $value)
            {

                if($key == $readingType->name)
                {
                    if(isset($value))
                    {
                        foreach($value as $reading)
                        {
                            $values[$x]['timestamp'] = $reading->ts;
                            $values[$x]['reading'] = $reading->value;
                            $x++;
                        }
                    }
                }
            }
        }

        $values = $this->splitDataIntervals($values, $interval);

        return json_encode($values);
    }

    public function getSiteSensors($site)
    {
        //get all the controls on the site
        $sensors = array();
        $x = 0;

        $controls = Control::where('current_site', '=', $site)->where('archived', '=', 0)->get();
        foreach($controls as $control)
        {
            //get all the sensors on this control
            $devices = Thingsboards_Device::where('control_id', '=', $control->id)->get();
            foreach($devices as $device)
            {
                $sensors[$x] = $device;
                $x++;
            }
        }

        return json_encode($sensors);
    }

    public function splitDataIntervals($values, $interval)
    {
        /*
            $interval is in minutes
        */

        $results = array();
        $r = 0;
        $m = 0;
        $total = 0;
        $count = 0;
        $last = 0;

        foreach($values as $value)
        {
            if($m == 0)
            {
                $m = $value['timestamp'];
            }

            $diffMs = $value['timestamp'] - $m;
            $diffMins = $diffMs / 60000;


            if($diffMins >= $interval)
            {
                $results[$r]['timestamp'] = $last;
                $results[$r]['reading'] = $total / $count;
                $r++;
                $total = 0;
                $count = 0;

                $m = $value['timestamp'];
            }

            $count++;
            $total += $value['reading'];
            $last = $value['timestamp'];

            //echo "<br>" . $value['timestamp'] . " DiffMS is " . $diffMs . " DiffMins is " . $diffMins . " reading is " . $value['reading'] . " total is " . $total . " count is " . $count . " average is" . $total/$count;
        }

        return $results;
    }
}