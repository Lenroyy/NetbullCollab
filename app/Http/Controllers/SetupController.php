<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Models\Actions_Control;
use App\Models\Activities;
use App\Models\Activities_Trades;
use App\Models\Activities_Permits;
use App\Models\Api;
use App\Models\Assessment;
use App\Models\Assessments_Activities;
use App\Models\Assessments_Permit;
use App\Models\Assessments_Question;
use App\Models\Assessments_Questions_Answer;
use App\Models\Assessments_Questions_Answers_Option;
use App\Models\Assessments_Questions_Group;
use App\Models\Assessments_Site;
use App\Models\Controls_Type;
use App\Models\Controls_Type_Field;
use App\Models\Controls_Type_Group;
use App\Models\Controls_Types_Shapes;
use App\Models\Control;
use App\Models\Controls_Field;
use App\Models\Controls_Sites;
use App\Models\Cost_Center;
use App\Models\File;
use App\Models\Hazard;
use App\Models\Hazards_Trades;
use App\Models\Hazards_Activities;
use App\Models\Exposure;
use App\Models\News;
use App\Models\Permit;
use App\Models\Permits_Profile;
use App\Models\Permits_Training;
use App\Models\Permits_Type;
use App\Models\Profile;
use App\Models\Profiles_Trade;
use App\Models\Reading_Rules;
use App\Models\Sample;
use App\Models\Security_Groups;
use App\Models\Security_Group_Details;
use App\Models\Site;
use App\Models\Sites_Map;
use App\Models\Sites_Maps_Zone;
use App\Models\Sites_Maps_Zones_Hazard;
use App\Models\Thingsboards_Device;
use App\Models\Thingsboards_Device_Reading_Types;
use App\Models\Thingsboards_Readings_Type;
use App\Models\Trade;
use App\Models\Training;
use App\Models\Trainings_Hygenist;
use App\Models\Trainings_Types;
use App\Models\Video;

use App\Http\Controllers\ThingsboardController;


