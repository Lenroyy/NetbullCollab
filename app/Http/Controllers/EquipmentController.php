<?php

namespace App\Http\Controllers;

use App\Models\Control;
use App\Models\Controls_Field;
use App\Models\Controls_Orders;
use App\Models\Controls_Sites;
use App\Models\Controls_Type;
use App\Models\Controls_Type_Field;
use App\Models\Site;

use App\Http\Controllers\PeopleController;
use App\Http\Controllers\SimproController;

use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    



     //Controls

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
        $standardDisplay = Controller::standardDisplay();
        $breadcrumbs = Controller::createBreadcrumbs("Controls", NULL);

        $sitesArray = array();
        $x = 0;

        $peopleController = new PeopleController();

        $mySites = $peopleController->getUsersSites($standardDisplay['profile'], "active");
        foreach($mySites as $mySite)
        {
            $sites = Control::where('current_site', '=', $mySite->id)
                                ->where('archived', '=', 0)
                                ->where('current_site', '>', 0)->limit(1)
                                ->get();

            foreach($sites as $site)
            {
                $sitesArray[$x] = Site::find($site->current_site);
                $x++;
            }
        }
        

        
        return view('equipment.sites', [
            'breadcrumbs' => $breadcrumbs,
            'alert' => $alert,
            'standardDisplay' => $standardDisplay,
            'sites' => $sitesArray,
        ]);
    }

    public function controls($site)
    {
        /*
            Standard page setup
        */
        $alert = NULL;

        return $this->displayControls($site, $alert);
    }

    public function displayControls($site, $alert)
    {
        $standardDisplay = $this->checkFunctionPermission("controls:view");
        if($standardDisplay == 0)
        {
            $index = new HomeController();
            $alert = "You do not have privileges to do that.";

            return $index->displayDashboard($alert);
        }

        $site = Site::find($site);
        $breadcrumbs = Controller::createBreadcrumbs("Controls", $site->name);

        $peopleController = new PeopleController();
        $controls = $peopleController->getSiteControls($site);
        
        return view('equipment.controls', [
            'breadcrumbs' => $breadcrumbs,
            'alert' => $alert,
            'standardDisplay' => $standardDisplay,
            'controls' => $controls,
        ]);
    }

    public function orderControl($order)
    {
        $alert = NULL;
        return $this->showOrderControl($alert, $order);
    }

    public function showOrderControl($alert, $order)
    {
        $standardDisplay = $this->checkFunctionPermission("controls:order");
        if($standardDisplay == 0)
        {
            $index = new HomeController();
            $alert = "You do not have privileges to do that.";

            return $index->displayDashboard($alert);
        }

        $breadcrumbs = Controller::createBreadcrumbs("Controls", "Order");

        if($order == 0)
        {
            $order = new Controls_Orders();
            $order->id = 0;
        }
        else
        {
            $order = Controls_Orders::find($order);
        }

        $peopleController = new PeopleController();
        $organisation = $peopleController->requestActiveMembership($standardDisplay['profile']);
        

        if(is_object($organisation))
        {
            $orders = Controls_Orders::where('archived', '=', 0)
                                        ->where('organisation_id', '=', $organisation->organisation_id)
                                        ->get();
        }
        else 
        {
            $orders = array();
        }

        $controlTypes = Controls_Type::where('archived', '=', 0)->orderBy('name', 'asc')->get();
        $sites = $peopleController->getUsersSites($standardDisplay['profile'], "active");


        
        return view('equipment.orderControl', [
            'breadcrumbs' => $breadcrumbs,
            'alert' => $alert,
            'standardDisplay' => $standardDisplay,
            'order' => $order,
            'orders' => $orders,
            'controlTypes' => $controlTypes,
            'sites' => $sites,
        ]);
    }

    public function saveOrder(Request $request, $return)
    {
        $standardDisplay = $this->checkFunctionPermission("controls:order");
        if($standardDisplay == 0)
        {
            $index = new HomeController();
            $alert = "You do not have privileges to do that.";

            return $index->displayDashboard($alert);
        }
        
        $peopleController = new PeopleController();

        if($request->order == 0)
        {
            $organisation = $peopleController->requestActiveMembership($standardDisplay['profile']);

            if(is_object($organisation))
            {
                $order = new Controls_Orders();
                $order->archived = 0;
                $order->user_id = $standardDisplay['profile']->id;
                $order->organisation_id = $organisation->organisation_id;

                $alert = "Order created.";
            }
            else
            {
                $alert = "Can't create an order as you do not belong to an organisation.";
                return $this->showOrderControl($alert, 0);
            }            
        }
        else
        {
            $order = Controls_Orders::find($request->order);

            $alert = "Order updated.";
        }

        $order->control_type = $request->controlType;
        $order->quantity = $request->quantity;
        $order->date_due = $request->date;
        $order->order_no = $request->orderNo;
        $order->notes = $request->notes;
        $order->site_id = $request->site;
        
        $order->save();

        $simproController = new SimproController();

        if($order->simpro_id > 0)
        {
            //update an existing job in simPRO
            $simproController->updateOrder($order->id);
        }
        else 
        {
            //create a job to order the gear in simPRO    
            $simproID = $simproController->controlOrderJob($order->id);
            $order->simpro_id = $simproID;
            $order->save();
        }

        if($return == "equipment")
        {
            return $this->showOrderControl($alert, 0);
        }
        else 
        {

            return $peopleController->editSite($order->site_id);
        }
        
    }

    public function removeOrder(Request $request)
    {
        $standardDisplay = $this->checkFunctionPermission("controls:order");
        if($standardDisplay == 0)
        {
            $index = new HomeController();
            $alert = "You do not have privileges to do that.";

            return $index->displayDashboard($alert);
        }
        
        $peopleController = new PeopleController();
        $organisation = $peopleController->requestActiveMembership($standardDisplay['profile']);
        $organisationID = 0;

        if(is_object($organisation))
        {
            $organisationID = $organisation->organisation_id;
        }
        if($organisationID == 0)
        {
            $site = Site::find($request->site);
            $organisationID = $site->builder_id;
        }

        
        if($organisationID > 0)
        {            
            $order = new Controls_Orders();
            $order->archived = 0;
            $order->user_id = $standardDisplay['profile']->id;
            $order->organisation_id = $organisationID;

            $alert = "Remove order created.";

            $order->control_type = 0;
            $order->quantity = 0;
            $order->date_due = date('Y-m-d');
            //$order->order_no = $request->orderNo;
            $notes = "Removing the following controls from site<br>";
            

            if (is_array($request->controlsSelected) || is_object($request->controlsSelected)){

                foreach($request->controlsSelected as $control)
                {
                    $control = Control::find($control);
    
                    $notes .= $control->Controls_Type->name . " with serial number " . $control->serial . "<br>";
                }
            }
            $order->notes = $notes;
            $order->site_id = $request->site;
            
            $order->save();

            $simproController = new SimproController();

            //create a job to remove the control in simPRO    
            $simproID = json_decode($simproController->controlOrderJob($order->id));
            foreach($simproID as $id)
            {
                $thisID = $id;
                break;
            }
            
            $order->simpro_id = $thisID;
            $order->save();

            return $peopleController->editSite($order->site_id);
        }
        else
        {
            $alert = "Can't create an order to remove as you do not belong to an organisation.";
            return $this->showOrderControl($alert, 0);
        }            
        
    }

    
}