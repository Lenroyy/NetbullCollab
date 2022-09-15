<?php

namespace App\Http\Controllers;

use App\Models\Actions_Assessment;
use App\Models\Actions_Time_Entry;
use App\Models\Control;
use App\Models\Dashboard;
use App\Models\Email; 
use App\Models\File;
use App\Models\History;
use App\Models\Log;
use App\Models\Membership;
use App\Models\News;
use App\Models\Permits_Site;
use App\Models\Profile;
use App\Models\Security_Groups;
use App\Models\Security_Group_Details;
use App\Models\Site; 
use App\Models\Sites_Logon; 

use App\Jobs\emailSender;


use App\Http\Controllers\PeopleController;
use App\Http\Controllers\HomeController;

use Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;

date_default_timezone_set('Australia/Brisbane');

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function generateRandomString() {
        $length = 12;

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+-$#!';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        //check to make sure it doesn't already exist
        $check = Profile::where('member_hash', '=', $randomString)->count();
        if($check > 0)
        {
            $randomString = $this->generateRandomString();
        }

        return $randomString;
    }

    public function checkPermission($module, $page, $permissions)
    {
        if(in_array($module . ":" . $page, $permissions))
        {
            return "ok";
        }
        else
        {
            return "not ok";
        }

    }

    public function checkFunctionPermission($permission)
    {
        $standardDisplay = $this->standardDisplay();
        
        //for debugging
        /*
        echo "looking for " . $permission . "<br><br>";
        print_r($standardDisplay['permissions']);
        exit;
        */

        if(!in_array($permission, $standardDisplay['permissions']))
        {
            if($permission == "sites:view")
            {
                //check view-all
                if(!in_array("sites:view-all", $standardDisplay['permissions']))
                {
                    if($standardDisplay['profile']->super_user == 0)
                    {
                        return 0;
                    }
                }
            }
            else
            {
                if($standardDisplay['profile']->super_user == 0)
                {
                    return 0;
                }
            }
        }

        return $standardDisplay;
    }

    public function resetControlCoords($control)
    {
        /*
            Function resets the x and y co-ordinates of a control
        */

        $control = Control::find($control);
        $control->x = 100;
        $control->y = 100;
        $control->save();

        return 1;
    }

    public function getSpecificPermissions($group)
    {
        //create an array of all the permissions this group has
        //$group is the ID of the security group
        $permissions = array();
        $x = 0;
        
        //create an array of all the permissions this group has
        $permissions = array();
        $x = 0;
        
        $sPermisssions = security_group_details::where('security_group_id', '=', $group)->get();
        foreach($sPermisssions as $p)
        {
            $permissions[$x] = $p->module . ":" . $p->action;
            
            $x++;
        }
        

        return $permissions;
    }

    public function getPermissions($profile) 
    {
        //create an array of all the permissions this group has
        //$profile is the full StandardDisplay['profile'] object
        $permissions = array();
        $x = 0;
        
        //create an array of all the permissions this group has
        $permissions = array();
        $x = 0;
        
        $group = Security_Groups::find($profile->security_group);
        if(is_object($group) && $group->archived == 0)
        {
            $sPermisssions = security_group_details::where('security_group_id', '=', $profile->security_group)->get();
            foreach($sPermisssions as $p)
            {
                $permissions[$x] = $p->module . ":" . $p->action;
                $x++;
            }
        }
        
        

        //get all active memberships and the associated permissions
        $memberships = Membership::where('user_id', '=', $profile->id)->where('membership_status', '=', 'active')->get();
        foreach($memberships as $membership)
        {
            $group = Security_Groups::find($membership->security_group);
            if(is_object($group) && $group->archived == 0)
            {
                $sPermisssions = security_group_details::where('security_group_id', '=', $membership->security_group)->get();
                foreach($sPermisssions as $p)
                {
                    $permissions[$x] = $p->module . ":" . $p->action;
                    
                    $x++;
                }
            }
        }
        

        return $permissions;
    }

    public function getProfileHistory($profile)
    {
        $historyArray = array();
        $h = 0;

        $history = Actions_Assessment::where('user_id', '=', $profile)->orderBy('created_at', 'asc')->get();
        foreach($history as $hi)
        {
            $historyArray[$h] = $hi;
            $h++;
        }
        $history = Actions_Time_Entry::where('user_id', '=', $profile)->orderBy('created_at', 'asc')->get();
        foreach($history as $hi)
        {
            $historyArray[$h] = $hi;
            $h++;
        }
        /*
        $history = Sites_Logon::where('profile_id', '=', $profile)->orderBy('created_at', 'asc')->get();
        foreach($history as $hi)
        {
            $historyArray[$h] = $hi;
            $h++;
        }
        */

        return $historyArray;
    }

    public function getOrganisationHistory($organisation)
    {
        $historyArray = array();
        $h = 0;

        $history = Actions_Assessment::where('active_organisation_id', '=', $organisation)->orderBy('created_at', 'asc')->get();
        foreach($history as $hi)
        {
            $historyArray[$h] = $hi;
            $h++;
        }
        $history = Actions_Time_Entry::where('active_organisation_id', '=', $organisation)->orderBy('created_at', 'asc')->get();
        foreach($history as $hi)
        {
            $historyArray[$h] = $hi;
            $h++;
        }

        return $historyArray;
    }

    public function getBuilderHistory($builder)
    {
        $historyArray = array();
        $h = 0;

        $sites = Site::where('builder_id', '=', $builder)->get();
        foreach($sites as $site)
        {
            $history = Actions_Assessment::where('site_id', '=', $site->id)->orderBy('created_at', 'asc')->get();
            foreach($history as $hi)
            {
                $historyArray[$h] = $hi;
                $h++;
            }
            $history = Actions_Time_Entry::where('site_id', '=', $site->id)->orderBy('created_at', 'asc')->get();
            foreach($history as $hi)
            {
                $historyArray[$h] = $hi;
                $h++;
            }
        }
        

        return $historyArray;
    }

    public function createBreadcrumbs($name, $label)
    {
        $breadcrumbs = "<div class=\"col-lg-9 col-sm-8 col-md-8 col-xs-12\">";
            $breadcrumbs = $breadcrumbs . "<ol class=\"breadcrumb\">";
                $breadcrumbs = $breadcrumbs . "<li><a href=\"/home\">Dashboard</a></li>";

                if($name == "Dashboard")
                {
                    $breadcrumbs = $breadcrumbs;
                }      

                if($name == "Trades")
                {
                    
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/setup/trades/0\">Trades</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Trades</li>";
                    }
                }

                if($name == "Services")
                {
                    
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/setup/training\">Setup services</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Setup services</li>";
                    }
                } 
                
                if($name == "Permits")
                {
                    
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/setup/permits\">Entry requirements</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Entry requirements</li>";
                    }
                } 

                if($name == "Activities")
                {
                    
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/setup/activities\">Activities</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Activities</li>";
                    }
                } 

                if($name == "Hazards")
                {
                    
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/setup/hazards/0\">Hazards</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Hazards</li>";
                    }
                } 

                if($name == "Samples")
                {
                    
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/setup/samples/0\">Samples</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Samples</li>";
                    }
                } 

                if($name == "News")
                {
                    
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/setup/news\">News</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">News</li>";
                    }
                } 

                if($name == "Control types")
                {
                    
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/setup/controlTypes\">Control types</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Control types</li>";
                    }
                } 

                if($name == "Control")
                {
                    
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/controls\">Controls</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Controls</li>";
                    }
                } 
                

                if($name == "Assessments")
                {
                    
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/setup/assessments\">Assessments</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Assessments</li>";
                    }
                } 

                if($name == "Security group")
                {
                    
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/setup/securityGroups\">Security groups</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Security groups</li>";
                    }
                }

                if($name == "Users")
                {
                    
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/users\">Users</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Users</li>";
                    }
                }

                if($name == "Contractors")
                {
                    
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/contractors\">Contractors</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Contractors</li>";
                    }
                }

                if($name == "Builders")
                {
                    
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/builders\">Builders</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Builders</li>";
                    }
                }

                if($name == "Hygenists")
                {
                    
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/hygenists\">Hygienists</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Hygienists</li>";
                    }
                }

                if($name == "Providers")
                {
                    
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/servicePartners\">Service providers</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Service providers</li>";
                    }
                }                

                if($name == "Sites")
                {
                    
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/sites\">Sites</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Sites</li>";
                    }
                }

                if($name == "Controls")
                {
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/controls\">Controls</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Controls</li>";
                    }
                }

                if($name == "Log activity")
                {
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/logActivity\">Log activity</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Log activity</li>";
                    }
                }

                if($name == "Site SWMS")
                {
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/swms\">Site SWMS</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Site SWMS</li>";
                    }
                }

                if($name == "Site History")
                {
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/siteHistory\">Site History</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Site History</li>";
                    }
                }

                if($name == "Site Work Date")
                {
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/siteHistory\">Site Date</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Site Date</li>";
                    }
                }

                if($name == "Site person history")
                {
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/siteHistory\">Site History</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Site History</li>";
                    }
                }

                if($name == "Site asset history")
                {
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/siteHistory\">Site control history</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Site control history</li>";
                    }
                }

                if($name == "Tasks")
                {
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/tasks\">Tasks</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Tasks</li>";
                    }
                }

                if($name == "Services marketplace")
                {
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/training\">Services marketplace</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Services marketplace</li>";
                    }
                }

                if($name == "Account")
                {
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/account\">Account</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Account</li>";
                    }
                }

                if($name == "Reports")
                {
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"#\">Reports</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Reports</li>";
                    }
                }

                if($name == "Integrations")
                {
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/setup/integrations\">Integrations</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Integrations</li>";
                    }
                }

                if($name == "Run Integration")
                {
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"#\">Run Integration</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Run Integration</li>";
                    }
                }

                if($name == "Rules")
                {
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/setup/rules\">Rules</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Rules</li>";
                    }
                }

                if($name == "Exposures")
                {
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/setup/exposures\">Exposures</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Exposures</li>";
                    }
                }

                if($name == "Exposure")
                {
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/exposures\">Exposures</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Exposures</li>";
                    }
                }

                if($name == "Training types")
                {
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/setup/trainingTypes\">Service types</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Service types</li>";
                    }
                }

                if($name == "Import")
                {
                    if($label)
                    {
                        $breadcrumbs = $breadcrumbs . "<li><a href=\"/utilities/import/\">Import</a></li>";
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">" . $label . "</li>";
                    }
                    else
                    {
                        $breadcrumbs = $breadcrumbs . "<li class=\"active\">Import</li>";
                    }
                }

                

                

                

                

                

                
                

                

                

                
                
                

                
                
                

            $breadcrumbs = $breadcrumbs . "</ol>";
        $breadcrumbs = $breadcrumbs . "</div>";

        return $breadcrumbs;
    }

    public function standardDisplay()
    {
        //check to see if there is a user profile that matches this user ID
        $user = Auth::user();
        $standardDisplay = array();
        $site = 0;
        $entry = 0;
        
        /*$defaults = defaults::find(1);*/
        $news = News::where('archived', '=', 0)->orderBy('id', 'desc')->first();
        if(is_object($news))
        {
            $headline = $news;
        }
        else
        {
            $headline = new News();
            $headline->name = "No current news.";
        }
        
        $checkMatch = profile::where('user_id', '=', $user->id)->count();
        if($checkMatch == 0)
        {
            $checkMatch = profile::where('email', '=', $user->email)->first();
            if(is_object($checkMatch))
            {
                $checkMatch->user_id = $user->id;
                $checkMatch->save();
                $checkMatch = 1;
            }
        }


        if($checkMatch > 0)
        {
            $activityController = new ActivityController();

            $profile = profile::where('user_id', '=', $user->id)->first();

            //check to see if this user is currently signed into a site
            $logoff = Sites_Logon::where('profile_id', '=', $profile->id)
                                    ->whereNull('time_out')
                                    ->get();
            foreach($logoff as $lo)
            {
                $site = $lo->site_id;
            } 

            //check to see if this user has a zone they are currently logged into
            $zoneLogoff = History::where('profiles_id', '=', $profile->id)
                                    ->whereNull('time_end')
                                    ->where('type', '=', 'entry')
                                    ->get();
            foreach($zoneLogoff as $zlo)
            {
                $entry = $zlo;
            }

            //get the details from that user profile (People)
            $standardDisplay['profile'] = $profile;
            $standardDisplay['permissions'] = $this->getPermissions($profile);
            $standardDisplay['tasks'] = $activityController->getMyTasks($profile->id);            
            $standardDisplay['news'] = $headline;
            $standardDisplay['site'] = $site;
            $standardDisplay['entry'] = $entry;
        }
        else
        {
            $hash = $this->generateRandomString();
            
            //create a new profile for this user - first log in
            $new = new profile();
            $new->name = $user->name;
            $new->email = $user->email;
            $new->security_group = 8;
            $new->user_id = $user->id;
            $new->type = "user";
            // $new->theme = "dark";
            $new->theme = "light";
            $new->archived = 0;
            
            $new->save();

            // After creating a new user mail will be sent to integration with the user details


                $subject = "A new user has been added to NexTrack";
                $content = "Name " . $new->name . "<br>";
                $content .= "Email ". $new->email . "<br>";
                $content .= "Security Group ". $new->security_group . "<br>";
                $content .= "Type ". $new->type . "<br>";



                $email = new Email();
                $email->send_to = "Gigi Fleiner";
                $email->send_email = "integrations@nextrack.com.au";
                $email->subject = $subject;
                $email->content = $content;
                $email->status = "pending";
                $email->save();
    
                emailSender::dispatch();






                





            try
            {
                $new->member_hash = $hash;
                $new->save();
            }
            catch(Exception $e)
            {
                //do nothing
            }

            //check to see if this user is a kind of founder
            if(!empty($user->founder) && $user->founder != "worker")
            {
                $businessHash = $this->generateRandomString();

                $check = Profile::where('name', '=', $user->business_name)->count();
                if($check == 0)
                {
                    $business = new profile();
                    $business->archived = 0;


                    if($user->founder == "builder")
                    {
                        //create a new builder
                        $business->type = "builder";
                        $securityGroup = 5;
                    }
                    elseif($user->founder == "contractor")
                    {
                        $business->type = "contractor";
                        $securityGroup = 2;
                    }
                    elseif($user->founder == "hygenist")
                    {
                        $business->type = "hygenist";
                        $securityGroup = 6;
                        $business->provider_type = "h";
                    }
                    elseif($user->founder == "provider")
                    {
                        $business->type = "hygenist";
                        $securityGroup = 6;
                        $business->provider_type = "s";
                    }

                    $business->name = $user->business_name;
                    $business->primary_contact = $new->id;
                    $business->email = $new->email;
                    $business->super_user = 0;

                    $business->save();

                    try
                    {
                        $business->member_hash = $businessHash;
                        $business->save();
                    }
                    catch(Exception $e)
                    {
                        //do nothing
                    }

                    //link the two together in an active membership

                    $membership = new Membership();
                    $membership->user_id = $new->id;
                    $membership->organisation_id = $business->id;
                    $membership->organisation_type = $business->type;
                    $membership->security_group = $securityGroup;
                    $membership->membership_status = "active";
                    $membership->joined = date('y-m-d');
                    $membership->save();
                }
            }

            //check to see if this user is requesting a new membership
            if(!empty($user->membership_request))
            {
                //send a request to join the organisation
                $peopleController = new PeopleController();
                $peopleController->requestMembership($user->membership_request, $new->id);
            }

            //check to see if this user is currently signed into a site
            $logoff = Sites_Logon::where('profile_id', '=', $new->id)
                                    ->whereNull('time_out')
                                    ->get();
            foreach($logoff as $lo)
            {
                $site = $lo->site_id;
            }            

            $standardDisplay['permissions'] = $this->getPermissions($new);
            $standardDisplay['profile'] = $new;
            $standardDisplay['tasks'] = array();
            $standardDisplay['news'] = $headline;
            $standardDisplay['site'] = $site;
            $standardDisplay['entry'] = 0;
        }

        return $standardDisplay;        
    }

    public function insertLog($profile, $module, $module_id, $action, $notes, $log_level)
    {
        /*
            Function inserts a new log entry
            $profile is the profile ID of the person inserting the log
            $module is one of - companies, accounts.....
            $module_id is the ID of the record from the module
            $action is one of - Created, Updated, Deleted
            $notes is the text of the log entry
            $log_level is one of - INFO, WARNING, ERROR
        */
        $newLog = new Log();

        $newLog->profiles_id = $profile;
        $newLog->module = $module;
        $newLog->module_id = $module_id;
        $newLog->action = strtolower($action);
        $newLog->notes = $notes;
        $newLog->log_level = $log_level;

        $newLog->save();

        return 1;
    }

    public function retrieveLogs($module, $id)
    {
        $logs = Log::where('module', '=', $module)
                        ->where('module_id', '=', $id)
                        ->orderBy('created_at', 'desc')
                        ->get();

        return $logs;
    }

    public function uploadFiles(Request $request)
    {
        if($request->hasfile('files'))
        {
            foreach($request->file('files') as $file)
            {
                $currentMonth = date('Y-m');
                $originalName = $file->getClientOriginalName();
                $fileSize = round(($file->getSize())/1024, 0);
                $thisFile = $file->store('attachments/' . $currentMonth, 'public');

                $f = new files();
                $f->filename = $thisFile;
                $f->original_name = $originalName;
                $f->file_size = $fileSize;
                
                $f->save();
            }
        }

        $alert = "Files uploaded.";

        return $this->showUnlinked($alert);
    }

    public function deleteFile($fileId)
    {
        $standardDisplay = $this->standardDisplay();

        $file = File::find($fileId);
        $module = $file->module;
        $moduleID = $file->module_id;

        $this->insertLog($standardDisplay['profile']->id, $module, $moduleID, "File deleted", "$file->original_name deleted.", "WARNING");

        $file->delete();

        if($module == "controlTypes")
        {
            return redirect('/setup/controlType/' . $moduleID);
        }
        if($module == "controls")
        {
            return redirect('/editControl/' . $moduleID);
        }
        if($module == "permits")
        {
            return redirect('/editPermit/' . $moduleID);
        }
        if($module == "profiles")
        {
            return redirect('/editProfile/' . $moduleID);
        }
        if($module == "users")
        {
            return redirect('/editProfile/' . $moduleID);
        }
        if($module == "contractors")
        {
            return redirect('/editContractor/' . $moduleID);
        }
        if($module == "hygenists")
        {
            return redirect('/editHygenist/' . $moduleID);
        }
        if($module == "builders")
        {
            return redirect('/editBuilder/' . $moduleID);
        }
        if($module == "sites")
        {
            return redirect('/editSite/' . $moduleID);
        }
        if($module == "zones")
        {
            return redirect('/sites/zone/' . $moduleID);
        }
        if($module == "hazards")
        {
            return redirect('/sites/hazards/' . $moduleID);
        }
        if($module == "tasks")
        {
            return redirect('/editTask/' . $moduleID);
        }
        
    }

    public function checkUserVisibility($user)
    {
        $standardDisplay = $this->standardDisplay();
        $user = Profile::find($user);
        //find out the users memberships
        $userMemberships = array();
        $m = 0;
        $memberships = Membership::where('user_id', '=', $user->id)->get();
        foreach($memberships as $mem)
        {
            $userMemberships[$m] = $mem->organisation_id;
            $m++;
        }

        $viewable = 0;

        if(in_array("users:view", $standardDisplay['permissions']))
        {
            //get the logged in users memberships
            $memberships = Membership::where('user_id', '=', $standardDisplay['profile']->id)->get();
            
            foreach($memberships as $mem)
            {
                if(in_array($mem->organisation_id, $userMemberships))
                {
                    $viewable = 1;
                }
            }
        }
        if($user->id == $standardDisplay['profile']->id)
        {
            $viewable = 1;
        }
        if($standardDisplay['profile']->super_user == 1)
        {
            $viewable = 1;
        }

        if($viewable == 1)
        {
            $return = $user->name;
        }
        else
        {
            $return = $user->member_hash;
        }
        return $return;
    }

    public function calcTime($timeEntry, $format)
    {
        $timeEntry = Actions_Time_Entry::find($timeEntry);

        $time = 0;
        if(!empty($timeEntry->finish))
        {
            $startTime = strtotime($timeEntry->start);
            $finishTime = strtotime($timeEntry->finish);
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
            else
            {
                $time = ($timeInSeconds/60)/60;  //return the time in hours
            }
        }

        return $time;
    }

    public function calcHistoryTime($history, $format)
    {
        $timeEntry = History::find($history);

        $time = 0;
        if(!empty($timeEntry->time_end))
        {
            $startTime = strtotime($timeEntry->time_start);
            $finishTime = strtotime($timeEntry->time_end);
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
            else
            {
                $time = ($timeInSeconds/60)/60;  //return the time in hours
            }
        }

        return $time;
    }

    function calcAge($datetime)
    {
        /*
            Function can take a date or a datetime and work out how old it is in days
        */
        $today = time();
        
        $year = substr($datetime, 0, 4);
        $month = substr($datetime, 5, 2);
        $day = substr($datetime, 8, 2);

        $compareDate = $year . "-" . $month . "-" . $day;
        $compareDate = strtotime($compareDate);

        $datediff = $today - $compareDate;

        return round($datediff / (60 * 60 * 24));
    }

    function calcAgeMinutes($datetime)
    {
        /*
            Function can take a date or a datetime and work out how old it is in days
        */
        $today = time();
        
        $year = substr($datetime, 0, 4);
        $month = substr($datetime, 5, 2);
        $day = substr($datetime, 8, 2);
        $hour = substr($datetime, 11, 2);
        $minute = substr($datetime, 14, 2);

        $compareDate = $year . "-" . $month . "-" . $day . " " . $hour . ":" . $minute;
        $compareDate = strtotime($compareDate);

        $datediff = $today - $compareDate;

        return round($datediff / (60));
    }

    public function getWidgetOptions($type, $moduleID)
    {
        $array = array();
        $a = 0;

        //lookup the options already on this dashboard
        $widgets = Dashboard::where('type', '=', $type)->where('module_id', '=', $moduleID)->get();
        foreach($widgets as $w)
        {
            $array[$a] = $w->widget;
            $a++;
        }

        $widgetArray = array();
        $w = 0;

        //All dashboard options
            if(!in_array('url', $array))    
            {
                $widgetArray[$w]['type'] = "url";
                $widgetArray[$w]['name'] = "Thingsboard dashboard";
                $w++;
            }

            if(!in_array('url2', $array) && in_array('url', $array))
            {
                $widgetArray[$w]['type'] = "url2";
                $widgetArray[$w]['name'] = "Thingsboard dashboard";
                $w++;
            }

            if(!in_array('url3', $array) && in_array('url2', $array))
            {
                $widgetArray[$w]['type'] = "url3";
                $widgetArray[$w]['name'] = "Thingsboard dashboard";
                $w++;
            }

            if(!in_array('url4', $array) && in_array('url3', $array))
            {
                $widgetArray[$w]['type'] = "url4";
                $widgetArray[$w]['name'] = "Thingsboard dashboard";
                $w++;
            }

            if(!in_array('url5', $array) && in_array('url4', $array))
            {
                $widgetArray[$w]['type'] = "url5";
                $widgetArray[$w]['name'] = "Thingsboard dashboard";
                $w++;
            }

            if(!in_array('graph1', $array))
            {
                $widgetArray[$w]['type'] = "graph1";
                $widgetArray[$w]['name'] = "Graph widget";
                $w++;
            }

            if(!in_array('graph2', $array) && in_array('graph1', $array))
            {
                $widgetArray[$w]['type'] = "graph2";
                $widgetArray[$w]['name'] = "Graph widget";
                $w++;
            }

            if(!in_array('graph3', $array) && in_array('graph2', $array))
            {
                $widgetArray[$w]['type'] = "graph3";
                $widgetArray[$w]['name'] = "Graph widget";
                $w++;
            }

            if(!in_array('graph4', $array) && in_array('graph3', $array))
            {
                $widgetArray[$w]['type'] = "graph4";
                $widgetArray[$w]['name'] = "Graph widget";
                $w++;
            }

            if(!in_array('graph5', $array) && in_array('graph4', $array))
            {
                $widgetArray[$w]['type'] = "graph5";
                $widgetArray[$w]['name'] = "Graph widget";
                $w++;
            }

            if(!in_array('graph6', $array) && in_array('graph5', $array))
            {
                $widgetArray[$w]['type'] = "graph6";
                $widgetArray[$w]['name'] = "Graph widget";
                $w++;
            }

            if(!in_array('trafficLight1', $array))
            {
                $widgetArray[$w]['type'] = "trafficLight1";
                $widgetArray[$w]['name'] = "Traffic light widget";
                $w++;
            }
            
            if(!in_array('trafficLight2', $array) && in_array('trafficLight1', $array))
            {
                $widgetArray[$w]['type'] = "trafficLight2";
                $widgetArray[$w]['name'] = "Traffic light widget";
                $w++;
            }

            if(!in_array('trafficLight3', $array) && in_array('trafficLight2', $array))
            {
                $widgetArray[$w]['type'] = "trafficLight3";
                $widgetArray[$w]['name'] = "Traffic light widget";
                $w++;
            }

            if(!in_array('trafficLight4', $array) && in_array('trafficLight3', $array))
            {
                $widgetArray[$w]['type'] = "trafficLight4";
                $widgetArray[$w]['name'] = "Traffic light widget";
                $w++;
            }

            if(!in_array('trafficLight5', $array) && in_array('trafficLight4', $array))
            {
                $widgetArray[$w]['type'] = "trafficLight5";
                $widgetArray[$w]['name'] = "Traffic light widget";
                $w++;
            }

            if(!in_array('trafficLight6', $array) && in_array('trafficLight5', $array))
            {
                $widgetArray[$w]['type'] = "trafficLight6";
                $widgetArray[$w]['name'] = "Traffic light widget";
                $w++;
            }
        //End all dashboard options

        //Main dashboard only options
            if($type == "main")
            {
                $standardDisplay = $this->standardDisplay();

                $peopleController = new PeopleController();

                $memberOf = $peopleController->requestActiveMembership($standardDisplay['profile']);

                if(is_object($memberOf))
                {
                    if($memberOf->organisation_type == "builder" OR $memberOf->organisation_type == "contractor" OR $standardDisplay['profile']->super_user == 1)
                    {
                        if(!in_array('mySites', $array))
                        {
                            $widgetArray[$w]['type'] = "mySites";
                            $widgetArray[$w]['name'] = "My Sites";
                            $w++;
                        }

                        if(!in_array('builderSites', $array))
                        {
                            $widgetArray[$w]['type'] = "builderSites";
                            $widgetArray[$w]['name'] = "Organisations Sites";
                            $w++;
                        }
                    }
                }
                if(!in_array('myExposures', $array))
                {
                    $widgetArray[$w]['type'] = "myExposures";
                    $widgetArray[$w]['name'] = "My exposure status";
                    $w++;
                }
            }
        //End main dashboard only options

        //Site dashboard only options
            if($type == "site")
            {                
                if(!in_array('siteParticipation', $array))
                {
                    $widgetArray[$w]['type'] = "siteParticipation";
                    $widgetArray[$w]['name'] = "Site participation level";
                    $w++;
                }
            }
        //End main dashboard only options

        //Zone dashboard only options
            if($type == "zone")
            {

            }
        //End main dashboard only options

        return $widgetArray;
        
    }

    public function flipDate($date)
    {
        /*
            Function takes a date formatted as d-m-Y and flips it to Y-m-d
        */
        $year = substr($date, 6, 4);
        $month = substr($date, 3, 2);
        $day = substr($date, 0, 2);

        $newDate = $year . "-" . $month . "-" . $day;

        return $newDate;
    }

}

