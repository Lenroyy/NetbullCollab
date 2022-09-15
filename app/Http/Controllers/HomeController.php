<?php

namespace App\Http\Controllers;

use App\Models\Dashboard;
use App\Models\Profile;
use App\Models\Thingsboards_Device;

use App\Http\Controllers\PeopleController;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        /*
            Standard page setup
        */
        $alert = NULL;

        return $this->displayDashboard($alert);
    }

    public function loadDefaultDashboard($type)
    {
        /*
            This function copies one of the default dashoards to this users dashboard
        */

        $standardDisplay = Controller::standardDisplay();
        $profileName = "Dashboard " . $type;

        $defaultProfile = Profile::where('name', '=', $profileName)->first();

        if(is_object($defaultProfile))
        {
            $alert = "Default dashboard loaded";
            $currentDashboard = Dashboard::where('type', '=', 'main')->where('module_id', '=', $standardDisplay['profile']->id)->delete();

            $getDashboardWidgets = Dashboard::where('type', '=', 'main')->where('module_id', '=', $defaultProfile->id)->get();
            foreach($getDashboardWidgets as $widget)
            {
                $newDashboard = new Dashboard();

                $newDashboard->col = $widget->col;
                $newDashboard->row = $widget->row;
                $newDashboard->size_x = $widget->size_x;
                $newDashboard->size_y = $widget->size_y;
                $newDashboard->widget = $widget->widget;
                $newDashboard->content = $widget->content;
                $newDashboard->type = "main";
                $newDashboard->module_id = $standardDisplay['profile']->id;

                $newDashboard->save();
            }
        }
        else 
        {
            $alert = "Default dashboard doesn't exist";
        }

        return $this->displayDashboard($alert);
    }

    public function displayDashboard($alert)
    {
        $standardDisplay = Controller::standardDisplay();
        $breadcrumbs = Controller::createBreadcrumbs("Dashboard", NULL);      
        
        $dashboard = Dashboard::where('type', '=', 'main')->where('module_id', '=', $standardDisplay['profile']->id)->get();
        $dashboardArray = array();
        $d = 0;

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

        if($d == 0)
        {
            //setup the system default dashboard
            $newDashboard = new Dashboard();
            $newDashboard->col = 1;
            $newDashboard->row = 1;
            $newDashboard->size_x = 6;
            $newDashboard->size_y = 2;
            $newDashboard->widget = "Welcome";
            $newDashboard->content = "Welcome";
            $newDashboard->type = "main";
            $newDashboard->module_id = $standardDisplay['profile']->id;
            $newDashboard->save();

            $dashboard = Dashboard::where('type', '=', 'main')->where('module_id', '=', $standardDisplay['profile']->id)->get();

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

        $dashboardType = "main";
        $moduleID = $standardDisplay['profile']->id;
        $widgetOptions = $this->getWidgetOptions($dashboardType, $moduleID);
        
        $peopleController = new PeopleController();
        $devices = array();
        $d = 0;
        $sites = $peopleController->getUsersSites($standardDisplay['profile'], "active");
        foreach($sites as $site)
        {
            $controls = $peopleController->getSiteControls($site);
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
        }

        //print_r($devices);
        //exit;
        
        return view('dashboard.index', [
            'breadcrumbs' => $breadcrumbs,
            'alert' => $alert,
            'standardDisplay' => $standardDisplay,
            'dashboardArray' => $dashboardArray,
            'dashboardType' => $dashboardType,
            'moduleID' => $moduleID,
            'widgetOptions' => $widgetOptions,
            'devices' => $devices,
        ]);
    }

    public function saveDashboardWidget(Request $request)
    {
        if($request->widget != "--- Please Select ---")
        {
            if($request->widget_id == 0)
            {
                $new = new Dashboard();
                $new->col = 1;
                $new->row = 1;
                $new->size_x = 2;
                $new->size_y = 2;
            }
            else
            {
                $new = Dashboard::find($request->widget_id);
            }
            
            $new->widget = $request->widget;
            $new->type = $request->referrer;
            $new->module_id = $request->module_id;

            if(!empty($request->content))
            {
                $new->content = $request->content;
            }
            elseif(isset($request->graphType))
            {
                //create array, json encode it, then save it.
                $array = array();
                $array['graphType'] = $request->graphType;
                $array['period'] = $request->period;
                $array['sensor'] = $request->sensor;
                $array['readingType'] = $request->readingType;
                $array['hazardContent'] = $request->hazardContent;
                // Adding the Site ID module_ID is the site ID
                $array['module_id'] = $request->module_id;

                

                $new->content = json_encode($array);
            }
            elseif(isset($request->greenStart))
            {
                //create array, json encode it, then save it.
                $array = array();

                if($request->type == "standard")
                {
                    $array['type'] = $request->type;
                    $array['greenStart'] = $request->greenStart;
                    $array['greenEnd'] = $request->greenEnd;
                    $array['orangeStart'] = $request->orangeStart;
                    $array['orangeEnd'] = $request->orangeEnd;
                    $array['redStart'] = $request->redStart;
                    $array['average'] = $request->average;
                    $array['sensor'] = $request->sensor;
                    $array['readingType'] = $request->readingType;
                }
                else
                {
                    $array['type'] = $request->type;
                    $array['greenStart'] = $request->greenStart;
                    $array['orangeStart'] = $request->orangeStart;
                    $array['orangeEnd'] = $request->orangeEnd;
                    $array['redStart'] = $request->redStart;
                    $array['redEnd'] = $request->redEnd;
                    $array['average'] = $request->average;
                    $array['sensor'] = $request->sensor;
                    $array['readingType'] = $request->readingType;
                }

                $new->content = json_encode($array);
            }
            else
            {
                $new->content = "";
            }

            $new->save();    
        }
        
        $alert = "Dashboard widget added.";

        if($request->referrer == "main")
        {
            return $this->displayDashboard($alert);
        }
        elseif($request->referrer == "site")
        {
            $people = new PeopleController();
            return $people->editSite($request->module_id);
        }
        elseif($request->referrer == "zone")
        {
            $people = new PeopleController();
            return $people->editZone($request->module_id);
        }
    }

    public function saveWidgetLayout($type, $moduleID)
    {
        $standardDisplay = Controller::standardDisplay();
        $widgets = json_decode($_GET['widgets']);

        //first up find out if there are any Dashboards in the database that have not been presented in this set - if not then delete it.
        $dashboards = dashboard::where('type', '=', $type)->where('module_id', '=', $moduleID)->get();
        foreach($dashboards as $dbs)
        {
            $found = 0;
            foreach($widgets as $widget)
            {
                $IDlen = strlen($widget->id);
                $id = substr($widget->id, 3, $IDlen);

                if($dbs->id == $id)
                {
                    $found = 1;
                }
            }
            if($found == 0)
            {
                $dbs->delete();
            }
        }

        foreach($widgets as $widget)
        {
            //find the widget in the DB that matches this widget
            $IDlen = strlen($widget->id);
            $id = substr($widget->id, 3, $IDlen);

            $dashboard = dashboard::find($id);
            $dashboard->col = $widget->col;
            $dashboard->row = $widget->row;
            $dashboard->size_x = $widget->size_x;
            $dashboard->size_y = $widget->size_y;

            $dashboard->save();
        }
        
        return 1;
    }

    public function getSettings($dashboard)
    {
        $dashboard = substr($dashboard, 3, 100);
        $dashboard = Dashboard::find($dashboard);
        $settings = $dashboard->content;

        return $settings;
    }

    public function fixWidgets()
    {
        echo "Fixing Widgets";
        $dashboards = dashboard::get();
        foreach($dashboards as $dashboard)
        {
            if(!empty($dashboard->content))
            {
                //code to fix widgets, currently nothing wrong so no code required.
            }
        }
    }
}