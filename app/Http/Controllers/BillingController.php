<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Billings_Details;
use App\Models\Control;
use App\Models\Controls_Sites;
use App\Models\License;
use App\Models\License_Profile;
use App\Models\Profile;
use App\Models\Site;
use App\Models\Trainings_Profile;

use App\Http\Controllers\PeopleController;
use App\Http\Controllers\SimproController;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BillingController extends Controller
{
    public function calculate($testMode)
    {
        /*
            This is the main function that calculates all the billing required
        */

        /*
            Set some variables to be used throughout
        */
        $today = date('Y-m-d');
        $defaults = License::find(1);
        $peopleController = new PeopleController();

        //go through and get all the builder and contractor profiles into an array to calculate billing where they are due to start being billed
        $profiles = array();
        $x = 0;
        
        $builders = Profile::where('archived', '=', 0)
                            ->where('type', '=', 'builder')
                            ->get();
        foreach($builders as $builder)
        {
            if($today > $builder->billing_start OR empty($builder->billing_start))
            {
                $profiles[$x] = $builder->id;
                $x++;
            }
        }
        
        $contractors = Profile::where('archived', '=', 0)
                            ->where('type', '=', 'contractor')
                            ->get();
        foreach($contractors as $contractor)
        {
            if($today > $contractor->billing_start OR empty($contractor->billing_start))
            {
                $profiles[$x] = $contractor->id;
                $x++;
            }
        }

        //now start looping through all the profiles and generating their bills
        foreach($profiles as $profile)
        {
            $profile = Profile::find($profile);

            //calculate the percentage of the month to charge this profile based on their billing commencement date
            $profilePercentage = $this->calculateBillingPercentage($profile, "profile", $today);
            
            $billing = new Billing();
            $billing->profile_id = $profile->id;
            $billing->posted = 0;
            $billing->save();

            $licenses = License_Profile::where('profile_id', '=', $profile->id)->orderBy('id', 'desc')->first();
            if(!is_object($licenses))
            {
                $licenses = new License_Profile();
                
                $licenses->profile_id = $profile->id;
                $licenses->site_cost = $defaults->default_site_cost;

                $licenses->save();
            }

            $peopleController->checkLicenseTable($profile->id);
            
            //enter into this billing cycle the total value for sites
            $this->getMonthlySites($licenses, $defaults, $profilePercentage, $billing);

            //enter into this billing cycle the total value for users
            $this->getMonthlyUsers($licenses, $defaults, $profilePercentage, $billing);

            //get the marketplace transactions
            $this->getMarketPlaceTransactions($licenses, $profile, $billing);           
        }


        //go through all the controls, work out which sites they were on last month and whether they should be billed
        $controls = Control::where('archived', '=', 0)->get();
        foreach($controls as $control)
        {
            $this->getControlMonthlyCost($control);
        }

        //get the total cost of all the billings
        $billings = Billing::where('posted', '=', 0)->get();
        foreach($billings as $billing)
        {
            $total = 0;
            $details = Billings_Details::where('billing_id', '=', $billing->id)->get();
            foreach($details as $detail)
            {
                $total += $detail->cost;
            }
            $billing->total_cost = $total;
            $billing->save();
        }

        //now post all the invoices
        $this->postInvoices();
        
        return 1;
    }

    public function getLastMonth()
    {
        $currentMonth = date('n');
        $currentYear = date('Y');

        if($currentMonth == 1) {
            $lastMonth = 12;
            $lastYear = $currentYear - 1;
        }
        else {
            $lastMonth = $currentMonth -1;
            $lastYear = $currentYear;
        }

        if($lastMonth < 10) {
            $lastMonth = '0' . $lastMonth;
        }

        $lastDayOfMonth = date('t', $lastMonth);

        $lastDateOfPreviousMonth = $lastDayOfMonth . '-' . $lastMonth . '-' . $lastYear;

        $firstDateOfPreviousMonth = "01" . '-' . $lastMonth . '-' . $lastYear;

        $lastMonthRange = $firstDateOfPreviousMonth . ' - ' . $lastDateOfPreviousMonth;

        return $lastMonthRange;       
    }

    public function calculateBillingPercentage($id, $type, $endDate)
    {
        /*
            Function return a value to be used as a percentage to calculate billing values against
            $id is the full object of either the control or the profile
        */

        $today = date('Y-m-d');

        if($type == "profile")
        {
            //echo "is profile $id->id";
            $startDate = $id->billing_start;
        }
        else
        {
            //echo "is control $id->id";
            $startDate = $id->billing_commencement;
        }

        if($startDate > $endDate)
        {
            //The start date is beyond what the end date for this particular test is, therefore this is not relevant to this site
            return 1;
        }

        if(empty($startDate))
        {
            //bill for the whole month as no start date
            return 1;
        }
        else
        {
            if($today > $startDate)
            {
                //the commencement date was some time in the past
                $lastMonth = $this->getLastMonth();
                $lastMonthStart = substr($lastMonth, 0, 10);

                if($this->flipDate($lastMonthStart) > $startDate)
                {
                    //billing start date was prior to last month
                    return 1;
                }
                else
                {
                    $lastMonthLastDay = substr($lastMonth, 13, 2);

                    $startDay = substr($startDate, 8, 2);
                    $numberOfDays = $lastMonthLastDay - $startDay;

                    $percentage = $numberOfDays/$lastMonthLastDay;

                    return $percentage;
                }
            }
        }

        return 0;
    }

    public function getControlMonthlyCost($control)
    {
        /*
            This function works out the monthly cost of having the control on site.
            $control is the full control object
            $billing is the billing object we are current filling
        */

        $lastMonth = $this->getLastMonth();
        $lastDay = substr($lastMonth, 13, 2);
        $startLastMonth = substr($lastMonth, 0, 10);
        $endLastMonth = substr($lastMonth, 13, 10);
        

        //work out all the sites it was on last month
        $controlsSites = Controls_Sites::where('control_id', '=', $control->id)
                                            ->whereBetween('created_at', [$this->flipDate($startLastMonth), $this->flipDate($endLastMonth)])
                                            ->get();
        if(count($controlsSites) == 0)
        {
            //there was no movements last month, lets see if it was deployed on a site all month
            $siteID = $control->deployed;
            if($siteID > 0)
            {
                //control was deployed on this site and didn't move all month - the easiest of the scenarios
                $site = Site::find($siteID);
                if(is_object($site))
                {
                    $billing = Billing::where('profile_id', '=', $site->builder_id)->where('posted', '=', 0)->first();
                    if(is_object($billing))
                    {
                        $this->enterControlCost($siteID, $control, $billing, 1, 1, $lastDay);
                        unset($billing);
                    }
                    unset($site);
                }
            }
        }
        else
        {
            //The control has moved around
            $startDay = 1;
            $movements = array();
            $m = 0;
            $site = 0;
            $endDay = 0;

            foreach($controlsSites as $cs)
            {
                $billingPercentage = $this->calculateBillingPercentage($control, "control", $cs->created_at);

                if($m == 0)
                {
                    //This means we are looking at the first transfer for the month, so we are interested in where it is coming from (could just be transferred around on the same site)
                    $m++;
                    
                    if($cs->from_site_id == 0)
                    {
                        //its been brought out of storage
                        $startDay = substr($cs->created_at, 8, 2);
                    }
                    elseif($cs->to_site_id == 0)
                    {
                        //its been put into storage
                        $siteID = $cs->from_site_id;
                        $endDay = substr($cs->created_at, 8, 2);
                    }
                    else
                    {
                        //its been on another site
                        $siteID = $cs->from_site_id;
                        $endDay = substr($cs->created_at, 8, 2) - 1;
                    }

                    if($endDay > 0)
                    {
                        $site = Site::find($siteID);
                        if(is_object($site))
                        {
                            $billing = Billing::where('profile_id', '=', $site->builder_id)->where('posted', '=', 0)->first();
                            if(is_object($billing))
                            {
                                $this->enterControlCost($siteID, $control, $billing, $billingPercentage, $startDay, $endDay);
                                $startDay = $endDay;
                                unset($billing);
                            }
                            unset($site);
                        }
                    }
                }
                else
                {
                    //there have been multiple movements throughout the month.
                    if($cs->from_site_id == 0)
                    {
                        //its been brought out of storage
                        $startDay = substr($cs->created_at, 8, 2);
                        $endDay = 0;
                        $siteID = $cs->to_site_id;
                    }
                    elseif($cs->to_site_id == 0)
                    {
                        //its been put into storage
                        $siteID = $cs->from_site_id;
                        $endDay = substr($cs->created_at, 8, 2);
                    }
                    else
                    {
                        //its been on another site
                        $siteID = $cs->from_site_id;
                        $endDay = $startDay = substr($cs->created_at, 8, 2) - 1;
                    }

                    if($endDay > 0)
                    {
                        $site = Site::find($siteID);
                        if(is_object($site))
                        {
                            $billing = Billing::where('profile_id', '=', $site->builder_id)->where('posted', '=', 0)->first();
                            if(is_object($billing))
                            {
                                $this->enterControlCost($siteID, $control, $billing, $billingPercentage, $startDay, $endDay);
                                $startDay = $endDay;
                                unset($billing);
                            }
                            unset($site);
                        }
                    }

                }
            }
            
            if($endDay == 0)
            {
                //the last movement brought the control out of storage for the remainder of the month
                if($cs->from_site_id == 0)
                {
                    //its been brought out of storage
                    $siteID = $cs->to_site_id;
                    $site = Site::find($siteID);
                    if(is_object($site))
                    {
                        $billing = Billing::where('profile_id', '=', $site->builder_id)->where('posted', '=', 0)->first();
                        if(is_object($billing))
                        {
                            $this->enterControlCost($siteID, $control, $billing, $billingPercentage, $startDay, $lastDay);
                            unset($billing);
                        }
                        unset($site);
                    }
                }
            }
        }
        return 1;
    }

    public function enterControlCost($siteID, $control, $billing, $billingPercentage, $startDay, $lastDay)
    {
        /*
            Function calculated the percentage of time we are calculating for, the actual cost, then enters the detail into the database
        */

        $cost = 0;

        $lastMonth = $this->getLastMonth();
        $billingAmount = $control->billing_amount;
        $lastDayofMonth = substr($lastMonth, 13, 2);

        //need to get the percentage of the month the control was on site for
        $period = $lastDay - $startDay - 1;
        $period = $period / $lastDayofMonth;
        
        $frequency = $control->billing_frequency;
        if($frequency == "monthly")
        {
            $cost = ($billingAmount * $billingPercentage)*$period;
        }
        elseif($frequency == "weekly")
        {
            $cost = (($billingAmount*4.3)*$billingPercentage)*$period;
        }
        else
        {
            $cost = (($billingAmount*$lastDay)*$billingPercentage)*$period;
        }

        $site = Site::find($siteID);
        $profile = Profile::find($site->builder_id);
        if(is_object($profile))
        {
            $licenses = License_Profile::where('profile_id', '=', $profile->id)->orderBy('id', 'desc')->first();
            if($licenses->hardware_discount)
            {
                $hardwareDiscount = (100-$licenses->hardware_discount)/100;
            }
            else
            {
                $hardwareDiscount = 1;
            }

        
            if($hardwareDiscount > 0)
            {
                $cost = $cost * $hardwareDiscount;
            }

            $newDetail = new Billings_Details();
        
            $newDetail->billing_id = $billing->id;
            $newDetail->type = "controls";
            $newDetail->reference = $control->id;
            $newDetail->cost = round($cost, 2);
            $newDetail->save();
            
        }

        return 1;
    }

    public function getMarketPlaceTransactions($licenses, $profile, $billing)
    {
        /*
            Calculate market place purchases
        */
        if($licenses->marketplace_discount)
        {
            $marketplaceDiscount = (100-$licenses->marketplace_discount)/100;
        }
        else
        {
            $marketplaceDiscount = 1;
        }

        //go through all the services ordered where this was the active organisation that is not paid
        $orders = Trainings_Profile::where('active_organisation_id', '=', $profile->id)
                                    ->where('status', '=', 'completed')
                                    ->where('paid', '=', 0)
                                    ->get();
        foreach($orders as $order)
        {
            $newDetail = new Billings_Details();
            
            $newDetail->billing_id = $billing->id;
            $newDetail->type = "marketplace";
            $newDetail->reference = $order->Training->name . " for " . $order->Profile->name;
            $newDetail->cost = $order->price * $marketplaceDiscount;
            $newDetail->save();

            $order->paid = 1;
            $order->save();        
        }

    }

    public function getMonthlyUsers($licenses, $defaults, $profilePercentage, $billing)
    {
        //reverse the discount to get a percentage for the value of the users
        if($licenses->user_discount)
        {
            $userDiscount = (100-$licenses->user_discount)/100;
        }
        else
        {
            $userDiscount = 1;
        }

        if($licenses->no_users < 11)
        {
            $userCost = $defaults->user_1_10;
        }
        elseif($licenses->no_users > 10 && $licenses->no_users < 21)
        {
            $userCost = $defaults->user_11_20;
        }
        elseif($licenses->no_users > 20 && $licenses->no_users < 31)
        {
            $userCost = $defaults->user_21_30;
        }
        elseif($licenses->no_users > 30 && $licenses->no_users < 41)
        {
            $userCost = $defaults->user_31_40;
        }
        elseif($licenses->no_users > 40 && $licenses->no_users < 51)
        {
            $userCost = $defaults->user_41_50;
        }
        else
        {
            $userCost = $defaults->user_50_100;
        }

        $newDetail = new Billings_Details();
        
        $newDetail->billing_id = $billing->id;
        $newDetail->type = "users";
        $newDetail->reference = "all";
        $newDetail->cost = round((($licenses->no_users * $licenses->site_cost) * $userDiscount) * $profilePercentage, 0);
        $newDetail->save();
    }

    public function getMonthlySites($licenses, $defaults, $profilePercentage, $billing)
    {
        $newDetail = new Billings_Details();
            
        $newDetail->billing_id = $billing->id;
        $newDetail->type = "sites";
        $newDetail->reference = "all";
        $newDetail->cost = round(($licenses->no_sites * $licenses->site_cost) * $profilePercentage, 0);
        $newDetail->save();
    }

    public function postInvoices()
    {
        /*
            This function goes through all the unposted invoices and sends them to simPRO
        */
        $simpro = new SimproController();

        $invoices = Billing::where('posted', '=', 0)->get();
        foreach($invoices as $invoice)
        {
            if((isset($testMode)) > 1)
            {
                $response = $simpro->createInvoice($invoice->id);
                print_r($response);
                echo PHP_EOL;
            }

            $invoice->posted = 1;
            $invoice->save();
        }
        return 1;
    }
    
}