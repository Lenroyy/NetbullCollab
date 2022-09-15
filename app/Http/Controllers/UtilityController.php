<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\License_Profile;
use App\Models\Profile;
use App\Models\Membership;
use App\Models\Site;
use App\Models\Control;
use App\Models\Controls_Type;
use App\Models\Training;
use App\Models\Trainings_Profile;

use App\Http\Controllers\PeopleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\HomeController;

use Illuminate\Http\Request;

class UtilityController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    



     //API


    public function api()
    {
        $standardDisplay = Controller::standardDisplay();
        $breadcrumbs = Controller::createBreadcrumbs("API Documentation", NULL);        
        
        return view('dashboard.index', [
            'breadcrumbs' => $breadcrumbs,
            'alert' => $alert,
            'standardDisplay' => $standardDisplay,
        ]);
    }



    //Import

    public function importInvites()
    {
        $standardDisplay = $this->checkFunctionPermission("utilities:import");
        $breadcrumbs = Controller::createBreadcrumbs("Import", "Bulk invites");

        $memberships = Membership::where('user_id', '=', $standardDisplay['profile']->id)->get();
        
        return view('utilities.importInvites', [
            'breadcrumbs' => $breadcrumbs,
            'standardDisplay' => $standardDisplay,
            'memberships' => $memberships,
        ]);
    }

    public function processImportInvites(Request $request)
    {
        /*
            This function imports a CSV, column A is name, column B is email address and does a bulk invite
            The requestor is the organisation from which the import is being initiated from (as the user may be a member of multiple organisations)
        */

        $standardDisplay = $this->checkFunctionPermission("utilities:import");
        $x = 0;
        $peopleController = new PeopleController();

        if(!empty($request->file('fileinput')))
        {
            $path = $request->file('fileinput')->store('files', 'public');
            $fileName = substr($path, 6, 100);
        }

        if(isset($fileName))
        {
            ini_set('auto_detect_line_endings',TRUE);
            $handle = fopen('storage/files/' . $fileName,'r');
            
            while ( ($data = fgetcsv($handle) ) !== FALSE ) 
            {
                if($x > 0)
                {
                    $name = TRIM($data['0']);
                    $address = TRIM($data['1']);

                    $email = new Request();
                    $email->email = $address;
                    $email->profileID = $request->requestor;
                    $email->name = $name;
                    $email->send_email = $address;
                    $email->notes = "";
                    $email->fromImport = 1;

                    $peopleController->sendInvitation($email);

                    unset($name);
                    unset($address);
                    unset($email);
                }
                $x++;
            }
        }
        $home = new HomeController();
        $alert = "Import is processing and invitations are being sent.";
        return $home->displayDashboard($alert);

    }



    //Export

    public function export()
    {
        $standardDisplay = Controller::standardDisplay();
        $breadcrumbs = Controller::createBreadcrumbs("Export", NULL);        
        
        return view('utilities.export', [
            'breadcrumbs' => $breadcrumbs,
            'standardDisplay' => $standardDisplay,
        ]);
    }



    //Billing


    public function account()
    {
        
        /*

        THIS FUNCTION NEEDS TO BE COMPLETED REWRITTEN TO SUPPORT THE NEW BILLING FUNCTIONALITY
        

        $standardDisplay = Controller::standardDisplay();

        $breadcrumbs = Controller::createBreadcrumbs("Account", NULL);
        $reportController = new ReportController();
        $range = $this->getLastMonth();
        $total = 0;
        
        //lookup what this user is a member of to see who's licenses they are looking at
        $membership = Membership::where('membership_status', '=', 'active')
                                    ->where('user_id', '=', $standardDisplay['profile']->id)
                                    ->first();
        if(!is_object($membership))
        {
            $index = new HomeController();
            $alert = "You do not have access to any organisations licenses.";

            return $index->displayDashboard($alert);
        }

        $organisation = Profile::find($membership->organisation_id);
        $licenses = License_Profile::where('profile_id', '=', $organisation->id)->first();

        if(!is_object($licenses))
        {
            $index = new HomeController();
            $alert = $organisation->name . " does not have any license entries.  Open their profile and submit to force a refresh.";

            return $index->displayDashboard($alert);
        }

        $total = $total + ($licenses->no_sites * $licenses->site_cost);
        $total = $total + ($licenses->no_users * $licenses->user_cost);

        //go through all the sites belonging to this profile and get all the controls along with their monthly cost.
        $controlsArray = array();
        $c = 0;
        $types = Controls_type::where('archived', '=', 0)->orderBy('name', 'asc')->get();
        foreach($types as $type)
        {
            $sites = Site::where('builder_id', '=', $organisation->id)->get();
            foreach($sites as $site)
            {
                //find all the controls currently on this site
                $countControls = Control::where('current_site', '=', $site->id)->where('controls_type_id', '=', $type->id)->count();
                $controls = Control::where('current_site', '=', $site->id)->where('controls_type_id', '=', $type->id)->get();
                if($countControls > 0)
                {
                    $controlsArray[$c]['type'] = $type;
                    $controlsArray[$c]['qty'] = $countControls;
                    $c++;

                    foreach($controls as $control)
                    {
                        $bill = $reportController->getControlSiteBill($control, $range, $site->id);
                        $total = $total + $bill;   
                    }
                }
            }    
        }        

        //go through all the services ordered where this was the active organisation that is not paid
        $trainingArray = array();
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
            }
        }
        
        return view('utilities.account', [
            'breadcrumbs' => $breadcrumbs,
            'standardDisplay' => $standardDisplay,
            'organisation' => $organisation,
            'licenses' => $licenses,
            'controls' => $controlsArray,
            'services' => $trainingArray,
            'total' => $total,
        ]);
        */
    }

}