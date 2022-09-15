<?php

namespace App\Http\Controllers;

use App\Models\Actions_Control;
use App\Models\Control;
use App\Models\Controls_Sites;
use App\Models\Controls_Type;
use App\Models\Email;
use App\Models\History;
use App\Models\Histories_Assessments;
use App\Models\License;
use App\Models\License_Profile;
use App\Models\Log;
use App\Models\Profile;
use App\Models\Profiles_Trade;
use App\Models\Site;
use App\Models\Sites_Logon;
use App\Models\Thingsboards_Device;
use App\Models\Thingsboards_Readings_Type;
use App\Models\Trade;
use App\Models\Training;
use App\Models\Trainings_Profile;

use App\Http\Controllers\PeopleController;
use App\Http\Controllers\ThingsboardController;

use Illuminate\Http\Request;
use DateTime;

use App\Jobs\emailSender;

class ReportController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    
    //generic functions

        public function breakoutRange($range)
        {
            $startRange = substr($range, 0, 10);
            $endRange = substr($range, 13, 10);

            $array = array();
            $array['start'] = $startRange;
            $array['end'] = $endRange;
            
            return $array;
        }

        public function widgetRange($range)
        {
            $startYear = substr($range['start'], 6, 4);
            $startMonth = substr($range['start'], 3, 2);
            $startDay = substr($range['start'], 0, 2);

            $endYear = substr($range['end'], 6, 4);
            $endMonth = substr($range['end'], 3, 2);
            $endDay = substr($range['end'], 0, 2);

            $return = $startMonth . "/" . $startDay . "/" . $startYear . " - " . $endMonth . "/" . $endDay . "/" . $endYear;

            return $return;
        }

        public function sqlDate($date)
        {
            $year = substr($date, 6, 4);
            $month = substr($date, 3, 2);
            $day = substr($date, 0, 2);

            $date = $year . "-" . $month . "-" . $day; 
            
            $calc = strtotime(TRIM($date));

            //echo "Entered with $date, exitting with $calc";

            return $date;
        }

        public function calcTimeDiff($range, $format)
        {
            //$range should have already gone through breakoutRange
            $time = 0;

            
            $startTime = strtotime($range['start']);
            $finishTime = strtotime($range['end']);
            $diff = $finishTime - $startTime;
            $timeInSeconds = abs($diff);
            if($format == "seconds")
            {
                $time = $timeInSeconds;  //return the time in seconds
            }
            elseif($format == "minutes")
            {
                $time = $timeInSeconds/60;  //return the time in minutes
            }
            elseif($format == "hours")
            {
                $time = ($timeInSeconds/60)/60;  //return the time in hours
            }
            else
            {
                $time = abs((($timeInSeconds/60)/60)/24);  //return the time in days
            }
            

            return $time;
        }

    //End generic functions


        public function individualExposures()
        {
            /*
                Standard page setup
            */
            $standardDisplay = Controller::standardDisplay();
            $breadcrumbs = Controller::createBreadcrumbs("Export", NULL);       
            $today = date('Y-m-d');
            $results = array();
            
            return view('reports.individualExposures', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'results' => $results,
                'today' => $today
            ]);
        }


    
    //Billing Report

        public function billing()
        {
            $month_ini = new DateTime("first day of last month");
            $month_end = new DateTime("last day of last month");
            $range = $month_ini->format('d/m/Y') . " - " . $month_end->format('d/m/Y');

            return $this->showBilling($range);
        }

        public function filterBilling(Request $request)
        {
            return $this->showBilling($request->search_date);
        }

        public function showBilling($range)
        {
            /*
                Standard page setup
            */

            $standardDisplay = Controller::standardDisplay();
            $breadcrumbs = Controller::createBreadcrumbs("Reports", "Billing");
            
            if($standardDisplay['profile']->super_user != 1)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }


            
            $results = array();
            $grandTotal = 0;

            //get all the builders
            $buildersArray = array();
            $b = 0;

            $builders = Profile::where('type', '=', 'builder')->where('archived', '=', 0)->orderBy('name', 'asc')->get();
            foreach($builders as $builder)
            {            
                $bill = $this->getBill($builder->id, $range);
                if(isset($bill['total']))
                {
                    $buildersArray[$b] = $bill;
                    $grandTotal = $bill['total']  + $grandTotal;
                    $b++;
                }
            }

            //get all the contractors
            $contractorsArray = array();
            $c = 0;

            $contractors = Profile::where('type', '=', 'contractor')->where('archived', '=', 0)->orderBy('name', 'asc')->get();
            foreach($contractors as $contractor)
            {            
                $bill = $this->getBill($contractor->id, $range);
                if(isset($bill['total']))
                {
                    $contractorsArray[$c] = $bill;
                    $grandTotal = $bill['total']  + $grandTotal;
                    $c++;
                }
            }

            //get all the hygenists
            $hygenistsArray = array();
            $h = 0;

            $hygenists = Profile::where('type', '=', 'hygenist')->where('archived', '=', 0)->orderBy('name', 'asc')->get();
            foreach($hygenists as $hygenist)
            {            
                $bill = $this->getBill($hygenist->id, $range);
                if(isset($bill['total']))
                {
                    $hygenistsArray[$h] = $bill;
                    $grandTotal = $bill['total']  + $grandTotal;
                    $h++;
                }
            }

            $results['builders'] = $buildersArray;
            $results['contractors'] = $contractorsArray;
            $results['hygenists'] = $hygenistsArray;
            $results['grandTotal'] = $grandTotal;

            $filterRange = $this->breakoutRange($range);
            $widgetRange = $this->widgetRange($filterRange);
            
            
            return view('reports.billing', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'dateRange' => $range,
                'widgetRange' => $widgetRange,
                'results' => $results,
            ]);
        }

        public function getBill($profile, $range)
        {
            $total = 0;
            $bill = array();
            
            $organisation = Profile::find($profile);
            $licenses = License_Profile::where('profile_id', '=', $organisation->id)->first();

            if(!is_object($licenses))
            {
                $index = new HomeController();
                $alert = $organisation->name . " does not have any license entries.  Open their profile and submit to force a refresh.";

                return $index->displayDashboard($alert);
            }

            //go through all the sites belonging to this profile and get all the controls along with their monthly cost.
            $controlsArray = array();
            $controlsTotal = 0;
            $c = 0;
            $types = Controls_type::where('archived', '=', 0)->orderBy('name', 'asc')->get();
            foreach($types as $type)
            {
                $typeTotal = 0;
                $sites = Site::where('builder_id', '=', $organisation->id)->get();
                foreach($sites as $site)
                {
                    //find all the controls currently on this site
                    $controls = Control::where('current_site', '=', $site->id)->where('controls_type_id', '=', $type->id)->count();
                    if($controls > 0)
                    {
                        $theseControls = Control::where('current_site', '=', $site->id)->where('controls_type_id', '=', $type->id)->get();
                        foreach($theseControls as $control)
                        {
                            $amount = $this->getControlSiteBill($control, $range, $site->id);
                            
                            $controlsTotal = $controlsTotal + $amount;
                            $typeTotal = $typeTotal + $amount;
                        }
                        
                        $controlsArray[$c]['type'] = $type;
                        $controlsArray[$c]['qty'] = $controls;
                        $controlsArray[$c]['typeTotal'] = $typeTotal;
                        
                        $c++;
                    }
                }    
            }
            $total = $total + $controlsTotal;    

            //go through all the services ordered where this was the active organisation that is not paid
            $trainingArray = array();
            $servicesTotal = 0;
            $t = 0;
            $training = Training::where('archived', '=', 0)->orderBy('name', 'asc')->get();
            foreach($training as $tr)
            {
                $count = Trainings_Profile::where('training_id', '=', $tr->id)->where('active_organisation_id', '=', $organisation->id)->where('paid', '=', 0)->count();
                if($count > 0)
                {
                    $names = array();
                    $n = 0;
                    $price = 0;

                    $instances = Trainings_Profile::where('training_id', '=', $tr->id)->where('active_organisation_id', '=', $organisation->id)->get();
                    foreach($instances as $instance)
                    {
                        $names[$n] = $instance->Profile->name;
                        $n++;

                        $price = $price + $instance->price;
                    }

                    $averageCost = $price / $count;

                    $trainingArray[$t]['service'] = $tr->name;
                    $trainingArray[$t]['names'] = $names;
                    $trainingArray[$t]['cost'] = $averageCost;
                    $trainingArray[$t]['total'] = $price;
                    $trainingArray[$t]['qty'] = $count;
                    $t++;

                    $total = $total + $price;
                    $servicesTotal = $servicesTotal + $price;
                }
            }
            $total = $total + ($licenses->no_sites * $licenses->site_cost);
            $total = $total + ($licenses->no_users * $licenses->user_cost);

            $bill['profile'] = $organisation;
            $bill['no_users'] = $licenses->no_users;
            $bill['user_cost'] = $licenses->user_cost;
            $bill['no_sites'] = $licenses->no_sites;
            $bill['site_cost'] = $licenses->site_cost;
            $bill['controls'] = $controlsArray;
            $bill['controlsTotal'] = $controlsTotal;
            $bill['services'] = $trainingArray;
            $bill['servicesTotal'] = $servicesTotal;
            $bill['total'] = $total;

            return $bill;
        }

        public function getControlSiteBill($control, $range, $site)
        {
            //$control is the full control object, $range is a date range formatted like dd-mm-YYYY - dd-mm-YYYY, $site is a site ID

            $startRange = substr($range, 0, 10);
            $endRange = substr($range, 13, 10);
            $startBill = $startRange;
            $endBill = $endRange;
            
            $endRangeCalc = strtotime($endRange);
            $startRangeCalc = strtotime($startRange);
            $daysRange = $endRangeCalc - $startRangeCalc;

            $noDaysRange = round($daysRange / (60 * 60 * 24)) + 1;

            $startBillCheck = date('Y-m-d', $startRangeCalc);
            $endBillCheck = date('Y-m-d', $endRangeCalc);

            //get the date the control was put on site
            $dateArrived = Controls_Sites::where('control_id', '=', $control->id)
                                            ->where('to_site_id', '=', $site)
                                            ->where('from_site_id', '!=', $site)
                                            ->orderBy('id', 'desc')
                                            ->first();
            if(is_object($dateArrived))
            {

                if($dateArrived->created_at > $startBillCheck)
                {
                    $startBill = $dateArrived->created_at;
                }
            }

            //get the date the control was taken off site
            $dateRemoved = Controls_Sites::where('control_id', '=', $control->id)
                                            ->where('to_site_id', '!=', $site)
                                            ->where('from_site_id', '=', $site)
                                            ->orderBy('id', 'desc')
                                            ->first();
            if(is_object($dateRemoved))
            {
                if($dateRemoved->created_at < $endBillCheck)
                {
                    $endBill = $dateRemoved->created_at;
                }
            }

            $endBillCalc = strtotime($endBill);
            $startBillCalc = strtotime($startBill);
            $billRange = $endBillCalc - $startBillCalc;

            $noDaysBill = round($billRange / (60 * 60 * 24)) + 1;

            $type = Controls_Type::find($control->controls_type_id);

            $frequency = $control->billing_frequency;
            if(empty($frequency))
            {
                $frequency = $type->billing_frequency;
            }

            $amount = $control->billing_amount;
            if(empty($amount))
            {
                $frequency = $type->billing_amount;
            }

            //Does this item get billed monthly or daily
            if($frequency == "monthly")
            {
                //work out the percentage of the month that was on site for to bill item
                $percentage = $noDaysBill / $noDaysRange;
                $bill = round($amount * $percentage, 2);
            }
            else
            {
                $bill = round($amount * $noDaysBill, 2);
            }

            return $bill;
        }
    
    //End billing report

    //Activities Report

        public function activities()
        {
            $month_ini = new DateTime("first day of last month");
            $month_end = new DateTime("last day of last month");
            $range = $month_ini->format('d/m/Y') . " - " . $month_end->format('d/m/Y');

            return $this->showActivities($range, 0);
        }

        public function filterActivities(Request $request)
        {
            $startYear = substr($request->search_date, 6, 4);
            $startMonth = substr($request->search_date, 0, 2);
            $startDay = substr($request->search_date, 3, 2);

            $endYear = substr($request->search_date, 19, 4);
            $endMonth = substr($request->search_date, 13, 2);
            $endDay = substr($request->search_date, 16, 2);

            $range = $startDay . "/" . $startMonth . "/" . $startYear . " - " . $endDay . "/" . $endMonth . "/" . $endYear;

            return $this->showActivities($range, $request->site);
        }

        public function showActivities($range, $site)
        {
            $standardDisplay = $this->checkFunctionPermission("reports:view");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $breadcrumbs = Controller::createBreadcrumbs("Reports", "Activities");
            $peopleController = new PeopleController();
            $userSites = $peopleController->getUsersSites($standardDisplay['profile'], "all");

            $siteArray = array();
            $s = 0;
            foreach($userSites as $st)
            {
                $siteArray[$s] = $st->id;
                $s++;
            }
            
            $results = array();
            $x = 0;
            $filterRange = $this->breakoutRange($range);
            


            //Need to get all activities within the date range
            if($site == 0)
            {
                $histories = History::where('archived', '=', 0)
                                        ->whereBetween('created_at', [$this->sqlDate($filterRange['start'] . " 00:00:00"), $this->sqlDate($filterRange['end'] . " 23:59:59")])
                                        ->get();
            }
            else
            {
                $histories = History::where('archived', '=', 0)
                                        ->where('site_id', '=', $site)
                                        ->whereBetween('created_at', [$this->sqlDate($filterRange['start']), $this->sqlDate($filterRange['end'])])
                                        ->get();
            }

            foreach($histories as $history)
            {
                if(in_array($history->site_id, $siteArray))
                {
                    $results[$x]['id'] = $history->id;
                    $results[$x]['date'] = $history->created_at->format('d-m-Y');
                    $results[$x]['person'] = $this->checkUserVisibility($history->profiles_id);
                    if(is_object($history->Site))
                    {
                        $results[$x]['site'] = $history->Site->name;
                    }
                    else 
                    {
                        $results[$x]['site'] = "-";
                    }

                    if(is_object($history->Zone))
                    {
                        $results[$x]['zone'] = $history->Zone->name;
                    }
                    else 
                    {
                        $results[$x]['zone'] = "-";
                    }

                    if(is_object($history->Activity))
                    {
                        $results[$x]['activity'] = $history->Activity->name;
                    }
                    else 
                    {
                        $results[$x]['activity'] = "-";
                    }
                    
                    
                    $results[$x]['time'] = $this->calcHistoryTime($history->id, "hours");
                    $results[$x]['assessments'] = count($history->Assessment);

                    $x++;
                }
            }

            $widgetRange = $this->widgetRange($filterRange);
            
            return view('reports.activities', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'dateRange' => $range,
                'widgetRange' => $widgetRange,
                'site' => $site,
                'userSites' => $userSites,
                'results' => $results,
            ]);
        }

    //End activities report

    //Logs Report
        public function logs()
        {
            $month_ini = new DateTime("first day of last month");
            $month_end = new DateTime("last day of last month");
            $range = $month_ini->format('d/m/Y') . " - " . $month_end->format('d/m/Y');

            return $this->showLogs($range, 0, 0);
        }

        public function filterLogs(Request $request)
        {
            $startYear = substr($request->search_date, 6, 4);
            $startMonth = substr($request->search_date, 0, 2);
            $startDay = substr($request->search_date, 3, 2);

            $endYear = substr($request->search_date, 19, 4);
            $endMonth = substr($request->search_date, 13, 2);
            $endDay = substr($request->search_date, 16, 2);

            $range = $startDay . "/" . $startMonth . "/" . $startYear . " - " . $endDay . "/" . $endMonth . "/" . $endYear;

            return $this->showLogs($range);
        }


        public function showLogs($range)
        {
            $standardDisplay = $this->checkFunctionPermission("reports:view");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $breadcrumbs = Controller::createBreadcrumbs("Reports", "Logs");
            $peopleController = new PeopleController();
            $profiles = $peopleController->userProfiles($standardDisplay);
            $pArray = array();
            $p = 0;

            foreach($profiles as $pr)
            {
                $pArray[$p] = $pr->id;
                $p++;
            }
            
            $results = array();
            $x = 0;
            $filterRange = $this->breakoutRange($range);
            
            //Need to get all logs within the date range and filters set
            $logs = Log::whereBetween('created_at', [$this->sqlDate($filterRange['start'] . " 00:00:00"), $this->sqlDate($filterRange['end'] . " 23:59:59")])
                            ->get();
            

            foreach($logs as $log)
            {
                if(in_array($log->profiles_id, $pArray))
                {
                    $results[$x]['date'] = $log->created_at->format('d-m-Y');
                    $results[$x]['person'] = $this->checkUserVisibility($log->profiles_id);
                    $results[$x]['module'] = $log->module;
                    $results[$x]['ID'] = $log->module_id;
                    $results[$x]['action'] = $log->action;
                    $results[$x]['entry'] = $log->notes;
                    $results[$x]['level'] = $log->log_level;

                    $x++;
                }
            }

            $widgetRange = $this->widgetRange($filterRange);
            
            return view('reports.logs', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'dateRange' => $range,
                'widgetRange' => $widgetRange,
                'profiles' => $profiles,
                'results' => $results,
            ]);
        }
    //End logs report

    //Control usage Report
        public function controlUsage()
        {
            $month_ini = new DateTime("first day of last month");
            $month_end = new DateTime("last day of last month");
            $range = $month_ini->format('d/m/Y') . " - " . $month_end->format('d/m/Y');

            return $this->showControlUsage($range, 0, 0);
        }

        public function filterControlUsage(Request $request)
        {
            $startYear = substr($request->search_date, 6, 4);
            $startMonth = substr($request->search_date, 0, 2);
            $startDay = substr($request->search_date, 3, 2);

            $endYear = substr($request->search_date, 19, 4);
            $endMonth = substr($request->search_date, 13, 2);
            $endDay = substr($request->search_date, 16, 2);

            $range = $startDay . "/" . $startMonth . "/" . $startYear . " - " . $endDay . "/" . $endMonth . "/" . $endYear;

            return $this->showControlUsage($range);
        }


        public function showControlUsage($range)
        {
            $standardDisplay = $this->checkFunctionPermission("reports:view");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $breadcrumbs = Controller::createBreadcrumbs("Reports", "Controls usage");
            
            $peopleController = new PeopleController();
            $userSites = $peopleController->getUsersSites($standardDisplay['profile'], "all");

            $siteArray = array();
            $s = 0;
            foreach($userSites as $st)
            {
                $siteArray[$s] = $st->id;
                $s++;
            }
            
            
            $results = array();
            $x = 0;
            $filterRange = $this->breakoutRange($range);
            
            //Need to get all logs within the date range and filters set
            $usage = Actions_Control::whereBetween('created_at', [$this->sqlDate($filterRange['start'] . " 00:00:00"), $this->sqlDate($filterRange['end'] . " 23:59:59")])
                                        ->get();
            

            foreach($usage as $use)
            {
                if(in_array($use->site_id, $siteArray))
                {
                    $results[$x]['date'] = $use->created_at->format('d-m-Y');
                    $results[$x]['type'] = $use->Control->Controls_Type->name;
                    $results[$x]['serial'] = $use->Control->serial;
                    $results[$x]['person'] = $this->checkUserVisibility($use->user_id);
                    $results[$x]['site'] = $use->Site->name;
                    if(is_object($use->Zone))
                    {
                        $results[$x]['zone'] = $use->Zone->name;
                    }
                    else
                    {
                        $results[$x]['zone'] = "-";
                    }
                    
                    $results[$x]['time'] = $this->calcHistoryTime($use->history_id, "minutes");

                    $x++;
                }
            }

            $widgetRange = $this->widgetRange($filterRange);
            
            return view('reports.controlUsage', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'dateRange' => $range,
                'widgetRange' => $widgetRange,
                'results' => $results,
            ]);
        }
    //End control usage report

    //Participation Report
        public function participation()
        {
            $week_ini = new DateTime("first day of last week");
            $week_end = new DateTime("last day of last week");
            $range = $week_ini->format('d/m/Y') . " - " . $week_end->format('d/m/Y');

            return $this->showParticipation($range, 0, 0);
        }

        public function filterParticipation(Request $request)
        {
            $startYear = substr($request->search_date, 6, 4);
            $startMonth = substr($request->search_date, 0, 2);
            $startDay = substr($request->search_date, 3, 2);

            $endYear = substr($request->search_date, 19, 4);
            $endMonth = substr($request->search_date, 13, 2);
            $endDay = substr($request->search_date, 16, 2);

            $range = $startDay . "/" . $startMonth . "/" . $startYear . " - " . $endDay . "/" . $endMonth . "/" . $endYear;

            return $this->showParticipation($range, $request->site);
        }

        public function buildParticipation($range, $site)
        {
            $x = 0;
            $filterRange = $this->breakoutRange($range);
            $lastDay = $this->sqlDate($filterRange['end']) . " 23:59:59";
            $results = array();
            $return = array();
            $x = 0;
            
            //work out how many days are in the date range
            $timeDiff = $this->calcTimeDiff($filterRange, "days");

            //get all the trades
            $trades = Trade::where('archived', '=', 0)->orderBy('name', 'asc')->get();
            foreach($trades as $trade)
            {
                //get how many of this trade was on site for the date range
                $tradeNumber = 0;
                $tradeActivities = 0;
                $tradeSWMS = 0;
                $totalExpectedHazards = 0;
                $tradeLogons = 0;
                
                $lookDay = $this->sqlDate($filterRange['start']);

                while($lastDay > $lookDay)
                {
                    $start = $lookDay;
                    $end = substr($lookDay, 0, 10) . " 23:59:59";

                    //echo $start . "<br>";

                    $logons = Sites_Logon::where('site_id', '=', $site)
                                        ->whereBetween('date', [$start, $end])
                                        ->get();
                    foreach($logons as $logon)
                    {
                        //echo "Got a logon today<br>";
                        $profileTrade = Profiles_Trade::where('profiles_id', '=', $logon->profile_id)
                                                    ->where('trades_id', '=', $trade->id)
                                                    ->count();
                        $tradeNumber = $tradeNumber + $profileTrade;

                        if($profileTrade > 0)
                        {
                            $tradeLogons++;
                            //echo "Finding estimated hazards<br>";
                            $totalExpectedHazards = $totalExpectedHazards + $trade->est_hazards;
                            //count the number of Activities and SWMS filled in by this person on this site between those dates
                            $histories = History::where('profiles_id', '=', $logon->profile_id)
                                                    ->whereBetween('time_start', [$start, $end])
                                                    ->where('archived', '=', 0)
                                                    ->get();
                            $tradeActivities = $tradeActivities + count($histories);
                            //echo "Trade activities is " . $tradeActivities . "<br>";
                            foreach($histories as $history)
                            {
                                //echo "Counting tradeSWMS - $history->id<br>";
                                $historySWMS = Histories_Assessments::where('history_id', '=', $history->id)->count();
                                $tradeSWMS = $tradeSWMS + $historySWMS;
                            }
                            
                        }
                    }

                    $lookDay = date('Y-m-d H:i:s', strtotime($lookDay . ' +1 day'));
                }

                //work out the participation difference
                if($totalExpectedHazards > 0 && $tradeSWMS > 0)
                {
                    $participation = round(($tradeSWMS / $totalExpectedHazards) * 100, 0);
                }
                else 
                {
                    $participation = 0;
                }

                //Populate this record in the array
                $results[$x]['trade'] = $trade->name;
                $results[$x]['workers'] = $tradeNumber;
                $results[$x]['expectedSWMS'] = $totalExpectedHazards;
                $results[$x]['actualSMWS'] = $tradeSWMS;
                $results[$x]['activities'] = $tradeActivities;
                $results[$x]['participation'] = $participation;
                $results[$x]['logons'] = $tradeLogons;
                $x++;
            }

            $totalWorkers = 0;
            $totalEstHazards = 0;
            $totalActHazards = 0;
            $totalActivities = 0;
            $totalParticipation = 0;
            $totalLogons = 0;

            foreach($results as $result)
            {
                $totalWorkers = $totalWorkers + $result['workers'];
                $totalEstHazards = $totalEstHazards + $result['expectedSWMS'];
                $totalActHazards = $totalActHazards + $result['actualSMWS'];
                $totalActivities = $totalActivities + $result['activities'];
                $totalLogons += $result['logons'];
            }
            if($totalActHazards > 0 && $totalEstHazards > 0)
            {
                $totalParticipation = ($totalActHazards / $totalEstHazards) * 100;
            }

            $return['totalWorkers'] = $totalWorkers;
            $return['totalEstHazards'] = $totalEstHazards;
            $return['totalActHazards'] = $totalActHazards;
            $return['totalActivities'] = $totalActivities;
            $return['totalLogons'] = $totalLogons;
            $return['totalParticipation'] = $totalParticipation;
            $return['results'] = $results;
            
            return $return;
        }


        public function showParticipation($range, $site)
        {
            $standardDisplay = $this->checkFunctionPermission("reports:view");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }
            
            $breadcrumbs = Controller::createBreadcrumbs("Reports", "Participation");
            
            
            if($site > 0)
            {
                $results = $this->buildParticipation($range, $site);
            }
            else 
            {
                $results = NULL;
            }

            $filterRange = $this->breakoutRange($range);
            $widgetRange = $this->widgetRange($filterRange);

            $peopleController = new PeopleController();
            $userSites = $peopleController->getUsersSites($standardDisplay['profile'], "all");
            
            return view('reports.participation', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'dateRange' => $range,
                'widgetRange' => $widgetRange,
                'results' => $results,
                'userSites' => $userSites,
                'site' => $site,
            ]);
        }

        public function emailParticipations()
        {
            /*
                This function is called up by the task scheduler and automatically queue's up all participation reports to be emailed automatically.
            */

            $day = date('D');

            if($day == "Fri")
            {
                $date = date('l d F Y');
                $week_ini = new DateTime("7 days ago");
                $week_end = new DateTime("now");
                $range = $week_ini->format('d/m/Y') . " - " . $week_end->format('d/m/Y');

                $sites = Site::where('archived', '=', 0)
                                ->where('status', '=', 'active')
                                ->get();

                foreach($sites as $site)
                {
                    if($site->primary_contact_id > 0)
                    {
                        $profile = Profile::find($site->primary_contact_id);
                        if(is_object($profile))
                        {
                            if(!empty($profile->email))
                            {
                                $report = $this->buildParticipation($range, $site->id);
                                $pdfContent = "";
                                
                                foreach($report['results'] as $result)
                                {
                                    $pdfContent .= "<tr><td>" . $result['trade'] . "</td><td>" . $result['workers'] . "</td><td>" . $result['expectedSWMS'] . "</td><td>" . $result['actualSMWS'] . "</td><td>" . $result['activities'] . "</td><td>" . $result['participation'] . "%</td></tr>";
                                }
                                
                                $content = "Here is the weekly participation report for <b>$site->name</b> during the week ending " . $date;
                                $content .= "<br><br><table style=\"width: 100%;\"><tr><td><b>Trade</b></td><td><b>Workers</b></td><td><b>Est Hazards</b></td><td><b>Logged SWMS</b></td><td><b>Activities</b></td><td><b>Participation</b></td></tr>";
                                $content .= "<tr><td><b>Totals</b></td><td><b>" . $report['totalWorkers'] . "</b></td><td><b>" . $report['totalEstHazards'] . "</b></td><td><b>" . $report['totalActHazards'] . "</b></td><td><b>" . $report['totalActivities'] . "</b></td><td><b>" . $report['totalParticipation'] . "%</b></td></tr></table>";
                                $content .= "<br><br><br>Regards,<br><br>The Nextrack team.";

                                $email = new Email();
                                $email->send_to = $profile->name;
                                $email->send_email = $profile->email;
                                $email->subject = "Weekly participation report";
                                $email->content = $content;
                                $email->status = "pending";
                                $email->save();
                            }
                        }
                    }
                }
            }
            emailSender::dispatch();

            return 1;
        }
    //End Participation Report

    //Exposure report

        public function exposure()
        {
            $month_ini = new DateTime("first day of last month");
            $month_end = new DateTime("last day of last month");
            $range = $month_ini->format('d/m/Y') . " - " . $month_end->format('d/m/Y');

            return $this->showExposure($range, 0);
        }

        public function filterExposure(Request $request)
        {
            $startYear = substr($request->search_date, 6, 4);
            $startMonth = substr($request->search_date, 0, 2);
            $startDay = substr($request->search_date, 3, 2);

            $endYear = substr($request->search_date, 19, 4);
            $endMonth = substr($request->search_date, 13, 2);
            $endDay = substr($request->search_date, 16, 2);

            $range = $startDay . "/" . $startMonth . "/" . $startYear . " - " . $endDay . "/" . $endMonth . "/" . $endYear;

            return $this->showExposure($range, $request);
        }

        public function buildExposure($range, $sensor, $reading, $limit, $ppe1, $ppe2, $ppe3, $ppe4, $interval, $weight)
        {
            $x = 0;
            $filterRange = $this->breakoutRange($range);
            $start = $this->sqlDate($filterRange['start']) . " 00:00:01";
            $end = $this->sqlDate($filterRange['end']) . " 23:59:59";
            $results = array();
            $return = array();
            $x = 0;
            $thingsboardController = new ThingsboardController();

            $readings = json_decode($thingsboardController->getRangeReadings($sensor, $start, $end, $reading, $interval), true);

            $values = array();
            $x = 1;
            $likelyArray = array();
            $l = 0;
            

            foreach($readings as $reading)
            {
                $likely = 0;
                $total = 0;
                $equipment1 = "unknown";
                $equipment2 = "unknown";
                $equipment3 = "unknown";
                $equipment4 = "unknown";

                if($reading['reading'] > 0)
                {
                    $likely = ($reading['reading'] * $weight)/100;
                    $likelyArray[$l]['value'] = $likely;
                    $likelyArray[$l]['timestamp'] = $reading['timestamp'];
                    $l++;

                    $oldTime = $reading['timestamp'] - 28800000;
                    $c = 0; 

                    foreach($likelyArray as $cReading)
                    {
                        if($cReading['timestamp'] >= $oldTime)
                        {
                            $total += $cReading['value'];
                            $c++;
                        }
                    }
                    
                    $average = $total/$c;
                }
                else
                {
                    $average = 1;
                }

                // echo "<br>Timestamp is " . date('d-m-Y H:i:s', ($reading['timestamp'])/1000) . " Reading is " . $reading['reading'] . " Total is " . $total . " and the average is " . $average;
                $baseOutcome = "unknown";
                if($limit > $average)
                {
                    $baseOutcome = "green";
                }
                else
                {
                    $baseOutcome = "orange";
                }

                $e1 = $limit/($average/$ppe1);
                $e2 = $limit/($average/$ppe2);
                $e3 = $limit/($average/$ppe3);
                $e4 = $limit/($average/$ppe4);

                if($e1 > 8)
                {
                    $e1 = 8;
                }

                if($e2 > 8)
                {
                    $e2 = 8;
                }

                if($e3 > 8)
                {
                    $e3 = 8;
                }

                if($e4 > 8)
                {
                    $e4 = 8;
                }
                


                $values[$x]['timestamp'] = date('d-m-Y H:i:s', $reading['timestamp']/1000);
                $values[$x]['reading'] = $reading['reading'];
                $values[$x]['likely'] = $likely;
                $values[$x]['average'] = $average;
                $values[$x]['baseOutcome'] = $baseOutcome;
                $values[$x]['ppe1'] = $e1;
                $values[$x]['ppe2'] = $e2;
                $values[$x]['ppe3'] = $e3;
                $values[$x]['ppe4'] = $e4;

                $x++;
            }
            
            return $values;
        }


        public function showExposure($range, $request)
        {
            $standardDisplay = $this->checkFunctionPermission("reports:view");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }
            
            $breadcrumbs = Controller::createBreadcrumbs("Reports", "Exposure");
            $reading = 0;
            $sensor = 0;
            
            
            if(isset($request->sensor))
            {
                $results = $this->buildExposure($range, $request->sensor, $request->reading, $request->limit, $request->ppe1, $request->ppe2, $request->ppe3, $request->ppe4, $request->interval, $request->weight);
                $sensor = Thingsboards_Device::find($request->sensor);
                $interval = $request->interval;
            }
            else 
            {
                $results = NULL;
                $interval = 0;
            }

            if(isset($request->reading))
            {
                $reading = Thingsboards_Readings_Type::find($request->reading);
            }

            $filterRange = $this->breakoutRange($range);
            $widgetRange = $this->widgetRange($filterRange);

            if(isset($request->site))
            {
                $thingsboardController = new ThingsboardController();
                $sensors = json_decode($thingsboardController->getSiteSensors($request->site));
                $site = $request->site;
            }
            else
            {
                $sensors = array();
                $site = 0;
            }

            $peopleController = new PeopleController();
            $sites = $peopleController->getUsersSites($standardDisplay['profile'], "all");

            
            return view('reports.exposure', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'dateRange' => $range,
                'widgetRange' => $widgetRange,
                'reading' => $reading,
                'results' => $results,
                'sensor' => $sensor,
                'sensors' => $sensors,
                'sites' => $sites,
                'site' => $site,
                'interval' => $interval,
            ]);
        }

    //end Exposure report

    //Test
        public function testEmail()
        {
            echo "Hello world";
            $email = new Email();
            $email->send_to = "Curtis Thomson";
            $email->send_email = "curtisjthomson@gmail.com";
            $email->subject = "From laravel queue";
            $email->content = "This is the body of the email. <b>Testing a tag</b>...<br><br><table><tr><td>Testing a table</td><td>This is a cell</td></tr></table>";
            $email->status = "pending";
            $email->save();

            emailSender::dispatch();

            /*
            $mail = new MailController();

            $emails = Email::where('status', '=', 'pending')->get();
            foreach($emails as $email)
            {
                $mail->html_email($email->id);
            }
            */
        }

        public function test()
        {
            echo date('d-m-Y H:i:s');
            echo "<br>Timezone : ";
            echo ini_get('date.timezone');
        }
    //End Test
}