class SetupController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    


    //Users

        public function users()
        {
            /*
                Standard page setup
            */
            $alert = NULL;

            return $this->displayUsers($alert);
        }

        public function displayUsers($alert)
        {
            $standardDisplay = Controller::standardDisplay();
            $breadcrumbs = Controller::createBreadcrumbs("Users", NULL);        
            
            return view('dashboard.index', [
                'breadcrumbs' => $breadcrumbs,
                'alert' => $alert,
                'standardDisplay' => $standardDisplay,
            ]);
        }



    //Activities

        public function activities()
        {
            $alert = NULL;

            return $this->displayActivities($alert);
        }

        public function displayActivities($alert)
        {
            $standardDisplay = $this->checkFunctionPermission("activities:setup");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $breadcrumbs = Controller::createBreadcrumbs("Activities", NULL);

            $activityArray = array();
            $x = 0;
            $activities = Activities::where('archived', '=', '0')->orderBy('name', 'asc')->get();

            foreach($activities as $act)
            {
                $tradeArray = array();
                $y = 0;
                $trades = Activities_Trades::where('activities_id', '=', $act->id)->get();
                foreach($trades as $trade)
                {
                    $tradeArray[$y] = $trade->trades_id;
                    $y++;
                }

                $permitArray = array();
                $z = 0;
                $permits = Activities_Permits::where('activities_id', '=', $act->id)->get();
                foreach($permits as $permit)
                {
                    $permitArray[$z] = $permit->permits_id;
                    $z++;
                }

                $activityArray[$x]['activity'] = $act;  
                $activityArray[$x]['trades'] = $tradeArray;
                $activityArray[$x]['permits'] = $permitArray;
                                        
                $x++;
            }

            $trades = Trade::where('archived', '=', 0)->orderBy('name', 'asc')->get();
            $permits = Permit::where('archived', '=', 0)->orderBy('name', 'asc')->get();
            
            return view('setup.activities', [
                'breadcrumbs' => $breadcrumbs,
                'activities' => $activityArray,
                'trades' => $trades,
                'permits' => $permits,
                'alert' => $alert,
                'standardDisplay' => $standardDisplay,
            ]);
        }

        public function saveActivity(Request $request)
        {
            if($request->activity == 0)
            {
                $activity = new Activities();
                $activity->archived = 0;

                $alert = "Activity $request->name created.";
            }
            else
            {
                $activity = Activities::find($request->activity);
                $alert = "Activity $request->name updated.";
            }

            $activity->name = $request->name;
            $activity->save();

            //remove all trades and permits
            $delete = Activities_Trades::where('activities_id', '=', $activity->id)->delete();
            $delete = Activities_Permits::where('activities_id', '=', $activity->id)->delete();
            
            //save all the trades
            if(!empty($request->trades))
            {
                foreach($request->trades as $tr)
                {
                    $new = new Activities_Trades();
                    $new->activities_id = $activity->id;
                    $new->trades_id = $tr;
                    $new->save();
                }
            }
            else
            {
                //first just make sure everything is cleaned up
                $existingTrades = Activities_Trades::where('activities_id', '=', $activity->id)->delete();

                //now add all trades to this activity
                $trades = Trade::where('archived', '=', 0)->get();
                foreach($trades as $trade)
                {
                    $new = new Activities_Trades();
                    $new->trade_id = $trade->id;
                    $new->activities_id = $activity->id;
                    $new->save();
                }
            }

            //save all the permits
            if(!empty($request->permits))
            {
                foreach($request->permits as $p)
                {
                    $new = new Activities_Permits();
                    $new->activities_id = $activity->id;
                    $new->permits_id = $p;
                    $new->save();
                }
            }

            return $this->displayActivities($alert);
        }

        public function archiveActivity($permit)
        {
            $activity = Activities::find($permit);
            $activity->archived = 1;
            $activity->save();

            $alert = "Archived $activity->name";

            return $this->displayActivities($alert);
        }

    // End Activities
    
    
    //Trades

        public function trades($trade)
        {
            /*
                Standard page setup
            */
            $alert = NULL;

            return $this->displayTrades($alert, $trade);
        }
        
        public function displayTrades($alert, $trade)
        {
            $standardDisplay = $this->checkFunctionPermission("trades:setup");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $breadcrumbs = Controller::createBreadcrumbs("Trades", NULL);

            $trades = Trade::where('archived', '=', '0')->orderBy('name', 'asc')->get();

            $trade = Trade::find($trade);

            $hazards = array();
            $h = 0;
            if(!empty($trade))
            {
                $tds = Hazards_Trades::where('trade_id', '=', $trade->id)->get();
                foreach($tds as $td)
                {
                    $hazards[$h] = $td->hazard_id;
                    $h++;
                }

            }
            $allHazards = Hazard::where('archived', '=', 0)->orderBy('name', 'asc')->get();
            
            return view('setup.trades', [
                'breadcrumbs' => $breadcrumbs,
                'alert' => $alert,
                'trades' => $trades,
                'trade' => $trade,
                'standardDisplay' => $standardDisplay,
                'hazards' => $hazards,
                'allHazards' => $allHazards,
            ]);
        }

        public function saveTrade(Request $request)
        {
            if($request->trade == 0)
            {
                $trade = new Trade();
                $trade->qty = 0;
                $trade->archived = 0;

                $alert = "Trade $request->name created.";
            }
            else
            {
                $trade = Trade::find($request->trade);
                $alert = "Trade $request->name updated.";
            }

            $trade->est_hazards = $request->est_hazards;
            $trade->name = $request->name;
            $trade->save();

            //delete the hazards that didn't come through in the array
            if(!empty($request->hazards))
            {
                $existingHazards = Hazards_Trades::where('trade_id', '=', $trade->id)->get();
                foreach($existingHazards as $et)
                {
                    if(!in_array($et->hazard, $request->hazards))
                    {
                        $et->delete();
                    }
                }


                //save all the new hazards
                foreach($request->hazards as $hazard)
                {
                    $check = Hazards_Trades::where('hazard_id', '=', $hazard)
                                            ->where('trade_id', '=', $trade->id)
                                            ->count();
                    if($check == 0)                                        
                    {
                        $new = new Hazards_Trades();
                        $new->trade_id = $trade->id;
                        $new->hazard_id = $hazard;
                        $new->save();
                    }
                }
            }
            else
            {
                //lets just make sure everything is cleaned up
                $existingTrades = Hazards_Trades::where('trade_id', '=', $trade->id)->delete();
            }

            return $this->displayTrades($alert, 0);
        }

        public function archiveTrade($trade)
        {
            $trade = Trade::find($trade);
            $trade->archived = 1;
            $trade->save();

            $alert = "Archived $trade->name";

            return $this->displayTrades($alert, 0);
        }

        public function checkTrades()
        {
            $trade = Trade::get();
            $i = 0;
            
            foreach($trade as $t)
            {
                //Count the number of profiles with this trade
                $people = Profiles_Trade::where('trade_id', '=', $t->id)->count();
                $t->qty = $people;
                $t->save();
            }
            return $i;
        }

    //End trades


    //Permits

        public function permits()
        {
            /*
                Standard page setup
            */
            $alert = NULL;

            return $this->displayPermits($alert);
        }

        public function displayPermits($alert)
        {
            $standardDisplay = $this->checkFunctionPermission("permits:setup");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $breadcrumbs = Controller::createBreadcrumbs("Permits", NULL);

            $permitArray = array();
            $x = 0;
            $permits = Permit::where('archived', '=', '0')->orderBy('name', 'asc')->get();

            foreach($permits as $permit)
            {
                $trainingArray = array();
                $y = 0;
                $trainings = Permits_Training::where('permits_id', '=', $permit->id)->get();
                foreach($trainings as $training)
                {
                    $trainingArray[$y] = $training->trainings_id;
                    $y++;
                }

                $permitArray[$x]['permit'] = $permit;
                $permitArray[$x]['trainings'] = $trainingArray;
                                        
                $x++;
            }

            $trainings = Training::where('archived', '=', 0)->orderBy('name', 'asc')->get();
            $types = Permits_Type::orderBy('name', 'asc')->get();
            
            return view('setup.permits', [
                'breadcrumbs' => $breadcrumbs,
                'alert' => $alert,
                'permits' => $permitArray,
                'trainings' => $trainings,
                'types' => $types,
                'standardDisplay' => $standardDisplay,
            ]);
        }

        public function savePermit(Request $request)
        {
            if($request->permit == 0)
            {
                $permit = new Permit();
                $permit->qty = 0;
                $permit->archived = 0;

                $alert = "Permit $request->name created.";
            }
            else
            {
                $permit = Permit::find($request->permit);
                $alert = "Permit $request->name updated.";
            }

            $permit->name = $request->name;
            $permit->permits_types_id = $request->type;
            $permit->save();

            //remove all trainings
            $delete = Permits_Training::where('permits_id', '=', $permit->id)->delete();

            if(!empty($request->trainings))
            {
                foreach($request->trainings as $tr)
                {
                    $new = new Permits_Training();
                    $new->permits_id = $permit->id;
                    $new->trainings_id = $tr;
                    $new->save();
                }
            }

            return $this->displayPermits($alert);
        }

        public function archivePermit($permit)
        {
            $permit = Permit::find($permit);
            $permit->archived = 1;
            $permit->save();

            $alert = "Archived $permit->name";

            return $this->displayPermits($alert, 0);
        }

        public function checkPermits()
        {
            $permits = Permit::get();
            $i = 0;
            
            foreach($permits as $p)
            {
                //Count the number of profiles with this permit
                $people = Permits_Profile::where('permit_id', '=', $p->id)->count();
                $p->qty = $people;
                $p->save();
            }
            return $i;
        }

    // End Permits


    //Hazards

        public function hazards($hazard)
        {
            /*
                Standard page setup
            */
            $alert = NULL;

            return $this->displayHazards($alert, $hazard);
        }
        
        public function displayHazards($alert, $hazard)
        {
            $standardDisplay = $this->checkFunctionPermission("hazards:setup");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $breadcrumbs = Controller::createBreadcrumbs("Hazards", NULL);

            $hazards = Hazard::where('archived', '=', '0')->orderBy('name', 'asc')->get();

            $hazard = Hazard::find($hazard);

            $trades = array();
            $t = 0;
            if(!empty($hazard))
            {
                $tds = Hazards_Trades::where('hazard_id', '=', $hazard->id)->get();
                foreach($tds as $td)
                {
                    $trades[$t] = $td->trade_id;
                    $t++;
                }

            }

            $allTrades = Trade::where('archived', '=', 0)->orderBy('name', 'asc')->get();

            $activities = array();
            $a = 0;
            if(!empty($hazard))
            {
                $acts = Hazards_Activities::where('hazard_id', '=', $hazard->id)->get();
                foreach($acts as $act)
                {
                    $activities[$a] = $act->activity_id;
                    $a++;
                }

            }
            $allActivities = Activities::where('archived', '=', 0)->orderBy('name', 'asc')->get();
            
            
            return view('setup.hazards', [
                'breadcrumbs' => $breadcrumbs,
                'alert' => $alert,
                'hazards' => $hazards,
                'hazard' => $hazard,
                'standardDisplay' => $standardDisplay,
                'trades' => $trades,
                'allTrades' => $allTrades,
                'activities' => $activities,
                'allActivities' => $allActivities,
            ]);
        }

        public function saveHazard(Request $request)
        {
            if($request->hazard == 0)
            {
                $hazard = new Hazard();
                $hazard->archived = 0;

                $alert = "Hazard $request->name created.";
            }
            else
            {
                $hazard = Hazard::find($request->hazard);
                $alert = "Hazard $request->name updated.";
            }

            $hazard->name = $request->name;
            $hazard->save();

            //delete the trades that didn't come through in the array
            if(!empty($request->trades))
            {
                $existingTrades = Hazards_Trades::where('hazard_id', '=', $hazard->id)->get();
                foreach($existingTrades as $et)
                {
                    if(!in_array($et->trade_id, $request->trades))
                    {
                        $et->delete();
                    }
                }


                //save all the new trades
                foreach($request->trades as $trade)
                {
                    $check = Hazards_Trades::where('hazard_id', '=', $hazard->id)
                                            ->where('trade_id', '=', $trade)
                                            ->count();
                    if($check == 0)                                        
                    {
                        $new = new Hazards_Trades();
                        $new->trade_id = $trade;
                        $new->hazard_id = $hazard->id;
                        $new->save();
                    }
                }
            }
            else
            {
                //first just make sure everything is cleaned up
                $existingTrades = Hazards_Trades::where('hazard_id', '=', $hazard->id)->delete();

                //now add all trades to this hazard
                $trades = Trade::where('archived', '=', 0)->get();
                foreach($trades as $trade)
                {
                    $new = new Hazards_Trades();
                    $new->trade_id = $trade->id;
                    $new->hazard_id = $hazard->id;
                    $new->save();
                }
            }

            //Now do the same for the activities
            if(!empty($request->activities))
            {
                $existingActivities = Hazards_Activities::where('hazard_id', '=', $hazard->id)->get();
                foreach($existingActivities as $ea)
                {
                    if(!in_array($ea->activity_id, $request->activities))
                    {
                        $ea->delete();
                    }
                }


                //save all the new trades
                foreach($request->activities as $activity)
                {
                    $check = Hazards_Activities::where('hazard_id', '=', $hazard->id)
                                            ->where('activity_id', '=', $activity)
                                            ->count();
                    if($check == 0)                                        
                    {
                        $new = new Hazards_Activities();
                        $new->activity_id = $activity;
                        $new->hazard_id = $hazard->id;
                        $new->save();
                    }
                }
            }
            else
            {
                //lets just make sure everything is cleaned up
                $existingActivities = Hazards_Activities::where('hazard_id', '=', $hazard->id)->delete();
            }

            return $this->displayHazards($alert, 0);
        }

        public function archiveHazard($hazard)
        {
            $hazard = Hazard::find($hazard);
            $hazard->archived = 1;
            $hazard->save();

            $alert = "Archived $hazard->name";

            return $this->displayHazards($alert, 0);
        }

    // End Hazards


    //Samples

        public function samples($sample)
        {
            /*
                Standard page setup
            */
            $alert = NULL;

            return $this->displaySamples($alert, $sample);
        }
        
        public function displaySamples($alert, $sample)
        {
            $standardDisplay = $this->checkFunctionPermission("samples:setup");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $breadcrumbs = Controller::createBreadcrumbs("Samples", NULL);

            $samples = Sample::where('archived', '=', '0')->orderBy('name', 'asc')->get();

            $sample = Sample::find($sample);
            
            
            return view('setup.samples', [
                'breadcrumbs' => $breadcrumbs,
                'alert' => $alert,
                'samples' => $samples,
                'sample' => $sample,
                'standardDisplay' => $standardDisplay,
            ]);
        }

        public function saveSample(Request $request)
        {
            if($request->sample == 0)
            {
                $sample = new Sample();
                $sample->archived = 0;

                $alert = "Sample $request->name created.";
            }
            else
            {
                $sample = Sample::find($request->sample);
                $alert = "Sample $request->name updated.";
            }

            $sample->name = $request->name;
            $sample->measurement = $request->measurement;
            $sample->save();

            return $this->displaySamples($alert, 0);
        }

        public function archiveSample($sample)
        {
            $sample = Sample::find($sample);
            $sample->archived = 1;
            $sample->save();

            $alert = "Archived $sample->name";

            return $this->displaySamples($alert, 0);
        }

    //End samples


    //Training

        public function training()
        {
            /*
                Standard page setup
            */
            $alert = NULL;

            return $this->displayTraining($alert);
        }

        public function displayTraining($alert)
        {
            $standardDisplay = $this->checkFunctionPermission("training:setup");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $breadcrumbs = Controller::createBreadcrumbs("Services", NULL);

            $this->checkTrainingHygenists();
            $trainings = Training::where('archived', "=", '0')->orderBy('name', 'asc')->get();
            $training = array();
            $x = 0;

            foreach($trainings as $t)
            {
                $tHygenists = array();
                $y = 0;

                $hyg = Trainings_Hygenist::where('training_id', '=', $t->id)->get();
                foreach($hyg as $h)
                {
                    //check to make sure this hygenist exists and hasn't been deleted.
                    $hProfile = Profile::find($h->profile_id);
                    {
                        if(is_object($hProfile))
                        {
                            $tHygenists[$y] = $h;
                            $y++;
                        }
                        else
                        {
                            $h->delete();
                        }
                    }
                    
                }
                $training[$x]['training'] = $t;
                $training[$x]['hygenists'] = $tHygenists; 

                $x++;
            }

            $hygenists = Profile::where('type', '=', 'hygenist')
                                    ->where('archived', '=', '0')
                                    ->orderBy('name', 'asc')
                                    ->get();

            $trainingTypes = Trainings_Types::where('archived', '=', 0)->orderBy('name', 'asc')->get();

            
            return view('setup.training', [
                'breadcrumbs' => $breadcrumbs,
                'alert' => $alert,
                'training' => $training,
                'hygenists' => $hygenists,
                'standardDisplay' => $standardDisplay,
                'trainingTypes' => $trainingTypes,
            ]);
        }

        public function saveTraining(Request $request)
        {
            if($request->training == 0)
            {
                $training = new Training();
                $training->archived = 0;

                $alert = "Training $request->name created.";
            }
            else
            {
                $training = Training::find($request->training);
                $alert = "Training $request->name updated.";
            }

            $training->name = $request->name;
            $training->price = $request->price;
            $training->link = $request->link;
            $training->description = $request->description;
            $training->training_type_id = $request->type;

            $training->save();
            
            foreach($request->hygenists as $hygenist)
            {

                $up = Trainings_Hygenist::where('training_id', '=', $training->id)
                                            ->where('profile_id', '=', $hygenist['id'])
                                            ->first();
                if(is_object($up))
                {
                    $up->link = $hygenist['link'];
                    $up->price = $hygenist['price'];

                    $active = 0;
                    if(!empty($hygenist['activeProvider']))
                    {
                        $active = 1;
                    }
                    $up->active_provider = $active;
                    $up->save();
                }
                else
                {

                    $new = new Trainings_Hygenist();
                    $new->training_id = $training->id;
                    $new->profile_id = $hygenist['id'];

                    if($hygenist['link'] == "")
                    {
                        $link = $request->link;
                    }
                    else
                    {
                        $link = $hygenist['link'];
                    }

                    if($hygenist['price'] == "")
                    {
                        $price = $request->price;
                    }
                    else
                    {
                        $price = $hygenist['price'];
                    }
                    $active = 0;
                    if(!empty($hygenist['activeProvider']))
                    {
                        $active = 1;
                    }
                    
                    $new->active_provider = $active;
                    $new->link = $link;
                    $new->price = $price;

                    $new->save();
                }
            }


            $training->save();

            return $this->displayTraining($alert, 0);
        }

        public function archiveTraining($training)
        {
            $training = Training::find($training);
            $training->archived = 1;
            $training->save();

            $alert = "Archived $training->name";

            return $this->displayTraining($alert);
        }

        public function checkTrainingHygenists()
        {
            $i = 0;
            $trainings = Training::get();
            
            foreach($trainings as $t)
            {
                //go through all hygenists and make sure they are on this training program
                $hygenists = Profile::where('type', '=', 'hygenist')->get();
                foreach($hygenists as $h)
                {
                    $th = Trainings_Hygenist::where('training_id', '=', $t->id)
                                                ->where('profile_id', '=', $h->id)
                                                ->count();
                    //if not add them and use the default values
                    if($th == 0)
                    {
                        $new = new Trainings_Hygenist();
                        $new->profile_id = $h->id;
                        $new->training_id = $t->id;
                        $new->price = $t->price;
                        $new->link = $t->link;
                        $new->save();

                        $i++;
                    }
                }

                //check to make sure that other profiles that are not hygenists are not on the training
                $checkProfiles = Trainings_Hygenist::where('training_id', '=', $t->id)->get();
                foreach($checkProfiles as $cp)
                {
                    $profile = Profile::find($cp->profile_id);
                    if(is_object($profile))
                    {
                        if($profile->type != "hygenist")
                        {
                            $cp->delete();
                        }

                        if($profile->archived == 1)
                        {
                            $cp->delete();
                        }
                    }
                    else
                    {
                        $cp->delete();    
                    }
                }
            }



            return $i;
        }

        public function trainingTypes($type)
        {
            /*
                Standard page setup
            */
            $alert = NULL;

            return $this->displayTrainingTypes($type, $alert);
        }
        
        public function displayTrainingTypes($type, $alert)
        {
            $standardDisplay = $this->checkFunctionPermission("training:setup");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }
            
            $breadcrumbs = Controller::createBreadcrumbs("Training types", NULL);

            $types = Trainings_Types::where('archived', '=', '0')->orderBy('name', 'asc')->get();

            $type = Trainings_Types::find($type);
            
            
            return view('setup.trainingTypes', [
                'breadcrumbs' => $breadcrumbs,
                'alert' => $alert,
                'types' => $types,
                'type' => $type,
                'standardDisplay' => $standardDisplay,
            ]);
        }

        public function saveTrainingTypes(Request $request)
        {
            if($request->type == 0)
            {
                $type = new Trainings_Types();
                $type->archived = 0;

                $alert = "Service type $request->name created.";
            }
            else
            {
                $type = Trainings_Types::find($request->type);
                $alert = "Service type $request->name updated.";
            }

            $type->name = $request->name;
            $type->save();

            return $this->displayTrainingTypes(0, $alert);
        }

        public function archiveTrainingTypes($type)
        {
            $type = Trainings_Types::find($type);
            $type->archived = 1;
            $type->save();

            $alert = "Service type $type->name archived.";

            return $this->displayTrainingTypes(0, $alert);
        }

    //End training


    //Assessments

        public function assessments()
        {
            /*
                Standard page setup
            */
            $alert = NULL;

            return $this->displayAssessments($alert);
        }

        public function displayAssessments($alert)
        {
            $standardDisplay = $this->checkFunctionPermission("swms:setup");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $breadcrumbs = Controller::createBreadcrumbs("Assessments", NULL);
            
            $assessments = Assessment::where('archived', '=', 0)->orderBy('name', 'asc')->get();
            
            return view('setup.assessments', [
                'breadcrumbs' => $breadcrumbs,
                'alert' => $alert,
                'standardDisplay' => $standardDisplay,
                'assessments' => $assessments,
            ]);
        }

        public function editAssessment($assessment)
        {
            if($assessment == 0)
            {
                $assessment = new Assessment();
                $assessment->id = 0;
                $assessment->name = "New";
            }
            else
            {
                $assessment = Assessment::find($assessment);
            }

            $standardDisplay = Controller::standardDisplay();
            $breadcrumbs = Controller::createBreadcrumbs("Assessments", $assessment->name);

            if(!in_array("swms:setup", $standardDisplay['permissions']))
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $activities = Activities::orderBy('name', 'asc')->where('archived', '=', 0)->get();
            $assessmentActivities = Assessments_Activities::where('assessment_id', '=', $assessment->id)->get();
            $aArray = array();
            $aa = 0;
            foreach($assessmentActivities as $assessmentActivity)
            {
                $aArray[$aa] = $assessmentActivity->activities_id;
                $aa++; 
            }

            $permits = Permit::orderBy('name', 'asc')->where('archived', '=', 0)->get();
            $assessmentPermits = Assessments_Permit::where('assessment_id', '=', $assessment->id)->get();
            $pArray = array();
            $ap = 0;
            foreach($assessmentPermits as $assessmentPermit)
            {
                $pArray[$ap] = $assessmentPermit->permits_id;
                $ap++; 
            }

            $sites = Site::orderBy('name', 'asc')->where('archived', '=', 0)->get();
            $assessmentSites = Assessments_Site::where('assessments_id', '=', $assessment->id)->get();
            $sArray = array();
            $as = 0;
            foreach($assessmentSites as $assessmentSite)
            {
                $sArray[$as] = $assessmentSite->sites_id;
                $as++; 
            }

            $answerOptions = Assessments_Questions_Answers_Option::orderBy('name', 'asc')->where('archived', '=', 0)->get();

            $questions = $this->getQuestionArray($assessment->id, 0);

            $questionList = Assessments_Question::where('assessment_id', '=', $assessment->id)
                                                    ->orderBy('question', 'asc')
                                                    ->get();

            $groups = Assessments_Questions_Group::where('assessment_id', '=', $assessment->id)
                                                    ->where('parent_id', '=', 0)
                                                    ->get();
            
            $allGroups = Assessments_Questions_Group::where('assessment_id', '=', $assessment->id)
                                                    ->get();

            return view('setup.editAssessment', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'assessment' => $assessment,
                'activities' => $activities,
                'assessmentActivities' => $aArray,
                'permits' => $permits,
                'assessmentPermits' => $pArray,
                'sites' => $sites,
                'assessmentSites' => $sArray,
                'answerOptions' => $answerOptions,
                'questions' => $questions,
                'groups' => $groups,
                'allGroups' => $allGroups,
                'questionList' => $questionList,
            ]);
        }

        public function getQuestionArray($assessment, $parent)
        {
            $i = 0;
            $array = array();
            $groups = Assessments_Questions_Group::where('assessment_id', '=', $assessment)
                                                    ->where('parent_id', '=', $parent)
                                                    ->get();
            foreach($groups as $group)
            {
                $questionArray = array();
                $q = 0;

                $questions = Assessments_Question::where('assessment_id', '=', $assessment)
                                                    ->where('assessments_questions_group_id', '=', $group->id)
                                                    ->get();
                foreach($questions as $question)
                {
                    $questionArray[$q]['question'] = $question->question;
                    $questionArray[$q]['question_id'] = $question->id;

                    $q++;
                }

                $children = $this->getQuestionArray($assessment, $group->id);
                
                $array[$i]['group_id'] = $group->id;
                $array[$i]['group_name'] = $group->name;
                $array[$i]['questions'] = $questionArray;
                $array[$i]['children'] = $children;
                
                $i++;
            }

            return $array;
        }

        public function getAssessmentQuestions($assessment)
        {
            $i = 0;
            $array = array();
            $groups = Assessments_Questions_Group::where('assessment_id', '=', $assessment)->get();
            foreach($groups as $group)
            {
                $questionArray = array();
                $q = 0;

                $questions = Assessments_Question::where('assessment_id', '=', $assessment)
                                                    ->where('assessments_questions_group_id', '=', $group->id)
                                                    ->get();
                foreach($questions as $question)
                {
                    $questionArray[$q]['question'] = $question->question;
                    $questionArray[$q]['question_id'] = $question->id;

                    $q++;
                }
                
                $array[$i]['group_id'] = $group->id;
                $array[$i]['group_name'] = $group->name;
                $array[$i]['questions'] = $questionArray;
                
                $i++;
            }

            return json_encode($array);
        }

        public function saveAssessment(Request $request, $assessment)
        {
            if($assessment == 0)
            {
                $assessment = new Assessment();
                $assessment->archived = 0;
            }
            else
            {
                $assessment = Assessment::find($assessment);
            }

            $assessment->name = $request->name;
            $assessment->once_per = $request->oncePer;
            $assessment->save();

            //Handle all of the permits, activities and sites multidrop downs
            $this->handleMultiDropdowns($request, $assessment);

            if($request->submit == "Save group")
            {
                if($request->groupId == 0)
                {
                    $group = new Assessments_Questions_Group();
                }
                else
                {
                    $group = Assessments_Questions_Group::find($request->groupId);
                }
                $group->name = $request->groupName;
                $group->parent_id = $request->groupParent;
                $group->assessment_id = $assessment->id;
                
                $group->save();

                return $this->editAssessment($assessment->id);
            }
            elseif($request->submit == "Save question")
            {
                if($request->questionId == "0")
                {
                    $question = new Assessments_Question();
                }
                else
                {
                    $question = Assessments_Question::find($request->questionId);
                }
                $question->assessment_id = $assessment->id;
                $question->question = $request->questionName;
                $question->assessments_questions_group_id = $request->questionGroup;
                $question->answer_type = $request->answerType;
                $question->save();

                //clean up unwanted answers
                $answers = Assessments_Questions_Answer::where('question_id', '=', $question->id)->get();
                foreach($answers as $a)
                {
                    if(is_array($request->answerOptions))
                    {
                        if(!in_array($a->option_id, $request->answerOptions))
                        {
                            $a->delete();
                        }
                    }
                }

                if(is_array($request->answerOptions))
                {
                    foreach($request->answerOptions as $option)
                    {
                        //check to see if its already there
                        $check = Assessments_Questions_Answer::where('question_id', '=', $question->id)
                                                                ->where('option_id', '=', $option)
                                                                ->count();
                        if($check == 0)
                        {
                            $new = new Assessments_Questions_Answer();

                            $new->question_id = $question->id;
                            $new->option_id = $option;
                            
                            $new->save();
                        }
                    }
                }

                //go through the option config table
                if(is_array($request->answerId))
                {
                    foreach($request->answerId as $key => $value)
                    {
                        $answer = Assessments_Questions_Answer::find($value);
                        $answer->action = $request->action[$key];
                        if($answer->action == "jump")
                        {
                            $answer->goto_id = $request->goto[$key];
                        }
                        else
                        {
                            $answer->goto_id = NULL;
                        }
                        $answer->score = $request->score[$key];
                        $answer->comments = $request->comments[$key];

                        $answer->save();
                    }
                }
                return $this->editAssessment($assessment->id);
            }
            else
            {
                $alert = "Assessment " . $assessment->name . " saved.";
                return $this->displayAssessments($alert);
            }
        }

        public function handleMultiDropdowns($request, $assessment)
        {
            $delete = Assessments_Activities::where('assessment_id', '=', $assessment->id)->delete();
            if(is_array($request->activities))
            {
                foreach($request->activities as $activity)
                {
                    $insert = new Assessments_Activities();
                    $insert->assessment_id = $assessment->id;
                    $insert->activities_id = $activity;
                    $insert->save();
                }
            }

            $delete = Assessments_Site::where('assessments_id', '=', $assessment->id)->delete();
            if(is_array($request->sites))
            {
                foreach($request->sites as $site)
                {
                    $insert = new Assessments_Site();
                    $insert->assessments_id = $assessment->id;
                    $insert->sites_id = $site;
                    $insert->save();

                }
            }

            $delete = Assessments_Permit::where('assessment_id', '=', $assessment->id)->delete();
            if(is_array($request->permits))
            {
                foreach($request->permits as $permit)
                {
                    $insert = new Assessments_Permit();
                    $insert->assessment_id = $assessment->id;
                    $insert->permits_id = $permit;
                    $insert->save();
                }
            }

            return 1;
        }

        public function archiveAssessment($assessment)
        {
            $assessment = Assessment::find($assessment);
            $assessment->archived = 1;
            $assessment->save();

            $alert = "Archived $assessment->name";

            return $this->displayAssessments($alert);
        }

        public function getGroup($group)
        {
            $group = Assessments_Questions_Group::find($group);

            return json_encode($group);
        }

        public function getQuestion($question)
        {
            $question = Assessments_Question::find($question);

            return json_encode($question);        
        }

        public function getAnswers($question)
        {
            $options = Assessments_Questions_Answer::where('question_id', '=', $question)->get();

            return json_encode($options);
        }

        public function getAnswerDetails($question)
        {
            $options = Assessments_Questions_Answer::where('question_id', '=', $question)->get();
            $optionArray = array();
            $i = 0;

            foreach($options as $option)
            {
                $optionArray[$i]['id'] = $option->id;
                $optionArray[$i]['answer'] = $option->Assessments_Questions_Answers_Option->name;
                $optionArray[$i]['action'] = $option->action;
                $optionArray[$i]['score'] = $option->score;
                $optionArray[$i]['comments'] = $option->comments;
                if($option->goto_id > 0)
                {
                    $optionArray[$i]['goto'] = $option->goto->question;
                    $optionArray[$i]['goto_id'] = $option->goto_id;
                }
                else
                {
                    $optionArray[$i]['goto'] = "unset";
                    $optionArray[$i]['goto_id'] = 0;
                }
                $i++;
            }

            return json_encode($optionArray);
        }

    //End Assessments
    

    //Control types

        public function controlTypes()
        {
            /*
                Standard page setup
            */
            $alert = NULL;

            return $this->displayControlTypes($alert);
        }

        public function displayControlTypes($alert)
        {
            $standardDisplay = $this->checkFunctionPermission("controls:setup");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $breadcrumbs = Controller::createBreadcrumbs("Control types", NULL);      

            $controlTypes = Controls_Type::where('archived', '=', '0')->orderBy('name', 'asc')->get();
            
            return view('setup.controlTypes', [
                'breadcrumbs' => $breadcrumbs,
                'alert' => $alert,
                'controlTypes' => $controlTypes,
                'standardDisplay' => $standardDisplay,
            ]);
        }

        public function getTypeControls($type)
        {
            /* 
                Similar to PeopleControllers getSiteControls, this gets all the controls of a type and puts them into an array
                with the first three fields.
                $type is the Control Type ID
            */   
            $array = array();         
            $fieldsArray = array();
            $f = 0;

            $fields = Controls_Type_Field::where('controls_type_id', '=', $type)
                                            ->orderBy('id', 'asc')
                                            ->limit(3)
                                            ->get();
            foreach($fields as $field)
            {
                $fieldsArray[$f] = $field->name;
                $f++;
            }

            $controlsArray = array();
            $c = 0;
            $controls = Control::where('archived', '=', 0)
                                ->where('controls_type_id', '=', $type)
                                ->get();
            foreach($controls as $control)
            {
                $valuesArray = array();
                $v = 0;
                foreach($fields as $field)
                {
                    $value = Controls_Field::where('control_id', '=', $control->id)   
                                            ->where('control_field_id', '=', $field->id)
                                            ->first();
                    if(is_object($value))
                    {
                        $valuesArray[$v]['value'] = $value->value;
                        $valuesArray[$v]['field'] = $field->name;
                        $v++;
                    }
                }

                $controlsArray[$c]['control'] = $control;
                $controlsArray[$c]['fieldValues'] = $valuesArray;

                unset($control);
                unset($valuesArray);

                $c++;
            }

            $array['fields'] = $fieldsArray;
            $array['controls'] = $controlsArray;

            return $array;
        }

        public function editControlType($type)
        {
            $standardDisplay = Controller::standardDisplay();
            $breadcrumbs = Controller::createBreadcrumbs("Control type", "Edit");  
            
            if($type > 0)
            {
                $controlType = Controls_Type::find($type);
            }
            else
            {
                $controlType = new Controls_Type();
                $controlType->id = 0;
            }

            $typeFields = Controls_Type_Field::where('controls_type_id', '=', $controlType->id)->get();
            
            $controls = $this->getTypeControls($controlType->id);

            $module = 'controlTypes';
            $moduleID = $controlType->id;
            $files = File::where('module', '=', $module)
                            ->where('module_id', '=', $moduleID)
                            ->get();
            
            $logs = $this->retrieveLogs($module, $moduleID);

            $sites = Site::where('archived', '=', 0)
                            ->where('status', '=', 'active')
                            ->orderBy('name', 'asc')
                            ->get();

            $shapes = Controls_Types_Shapes::orderBy('shape', 'asc')->get();

            $typeGroups = Controls_Type_Group::where('archived', '=', 0)
                                                ->orderBy('name', 'asc')
                                                ->get();
            $simPROInt = Api::find(2);
            $simPROSettings = NULL;
            if($simPROInt->settings)
            {
                $simPROSettings = json_decode($simPROInt->settings);
            }
            $costCenters = Cost_Center::orderBy('company_id', 'asc')->orderBy('cost_center_name', 'asc')->get();
            
            return view('setup.editControlType', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'files' => $files,
                'module' => $module,
                'moduleID' => $moduleID,
                'logs' => $logs,
                'shapes' => $shapes,
                'controlType' => $controlType,
                'typeFields' => $typeFields,
                'controls' => $controls,
                'sites' => $sites,
                'typeGroups' => $typeGroups,
                'simPROSettings' => $simPROSettings,
                'costCenters' => $costCenters,
            ]);
        }

        public function saveVideo(Request $request)
        {
            $standardDisplay = Controller::standardDisplay();
            $type = Controls_type::find($request->controlType);

            if($request->video == 0)
            {
                $video = new Video;
            }
            else
            {
                $video = Video::find($request->video);
            }

            $video->control_type_id = $type->id;
            $video->type = $request->type;
            $video->code = $request->code;

            $video->save();

            return $this->editControlType($type->id);
        }

        public function deleteVideo($video)
        {
            $standardDisplay = Controller::standardDisplay();
            $video = Video::find($video);
            $type = $video->control_type_id;

            $video->delete();

            return $this->editControlType($type);
            
        }

        

        public function saveControlType(Request $request)
        {
            $standardDisplay = Controller::standardDisplay();

            if($request->controlType == 0)
                {
                    $type = new Controls_Type();
                    $type->archived = 0;
                    $action = "created";
                }
                else
                {
                    $type = Controls_type::find($request->controlType);
                    $action = "updated";
                }

                $alert = "Control type $request->name $action.";

                $type->name = $request->name;
                $type->manufacturer = $request->manufacturer;
                $type->simpro_asset_type_id_1 = $request->simpro_asset_type_id;
                $type->billing_amount = $request->internalLease;
                $type->external_billing_amount = $request->externalLease;
                $type->monitoring_only_billing_amount = $request->monitoringAmount;
                $type->sale_amount = $request->saleAmount;
                $type->billing_frequency = $request->billingFrequency;
                $type->shape = $request->shape;
                $type->control_type_group = $request->typeGroup;
                $type->internal_lease_cost_center_id = $request->internal_lease_cost_center_id;
                $type->external_lease_cost_center_id = $request->external_lease_cost_center_id;
                $type->sale_cost_center_id = $request->sale_cost_center_id;
                $type->monitoring_only_cost_center_id = $request->monitoring_only_cost_center_id;
                
                if(!empty($request->file('image')))
                {
                    $image = $request->file('image')->store('images/controls', 'public');
                    $type->image = $image;
                }

                $type->save();

                if($request->newFieldName != "")
                {
                    //add a new field to the control type
                    $field = new Controls_Type_Field();
                    $field->name = $request->newFieldName;
                    $field->controls_type_id = $type->id;
                    $field->archived = 0;

                    $field->save();

                    //go through and add this field to all of the controls of this type
                    $theseControls = Control::where('controls_type_id', '=', $type->id)->get();
                    foreach($theseControls as $tc)
                    {
                        $newF = new Controls_Field();
                        $newF->control_field_id = $field->id;
                        $newF->control_id = $tc->id;

                        $newF->save();
                    }
                }

                if(isset($request->fields))
                {
                    foreach($request->fields as $key=>$value)
                    {
                        $field = Controls_Type_field::find($request->fieldIDs[$key]);
                        $field->name = $value;
                        $field->save();
                    }
                }

                if($request->hasfile('files'))
                {
                    foreach($request->file('files') as $file)
                    {
                        $currentMonth = date('Y-m');
                        $originalName = $file->getClientOriginalName();
                        $fileSize = round(($file->getSize())/1024, 0);
                        $thisFile = $file->store('attachments/' . $currentMonth, 'public');

                        $f = new File();
                        $f->filename = $thisFile;
                        $f->original_name = $originalName;
                        $f->file_size = $fileSize;
                        $f->module_id = $type->id;
                        $f->module = "controlTypes";
                        
                        $f->save();
                    }
                }

                $this->insertLog($standardDisplay['profile']->id, "controlTypes", $type->id, $action, "$type->name $action.", "INFO");

                if($request->submit == "Save")
                {
                    return $this->displayControlTypes($alert);
                }
                else
                {
                    return $this->editControlType($type->id);
                }
        }

        public function archiveControlType($type)
        {
            $standardDisplay = $this->standardDisplay();

            $type = Controls_Type::find($type);
            $type->archived = 1;
            $type->save();

            $alert = "Controls type $type->name archived";

            $this->insertLog($standardDisplay['profile']->id, "controlTypes", $type->id, "Archived", "$type->name deleted.", "WARNING");

            return $this->displayControlTypes($alert);
        }

        public function saveControl(Request $request)
        {
            $standardDisplay = Controller::standardDisplay();

            if($request->control == 0)
            {
                $type = Controls_Type::find($request->controlType);

                $control = new Control();
                $control->archived = 0;
                $control->controls_type_id = $type->id;
                $control->deployed = 0;
                $action = "created";
                
            }
            else
            {
                $control = Control::find($request->control);
                
                $type = Controls_Type::find($control->controls_type_id);

                $action = "updated";
            }

            $alert = "Control $request->name $action.";

            if(empty($request->billingAmount))
            {
                $amount = $type->billing_amount;
            }
            else 
            {
                $amount = $request->billingAmount;
            }

            if(empty($request->billingFrequency))
            {
                $frequency = $type->billing_frequency;
            }
            else 
            {
                $frequency = $request->billingFrequency;
            }

            $control->serial = $request->serial;
            $control->simpro_asset_id_1 = $request->simproAssetID;
            $control->commission_date = $request->commissionDate;
            $control->colour = $request->colour;
            $control->billing_amount = $amount;
            $control->billing_frequency = $frequency;
            $control->billing = $request->billing;
            if($request->commencementDate)
            {
                $control->billing_commencement = $this->flipDate($request->commencementDate);
            }

            $control->save();

            if(isset($request->fieldID))
            {
                foreach($request->fieldValue as $key=>$value)
                {
                    //check to make sure this control has this field, if not add it
                    $controlField = Controls_Field::where('control_field_id', '=', $request->fieldID[$key])
                                                        ->where('control_id', '=', $control->id)
                                                        ->get();
                    if(count($controlField) == 0)
                    {
                        $field = new Controls_Field();
                        $field->control_field_id = $request->fieldID[$key];
                        $field->control_id = $control->id;
                        $field->save();
                    }
                    else
                    {
                        $field = $controlField{0};
                    }
                    
                    $field->value = $value;
                    $field->save();
                }
            }

            if($request->hasfile('files'))
            {
                foreach($request->file('files') as $file)
                {
                    $currentMonth = date('Y-m');
                    $originalName = $file->getClientOriginalName();
                    $fileSize = round(($file->getSize())/1024, 0);
                    $thisFile = $file->store('attachments/' . $currentMonth, 'public');

                    $f = new File();
                    $f->filename = $thisFile;
                    $f->original_name = $originalName;
                    $f->file_size = $fileSize;
                    $f->module_id = $control->id;
                    $f->module = "controls";
                    
                    $f->save();
                }
            }

            $this->insertLog($standardDisplay['profile']->id, "controls", $control->id, $action, "$control->serial $action.", "INFO");

            return $this->editControlType($control->controls_type_id);
        }

        public function removeControl($control)
        {
            $control = Control::find($control);
            $latestTransfer = Controls_Sites::where('control_id', '=', $control->id)->orderby('id', 'desc')->first();
            
            $request = new Request();

            $request->control_id = $control->id;
            
            $request->fromSite = Site::find($latestTransfer->to_site_id);
            $request->toSite = 0;

            $request->fromMap = Sites_Map::find($latestTransfer->to_map_id);
            $request->toMap = 0;

            $request->fromZone = Sites_Maps_Zone::find($latestTransfer->to_zone_id);
            $request->toZone = 0;

            $request->fromHazard = Sites_Maps_Zones_Hazard::find($latestTransfer->to_hazard_id);
            $request->toHazard = 0;

            $request->originator = "controlType";
            $request->moduleID = $control->controls_type_id;

            $this->resetControlCoords($control->id);

            return $this->moveControl($request, $control->id);
        }

        public function moveControl(Request $request, $control)
        {
            $standardDisplay = Controller::standardDisplay();
            $control = Control::find($control);
            $this->resetControlCoords($control->id);

            //find out what site the control is currently on
            if($control->deployed == 0)
            {
                $fromSite = "Storage";
                $fromSiteID = "0";
            }
            else
            {
                $fromSite = $control->Site->name;
                $fromSiteID = $control->Site->id;
            }

            if(isset($request->fromZone))
            {
                $fromZoneID = $request->fromZone;
                $toZoneID = $request->toZone;
            }
            else
            {
                $fromZoneID = 0;
                $toZoneID = 0;
            }

            if(isset($request->fromHazard))
            {
                $fromHazardID = $request->fromHazard->id;
                $toHazardID = $request->toHazard;
            }
            else
            {
                $fromHazardID = 0;
                $toHazardID = 0;
            }

            if(isset($request->fromMap))
            {
                $fromMapID = $request->fromMap;
                if(is_object($fromMapID))
                {
                    $fromMapID = $fromMapID->id;
                }
                $toMapID = $request->toMap;
            }
            else
            {
                $fromMapID = 0;
                $toMapID = 0;
            }

            if($request->site > 0)
            {
                $toSite = Site::find($request->site);
                $control->deployed = $toSite->id;
                $control->current_site = $toSite->id;
            }
            else
            {
                $toSite = new Site();
                $toSite->id = 0;
                $toSite->name = "Storage";
                $control->deployed = 0;
                $control->current_site = 0;
            }
            
            
            $control->save();

            //insert the transfer record
            $transfer = new Controls_Sites();

            if(is_object($fromZoneID))
            {
                $fromZoneID = $fromZoneID->id;
            }

            $transfer->control_id = $control->id;
            $transfer->from_site_id = $fromSiteID;
            $transfer->to_site_id = $toSite->id;
            $transfer->from_map_id = $fromMapID;
            $transfer->to_map_id = $toMapID;
            $transfer->from_zone_id = $fromZoneID;
            $transfer->to_zone_id = $toZoneID;
            $transfer->from_hazard_id = $fromHazardID;
            $transfer->to_hazard_id = $toHazardID;

            $transfer->save();

            //Insert the relevant logs        
            if($fromSite != $toSite)
            {
                $action = "Control moved from site " . $fromSite . " to " . $toSite->name;
                $this->insertLog($standardDisplay['profile']->id, "controls", $control->id, "Moved", "$action.", "INFO");
            }
            if($fromMapID != $toMapID)
            {
                $fromMap = Sites_Map::find($fromMapID);
                if(is_object($fromMap))
                {
                    $fromMap = $fromMap->name;
                }
                else
                {
                    $fromMap = "-";
                }
                $toMap = Sites_Map::find($toMapID);
                if(is_object($toMap))
                {
                    $toMap = $toMap->name;
                }
                else
                {
                    $toMap = "-";
                }

                $action = "Control moved from map " . $fromMap . " to " . $toMap;
                $this->insertLog($standardDisplay['profile']->id, "controls", $control->id, "Moved", "$action.", "INFO");
            }
            if($fromZoneID != $toZoneID)
            {
                $fromZone = Sites_Maps_Zone::find($fromZoneID);
                if(is_object($fromZone))
                {
                    $fromZone = $fromZone->name;
                }
                else
                {
                    $fromZone = "-";
                }
                $toZone = Sites_Maps_Zone::find($toZoneID);
                if(is_object($toZone))
                {
                    $toZone = $toZone->name;
                }
                else
                {
                    $toZone = "-";
                }

                $action = "Control moved from zone " . $fromZone . " to " . $toZone;
                $this->insertLog($standardDisplay['profile']->id, "controls", $control->id, "Moved", "$action.", "INFO");
            }

            if($request->originator == "controlType")
            {
                return $this->editControlType($request->moduleID);
            }
            if($request->originator == "completeSite")
            {
                return 1;
            }
        }

        public function editControl($control)
        {
            $standardDisplay = Controller::standardDisplay();
            $breadcrumbs = Controller::createBreadcrumbs("Control", "Edit");  

            if(!in_array("controls:edit", $standardDisplay['permissions']))
            {
                if($standardDisplay['profile']->super_user == 0)
                {
                    $index = new HomeController();
                    $alert = "You do not have privileges to do that.";

                    return $index->displayDashboard($alert);
                }
            }
            
            if($control > 0)
            {
                $control = Control::find($control);

                if(empty($control->commission_date))
                {
                    $today = date('Y-m-d');
                    
                    $control->commission_date = $today;
                    $control->save();
                }

                if(empty($control->billing))
                {
                    $control->billing = "yes";
                    $control->save();
                }
            }
            else
            {
                $index = new HomeController();
                $alert = "Control doesn't exist, not sure how you got here.";

                return $index->displayDashboard($alert);
            }

            $currentLocation = $this->getControlCurrentLocation($control);

            //check to make sure this control has all the fields it should have
            $checkFields = Controls_Type_Field::where('controls_type_id', '=', $control->controls_type_id)->get();
            foreach($checkFields as $cf)
            {
                $conField = Controls_Field::where('control_field_id', '=', $cf->id)
                                            ->where('control_id', '=', $control->id)
                                            ->first();
                if(!is_object($conField))
                {
                    $newF = new Controls_Field();
                    $newF->control_field_id = $cf->id;
                    $newF->control_id = $control->id;

                    $newF->save();
                }
            }

            //check to make sure the control doesn't have any fields that shouldn't be there.
            $fields = Controls_Field::where('control_id', '=', $control->id)->get();
            foreach($fields as $field)
            {
                $checkField = Controls_Type_Field::where('controls_type_id', '=', $control->controls_type_id)->where('id', '=', $field->control_field_id)->count();
                if($checkField == 0)
                {
                    $field->delete();
                }
            }
            $fields = Controls_Field::where('control_id', '=', $control->id)->get();
            
            $transfers = Controls_Sites::where('control_id', '=', $control->id)
                                            ->get();

            $module = 'controls';
            $moduleID = $control->id;
            $files = File::where('module', '=', $module)
                                    ->where('module_id', '=', $moduleID)
                                    ->get();
            $logs = $this->retrieveLogs($module, $moduleID);

            $spareMonitors = Thingsboards_Device::whereNull('control_id')->where('archived', '=', 0)->get();
            $monitors = Thingsboards_Device::where('control_id', '=', $control->id)->get();

            $activities = Actions_Control::where('control_id', '=', $control->id)->get();
            
            return view('equipment.editControl', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'files' => $files,
                'module' => $module,
                'moduleID' => $moduleID,
                'logs' => $logs,
                'control' => $control,
                'fields' => $fields,
                'transfers' => $transfers,
                'currentLocation' => $currentLocation,
                'spareMonitors' => $spareMonitors,
                'monitors' => $monitors,
                'activities' => $activities,
            ]);
        }

        public function getControlCurrentLocation($control)
        {
            $currentLocation = "unknown";

            $latestTransfer = Controls_Sites::where('control_id', '=', $control->id)->orderBy('id', 'desc')->get();
            if(count($latestTransfer) > 0)
            {
                $t = $latestTransfer{0};
                if($t->to_site_id == 0)
                {
                    $currentLocation = "Storage";    
                }
                else
                {
                    $currentLocation = "Site: " . $t->To_Site->name;

                    if($t->to_map_id > 0)
                    {
                        $currentLocation .= " :: Map: " . $t->To_Map->name;
                    }

                    if($t->to_zone_id > 0)
                    {
                        $currentLocation .= " :: Zone: " . $t->To_Zone->name;
                    }
                }
            }
            else
            {
                $currentLocation = "Storage";
            }

            return $currentLocation;
        }

        public function addMonitor($monitor, $control)
        {
            $monitor = Thingsboards_Device::find($monitor);
            
            if($monitor->control_id != $control)
            {
                $monitor->control_id = $control;
                $monitor->save();
            }

            $devices = Thingsboards_Device::where('control_id', '=', $control)->get();
            
            return json_encode($devices);
        }

        public function removeMonitor($monitor)
        {
            $monitor = Thingsboards_Device::find($monitor);
            $control = $monitor->control_id;

            $monitor->control_id = NULL;
            $monitor->save();

           return $this->editControl($control);
        }
        
        public function archiveControl($control)
        {
            $standardDisplay = $this->standardDisplay();

            $control = Control::find($control);
            $control->archived = 1;
            $control->save();

            $this->resetControlCoords($control->id);

            $alert = "Control $control->serial archived";

            $this->insertLog($standardDisplay['profile']->id, "controls", $control->id, "Archived", "$control->serial archived.", "WARNING");

            return $this->editControlType($control->controls_type_id);
        }

        public function controlGroups($group)
        {
            /*
                Standard page setup
            */
            $alert = NULL;

            return $this->displayContolGroups($alert, $group);
        }
        
        public function displayContolGroups($alert, $group)
        {
            $standardDisplay = Controller::standardDisplay();
            $breadcrumbs = Controller::createBreadcrumbs("Control Types", "Groups");

            if(!in_array("controls:setup", $standardDisplay['permissions']))
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $groups = Controls_Type_Group::where('archived', '=', '0')->orderBy('name', 'asc')->get();

            $group = Controls_Type_Group::find($group);
            
            return view('setup.controlGroups', [
                'breadcrumbs' => $breadcrumbs,
                'alert' => $alert,
                'groups' => $groups,
                'group' => $group,
                'standardDisplay' => $standardDisplay,
            ]);
        }

        public function saveControlGroup(Request $request)
        {
            if($request->group == 0)
            {
                $new = new Controls_Type_Group();
                $new->archived = 0;

                $alert = "Control Type Group $request->name created.";
            }
            else
            {
                $new = Controls_Type_Group::find($request->group);
                $alert = "Control Type Group $request->name updated.";
            }

            $new->name = $request->name;
            $new->save();

            return $this->displayContolGroups($alert, 0);
        }

        public function archiveControlGroup($group)
        {
            $group = Controls_Type_Group::find($group);
            $group->archived = 1;
            $group->save();

            $alert = "Archived $group->name";

            return $this->displayContolGroups($alert, 0);
        }

        public function deleteField($field)
        {
            $standardDisplay = Controller::standardDisplay();
            if(!in_array("controls:edit", $standardDisplay['permissions']))
            {
                if($standardDisplay['profile']->super_user != 1)
                {
                    $index = new HomeController();
                    $alert = "You do not have privileges to do that.";

                    return $index->displayDashboard($alert);
                }
            }

            $field = Controls_Type_Field::find($field);
            $controlsType = $field->controls_type_id;
            $data = Controls_Field::where('control_field_id', '=', $field->id)->delete();
            $field->delete();

            return $this->editControlType($controlsType);
        }

    // End control types    


    //News

        public function news()
        {
            /*
                Standard page setup
            */
            $alert = NULL;

            return $this->displayNews($alert);
        }

        public function displayNews($alert)
        {
            $standardDisplay = $this->checkFunctionPermission("news:setup");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $breadcrumbs = Controller::createBreadcrumbs("News", NULL);

            $news = News::where('archived', "=", '0')->orderBy('name', 'asc')->get();
            
            return view('setup.news', [
                'breadcrumbs' => $breadcrumbs,
                'alert' => $alert,
                'news' => $news,
                'standardDisplay' => $standardDisplay,
            ]);
        }

        public function saveNews(Request $request)
        {
            if($request->news == 0)
            {
                $news = new News();
                $news->archived = 0;
                $news->status = "standard";

                $alert = "News $request->name created.";
            }
            else
            {
                $news = News::find($request->news);
                $alert = "News $request->name updated.";
            }

            $news->name = $request->name;
            $news->body = $request->body;
            
            if(!empty($request->file('image')))
            {
                $image = $request->file('image')->store('images/blog-images', 'public');
                $news->image = $image;
            }

            $news->save();

            return $this->displayNews($alert);
        }

        public function archiveNews($news)
        {
            $news = News::find($news);
            $news->archived = 1;
            $news->save();

            $alert = "Archived $news->name";

            return $this->displayNews($alert);
        }

        public function makeNewsHeadline($news)
        {
            $nws = News::get();
            foreach($nws as $n)
            {
                $n->status = "standard";
                $n->save();
            }

            $news = News::find($news);
            $news->status = "headline";
            $news->save();

            $alert = "Promoted $news->name to headline";

            return $this->displayNews($alert);
        }


    // End News


    //Security Groups

        public function securityGroups()
        {
            /*
                Standard page setup
            */
            $alert = NULL;

            return $this->displaySecurityGroups($alert);
        }

        public function displaySecurityGroups($alert)
        {
            $standardDisplay = $this->checkFunctionPermission("secGroups:setup");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }
            
            $breadcrumbs = Controller::createBreadcrumbs("Security group", NULL);

            $groups = Security_Groups::where('archived', '=', 0)->orderBy('name', 'asc')->get();
            
            return view('setup.securityGroups', [
                'breadcrumbs' => $breadcrumbs,
                'alert' => $alert,
                'standardDisplay' => $standardDisplay,
                'groups' => $groups,
            ]);
        }

        public function editSecurityGroup($type, $group)
        {
            $standardDisplay = Controller::standardDisplay();

            if(!in_array("secGroups:setup", $standardDisplay['permissions']))
            {
                if($standardDisplay['profile']->super_user == 0)
                {
                    $index = new HomeController();
                    $alert = "You do not have privileges to do that.";

                    return $index->displayDashboard($alert);
                }
            }
            
            if($group == 0)
            {
                $group = new Security_Groups();
                $group->id = 0;
                $group->name = "New";
            }
            else
            {
                $group = Security_Groups::find($group);
            }
            
            $breadcrumbs = Controller::createBreadcrumbs("Security group", $group->name);  
            
            $sgSettings = $this->getSpecificPermissions($group->id);
            $logs = $this->retrieveLogs("security groups", $group->id);
            
            return view('setup.editSecurityGroup', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'sgSettings' => $sgSettings,
                'logs' => $logs,
                'type' => $type,
                'group' => $group,
            ]);
        }

        public function saveSecurityGroup(Request $request, $type, $group)
        {
            $standardDisplay = $this->standardDisplay();

            if(!in_array("secGroups:setup", $standardDisplay['permissions']))
            {
                if($standardDisplay['profile']->super_user == 0)
                {
                    $index = new HomeController();
                    $alert = "You do not have privileges to do that.";

                    return $index->displayDashboard($alert);
                }
            }
            
            if($group == 0)
            {
                $group = new Security_Groups();
                $group->archived = 0;
                $group->type = $type;
                $action = "created";
            }
            else
            {
                $sgDetails = security_group_details::where('security_group_id', '=', $group)->delete();
                $group = Security_Groups::find($group);
                $action = "Edited";
            }

            $group->name = $request->security_group_name;
            
            $billable = 0;
            if($request->billable == "on")
            {
                $billable = 1;
            }
            $group->billable = $billable;
            $group->save();

            $this->insertLog($standardDisplay['profile']->id, "security groups", $group->id, $action, "Security group " . $group->name . " " . $action, "INFO");

            foreach($request->perms as $p)
            {
                foreach($p as $pKey => $pValue)
                {
                    $key = $pKey;
                    foreach($pValue as $vKey => $v)
                    {
                        //save new security group
                        $perm = new security_group_details();
                        
                        $perm->security_group_id = $group->id;
                        $perm->module = $key;
                        $perm->action = $vKey;
                        
                        $perm->save();
                    }
                }
                
            }

            $alert = "Security group " . $group->name . " saved";
            
            return $this->displaySecurityGroups($alert);


        }

        public function archiveSecurityGroup($group)
        {
            $standardDisplay = $this->standardDisplay();
            if(!in_array("secGroups:setup", $standardDisplay['permissions']))
            {
                if($standardDisplay['profile']->super_user == 0)
                {
                    $index = new HomeController();
                    $alert = "You do not have privileges to do that.";

                    return $index->displayDashboard($alert);
                }
            }

            $securityGroup = Security_Groups::find($group);
            $securityGroup->archived = 1;
            $securityGroup->save();

            $alert = "Security group " . $securityGroup->name . " deleted";

            $this->insertLog($standardDisplay['profile']->id, "security groups", $securityGroup->id, "Deleted", "Security group " . $securityGroup->name . " " . " deleted", "WARNING");

            return $this->displaySecurityGroups($alert);
        }
    
    //End security groups


    //Integrations Setup
        public function integrations()
        {
            /*
                Standard page setup
            */
            $alert = NULL;

            return $this->displayIntegrations($alert);
        }

        public function displayIntegrations($alert)
        {
            $standardDisplay = $this->checkFunctionPermission("iot:setup");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $breadcrumbs = Controller::createBreadcrumbs("Integrations", NULL);

            $integrations = Api::where('archived', "=", '0')->orderBy('application_name', 'asc')->get();
            
            $monitors = Thingsboards_Device::where('archived', '=', 0)->orderBy('type', 'asc')->get();

            $costCenters = Cost_Center::orderBy('company_id', 'asc')->orderBy('cost_center_name', 'asc')->get();

            $simPROSettings = array();
            $simPROInt = Api::find(2);
            if($simPROInt->settings != NULL)
            {
                $simPROSettings = json_decode($simPROInt->settings);
            }
            
            return view('setup.integrations', [
                'breadcrumbs' => $breadcrumbs,
                'alert' => $alert,
                'integrations' => $integrations,
                'monitors' => $monitors,
                'costCenters' => $costCenters,
                'standardDisplay' => $standardDisplay,
                'simPROSettings' => $simPROSettings,
            ]);
        }

        public function saveIntegrations(Request $request)
        {
            foreach($request->integrationID as $key => $value)
            {
                $integration = Api::find($value);
                $integration->base_url = $request->baseURL[$key];
                $integration->username = $request->username[$key];
                $integration->password = $request->password[$key];
                $integration->save();
            }

            /*
                Save the simPRO Settings against the API record
            */
            if($request->subscriptions)
            {
                $array = array();
                $array['subscriptions'] = $request->subscriptions;
                $array['marketplace'] = $request->marketplace;
                $array['external_lease'] = $request->external_lease;
                $array['monitoring_only'] = $request->monitoring_only;
                $array = json_encode($array);

                $simPRO = Api::find(2);
                $simPRO->settings = $array;
                $simPRO->save();
            }

            $alert = "Integration settings saved";


            return $this->displayIntegrations($alert);
        }

        public function setupDevice($device)
        {
            $standardDisplay = Controller::standardDisplay();
            $breadcrumbs = Controller::createBreadcrumbs("Integrations", "Setup device");

            if(!in_array("iot:setup", $standardDisplay['permissions']))
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $device = Thingsboards_Device::find($device);

            $thingsboard = new ThingsboardController();
            $thingsboard->retrieveDeviceReadingTypes($device);

            $readingTypes = Thingsboards_Device_Reading_Types::where('device_id', '=', $device->id)->orderBy('reading_type_id', 'asc')->get();
            
            return view('setup.configureDevice', [
                'standardDisplay' => $standardDisplay,
                'breadcrumbs' => $breadcrumbs,
                'readingTypes' => $readingTypes,
                'device' => $device,
            ]);
        }

        public function saveDevice(Request $request)
        {
            foreach($request->readingTypeId as $key => $value)
            {
                $readingType = Thingsboards_Device_Reading_Types::find($value);
                $readingType->calculation = $request->calculation[$key];
                $readingType->save();
            }

            $alert = "Reading type calculations have been updated.";
            return $this->displayIntegrations($alert);
        }

        public function archiveMonitor($monitor)
        {
            $monitor = Thingsboards_Device::find($monitor);

            $monitor->archived = 1;
            $monitor->save();

            $alert = $monitor->name . " has been archived successfully";

            return $this->displayIntegrations($alert);
        }

    //End Integrations Setup


    //Rules Setup
        public function rules()
        {
            $alert = NULL;

            return $this->displayRules($alert);
        }

        public function displayRules($alert)
        {
            $standardDisplay = $this->checkFunctionPermission("iot:setup");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $breadcrumbs = Controller::createBreadcrumbs("Rules", NULL);

            $rules = Reading_Rules::where('archived', '=', 0)->orderBy('reading_type_id', 'asc')->orderBy('order', 'asc')->get();
            
            return view('setup.rules', [
                'breadcrumbs' => $breadcrumbs,
                'alert' => $alert,
                'rules' => $rules,
                'standardDisplay' => $standardDisplay,
            ]);
        }

        public function editRule($rule)
        {
            $standardDisplay = Controller::standardDisplay();
            $breadcrumbs = Controller::createBreadcrumbs("Rules", "Edit rule");

            if(!in_array("iot:setup", $standardDisplay['permissions']))
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            if($rule > 0)
            {
                $rule = Reading_Rules::find($rule);
            }
            else 
            {      
                $rule = new Reading_Rules();
            }

            $readingTypes = Thingsboards_Readings_Type::orderBy('name', 'asc')->get();
            $assessments = Assessment::where('archived', '=', 0)->orderBy('name', 'asc')->get();
            
            return view('setup.editRule', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'rule' => $rule,
                'readingTypes' => $readingTypes,
                'assessments' => $assessments,
            ]);
        }

        public function saveRule(Request $request)
        {
            if($request->ruleID == 0)
            {
                $rule = new Reading_Rules();
                $rule->archived = 0;
            }
            else 
            {
                $rule = Reading_Rules::find($request->ruleID);
            }
            $rule->name = $request->name;
            $rule->order = $request->order;
            $rule->reading_type_id = $request->readingType;
            $rule->rule_type = $request->ruleType;
            $rule->outcome = $request->outcome;
            $rule->save();

            if($rule->rule_type == "response")
            {
                $rule->assessment_id = $request->assessmentID;
                $rule->question_id = $request->questionID;
                $rule->answer_id = $request->answerID;

                $rule->within_range_max = NULL;
                $rule->within_range_min = NULL;
                $rule->above_max = NULL;
                $rule->below_min = NULL;

            }
            if($rule->rule_type == "range")
            {
                $rule->within_range_max = $request->maximum;
                $rule->within_range_min = $request->minimum;

                $rule->assessment_id = NULL;
                $rule->question_id = NULL;
                $rule->answer_id = NULL;
                $rule->above_max = NULL;
                $rule->below_min = NULL;
            }
            if($rule->rule_type == "above")
            {
                $rule->above_max = $request->aboveMax;
                
                $rule->assessment_id = NULL;
                $rule->question_id = NULL;
                $rule->answer_id = NULL;
                $rule->within_range_max = NULL;
                $rule->within_range_min = NULL;
                $rule->below_min = NULL;
            }
            if($rule->rule_type == "below")
            {
                $rule->below_min = $request->belowMin;
                
                $rule->assessment_id = NULl;
                $rule->question_id = NULL;
                $rule->answer_id = NULL;
                $rule->within_range_max = NULL;
                $rule->within_range_min = NULL;
                $rule->above_max = NULL;
            }

            if($rule->outcome == "formula")
            {
                $rule->formula = $request->formula;
            }
            else 
            {
                $rule->formula = NULL;
            }

            $rule->save();
            $this->orderRules($rule);

            $alert = "Rule details saved.";

            return $this->displayRules($alert);
        }

        public function archiveRule($rule)
        {
            $rule = Reading_Rules::find($rule);
            $rule->archived = 1;
            $rule->save();

            $alert = "Archived rule $rule->name";

            return $this->displayRules($alert);
        }

        public function orderRules($rule)
        {
            //this function needs to reorder all the rules taking the input rule as the primary and move everything else around it
            //$rule is the full $rule object

            $allRules = Reading_Rules::where('reading_type_id', '=', $rule->reading_type_id)
                                        ->where('order', '>=', $rule->order)
                                        ->where('id', '!=', $rule->id)
                                        ->orderBy('order', 'asc')
                                        ->get();
            foreach($allRules as $ar)
            {
                $checkAnother = Reading_Rules::where('reading_type_id', '=', $rule->reading_type_id)
                                                ->where('order', '=', $ar->order)
                                                ->where('id', '!=', $ar->id)
                                                ->count();
                
                if($checkAnother > 0)
                {
                    $ar->order = $ar->order + 1;
                    $ar->save();
                }
            }

            return 1;
        }
    //End Rules Setup


    //Exposure

        public function exposures($exposure)
        {
            /*
                Standard page setup
            */
            $alert = NULL;

            return $this->displayExposures($alert, $exposure);
        }
        
        public function displayExposures($alert, $exposure)
        {
            $standardDisplay = $this->checkFunctionPermission("exposures:setup");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }
            
            $breadcrumbs = Controller::createBreadcrumbs("Exposures", NULL);

            $exposures = Exposure::where('archived', '=', '0')->orderBy('name', 'asc')->get();
            $readingTypes = Thingsboards_Readings_Type::orderBy('name', 'asc')->get();
            $exposure = Exposure::find($exposure);
            
            return view('setup.exposures', [
                'breadcrumbs' => $breadcrumbs,
                'alert' => $alert,
                'exposures' => $exposures,
                'exposure' => $exposure,
                'standardDisplay' => $standardDisplay,
                'readingTypes' => $readingTypes,
            ]);
        }

        public function saveExposure(Request $request)
        {
            if($request->exposure == 0)
            {
                $exposure = new Exposure();
                $exposure->archived = 0;

                $alert = "Exposure $request->name created.";
            }
            else
            {
                $exposure = Exposure::find($request->exposure);
                $alert = "Exposure $request->name updated.";
            }

            $exposure->name = $request->name;
            $exposure->reading_type_id = $request->readingType;
            $exposure->time_period = $request->timePeriod;
            $exposure->level = $request->level;
            $exposure->save();

            return $this->displayExposures($alert, 0);
        }

        public function archiveExposure($exposure)
        {
            $exposure = Exposure::find($exposure);
            $exposure->archived = 1;
            $exposure->save();

            $alert = "Archived $exposure->name";

            return $this->displayExposures($alert, 0);
        }

    //End Exposures

}