<?php

namespace App\Http\Controllers;

use App\Models\Actions_Assessment;
use App\Models\Actions_Control;
use App\Models\Actions_Time_Entry;
use App\Models\Assessments_Activities;
use App\Models\Assessments_Site;
use App\Models\Control;
use App\Models\Controls_Field;
use App\Models\Controls_Orders;
use App\Models\Controls_Sites;
use App\Models\Controls_Type;
use App\Models\Controls_Type_Field;
use App\Models\Dashboard;
use App\Models\Email;
use App\Models\File;
use App\Models\Hazard;
use App\Models\Hazards_Activities;
use App\Models\Hazards_Trades;
use App\Models\History;
use App\Models\License_Profile;
use App\Models\License;
use App\Models\Log;
use App\Models\Membership;
use App\Models\Permit;
use App\Models\Permits_Profile;
use App\Models\Permits_Site;
use App\Models\Permits_Training;
use App\Models\Permits_Zone;
use App\Models\Profile;
use App\Models\Profiles_Trade;
use App\Models\Security_Groups;
use App\Models\Sample;
use App\Models\Site;
use App\Models\Sites_Contractors;
use App\Models\Sites_Logon;
use App\Models\Sites_Map;
use App\Models\Sites_Maps_Zone;
use App\Models\Sites_Maps_Zones_Hazard;
use App\Models\Sites_Maps_Zones_Hazards_Plan_Step;
use App\Models\Sites_Maps_Zones_Hazards_Sample;
use App\Models\Sites_Profile;
use App\Models\Sites_Reports;
use App\Models\Task;
use App\Models\Thingsboards_Device;
use App\Models\Thingsboards_Device_Reading;
use App\Models\Thingsboards_Device_Reading_Types;
use App\Models\Trade;
use App\Models\Training;
use App\Models\Trainings_Hygenist;
use App\Models\Trainings_Profile;
use App\Models\User;

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportController;

use App\Jobs\emailSender;

use Illuminate\Http\Request;
use DateTime;
use Auth;

