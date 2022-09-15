<?php

namespace App\Http\Controllers;

use App\Models\Actions_Assessment;
use App\Models\Actions_Assessments_Question;
use App\Models\Actions_Control;
use App\Models\Actions_Time_Entry;
use App\Models\Assessments_Questions_Answer;
use App\Models\Assessment;
use App\Models\Assessments_Activities;
use App\Models\Assessments_Question;
use App\Models\Assessments_Questions_Answers_Option;
use App\Models\Assessments_Site;
use App\Models\Activities_Trades;
use App\Models\Control;
use App\Models\Controls_Field;
use App\Models\Controls_Sites;
use App\Models\Controls_Type_Field;
use App\Models\Controls_Type;
use App\Models\File;
use App\Models\Hazards_Activities;
use App\Models\History;
use App\Models\Histories_Assessments;
use App\Models\Histories_Check;
use App\Models\Log;
use App\Models\Membership;
use App\Models\Profile;
use App\Models\Profiles_Trade;
use App\Models\Site;
use App\Models\Sites_Maps_Zone; 
use App\Models\Sites_Maps_Zones_Hazard;
use App\Models\Sites_Logon;
use App\Models\Task; 
use App\Models\Thingsboards_Device_Reading;
use App\Models\Training; 
use App\Models\Trainings_Hygenist_Member;
use App\Models\Trainings_Hygenist;
use App\Models\Trainings_Profile;


use App\Http\Controllers\PeopleController;
use App\Http\Controllers\ThingsboardController;

use App\Jobs\historyChecker;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ActivityController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    
    



    //Tasks

        public function tasks()
        {
            /*
                Standard page setup
            */
            $alert = NULL;

            return $this->displayTasks($alert);
        }

        public function getMyTasks($profileID)
        {
            $tasks = Task::where('archived', '=', 0)
                            ->whereNull('completed_date')
                            ->where('assigned_id', '=', $profileID)
                            ->limit(10)
                            ->get();
            
            return $tasks;
        }

        public function displayTasks($alert)
        {
            $standardDisplay = $this->checkFunctionPermission("tasks:view");

            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }


            $breadcrumbs = Controller::createBreadcrumbs("Tasks", NULL);

            //get all the tasks that this user is related to.
            $tasksArray = array();
            $checkArray = array();
            $t = 0;

            //check to see if in the membership you have, whether you can view all tasks or just see your own.
            $memberships = Membership::where('user_id', '=', $standardDisplay['profile']->id)->where('membership_status', '=', 'active')->get();
            foreach($memberships as $mem)
            {
                
                //find out if the security group for this membership has access to view-all
                $perms = $this->getSpecificPermissions($mem->security_group);
                if(in_array("tasks:view-all", $perms))
                {
                    //get all members of this organisation
                    $members = Membership::where('organisation_id', '=', $mem->organisation_id)->where('membership_status', '=', 'active')->get();
                    foreach($members as $member)
                    {
                        //get all the tasks for this member
                        $tasks = Task::where('assigned_id', '=', $member->user_id)->where('archived', '=', 0)->get();
                        foreach($tasks as $task)
                        {
                            if(!in_array($task->id, $checkArray))
                            {
                                $tasksArray[$t] = $task;
                                $checkArray[$t] = $task->id;
                                $t++;
                            }
                        }
                    }
                    //get all the tasks for this member
                    $tasks = Task::where('assigned_id', '=', $member->organisation_id)->where('archived', '=', 0)->get();
                    foreach($tasks as $task)
                    {
                        if(!in_array($task->id, $checkArray))
                        {
                            $tasksArray[$t] = $task;
                            $checkArray[$t] = $task->id;
                            $t++;
                        }
                    }

                }
            }
            if(count($tasksArray) == 0)
            {
                //only get your own tasks
                $tasks = Task::where('assigned_id', '=', $standardDisplay['profile']->id)->where('archived', '=', 0)->get();
                foreach($tasks as $task)
                {
                    if(!in_array($task->id, $checkArray))
                    {
                        $tasksArray[$t] = $task;
                        $checkArray[$t] = $task->id;
                        $t++;
                    }
                }
            }

            //get all your sites
            $peopleController = new PeopleController();
            $sites = $peopleController->getUsersSites($standardDisplay['profile'], "all");
            foreach($sites as $site)
            {
                $tasks = Task::where('assigned_id', '=', '0')->where('archived', '=', 0)->where('site_id', '=', $site->id)->get();
                foreach($tasks as $task)
                {
                    if(!in_array($task->id, $checkArray))
                    {
                        $tasksArray[$t] = $task;
                        $checkArray[$t] = $task->id;
                        $t++;
                    }
                }
            }
            
            return view('activities.tasks', [
                'breadcrumbs' => $breadcrumbs,
                'alert' => $alert,
                'standardDisplay' => $standardDisplay,
                'tasks' => $tasksArray,
            ]);
        }

        public function editTask($task)
        {
            if($task > 0)
            {
                $task = Task::find($task);
            }
            else
            {
                $task = new Task();
                $task->id = 0;
            }

            $standardDisplay = $this->checkFunctionPermission("tasks:edit");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }


            $breadcrumbs = Controller::createBreadcrumbs("Tasks", $task->subject);
            $peopleController = new PeopleController();

            //find all the people we have access to.
            $peopleArray = array();
            $checkArray = array();
            $p = 0;

            //check to see if in the membership you have, whether you can view all tasks or just see your own.
            $memberships = Membership::where('user_id', '=', $standardDisplay['profile']->id)->where('membership_status', '=', 'active')->get();
            foreach($memberships as $mem)
            {
                //get all members of this organisation
                $members = Membership::where('organisation_id', '=', $mem->organisation_id)->where('membership_status', '=', 'active')->get();
                foreach($members as $member)
                {
                    //get all the tasks for this member
                    if(!in_array($member->user_id, $checkArray))
                    {
                        $peopleArray[$p] = $member->Profile;
                        $checkArray[$p] = $member->user_id;
                        $p++;
                    }
                }
            }
            if(count($peopleArray) == 0)
            {
                //only get your own tasks
                $peopleArray[$p] = $standardDisplay['profile'];
            }
            
            $sites = $peopleController->getUsersSites($standardDisplay['profile'], "all");
            $module = 'tasks';
            $moduleID = $task->id;
            $files = File::where('module', '=', $module)
                                        ->where('module_id', '=', $moduleID)
                                        ->get();
            $logs = $this->retrieveLogs($module, $moduleID);
            
            return view('activities.editTask', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'files' => $files,
                'logs' => $logs,
                'task' => $task,
                'sites' => $sites,
                'people' => $peopleArray,
            ]);
        }

        public function completeTask($task)
        {
            $standardDisplay = $this->checkFunctionPermission("tasks:edit");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }
            
            $date = date('Y-m-d');
            $task = Task::find($task);
            $task->completed_date = $date;
            $task->status == "completed";
            $task->progress = "100";
            $task->save();

            $action = "Completed";

            $this->insertLog($standardDisplay['profile']->id, "tasks", $task->id, $action, "Task " . $task->subject . " " . $action, "INFO");

            $alert = "Task " . $task->subject . " completed";
            
            return $this->displayTasks($alert);
        }

        public function deleteTask($task)
        {
            $standardDisplay = $this->checkFunctionPermission("tasks:delete");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }
            
            if(!in_array("tasks:delete", $standardDisplay['permissions']))
            {
                if($standardDisplay['profile']->super_user == 0)
                {
                    $index = new HomeController();
                    $alert = "You do not have privileges to do that.";

                    return $index->displayDashboard($alert);
                }
            }

            $task = Task::find($task);
            $task->archived = 1;
            $task->save();

            $action = "Deleted";

            $this->insertLog($standardDisplay['profile']->id, "tasks", $task->id, $action, "Task " . $task->subject . " " . $action, "WARNING");

            $alert = "Task " . $task->subject . " deleted";
            
            return $this->displayTasks($alert);


        }

        public function saveTask(Request $request, $task)
        {
            $standardDisplay = $this->checkFunctionPermission("tasks:edit");   
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            if($task > 0)
            {
                $task = Task::find($task);
                $action = "Edited";
            }
            else
            {
                $task = new Task();
                $task->archived = 0;
                $action = "Created";
            }

            $task->subject = $request->subject;
            $task->description = $request->description;
            $task->notes = $request->notes;
            $task->assigned_id = $request->assignedTo;
            $task->status = $request->status;
            $task->priority = $request->priority;
            $task->progress = $request->progress;
            $task->site_id = $request->site;
            $task->start_date = $request->start_date;
            $task->due_date = $request->due_date;
            $task->completed_date = $request->completed_date;

            $task->save();

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
                    $f->module_id = $task->id;
                    $f->module = "tasks";
                    
                    $f->save();
                }
            }


            $this->insertLog($standardDisplay['profile']->id, "tasks", $task->id, $action, "Task " . $task->subject . " " . $action, "INFO");

            $alert = "Task " . $task->subject . " saved";

            if($task->status == "complete" && empty($task->completed_date))
            {
                return $this->completeTask($task->id);
            }
            else
            {
                return $this->displayTasks($alert);
            }
            
            
        }

        public function addTask($profile, $subject, $description, $notes, $assignedTo, $site)
        {
            /*
                Function will create a new task
                $profile is the $standardDisplay['profile']->id.  
                The $assignedTo and $site are also just ID's - $site can be NULL
            */

            $task = new Task();
            $task->archived = 0;
            $action = "Created";

            $task->subject = $subject;
            $task->description = $description;
            $task->notes = $notes;
            $task->assigned_id = $assignedTo;
            $task->status = "pending";
            $task->priority = "medium";
            $task->progress = "0";
            $task->site_id = $site;
            $task->start_date = date('Y-m-d');

            $task->save();

            $this->insertLog($profile, "tasks", $task->id, $action, "Task " . $task->subject . " " . $action, "INFO");

            return $task->id;

        }

        public function createTask($profile, $subject, $description, $notes, $assignedID, $priority, $site)
        {
        
            $task = new Task();
            $task->archived = 0;
            $action = "Created";
            
            $today = date('Y-m-d');

            $task->subject = $subject;
            $task->description = $description;
            $task->notes = $notes;
            $task->assigned_id = $assignedID;
            $task->status = "pending";
            $task->priority = $priority;
            $task->progress = 0;
            $task->site_id = $site;
            $task->start_date = $today;

            $task->save();

            $this->insertLog($profile->id, "tasks", $task->id, $action, "Task " . $task->subject . " " . $action, "INFO");

            return $task->id;
        }

    //End Tasks


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
            $standardDisplay = $this->checkFunctionPermission("training:view");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $breadcrumbs = Controller::createBreadcrumbs("Services marketplace", NULL);

            $hygenists = array();
            $i = 0;
            
            //find out if any organisation the user is a member of, is a member of a hygenist
            $memberships = Membership::where('user_id', '=', $standardDisplay['profile']->id)
                                        ->where('membership_status', '=', 'active')
                                        ->get();
            foreach($memberships as $mem)
            {
                $orgMemberships = Membership::where('user_id', '=', $mem->organisation_id)
                                                ->where('organisation_type', '=', 'hygenist')
                                                ->where('membership_status', '=', 'active')
                                                ->get();
                foreach($orgMemberships as $orgMem)
                {
                    $hygenists[$i]['hygenist'] = $orgMem->organisation_id;
                    $hygenists[$i]['memberOf'] = $orgMem->user_id;
                    $i++;
                }
            }

            $allTrainingArray = array();
            $x = 0;

            $allTraining = Training::where('archived', '=', 0)->orderBy('name', 'asc')->get();
            foreach($allTraining as $training)
            {
                $allTrainingArray[$x]['training'] = $training;
                $tHygenists = array();
                $z = 0;
                foreach($training->Training_Hygenist as $th)
                {
                    $tHygenists[$z] = $th;
                    $z++;
                }

                $allTrainingArray[$x]['hygenists'] = $tHygenists;

                $memberPricing = array();
                $mp = 0;

                foreach($hygenists as $hyg)
                {
                    $pricing = Trainings_Hygenist_Member::where('training_id', '=', $training->id)
                                                            ->where('hygenist_id', '=', $hyg['hygenist'])
                                                            ->where('member_id', '=', $hyg['memberOf'])
                                                            ->first();
                    $memberPricing[$mp] = $pricing;
                    $mp++;
                }
                
                $allTrainingArray[$x]['memberPricing'] = $memberPricing;
                $x++;
            }

            $subscribedTraining = Trainings_Profile::where('profile_id', '=', $standardDisplay['profile']->id)->get();
            
            return view('activities.training', [
                'breadcrumbs' => $breadcrumbs,
                'alert' => $alert,
                'standardDisplay' => $standardDisplay,
                'training' => $allTrainingArray,
                'subscribedTraining' => $subscribedTraining,
            ]);
        }

        public function buyTraining($training)
        {
            $standardDisplay = $this->checkFunctionPermission("training:order");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $training = Training::find($training);
            $breadcrumbs = Controller::createBreadcrumbs("Services marketplace", $training->name);
            $isHygenist = 0;
            $isActive = 0;
            $myMembers = array();
            $myOrders = array();
            $mm = 0;
            $mo = 0;

            //find out if any organisation the user is a member of, is a member of a hygenist
            $hArray = array();
            $i = 0;
            $memberships = Membership::where('user_id', '=', $standardDisplay['profile']->id)
                                        ->where('membership_status', '=', 'active')
                                        ->get();
            foreach($memberships as $mem)
            {
                if($mem->organisation_type == "hygenist")
                {
                    $isHygenist = $mem->organisation_id;
                    $checkActive = Trainings_Hygenist::where('training_id', '=', $training->id)
                                            ->where('profile_id', '=', $isHygenist)
                                            ->first();
                    if(!is_object($checkActive))
                    {
                        $checkActive = new Trainings_Hygenist();

                        $checkActive->profile_id = $isHygenist;
                        $checkActive->training_id = $training->id;
                        $checkActive->price = $training->price;
                        $checkActive->link = $training->link;
                        $checkActive->active_provider = 0;

                        $checkActive->save();
                    }
                    $isActive = $checkActive->active_provider;

                    $hm = Membership::where('organisation_id', '=', $mem->organisation_id)
                                        ->where('membership_status', '=', 'active')
                                        ->where('user_id', '!=', $standardDisplay['profile']->id)
                                        ->get();
                    foreach($hm as $h)
                    {
                        //make sure there is a training hygenist member entry
                        $check = Trainings_Hygenist_Member::where('training_id', '=', $training->id)
                                                            ->where('hygenist_id', '=', $mem->organisation_id)
                                                            ->where('member_id', '=', $h->user_id)
                                                            ->first();
                        if(!is_object($check))
                        {
                            //get the default price for this hygenist for the course
                            $default = Trainings_Hygenist::where('profile_id', '=', $mem->organisation_id)
                                                            ->where('training_id', '=', $training->id)
                                                            ->first();
                            if(is_object($default))
                            {
                                $price = $default->price;
                            }
                            else
                            {
                                $price = $training->price;
                            }

                            $check = new Trainings_Hygenist_Member();
                            $check->training_id = $training->id;
                            $check->hygenist_id = $mem->organisation_id;
                            $check->member_id = $h->user_id;
                            $check->price = $price;

                            $check->save();                        
                        }
                        $myMembers[$mm] = $check;
                        $mm++;

                        //see if this member has ordered this course off the active hygenist
                        $orders = Trainings_Profile::where('training_id', '=', $training->id)
                                                    ->where('hygenist_id', '=', $mem->organisation_id)
                                                    ->orderBy('id', 'desc')
                                                    ->get();
                        foreach($orders as $order)
                        {
                            $myOrders[$mo]['order'] = $order;
                            $myOrders[$mo]['name'] = $this->checkUserVisibility($order->profile_id);
                            $mo++;
                        }

                    }
                }

                $orgMemberships = Membership::where('user_id', '=', $mem->organisation_id)
                                                ->where('organisation_type', '=', 'hygenist')
                                                ->where('membership_status', '=', 'active')
                                                ->get();
                foreach($orgMemberships as $orgMem)
                {
                    $hArray[$i]['hygenist'] = $orgMem->organisation_id;
                    $hArray[$i]['memberOf'] = $orgMem->user_id;
                    $i++;
                }
            }
            
            
            $hygenistArray = array();
            $x = 0;
            $hygenists = Trainings_Hygenist::where('training_id', '=', $training->id)
                                            ->where('active_provider', '=', 1)
                                            ->get();
            foreach($hygenists as $hygenist)
            {
                if(!empty($hygenist->Profile))
                {
                    $hygenistArray[$x]['hygenist_id'] = $hygenist->profile_id;
                    $hygenistArray[$x]['name'] = $hygenist->Profile->name;
                    $check = 0;
                }

                if(count($hArray) > 0)
                {
                    foreach($hArray as $hyg)
                    {
                        if($hyg['hygenist'] == $hygenist->profile_id)
                        {

                            $pricing = Trainings_Hygenist_Member::where('training_id', '=', $training->id)
                                                                    ->where('hygenist_id', '=', $hyg['hygenist'])
                                                                    ->where('member_id', '=', $hyg['memberOf'])
                                                                    ->first();
                            $hygenistArray[$x]['price'] = $pricing->price;
                            $hygenistArray[$x]['special'] = 1;

                            $check = 1;
                        }   
                    }
                }
                if($check == 0)
                {            
                    $hygenistArray[$x]['price'] = $hygenist->price;
                    $hygenistArray[$x]['special'] = 0;
                }

                $x++;
            }

            
            return view('activities.buyTraining', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'training' => $training,
                'hygenists' => $hygenistArray,
                'isHygenist' => $isHygenist,
                'myMembers' => $myMembers,
                'myOrders' => $myOrders,
                'isActive' => $isActive,
            ]);
        }

        public function updateTrainingOrders(Request $request, $training)
        {
            $training = Training::find($training);
            $standardDisplay = Controller::standardDisplay();

            if(isset($request->orderID))
            {
                foreach($request->orderID as $key=>$value)
                {
                    //find the training profile
                    $tp = Trainings_Profile::find($request->orderID[$key]);
                    if(is_object($tp))
                    {
                        $paid = 0;
                        if(isset($request->paid[$key]))
                        {
                            if($request->paid[$key] == "on")
                            {
                                $paid = 1;
                            }
                        }
                        
                        $tp->instructions = $request->instructions[$key];
                        $tp->price = $request->price[$key];
                        $tp->status = $request->status[$key];
                        $tp->paid = $paid;
                        
                        $tp->save();                
                    }
                    else
                    {
                        echo "We weren't able to find a training profile for order ID of $request->order[$key].  Please send this error to the system administrator.";
                        exit;
                    }
                }
            }
            return $this->buyTraining($training->id);
        }

        public function joinTraining($profileTraining)
        {
            $standardDisplay = $this->checkFunctionPermission("training:order");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $training = Trainings_Profile::find($profileTraining);
            $breadcrumbs = Controller::createBreadcrumbs("Services marketplace", "Join " . $training->Training->name);

            return view('activities.joinTraining', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'training' => $training,
            ]);
        }

        public function confirmTrainingPurchase(Request $request, $training)
        {
            $standardDisplay = $this->checkFunctionPermission("training:order");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            //first check to make sure its not already there, if it is just edit it.
            $check = Trainings_Profile::where('profile_id', '=', $standardDisplay['profile']->id)
                                        ->where('training_id', '=', $training)
                                        ->first();
            if(is_object($check))
            {
                $course = $check;
                $alert = "Service updated";
            }
            else
            {
                $course = new Trainings_Profile();
                $course->training_id = $training;
                $course->profile_id = $standardDisplay['profile']->id;
                $course->paid = 0;

                $alert = "Service ordered";
            }
            

            $activeOrganisation = 0;
            $training = Training::find($training);
            $price = $training->price;
            
            $trainingHygenist = Trainings_Hygenist::where('profile_id', '=', $request->provider)
                                                    ->where('training_id', '=', $training->id)
                                                    ->first();
            if(!is_object($trainingHygenist))
            {
                $trainingHygenist = new Trainings_Hygenist();

                $trainingHygenist->profile_id = $isHygenist;
                $trainingHygenist->training_id = $training->id;
                if($training->price > 0)
                {
                    $trainingHygenist->price = $training->price;
                }
                else
                {
                    $trainingHygenist->price = 1;
                }
                $trainingHygenist->link = $training->link;
                $trainingHygenist->active_provider = 0;

                $trainingHygenist->save();
            }

            $price = $trainingHygenist->price;

            //lets find out which organisations this user is a part of
            $memberships = Membership::where('user_id', '=', $standardDisplay['profile']->id)
                        ->where('membership_status', '=', 'active')
                        ->get();
            foreach($memberships as $mem)
            {
                $activeOrganisation = $mem->organisation_id;
                $orgMemberships = Membership::where('user_id', '=', $mem->organisation_id)
                                    ->where('organisation_id', '=', $request->provider)
                                    ->where('membership_status', '=', 'active')
                                    ->first();
                if(is_object($orgMemberships))
                {
                    //get some specific details if they exist about this membership and an offer from the provider
                    $offer = Trainings_Hygenist_Member::where('training_id', '=', $training->id)
                                                ->where('hygenist_id', '=', $request->provider)
                                                ->where('member_id', '=', $orgMemberships->user_id)
                                                ->first();
                    if(is_object($offer))
                    {
                        $price = $offer->price;
                    }
                }
            }
            if($price > 0)
            {
                $price = $price;
            }
            else
            {
                $price = 1;
            }

            

            $course->hygenist_id = $request->provider;
            $course->active_organisation_id = $activeOrganisation;
            $course->price = $price;
            $course->training_hygenist_id = $trainingHygenist->id;
            $course->status = "pending";
            $course->order_no = $request->order;

            $course->save();

            /*now create the task for the new course*/
            $provider = Profile::find($course->hygenist_id);
            if($provider->primary_contact > 0)
            {
                $contact = $provider->primary_contact;
            }
            else 
            {
                $contact = $provider->id;
            }

            $this->addTask($standardDisplay['profile']->id, "New service signup", $course->Profile->name . " has just ordered a new service for " . $training->name, NULL, $contact, NULL);

            return $this->displayTraining($alert);
        }

        public function updateMemberPricing(Request $request, $training)
        {
            if(isset($request->memberID))
            {
                foreach($request->memberID as $key => $value)
                {
                    $memberPrice = Trainings_Hygenist_Member::find($value);
                    $memberPrice->price = $request->price[$key];
                    $memberPrice->save();
                }

                //work out which training that was for
                $trainingHygenist = Trainings_Hygenist::where('profile_id', '=', $memberPrice->hygenist_id)
                                                        ->where('training_id', '=', $memberPrice->training_id)
                                                        ->first();
                $active = 0;
                if(!empty($request->activeProvider))
                {
                    $active = 1;
                }
                
                $trainingHygenist->active_provider = $active;
                $trainingHygenist->save();
            }

            

            $alert = "Member pricing updated";

            return $this->displayTraining($alert);
        }

        public function cancelService($order)
        {
            $alert = "Unable to cancel order for service due to it already being in progress.  Please contact the service provider directly.";

            $order = Trainings_Profile::find($order);
            if($order->status == "pending")
            {
                $order->delete();
                $alert = "Cancelled order for service";
            }
            
            return $this->displayTraining($alert);
        }
    
    //End Training


    //Site History

        public function sites()
        {
            /*
                Standard page setup
            */
            $alert = NULL;
    
            return $this->displaySites($alert);
        }

        public function displaySites($alert)
        {
            /*
                Standard page setup
            */
            $standardDisplay = $this->checkFunctionPermission("history:view");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $breadcrumbs = Controller::createBreadcrumbs("Site History", NULL);  

            $peopleController = new PeopleController();

            $sites = $peopleController->getUsersSites($standardDisplay['profile'], "all");

            historyChecker::dispatch();
            
            return view('activities.sites', [
                'breadcrumbs' => $breadcrumbs,
                'alert' => $alert,
                'standardDisplay' => $standardDisplay,
                'sites' => $sites,
            ]);
        }
    
        public function displaySiteHistory($site)
        {
            $site = Site::find($site); 
            $standardDisplay = $this->checkFunctionPermission("history:view");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            // $breadcrumbs = Controller::createBreadcrumbs("Site History", $site->name);      
            // $breadcrumbs = Controller::createBreadcrumbs("Site History", $site['name']);        
            // $breadcrumbs = Controller::createBreadcrumbs("Site History", count($site['name']) ? count($site['name']) : 0);        
            // $breadcrumbs = Controller::createBreadcrumbs("Site History", count(array($site['name'])) ? count(array($site['name'])) : 0);        
            $breadcrumbs = Controller::createBreadcrumbs("Site History", isset($site['name']) ? count(array($site['name'])) : 0);        

            $visits = $this->getSiteVisits($site);
            $swms = $this->getSiteActivities($site, $standardDisplay['profile']);
            $people = $this->getSitePeople($site, $standardDisplay['profile']);
            $controls = $this->getSiteControls($site);

            return view('activities.siteHistory', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'swms' => $swms,
                'visits' => $visits,
                'people' => $people,
                'controls' => $controls,
                // 'site' => isset($site['id']) ? count($site['id']) : 0,
                // 'site' => count(array($site['id'])) ? count(array($site['id'])) : 0,
                'site' =>  isset($site['id']) ? count(array($site['id'])) : 0,
            ]);
        }

        public function getSiteVisits($site)
        {
            //$site is the full site object
            //find all the unqique days that assessments were on
            $return = array();
            $x = 0;

            // $days = Actions_Time_Entry::where('site_id', '=', $site->id)->distinct('date')->select('date')->get();
            // isset($site['name']) ? count($site['name']) : 0
            // $days = Actions_Time_Entry::where('site_id', '=', $site['id'])->distinct('date')->select('date')->get();
            // $days = Actions_Time_Entry::where('site_id', '=', isset($site['id']) ? count($site['id']) : 0)->distinct('date')->select('date')->get();
            // $days = Actions_Time_Entry::where('site_id', '=', count(array($site['id'])) ? count(array($site['id'])) : 0)->distinct('date')->select('date')->get();
            $days = Actions_Time_Entry::where('site_id', '=',  isset($site['id']) ? count(array($site['id'])) : 0)->distinct('date')->select('date')->get();
            foreach($days as $d)
            {
                $date = substr($d->date, 0, 10);
                $activities = Actions_Time_Entry::where('date', '=', $date)->count();
                $from = $date . " 00:00:00";
                $to = $date . " 23:59:59";

                $assessments = Actions_Assessment::whereBetween('created_at', [$from, $to])->count();

                $people = Actions_Time_Entry::where('date', '=', $date)->distinct('user_id')->select('user_id')->count();

                $return[$x]['id'] = $x;
                $return[$x]['date'] = $d->date->format('d-m-Y');
                $return[$x]['activities'] = $activities;
                $return[$x]['assessments'] = $assessments;
                $return[$x]['people'] = $people;

                $x++;
            }

            return $return;
        }

        public function getSitePeople($site, $profile)
        {
            //Site is the full site object, profile is the full profile object. Function returns all activities that have happened on the site.\
            $profiles = array();
            $pr = 0;
            // $histories = History::where('site_id', '=', $site->id)
            // isset($site['name']) ? count($site['name']) : 0
            // $histories = History::where('site_id', '=', $site['id'])
            // $histories = History::where('site_id', '=', isset($site['id']) ? count($site['id']) : 0)
            // $histories = History::where('site_id', '=', count(array($site['id'])) ? count(array($site['id'])) : 0)
            $histories = History::where('site_id', '=',  isset($site['id']) ? count(array($site['id'])) : 0)
                                    ->where('archived', '=', 0)
                                    ->select('profiles_id', 'id')
                                    ->distinct('profiles_id')
                                    ->get();
            $peopleArray = array();
            $p = 0;

            foreach($histories as $h)
            {
                $timeEntry = Actions_Time_Entry::where('history_id', '=', $h->id)->first();
                if(is_object($timeEntry))
                {
                    if(is_object($timeEntry->Organisation))
                    {
                        $member = $timeEntry->Organisation->name;
                    }
                    else
                    {
                        $member = "Organisation removed";
                    }
                }
                else
                {
                    $membership = Membership::where('user_id', '=', $h->profiles_id)->where('membership_status', '=', 'active')->first();
                    if(is_object($membership))
                    {
                        if(is_object($membership->Organisation))
                        {
                            $member = $membership->Organisation->name;
                        }
                        else
                        {
                            $member = "Organisation removed";
                        }
                    }
                    else 
                    {
                        $member = "unknown";
                    }
                }

                if(!in_array($h->profiles_id, $profiles))
                {
                    $peopleArray[$p]['id'] = $h->profiles_id;
                    $peopleArray[$p]['name'] =  $this->checkUserVisibility($h->profiles_id);
                    $peopleArray[$p]['rName'] = $h->Profile->name;
                    $peopleArray[$p]['memberOf'] = $member;
                    $peopleArray[$p]['email'] = $h->Profile->email;
                    $peopleArray[$p]['mobile'] = $h->Profile->mobile;

                    $p++;

                    $profiles[$pr] = $h->profiles_id;
                    $pr++;
                }
            }

            return $peopleArray;
        }

        public function getSiteActivities($site, $profile)
        {
            //Site is the full site object, profile is the full profile object. Function returns all activities that have happened on the site.

            // $histories = History::where('site_id', '=', $site->id)->where('archived', '=', 0)->get();
            
            // $histories = History::where('site_id', '=', $site['id'])->where('archived', '=', 0)->get();
            // $histories = History::where('site_id', '=', isset($site['id']) ? count($site['id']) : 0)->where('archived', '=', 0)->get();
            // $histories = History::where('site_id', '=', count(array($site['id'])) ? count(array($site['id'])) : 0)->where('archived', '=', 0)->get();
            $histories = History::where('site_id', '=', isset($site['id']) ? count(array($site['id'])) : 0)->where('archived', '=', 0)->get();
            $historyArray = array();
            $h = 0;

            foreach($histories as $history)
            {
                if(!empty($history->Activity->name))
                {
                    $historyArray[$h]['id'] = $history->id;
                    $historyArray[$h]['date'] = $history->created_at->format('d-m-Y');
                    $historyArray[$h]['activity'] = $history->Activity->name;
                    if(is_object($history->Zone))
                    {
                        $historyArray[$h]['zone'] = $history->Zone->name;
                        $historyArray[$h]['map'] = $history->Zone->Sites_Map->name;
                    }
                    else 
                    {
                        $historyArray[$h]['zone'] = "-";
                        $historyArray[$h]['map'] = "-";
                    }
                    $historyArray[$h]['person'] = $this->checkUserVisibility($history->profiles_id);
                    $historyArray[$h]['assessments'] = count($history->Assessment);
                    $historyArray[$h]['time'] = round($this->calcHistoryTime($history->id, "hours"), 2);

                    $h++;
                }
            }

            return $historyArray;
        }

        public function getSiteControls($site)
        {
            //Site is the full site object
            $controlsArray = array();
            $c = 0;

            // $allControls = Controls_Sites::where('to_site_id', '=', $site->id)
            // $allControls = Controls_Sites::where('to_site_id', '=', isset($site['id']) ? count($site['id']) : 0)
            // $allControls = Controls_Sites::where('to_site_id', '=', count(array($site['id'])) ? count(array($site['id'])) : 0)
            $allControls = Controls_Sites::where('to_site_id', '=', isset($site['id']) ? count(array($site['id'])) : 0)
                                            ->select('control_id')
                                            ->distinct('control_id')
                                            ->get();
            foreach($allControls as $control)
            {
                $cntrl = Control::find($control->control_id);
                // $first = Controls_Sites::where('to_site_id', '=', $site->id)
                $first = Controls_Sites::where('to_site_id', '=',isset($site['id']) ? count($site['id']) : 0)
                                            ->where('control_id', '=', $cntrl->id)
                                            ->orderBy('id', 'asc')
                                            ->first();
                
                $last = Controls_Sites::where('from_site_id', '=', isset($site['id']) ? count($site['id']) : 0)
                                            ->where('control_id', '=', $cntrl->id)
                                            ->orderBy('id', 'desc')
                                            ->first();

                if(is_object($control))
                {
                    $controlsArray[$c]['id'] = $control->control_id;
                    $controlsArray[$c]['type'] = $control->Control->Controls_Type->name;
                    $controlsArray[$c]['serial'] = $control->Control->serial;
                }
                else
                {
                    $controlsArray[$c]['id'] = "-";
                    $controlsArray[$c]['type'] = "-";
                    $controlsArray[$c]['serial'] = "-";
                }

                if(is_object($first))
                {
                    $controlsArray[$c]['arrived'] = $first->created_at->format('d-m-Y H:i');
                }
                else
                {
                    $controlsArray[$c]['arrived'] = "-";
                }

                if(is_object($last))
                {
                    $controlsArray[$c]['removed'] = $last->created_at->format('d-m-Y H:i');
                }
                else
                {
                    $controlsArray[$c]['removed'] = "-";
                }

                $c++;
            }

            return $controlsArray;
        }

        public function displayHistoryVisit($site, $date)
        {
            $standardDisplay = $this->checkFunctionPermission("history:view");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }
            
            $year = substr($date, 6, 4);
            $month = substr($date, 3, 2);
            $day = substr($date, 0, 2);

            $breadcrumbs = Controller::createBreadcrumbs("Site Work Date", $day . "-" . $month . "-" . $year);

            $start = $year . "-" . $month . "-" . $day . " 00:00:00";
            $end = $year . "-" . $month . "-" . $day . " 23:59:59";

            $people = $this->getSiteDatePeople($site, $start, $end);
            $assessments = $this->getSiteDateAssessments($site, $start, $end);
            $controls = $this->getSiteDateControls($site, $start, $end);

            $site = Site::find($site);
            
            return view('activities.visit', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'people' => $people,
                'assessments' => $assessments,
                'controls' => $controls,
                'site' => $site,
                'date' => $day . "-" . $month . "-" . $year,
            ]);
        }

        public function displayHistoryPerson($person, $site)
        {
            $standardDisplay = $this->checkFunctionPermission("history:view");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $site = Site::find($site);
            $person = Profile::find($person);
            // $breadcrumbs = Controller::createBreadcrumbs("Site person history", $site->name . " - " . $this->checkUserVisibility($person->id));    
            $breadcrumbs = Controller::createBreadcrumbs(
                                                            "Site person history", 
                                                            isset($site['name']) ? count($site['name']) : 0 . 
                                                            " - " . 
                                                            $this->checkUserVisibility($person->id)
                                                        );    

            $peopleController = new PeopleController();

            // $activities = $this->getSiteProfileActivities($site->id, $person->id);
            $activities = $this->getSiteProfileActivities(
                                                            isset($site['id']) ? count(array($site['id'])) : 0, 
                                                            isset($person['id']) ? count(array($person['id'])) : 0,
                                                        );
            $controls = $this->getSiteProfileControls(
                                                            isset($site['id']) ? count(array($site['id'])) : 0, 
                                                            isset($person['id']) ? count(array($person['id'])) : 0,
                                                    );
            $name = $this->checkUserVisibility($person->id);
            $membership = $peopleController->requestActiveMembership($person);
            
            return view('activities.person', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'activities' => $activities,
                'controls' => $controls,
                'name' => $name,
                'membership' => $membership,
            ]);
        }

        public function editSWMS($swms)
        {
            $standardDisplay = $this->checkFunctionPermission("history:edit");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $breadcrumbs = Controller::createBreadcrumbs("Site SWMS", NULL);

            $historyAssessment = Histories_Assessments::find($swms);
            $history = History::find($historyAssessment->history_id);
            $assessment = Assessment::find($historyAssessment->assessment_id);
            $peopleController = new PeopleController();

            

            $checkActionAssessments = Actions_Assessment::where('history_assessment_id', '=', $historyAssessment->id)
                                                            ->where('assessment_id', '=', $assessment->id)
                                                            ->count();
            if($checkActionAssessments == 0)
            {
                $membership = $peopleController->requestActiveMembership($standardDisplay['profile']);
                if(is_object($membership))
                {
                    $activeOrganisation = $membership->organisation_id;
                }
                else
                {
                    $activeOrganisation = 0;   
                }

                $newActionAssessment = new Actions_Assessment();
                $newActionAssessment->user_id = $standardDisplay['profile']->id;
                $newActionAssessment->site_id = $history->site_id;
                $newActionAssessment->zone_id = $history->zone_id;
                $newActionAssessment->assessment_id = $assessment->id;
                $newActionAssessment->active_organisation_id = $activeOrganisation;
                $newActionAssessment->history_assessment_id = $historyAssessment->id;
                $newActionAssessment->save();
            }
            else
            {
                $newActionAssessment = Actions_Assessment::where('history_assessment_id', '=', $historyAssessment->id)
                                                            ->where('assessment_id', '=', $assessment->id)
                                                            ->first();
            }
            
            return view('activities.siteSWMS', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'history' => $history,
                'historyAssessment' => $historyAssessment,
                'assessment' => $assessment,
                'actionAssessment' => $newActionAssessment,
            ]);
        }

        public function requestQuestion($actionAssessment, $assessmentQuestion, $question, $answer)
        {
            $actionAssessment = Actions_Assessment::find($actionAssessment);
            $response = array();

            // function is to work out what the next question is and return it
            if($assessmentQuestion == 0)
            {
                //find out if there have already been questions answered
                $last_question = Actions_Assessments_Question::where('action_assessment_id', '=', $actionAssessment->id)->orderBy('id', 'desc')->first();
                if(is_object($last_question))
                {
                    if($last_question->answer == "")
                    {
                        $question = Assessments_Question::find($last_question->question_id);

                        $answerOptions = Assessments_Questions_Answer::where('question_id', '=', $question->id)
                                                                        ->get();
                        $optionsArray = array();
                        $o = 0;
                        foreach($answerOptions as $ao)
                        {
                            $optionsArray[$o]['id'] = $ao->id;
                            $optionsArray[$o]['name'] = $ao->Assessments_Questions_Answers_Option->name;
                            $o++;
                        }

                        $response['question'] = $question;
                        $response['actionsQuestion'] = $last_question;
                        $response['comments'] = "";
                        $response['options'] = $optionsArray;
                    }
                    else
                    {
                        $response['question'] = "finished";
                        $response['comments'] = "";
                    }
                }
                else
                {
                    //get the first question of this assessment
                    $firstQuestion = Assessments_Question::where('assessment_id', '=', $actionAssessment->assessment_id)->orderBy('id', 'asc')->first();
                    
                    $question = new Actions_Assessments_Question();
                    $question->action_assessment_id = $actionAssessment->id;
                    $question->history_assessment_id = $actionAssessment->history_assessment_id;
                    $question->history_id = $actionAssessment->History_Assessment->history_id;
                    $question->question_id = $firstQuestion->id;
                    $question->save();

                    $answerOptions = Assessments_Questions_Answer::where('question_id', '=', $firstQuestion->id)
                                                                    ->get();
                    $optionsArray = array();
                    $o = 0;
                    foreach($answerOptions as $ao)
                    {
                        $optionsArray[$o]['id'] = $ao->id;
                        $optionsArray[$o]['name'] = $ao->Assessments_Questions_Answers_Option->name;
                        $o++;
                    }

                    $response['question'] = $firstQuestion;
                    $response['actionsQuestion'] = $question;
                    $response['comments'] = "";
                    $response['options'] = $optionsArray;
                }
            }
            else
            {
                //need to look at the question, save it, then return the next question to ask
                $oldQuestion = Actions_Assessments_Question::find($assessmentQuestion);
                $oldQuestion->question_id = $question;
                $oldQuestion->answer = $answer;
                $oldQuestion->save();

                //now find out what the next question should be
                $setupAssessmentQuestion = Assessments_Question::find($question);
                if($setupAssessmentQuestion->answer_type == "text")
                {
                    //just get the next question in the list
                    $newQuestion = Assessments_Question::where('id', '>', $question)
                                                            ->where('assessment_id', '=', $actionAssessment->assessment_id)
                                                            ->orderBy('id', 'asc')
                                                            ->first();
                    if(is_object($newQuestion))
                    {
                        $actionQuestion = new Actions_Assessments_Question();
                        $actionQuestion->action_assessment_id = $actionAssessment->id;
                        $actionQuestion->history_assessment_id = $actionAssessment->history_assessment_id;;
                        $actionQuestion->history_id = $actionAssessment->History_Assessment->history_id;
                        $actionQuestion->question_id = $newQuestion->id;
                        $actionQuestion->save();

                        $answerOptions = Assessments_Questions_Answer::where('question_id', '=', $newQuestion->id)
                                                                        ->get();

                        $optionsArray = array();
                        $o = 0;
                        foreach($answerOptions as $ao)
                        {
                            $optionsArray[$o]['id'] = $ao->id;
                            $optionsArray[$o]['name'] = $ao->Assessments_Questions_Answers_Option->name;
                            $o++;
                        }
                        
                        $response['question'] = $newQuestion;
                        $response['actionsQuestion'] = $actionQuestion;
                        $response['comments'] = "";
                        $response['options'] = $optionsArray;
                    }
                    else
                    {
                        $response['question'] = "finished";
                        $response['comments'] = "";
                    }
                }
                else
                {
                    //The question was an options question, need to find the action for the answer given
                    //find the corresponding answer for the recently saved question
                    $answer = Assessments_Questions_Answer::find($answer);
                    if(is_object($answer))
                    {
                        $response['comments'] = $answer->comments;
                        

                        if($answer->action == "end")
                        {
                            $response['question'] = "finished";
                        }
                        elseif($answer->action == "jump")
                        {
                            $newQuestion = Assessments_Question::find($answer->goto_id);

                            $actionQuestion = new Actions_Assessments_Question();
                            $actionQuestion->action_assessment_id = $actionAssessment->id;
                            $actionQuestion->history_assessment_id = $actionAssessment->history_assessment_id;;
                            $actionQuestion->history_id = $actionAssessment->History_Assessment->history_id;
                            $actionQuestion->question_id = $newQuestion->id;
                            $actionQuestion->save();

                            $answerOptions = Assessments_Questions_Answer::where('question_id', '=', $newQuestion->id)
                                                                            ->get();

                            $optionsArray = array();
                            $o = 0;
                            foreach($answerOptions as $ao)
                            {
                                $optionsArray[$o]['id'] = $ao->id;
                                $optionsArray[$o]['name'] = $ao->Assessments_Questions_Answers_Option->name;
                                $o++;
                            }

                            $response['question'] = $newQuestion;
                            $response['actionsQuestion'] = $actionQuestion;
                            $response['options'] = $optionsArray;
                            
                        }
                        elseif($answer->action == "proceed")
                        {
                            $nextQuestion = Assessments_Question::where('assessment_id', '=', $actionAssessment->assessment_id)
                                                                    ->where('id', '>', $question)
                                                                    ->orderBy('id', 'asc')
                                                                    ->first();
                            if(is_object($nextQuestion))
                            {
                                $actionQuestion = new Actions_Assessments_Question();
                                $actionQuestion->action_assessment_id = $actionAssessment->id;
                                $actionQuestion->history_assessment_id = $actionAssessment->history_assessment_id;;
                                $actionQuestion->history_id = $actionAssessment->History_Assessment->history_id;
                                $actionQuestion->question_id = $nextQuestion->id;
                                $actionQuestion->save();

                                $answerOptions = Assessments_Questions_Answer::where('question_id', '=', $nextQuestion->id)
                                                                                ->get();
                                $optionsArray = array();
                                $o = 0;
                                foreach($answerOptions as $ao)
                                {
                                    $optionsArray[$o]['id'] = $ao->id;
                                    $optionsArray[$o]['name'] = $ao->Assessments_Questions_Answers_Option->name;
                                    $o++;
                                }

                                $response['question'] = $nextQuestion;
                                $response['actionsQuestion'] = $actionQuestion;
                                $response['options'] = $optionsArray;
                            }
                            else 
                            {
                                $response['question'] = "finished";
                            }
                        }
                        else
                        {
                            $response['question'] = "finished";
                        }
                    }
                    else
                    {
                        $newQuestion = Assessments_Question::where('assessment_id', '=', $actionAssessment->assessment_id)
                                                                ->where('id', '>', $question)
                                                                ->orderBy('id', 'asc')
                                                                ->first();
                        if(is_object($newQuestion))
                        {
                            $actionQuestion = new Actions_Assessments_Question();
                            $actionQuestion->action_assessment_id = $actionAssessment->id;
                            $actionQuestion->history_assessment_id = $actionAssessment->history_assessment_id;;
                            $actionQuestion->history_id = $actionAssessment->History_Assessment->history_id;
                            $actionQuestion->question_id = $newQuestion->id;
                            $actionQuestion->save();

                            $answerOptions = Assessments_Questions_Answer::where('question_id', '=', $newQuestion->id)
                                                                            ->get();
                            $optionsArray = array();
                            $o = 0;
                            foreach($answerOptions as $ao)
                            {
                                $optionsArray[$o]['id'] = $ao->id;
                                $optionsArray[$o]['name'] = $ao->Assessments_Questions_Answers_Option->name;
                                $o++;
                            }

                            $response['question'] = $newQuestion;
                            $response['actionsQuestion'] = $actionQuestion;
                            $response['comments'] = "";
                            $response['options'] = $optionsArray;
                        }
                        else
                        {
                            $response['question'] = "finished";
                            $response['comments'] = "";
                        }
                    }
                }
            }

            return json_encode($response);        
        }

        public function requestPreviousQuestions($actionAssessment, $status)
        {
            $array = array();
            $x = 0;

            if($status == 0)
            {
                /*
                    Still in the process of filling in the questions, need all questions except the current one in the history
                */
                $lastQuestion = Actions_Assessments_Question::where('action_assessment_id', '=', $actionAssessment)->orderBy('id', 'desc')->first();
                $questions = Actions_Assessments_Question::where('action_assessment_id', '=', $actionAssessment)->where('id', '!=', $lastQuestion->id)->get();
            }
            else
            {
                /*
                    Finished the assessment.  Need all questions in the history
                */
                $questions = Actions_Assessments_Question::where('action_assessment_id', '=', $actionAssessment)->get();
            }
            foreach($questions as $q)
            {
                $array[$x]['question'] = $q->Question->question;
                
                if($q->Question->answer_type == "options")
                {
                    $array[$x]['answer'] = $q->Answer->Assessments_Questions_Answers_Option->name; 
                    $array[$x]['comments'] = $q->Answer->comments;
                }
                else
                {
                    $array[$x]['answer'] = $q->answer;    
                    $array[$x]['comments'] = "";
                }

                
                $x++;
            }
            
            return json_encode($array);
        }

        public function submitAssessment($assessment)
        {
            $standardDisplay = Controller::standardDisplay();
            $breadcrumbs = Controller::createBreadcrumbs("Site SWMS", "Signature");
            
            //load up the history assessment
            $actionAssessment = Actions_Assessment::find($assessment);
            $assessment = Histories_Assessments::find($actionAssessment->history_assessment_id);
            $questions = Actions_Assessments_Question::where('action_assessment_id', '=', $actionAssessment->id)->get();
            $score = 0;

            //calculate the final score
            foreach($questions as $q)
            {
                $assementQ = Assessments_Question::find($q->question_id);
                if($assementQ->answer_type == "options")
                {
                    $answer = Assessments_Questions_Answer::where('question_id', '=', $q->question_id)
                                                            ->where('option_id', '=', $q->answer)
                                                            ->first();
                    if(is_object($answer))
                    {
                        $score = $score + $answer->score;
                    }
                }
            }
            $assessment->score = $score;
            $assessment->status = "Completed";
            $assessment->save();

            return view('activities.signSWMS', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'assessment' => $assessment,
            ]);
        }


        public function postSignature(Request $request, $assessment)
        {       
            //decode and upload the signature image.

            $image = $request->thisOne;  // your base64 encoded
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $fileName = str::random(10) . '.png';
            $saveName = '/public/images/signatures/' . $fileName;
            $recordName = '/storage/images/signatures/' . $fileName;

            Storage::disk('local')->put($saveName, base64_decode($image));
            
            //image has now been uploaded to the server, save it against the assessment
            $assessment = Histories_Assessments::find($assessment);
            $assessment->signature = $recordName;
            $assessment->save();

            return $this->logActivity($assessment->history_id);        
        }


        public function displayHistoryControl($control, $site)
        {
            $standardDisplay = Controller::standardDisplay();
            $control = Control::find($control);
            $site = Site::find($site);
            $breadcrumbs = Controller::createBreadcrumbs("Site asset history", $control->Controls_Type->name . " - " . $control->serial);        

            $fieldsArray = array();
            $f = 0;

            $fields = Controls_Type_Field::where('controls_type_id', '=', $control->controls_type_id)->where('archived', '=', 0)->get();
            foreach($fields as $field)
            {
                $value = Controls_Field::where('control_id', '=', $control->id)
                                            ->where('control_field_id', '=', $field->id)
                                            ->first();
                
                $fieldsArray[$f]['name'] = $field->name;
                $fieldsArray[$f]['value'] = $value->value;
                
                $f++;
            }

            $usage = $this->getSiteControlUsage($control, $site);
            $movements = $this->getSiteControlMovements($control, $site);       
            
            return view('activities.control', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'fields' => $fieldsArray,
                'control' => $control,
                'usage' => $usage,
                'movements' => $movements,
            ]);
        }

        public function roundMinute()
        {
            $minute = date('i');

            $digit1 = substr($minute, 0, 1);
            $digit2 = substr($minute, 1, 1);

            if($digit2 == 8 OR $digit2 == 9 OR $digit2 == 1 OR $digit2 == 2)
            {
                $digit2 = 0;
            }

            if($digit2 == 3 OR $digit2 == 4 OR $digit2 == 6 OR $digit2 == 7)
            {
                $digit2 = 5;
            }

            return $digit1 . $digit2;
        }

        public function logActivity($history)
        {
            $standardDisplay = $this->checkFunctionPermission("activity:log");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $peopleController = new PeopleController();

            if($history == 0)
            {
                $history = new History();
                $history->id = 0;
                $assessments = array();

                $logon = $this->getLogon($standardDisplay['profile']);
                $history->site_id = $logon;
                $history->zone_id = 0;

                

                if($logon > 0)
                {
                    $zones = $peopleController->getSiteZones($history->site_id);
                }
                else 
                {
                    $zones = array();    
                }

                $startHour = date('H');
                $startMinute = $this->roundMinute();
                $endHour = 23;
                $endMinute = 59;
            }
            else
            {
                $history = History::find($history);
                $zones = $peopleController->getSiteZones($history->site_id);
                $assessments = json_decode($this->getActivityAssessments($history->activity_id, $history->id, $history->site_id, $history->zone_id));

                $startHour = substr($history->time_start, 11, 2);
                $startMinute = substr($history->time_start, 14, 2);
                $endHour = substr($history->time_end, 11, 2);
                $endMinute = substr($history->time_end, 14, 2);

            }

            $breadcrumbs = Controller::createBreadcrumbs("Log activity", NULL);   
            $people = new PeopleController();

            $sites = $people->getUsersSites($standardDisplay['profile'], "active");    
            
            $check = array();
            $c = 0;
            $activities = array();
            $a = 0;
            
            $trades = Profiles_Trade::where('profiles_id', '=', $standardDisplay['profile']->id)->get();
            foreach($trades as $trade)
            {
                $lActivities = Activities_Trades::where('trades_id', '=', $trade->trades_id)->get();
                foreach($lActivities as $act)
                {

                    if(!in_array($act->Activities->id, $check) && $act->Activities->archived == 0)
                    {
                        $activities[$a] = $act->Activities;
                        $check[$c] = $act->Activities->id;

                        $a++;
                        $c++;
                    }
                }
            }

            if($history->id == 0)
            {
                $readings = array();
                $worstReading = "Not defined";
            }
            else 
            {
                $historyControls = $this->getHistoryControls($history->id);
                $readings = $historyControls['controls'];
                $worstReading = $historyControls['worstReading'];
            }

            $module = 'history';
            $moduleID = $history->id;
            $files = File::where('module', '=', $module)
                                        ->where('module_id', '=', $moduleID)
                                        ->get();
            $logs = $this->retrieveLogs($module, $moduleID);
            
            return view('activities.logActivity', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'sites' => $sites,
                'activities' => $activities,
                'history' => $history,
                'zones' => $zones,
                'assessments' => $assessments,
                'startHour' => $startHour,
                'startMinute' => $startMinute,
                'endHour' => $endHour,
                'endMinute' => $endMinute,
                'readings' => $readings,
                'worstReading' => $worstReading,
                'files' => $files,
                'logs' => $logs,
            ]);
        }

        public function logEntry($history)
        {
            $standardDisplay = $this->checkFunctionPermission("activity:log");

            $zone = Sites_Maps_Zone::find($history->zone_id);
            $site = Site::find($history->site_id);

            return view('activities.logEntry', [
                'zone' => $zone,
                'site' => $site,
                'standardDisplay' => $standardDisplay,
            ]);
        }

        public function logExit($history)
        {
            $standardDisplay = $this->checkFunctionPermission("activity:log");

            $zone = Sites_Maps_Zone::find($history->zone_id);
            $site = Site::find($history->site_id);

            return view('activities.logExit', [
                'zone' => $zone,
                'site' => $site,
                'standardDisplay' => $standardDisplay,
            ]);
        }

        public function qrActivity($site, $zone)
        {
            $standardDisplay = $this->checkFunctionPermission("activity:log");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $site = Site::find($site);

            //check to see if the person is currently logged onto the site
            if($standardDisplay['site'] != $site->id)
            {
                $peopleController = new PeopleController();
                $peopleController->siteLogon($site->id);
            }

            if($site->zone_qr_code_function == 0)
            {
                $timeStart = date('Y-m-d H:i:s');
            
                $history = new History();
                
                $history->profiles_id = $standardDisplay['profile']->id;
                $history->site_id = $site->id;
                $history->zone_id = $zone;
                $history->activity_id = 0;
                $history->time_start = $timeStart;
                //$history->time_end = $timeStart;
                $history->archived = 0;
                $history->checked = 0;
                $history->type = "activity";

                $history->save();

                return $this->logActivity($history->id);
            }
            else
            {
                //lookup to see if this site already has a zone entry log active
                $zChecker = 0;
                $lookupHistories = History::where('type', '=', 'entry')
                                            ->where('profiles_id', '=', $standardDisplay['profile']->id)
                                            ->whereNull('time_end')
                                            ->get();

                foreach($lookupHistories as $lh)
                {
                    if($lh->zone_id == $zone)
                    {
                        $zChecker = 1;
                        $zHistory = $lh;
                    }
                    $lh->time_end = date('Y-m-d H:i:s');
                    $lh->save();
                    
                    $check = new Histories_Check();
                    $check->history_id = $lh->id;
                    $check->save();
                }

                if($zChecker == 0)
                {
                    $timeStart = date('Y-m-d H:i:s');
            
                    $history = new History();
                    
                    $history->profiles_id = $standardDisplay['profile']->id;
                    $history->site_id = $site->id;
                    $history->zone_id = $zone;
                    $history->activity_id = 0;
                    $history->time_start = $timeStart;
                    //$history->time_end = $timeStart;
                    $history->archived = 0;
                    $history->checked = 0;
                    $history->type = "entry";
    
                    $history->save();

                    return $this->logEntry($history);
                }
                else
                {
                    return $this->logExit($lh);
                }
            }

            
            
            
            


            $this->insertLog($standardDisplay['profile']->id, "history", $history->id, "Created", "Activity logged against " . $site->name . " " . "Created", "INFO");
            
            if($site->zone_qr_code_function == 0)
            {
                
            }
            else
            {
                
                $history->save();
                
            }
        }

        public function printableQRCode($zone)
        {
            $zone = Sites_Maps_Zone::find($zone);
            $site = Site::find($zone->site_id);

            $qrAddress = env("APP_URL", "https://client.nextrack.tech/") . "qrActivity/" . $zone->site_id . "/" . $zone->id;

            return view('activities.printableQRCode', [
                'qrAddress' => $qrAddress,
                'zone' => $zone,
                'site' => $site,
            ]);
        }

        public function printableSiteQRCode($site)
        {
            $site = Site::find($site);

            $qrAddress = env("APP_URL", "https://client.nextrack.tech/") . "site/logon/" . $site->id;

            return view('activities.printableSiteQRCode', [
                'qrAddress' => $qrAddress,
                'site' => $site,
            ]);
        }
        
        public function getHistoryControls($history)
        {
            //$history is the history ID

            //first off get all the types
            $returnArray = array();
            $worstReadings = "ok";

            $typeArray = array();
            $t = 0;
            $types = Controls_Type::where('archived', '=', 0)->orderBy('name', 'asc')->get();
            foreach($types as $type)
            {
                $fieldsArray = array();
                $f = 0;

                $fields = Controls_Type_Field::where('controls_type_id', '=', $type->id)
                                                ->orderBy('id', 'asc')
                                                ->limit(3)
                                                ->get();
                foreach($fields as $field)
                {
                    $fieldsArray[$f] = $field->name;
                    $f++;
                }

                
                $historyControls = Actions_Control::where('history_id', '=', $history)->get();
                foreach($historyControls as $hc)
                {
                    $controlsArray = array();
                    $c = 0;
                    $controls = Control::where('id', '=', $hc->control_id)
                                            ->where('controls_type_id', '=', $type->id)
                                            ->where('archived', '=', 0)
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
                            }
                            else
                            {
                                $newValue = new Controls_Field();
                                $newValue->control_id = $control->id;
                                $newValue->control_field_id = $field->id;
                                $newValue->save();

                                $valuesArray[$v]['value'] = "";

                            }
                            $valuesArray[$v]['field'] = $field->name;
                            $v++;
                        }
                        $outcome = "ok";

                        $readings = Thingsboards_Device_Reading::where('history_id','=', $history)
                                                                    ->where('control_id', '=', $control->id)
                                                                    ->get();
                        foreach($readings as $reading)
                        {
                            if($reading->outcome == "not ok")
                            {
                                $outcome = "not ok";
                                $worstReadings = "not ok";
                            }
                            
                            if($reading->outcome == "monitor" && $outcome != "not ok")
                            {
                                $outcome = "monitor";
                                if($worstReadings != "not ok")
                                {
                                    $worstReadings = "monitor";
                                }
                            }
                        }

                        $controlsArray[$c]['control'] = $control;
                        $controlsArray[$c]['fieldValues'] = $valuesArray;
                        $controlsArray[$c]['readings'] = $readings;
                        $controlsArray[$c]['outcome'] = $outcome;
                        
                        $c++;   
                        
                    }
                    if(count($controlsArray) > 0)
                    {
                        $typeArray[$t]['fields'] = $fieldsArray;
                        $typeArray[$t]['controls'] = $controlsArray;
                        $typeArray[$t]['type'] = $type;
                        $t++;
                    }

                    unset($controlsArray);
                    
                }
                unset($fieldsArray);
            }
            $returnArray['controls'] = $typeArray;
            $returnArray['worstReading'] = $worstReadings;

            return $returnArray;
        }

        public function getActivityAssessments($activity, $loggedActivity, $site, $zone)
        {
            $standardDisplay = Controller::standardDisplay();

            if($loggedActivity == 0)
            {
                $history = new History();
                $history->profiles_id = $standardDisplay['profile']->id;
                $history->site_id = $site;
                $history->zone_id = $zone;
                $history->activity_id = $activity;
                $history->time_start = date('Y-m-d H:i:s');
                //$history->time_end = date('0000-00-00 00:00:00');
                $history->activity_id = $activity;
                $history->archived = 0;

                $history->save();
            }
            else
            {
                $history = History::find($loggedActivity);
                $history->activity_id = $activity;

                $history->save();
            }

            
            //find all the assessments already on the History
            $array = array();
            $a = 0;
            $assessmentArray = array();
            $i = 0;

            $historyAssessments = Histories_Assessments::where('history_id', '=', $history->id)->get();
            foreach($historyAssessments as $ass)
            {
                $array[$a] = $ass->assessment_id;
                $a++;
            }
            
            //find all the assessments that need to be done on this history based on the activity
            $assessments = Assessments_Activities::where('activities_id', '=', $activity)->get();
            foreach($assessments as $ass)
            {
                $entry = 0;
                
                if(!in_array($ass->assessment_id, $array) && $ass->Assessment->archived == 0)
                {
                    $ok = 0;

                    //check to make sure that the assessment is not limited to specific sites.
                    $checkSites = Assessments_Site::where('assessments_id', '=', $ass->assessment_id)->count();
                    if($checkSites == 0)
                    {
                        $ok = 1;
                    }
                    else 
                    {
                        $checkSite = Assessments_Site::where('assessments_id', '=', $ass->assessment_id)->where('sites_id', '=', $history->site_id)->count();
                        if($checkSite > 0)
                        {
                            $ok = 1;
                        }

                    }

                    if($ok == 1)
                    {
                        //add it to the history entry
                        //check to see whether it needs to be done on this assessment based on whether its just the 
                        //first assessment for the site, zone, day or per activity
                        $ok = 0;
                        if($ass->Assessment->once_per == "assessment")
                        {
                            $ok = 1;
                        }
                        else
                        {
                            if($ass->Assessment->once_per == "day")
                            {
                                $tc = 0;
                                $todayStart = date('Y-m-d') . " 00:00:00";
                                $todayEnd = date('Y-m-d') . " 23:59:59";

                                //check to see whether this person has already done this assessment today
                                $todaysActivities = History::where('profiles_id', '=', $standardDisplay['profile']->id)
                                                            ->whereBetween('created_at', [$todayStart, $todayEnd])
                                                            ->get();
                                foreach($todaysActivities as $ta)
                                {
                                    $checkAssessment = Histories_Assessments::where('history_id', '=', $ta->id)
                                                                                ->where('assessment_id', '=', $ass->assessment_id)
                                                                                ->count();                                                                             
                                    if($checkAssessment > 0)
                                    {
                                        $tc = 1;
                                    }
                                }
                                if($tc == 0)
                                {
                                    $ok = 1;
                                }
                            }
                            
                            if($ass->Assessment->once_per == "site")
                            {
                                $sc = 0;
                                //check to see whether this person has already done this assessment on this site
                                $sitesActivities = History::where('profiles_id', '=', $standardDisplay['profile']->id)
                                                            ->where('site_id', '=', $site)
                                                            ->get();
                                foreach($sitesActivities as $sa)
                                {
                                    $checkAssessment = Histories_Assessments::where('history_id', '=', $sa->id)
                                                                                ->where('assessment_id', '=', $ass->assessment_id)
                                                                                ->count();
                                    if($checkAssessment > 0)
                                    {
                                        $sc = 1;
                                    }
                                }
                                if($sc == 0)
                                {
                                    $ok = 1;
                                }
                            }

                            if($ass->Assessment->once_per == "zone")
                            {
                                $zc = 0;
                                //check to see whether this person has already done this assessment on this site
                                $zoneActivities = History::where('profiles_id', '=', $standardDisplay['profile']->id)
                                                            ->where('zone_id', '=', $zone)
                                                            ->get();
                                foreach($zoneActivities as $za)
                                {
                                    $checkAssessment = Histories_Assessments::where('history_id', '=', $za->id)
                                                                                ->where('assessment_id', '=', $ass->assessment_id)
                                                                                ->count();
                                    if($checkAssessment > 0)
                                    {
                                        $zc = 1;
                                    }
                                }
                                if($zc == 0)
                                {
                                    $ok = 1;
                                }
                            }
                        }      

                        if($ok == 1)
                        {
                            $new = new Histories_Assessments();
                            $new->history_id = $history->id;
                            $new->assessment_id = $ass->assessment_id;
                            $new->status = "Pending";
                            $new->score = 0;
                            $new->save();
                            
                            $entry = $new;
                        }
                    }
                }
                
            }

            //now go through and put them all into the assessmentArray
            $historyAssessments = Histories_Assessments::where('history_id', '=', $history->id)->get();
            foreach($historyAssessments as $ass)
            {
                if(is_object($ass->Assessment))
                {
                    $eArray = array();
                    $eArray['id'] = $ass->id;
                    $eArray['name'] = $ass->Assessment->name;
                    $eArray['status'] = $ass->status;
                    $eArray['score'] = $ass->score;

                    $assessmentArray[$i] = $eArray;
                    $i++;

                    unset($eArray);
                }
                else
                {
                    //someone has broken the link in the database, clean up the db.
                    $ass->delete();
                }
            }

            $return['id'] = $history->id;
            $return['assessments'] = $assessmentArray;

            return json_encode($return);
        }

        public function saveActivity(Request $request, $from)
        {
            /*
                Function handles all activities from an activity being saved.
                $from is a 1 if its coming from most of the app, its a 2 if its coming from the queued job
            */
            $peopleController = new PeopleController();
            if($from == 1)
            {
                $standardDisplay = $this->checkFunctionPermission("activity:log");
                if($standardDisplay == 0)
                {
                    $index = new HomeController();
                    $alert = "You do not have privileges to do that.";

                    return $index->displayDashboard($alert);
                }

            }

            if($request->loggedActivity == 0)
            {
                $history = new History();
                $history->profiles_id = $standardDisplay['profile']->id;
                $history->archived = 0;
                $history->checked = 0;
                $dateStart = date('Y-m-d');

                $action = "created";
            }
            else
            {
                $history = History::find($request->loggedActivity);
                $action = "edited";
                $dateStart = substr($history->time_start, 0, 10);
            }

            $history->site_id = $request->site;
            $history->zone_id = $request->zone;
            $history->activity_id = $request->activity;
            
            if($request->startHour == "00")
            {
                $startHour = date('H');
            }
            else
            {
                $startHour = $request->startHour;
            }

            if($request->endHour == "00")
            {
                $endHour = date('H');
            }
            else
            {
                $endHour = $request->endHour;
            }


            if(empty($request->startMinute))
            {
                $startMinute = "00";
            }
            else 
            {
                $startMinute = $request->startMinute;
            }

            if(empty($request->endMinute))
            {
                $endMinute = "00";
            }
            else 
            {
                $endMinute = $request->endMinute;
            }

            $start = $dateStart . " " . $startHour . ":" . $startMinute;
            $end = date('Y-m-d') . " " . $endHour . ":" . $endMinute;

            $history->time_start = $start;
            $history->time_end = $end;

            $history->save();

            //find out if there is already a time entry for this history
            $timeEntry = Actions_Time_Entry::where('history_id', '=', $history->id)->first();
            if(is_object($timeEntry))
            {
                $entry = $timeEntry;
            }
            else
            {
                $entry = new Actions_Time_Entry();
                $entry->date = date('Y-m-d');
                $entry->history_id = $history->id;
                $entry->user_id = $history->profiles_id;

                $membership = $peopleController->requestActiveMembership($history->profiles_id);
                if(is_object($membership))
                {
                    $activeOrganisation = $membership->organisation_id;
                }
                else
                {
                    $activeOrganisation = 0;   
                }

                $entry->active_organisation_id = $activeOrganisation;
            }

            
            $entry->site_id = $request->site;
            $entry->zone_id = $request->zone;
            $entry->start = $startHour . ":" . $request->startMinute . ":00";
            $entry->finish = $endHour . ":" . $request->endMinute . ":00";

            $entry->save(); 
            
            $site = Site::find($history->site_id);
            
            //save all the controls to this history
            $controlsArray = array();
            $ca = 0;
            
            if($history->zone_id > 0)
            {
                //lets check to see if this zone has any hazards
                $zoneHazards = Sites_Maps_Zones_Hazard::where('zone_id', '=', $history->zone_id)->get();
                $hazardControlCheck = 0;
                foreach($zoneHazards as $zh)
                {
                    //check to see if any of those hazards are associated with the activity on this history
                    $hazardActivities = Hazards_Activities::where('hazard_id', '=', $zh->hazard_id)
                                                            ->where('activity_id', '=', $history->activity_id)
                                                            ->get();
                    if(count($hazardActivities) > 0)
                    {
                        //see if there are any controls on this hazard
                        $siteControls = Control::where('current_site', '=', $history->site_id)->get();
                        foreach($siteControls as $sc)
                        {
                            $hazardControls = Controls_Sites::where('control_id', '=', $sc->id)->orderBy('id', 'desc')->limit(1)->get();
                            foreach($hazardControls as $hc)
                            {
                                if($hc->to_hazard_id == $zh->id)
                                {
                                    //we have found a control in a hazard for the activity that is being done - lets get these controls
                                    $controlsArray[$ca] = $peopleController->getHazardControls($zh);
                                    $ca++;
                                }
                            }
                        }
                    }
                }
                //get the zones controls
                if($ca == 0)
                {
                    $zone = Sites_Maps_Zone::find($history->zone_id);
                    $controlsArray[$ca] = $peopleController->getZoneControls($zone, $site);
                }
            }
            else
            {
                //get the sites controls
                $controlsArray[$ca] = $peopleController->getSiteControls($site);
            }
            $profile = Profile::find($history->profiles_id);
            $membership = $peopleController->requestActiveMembership($profile);
            if(is_object($membership))
            {
                $activeOrganisation = $membership->organisation_id;
            }
            else
            {
                $activeOrganisation = 0;   
            }

            foreach($controlsArray as $controls)
            {
                foreach($controls as $cntrl)
                {
                    foreach($cntrl['controls'] as $control)
                    {
                        //check to see if this control is already on the card for this history
                        $check = Actions_Control::where('control_id', '=', $control['control']->id)
                                                    ->where('history_id', '=', $history->id)
                                                    ->count();
                        if($check == 0)
                        {
                            $ctrl = new Actions_Control();
                        }
                        else
                        {
                            $ctrl = Actions_Control::where('control_id', '=', $control['control']->id)
                                                        ->where('history_id', '=', $history->id)
                                                        ->first();
                        }
                        $ctrl->user_id = $history->profiles_id;
                        $ctrl->control_id = $control['control']->id;
                        $ctrl->zone_id = $history->zone_id;
                        $ctrl->site_id = $history->site_id;
                        $ctrl->active_organisation_id = $activeOrganisation;
                        $ctrl->history_id = $history->id;
                        
                        $ctrl->save();
                    }
                }
            }

            //add this history to the checklist to see if it can get some readings
            $check = new Histories_Check();
            $check->history_id = $history->id;
            $check->save();

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
                    $f->module_id = $history->id;
                    $f->module = "history";
                    
                    $f->save();
                }
            }

            $this->insertLog($history->profiles_id, "history", $history->id, $action, "Activity logged against " . $site->name . " " . $action, "INFO");

            if($from == 1)
            {
                return $this->sites();
            }
            else 
            {
                return 2;
            }
        }

        public function getZoneControlIds($zone, $site)
        {
            //get all the controls on the site
            $controlsArray = array();
            $x = 0;

            $controls = Control::where('current_site', '=', $site->id)->get();

            foreach($controls as $control)
            {
                //find out if they are currently in this zone
                $movement = Controls_Sites::where('control_id', '=', $control->id)->orderBy('id', 'desc')->first();
                if($movement->to_zone_id == $zone->id)
                {
                    $controlsArray[$x] = $control;
                    $x++;
                }
            }

            return $controlsArray;
        }

        public function getSiteControlIds($site)
        {
            //get all the controls on the site
            $controls = Control::where('current_site', '=', $site->id)->get();

            return $controls;
        }

        public function getSiteDatePeople($site, $start, $end)
        {
            //find all the histories on this site       
            $people = array();

            $p = 0;

            $histories = History::whereBetween('created_at', [$start, $end])
                                    ->where('site_id', '=', $site)
                                    ->select('profiles_id')
                                    ->distinct('profiles_id')
                                    ->get();
            foreach($histories as $history)
            {
                $timeEntry = Actions_Time_Entry::where('history_id', '=', $history->id)->first();
                if(is_object($timeEntry))
                {
                    $member = $timeEntry->Organisation->name;
                }
                else
                {
                    $member = "unknown";
                }

                $people[$p]['id'] =  $history->profiles_id;
                $people[$p]['name'] =  $this->checkUserVisibility($history->profiles_id);
                $people[$p]['rName'] = $history->Profile->name;
                $people[$p]['memberOf'] = $member;
                $people[$p]['email'] = $history->Profile->email;
                $people[$p]['mobile'] = $history->Profile->mobile;

                $p++;
            }

            return $people;
        }

        public function getSiteDateAssessments($site, $start, $end)
        {
            $assessmentsArray = array();
            $a = 0;
            $histories = History::whereBetween('created_at', [$start, $end])
                                    ->where('site_id', '=', $site)
                                    ->get();
            foreach($histories as $history)
            {
                $assessments = Histories_Assessments::where('status', '=', 'Completed')
                                                        ->where('history_id', '=', $history->id)
                                                        ->get();
                foreach($assessments as $assessment)
                {
                    $timeEntry = Actions_Time_Entry::where('history_id', '=', $assessment->history_id)->first();
                    
                    if(is_object($timeEntry))
                    {
                        $member = $timeEntry->Organisation->name;
                    }
                    else
                    {
                        $member = "unknown";
                    }

                    $assessmentsArray[$a]['name'] =  $this->checkUserVisibility($history->profiles_id);
                    $assessmentsArray[$a]['id'] = $assessment->id;
                    $assessmentsArray[$a]['assessment'] = $assessment->Assessment->name;
                    $assessmentsArray[$a]['score'] = $assessment->score;
                    $assessmentsArray[$a]['memberOf'] = $member;

                    $a++;
                }
            }
            return $assessmentsArray;
        }

        public function getSiteDateControls($site, $start, $end)
        {
            $controls = array();
            $a = 0;

            
            $actions = Actions_Control::whereBetween('created_at', [$start, $end])
                                    ->where('site_id', '=', $site)
                                    ->get();
            foreach($actions as $action)
            {
                $timeEntry = Actions_Time_Entry::where('history_id', '=', $action->history_id)->first();
                $time = 0;

                if(is_object($timeEntry))
                {
                    $member = $timeEntry->Organisation->name;
                    $time = $this->calcTime($timeEntry->id, "hours");
                }
                else
                {
                    $member = "unknown";
                }

                
                if(is_object($action->Control))
                {
                    $controls[$a]['type'] = $action->Control->Controls_Type->name;
                    $controls[$a]['serial'] = $action->Control->serial;
                }
                else
                {
                    $controls[$a]['type'] = "-";
                    $controls[$a]['serial'] = "-";
                }
                
                if(is_object($action->Zone))
                {
                    $controls[$a]['map'] = $action->Zone->Sites_Map->name;
                    $controls[$a]['zone'] = $action->Zone->name;
                }
                else
                {
                    $controls[$a]['map'] = "-";
                    $controls[$a]['zone'] = "-";
                }
                
                $controls[$a]['name'] =  $this->checkUserVisibility($action->History->profiles_id);
                $controls[$a]['memberOf'] = $member;
                $controls[$a]['time'] = $time;

                $a++;
            }
            

            return $controls;
        }

        public function getSiteProfileActivities($site, $profile)
        {
            $historiesArray = array();
            $a = 0;
            $histories = History::where('profiles_id', '=', $profile)
                                    ->where('site_id', '=', $site)
                                    ->get();
            foreach($histories as $history)
            {
                $assessments = Histories_Assessments::where('status', '=', 'Completed')
                                                        ->where('history_id', '=', $history->id)
                                                        ->count();
                
                $timeEntry = Actions_Time_Entry::where('history_id', '=', $history->id)->first();
                
                if(is_object($timeEntry))
                {
                    $member = $timeEntry->Organisation->name;
                }
                else
                {
                    $member = "unknown";
                }
                $historiesArray[$a]['history'] = $history;
                $historiesArray[$a]['assessments'] = $assessments;

                $a++;
                
            }
            return $historiesArray;
        }

        public function getSiteProfileControls($site, $profile)
        {
            $controls = array();
            $a = 0;
            
            $actions = Actions_Control::where('site_id', '=', $site)
                                        ->where('user_id', '=', $profile)
                                        ->get();
            foreach($actions as $action)
            {
                $timeEntry = Actions_Time_Entry::where('history_id', '=', $action->history_id)->first();
                $time = 0;

                if(is_object($timeEntry))
                {
                    $member = $timeEntry->Organisation->name;
                    $time = $this->calcTime($timeEntry->id, "hours");
                }
                else
                {
                    $member = "unknown";
                }
                
                $controls[$a]['date'] = $action->created_at->format('d-m-Y');
                if(is_object($action->Control->Controls_Type))
                {
                    $typeName = $action->Control->Controls_Type->name;
                }
                else
                {
                    $typeName = "-";
                }

                if(is_object($action->Control))
                {
                    $serial = $action->Control->serial;
                }
                else
                {
                    $serial = "-";
                }
                
                if(is_object($action->Zone))
                {
                    $zoneName = $action->Zone->name;
                    if(is_object($action->Zone->Sites_Map))
                    {
                        $mapName = $action->Zone->Sites_Map->name;
                    }
                }
                else
                {
                    $zoneName = "-";
                    $mapName = "-";
                }

                $controls[$a]['type'] = $typeName;
                $controls[$a]['serial'] = $serial;
                $controls[$a]['map'] = $mapName;
                $controls[$a]['zone'] = $zoneName;
                $controls[$a]['time'] = $time;

                $a++;
            }
            

            return $controls;
        }

        public function getSiteControlUsage($control, $site)
        {
            $controls = array();
            $a = 0;
            
            // $actions = Actions_Control::where('site_id', '=', $site->id)
            $actions = Actions_Control::where('site_id', '=', isset($site['id']) ? count(array($site['id'])) : 0)
                                        ->where('control_id', '=', $control->id)
                                        ->get();
            foreach($actions as $action)
            {
                $timeEntry = Actions_Time_Entry::where('history_id', '=', $action->history_id)->first();
                $time = 0;

                if(is_object($timeEntry))
                {
                    if(is_object($timeEntry->Organisation))
                    {
                        $member = $timeEntry->Organisation->name;
                    }
                    else
                    {
                        $member = "unknown";
                    }
                    $time = $this->calcTime($timeEntry->id, "hours");
                }
                else
                {
                    $member = "unknown";
                }
                
                if(is_object($action->Zone))
                {
                    $zoneName = $action->Zone->name;
                    if(is_object($action->Zone->Sites_Map))
                    {
                        $mapName = $action->Zone->Sites_Map->name;
                    }
                }
                else
                {
                    $zoneName = "-";
                    $mapName = "-";
                }

                
                $controls[$a]['date'] = $action->created_at->format('d-m-Y');
                $controls[$a]['map'] = $mapName;
                $controls[$a]['zone'] = $zoneName;
                $controls[$a]['time'] = $time;
                $controls[$a]['name'] =  $this->checkUserVisibility($action->History->profiles_id);
                $controls[$a]['memberOf'] = $member;

                $a++;
            }
            

            return $controls;
        }

        public function getSiteControlMovements($control, $site)
        {
            // $arrival = Controls_Sites::where('from_site_id', '!=', $site->id)
            $arrival = Controls_Sites::where('from_site_id', '!=', isset($site['id']) ? count(array($site['id'])) : 0)
                                        // ->where('to_site_id', '=', $site->id)
                                        ->where('to_site_id', '=', isset($site['id']) ? count(array($site['id'])) : 0)
                                        ->orderBy('id', 'asc')
                                        ->first();

            // $removal = Controls_Sites::where('from_site_id', '=', $site->id)
            $removal = Controls_Sites::where('from_site_id', '=', isset($site['id']) ? count(array($site['id'])) : 0)
                                        // ->where('to_site_id', '!=', $site->id)
                                        ->where('to_site_id', '!=', isset($site['id']) ? count(array($site['id'])) : 0)
                                        ->orderBy('id', 'desc')
                                        ->first();

            // $movements = Controls_Sites::where('from_site_id', '=', $site->id)
            $movements = Controls_Sites::where('from_site_id', '=', isset($site['id']) ? count(array($site['id'])) : 0)
                                            // ->where('to_site_id', '=', $site->id)
                                            ->where('to_site_id', '=', isset($site['id']) ? count(array($site['id'])) : 0)
                                            ->get();

            $array = array();
            $array['arrival'] = $arrival;
            $array['removal'] = $removal;
            $array['movements'] = $movements;

            return $array;
        }

        public function getLogon($profile)
        {
            $loggedOn = Sites_Logon::where('profile_id', '=', $profile->id)
                                    ->whereNull('time_out')
                                    ->get();
            if(count($loggedOn) == 0)
            {
                $site = 0;
            }
            else
            {
                $site = $loggedOn[0]->site_id;
            }

            return $site;
        }

        public function deleteActivity($activity)
        {
            /*
                Function needs to be used through the URL (no access through UI) and just helps to keep database clean
                after some tests have been run
            */
            $history = History::find($activity);

            //find all the assessments
            $historyAssessments = Histories_Assessments::where('history_id', '=', $history->id)->get();
            foreach($historyAssessments as $ha)
            {
                $actionAssessments = Actions_Assessment::where('history_assessment_id', '=', $ha->id)->get();
                foreach($actionAssessments as $aa)
                {
                    $questions = Actions_Assessments_Question::where('action_assessment_id', '=', $aa->id)
                                                                ->where('history_assessment_id', '=', $ha->id)
                                                                ->delete();
                    $aa->delete();
                }
                $ha->delete();
            }
            $actionControls = Actions_Control::where('history_id', '=', $history->id)->delete();
            $actionTimes = Actions_Time_Entry::where('history_id', '=', $history->id)->delete();
            $readings = Thingsboards_Device_Reading::where('history_id', '=', $history->id)->delete();
            $history->delete();

            return "Cleaned.";
        }

    //End Site History


    //Exposures
        public function exposures()
        {
            $standardDisplay = Controller::standardDisplay();
            $breadcrumbs = Controller::createBreadcrumbs("Exposures", NULL);

            $exposuresArray = array();
            $x = 0;
            $peopleController = new PeopleController();
            $thingsboardController = new ThingsboardController();

            //get all the users this person is allowed to see
            /*
            
            COMMENTING THIS CODE OUT DUE TO KRISTENS REQUEST NOT TO SEE ANY EXPOSURES OTHER THAN YOUR OWN... NOT SURE
            THIS HAS IS ACCURATE TO KEEPING CODE TO EASILY REVERT LATER IF NEED BE

            
            $profiles = $peopleController->userProfiles($standardDisplay);
            foreach($profiles as $profile)
            {
                $exposures = $thingsboardController->calculateExposures($profile, "exposures");
                $worstOutcome = "unknown";
                foreach($exposures as $exposure)
                {
                    if($exposure['outcome'] == "not ok")
                    {
                        $worstOutcome = "not ok";
                        break;
                    }
                    if($exposure['outcome'] == "monitor" && $worstOutcome == "unknown")
                    {
                        $worstOutcome = "monitor";
                    }
                    if($exposure['outcome'] == "ok" && $worstOutcome == "unknown")
                    {
                        $worstOutcome = "ok";
                    }
                }
                
                $exposuresArray[$x]['profile'] = $profile;
                $exposuresArray[$x]['exposure'] = $worstOutcome;
                $x++;
            }
             */
           

            /*
            INSTEAD USING THIS CODE - PERHAPS TEMPORARILY INCASE ABOVE NEEDS TO REINSTATED
            */

            
            $exposures = $thingsboardController->calculateExposures($standardDisplay['profile'], "exposures");
            $worstOutcome = "unknown";
            foreach($exposures as $exposure)
            {
                if($exposure['outcome'] == "not ok")
                {
                    $worstOutcome = "not ok";
                    break;
                }
                if($exposure['outcome'] == "monitor" && $worstOutcome == "unknown")
                {
                    $worstOutcome = "monitor";
                }
                if($exposure['outcome'] == "ok" && $worstOutcome == "unknown")
                {
                    $worstOutcome = "ok";
                }
            }
            
            
            $exposuresArray[$x]['profile'] = $standardDisplay['profile'];
            $exposuresArray[$x]['exposure'] = $worstOutcome;
            $x++;
                

            return view('activities.exposures', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'exposuresArray' => $exposuresArray,
            ]);
        }

        public function exposureDetails($person)
        {
            $standardDisplay = Controller::standardDisplay();
            $person = Profile::find($person);
            $breadcrumbs = Controller::createBreadcrumbs("Exposure", $person->name);

            $thingsboardController = new ThingsboardController();

            $exposures = $thingsboardController->calculateExposures($person, "exposures");
            
            return view('activities.exposurePerson', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'exposures' => $exposures,
                'person' => $person,
            ]);
        }

        public function exposureTypeDetails($person, $type)
        {
            $standardDisplay = Controller::standardDisplay();
            $person = Profile::find($person);
            $breadcrumbs = Controller::createBreadcrumbs("Exposures", "Exposure of " . $type . " for " . $person->name);
            $range['start'] = date("Y-m-d", strtotime("-7 days"));
            $range['end'] = date("Y-m-d");
            $outcome = "unknown";

            $exposuresArray = array();
            $x = 0;
            $thingsboardController = new ThingsboardController();

            $exposures = $thingsboardController->exposureDetails($person->id, $type, $range);

            $allExposures = $thingsboardController->calculateExposures($person, "exposures");
            
            foreach($allExposures as $ae)
            {
                if($ae['type'] == $type)
                {
                    $outcome = $ae['outcome'];
                }
            }

            return view('activities.exposureDetail', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'outcome' => $outcome,
                'person' => $person,
                'exposures' => $exposures,
                'type' => $type,
            ]);

        }

    //End Exposures
}