class PeopleController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */


    //Memberships functions
        public function requestMembership($hash, $requestor)
        {
            /*  
                Function to submit a new membership request (with all tasks etc.)
                $hash is the Hash being submitted to find the organisation they are trying to join
                $requestor is the Profile ID of the profile requesting the membership
            */

            $response = array();
            $standardDisplay = $this->standardDisplay();

            //find out if the requestor is a user or an organisation
            $profile = Profile::find($requestor);
            if($profile->type == "user")
            {
                //a user is requesting to join an organisation - find the organisation
                $user = $profile;
                $organisation = Profile::where('member_hash', '=', $hash)->first();
                if(empty($organisation))
                {
                    $response['message'] = "That ID doesn't exist";   
                    return json_encode($response);
                    exit; 
                }
                $status = "user requested";
                $message = "requested to join $organisation->type, $organisation->name.  Waiting on $organisation->type's acceptance.";
                $response['message'] = "You have $message";
                $log = "User " . $message;
            }
            elseif($profile->type == "contractor")
            {
                //lets find out if the contractor is trying to join a builder or requesting a user to join them
                $requesting = Profile::where('member_hash', '=', $hash)->first();
                if($requesting->type == "user")
                {
                    //contractor is asking a user to join them
                    $organisation = $profile;
                    $user = Profile::where('member_hash', '=', $hash)->first();
                    if(empty($user))
                    {
                        $response['message'] = "That ID doesn't exist";  
                        return json_encode($response);  
                        exit;
                    }
                    $status = "organisation requested";
                    $message = "requested user $user->name to join your organisation. Waiting on users acceptance.";
                    $response['message'] = "$organisation->name " . $message;
                    $log = "$organisation->name " . $message;

                    $log = "User " . $message;
                }
                elseif($requesting->type == "hygenist")
                {
                    $organisation = $requesting;
                    $user = $profile;
                    if(empty($organisation))
                    {
                        $response['message'] = "That ID doesn't exist";  
                        return json_encode($response);  
                        exit;
                    }
                    $status = "user requested";
                    $message = "requested $requesting->type $requesting->name to join your organisation. Waiting on $requesting->type acceptance.";
                    $response['message'] = "$organisation->name " . $message;
                    $log = "$organisation->name " . $message;

                    $log = "User " . $message;
                }
                else
                {
                    //contractor is asking to join a builder
                    $user = $profile;
                    $organisation = Profile::where('member_hash', '=', $hash)->first();
                    if(empty($organisation))
                    {
                        $response['message'] = "That ID doesn't exist";   
                        return json_encode($response);
                        exit; 
                    }
                    $status = "user requested";
                    $message = "requested to join $organisation->type, $organisation->name.  Waiting on $organisation->type's acceptance.";
                    $response['message'] = "You have $message";
                    $log = "User " . $message;
                }
            }
            elseif($profile->type == "hygenist")
            {
                    $organisation = $profile;
                    $user = Profile::where('member_hash', '=', $hash)->first();
                    if(empty($user))
                    {
                        $response['message'] = "That ID doesn't exist";   
                        return json_encode($response);
                        exit; 
                    }
                    $status = "organisation requested";
                    $message = "requested $user->type, $user->name to join.  Waiting on $user->type's acceptance.";
                    $response['message'] = "You have $message";
                    $log = "Organisation " . $message;
            }
            else
            {
                $requesting = Profile::where('member_hash', '=', $hash)->first();

                if($requesting->type == "hygenist")
                {
                    //the hygenist is always the organisation
                    $organisation = $requesting;
                    $user = $profile;
                    $status = "user requested";
                    $message = "requested to join Hygenist group $organisation->name. Waiting on hygenists acceptance.";
                    $response['message'] = "You " . $message;
                    $log = "$organisation->name " . $message;
                    $log = "User " . $message;
                }
                elseif($profile->type == "hygenist")
                {
                    $organisation = $profile;
                    $user = $requesting;
                    $status = "organisation requested";
                    $message = "requested to member $user->name to join your group. Waiting on users acceptance.";
                    $response['message'] = $organisation->name . " " . $message;
                    $log = "$organisation->name " . $message;
                    $log = "Organisation " . $message;
                }
                else
                {
                    $organisation = $profile;
                    $user = $requesting;
                    $status = "organisation requested";
                    $message = "requested to member $user->name to join your group. Waiting on users acceptance.";
                    $response['message'] = $organisation->name . " " . $message;
                    $log = "$organisation->name " . $message;
                    $log = "Organisation " . $message;
                }
                //an organisation is requesting a user join - find the user
                if(empty($user))
                {
                    $response['message'] = "That ID doesn't exist";  
                    return json_encode($response);  
                    exit;
                }
            }

            if($user->id == $organisation->id)
            {
                $response['message'] = "Now thats just silly, you cannot request yourself to become a member of yourself.";
            }
            else
            {
                $check = Membership::where('user_id', '=', $user->id)
                                    ->where('organisation_id', '=', $organisation->id)
                                    ->count();
                if($check == 0)
                {
                    $newRequest = new Membership();

                    $newRequest->user_id = $user->id;
                    $newRequest->organisation_id = $organisation->id;
                    $newRequest->organisation_type = $organisation->type;
                    $newRequest->security_group = 0;
                    $newRequest->membership_status = $status;
                    $newRequest->save();

                    $this->insertLog($standardDisplay['profile']->id, "profiles", $user->id, "created", $log, "INFO");
                    $this->insertLog($standardDisplay['profile']->id, "profiles", $organisation->id, "created", $log, "INFO");

                    $this->submitMembershipTask($newRequest->id, $standardDisplay['profile']->id);
                }
                else
                {
                    $check = Membership::where('user_id', '=', $user->id)
                                    ->where('organisation_id', '=', $organisation->id)
                                    ->where('membership_status', '=', 'inactive')
                                    ->first();
                    if(is_object($check))
                    {
                        $check->exitted = NULL;
                        $check->joined = NULL;
                        $check->membership_status = $status;
                        $check->save();

                        $this->insertLog($standardDisplay['profile']->id, "profiles", $user->id, "created", $log, "INFO");
                        $this->insertLog($standardDisplay['profile']->id, "profiles", $organisation->id, "created", $log, "INFO");

                        $this->submitMembershipTask($check->id, $standardDisplay['profile']->id);
                    }
                }
            }
            return json_encode($response);
        }

        public function submitMembershipTask($membership, $profileID)
        {
            /*
                Function is designed to lookup a new membership request and assign a task to the appropriate person to follow it up
                $membership is the membership id thats just been updated.
                $profileID is the $standardDisplay['profile']->id
            */
            $activityController = new ActivityController();

            $membership = Membership::find($membership);
            
            if($membership->membership_status == "organisation requested")
            {
                //log a task to the user
                //First off, find out what kind of user it is - if its a builder or a contractor, find their primary contact
                $profile = Profile::find($membership->user_id);
                if($profile->type == "user")
                {
                    $assigned = $profile->id;
                }
                else
                {
                    if(!empty($profile->primary_contact))
                    {
                        $assigned = $profile->primary_contact;
                    }
                    else
                    {
                        $assigned = $profile->id;
                    }
                }

                $requesting = $membership->Organisation->name;
            }
            else
            {
                //log a task to the organisation - get their primary contact
                $profile = Profile::find($membership->organisation_id);
                if(!empty($profile->primary_contact))
                {
                    $assigned = $profile->primary_contact;
                }
                else
                {
                    $assigned = $profile->id;
                }

                $requesting = $membership->Profile->name;
                
            }
            
            $activityController->addTask($profileID, "New Membership requested", $requesting . " has requested a new membership.", "To accept, open your profile, go to memberships and accept or reject the request.", $assigned, NULL);

            return 1;
        }

        public function acceptMembership($membership, $status, $profile)
        {
            $standardDisplay = $this->standardDisplay();
            
            $profile = Profile::find($profile);
            $membership = Membership::find($membership);

            if($status == "accept")
            {
                $membership->membership_status = "active";
                $membership->joined = date('Y-m-d');
                $membership->save();
                $action = "accepted";
            }
            else
            {
                $membership->delete();
                $action = "declined";
            }


            if($membership->membership_status == "active")
            {
                //assign the default security group to the membership
                $organisation = Profile::find($membership->organisation_id);
                if($organisation->type == "hygenist")
                {
                    $membership->security_group = 7; //ID of the hygenist worker security group
                }
                elseif($organisation->type == "builder")
                {
                    $membership->security_group = 4; //ID of the builder worker security group
                }
                else
                {
                    $membership->security_group = 3; //ID of the contractor worker security group
                }
                $membership->save();
            }



            if($profile->type == "user")
            {
                $log = "User " . $profile->name . " $action the membership request.";
                $this->insertLog($standardDisplay['profile']->id, "profiles", $membership->user_id, $action, $log, "INFO");
                $this->insertLog($standardDisplay['profile']->id, "profiles", $membership->organisation_id, $action, $log, "INFO");
                $this->checkLicenseTable($membership->organisation_id);

                return $this->editProfile($profile->id);
            }
            elseif($profile->type == "hygenist")
            {
                $log = "Hygenist " . $profile->name . " $action the membership request.";
                $this->insertLog($standardDisplay['profile']->id, "profiles", $membership->user_id, $action, $log, "INFO");
                $this->insertLog($standardDisplay['profile']->id, "profiles", $membership->organisation_id, $action, $log, "INFO");
                $this->checkLicenseTable($membership->organisation_id);

                return $this->editHygenist($profile->id, $profile->provider_type);
            }
            elseif($profile->type == "builder")
            {
                $log = "Builder " . $profile->name . " $action the membership request.";
                $this->insertLog($standardDisplay['profile']->id, "profiles", $membership->user_id, $action, $log, "INFO");
                $this->insertLog($standardDisplay['profile']->id, "profiles", $membership->organisation_id, $action, $log, "INFO");
                $this->checkLicenseTable($membership->organisation_id);

                return $this->editBuilder($profile->id);
            }
            else
            {
                $log = "Contractor " . $profile->name . " $action the membership request.";
                $this->insertLog($standardDisplay['profile']->id, "profiles", $membership->user_id, $action, $log, "INFO");
                $this->insertLog($standardDisplay['profile']->id, "profiles", $membership->organisation_id, $action, $log, "INFO");
                $this->checkLicenseTable($membership->organisation_id);

                return $this->editContractor($profile->id);
            }

            //update previous blank entries to show who this user was working for
            $entries = Actions_Time_Entry::where('active_organisation_id', '=', 0)
                                                ->where('user_id', '=', $membership->user_id)
                                                ->get();
            $membership_organisation_id = NULL;//initializing the variable            
            foreach($entries as $e)
            {
                $e->organisation_id = $membership_organisation_id;
                $e->save();
            }
            $entries = Actions_Controls::where('active_organisation_id', '=', 0)
                                            ->where('user_id', '=', $membership->user_id)
                                            ->get();
            foreach($entries as $e)
            {
                $e->organisation_id = $membership_organisation_id;
                $e->save();
            }
            $entries = Actions_Assessment::where('active_organisation_id', '=', 0)
                                            ->where('user_id', '=', $membership->user_id)
                                            ->get();
            foreach($entries as $e)
            {
                $e->organisation_id = $membership_organisation_id;
                $e->save();
            }



        }

        public function cancelMembership($membership, $profile)
        {
            $standardDisplay = $this->standardDisplay();
            $profile = Profile::find($profile);
            $membership = Membership::find($membership);

            $membership->membership_status = "inactive";
            $membership->security_group = 0;
            $membership->exitted = date('Y-m-d');
            $membership->save();

            $action = "cancelled";

            if($profile->type == "user")
            {
                $log = "User " . $profile->name . " left the membership.";
                $this->insertLog($standardDisplay['profile']->id, "profiles", $membership->user_id, $action, $log, "INFO");
                $this->insertLog($standardDisplay['profile']->id, "profiles", $membership->organisation_id, $action, $log, "INFO");
                return $this->editProfile($profile->id);
            }
            elseif($profile->type == "hygenist")
            {
                $log = "Hygenist " . $profile->name . " cancelled the membership.";
                $this->insertLog($standardDisplay['profile']->id, "profiles", $membership->user_id, $action, $log, "INFO");
                $this->insertLog($standardDisplay['profile']->id, "profiles", $membership->organisation_id, $action, $log, "INFO");
                return $this->editHygenist($profile->id, 0);
            }
            elseif($profile->type == "builder")
            {
                $log = "Builder " . $profile->name . " cancelled the membership.";
                $this->insertLog($standardDisplay['profile']->id, "profiles", $membership->user_id, $action, $log, "INFO");
                $this->insertLog($standardDisplay['profile']->id, "profiles", $membership->organisation_id, $action, $log, "INFO");
                return $this->editBuilder($profile->id);
            }
            else
            {
                $log = "Contractor " . $profile->name . " cancelled the membership.";
                $this->insertLog($standardDisplay['profile']->id, "profiles", $membership->user_id, $action, $log, "INFO");
                $this->insertLog($standardDisplay['profile']->id, "profiles", $membership->organisation_id, $action, $log, "INFO");
                return $this->editContractor($profile->id);
            }
        }

        public function requestActiveMembership($profile)
        {
            if(!is_object($profile))
            {
                $profile = Profile::find($profile);
            }
            //first check to see if the user is a member of a builder, if so, return the builder
            $builderMember = Membership::where('user_id', '=', $profile->id)
                                        ->where('membership_status', '=', 'active')
                                        ->where('organisation_type', '=', 'builder')
                                        ->first();
            if(is_object($builderMember))
            {
                return $builderMember;
            }
            else
            {
                //now check to see if the user is a member of a hygenist, if so, return the hygenist
                $hygenistMember = Membership::where('user_id', '=', $profile->id)
                                                ->where('membership_status', '=', 'active')
                                                ->where('organisation_type', '=', 'hygenist')
                                                ->first();
                if(is_object($hygenistMember))
                {
                    return $hygenistMember;
                }
                else
                {
                    //now check to see if the user is a member of a contractor, if so, return the contractor
                    $contractorMember = Membership::where('user_id', '=', $profile->id)
                                                    ->where('membership_status', '=', 'active')
                                                    ->where('organisation_type', '=', 'contractor')
                                                    ->first();
                    if(is_object($contractorMember))
                    {
                        return $contractorMember;
                    }
                    else
                    {
                        //User has no memberships, return 0.
                        return 0;
                    }
                    
                }
            }
            
        }

        public function sendInvitation(Request $request)
        {
            /*
                This function checks to make sure a user is not already a Nextrack user, if so send them a joining request email (and setup the request)
                if not, send them a join nextrack email
            */
            //first lets see if this user is already a Nextrack user
            $toProfile = Profile::where('email', '=', $request->email)->where('archived', '=', 0)->first();
            $fromProfile = Profile::find($request->profileID);

            $email = new Email();
            $email->send_to = $request->name;
            $email->send_email = $request->email;
            $email->status = "pending";

            if(is_object($toProfile))
            {
                //user is already a Nextrack user, lets create a membership request and send an email
                $this->requestMembership($toProfile->member_hash, $fromProfile->id);
                $subject = $fromProfile->name . " would like you to join them.";
                $content = "We've just received a request from the team at " . $fromProfile->name . " for you to join their organisation.<br><br>";
                $content .= "A request has been logged in your member profile.  All you need to do is either accept or reject the request.<br><br>";
                $content .= "You can get to your membership profile by clicking on the link below<br><br>";

                if($toProfile->type == "user")
                {
                    $link = "https://client.nextrack.tech/editProfile/" . $toProfile->id;
                }
                elseif($toProfile->type == "builder")
                {
                    $link = "https://client.nextrack.tech/editBuilder/" . $toProfile->id;
                }
                elseif($toProfile->type == "contractor")
                {
                    $link = "https://client.nextrack.tech/editContractor/" . $toProfile->id;
                }
                elseif($toProfile->type == "hygienist")
                {
                    $link = "https://client.nextrack.tech/editHygenist/" . $toProfile->id;
                }
                
                if(isset($link))
                {
                    $content .= $link;
                }
                
                $content .= "<br><br>" . $request->notes;

            }
            else
            {
                //the invitation is going to a non-nextrack user
                $subject = $fromProfile->name . " would like you to join them in Nextrack.";
                $content = "We've just received a request from the team at " . $fromProfile->name . " for you to join their organisation in neXtrack.<br><br>";
                $content .= "neXtrack is a hazard and control live monitoring system designed to help keep you healthy and monitor your risk to harmful substances whilst on site.<br><br>";
                $content .= "Please follow the link below to register<br><br>";
                $content .= "https://client.nextrack.tech/register?code=" . urlencode($fromProfile->member_hash) . "<br><br>";
                $content .= "Once you have registered, be sure to let " . $fromProfile->name . " know to approve your request.<br><br>";

                $content .= "<br><br>" . $request->notes;
                
            }

            $email->subject = $subject;
            $email->content = $content;
            $email->save();

            emailSender::dispatch();

            if(isset($request->fromImport))
            {
                return 1;
            }
            else
            {
                if($fromProfile->type == "user")
                {
                    return $this->editProfile($fromProfile->id);
                }
                elseif($fromProfile->type == "builder")
                {
                    return $this->editBuilder($fromProfile->id);
                }
                elseif($fromProfile->type == "contractor")
                {
                    return $this->editContractor($fromProfile->id);
                }
                elseif($fromProfile->type == "hygenist")
                {
                    return $this->editHygenist($fromProfile->id, 0);
                }
            }
        }


    //Permit functions
        public function savePermit(Request $request, $permit)
        {
            $standardDisplay = $this->standardDisplay();

            if(!in_array("users:edit", $standardDisplay['permissions']))
            {
                if($standardDisplay['profile']->super_user == 0)
                {
                    if($standardDisplay['profile']->id != $request->profile)
                    {
                        $index = new HomeController();
                        $alert = "You do not have privileges to do that.";

                        return $index->displayDashboard($alert);
                    }
                }
            }
            
            if($permit == 0)
            {
                $permit = new Permits_Profile();
                $permit->status = "pending approval";
                $permit->profiles_id = $request->profile;
                $action = "created";
            }
            else
            {
                $permit = Permits_Profile::find($permit);
                $permit->status = $request->status;
                $action = "Edited";
            }

            $permit->permits_id = $request->permitType;
            $permit->reference = $request->reference;
            $permit->effective_date = $request->effectiveDate;
            $permit->expiry_date = $request->expiryDate;

            $permit->save();


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
                    $f->module_id = $permit->id;
                    $f->module = "permits";
                    
                    $f->save();
                }
            }

            $this->insertLog($standardDisplay['profile']->id, "profiles", $request->profile, $action, "Permit " . $permit->Permit->name . " " . $action . " on profile ", "INFO");
            $this->insertLog($standardDisplay['profile']->id, "permits", $permit->id, $action, "Permit " . $permit->Permit->name . " " . $action . " on profile ", "INFO");

            $profile = Profile::find($permit->profiles_id);
            //find out who the user is a member of and log a task to approve the compliance entry
            $taskCheck = 0;
            $activityController = new ActivityController();
            $membership = Membership::where('user_id', '=', $profile->id)
                                        ->where('membership_status', '=', 'active')
                                        ->first();
            if(is_object($membership))
            {
                $taskCheck = 1;
                $organisation = Profile::find($membership->organisation_id);
                if(!empty($organisation->primary_contact))
                {
                    $assignedTo = $organisation->primary_contact;
                }
                else
                {
                    $assignedTo = $organisation->id;
                }
                $activityController->addTask($standardDisplay['profile']->id, "New compliance to check", $profile->name . " has entered details for new compliance to be approved.", "To view, open the " . $profile->type . " record for " . $profile->name . " go to the compliance tab and accept or reject the new compliance.  The new compliance will be shown with a ! mark icon.", $assignedTo, NULL);
            }
            if($taskCheck = 0)
            {
                $activityController->addTask($standardDisplay['profile']->id, "New compliance to check", $profile->name . " has entered details for new compliance to be approved.", "To view, open the " . $profile->type . " record for " . $profile->name . " go to the compliance tab and accept or reject the new compliance.  The new compliance will be shown with a ! mark icon.", 8, NULL);
            }
            
            if($profile->type == "user")
            {
                return $this->editProfile($request->profile);
            }
            else
            {
                return $this->editContractor($request->profile);
            }
        }

        public function getPermitTraining($permit)
        {
            $array = array();
            $a = 0;

            $trainings = Permits_Training::where('permits_id', '=', $permit)->get();
            foreach($trainings as $t)
            {
                $array[$a]['trainings_id'] = $t->trainings_id;
                $array[$a]['trainings_name'] = $t->Training->name;
                $a++;
            }


            return json_encode($array);
        }

        public function editPermit($permit)
        {
            $standardDisplay = $this->checkFunctionPermission("users:edit");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }
            
            $permit = Permits_Profile::find($permit);
            $allPermits = Permit::where('archived', '=', 0)->orderBy('name', 'asc')->get();
            $profile = Profile::find($permit->profiles_id);

            $requiredTraining = Permits_Training::where('permits_id', '=', $permit->permits_id)->get();

            $breadcrumbs = Controller::createBreadcrumbs("Users", $profile->name);       
            
            $module = 'permits';
            $moduleID = $permit->id;
            $files = File::where('module', '=', $module)
                                        ->where('module_id', '=', $moduleID)
                                        ->get();
            $logs = $this->retrieveLogs($module, $moduleID);

            $isBuilder = 0;
            $membersOf = Membership::where('user_id', '=', $standardDisplay['profile']->id)
                                    ->where('organisation_type', '=', 'builder')
                                    ->count();
            if($membersOf > 0)
            {
                $isBuilder = 1;
            }

            $forProfile = Profile::find($permit->profiles_id);

            return view('people.editPermit', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'files' => $files,
                'logs' => $logs,
                'permit' => $permit,
                'requiredTraining' => $requiredTraining,
                'allPermits' => $allPermits,
                'profile' => $profile,
                'isBuilder' => $isBuilder,
                'forProfile' => $forProfile,
            ]);
        }



    //Sites functions

        public function addSiteRequirement($permit, $mandatory, $site)
        {
            if($mandatory != 0)
            {
                //check to make sure this requirement doesn't already exist on the site
                $requirement = Permits_Site::where('permits_id', '=', $permit)
                                            ->where('sites_id', '=', $site)
                                            ->first();
                if(empty($requirement))
                {
                    $requirement = new Permits_Site();
                    $requirement->sites_id = $site;
                    $requirement->permits_id = $permit;
                }

                $requirement->mandatory = $mandatory;
                $requirement->save();
            }

            $site = Site::find($site);

            $requirements = $this->getSitePermits($site);

            return json_encode($requirements);
        }

        public function addZoneRequirement($zone, $permit, $mandatory)
        {
            if($mandatory != 0)
            {
                //check to make sure this requirement doesn't already exist on the site
                $requirement = Permits_Zone::where('permits_id', '=', $permit)
                                            ->where('zones_id', '=', $zone)
                                            ->first();
                if(empty($requirement))
                {
                    $requirement = new Permits_Zone();
                    $requirement->zones_id = $zone;
                    $requirement->permits_id = $permit;
                }

                $requirement->mandatory = $mandatory;
                $requirement->save();
            }

            $zone = Sites_Maps_Zone::find($zone);

            $requirements = $this->getZonePermits($zone);

            return json_encode($requirements);
        }
        
        public function sites()
        {
            /*
                Standard page setup
            */
            $alert = NULL;

            return $this->displaySites($alert);
        }

        public function getUsersSites($profile, $status)
        {
            //$profile is the full standardDisplay['profile'] object
            //$status is either "active" or "all" to filter the sites

            if($profile->super_user == 1)
            {
                if($status == "active")
                {
                    $sitesArray = Site::where('status', '=', 'active')
                                        ->where('archived', '=', 0)
                                        ->orderBy('name', 'asc')
                                        ->get();
                }
                else
                {
                    $sitesArray = Site::where('archived', '=', 0)
                                        ->orderBy('name', 'asc')
                                        ->get();
                }
            }
            else
            {
                $sitesArray = array();
                $sitesCheckArray = array();
                $s = 0;

                //find out if the user is a member of a contractor or builder and if so what their permissions are
                $memberships = Membership::where('user_id', '=', $profile->id)
                                            ->where('membership_status', '=', 'active')
                                            ->get();
                if(count($memberships) > 0)
                {
                    foreach($memberships as $member)
                    {
                        $permissions = $this->getSpecificPermissions($member->security_group);
                        if(in_array("sites:view", $permissions) OR in_array("sites:view-all", $permissions))
                        {
                            //if the organisation is a hygenist, get that hygenists sites
                            if($member->Organisation->type == "hygenist")
                            {
                                if($status == "active")
                                {
                                    $sites = Site::where('status', '=', 'active')
                                                        ->where('hygenist_id', '=', $member->organisation_id)
                                                        ->where('archived', '=', 0)
                                                        ->orderBy('name', 'asc')
                                                        ->get();
                                }
                                else
                                {
                                    $sites = Site::where('hygenist_id', '=', $member->organisation_id)
                                                    ->where('archived', '=', 0)
                                                    ->orderBy('name', 'asc')
                                                    ->get();
                                }
                                
                                foreach($sites as $site)
                                {
                                    if(!in_array($site->id, $sitesCheckArray))
                                    {
                                        if(in_array("sites:view-all", $permissions))
                                        {
                                            //user is allowed to see all of this companies sites.
                                            $sitesArray[$s] = $site;
                                            $sitesCheckArray[$s] = $site->id;
                                            $s++;
                                        }
                                        else 
                                        {
                                            //user is allowed to see all sites on which their organisation is the assigned hygenist
                                            if($site->hygenist_id == $member->organisation_id)
                                            {
                                                $sitesArray[$s] = $site;
                                                $sitesCheckArray[$s] = $site->id;
                                                $s++;
                                            }
                                        }
                                    }
                                }
                            }

                            //if the organisation is a builder, get that builders sites
                            if($member->Organisation->type == "builder" OR $member->Organisation->type == "contractor")
                            {
                                if($status == "active")
                                {
                                    $sites = Site::where('status', '=', 'active')
                                                        ->where('builder_id', '=', $member->organisation_id)
                                                        ->where('archived', '=', 0)
                                                        ->orderBy('name', 'asc')
                                                        ->get();
                                }
                                else
                                {
                                    $sites = Site::where('builder_id', '=', $member->organisation_id)
                                                    ->orderBy('name', 'asc')
                                                    ->where('archived', '=', 0)
                                                    ->get();
                                }

                                foreach($sites as $site)
                                {
                                    if(!in_array($site->id, $sitesCheckArray))
                                    {                                   
                                        if(in_array("sites:view-all", $permissions))
                                        {
                                            $sitesArray[$s] = $site;
                                            $sitesCheckArray[$s] = $site->id;
                                            $s++;
                                        }
                                        else 
                                        {
                                            if($site->primary_contact_id == $profile->id)
                                            {
                                                $sitesArray[$s] = $site;
                                                $sitesCheckArray[$s] = $site->id;
                                                $s++;
                                            }
                                        }
                                    }
                                }
                            }

                            //if the organisation is a contractor, find all the builders sites that the contractor has been added to.
                            $contractorSites = Sites_Contractors::where('profile_id', '=', $member->Organisation->id)->get();
                            foreach($contractorSites as $cs)
                            {
                                $site = Site::find($cs->site_id);
                                if(!in_array($site->id, $sitesCheckArray))
                                {
                                    if($status == "active")
                                    {
                                        if($site->status == "active" && $site->archived == 0)
                                        {
                                            $sitesArray[$s] = ""; 
                                            $sitesArray[$s] = $site;   
                                            $sitesCheckArray[$s] = $site->id; 
                                            $s++;
                                        }
                                    }
                                    else
                                    {
                                        if($site->archived == 0)
                                        {
                                            $sitesArray[$s] = $site;  
                                            $sitesCheckArray[$s] = $site->id;  
                                            $s++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // print_r($sitesArray);
            // exit;
            
            return $sitesArray;
        }

        public function displaySites($alert)
        {
            $standardDisplay = $this->checkFunctionPermission("sites:view");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $week_ini = new DateTime("7 days ago");
            $week_end = new DateTime("now");
            $range = $week_ini->format('d/m/Y') . " - " . $week_end->format('d/m/Y');

            $reportController = new ReportController();

            $breadcrumbs = Controller::createBreadcrumbs("Sites", NULL);
            $sitesParticipation = array();

            //check to see if the user can see other users
            $sites = $this->getUsersSites($standardDisplay['profile'], "all");
            $sitesArray = array();
            $sa = 0;
            foreach($sites as $site)
            {
                $sitesArray[$sa] = $site->id;
                $sitesParticipation[$sa]['participation'] = $reportController->buildParticipation($range, $site->id);
                $sitesParticipation[$sa]['site'] = $site->id;
                $sa++;
                
            }

            $pendingSites = array();
            $p = 0;

            $builders = $this->getUserBuilders($standardDisplay['profile']);
            foreach($builders as $builder)
            {
                $rSites = Site::where('relinquish_id', '=', $builder['builder']->id)->get();
                foreach($rSites as $rs)
                {
                    if(!in_array($rs->id, $sitesArray))
                    {
                        $pendingSites[$p] = $rs;
                        $p++;
                    }
                }
            }

            $activeMembership = $this->requestActiveMembership($standardDisplay['profile']);
            
            return view('people.sites', [
                'breadcrumbs' => $breadcrumbs,
                'alert' => $alert,
                'standardDisplay' => $standardDisplay,
                'sites' => $sites,
                'sitesParticipation' => $sitesParticipation,
                'builders' => $builders,
                'pendingSites' => $pendingSites,
                'activeMembership' => $activeMembership,
            ]);
        }

        public function getSitePermits($site)
        {
            $permitsArray = array();
            $pa = 0;

            $permits = Permits_Site::where('sites_id', '=', $site->id)->get();
            $today = date('Y-m-d');

            //get a list of all people who have been on this site
            $people = array();
            $p = 0;
            $assessments = Actions_Assessment::where('site_id', '=', $site->id)->distinct('user_id')->get();
            foreach($assessments as $ass)
            {
                if(!in_array($ass->user_id, $people))
                {
                    $people[$p] = $ass->user_id;
                    $p++;
                }
            }
            $times = Actions_Time_Entry::where('site_id', '=', $site->id)->distinct('user_id')->get();
            if(isset($ass))
            {
                foreach($times as $time)
                {
                    if(!in_array($time->user_id, $people))
                    {
                        $people[$p] = $ass->user_id;
                        $p++;
                    }
                }
            }

            //now we have a complete list of all the people that have visited the site, lets go through the site permit requirements and make sure everyone
            //on site / has been on site meets the requirements
            foreach($permits as $pr)
            {
                $prCount = 0;
                $ppl = "";

                foreach($people as $person)
                {
                    $profilePermit = Permits_Profile::where('permits_id', '=', $pr->permits_id)->where('profiles_id', '=', $person)->where('status', '=', 'approved')->get();
                    if(count($profilePermit) == 0)
                    {
                        $prCount++;
                        $plu = Profile::find($person);
                        $ppl .= $plu->name . ", ";
                    }
                    else
                    {
                        foreach($profilePermit as $pp)
                        {
                            //they do have the permit - lets quickly make sure that it hasn't expired
                            if($today > $pp->expiry_date)
                            {
                                $prCount++;
                                $plu = Profile::find($person);
                                $ppl .= $plu->name . ", ";
                            }
                        }
                        
                    }
                }
                $permitsArray[$pa]['pid'] = $pr->id;
                $permitsArray[$pa]['mandatory'] = $pr->mandatory;
                $permitsArray[$pa]['count'] = $prCount;
                $permitsArray[$pa]['people'] = $ppl;

                if(is_object($pr->Permit))
                {
                    $permitsArray[$pa]['id'] = $pr->Permit->id;
                    $permitsArray[$pa]['name'] = $pr->Permit->name;
                    $permitsArray[$pa]['type'] = $pr->Permit->Permits_Type->name;
                }
                else
                {
                    $permitsArray[$pa]['id'] = "-";
                    $permitsArray[$pa]['name'] = "-";
                    $permitsArray[$pa]['type'] = "-";
                }

                $pa++;
            }

            return $permitsArray;
        }

        public function getSiteParticipation($site)
        {
            $week_ini = new DateTime("7 days ago");
            $week_end = new DateTime("now");
            $range = $week_ini->format('d/m/Y') . " - " . $week_end->format('d/m/Y');

            $reportController = new ReportController();

            $p = $reportController->buildParticipation($range, $site);

            $sitesParticipation['participation'] = $p['totalParticipation'];

            return json_encode($sitesParticipation);                
        }

        public function getZonePermits($zone)
        {
            $permitsArray = array();
            $pa = 0;

            $permits = Permits_Zone::where('zones_id', '=', $zone->id)->get();
            $today = date('Y-m-d');

            //get a list of all people who have been on this site
            $people = array();
            $p = 0;
            $assessments = Actions_Assessment::where('zone_id', '=', $zone->id)->distinct('user_id')->get();
            foreach($assessments as $ass)
            {
                if(!in_array($ass->user_id, $people))
                {
                    $people[$p] = $ass->user_id;
                    $p++;
                }
            }
            $times = Actions_Time_Entry::where('zone_id', '=', $zone->id)->distinct('user_id')->get();
            foreach($times as $time)
            {
                if(is_object($time))
                {
                    if(!in_array($time->user_id, $people))
                    {
                        $people[$p] = $time->user_id;
                        $p++;
                    }
                }
            }

            //now we have a complete list of all the people that have visited the site, lets go through the site permit requirements and make sure everyone
            //on site / has been on site meets the requirements
            foreach($permits as $pr)
            {
                $prCount = 0;
                $ppl = "";

                foreach($people as $person)
                {
                    $profilePermit = Permits_Profile::where('permits_id', '=', $pr->permits_id)->where('profiles_id', '=', $person)->where('status', '=', 'approved')->get();
                    if(count($profilePermit) == 0)
                    {
                        $prCount++;
                        $plu = Profile::find($person);
                        $ppl .= $plu->name . ", ";
                    }
                    else
                    {
                        foreach($profilePermit as $pp)
                        {
                            //they do have the permit - lets quickly make sure that it hasn't expired
                            if($today > $pp->expiry_date)
                            {
                                $prCount++;
                                $plu = Profile::find($person);
                                $ppl .= $plu->name . ", ";
                            }
                        }
                        
                    }
                }
                if(is_object($pr->Permit))
                {
                    $permitsArray[$pa]['pid'] = $pr->id;
                    $permitsArray[$pa]['id'] = $pr->Permit->id;
                    $permitsArray[$pa]['name'] = $pr->Permit->name;
                    $permitsArray[$pa]['type'] = $pr->Permit->Permits_Type->name;
                    $permitsArray[$pa]['mandatory'] = $pr->mandatory;
                    $permitsArray[$pa]['count'] = $prCount;
                    $permitsArray[$pa]['people'] = $ppl;
                }
                $pa++;
            }

            return $permitsArray;
        }

        public function getMapControls($map)
        {
            //Function returns all the controls on a map - $map is the full Sites_Map object
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

                $controlsArray = array();
                $c = 0;
                
                $controls = Control::where('current_site', '=', $map->site_id)
                                        ->where('controls_type_id', '=', $type->id)
                                        ->get();
                foreach($controls as $control)
                {
                    //check to see if its in the right map
                    $lastTransfer = Controls_Sites::where('control_id', '=', $control->id)->orderBy('id', 'desc')->first();
                    if($lastTransfer->to_map_id == $map->id)
                    {
                        $valuesArray = array();
                        $v = 0;
                        foreach($fields as $field)
                        {
                            $value = Controls_Field::where('control_id', '=', $control->id)   
                                                    ->where('control_field_id', '=', $field->id)
                                                    ->first();
                            $valuesArray[$v]['value'] = $value->value;
                            $valuesArray[$v]['field'] = $field->name;
                            $v++;
                            
                        }
                        $controlsArray[$c]['control'] = $control;
                        $controlsArray[$c]['fieldValues'] = $valuesArray;
                        $c++;
                    }
                }
                if($c > 0)
                {
                    $typeArray[$t]['fields'] = $fieldsArray;
                    $typeArray[$t]['controls'] = $controlsArray;
                    $typeArray[$t]['type'] = $type;
                    $t++;

                    unset($controlsArray);
                    unset($fieldsArray);
                    unset($type);
                }
            }

            return $typeArray;
        }

        public function getSiteMaps($site)
        {
            $maps = Sites_Map::where('site_id', '=', $site)->where('archived', '=', 0)->orderBy('name', 'asc')->get();
            $mapsArray = array();
            $m = 0;
            
            foreach($maps as $map)
            {   
                $controlsArray = $this->getMapControls($map);

                $zones = Sites_Maps_Zone::where('map_id', '=', $map->id)->where('archived', '=', 0)->get();

                $mapsArray[$m]['map'] = $map;
                $mapsArray[$m]['zones'] = count($zones);
                $mapsArray[$m]['controls'] = count($controlsArray);
                $mapsArray[$m]['controlsArray'] = $controlsArray;
                $m++;
            }

            return $mapsArray;
        }

        public function addSiteMap($site, $map)
        {
            $newMap = new Sites_Map();
            $newMap->name = $map;
            $newMap->site_id = $site;
            $newMap->archived = 0;
            $newMap->save();

            return json_encode($this->getSiteMaps($site));
        }

        public function saveMap(Request $request, $map)
        {
            $standardDisplay = $this->standardDisplay();

            $map = Sites_Map::find($map);
            $map->name = $request->mapName;

            if(!empty($request->file('mapImage')))
            {
                $image = $request->file('mapImage')->store('images/maps', 'public');
                $map->image = $image;

                $attr = getimagesize('storage/' . $map->image);
                list($width, $height) = $attr;

                $map->height = $height;
                $map->width = $width;
                
            }
            else
            {
                $map->height = 1;
                $map->width = 1;
            }

            $map->save();

            $this->insertLog($standardDisplay['profile']->id, "sites", $map->site_id, "Edited", "Map " . $map->name . " updated.", "INFO");

            return $this->editSite($map->site_id);
        }

        public function addSiteMapZone($site, $zone, $map)
        {
            $standardDisplay = $this->standardDisplay();

            $newZone = new Sites_Maps_Zone();
            $newZone->name = $zone;
            $newZone->map_id = $map;
            $newZone->site_id = $site;
            $newZone->archived = 0;
            
            $newZone->save();

            //add all ther permits from the site for the zone
            $permits = Permits_Site::where('sites_id', '=', $site)->get();
            foreach($permits as $permit)
            {
                $new = new Permits_Zone();
                $new->permits_id = $permit->permits_id;
                $new->zones_id = $newZone->id;
                $new->mandatory = $permit->mandatory;
                
                $new->save();
            }

            $this->insertLog($standardDisplay['profile']->id, "zones", $newZone->id, "Created", "Zone " . $newZone->name . " created.", "INFO");

            return json_encode($this->getSiteZones($site));
        }

        public function getSiteZones($site)
        {
            $zones = Sites_Maps_Zone::where('site_id', '=', $site)->where('archived', '=', 0)->orderBy('name', 'asc')->get();
            $zonesArray = array();
            $z = 0;
            
            foreach($zones as $zone)
            {
                $map = Sites_Map::find($zone->map_id);

                $i = 0;
                $siteControls = Control::where('current_site', '=', $site)->get();
                foreach($siteControls as $control)
                {
                    $controlSite = Controls_Sites::where('control_id', '=', $control->id)
                                                    ->orderby('id', 'desc')
                                                    ->first();
                    if($controlSite->to_zone_id == $zone->id)
                    {
                        $i++;
                    }
                }

                $zonesArray[$z]['zone'] = $zone->name;
                $zonesArray[$z]['id'] = $zone->id;
                $zonesArray[$z]['map'] = $map->name;
                $zonesArray[$z]['controls'] = $i;
                $z++;
            }

            return $zonesArray;
        }
        public function getSiteZonesJson($site)
        {
            $array = $this->getSiteZones($site);

            return json_encode($array);
        }

        public function getSiteControls($site)
        {
            //site is a full site object

            //first off get all the types
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

                /*foreach($type->Videos as $video)
                {
                    
                }
                */

                $controlsArray = array();
                $c = 0;
                $controls = Control::where('current_site', '=', $site->id)
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
                            $valuesArray[$v]['field'] = $field->name;
                            $v++;
                        }
                    }
                    //now get the Map and Zone this control is in.
                    $where = Controls_Sites::where('control_id', '=', $control->id)->orderBy('id', 'desc')->first();
                    if($where->to_map_id > 0)
                    {
                        $map = Sites_Map::find($where->to_map_id);
                        $map = $map->name;
                    }
                    else
                    {
                        $map = "-";
                    }

                    if($where->to_zone_id > 0)
                    {
                        $zone = Sites_Maps_Zone::find($where->to_zone_id);
                        $zone = $zone->name;
                    }
                    else
                    {
                        $zone = "-";
                    }

                    $controlsArray[$c]['control'] = $control;
                    $controlsArray[$c]['fieldValues'] = $valuesArray;
                    $controlsArray[$c]['map'] = $map;
                    $controlsArray[$c]['zone'] = $zone;

                    unset($control);
                    unset($valuesArray);
                    unset($map);
                    unset($zone);
                    

                    $c++;
                }
                if($c > 0)
                {
                    $typeArray[$t]['fields'] = $fieldsArray;
                    $typeArray[$t]['controls'] = $controlsArray;
                    $typeArray[$t]['type'] = $type;
                    $t++;
                }
            }

            return $typeArray;
        }

        public function siteLogon($site)
        {
            $standardDisplay = Controller::standardDisplay();
            $breadcrumbs = Controller::createBreadcrumbs("Sites", "Log on");  
            $site = Site::find($site);
            $allowed = 0;
            $logOff = 0;
            
            $member = "not ok";
            $membership = Membership::where('user_id', '=', $standardDisplay['profile']->id)->where('membership_status', '=', 'active')->first();
            if(is_object($membership))
            {
                $member = "ok";
                $allowed = 1;
            }
            if($allowed == 0)
            {
                //find out how old this user is
                $age = $this->calcAge($standardDisplay['profile']->created_at);
                if(4 > $age)
                {
                    $allowed = 1;
                }
            }
            $loSite = 0;
            $logon = 0;

            if($allowed == 1)
            {
                $timestamp = time();
                $date = date('Y-m-d');
                $logOff = 0;

                //first check to make sure the user is not already logged onto another site
                $logoff = Sites_Logon::where('profile_id', '=', $standardDisplay['profile']->id)
                                        ->whereNull('time_out')
                                        ->get();
                foreach($logoff as $lo)
                {
                    $logOff = $lo;
                }

                $logon = new Sites_Logon();
                $logon->site_id = $site->id;
                $logon->profile_id = $standardDisplay['profile']->id;
                $logon->time_in = $timestamp;
                $logon->date = $date;
                $logon->save();
            }

            if(is_object($logOff))
            {
                return view('people.siteLogOffCheck', [
                    'breadcrumbs' => $breadcrumbs,
                    'standardDisplay' => $standardDisplay,
                    'site' => $site,
                    'logon' => $logon,
                    'allowed' => $allowed,
                    'member' => $member,
                    'logOff' => $lo,
                ]);
            }
            else
            {
                return view('people.siteLogon', [
                    'breadcrumbs' => $breadcrumbs,
                    'standardDisplay' => $standardDisplay,
                    'site' => $site,
                    'logon' => $logon,
                    'allowed' => $allowed,
                    'member' => $member,
                ]);
            }

            

        }

        public function siteLogoff()
        {
            $standardDisplay = Controller::standardDisplay();
            $breadcrumbs = Controller::createBreadcrumbs("Sites", "Log off");  
            $site = NULL;
            $lo = NULL;

            $timestamp = time();

            //go through all sites (should only be one) that the user is currently logged into and sign them off
            $logoff = Sites_Logon::where('profile_id', '=', $standardDisplay['profile']->id)
                                    ->whereNull('time_out')
                                    ->get();
            foreach($logoff as $lo)
            {
                $lo->time_out = $timestamp;
                $lo->save();

                $site = Site::find($lo->site_id);
            }

            $standardDisplay = Controller::standardDisplay();

            return view('people.siteLogoff', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'site' => $site,
                'logoff' => $lo,
            ]);

        }

        public function laterSiteLogoff(Request $request)
        {
            $logOff = Sites_Logon::find($request->logOff);
            $timestring = $logOff->date->format('Y-m-d') . " " . $request->hour . ":" . $request->minute;

            $timestamp = strtotime($timestring);

            $logOff->time_out = $timestamp;
            $logOff->save();

            $standardDisplay = Controller::standardDisplay();
            $breadcrumbs = Controller::createBreadcrumbs("Sites", "Log on");

            return view('people.saveLogOff', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
            ]);

        }

        public function getZoneControls($zone)
        {
            //$zone is the full zone object

            //first off get all the types
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

                $controlsArray = array();
                $c = 0;
                $controls = Control::where('current_site', '=', $zone->Site->id)
                                    ->where('controls_type_id', '=', $type->id)
                                    ->where('archived', '=', 0)
                                    ->get();
                foreach($controls as $control)
                {
                    //check to see if its in the right zone
                    $lastTransfer = Controls_Sites::where('control_id', '=', $control->id)->orderBy('id', 'desc')->first();
                    if($lastTransfer->to_zone_id == $zone->id)
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
                        $controlsArray[$c]['control'] = $control;
                        $controlsArray[$c]['fieldValues'] = $valuesArray;
                        $c++;   
                    }
                }
                if(count($controlsArray) > 0)
                {
                    $typeArray[$t]['fields'] = $fieldsArray;
                    $typeArray[$t]['controls'] = $controlsArray;
                    $typeArray[$t]['type'] = $type;
                    $t++;
                }

                unset($controlsArray);
                unset($fieldsArray);
            }

            return $typeArray;
        }

        public function updateMapCoords()
        {
            $json = utf8_encode(stripslashes($_GET['coords']));
            $length = strlen($json);
            $string = substr($json, 1, $length-2);

            $json = json_decode($string, false);
            
            foreach($json->children as $stage)
            {
                foreach($stage->children as $layer)
                {
                    $control = Control::find($layer->attrs->id);
                    if(is_object($control))
                    {
                        
                        echo "<br>$control->id";
                        echo " X is ";
                        print_r($layer->attrs->x);
                        echo " Y is ";
                        print_r($layer->attrs->y);
                        
                        $control->x = round($layer->attrs->x);
                        $control->y = round($layer->attrs->y);
                        $control->save();
                    }
                }
            }
            $array = array();
            $array['0'] = "Co-ordinates saved.";

            return json_encode($array);
        }

        public function getHazardControls($hazard)
        {
            //$hazard is the full zone hazard object

            //first off get all the types
            $typeArray = array();
            $t = 0;
            $types = Controls_Type::where('archived', '=', 0)->orderBy('name', 'asc')->get();
            foreach($types as $type)
            {
                $fieldsArray = array();
                $f = 0;

                $fields = Controls_Type_Field::where('controls_type_id', '=', $type->id)->get();
                foreach($fields as $field)
                {
                    $fieldsArray[$f] = $field->name;
                    $f++;
                }

                $controlsArray = array();
                $c = 0;
                $controls = Control::where('current_site', '=', $hazard->Zone->Site->id)
                                        ->where('controls_type_id', '=', $type->id)
                                        ->where('archived', '=', 0)
                                        ->get();

                foreach($controls as $control)
                {
                    //check to see if its in the right hazard
                    $lastTransfer = Controls_Sites::where('control_id', '=', $control->id)->orderBy('id', 'desc')->first();
                    if($lastTransfer->to_hazard_id == $hazard->id)
                    {
                        $valuesArray = array();
                        $v = 0;
                        foreach($fields as $field)
                        {
                            $value = Controls_Field::where('control_id', '=', $control->id)   
                                                    ->where('control_field_id', '=', $field->id)
                                                    ->first();
                            $valuesArray[$v]['value'] = $value->value;
                            $valuesArray[$v]['field'] = $field->name;
                            $v++;
                            
                        }
                        $controlsArray[$c]['control'] = $control;
                        $controlsArray[$c]['fieldValues'] = $valuesArray;
                        $c++;
                    }
                }
                if(count($controlsArray) > 0)
                {
                    $typeArray[$t]['fields'] = $fieldsArray;
                    $typeArray[$t]['controls'] = $controlsArray;
                    $typeArray[$t]['type'] = $type;
                    $t++;
                }
            }

            return $typeArray;
        }

        public function getSiteWorkders($site, $standardDisplay)
        {
            //print_r($standardDisplay['profile']);
            //exit;
            $sitesPermits = Permits_Site::where('sites_id', '=', $site->id)->get();
            $today = date('Y-m-d');
            
            $workersArray = array();
            $wa = 0;
            $membershipArray = array();
            $m = 0;

            $memberOfs = Membership::where('user_id', '=', $standardDisplay['profile']->id)->get();
            foreach($memberOfs as $mo)
            {
                $membershipArray[$m] = $mo->organisation_id;
                $m++;
            }
            
            $siteWorkers = Sites_Profile::where('site_id', '=', $site->id)->get();
            foreach($siteWorkers as $sw)
            {
                //check to see if they have any issues
                $issues = array();
                $i = 0;

                foreach($sitesPermits as $sp)
                {
                    $profilePermit = Permits_Profile::where('permits_id', '=', $sp->permits_id)
                                                    ->where('profiles_id', '=', $sw->profile_id)
                                                    ->where('status', '=', 'approved')
                                                    ->get();
                    if(count($profilePermit) == 0)
                    {
                        $issues[$i] = $sp->Permit->name;
                        $i++;
                    }
                    else
                    {
                        foreach($profilePermit as $pp)
                        {
                            //they do have the permit - lets quickly make sure that it hasn't expired
                            if($today > $pp->expiry_date)
                            {
                                $issues[$i] = $pp->name;
                                $i++;
                            }
                        }
                        
                    }
                }
                $sw->issues = $i;
                $sw->save();

                $same = 0;
                $workerMember = Membership::where('user_id', '=', $sw->profile_id)->get();
                foreach($workerMember as $wm)
                {
                    if(in_array($wm->organisation_id, $membershipArray))
                    {
                        $same = 1;
                    }
                }

                //get their times
                $times = Actions_Time_Entry::where('user_id', '=', $sw->profile_id)->get();
                $tTime = 0;
                foreach($times as $time)
                {
                    $ct = $this->calcTime($time->id, "hours");
                    $tTime = $tTime + $ct;
                }           

                //get the total number of activities
                $cActivities = Actions_Assessment::where('user_id', '=', $sw->profile_id)->count();

                $mb = Membership::where('user_id', '=', $sw->profile_id)->whereNull('exitted')->where('membership_status', '=', 'active')->first();
                $organisation = $mb->Organisation->name;

                if(empty($organisation))
                {
                    $organisation = "Currently none";
                }

                $workersArray[$wa]['worker'] = $sw->Profile;
                $workersArray[$wa]['organisation'] = $organisation;
                $workersArray[$wa]['same'] = $same;
                $workersArray[$wa]['issues'] = $issues;
                $workersArray[$wa]['hours'] = $tTime;
                $workersArray[$wa]['activities'] = $cActivities;
                $wa++;
            }

            return $workersArray;
        }

        public function removeWorker($site, $worker)
        {
            $workers = Sites_Contractors::where('site_id', '=', $site)
                                        ->where('profile_id', '=', $worker)
                                        ->get();
            foreach($workers as $w)
            {
                $w->delete();
            }

            return $this->editSite($site);
        }

        public function deleteMap($map)
        {
            $map = Sites_Map::find($map);
            $site = $map->site_id;
            $map->archived = 1;
            $map->save();

            return $this->editSite($site);
        }

        public function deleteZone($zone)
        {
            $zone = Sites_Maps_Zone::find($zone);
            $zone->archived = 1;
            $zone->save();

            $site = $zone->site_id;

            return $this->editSite($site);
        }

        public function addContractorToSite($site, $contractor)
        {
            //check first to make sure its not already there
            $check = Sites_Contractors::where('site_id', '=', $site)
                                        ->where('profile_id', '=', $contractor)
                                        ->count();
            if($check == 0)
            {
                $new = new Sites_Contractors();
                $new->site_id = $site;
                $new->profile_id = $contractor;
                $new->save();
            }

            //now we need to check all the zones to make sure all of that contractors trades hazards are on there.
            $trades = Profiles_Trade::where('profiles_id', '=', $contractor)->get();
            foreach($trades as $trade)
            {
                //get all the trades hazards
                $hazards = Hazards_Trades::where('trade_id', '=', $trade->trades_id)->get();
                foreach($hazards as $hazard)
                {
                    //check each zone to make sure that the hazard is on there
                    $zones = Sites_Maps_Zone::where('site_id', '=', $site)->get();
                    foreach($zones as $zone)
                    {

                        $checkHazard = Sites_Maps_Zones_Hazard::where('zone_id', '=', $zone->id)
                                                                ->where('hazard_id', '=', $hazard->id)
                                                                ->count();
                        if($checkHazard == 0)
                        {
                            $newHazard = new Sites_Maps_Zones_Hazard();
                            $newHazard->zone_id = $zone->id;
                            $newHazard->hazard_id = $hazard->hazard_id;
                            $newHazard->archived = 0;
                            $newHazard->save();
                        }
                    }
                }
            }

            $array = array();
            $contractors = Sites_Contractors::where('site_id', '=', $site)->get();
            $a = 0;
            foreach($contractors as $c)
            {
                $trades = "";
                foreach($c->Profile->Profiles_Trade as $profileTrade)
                {
                    $trades .= $profileTrade->Trade->name . ", ";
                }

                $array[$a]['id'] = $c->Profile->id;
                $array[$a]['name'] = $c->Profile->name;
                $array[$a]['workers'] = count($c->Profile->Membership);
                $array[$a]['trades'] = $trades;
                $array[$a]['phone'] = $c->Profile->phone;

                $a++;
            }

            return json_encode($array);
        }

        public function getSiteZoneDashboard($type, $moduleID)
        {
            /*
                Function collates the dashboard for the site or a zone (defined by type)
                If there are no widgets on the dashboard, the system will attempt to retrieve a default dashboard
            */

            $dashboard = Dashboard::where('type', '=', $type)->where('module_id', '=', $moduleID)->get();
            $dashboardArray = array();
            $d = 0;

            if(count($dashboard) > 0)
            {

                foreach($dashboard as $db)
                {
                    $dashboardArray[$d]['id'] = "id_" . $db->id; 
                    $dashboardArray[$d]['col'] = $db->col;
                    $dashboardArray[$d]['row'] = $db->row;
                    $dashboardArray[$d]['size_x'] = $db->size_x;
                    $dashboardArray[$d]['size_y'] = $db->size_y;
                    $dashboardArray[$d]['widget'] = $db->widget;
                    $dashboardArray[$d]['content'] = $db->content;

                    $d++;
                }
            }
            else 
            {
                if($type == "site")    
                {
                    $site = Site::where('name', '=', 'Dashboard site')->first();
                    if(is_object($site))
                    {
                        $dashboard = Dashboard::where('type', '=', $type)->where('module_id', '=', $site->id)->get();
                        foreach($dashboard as $db)
                        {
                            $dashboardArray[$d]['id'] = "id_" . $db->id; 
                            $dashboardArray[$d]['col'] = $db->col;
                            $dashboardArray[$d]['row'] = $db->row;
                            $dashboardArray[$d]['size_x'] = $db->size_x;
                            $dashboardArray[$d]['size_y'] = $db->size_y;
                            $dashboardArray[$d]['widget'] = $db->widget;
                            $dashboardArray[$d]['content'] = $db->content;

                            $d++;
                        }
                    }
                }
                else
                {
                    $zone = Sites_Maps_Zone::where('name', '=', 'Dashboard zone')->first();
                    if(is_object($zone))
                    {
                        $dashboard = Dashboard::where('type', '=', $type)->where('module_id', '=', $zone->id)->get();
                        foreach($dashboard as $db)
                        {
                            $dashboardArray[$d]['id'] = "id_" . $db->id; 
                            $dashboardArray[$d]['col'] = $db->col;
                            $dashboardArray[$d]['row'] = $db->row;
                            $dashboardArray[$d]['size_x'] = $db->size_x;
                            $dashboardArray[$d]['size_y'] = $db->size_y;
                            $dashboardArray[$d]['widget'] = $db->widget;
                            $dashboardArray[$d]['content'] = $db->content;

                            $d++;
                        }
                    }
                }
            }

            return $dashboardArray;
        }

        public function checkReports($site)
        {
            $checkActivities = Sites_Reports::where('report_name', '=', 'activities')->where('site_id', '=', $site)->count();
            if($checkActivities == 0)
            {
                $new = new Sites_Reports();
                $new->report_name = 'activities';
                $new->site_id = $site;

            }
            
        }

        public function editSite($site)
        {
            $standardDisplay = $this->checkFunctionPermission("sites:edit");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            if($standardDisplay['profile']->super_user == 0 && $site > 0)
            {
                $allSites = $this->getUsersSites($standardDisplay['profile'], 'all');
                $siteChecker= 0;
                foreach($allSites as $thisSite)
                {
                    if($thisSite->id == $site)
                    {
                        $siteChecker = 1;
                    }
                }
                if($siteChecker != 1)
                {
                    $index = new HomeController();
                    $alert = "You do not have privileges to do that.";

                    return $index->displayDashboard($alert);
                }
            }          
            
            if($site == 0)
            {
                $site = new Site();
                $site->id = 0;
                $site->name = "New";
                $site->status = "active";
            }
            else
            {
                $site = Site::find($site);
                $this->checkReports($site->id);
            }

            $breadcrumbs = Controller::createBreadcrumbs("Sites", $site->name);       
            
            $module = 'sites';
            $moduleID = $site->id;
            $files = File::where('module', '=', $module)
                                        ->where('module_id', '=', $moduleID)
                                        ->get();
            $logs = $this->retrieveLogs($module, $moduleID);

            $permits = $this->getSitePermits($site);
            $allPermits = Permit::where('archived', '=', 0)->orderBy('name', 'asc')->get();
            $workers = $this->getSiteWorkders($site, $standardDisplay);
            $contractors = Sites_Contractors::where('site_id', '=', $site->id)->get();
            foreach($contractors as $contractor)
            {
                if(!is_object($contractor->Profile))
                {
                    $contractor->delete();
                }
            }
            
            $taskArray = array();
            $ta = 0;
            $tasks = Task::where('site_id', '=', $site->id)->where('archived', '=', 0)->get();
            foreach($tasks as $task)
            {
                $taskArray[$ta]['id'] = $task->id;
                $taskArray[$ta]['subject'] = $task->subject;
                if($task->assigned_id > 0)
                {
                    $taskArray[$ta]['assigned'] = $this->checkUserVisibility($task->assigned_id);
                }
                else
                {
                    $taskArray[$ta]['assigned'] = "-";
                }
                $taskArray[$ta]['status'] = $task->status;
                $taskArray[$ta]['priority'] = $task->priority;
                $taskArray[$ta]['progress'] = $task->progress;
                $ta++;
            }
            
            
            $scArray = array();
            $allContractorsArray = array();
            $sc = 0;
            $ac = 0;
            
            $memberships = Membership::where('organisation_id', '=', $site->builder_id)
                                        ->where('membership_status', '=', 'active')
                                        ->get();
            foreach($memberships as $member)
            {
                $memPro = Profile::find($member->user_id);
                // if($memPro->type == "contractor" && $memPro->archived == 0)
                // {
                //     $allContractorsArray[$ac] = $memPro;
                //     $ac++;
                // }
                //Add a check memPro
                if(is_object($memPro)){
                    if($memPro->type == "contractor" && $memPro->archived == 0)
                    {
                        $allContractorsArray[$ac] = $memPro;
                        $ac++;
                    }                  
                }
            }
            

            $headings = array();

            $hygenists = $this->getUserProviders($standardDisplay['profile'], 0);

            $time = 0;
            $timeEntries = Actions_Time_Entry::where('site_id', '=', $site->id)->get();
            foreach($timeEntries as $te)
            {
                $time = $time + $this->calcTime($te->id, "hours");
                //echo $te->id . " -> " . $this->calcTime($te->id, "hours") . "->" . $time . "<br>";
            }
            //exit;

            $contactsArray = array();
            $c = 0;
            $contacts = Membership::where('organisation_id', '=', $site->builder_id)
                                    ->where('membership_status', '=', 'active')
                                    ->get();
            foreach($contacts as $contact)
            {
                if($contact->Profile->type == "user")
                {
                    $contactsArray[$c] = $contact;
                    $c++;
                }
            }

            $timeEntries = Actions_Time_Entry::where('site_id', '=', $site->id)->get();
            $assessments = Actions_Assessment::where('site_id', '=', $site->id)->get();
            
            if($site->id > 0)
            {
                $controls = $this->getSiteControls($site);
                $maps = $this->getSiteMaps($site->id);
                $zones = $this->getSiteZones($site->id);
                if($site->builder_id > 0)
                {
                    $headings[0] = $site->Builder->name;
                }
                else
                {
                    $headings[0] = "Unknown";
                }                
            }
            else
            {
                $headings[0] = "Unknown";
                $controls = array();
                $maps = array();
                $zones = array();
            }
            $headings[1] = $site->status;
            $headings[2] = $time;
            
            $dashboardType = "site";
            $moduleID = $site->id;
            $widgetOptions = $this->getWidgetOptions($dashboardType, $moduleID);
            $dashboardArray = $this->getSiteZoneDashboard($dashboardType, $moduleID);
            

            $qrAddress = env("APP_URL", "https://client.nextrack.tech/") . "/site/logon/" . $site->id;

            $logons = array();
            $l = 0;

            foreach($site->Sites_Logon as $logon)
            {
                $logons[$l]['logon'] = $logon;
                $logons[$l]['name'] = $this->checkUserVisibility($logon->profile_id);
                $logons[$l]['member'] = $this->requestActiveMembership($logon->profile);
                $l++;
            }

            $controlOrderTypes = Controls_Type::where('archived', '=', 0)->orderBy('name', 'asc')->get();
            $sites = $this->getUsersSites($standardDisplay['profile'], "active");

            $devices = array();
            $d = 0;
            foreach($controls as $type)
            {
                foreach($type['controls'] as $control)
                {
                    //get all the devices on this control
                    $tbDevices = Thingsboards_Device::where('control_id', '=', $control['control']->id)->get();
                    foreach($tbDevices as $device)
                    {
                        $devices[$d]['device'] = $device;
                        $devices[$d]['site'] = $site;
                        $devices[$d]['control'] = $control['control'];
                        $d++;
                    }
                }
            }
            
            return view('people.editSite', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'files' => $files,
                'logs' => $logs,
                'dashboardArray' => $dashboardArray,
                'dashboardType' => $dashboardType,
                'moduleID' => $moduleID,
                'headings' => $headings,
                'site' => $site,
                'permits' => $permits,
                'allPermits' => $allPermits,
                'hygenists' => $hygenists,
                'contacts' => $contactsArray,
                'timeEntries' => $timeEntries,
                'assessments' => $assessments,
                'workers' => $workers,
                'controls' => $controls,
                'maps' => $maps,
                'zones' => $zones,
                'contractors' => $contractors,
                'allContractors' => $allContractorsArray,
                'widgetOptions' => $widgetOptions,
                'tasks' => $taskArray,
                'qrAddress' => $qrAddress,
                'logons' => $logons,
                'controlOrderTypes' => $controlOrderTypes,
                'sites' => $sites,
                'devices' => $devices,
            ]);
        }

        public function saveSite(Request $request, $site)
        {
            $standardDisplay = $this->checkFunctionPermission("sites:edit");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            
            if($site == 0)
            {
                $site = new Site();
                $site->archived = 0;
                $action = "created";
            
                //find out what builder this user is working for
                $builder = Membership::where('user_id', '=', $standardDisplay['profile']->id)
                                        ->where('organisation_type', '=', 'builder')
                                        ->where('membership_status', '=', 'active')
                                        ->first();
                if(is_object($builder))
                {
                    $site->builder_id = $builder->organisation_id;
                }
                else
                {
                    //see if they are a member of a contractor
                    $cont = Membership::where('user_id', '=', $standardDisplay['profile']->id)
                                        ->where('organisation_type', '=', 'contractor')
                                        ->where('membership_status', '=', 'active')
                                        ->first();
                    if(is_object($cont))
                    {
                        $site->builder_id = $cont->organisation_id;
                    }
                    else
                    {
                        $site->builder_id = 0;
                    }
                }

                $site->status = "active";
            }
            else
            {
                $site = Site::find($site);
                $action = "Edited";

                if($site->builder_id == 0)
                {
                    //find out what builder this user is working for
                    $builder = Membership::where('user_id', '=', $standardDisplay['profile']->id)
                                            ->where('organisation_type', '=', 'builder')
                                            ->first();
                    if(is_object($builder))
                    {
                        $site->builder_id = $builder->organisation_id;
                    }
                    else
                    {
                        $site->builder_id = 0;
                    }
                }
            }

            $site->name = $request->name;

            if(!empty($request->hygenist))
            {
                $site->hygenist_id = $request->hygenist;
            }
            if(!empty($request->contact))
            {
                $site->primary_contact_id = $request->contact;
            }
            $site->simpro_site_id_1 = $request->simpro_id;
            $site->address = $request->address;
            $site->city = $request->city;
            $site->state = $request->state;
            $site->postcode = $request->postcode;
            $site->country = $request->country;
            $site->phone = $request->phone;
            $site->mobile = $request->mobile;
            $site->zone_qr_code_function = $request->qrCodeFunction;
            

            if(!empty($request->file('image')))
            {
                $image = $request->file('image')->store('images/sites', 'public');
                $site->image = $image;
            }

            $site->save();
            if($site->builder_id > 0)
            {
                $this->checkLicenseTable($site->builder_id);
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
                    $f->module_id = $site->id;
                    $f->module = "sites";
                    
                    $f->save();
                }
            }

            if(!empty($request->requirements))
            {
                foreach($request->requirements as $key => $value)
                {
                    $ps = Permits_Site::where('permits_id', '=', $key)->where('sites_id', '=', $site->id)->first();
                    if(is_object($ps))
                    {
                        $ps->mandatory = $value;
                        $ps->save();
                    }
                }
            }


            $this->insertLog($standardDisplay['profile']->id, "sites", $site->id, $action, "Site " . $site->name . " " . $action, "INFO");

            $alert = "Site " . $site->name . " saved";
            
            return $this->displaySites($alert);
        }

        public function editZone($zone)
        {
            $standardDisplay = $this->checkFunctionPermission("sites:edit");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $breadcrumbs = Controller::createBreadcrumbs("Sites", "Zone");
            $zone = Sites_Maps_Zone::find($zone);
            $site = Site::find($zone->site_id);

            $timeEntries = Actions_Time_Entry::where('zone_id', '=', $zone->id)->get();
            $assessments = Actions_Assessment::where('zone_id', '=', $zone->id)->get();
            $permits = $this->getZonePermits($zone);
            $allPermits = Permit::where('archived', '=', 0)->orderBy('name', 'asc')->get();
            $allHazards = Hazard::where('archived', '=', 0)->orderBy('name', 'asc')->get();
            $controls = $this->getZoneControls($zone);
            $zones = $this->getSiteZones($zone->site_id);


            $module = 'zones';
            $moduleID = $zone->id;
            $files = File::where('module', '=', $module)
                                        ->where('module_id', '=', $moduleID)
                                        ->get();
            $logs = $this->retrieveLogs($module, $moduleID);

            $dashboardType = "zone";
            $widgetOptions = $this->getWidgetOptions($dashboardType, $moduleID);
            $dashboardArray = $this->getSiteZoneDashboard($dashboardType, $moduleID);

            $qrAddress = env("APP_URL", "https://client.nextrack.tech/") . "qrActivity/" . $zone->site_id . "/" . $zone->id;

            $devices = array();
            $d = 0;
            foreach($controls as $type)
            {
                foreach($type['controls'] as $control)
                {
                    //get all the devices on this control
                    $tbDevices = Thingsboards_Device::where('control_id', '=', $control['control']->id)->get();
                    foreach($tbDevices as $device)
                    {
                        $devices[$d]['device'] = $device;
                        $devices[$d]['site'] = $site;
                        $devices[$d]['control'] = $control['control'];
                        $d++;
                    }
                }
            }
            
            return view('people.editZone', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'dashboardArray' => $dashboardArray,
                'dashboardType' => $dashboardType,
                'moduleID' => $moduleID,
                'files' => $files,
                'logs' => $logs,
                'zone' => $zone,
                'timeEntries' => $timeEntries,
                'assessments' => $assessments,
                'permits' => $permits,
                'allPermits' => $allPermits,
                'allHazards' => $allHazards,
                'controls' => $controls,
                'zones' => $zones,
                'widgetOptions' => $widgetOptions,
                'qrAddress' => $qrAddress,
                'devices' => $devices,
            ]);
        }

        public function saveZone(Request $request, $zone)
        {
            $zone = Sites_Maps_Zone::find($zone);

            $standardDisplay = $this->checkFunctionPermission("sites:edit");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }


            $zone->name = $request->name;
            $zone->save();

            if(isset($request->requirements))
            {
                foreach($request->requirements as $key=>$value)
                {
                    $pz = Permits_Zone::where('permits_id', '=', $key)->where('zones_id', '=', $zone->id)->first();
                    if(is_object($pz))
                    {
                        $pz->mandatory = $value;
                        $pz->save();
                    }
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
                    $f->module_id = $zone->id;
                    $f->module = "zones";
                    
                    $f->save();
                }
            }

            $this->insertLog($standardDisplay['profile']->id, "zones", $zone->id, "Edited", "Zone " . $zone->name . " updated.", "INFO");

            return $this->editSite($zone->site_id);
        }

        public function addHazardSample($hazard, $type, $date, $result)
        {
            $standardDisplay = Controller::standardDisplay();

            $sample = new Sites_Maps_Zones_Hazards_Sample();
            $sample->zone_hazard_id = $hazard;
            $sample->archived = 0;
            $sample->sample_id = $type;
            $sample->date = $date;
            $sample->result = $result;
            $sample->save();
            
            $this->insertLog($standardDisplay['profile']->id, "hazards", $hazard, "Added", "New sample added", "INFO");
            
            $array = array();
            $a = 0;

            $samples = Sites_Maps_Zones_Hazards_Sample::where('zone_hazard_id', '=', $hazard)
                                                        ->where('archived', '=', 0)
                                                        ->orderBy('date', 'asc')
                                                        ->get();
            foreach($samples as $sample)
            {
                $array[$a]['id'] = $sample->id;
                $array[$a]['name'] = $sample->Sample->name;
                $array[$a]['date'] = $sample->date->format('d-m-Y');
                $array[$a]['result'] = $sample->result;
                $array[$a]['measurement'] = $sample->Sample->measurement;

                $a++;
            }

            return json_encode($array);
        }

        public function deleteSample($sample)
        {
            $standardDisplay = Controller::standardDisplay();
            $sample = Sites_Maps_Zones_Hazards_Sample::find($sample);
            $hazard = Sites_Maps_Zones_Hazard::find($sample->zone_hazard_id);

            $sample->archived = 1;
            $sample->save();

            $this->insertLog($standardDisplay['profile']->id, "hazards", $hazard->id, "Deleted", "Sample of " . $sample->Sample->name . " from " . $sample->date->format('d-m-Y') . " deleted", "INFO");

            return $this->editHazard($hazard->id);
        }

        public function editHazard($hazard)
        {
            $standardDisplay = $this->checkFunctionPermission("sites:edit");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $breadcrumbs = Controller::createBreadcrumbs("Sites", "Edit hazard");  
            $hazard = Sites_Maps_Zones_Hazard::find($hazard);

            $array = array();
            $x = 0;
            
            $hzrd = Hazard::find($hazard->hazard_id);
            $trades = Hazards_Trades::where('hazard_id', '=', $hzrd->id)->get();
            foreach($trades as $trade)
            {
                $activities = Hazards_Activities::where('hazard_id', '=', $hzrd->id)->get();
                foreach($activities as $activity)
                {
                    //get the assessments
                    $assessments = Assessments_Activities::where('activities_id', '=', $activity->activity_id)->get();
                    foreach($assessments as $assessment)
                    {
                        $array[$x]['trade'] = $trade->Trade->name;
                        $array[$x]['activity'] = $activity->Activity->name;
                        $array[$x]['assessment'] = $assessment->Assessment->name;
                        $x++;
                    }
                }
            }

            $controls = $this->getHazardControls($hazard);
            $zones = $this->getSiteZones($hazard->Zone->site_id);
            $samples = Sites_Maps_Zones_Hazards_Sample::where('zone_hazard_id', '=', $hazard->id)->where('archived', '=', 0)->orderBy('date', 'asc')->get();
            $sampleTypes = Sample::where('archived', '=', 0)->orderBy('name', 'asc')->get();
            $monitors = $this->getMonitors($controls);            

            $module = 'hazards';
            $moduleID = $hazard->id;
            $files = File::where('module', '=', $module)
                                        ->where('module_id', '=', $moduleID)
                                        ->get();
            $logs = $this->retrieveLogs($module, $moduleID);
            
            return view('people.editHazard', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'hazard' => $hazard,
                'files' => $files,
                'logs' => $logs,
                'assessments' => $array,
                'controls' => $controls,
                'zones' => $zones,
                'samples' => $samples,
                'sampleTypes' => $sampleTypes,
                'monitors' => $monitors,
            ]);
        }

        public function getMonitors($controls)
        {
            /*
                this function returns a list of monitors and their latest reading in an array
                $controls is the array create from the getHazardControls function
            */

            $monitors = array();
            $m = 0;

            foreach($controls as $type)
            {
                foreach($type['controls'] as $control)
                {
                    //find and associated monitors on this control
                    $devices = Thingsboards_Device::where('control_id', '=', $control['control']->id)->get();
                    foreach($devices as $device)
                    {
                        $readingsArray = array();
                        $ra = 0;
                        //get all the reading types for this device
                        $readingTypes = Thingsboards_Device_Reading_Types::where('device_id', '=', $device->id)->get();
                        foreach($readingTypes as $rt)
                        {
                            $readings = Thingsboards_Device_Reading::where('device_id', '=', $device->id)
                                                                    ->where('reading_type_id', '=', $rt->reading_type_id)
                                                                    ->orderBy('id', 'desc')
                                                                    ->limit(1)
                                                                    ->get();
                            foreach($readings as $r)
                            {
                                $readingsArray[$ra]['readingType'] = $rt->ReadingType->name;
                                $readingsArray[$ra]['reading'] = $r->reading;
                                $ra++;
                            }
                                            
                        }
                        $monitors[$m]['device'] = $device;
                        $monitors[$m]['readings'] = $readingsArray;
                        $monitors[$m]['control'] = $control['control'];
                        $m++;
                    }
                }
            }
            return $monitors;
        }

        public function saveHazard(Request $request, $hazard)
        {
            $hazard = Sites_Maps_Zones_Hazard::find($hazard);

            $standardDisplay = $this->checkFunctionPermission("sites:edit");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }


            $hazard->plan = $request->plan;
            $hazard->save();

            if(!empty($request->step))
            {
                $step = new Sites_Maps_Zones_Hazards_Plan_Step();
                $step->zone_hazard_id = $hazard->id;
                $step->step = $request->step;
                $step->save();

                $this->insertLog($standardDisplay['profile']->id, "hazards", $hazard->id, "Created", "New step added to hazard", "INFO");
            }

            $this->insertLog($standardDisplay['profile']->id, "hazards", $hazard->id, "Edited", "Hazard " . $hazard->Hazard->name . " updated.", "INFO");

            if($request->submitted == "Add step")
            {
                return $this->editHazard($hazard->id);
            }
            else
            {
                return $this->editZone($hazard->zone_id);
            }
            
        }

        public function addHazard(Request $request, $zone)
        {
            $check = Sites_Maps_Zones_Hazard::where('zone_id', '=', $zone)
                                            ->where('hazard_id', '=', $request->newHazard)
                                            ->where('archived', '=', 0)
                                            ->count();
            if($check == 0)
            {
                $new = new Sites_Maps_Zones_Hazard();
                $new->zone_id = $zone;
                $new->hazard_id = $request->newHazard;
                $new->archived = 0;
                $new->save();
            }

            return $this->editZone($zone);
            
        }

        public function removeStep($step)
        {
            $step = Sites_Maps_Zones_Hazards_Plan_Step::find($step);
            $hazard = Sites_Maps_Zones_Hazard::find($step->zone_hazard_id);

            $standardDisplay = $this->standardDisplay();
            
            $this->insertLog($standardDisplay['profile']->id, "hazards", $hazard->id, "Deleted", "Step deleted from hazard", "WARNING");
            
            $step->delete();

            return $this->editHazard($hazard->id);
        }

        public function archiveSite($site)
        {
            $standardDisplay = $this->checkFunctionPermission("sites:delete");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $action = "archived";

            $site = Site::find($site);
            $site->archived = 1;
            $site->status = "complete";
            $site->save();

            $alert = "Site " . $site->name . " archived";

            $this->insertLog($standardDisplay['profile']->id, "sites", $site->id, $action, "Site " . $site->name . " " . $action, "WARNING");
            
            return $this->displaySites($alert);
        }

        public function checkZoneHazards($zone)
        {
            $array = array();
            $a = 0;

            $hazards = Sites_Maps_Zones_Hazard::where('zone_id', '=', $zone)->get();
            
            foreach($hazards as $hazard)
            {
                $array[$a]['id'] = $hazard->id;
                $array[$a]['name'] = $hazard->Hazard->name;
                $a++;
            }

            return json_encode($array);
        }

        public function transferControlsOnSite(Request $request)
        {
            //go through each control
            if(!empty($request->controlsSelected))
            {
                foreach($request->controlsSelected as $control)
                {
                    $controlSite = Controls_Sites::where('control_id', '=', $control)->orderBy('id', 'desc')->first();
                    if(!empty($controlSite))
                    {
                        $fromMap = $controlSite->to_map_id;
                        $fromZone = $controlSite->to_zone_id;
                        $fromHazard = $controlSite->to_hazard_id;
                    }
                    else
                    {
                        $fromMap = 0;
                        $fromZone = 0;
                        $fromHazard = 0;
                    }

                    $zone = Sites_Maps_Zone::find($request->toZone);

                    $move = new Controls_Sites();
                    
                    $move->control_id = $control;

                    $move->from_site_id = $request->site;
                    $move->to_site_id = $request->site;

                    $move->from_map_id = $fromMap;
                    $move->to_map_id = $zone->map_id;

                    $move->from_zone_id = $fromZone;
                    $move->to_zone_id = $zone->id;

                    $move->from_hazard_id = $fromHazard;
                    $move->to_hazard_id = $request->toHazard;
                    
                    $move->save();

                    $this->resetControlCoords($control);
                }
            }

            if($request->referrer == "site")
            {
                return $this->editSite($request->site);
            }
            elseif($request->referrer == "zone")
            {
                return $this->editZone($request->zone);
            }
            else
            {
                return $this->editHazard($request->hazard);
            }
        }
        
        public function completeSite($site)
        {
            $setupController = new SetupController();

            $site = Site::find($site);
            $site->status = "complete";
            $site->save();

            $controls = Control::where('current_site', '=', $site->id)->get();
            foreach($controls as $control)
            {
                $latestTransfer = Controls_Sites::where('control_id', '=', $control->id)->orderby('id', 'desc')->first();
            
                $request = new Request();

                $request->control_id = $control->id;
                
                $request->fromSite = $latestTransfer->to_site_id;
                $request->toSite = 0;

                $request->fromSite = $latestTransfer->to_map_id;
                $request->toMap = 0;

                $request->fromZone = $request->fromSite = $latestTransfer->to_zone_id;
                $request->toZone = 0;

                $request->fromHazard = $request->fromSite = $latestTransfer->to_hazard_id;
                $request->toHazard = 0;

                $request->originator = "completeSite";
                $request->moduleID = $control->controls_type_id;

                $setupController->moveControl($request, $control->id);

                $this->resetControlCoords($control->id);
            }


            $alert = "Site " . $site->name . " has been marked as complete.  All controls have been removed.";
            return $this->displaySites($alert);
        }

        public function copySite($site)
        {
            //go through and copy all the top level details about a site.
            $origSite = Site::find($site);
            if(is_object($origSite))
            {
                $new = new Site();

                $new->name = "Copy of " . $origSite->name;
                $new->builder_id = $origSite->builder_id;
                $new->status = "active";
                $new->address = $origSite->address;
                $new->city = $origSite->city;
                $new->state = $origSite->state;
                $new->postcode = $origSite->postcode;
                $new->country = $origSite->country;
                $new->primary_contact_id = $origSite->primary_contact_id;
                $new->phone = $origSite->phone;
                $new->mobile = $origSite->mobile;
                $new->image = $origSite->image;
                $new->archived = 0;
                $new->save();

                $contractors = Sites_Contractors::where('site_id', '=', $origSite->id)->get();
                foreach($contractors as $c)
                {
                    $newC = new Sites_Contractors();
                    $newC->site_id = $c->site_id;
                    $newC->profile_id = $c->profile_id;

                    $newC->save();
                }

                $permits = Permits_Site::where('sites_id', '=', $origSite->id)->get();
                foreach($permits as $p)
                {
                    $newP = new Permits_Site();
                    $newP->permits_id = $p->permits_id;
                    $newP->sites_id = $p->sites_id;
                    $newP->mandatory = $p->mandatory;

                    $newP->save();
                }

                if($new->builder_id > 0)
                {
                    $this->checkLicenseTable($new->builder_id);
                }

                $alert = $origSite->name . " copied to " . $new->name;
            }
            else
            {
                $alert = "Problem finding record for $site.  Please contact Nextrack support.";
            }
            
            return $this->displaySites($alert);
        }

        public function relinquishSite(Request $request)
        {
            //a user is relinquishing control of the site to a builder.
            $standardDisplay = $this->checkFunctionPermission("sites:relinquish");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $site = Site::find($request->site);
            $site->relinquish_id = $request->builder;
            $site->save();

            $this->insertLog($standardDisplay['profile']->id, "sites", $site->id, "Relinquished", "Site " . $site->name . " was reqlinquished to builder, waiting for acceptance.", "INFO");

            $alert = "Site " . $site->name . " relinquished, pending acceptance by the builder.";
            
            return $this->displaySites($alert);
        }

        public function acceptSite($site)
        {
            $standardDisplay = $this->checkFunctionPermission("sites:relinquish");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $site = Site::find($site);
            if(!empty($site->relinquish_id))
            {
                $site->builder_id = $site->relinquish_id;
                $site->relinquish_id = NULL;
                $site->save();
            }

            $this->insertLog($standardDisplay['profile']->id, "sites", $site->id, "Accepted", "Site " . $site->name . " was accepted by the builder.", "INFO");

            $alert = "Site " . $site->name . " accepted.";
            
            return $this->displaySites($alert);
        }

        public function mergeSite(Request $request)
        {
            //a user is merging two sites together.
            $standardDisplay = $this->checkFunctionPermission("sites:merge");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $site = Site::find($request->site);
            $mergeToSite = Site::find($request->mergeTo);
            
            $actionAssessments = Actions_Assessment::where('site_id', '=', $site->id)->get();
            foreach($actionAssessments as $item)
            {
                $item->site_id = $mergeToSite->id;
                $item->save();
            }

            $actionControls = Actions_Control::where('site_id', '=', $site->id)->get();
            foreach($actionControls as $item)
            {
                $item->site_id = $mergeToSite->id;
                $item->save();
            }

            $actionTimeEntry = Actions_Time_Entry::where('site_id', '=', $site->id)->get();
            foreach($actionTimeEntry as $item)
            {
                $item->site_id = $mergeToSite->id;
                $item->save();
            }

            $actionAssessments = Assessments_Site::where('sites_id', '=', $site->id)->get();
            foreach($actionAssessments as $item)
            {
                $item->sites_id = $mergeToSite->id;
                $item->save();
            }

            $controls = Control::where('current_site', '=', $site->id)->get();
            foreach($controls as $item)
            {
                $item->current_site = $mergeToSite->id;
                $item->save();
            }

            $orders = Controls_Orders::where('site_id', '=', $site->id)->get();
            foreach($orders as $item)
            {
                $item->site_id = $mergeToSite->id;
                $item->save();
            }

            $controlSites = Controls_Sites::where('from_site_id', '=', $site->id)->get();
            foreach($controlSites as $item)
            {
                $item->from_site_id = $mergeToSite->id;
                $item->save();
            }

            $controlSites = Controls_Sites::where('to_site_id', '=', $site->id)->get();
            foreach($controlSites as $item)
            {
                $item->to_site_id = $mergeToSite->id;
                $item->save();
            }

            $dashboards = Dashboard::where('type', '=', 'site')
                                    ->where('module_id', '=', $mergeToSite->id)
                                    ->count();
            if($dashboards == 0)
            {
                $dboards = Dashboard::where('type', '=', 'site')
                                        ->where('module_id', '=', $site->id)
                                        ->get();
                foreach($dboards as $item)
                {
                    $item->module_id = $mergeToSite->id;
                    $item->save();
                }
            }

            $files = File::where('module', '=', 'sites')
                            ->where('module_id', '=', $site->id)
                            ->get();
            foreach($files as $item)
            {
                $item->module_id = $mergeToSite->id;
                $item->save();
            }

            $histories = History::where('site_id', '=', $site->id)->get();
            foreach($histories as $item)
            {
                $item->site_id = $mergeToSite->id;
                $item->save();
            }

            $logs = Log::where('module', '=', 'sites')
                            ->where('module_id', '=', $site->id)
                            ->get();
            foreach($logs as $item)
            {
                $item->module_id = $mergeToSite->id;
                $item->save();
            }

            $permits = Permits_Site::where('sites_id', '=', $site->id)->get();
            foreach($permits as $item)
            {
                $item->sites_id = $mergeToSite->id;
                $item->save();
            }

            $tasks = Task::where('site_id', '=', $site->id)->get();
            foreach($tasks as $item)
            {
                $item->site_id = $mergeToSite->id;
                $item->save();
            }

            $sitesContractors = Sites_Contractors::where('site_id', '=', $site->id)->get();
            foreach($sitesContractors as $contractor)
            {
                $check = Sites_Contractors::where('site_id', '=', $mergeToSite->id)
                                            ->where('profile_id', '=', $contractor->profile_id)
                                            ->get();
                if(count($check) == 0)
                {
                    $contractor->site_id = $mergeToSite->id;
                    $contractor->save();
                }
                else
                {
                    $contractor->delete();
                }
            }

            $logons = Sites_Logon::where('site_id', '=', $site->id)->get();
            foreach($logons as $item)
            {
                $item->site_id = $mergeToSite->id;
                $item->save();
            }

            $logons = Sites_Map::where('site_id', '=', $site->id)->get();
            foreach($logons as $item)
            {
                $item->site_id = $mergeToSite->id;
                $item->save();
            }

            $zones = Sites_Maps_Zone::where('site_id', '=', $site->id)->get();
            foreach($zones as $item)
            {
                $item->site_id = $mergeToSite->id;
                $item->save();
            }

            $sitesProfiles = Sites_Profile::where('site_id', '=', $site->id)->get();
            foreach($sitesProfiles as $profile)
            {
                $check = Sites_Profile::where('site_id', '=', $mergeToSite->id)
                                            ->where('profile_id', '=', $profile->profile_id)
                                            ->get();
                if(count($check) == 0)
                {
                    $profile->site_id = $mergeToSite->id;
                    $profile->save();
                }
                else
                {
                    $profile->delete();
                }
            }

            $site->archived = 1;
            $site->save();

            $this->insertLog($standardDisplay['profile']->id, "sites", $mergeToSite->id, "Merged", "Site " . $site->name . " was merged into this site.", "INFO");

            $alert = "Site " . $mergeToSite->name . " merged.";
            
            return $this->displaySites($alert);
        }

        public function transferMap(Request $request)
        {
            $standardDisplay = $this->checkFunctionPermission("sites:edit");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }


            $map = Sites_Map::find($request->map);
            $oldSite = Site::find($map->site_id);
            $newSite = Site::find($request->toSite);

            $zones = Sites_Maps_Zone::where('map_id', '=', $map->id)->get();
            foreach($zones as $zone)
            {
                
                $zoneControls = $this->getZoneControls($zone);

                foreach($zoneControls as $types)
                {
                    foreach($types['controls'] as $control)
                    {
                        //now lets transfer the control on the controls sites table
                        $controlsSite = Controls_Sites::where('control_id', '=', $control['control']->id)->orderBy('id', 'desc')->first();
                        if(is_object($controlsSite))
                        {
                            $cs = new Controls_Sites();

                            $cs->control_id = $control['control']->id;
                            $cs->from_site_id = $oldSite->id;
                            $cs->to_site_id = $newSite->id;
                            $cs->from_map_id = $controlsSite->from_map_id;
                            $cs->to_map_id = $controlsSite->to_map_id;
                            $cs->from_zone_id = $controlsSite->from_zone_id;
                            $cs->to_zone_id = $controlsSite->to_zone_id;
                            $cs->from_hazard_id = $controlsSite->from_hazard_id;
                            $cs->to_hazard_id = $controlsSite->to_hazard_id;
                            
                            $cs->save();
                        }
                        $control['control']->current_site = $newSite->id;
                        $control['control']->save();
                    }
                }
                
                $zone->site_id = $newSite->id;
                $zone->save();
            }

            $this->insertLog($standardDisplay['profile']->id, "sites", $oldSite->id, "Moved map", "Map " . $map->name . " was moved to " . $newSite->name . "", "INFO");
            $this->insertLog($standardDisplay['profile']->id, "sites", $newSite->id, "Moved map", "Map " . $map->name . " was moved from " . $oldSite->name . "", "INFO");

            $map->site_id = $newSite->id;
            $map->save();

            return $this->editSite($oldSite->id);
        }

        public function removePermit($type, $id)
        {
            /*
                Function determines what kind of permit needs removing, deletes is from the database the redirects the user back to the site / zone record
            */
            $standardDisplay = $this->checkFunctionPermission("sites:edit");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }


            if($type == "site")
            {
                $permit = Permits_Site::find($id);
                $site = $permit->sites_id;

                if(is_object($permit->Permit))
                {
                    $this->insertLog($standardDisplay['profile']->id, "sites", $site, "Edited", "Requirement " . $permit->Permit->name . " removed from site", "INFO");
                }
                
                $permit->delete();

                return $this->editSite($site);
            }
            else 
            {
                $permit = Permits_Zone::find($id);
                $zone = $permit->zones_id;
                
                $this->insertLog($standardDisplay['profile']->id, "zones", $zone, "Edited", "Requirement " . $permit->Permit->name . " removed from zone.", "INFO");
                
                $permit->delete();

                return $this->editZone($zone);
            }
        }

        public function removeHazard($id)
        {
            $hazard = Sites_Maps_Zones_Hazard::find($id);
            $zone = $hazard->zone_id;
            $hazard->delete();

            return $this->editZone($zone);
        }




    //Users functions

        public function users()
        {
            /*
                Standard page setup
            */
            $alert = NULL;

            return $this->displayUsers($alert);
        }

        public function userProfiles($standardDisplay)
        {
            /*
                This function returns a list of all the Profiles that this person is allowed to see
                $standardDisplay is the full $standardDisplay set of objects
            */
            $profiles = array();
            $p = 0;
            $profilesCheck = array();

            //check to see if the user can see other users
            if($standardDisplay['profile']->super_user == 1)
            {
                $users = profile::where('archived', '=', 0)->where('type', '=', 'user')->orderBy('name', 'asc')->get();
                foreach($users as $user)
                {
                    $profiles[$p] = $user;
                    $p++;
                }
            }
            else
            {
                //find out if the user is a member of any organisation, and if so, what their permissions are
                $memberships = Membership::where('user_id', '=', $standardDisplay['profile']->id)
                                            ->where('membership_status', '=', 'active')
                                            ->get();
                if(count($memberships) > 0)
                {
                    foreach($memberships as $member)
                    {
                        $permissions = $this->getSpecificPermissions($member->security_group);
                        if(in_array("users:view", $permissions))
                        {
                            //return a list of users in that organisation
                            $users = Membership::where('organisation_id', '=', $member->organisation_id)->get();
                            foreach($users as $user)
                            {
                                $profile = Profile::find($user->user_id);
                                if($profile->type == "user"  && $profile->archived == "0")
                                {
                                    if(!in_array($profile->id, $profilesCheck))
                                    {
                                        $profiles[$p] = $profile;
                                        $profilesCheck[$p] = $profile->id;
                                        $p++;
                                    }
                                }
                            }
                        }
                    }
                }        
            }

            if(($p == 0))
            {
                //only has access to themselves
                $users = profile::where('id', '=', $standardDisplay['profile']->id)->get();
                
                foreach($users as $user)
                {
                    $profiles[$p] = $user;
                    $p++;
                }
            }

            return $profiles;
        }

        public function displayUsers($alert)
        {
            $standardDisplay = Controller::standardDisplay();
            $breadcrumbs = Controller::createBreadcrumbs("Users", NULL);
            $profiles = $this->userProfiles($standardDisplay);
            
            
            return view('people.users', [
                'breadcrumbs' => $breadcrumbs,
                'alert' => $alert,
                'standardDisplay' => $standardDisplay,
                'profiles' => $profiles,
            ]);
        }

        public function editProfile($profile)
        {
            $standardDisplay = Controller::standardDisplay();
            
            if(!in_array("users:edit", $standardDisplay['permissions']))
            {
                if($standardDisplay['profile']->super_user != 1)
                {
                    if($profile != $standardDisplay['profile']->id)
                    {
                        $index = new HomeController();
                        $alert = "You do not have privileges to do that.";

                        return $index->displayDashboard($alert);
                    }
                }
            }

            if($standardDisplay['profile']->super_user != 1 && $profile != $standardDisplay['profile']->id)
            {
                $profileChecker = 0;
                $userProfiles = $this->userProfiles($standardDisplay);
                foreach($userProfiles as $thisProfile)
                {
                    if($thisProfile->id == $profile)
                    {
                        $profileChecker = 1;
                    }
                }
                if($profileChecker == 0)
                {
                    $index = new HomeController();
                    $alert = "You do not have privileges to do that.";

                    return $index->displayDashboard($alert);
                }
            }
            
            if($profile == 0)
            {
                $profile = new Profile();
                $profile->id = 0;
                $profile->name = "New";
                $profile->type = "user";
                $group = Security_Groups::where('type', '=', 'system')
                                            ->where('name', '!=', 'Super Admin')
                                            ->where('archived', '=', 0)
                                            ->first();
                $profile->security_group = $group->id;
            }
            else
            {
                $profile = Profile::find($profile);
            }

            $breadcrumbs = Controller::createBreadcrumbs("Users", $profile->name);       
            
            $module = 'profiles';
            $moduleID = $profile->id;
            $files = File::where('module', '=', $module)
                                        ->where('module_id', '=', $moduleID)
                                        ->get();
            $logs = $this->retrieveLogs($module, $moduleID);

            $history = $this->getProfileHistory($profile->id);

            $memberships = Membership::where('user_id', '=', $profile->id)->get();

            $trades = Profiles_Trade::where('profiles_id', '=', $profile->id)->get();
            $allTrades = Trade::where('archived', '=', 0)->orderBy('name', 'asc')->get();

            $permits = Permits_Profile::where('profiles_id', '=', $profile->id)->get();
            $allPermits = Permit::where('archived', '=', 0)->orderBy('name', 'asc')->get();

            $securityGroups = Security_Groups::where('type', '=', 'system')->where('archived', '=', 0)->orderBy('name', 'asc')->get();

            $headings = array();
            $lastLog = Log::where('profiles_id', '=', $profile->id)->orderBy('updated_at', 'desc')->first();
            $logCount = Log::where('profiles_id', '=', $profile->id)->count();
            
            if($profile->super_user == 1)
            {
                $securityGroup = "Super User";
            }
            else
            {
                if($profile->security_group > 0)
                {
                    $securityGroup = $profile->Security_Group->name;
                }
                else
                {
                    $securityGroup = "";
                }
            }

            if($profile->id > 0)
            {
                if(!empty($lastLog))
                {
                    $headings[0] = $lastLog->created_at->format('d-m-Y H:i');
                }
                else
                {
                    $headings[0] = "-";
                }
                $headings[1] = $securityGroup;
                $headings[2] = $logCount;
            }
            else
            {
                $headings[0] = "Never";
                $headings[1] = $securityGroup;
                $headings[2] = 0;
            }
            
            return view('people.editProfile', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'files' => $files,
                'logs' => $logs,
                'profile' => $profile,
                'history' => $history,
                'memberships' => $memberships,
                'trades' => $trades,
                'allTrades' => $allTrades,
                'permits' => $permits,
                'allPermits' => $allPermits,
                'headings' => $headings,
                'securityGroups' => $securityGroups,
            ]);
        }

        public function saveProfile(Request $request, $profile)
        {
            $standardDisplay = $this->standardDisplay();
            $user = Auth::user();

            if(!in_array("users:edit", $standardDisplay['permissions']))
            {
                if($standardDisplay['profile']->super_user == 0)
                {
                    if($standardDisplay['profile']->id != $profile)
                    {
                        $index = new HomeController();
                        $alert = "You do not have privileges to do that.";

                        return $index->displayDashboard($alert);
                    }
                }
            }
            
            if($profile == 0)
            {
                $profile = new Profile();
                $profile->archived = 0;
                $profile->security_group = 1;
                $profile->super_user = 0;
                $profile->member_hash = $this->generateRandomString();
                $action = "created";
            }
            else
            {
                $profile = Profile::find($profile);
                if($profile->member_hash == "")
                {
                    $profile->member_hash = $this->generateRandomString();
                }
                $action = "Edited";

                if($profile->email != $request->email)
                {
                    $userToUpdate = User::find($profile->user_id);
                    $userToUpdate->email = $request->email;
                    $userToUpdate->save();
                }
            }

            $profile->name = $request->name;
            $profile->simpro_id_1 = $request->simpro_id;
            $profile->type = $request->type;
            $profile->security_group = $request->security_groups_id;
            $profile->email = $request->email;
            $profile->phone = $request->phone;
            $profile->mobile = $request->mobile;
            $profile->theme = $request->theme;



            if(!empty($request->file('logo')))
            {
                $image = $request->file('logo')->store('images/users', 'public');
                $profile->logo = $image;
            }

            $profile->save();

            // After Creating a new profile in the system an email will be sent to intgrations

            $subject = "A new user Profile has been created in Nextrack";
            $content = "Name: " . $profile->name . "<br>";
            $content .= "SimPRO ID: " . $profile->simpro_id_1 . "<br>";
            $content .= "Type: " . $profile->type . "<br>";
            $content .= "Security Group: " . $profile->security_group . "<br>"; 
            $content .= "Email: " . $profile->email . "<br>";
            $content .= "Phone: " . $profile->phone . "<br>"; 
            $content .= "Mobile: " . $profile->mobile . "<br>"; 

            $email = new Email();
            $email->send_to = "Gigi Fleiner";
            $email->send_email = "integrations@nextrack.com.au";
            $email->subject = $subject;
            $email->content = $content;
            $email->status = "pending";
            $email->save();

            emailSender::dispatch();

            // Mail to be sent to integrations ends here


            
            

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
                    $f->module_id = $profile->id;
                    $f->module = "profiles";
                    
                    $f->save();
                }
            }


            $this->insertLog($standardDisplay['profile']->id, "profiles", $profile->id, $action, "User profile " . $profile->name . " " . $action, "INFO");

            $alert = "User profile " . $profile->name . " saved";
            
            return $this->displayUsers($alert);
        }

        public function addTrade($trade, $profile)
        {
            $standardDisplay = $this->standardDisplay();
            $profile = Profile::find($profile);

            //check to make sure this trade isn't already on the profile
            $check = Profiles_Trade::where('profiles_id', '=', $profile->id)->where('trades_id', '=', $trade)->count();
            if($check == 0)
            {
                $new = new Profiles_Trade();
                $new->profiles_id = $profile->id;
                $new->trades_id = $trade;
                $new->save();
                
                $action = "Edited";
                
                $this->insertLog($standardDisplay['profile']->id, "profiles", $profile->id, $action, $new->Trade->name . " added to  profile " . $profile->name, "INFO");
            }

            $array = array();
            $i = 0;

            $return = Profiles_Trade::where('profiles_id', '=', $profile->id)->get();
            foreach($return as $r)
            {
                $array[$i]['id'] = $r->id;
                $array[$i]['name'] = $r->Trade->name;
                $i++;
            }

            return json_encode($array);
        }

        public function deleteTrade($trade)
        {
            $standardDisplay = $this->standardDisplay();
            $profileTrade = Profiles_Trade::find($trade);
            $profile = Profile::find($profileTrade->profiles_id);

            $action = "Edited";

            $this->insertLog($standardDisplay['profile']->id, "profiles", $profile->id, $action, $profileTrade->Trade->name . " removed from  profile " . $profile->name, "INFO");

            $profileTrade->delete();

            if($profile->type == "user")
            {
                return $this->editProfile($profile->id);
            }
            else
            {
                return $this->editContractor($profile->id);
            }
        }

        public function archiveProfile($profile)
        {
            $standardDisplay = $this->standardDisplay();
            $action = "archived";

            if(!in_array("users:delete", $standardDisplay['permissions']))
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $profile = Profile::find($profile);
            $profile->archived = 1;
            $profile->save();

            $alert = "User profile for " . $profile->name . " archived";

            $this->insertLog($standardDisplay['profile']->id, "profiles", $profile->id, $action, "User profile " . $profile->name . " " . $action, "WARNING");
            
            return $this->displayUsers($alert);
        }

    

    //Hygenists functions

        public function hygenists()
        {
            /*
                Standard page setup
            */
            $alert = NULL;

            return $this->displayHygenists($alert, 0);
        }

        public function servicePartners()
        {
            $alert = NULL;

            return $this->displayHygenists($alert, 1);
        }

        public function getUserProviders($profile, $type)
        {
            /*
                Function returns a list of the hygenists / providers either they are a member of, or who are part of an organisation who are a member of them
                $profile is the full standardDisplay profile

                $type is a 0 if we are looking for hygenists, a 1 if we are looking for a service provider
            */

            $hygenistsArray = array();
            $hygenistCheckArray = array();
            $h = 0;      

            //check to see if the user can see other users
            if($profile->super_user == 1)
            {
                if($type == 0)
                {
                    $hygenistsArray = profile::where('archived', '=', 0)->where('type', '=', 'hygenist')->where('provider_type', '!=', 's')->orderBy('name', 'asc')->get();
                }
                else
                {
                    $hygenistsArray = profile::where('archived', '=', 0)->where('type', '=', 'hygenist')->where('provider_type', '=', 's')->orderBy('name', 'asc')->get();
                }
            }
            else
            {
                //find out if the user is a member of a Hygenist and if so what their permissions are
                if($type == 0)
                {
                    $memberships = Membership::where('user_id', '=', $profile->id)
                                                ->where('membership_status', '=', 'active')
                                                ->where('organisation_type', '=', 'hygenist')
                                                ->get();
                    if(count($memberships) > 0)
                    {
                        foreach($memberships as $member)
                        {
                            $permissions = $this->getSpecificPermissions($member->security_group);
                            if(in_array("hygenists:view", $permissions))
                            {
                                $hygenist = Profile::find($member->organisation_id);
                                if(is_object($hygenist))
                                {
                                    if($hygenist->provider_type == 'h')
                                    {
                                        if(!in_array($hygenist->id, $hygenistCheckArray))
                                        {
                                            $hygenistsArray[$h] = $hygenist;
                                            $hygenistCheckArray[$h] = $hygenist->id;
                                            $h++;
                                        }
                                    }   
                                }
                            }
                        }
                    }

                    //now check the memberships the user does have, and see if those organisations are also part of a hygenist
                    $memberships = Membership::where('user_id', '=', $profile->id)
                                                ->where('membership_status', '=', 'active')
                                                ->where('organisation_type', '!=', 'hygenist')
                                                ->get();
                    
                    foreach($memberships as $member)
                    {
                        //echo "Found a member " . $member->organisation_id;
                        //exit;
                        $orgMemberships = Membership::where('user_id', '=', $member->organisation_id)
                                                        ->where('membership_status', '=', 'active')
                                                        ->where('organisation_type', '=', 'hygenist')
                                                        ->get();
                        foreach($orgMemberships as $orgMember)
                        {
                            $permissions = $this->getSpecificPermissions($orgMember->security_group);
                            
                            if(in_array("hygenists:view", $permissions))
                            {
                                $hygenist = Profile::find($orgMember->organisation_id);
                                if(is_object($hygenist))
                                {
                                    if($hygenist->provider_type == 'h')
                                    {
                                        if(!in_array($hygenist->id, $hygenistCheckArray))
                                        {
                                            $hygenistsArray[$h] = $hygenist;
                                            $hygenistCheckArray[$h] = $hygenist->id;
                                            $h++;
                                        }
                                    }   
                                }
                            }
                        }
                    }
                    
                }
                else 
                {
                    $hygenistsArray = profile::where('archived', '=', 0)->where('type', '=', 'hygenist')->where('provider_type', '=', 's')->orderBy('name', 'asc')->get();
                }        
            }

            return $hygenistsArray;
        }


        public function displayHygenists($alert, $type)
        {
            //Type denotes what kind of hygenist we have here.  
            //Because hygenists and service providers have very similar functions, we are just using a flag to seperate them.

            $standardDisplay = Controller::standardDisplay();
            if($type == 0)
            {
                $breadcrumbs = Controller::createBreadcrumbs("Hygenists", NULL);  
            }
            else
            {
                $breadcrumbs = Controller::createBreadcrumbs("Providers", NULL);  
            }
            
            $hygenistsArray = $this->getUserProviders($standardDisplay['profile'], $type);
            
            return view('people.hygenists', [
                'breadcrumbs' => $breadcrumbs,
                'alert' => $alert,
                'standardDisplay' => $standardDisplay,
                'hygenists' => $hygenistsArray,
                'type' => $type,
            ]);
        }

        public function editHygenist($hygenist, $type)
        {
            $standardDisplay = $this->checkFunctionPermission("hygenists:edit");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            if($standardDisplay['profile']->super_user == 0)
            {
                $profileChecker = 0;
                $userProfiles = $this->getUserProviders($standardDisplay['profile'], $type);
                foreach($userProfiles as $thisProfile)
                {
                    if($thisProfile->id == $hygenist)
                    {
                        $profileChecker = 1;
                    }
                }
                if($profileChecker == 0)
                {
                    $index = new HomeController();
                    $alert = "You do not have privileges to do that.";

                    return $index->displayDashboard($alert);
                }
            }

            
            if($hygenist == 0)
            {
                $hygenist = new Profile();
                $hygenist->id = 0;
                $hygenist->name = "New";
                $hygenist->type = "hygenist";
            }
            else
            {
                $hygenist = Profile::find($hygenist);
            }

            if($type == 0)
            {
                $breadcrumbs = Controller::createBreadcrumbs("Hygenists", $hygenist->name);  
            }
            else
            {
                $breadcrumbs = Controller::createBreadcrumbs("Providers", $hygenist->name);  
            }    
            $memberships = Membership::where('organisation_id', '=', $hygenist->id)->get();

            if($hygenist->id > 0)
            {
                $sites = Site::where('hygenist_id', '=', $hygenist->id)->get();
            }
            else
            {
                $sites = array();
            }

            $headings = array();

            $countControls = 0;
            $countZones = 0;
            foreach($sites as $site)
            {
                $controls = Control::where('current_site', '=', $site->id)->count();
                $countControls = $countControls + $controls;

                $zones = Sites_Maps_Zone::where('site_id', '=', $site->id)->count();
                $countZones = $countZones + $zones;
            }

            $securityGroups = Security_Groups::where('type', '=', 'hygenist')->where('archived', '=', 0)->orderBy('name', 'asc')->get();
            
            if($type == 0)
            {
                $headings[0] = $countZones;
                $headings[1] = $countControls;
                $headings[2] = count($sites);
            }
            else
            {
                $allSales = Trainings_Profile::where('hygenist_id', '=', $hygenist->id)->get();
                $value = 0;
                foreach($allSales as $sale)
                {
                    $value = $value + $sale->price;
                }
                $offerings = Trainings_Hygenist::where('profile_id', '=', $hygenist->id)->count();
                
                $headings[0] = count($allSales);
                $headings[1] = $value;
                $headings[2] = $offerings;
            }
            
            
            $module = 'hygenists';
            $moduleID = $hygenist->id;
            $files = File::where('module', '=', $module)
                                        ->where('module_id', '=', $moduleID)
                                        ->get();
            $logs = $this->retrieveLogs($module, $moduleID);
            
            return view('people.editHygenist', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'files' => $files,
                'logs' => $logs,
                'headings' => $headings,
                'sites' => $sites,
                'hygenist' => $hygenist,
                'memberships' => $memberships,
                'securityGroups' => $securityGroups,
                'type' => $type,
            ]);
        }

        public function saveHygenist(Request $request, $hygenist)
        {
            $standardDisplay = $this->checkFunctionPermission("hygenists:edit");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            if($hygenist == 0)
            {

                $existing = Profile::where('name', '=', $request->name)
                                        ->where('type', '=', 'hygenist')
                                        ->first();
                if(is_object($existing))
                {
                    $profile = $existing;
                    $action = "re-created";
                }
                else
                {
                    $profile = new Profile();
                    $profile->security_group = 0;
                    $profile->super_user = 0;
                    $profile->type = "hygenist";
                    $profile->member_hash = $this->generateRandomString();
                    $action = "created";
                }

                $profile->archived = 0;

                if($request->type == 0)
                {
                    $profile->provider_type = "h";
                }
                else
                {
                    $profile->provider_type = "s";
                }
                
            }
            else
            {
                $profile = Profile::find($hygenist);
                if($profile->member_hash == "")
                {
                    $profile->member_hash = $this->generateRandomString();
                }

                if($request->type == 0)
                {
                    $profile->provider_type = "h";
                }
                else
                {
                    $profile->provider_type = "s";
                }
                $action = "Edited";
            }

            $profile->name = $request->name;
            $profile->simpro_id_1 = $request->simpro_id;
            $profile->email = $request->email;
            if($request->type == 0)
            {
                $profile->primary_contact = $request->primaryContact;
            }

            $profile->phone = $request->phone;
            $profile->mobile = $request->mobile;
            $profile->address = $request->address;
            $profile->city = $request->city;
            $profile->state = $request->state;
            $profile->postcode = $request->postcode;
            $profile->tax_id = $request->tax_id;

            if(!empty($request->file('logo')))
            {
                $image = $request->file('logo')->store('images/hygenists', 'public');
                $profile->logo = $image;
            }

            $profile->save();
            $this->checkLicenseTable($profile->id);


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
                    $f->module_id = $profile->id;
                    $f->module = "hygenists";
                    
                    $f->save();
                }
            }

            if(!empty($request->securityGroups) > 0)
            {
                foreach($request->securityGroups as $key => $value)
                {
                    $membership = Membership::find($request->memberID[$key]);
                    $membership->security_group = $value;
                    $membership->save();
                }
            }


            $this->insertLog($standardDisplay['profile']->id, "hygenists", $profile->id, $action, "Hygenist profile " . $profile->name . " " . $action, "INFO");

            $alert = "Hygenist profile " . $profile->name . " saved";

            //go through and make sure this hygenist has an entry on all training courses
            $training = Training::where('archived', '=', 0)->get();
            foreach($training as $t)
            {
                $check = Trainings_Hygenist::where('training_id', '=', $t->id)
                                            ->where('profile_id', '=', $profile->id)
                                            ->first();
                if(!is_object($check))
                {
                    $new = new Trainings_Hygenist();
                    $new->training_id = $t->id;
                    $new->profile_id = $profile->id;
                    $new->profile_id = $profile->id;
                    $new->price = $t->price;
                    $new->link = $t->link;

                    $new->save();
                }
            }
            
            return $this->displayHygenists($alert, $request->type);
        }

        public function archiveHygenist($profile)
        {
            $standardDisplay = $this->checkFunctionPermission("hygenists:delete");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $action = "archived";

            $profile = Profile::find($profile);
            $profile->archived = 1;
            $profile->save();

            $thygenists = Trainings_Hygenist::where('profile_id', '=', $profile->id)->delete();

            if($profile->provider_type == "s")
            {
                $type = 1;
                $typeName = "Service provider";
            }
            else
            {
                $type = 0;
                $typeName = "Hygienist";
            }

            $alert = $typeName . " profile for " . $profile->name . " archived";

            $this->insertLog($standardDisplay['profile']->id, "hygenists", $profile->id, $action, $typeName . " profile " . $profile->name . " " . $action, "WARNING");
            
            return $this->displayHygenists($alert, $type);
        }
    






    //Contractors functions

        public function contractors()
        {
            /*
                Standard page setup
            */
            $alert = NULL;

            return $this->displayContractors($alert);
        }

        public function getUserContractors($profile)
        {
            $contractorsArray = array();
            $c = 0;

            //check to see if the user can see other users
            if($profile->super_user == 1)
            {
                $contractors = profile::where('archived', '=', 0)->where('type', '=', 'contractor')->orderBy('name', 'asc')->get();
                foreach($contractors as $contractor)
                {
                    $count = Membership::where('organisation_id', '=', $contractor->id)
                                            ->where('membership_status', '=', 'active')
                                            ->count();
                    $contractorsArray[$c]['contractor'] = $contractor;
                    $contractorsArray[$c]['count'] = $count;
                    $c++;
                }
            }
            else
            {
                //find out if the user is a member of a contractor and if so what their permissions are
                $memberships = Membership::where('user_id', '=', $profile->id)
                                            ->where('membership_status', '=', 'active')
                                            ->where('organisation_type', '=', 'contractor')
                                            ->get();
                if(count($memberships) > 0)
                {
                    foreach($memberships as $member)
                    {
                        $permissions = $this->getSpecificPermissions($member->security_group);
                        if(in_array("contractors:view", $permissions))
                        {
                            $count = Membership::where('organisation_id', '=', $member->organisation_id)
                                    ->where('membership_status', '=', 'active')
                                    ->count();
                            $contractorsArray[$c]['contractor'] = Profile::find($member->organisation_id);
                            $contractorsArray[$c]['count'] = $count;
                            $c++;
                        }
                    }
                }        
            }

            return $contractorsArray;
        }
        
        public function displayContractors($alert)
        {
            $standardDisplay = $this->checkFunctionPermission("contractors:view");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $breadcrumbs = Controller::createBreadcrumbs("Contractors", NULL);
            $contractorsArray = $this->getUserContractors($standardDisplay['profile']);            
            
            return view('people.contractors', [
                'breadcrumbs' => $breadcrumbs,
                'alert' => $alert,
                'standardDisplay' => $standardDisplay,
                'contractors' => $contractorsArray,
            ]);
        }

        public function editContractor($contractor)
        {
            $standardDisplay = $this->checkFunctionPermission("contractors:edit");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            //lets make sure they can edit this particular contractor
            if($standardDisplay['profile']->super_user == 0)
            {
                $profileChecker = 0;
                $userProfiles = $this->getUserContractors($standardDisplay['profile']);
                foreach($userProfiles as $thisProfile)
                {
                    if($thisProfile['contractor']->id == $contractor)
                    {
                        $profileChecker = 1;
                    }
                }
                if($profileChecker == 0)
                {
                    $index = new HomeController();
                    $alert = "You do not have privileges to do that.";

                    return $index->displayDashboard($alert);
                }
            }
            
            if($contractor == 0)
            {
                $contractor = new Profile();
                $contractor->id = 0;
                $contractor->name = "New";
                $contractor->type = "contractor";
            }
            else
            {
                $contractor = Profile::find($contractor);
            }

            $breadcrumbs = Controller::createBreadcrumbs("Contractors", $contractor->name);       
            
            $module = 'contractors';
            $moduleID = $contractor->id;
            $files = File::where('module', '=', $module)
                                        ->where('module_id', '=', $moduleID)
                                        ->get();
            $logs = $this->retrieveLogs($module, $moduleID);

            $history = $this->getOrganisationHistory($contractor->id);

            $memberships = Membership::where('organisation_id', '=', $contractor->id)->where('membership_status', '!=', 'inactive')->get();
            $membersOf = Membership::where('user_id', '=', $contractor->id)->where('membership_status', '!=', 'inactive')->get();
            $usersArray = array();
            $u = 0;
            foreach($memberships as $member)
            {
                $usersArray[$u] = $member->user_id;
                $u++;
            }

            $trades = Profiles_Trade::where('profiles_id', '=', $contractor->id)->get();
            $allTrades = Trade::where('archived', '=', 0)->orderBy('name', 'asc')->get();

            $permits = Permits_Profile::where('profiles_id', '=', $contractor->id)->get();
            $allPermits = Permit::where('archived', '=', 0)->orderBy('name', 'asc')->get();

            $securityGroups = Security_Groups::where('type', '=', 'contractor')->where('archived', '=', 0)->orderBy('name', 'asc')->get();

            $headings = array();
            $countAssessmentSites = Actions_Assessment::distinct('site_id')->where('active_organisation_id', '=', $contractor->id)->count();
            $countAssessments = Actions_Assessment::where('active_organisation_id', '=', $contractor->id)->count();
            $countTime = Actions_Time_Entry::where('active_organisation_id', '=', $contractor->id)->count();

            $this->checkLicenseTable($contractor->id);

            $license = array();
            foreach($contractor->Licenses as $license)
            {
                $license = $license;
            }
            
            
            $headings[0] = $countAssessmentSites;
            $headings[1] = count($membersOf);
            $headings[2] = $countAssessments + $countTime;
           
            return view('people.editContractor', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'files' => $files,
                'logs' => $logs,
                'contractor' => $contractor,
                'history' => $history,
                'memberships' => $memberships,
                'membersOf' => $membersOf,
                'trades' => $trades,
                'allTrades' => $allTrades,
                'permits' => $permits,
                'allPermits' => $allPermits,
                'headings' => $headings,
                'securityGroups' => $securityGroups,
                'usersArray' => $usersArray,
                'license' => $license
            ]);
        }

        public function saveContractor(Request $request, $contractor)
        {
            $standardDisplay = $this->checkFunctionPermission("contractors:edit");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            
            if($contractor == 0)
            {
                $profile = new Profile();
                $profile->archived = 0;
                $profile->security_group = 0;
                $profile->super_user = 0;
                $profile->member_hash = $this->generateRandomString();
                $action = "created";
            }
            else
            {
                $profile = Profile::find($contractor);
                if($profile->member_hash == "")
                {
                    $profile->member_hash = $this->generateRandomString();
                }
                $action = "Edited";
            }

            $profile->name = $request->name;
            $profile->simpro_id_1 = $request->simpro_id;
            $profile->type = "contractor";
            $profile->email = $request->email;
            $profile->primary_contact = $request->primaryContact;
            $profile->phone = $request->phone;
            $profile->address = $request->address;
            $profile->city = $request->city;
            $profile->state = $request->state;
            $profile->postcode = $request->postcode;
            $profile->tax_id = $request->tax_id;
            if($request->commencementDate)
            {
                $profile->billing_start = $this->flipDate($request->commencementDate);
            }
            else
            {
                $profile->billing_start = NULL;
            }

            if(!empty($request->file('logo')))
            {
                $image = $request->file('logo')->store('images/contractors', 'public');
                $profile->logo = $image;
            }

            $profile->save();
            $this->checkLicenseTable($profile->id);


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
                    $f->module_id = $profile->id;
                    $f->module = "contractors";
                    
                    $f->save();
                }
            }

            if(!empty($request->securityGroups) > 0)
            {
                foreach($request->securityGroups as $key => $value)
                {
                    $membership = Membership::find($request->memberID[$key]);
                    $membership->security_group = $value;
                    $membership->save();
                }
            }

            //update licenses
            $this->updateLicense($profile, $request, $standardDisplay);


            $this->insertLog($standardDisplay['profile']->id, "contractors", $profile->id, $action, "Contractor profile " . $profile->name . " " . $action, "INFO");

            $alert = "Contractor profile " . $profile->name . " saved";
            
            return $this->displayContractors($alert);
        }

        public function archiveContractor($profile)
        {
            $standardDisplay = $this->checkFunctionPermission("contractors:delete");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $action = "archived";

            $profile = Profile::find($profile);
            $profile->archived = 1;
            $profile->save();

            $alert = "Contractor profile for " . $profile->name . " archived";

            $this->insertLog($standardDisplay['profile']->id, "contractors", $profile->id, $action, "Contractor profile " . $profile->name . " " . $action, "WARNING");
            
            return $this->displayContractors($alert);
        }






    //Builders functions

        public function builders()
        {
            /*
                Standard page setup
            */
            $alert = NULL;

            return $this->displayBuilders($alert);
        }

        public function displayBuilders($alert)
        {
            $standardDisplay = $this->checkFunctionPermission("builders:view");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            $breadcrumbs = Controller::createBreadcrumbs("Builders", NULL);
            $BuildersArray = array();

            $BuildersArray = $this->getUserBuilders($standardDisplay['profile']);
            
            return view('people.builders', [
                'breadcrumbs' => $breadcrumbs,
                'alert' => $alert,
                'standardDisplay' => $standardDisplay,
                'builders' => $BuildersArray,
            ]);
        }

        public function getUserBuilders($profile)
        {
            /*
                This function returns a list of builders that the user can see
                $profile is the full Profile object
            */

            //check to see if the user can see other users
            $checkArray = array();
            $c = 0;
            $BuildersArray = array();

            if($profile->super_user == 1)
            {
                $builders = profile::where('archived', '=', 0)->where('type', '=', 'builder')->orderBy('name', 'asc')->get();
                foreach($builders as $builder)
                {
                    $count = Membership::where('organisation_id', '=', $builder->id)
                                            ->where('membership_status', '=', 'active')
                                            ->count();
                    $BuildersArray[$c]['builder'] = $builder;
                    $BuildersArray[$c]['count'] = $count;
                    $c++;
                }
            }
            else
            {
                //find out if the user is a member of a builder and if so what their permissions are
                $memberships = Membership::where('user_id', '=', $profile->id)
                                            ->where('membership_status', '=', 'active')
                                            ->where('organisation_type', '=', 'builder')
                                            ->get();
                if(count($memberships) > 0)
                {
                    foreach($memberships as $member)
                    {
                        $permissions = $this->getSpecificPermissions($member->security_group);
                        if(in_array("builders:view", $permissions))
                        {
                            $count = Membership::where('organisation_id', '=', $member->organisation_id)
                                    ->where('membership_status', '=', 'active')
                                    ->count();
                            $BuildersArray[$c]['builder'] = Profile::find($member->organisation_id);
                            $BuildersArray[$c]['count'] = $count;
                            $checkArray[$c] = $member->organisation_id;
                            $c++;
                        }
                    }
                } 

                //Now find out if the user is a member of a contractor or hygenist that is a member of the builder
                $memberships = Membership::where('user_id', '=', $profile->id)
                                            ->where('membership_status', '=', 'active')
                                            ->where('organisation_type', '!=', 'builder')
                                            ->get();
                if(count($memberships) > 0)
                {
                    foreach($memberships as $member)
                    {
                        //Get the relationships where the builder is the organisation
                        $orgMemberships = Membership::where('user_id', '=', $member->organisation_id)
                                                        ->where('membership_status', '=', 'active')
                                                        ->where('organisation_type', '=', 'builder')
                                                        ->get();
                        
                        foreach($orgMemberships as $orgMember)
                        {
                            //load up the builders Profile as thisOrg
                            $thisOrg = Profile::find($orgMember->organisation_id);

                            if(!in_array($thisOrg->id, $checkArray))
                            {
                                $permissions = $this->getSpecificPermissions($member->security_group);
                                if(in_array("builders:view", $permissions))
                                {
                                    $count = Membership::where('organisation_id', '=', $member->organisation_id)
                                            ->where('membership_status', '=', 'active')
                                            ->count();
                                    $BuildersArray[$c]['builder'] = $thisOrg;
                                    $BuildersArray[$c]['count'] = $count;
                                    $checkArray[$c] = $thisOrg->id;
                                    $c++;
                                }
                            }
                        }

                        //find out if there are any builders that are also members of this organisation
                        $orgMembersOf = Membership::where('organisation_id', '=', $member->organisation_id)
                                                        ->where('membership_status', '=', 'active')
                                                        ->where('organisation_type', '!=', 'builder')
                                                        ->get();
                        
                        foreach($orgMembersOf as $orgMemberOf)
                        {
                            //load up the builders Profile as thisOrg
                            $thisOrg = Profile::find($orgMemberOf->user_id);

                            if($thisOrg->type == "builder")
                            {
                                if(!in_array($thisOrg->id, $checkArray))
                                {
                                    $permissions = $this->getSpecificPermissions($member->security_group);
                                    if(in_array("builders:view", $permissions))
                                    {
                                        $count = Membership::where('organisation_id', '=', $member->organisation_id)
                                                ->where('membership_status', '=', 'active')
                                                ->count();
                                        $BuildersArray[$c]['builder'] = $thisOrg;
                                        $BuildersArray[$c]['count'] = $count;
                                        $checkArray[$c] = $thisOrg->id;
                                        $c++;
                                    }
                                }
                            }
                        }
                    }
                } 
            }

            return $BuildersArray;
        }

        public function editBuilder($builder)
        {
            $standardDisplay = $this->checkFunctionPermission("builders:edit");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }

            //lets make sure they can edit this particular contractor
            if($standardDisplay['profile']->super_user == 0)
            {
                $profileChecker = 0;
                $userProfiles = $this->getUserBuilders($standardDisplay['profile']);
                foreach($userProfiles as $thisProfile)
                {
                    if($thisProfile['builder']->id == $builder)
                    {
                        $profileChecker = 1;
                    }
                }
                if($profileChecker == 0)
                {
                    $index = new HomeController();
                    $alert = "You do not have privileges to do that.";

                    return $index->displayDashboard($alert);
                }
            }
            
            if($builder == 0)
            {
                $builder = new Profile();
                $builder->id = 0;
                $builder->name = "New";
                $builder->type = "builder";
            }
            else
            {
                $builder = Profile::find($builder);
            }

            $breadcrumbs = Controller::createBreadcrumbs("Builders", "Edit " . $builder->name);       
            
            $module = 'builders';
            $moduleID = $builder->id;
            $files = File::where('module', '=', $module)
                                        ->where('module_id', '=', $moduleID)
                                        ->get();
            $logs = $this->retrieveLogs($module, $moduleID);

            $history = $this->getBuilderHistory($builder->id);

            $memberships = Membership::where('organisation_id', '=', $builder->id)->where('membership_status', '!=', 'inactive')->get();
            $membersOf = Membership::where('user_id', '=', $builder->id)->where('membership_status', '!=', 'inactive')->get();
            
            $usersArray = array();
            $u = 0;
            foreach($memberships as $member)
            {
                $usersArray[$u] = $member->user_id;
                $u++;
            }

            $permits = Permits_Profile::where('profiles_id', '=', $builder->id)->get();

            $securityGroups = Security_Groups::where('type', '=', 'builder')->where('archived', '=', 0)->orderBy('name', 'asc')->get();
            $sites = Site::where('builder_id', '=', $builder->id)->where('archived', '=', 0)->get();

            //get all the permit requirement problems
            $problems = array();
            $prb = 0;
            $today = date('Y-m-d');
            foreach($sites as $site)
            {
                //get a list of all people who have been on this site
                $people = array();
                $p = 0;
                $assessments = Actions_Assessment::where('site_id', '=', $site->id)->distinct('user_id')->get();
                foreach($assessments as $ass)
                {
                    $people[$p] = $ass->user_id;
                    $p++;
                }
                $times = Actions_Time_Entry::where('site_id', '=', $site->id)->distinct('user_id')->get();
                foreach($times as $time)
                {
                    if(!in_array($time->user_id, $people))
                    {
                        if(isset($ass))
                        {
                            $people[$p] = $ass->user_id;
                            $p++;
                        }
                    }
                }

                //now we have a complete list of all the people that have visited the site, lets go through the site permit requirements and make sure everyone
                //on site / has been on site meets the requirements
                $permitRequirements = Permits_Site::where('sites_id', '=', $site->id)->get();
                foreach($people as $person)
                {
                    foreach($permitRequirements as $pr)
                    {
                        $profilePermit = Permits_Profile::where('permits_id', '=', $pr->permits_id)->where('profiles_id', '=', $person)->where('status', '=', 'approved')->get();
                        if(count($profilePermit) == 0)
                        {
                            //found a required permit for the site that the person doesn't have.
                            $problems[$prb] = $this->foundPermitProblem($person, $pr, $site, "Doesn't have permit");
                            $prb++;
                            $prb++;
                        }
                        else
                        {
                            foreach($profilePermit as $pp)
                            {
                                //they do have the permit - lets quickly make sure that it hasn't expired
                                if($today > $pp->expiry_date)
                                {
                                    $problems[$prb] = $this->foundPermitProblem($person, $pr, $site, "Permit expired");
                                    $prb++;
                                }
                            }
                            
                        }
                    }
                }
            }


            $headings = array();
            
            $con = 0;
            $ca = 0;
            $ct = 0;

            foreach($memberships as $m)
            {
                // if($m->Profile->type == "contractor")
                // {
                //     $con++;
                // }
                if(is_object($m)){
                    if($m->Profile->type == "contractor")
                    {
                        $con++;
                    }
                }
                foreach($sites as $site)
                {
                    $countAssessments = Actions_Assessment::where('user_id', '=', $m->id)->where('site_id', '=', $site->id)->count();
                    $countTime = Actions_Time_Entry::where('user_id', '=', $m->id)->where('site_id', '=', $site->id)->count();    

                    $ca = $ca + $countAssessments;
                    $ct = $ct + $countTime;
                }
                
            }

            $this->checkLicenseTable($builder->id);

            $license = array();
            foreach($builder->Licenses as $license)
            {
                $license = $license;
            }
            
            $headings[0] = count($sites);
            $headings[1] = $con;
            $headings[2] = $ca + $ct;
            
            return view('people.editBuilder', [
                'breadcrumbs' => $breadcrumbs,
                'standardDisplay' => $standardDisplay,
                'files' => $files,
                'logs' => $logs,
                'builder' => $builder,
                'history' => $history,
                'memberships' => $memberships,
                'headings' => $headings,
                'securityGroups' => $securityGroups,
                'usersArray' => $usersArray,
                'sites' => $sites,
                'problems' => $problems,
                'membersOf' => $membersOf,
                'license' => $license,
            ]);
        }

        public function foundPermitProblem($person, $pr, $site, $description)
        {
            /*
                $person is the ID of a profile
                $pr is a site permit requirement object
                $site is a site object
            */
            //found a required permit for the site that the person doesn't have.
            $problem = array();
            $membership = Membership::where('user_id', '=', $person)->where('membership_status', '=', 'active')->orderBy('id', 'desc')->first();
            if(is_object($membership))
            {
                $organisation = Profile::find($membership->organisation_id);
                $organisationName = $organisation->name;
            }
            else 
            {
                $organisationName= "-";
            }

            $person = $this->checkUserVisibility($person);

            $problem['permit'] = Permit::find($pr->permits_id);
            $problem['person'] = $organisationName . " : " . $person;
            $problem['site'] = $site->name;
            $problem['mandatory'] = $pr->mandatory;
            $problem['description'] = $description;

            return $problem;

        }

        public function saveBuilder(Request $request, $builder)
        {
            $standardDisplay = $this->checkFunctionPermission("builders:edit");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }
            
            if($builder == 0)
            {
                $profile = new Profile();
                $profile->archived = 0;
                $profile->security_group = 0;
                $profile->super_user = 0;
                $profile->member_hash = $this->generateRandomString();
                $action = "created";
            }
            else
            {
                $profile = Profile::find($builder);
                if($profile->member_hash == "")
                {
                    $profile->member_hash = $this->generateRandomString();
                }
                $action = "Edited";
            }

            $profile->name = $request->name;
            $profile->simpro_id_1 = $request->simpro_id;
            $profile->type = "builder";
            $profile->email = $request->email;
            $profile->primary_contact = $request->primaryContact;
            $profile->phone = $request->phone;
            $profile->address = $request->address;
            $profile->city = $request->city;
            $profile->state = $request->state;
            $profile->postcode = $request->postcode;
            $profile->country = $request->country;
            $profile->tax_id = $request->tax_id;
            if($request->commencementDate)
            {
                $profile->billing_start = $this->flipDate($request->commencementDate);
            }
            else
            {
                $profile->billing_start = NULL;
            }
            $profile->theme = "dark";

            if(!empty($request->file('logo')))
            {
                $image = $request->file('logo')->store('images/builders', 'public');
                $profile->logo = $image;
            }

            $profile->save();

            $this->checkLicenseTable($profile->id);

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
                    $f->module_id = $profile->id;
                    $f->module = "builders";
                    
                    $f->save();
                }
            }

            if(!empty($request->securityGroups) > 0)
            {
                foreach($request->securityGroups as $key => $value)
                {
                    $membership = Membership::find($request->memberID[$key]);
                    $membership->security_group = $value;
                    $membership->save();
                }
            }

            //update licenses
            $this->updateLicense($profile, $request, $standardDisplay);


            $this->insertLog($standardDisplay['profile']->id, "builders", $profile->id, $action, "Builder profile " . $profile->name . " " . $action, "INFO");

            $alert = "Builder profile " . $profile->name . " saved";
            
            return $this->displayBuilders($alert);
        }

        public function updateLicense($profile, $request, $standardDisplay)
        {
            $currentLicense = License_Profile::where('profile_id', '=', $profile->id)->orderBy('id', 'desc')->first();
            if($request->user_discount != $currentLicense->user_discount OR $request->hardware_discount != $currentLicense->hardware_discount OR $request->marketplace_discount != $currentLicense->marketplace_discount OR $request->billing_site != $currentLicense->site_cost)
            {
                $change = "";
                if($request->user_discount != $currentLicense->user_discount)
                {
                    $change .= "Altered user discount from $currentLicense->user_discount to $request->user_discount.  ";
                }
                if($request->hardware_discount != $currentLicense->hardware_discount)
                {
                    $change .= "Altered hardware discount from $currentLicense->hardware_discount to $request->hardware_discount.  ";
                } 
                if($request->marketplace_discount != $currentLicense->marketplace_discount)
                {
                    $change .= "Altered marketplace discount from $currentLicense->marketplace_discount to $request->marketplace_discount.  ";
                } 
                if($request->billing_site != $currentLicense->site_cost)
                {
                    $change .= "Altered site cost from $currentLicense->site_cost to $request->billing_site.  ";
                } 
                
                $newLicense = new License_Profile();

                $newLicense->profile_id = $profile->id;
                $newLicense->no_sites = $currentLicense->no_sites;
                $newLicense->no_users = $currentLicense->no_users;

                $newLicense->site_cost = $request->billing_site;
                $newLicense->hardware_discount = $request->hardware_discount;
                $newLicense->marketplace_discount = $request->marketplace_discount;
                $newLicense->user_discount = $request->user_discount;
                $newLicense->changed_by = $standardDisplay['profile']->name;
                $newLicense->changed = $change;

                $newLicense->save();
            }

            return 1;
        }

        public function checkLicenseTable($profile)
        {
            //get the defaults
            $profile = Profile::find($profile);
            if(is_object($profile))
            {
                $u = 0;

                $users = Membership::where('organisation_id', '=', $profile->id)->where('membership_status', '=', 'active')->get();
                foreach($users as $user)
                {
                    if($user->Profile->type == "user")
                    {
                        $secGroup = Security_Groups::find($user->security_group);
                        if(is_object($secGroup))
                        {
                            if($secGroup->billable == 1)
                            {
                                $u++;
                            }
                        }
                    }
                }

                if($profile->type == "builder" OR $profile->type == "contractor")
                {                    
                    $sites = Site::where('builder_id', '=', $profile->id)->count();
                }
                else
                {
                    $sites = 0;
                }

                $check = License_Profile::where('profile_id', '=', $profile->id)->orderBy('id', 'desc')->first();
                if(!is_object($check))
                {
                    $defaults = License::find(1);                
                    
                    $licenses = new License_Profile();
                    $licenses->profile_id = $profile->id;
                    $licenses->site_cost = $defaults->default_site_cost;           
                }
                else
                {
                    $licenses = $check;
                }

                $licenses->no_sites = $sites;
                $licenses->no_users = $u;
                $licenses->save();
            }

            return 1;
        }

        public function archiveBuilder($profile)
        {
            $standardDisplay = $this->checkFunctionPermission("builders:delete");
            if($standardDisplay == 0)
            {
                $index = new HomeController();
                $alert = "You do not have privileges to do that.";

                return $index->displayDashboard($alert);
            }
            
            $action = "archived";

            $profile = Profile::find($profile);
            $profile->archived = 1;
            $profile->save();

            $alert = "Builder profile for " . $profile->name . " archived";

            $this->insertLog($standardDisplay['profile']->id, "builders", $profile->id, $action, "Builder profile " . $profile->name . " " . $action, "WARNING");
            
            return $this->displayBuilders($alert);
        }
}
