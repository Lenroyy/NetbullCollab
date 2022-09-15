<?php

namespace App\Http\Controllers;

use App\Models\Actions_Assessments_Question;
use App\Models\Actions_Control;
use App\Models\Api;
use App\Models\Billing;
use App\Models\Billings_Details;
use App\Models\Control;
use App\Models\Controls_Field;
use App\Models\Controls_Orders;
use App\Models\Controls_Type;
use App\Models\Controls_Type_Field;
use App\Models\Cost_Center;
use App\Models\Exposure;
use App\Models\History;
use App\Models\Histories_Assessments;
use App\Models\Profile;
use App\Models\Reading_Rules;
use App\Models\Site;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\SetupController;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SimproController extends Controller
{   

    public function controlLogJob($control)
    {
        /*
            This function goes through the LogJob function set and creates both jobs making sure all pre-requisites are in both companies as well as the contractor order
        */

        $nextrackCompany = 2;
        $defenderCompany = 3;

        $array = array();

        //run the first log job for the Nextrack company
        $job1 = $this->logJob($nextrackCompany, $control, 1, 0);
        
        $contractorJob = $this->createContractorJob($nextrackCompany, $job1['jobID'], $job1['sectionID'], $job1['costCenterID']);
        $orderNo = $job1['jobID'] . " : " . $contractorJob;

        $job2 = $this->logJob($defenderCompany, $control, 2, $orderNo);

        $array['one'] = $job1['jobID'];
        $array['two'] = $job2['jobID'];

        return json_encode($array);
    }

    public function logJob($company, $control, $simPROID, $orderNo)
    {
        /*
            this function checks all the prerequisits then creates a job in simPRO when a new job is logged against an asset
            $company is the simPRO company ID
            $control is the ID of the control that needs the job done on it
        */

        $control = Control::find($control);
        $customerID = 0;
        $siteID = 0;
        
        
        if($control->current_site > 0)
        {
            if($simPROID == 1)
            {
                $customerID = $control->Site->builder_id;
            }
            else
            {
                $customerID = 166; //is the customer in Nextrack that relates to the Nextrack builder
            }

            $siteID = $control->current_site;
        }


        if($customerID < 1)
        {
            //There is no customer, use the entity in Nextrack for the Nextrack builder
            $customerID = 166;
        }
        if($siteID == 0)
        {
            //There is no site, use theNextrack company generic site
            $siteID = 22;  
        }
        
        $customer = $this->lookupCustomer($customerID, $company, $simPROID);
        $site = $this->lookupSite($siteID, $company, $customer, $simPROID);
        $assetType = $this->lookupAssetType($control->controls_type_id, $company, $simPROID);
        $this->lookupAssetTypeDetails($control->controls_type_id, $company, $simPROID);
        $asset = $this->lookupAsset($control->id, $company, $site, $assetType, $simPROID);
        $job = $this->createJob($customer, $site, $asset, $assetType, $control->id, $company, $simPROID, $orderNo);

        $standardDisplay = $this->standardDisplay();

        $this->insertLog($standardDisplay['profile']->id, "controls", $control->id, "Job logged", "Job " . $job['jobID'] . " logged in simPRO for this control", "INFO");

        return $job;
    }

    public function lookupCustomer($customer, $company, $simPROID)
    {
        $customer = Profile::find($customer);

        $apiController = new ApiController();
        $customerID = 0;
        $endpoint = "companies/" . $company . "/customers/companies/?CompanyName=" . urlencode($customer->name);

        if($simPROID == 1)
        {
            if(empty($customer->simpro_id_1))
            {
                $response = json_decode($apiController->apiGet("2", $endpoint, "0"));

                if(count($response) > 0)
                {
                    foreach($response as $r)
                    {
                        $customerID = $r->ID;
                    }
                }
                else {
                    //need to create a customer
                    $customerID = $this->createCustomer($customer, $company, $apiController);
                }
                if($customerID != 0)
                {
                    $customer->simpro_id_1 = $customerID;
                    $customer->save();
                }
            }
            else 
            {
                $customerID = $customer->simpro_id_1;
            }
        }
        else
        {
            if(empty($customer->simpro_id_2))
            {
                $response = json_decode($apiController->apiGet("2", $endpoint, "0"));

                if(count($response) > 0)
                {
                    foreach($response as $r)
                    {
                        $customerID = $r->ID;
                    }
                }
                else {
                    //need to create a customer
                    $customerID = $this->createCustomer($customer, $company, $apiController);
                }
                if($customerID != 0)
                {
                    $customer->simpro_id_2 = $customerID;
                    $customer->save();
                }
            }
            else 
            {
                $customerID = $customer->simpro_id_2;
            }
        }

        return $customerID;
        
    }

    public function createCustomer($customer, $company, $apiController)
    {
        $endpoint = "companies/" . $company . "/customers/companies/?createSite=true";
        $customerID = 0;

        $address['Address'] = $customer->address;
        $address['City'] = $customer->city;
        $address['State'] = $customer->state;
        $address['PostalCode'] = $customer->postcode;
        $address['Country'] = $customer->country;

        $params['CompanyName'] = $customer->name;
        $params['CustomerType'] = "Customer";
        $params['Address'] = $address;
        $params['Email'] = $customer->email;
        $params['Phone'] = $customer->phone;
        $params['AltPhone'] = $customer->mobile;
        $params['EIN'] = $customer->tax_id;

        $response = json_decode($apiController->apiPost("2", $endpoint, json_encode($params)));

        if(is_object($response))
        {
            if(isset($response->ID))
            {
                $customerID = $response->ID;
            }
            
        }
        return $customerID;        
    }

    public function lookupBillingSite($customer, $company, $apiController)
    {

        $ntCustomer = Profile::where('simpro_id_1', '=', $customer)->first();

        if(empty($ntCustomer->simpro_billing_site_id))
        {
            $apiController = new ApiController();           
            
            $endpoint = "companies/" . $company . "/customers/companies/" . $customer;

            $response = json_decode($apiController->apiGet("2", $endpoint, "0"));

            if(count($response->Sites) > 0)
            {
                $match = 0;
                foreach($response->Sites as $site)
                {
                    if($site->Name == $ntCustomer->name)
                    {
                        $match = 1;
                        $siteID = $site->ID;
                    }
                }
                if($match == 0)    
                {
                    //create the site
                    $siteID = $this->createBillingSite($ntCustomer, $company, $apiController);
                }
            }
            else
            {
                //create the site
                $siteID = $this->createBillingSite($ntCustomer, $company, $apiController);
            }

            $ntCustomer->simpro_billing_site_id = $siteID;
            $ntCustomer->save();            
        }
        else
        {
            $siteID = $ntCustomer->simpro_billing_site_id;
        }

        return $siteID;
    }

    public function createBillingSite($site, $company, $apiController)
    {
        $endpoint = "companies/" . $company . "/sites/";
        $siteID = 0;

        $address['Address'] = $site->address;
        $address['City'] = $site->city;
        $address['State'] = $site->state;
        $address['PostalCode'] = $site->postcode;
        $address['Country'] = $site->country;

        $customers[0] = $site->simpro_id_1;

        $params['Name'] = $site->name;
        $params['Address'] = $address;
        $params['Customers'] = $customers;

        $response = json_decode($apiController->apiPost("2", $endpoint, json_encode($params)));

        if(is_object($response))
        {
            if(isset($response->ID))
            {
                $siteID = $response->ID;
            }
            
        }
        return $siteID; 
    }

    public function lookupSite($site, $company, $customer, $simPROID)
    {
        $site = Site::find($site);
        $apiController = new ApiController();
        $siteID = 0;

        $endpoint = "companies/" . $company . "/sites/?Name=" . urlencode($site->name);

        if($simPROID == 1)
        {
            if(empty($site->simpro_site_id_1))
            {
                
                $response = json_decode($apiController->apiGet("2", $endpoint, "0"));

                if(count($response) > 0)
                {
                    foreach($response as $r)
                    {
                        $siteID = $r->ID;
                    }
                }
                else {
                    //need to create a site
                    $siteID = $this->createSite($site, $company, $apiController, $customer);
                }
                if($siteID != 0)
                {
                    $site->simpro_site_id_1 = $siteID;
                    $site->save();
                }
            }
            else 
            {
                $siteID = $site->simpro_site_id_1;
            }
        }
        else
        {
            {
                if(empty($site->simpro_site_id_2))
                {
                    
                    $response = json_decode($apiController->apiGet("2", $endpoint, "0"));
    
                    if(count($response) > 0)
                    {
                        foreach($response as $r)
                        {
                            $siteID = $r->ID;
                        }
                    }
                    else {
                        //need to create a site
                        $siteID = $this->createSite($site, $company, $apiController, $customer);
                    }
                    if($siteID != 0)
                    {
                        $site->simpro_site_id_2 = $siteID;
                        $site->save();
                    }
                }
                else 
                {
                    $siteID = $site->simpro_site_id_2;
                }
            }
        }

        return $siteID;
    }

    public function createSite($site, $company, $apiController, $customer)
    {
        $endpoint = "companies/" . $company . "/sites/";
        $siteID = 0;

        $address['Address'] = $site->address;
        $address['City'] = $site->city;
        $address['State'] = $site->state;
        $address['PostalCode'] = $site->postcode;
        $address['Country'] = $site->country;

        $customers[0] = $customer;

        $params['Name'] = $site->name;
        $params['Address'] = $address;
        $params['Customers'] = $customers;

        $response = json_decode($apiController->apiPost("2", $endpoint, json_encode($params)));

        if(is_object($response))
        {
            if(isset($response->ID))
            {
                $siteID = $response->ID;
            }
            
        }
        return $siteID; 
    }

    public function lookupAssetType($control, $company, $simPROID)
    {
        $controlType = Controls_Type::find($control);
        $apiController = new ApiController();
        $assetTypeID = 0;

        $endpoint = "companies/" . $company . "/setup/assetTypes/?Name=" . urlencode($controlType->name);

        if($simPROID == 1)
        {
            if(empty($controlType->simpro_asset_type_id_1))
            {    
                $response = json_decode($apiController->apiGet("2", $endpoint, "0"));

                if(count($response) > 0)
                {
                    foreach($response as $r)
                    {
                        $assetTypeID = $r->ID;
                    }
                }
                else {
                    //need to create the asset type
                    $assetTypeID = $this->createAssetType($controlType, $company, $apiController, $simPROID);
                }
                if($assetTypeID != 0)
                {
                    $controlType->simpro_asset_type_id_1 = $assetTypeID;
                    $controlType->save();
                }
            }
            else 
            {
                $assetTypeID = $controlType->simpro_asset_type_id_1;
            }
        }
        else 
        {
            if(empty($controlType->simpro_asset_type_id_2))
            {    
                $response = json_decode($apiController->apiGet("2", $endpoint, "0"));

                if(count($response) > 0)
                {
                    foreach($response as $r)
                    {
                        $assetTypeID = $r->ID;
                    }
                }
                else {
                    //need to create the asset type
                    $assetTypeID = $this->createAssetType($controlType, $company, $apiController);
                }
                if($assetTypeID != 0)
                {
                    $controlType->simpro_asset_type_id_2 = $assetTypeID;
                    $controlType->save();
                }
            }
            else 
            {
                $assetTypeID = $controlType->simpro_asset_type_id_2;
            }
        }

        return $assetTypeID;
    }

    public function lookupAssetTypeDetails($control, $company, $simPROID)
    {
        $controlType = Controls_Type::find($control);
        $costCenter = 0;
        
        $apiController = new ApiController();
        $assetTypeID = 0;

        if($simPROID == 1)
        {
            $endpoint = "companies/" . $company . "/setup/assetTypes/" . $controlType->simpro_asset_type_id_1;
        }
        else
        {
            $endpoint = "companies/" . $company . "/setup/assetTypes/" . $controlType->simpro_asset_type_id_2;
        }
        $response = json_decode($apiController->apiGet("2", $endpoint, "0"));

        if(!empty($response->JobCostCenter->ID))
        {
            $costCenter = $response->JobCostCenter->ID;
        }

        $controlType->simpro_default_cost_center_id = $costCenter;
        $controlType->save();

        return $costCenter;
    }

    public function createAssetType($assetType, $company, $apiController, $simPROID)
    {
        $endpoint = "companies/" . $company . "/setup/assetTypes/";
        $assetTypeID = 0;

        $params['Name'] = $assetType->name;

        $response = json_decode($apiController->apiPost("2", $endpoint, json_encode($params)));

        if(is_object($response))
        {
            if(isset($response->ID))
            {
                $assetTypeID = $response->ID;
                if($simPROID == 1)
                {
                    $assetType->simpro_asset_type_id_1 = $assetTypeID;
                }
                else
                {
                    $assetType->simpro_asset_type_id_2 = $assetTypeID;
                }
                $assetType->save();

                //now start creating the fields
                $this->lookupAssetTypeField($assetType, "Manufacturer", $company, $simPROID);
                $this->lookupAssetTypeField($assetType, "Serial", $company, $simPROID);

                $fields = Controls_Type_Field::where('controls_type_id', '=', $assetType->id)->where('archived', '=', 0)->get();
                foreach($fields as $field)
                {
                    $this->lookupAssetTypeField($assetType, $field->name, $company, $simPROID);
                }
            }
            
        }
        return $assetTypeID; 
    }

    public function lookupAssetTypeField($assetType, $fieldName, $company, $simPROID)
    {
        $fieldID = 0;

        if(!empty($assetType->simpro_asset_type_id))
        {
            $apiController = new ApiController();

            if($simPROID == 1)
            {
                $endpoint = "companies/" . $company . "/setup/assetTypes/" . $assetType->simpro_asset_type_id_1 . "/customFields/?Name=" . urlencode($fieldName);
            }
            else
            {
                $endpoint = "companies/" . $company . "/setup/assetTypes/" . $assetType->simpro_asset_type_id_2 . "/customFields/?Name=" . urlencode($fieldName);
            }
            $response = json_decode($apiController->apiGet("2", $endpoint, "0"));

            if(count($response) > 0)
            {
                foreach($response as $r)
                {
                    $fieldID = $r->ID;
                }
            }
            else {
                //need to create the asset type field
                $fieldID = $this->createAssetTypeField($assetType, $company, $fieldName, $apiController, $simPROID);
            }
        }

        return $fieldID;
    }

    public function createAssetTypeField($assetType, $company, $fieldName, $apiController, $simPROID)
    {
        if($simPROID == 1)
        {
            $endpoint = "companies/" . $company . "/setup/assetTypes/" . $assetType->simpro_asset_type_id_1 . "/customFields/";
        }
        else
        {
            $endpoint = "companies/" . $company . "/setup/assetTypes/" . $assetType->simpro_asset_type_id_2 . "/customFields/";   
        }
        $fieldID = 0;

        $params['Name'] = $fieldName;
        $params['Type'] = "Text";

        $response = json_decode($apiController->apiPost("2", $endpoint, json_encode($params)));

        if(is_object($response))
        {
            if(isset($response->ID))
            {
                $fieldID = $response->ID;
            }
            
        }
        return $fieldID; 
    }

    public function lookupAsset($control, $company, $site, $assetType, $simPROID)
    {
        $control = Control::find($control);

        if($simPROID == 1)
        {
            if(empty($control->simpro_asset_id_1))
            {
                $apiController = new ApiController();
                $assetID = 0;

                //need to create the asset
                $assetID = $this->createAsset($control, $company, $apiController, $site, $assetType, $simPROID);

                if($assetID != 0)
                {
                    $control->simpro_asset_id_1 = $assetID;
                    $control->save();
                }
            }
            else 
            {
                $assetID = $control->simpro_asset_id_1;
            }
        }
        else
        {
            if(empty($control->simpro_asset_id_2))
            {
                $apiController = new ApiController();
                $assetID = 0;

                //need to create the asset
                $assetID = $this->createAsset($control, $company, $apiController, $site, $assetType, $simPROID);

                if($assetID != 0)
                {
                    $control->simpro_asset_id_2 = $assetID;
                    $control->save();
                }
            }
            else 
            {
                $assetID = $control->simpro_asset_id_2;
            }
        }

        return $assetID;
    }

    public function createPrebuild($baseItem, $type, $company)
    {
        //First off we need to find the prebuild group to use
        $apiController = new ApiController();
        $endPoint = "companies/" . $company . "/prebuildGroups/?Name=" . urlencode("Nextrack");
        $response = json_decode($apiController->apiGet("2", $endPoint, "0"));
        $groupID = 0;
        $prebuildID = 0;

        if(count($response) > 0)
        {
            foreach($response as $r)
            {
                $groupID = $r->ID;
            }
        }
        else {
            //need to create the prebuild group
            $params['Name'] = "Nextrack";
            $endPoint = "companies/" . $company . "/prebuildGroups/";
            $response = json_decode($apiController->apiPost("2", $endPoint, json_encode($params)));

            if(is_object($response))
            {
                if(isset($response->ID))
                {
                    $groupID = $response->ID;
                }
                
            }
        }

        if($type == "controlType")
        {
            $amount = $baseItem->billing_amount;
            settype($amount, "float");
            settype($groupID, "integer");

            $endpoint =  "companies/" . $company . "/prebuilds/setPrice/";
            $params = array();
            $params['Group'] =  $groupID;
            $params['Name'] = $baseItem->name;
            $params['Notes'] = "Created from Nextrack";
            $params['TotalEx'] = $amount;

            $response = json_decode($apiController->apiPost("2", $endpoint, json_encode($params)));
            if(is_object($response))
            {
                if(isset($response->ID))
                {
                    $prebuildID = $response->ID;
                }
                
            }
            
        }

        return $prebuildID;
    }

    public function lookupPrebuild($order, $company, $simPROID)
    {
        if($order->control_type > 0)
        {
            $controlType = Controls_Type::find($order->control_type);

            if($simPROID == 1)
            {
                $ntpbID = $controlType->simpro_prebuild_id_1;
            }
            else
            {
                $ntpbID = $controlType->simpro_prebuild_id_2;
            }
            
            if(empty($ntpbID))
            {
                $apiController = new ApiController();
                $prebuildID = 0;

                //need to create the asset
                $prebuildID = $this->createPrebuild($controlType, "controlType", $company);

                if($prebuildID != 0)
                {
                    if($simPROID == 1)
                    {
                        $controlType->simpro_prebuild_id_1 = $prebuildID;
                    }
                    else
                    {
                        $controlType->simpro_prebuild_id_2 = $prebuildID;
                    }
                    $controlType->save();
                }
            }
            else 
            {
                $prebuildID = $ntpbID;
            }
        }
        else 
        {
            $prebuildID = 0; 
        }

        return $prebuildID;
    }

    public function lookupBillingPrebuild($invoice, $company)
    {
        $details = array();
        $api = Api::find(2);
        $defaults = json_decode($api->settings);

        if($invoice->reference > 0)
        {
            $control = Control::find($invoice->reference);

            $controlType = Controls_Type::find($control->controls_type_id);

            $ntpbID = $controlType->simpro_prebuild_id_1;
            
            if(empty($ntpbID))
            {
                $apiController = new ApiController();
                $prebuildID = 0;

                //need to create the asset
                $prebuildID = $this->createPrebuild($controlType, "controlType", $company);

                if($prebuildID != 0)
                {
                    $controlType->simpro_prebuild_id_1 = $prebuildID;
                    $controlType->save();
                }
                $details['prebuildID'] = $prebuildID;
            }
            else 
            {
                $details['prebuildID'] = $ntpbID;
            }
        }
        else 
        {
            $details['prebuildID'] = 0;
        }

        //now lets find out how this is being billed
        if($control->billing == "yes" OR $control->billing == "initial")
        {
            if($control->Controls_Type->external_lease_cost_center_id > 0)
            {
                $details['costCenter'] = $control->Controls_Type->external_lease_cost_center_id;
            }
            else
            {
                $details['costCenter'] = $defaults->external_lease;
            }
            $details['cost'] = $invoice->cost;
            
        }
        elseif($control->billing == "monitoring")
        {
            if($control->Controls_Type->monitoring_only_cost_center_id > 0)
            {
                $details['costCenter'] = $control->Controls_Type->monitoring_only_cost_center_id;
            }
            else
            {
                $details['costCenter'] = $defaults->monitoring_only;
            }
            $details['cost'] = $invoice->cost;
        }

        return $details;
    }
    

    public function createAsset($control, $company, $apiController, $site, $assetType, $simPROID)
    {
        $endpoint = "companies/" . $company . "/sites/" . $site . "/assets/";
        $assetID = 0;

        if($simPROID == 1)
        {
            $assetType = Controls_Type::where('simpro_asset_type_id_1', '=', $assetType)->first();
        }
        else
        {
            $assetType = Controls_Type::where('simpro_asset_type_id_2', '=', $assetType)->first();
        }

        //build up an array of all the custom field ID's
        $fields = array();
        $f = 0;
        
        //now start creating the fields
        $manufacturer = $this->lookupAssetTypeField($assetType, "Manufacturer", $company, $simPROID);
        if($manufacturer > 0)
        {
            $fields[$f]['ID'] = $manufacturer;
            $fields[$f]['Name'] = "Manufacturer";
            $fields[$f]['Value'] = $assetType->manufacturer;
            $f++;
        }
        $serial = $this->lookupAssetTypeField($assetType, "Serial", $company, $simPROID);
        if($serial > 0)
        {
            $fields[$f]['ID'] = $serial;
            $fields[$f]['Name'] = "Serial";
            $fields[$f]['Value'] = $control->serial;
            $f++;
        }

        $typeFields = Controls_Type_Field::where('controls_type_id', '=', $assetType->id)->where('archived', '=', 0)->get();
        foreach($typeFields as $field)
        {
            $id = $this->lookupAssetTypeField($assetType, $field->name, $company, $simPROID);
            if($id > 0)
            {
                $fields[$f]['ID'] = $id;
                $fields[$f]['Name'] = $field->name;
                $fields[$f]['Value'] = $field->value;
                $f++;
            }
            unset($id);
        }
        
        if($simPROID == 1)
        {
            $params['AssetType'] = $assetType->simpro_asset_type_id_1;
        }
        else
        {
            $params['AssetType'] = $assetType->simpro_asset_type_id_2;
        }
        
        //create the asset
        $response = json_decode($apiController->apiPost("2", $endpoint, json_encode($params)));

        if(is_object($response))
        {
            if(isset($response->ID))
            {
                //get the Asset ID
                $assetID = $response->ID;

                foreach($fields as $field)
                {
                    if(!empty($field['Value']))
                    {
                        $params = array();
                        $endPoint = "companies/" . $company . "/sites/" . $site . "/assets/" . $assetID . "/customFields/" . $field['ID'];

                        $params['Value'] = $field['Value'];

                        $response = json_decode($apiController->apiPatch("2", $endPoint, json_encode($params)));
                    }
                }
            }
            
        }
        return $assetID; 
    }

    public function createJob($customer, $site, $asset, $assetType, $control, $company, $simPROID, $orderNo)
    {
        $jobArray = array();
        if($simPROID == 1)
        {
            $costCenterName = "Maintenance";    // Will need to be updated to reflect the default cost center name in Nextrack simPRO
            $costCenterID = 10;    // Will need to be updated to reflect the default cost center ID in Nextrack simPRO
        }
        else
        {
            $costCenterName = "Nextrack";    // Will need to be updated to reflect the default cost center name in Defender simPRO
            $costCenterID = 6;    // Will need to be updated to reflect the default cost center ID in Defender simPRO
        }
        
        if($simPROID == 1)
        {
            $assetType = Controls_Type::where('simpro_asset_type_id_1', '=', $assetType)->first();
        }
        else
        {
            $assetType = Controls_Type::where('simpro_asset_type_id_2', '=', $assetType)->first();
        }

        if($assetType->simpro_default_cost_center_id > 0)
        {
            $costCenterID = $assetType->simpro_default_cost_center_id;
        }

        settype($costCenterID, "integer");

        $control = Control::find($control);
        $setupController = new SetupController();
        $currentLocation = $setupController->getControlCurrentLocation($control);
        $apiController = new ApiController();
        

        if($costCenterID == 0)
        {
            $endpoint = "companies/" . $company . "/setup/accounts/costCenters/";
            $response = json_decode($apiController->apiGet("2", $endpoint, "0"));
            foreach($response as $r)
            {
                if($r->Name == $costCenterName)
                {
                    $costCenterID = $r->ID;
                }
            }
        }

        $endpoint = "companies/" . $company . "/jobs/";
        $jobID = 0;

        $params['Type'] = "Service";
        $params['Customer'] = $customer;
        $params['Stage'] = "Pending";
        $params['Site'] = $site;
        $params['Description'] = "Job logged from Nextrack for faulty " . $assetType->name . " asset";
        if($orderNo > 0)
        {
            $params['OrderNo'] = "$orderNo";
        }
        $params['Notes'] = "Control location is " . $currentLocation . " \nUnit serial number is " . $control->serial;

        $response = json_decode($apiController->apiPost("2", $endpoint, json_encode($params)));

        if(is_object($response))
        {
            if(isset($response->ID))
            {
                $jobID = $response->ID;
                $jobArray['jobID'] = $jobID;

                $params = array();
                $params['DisplayOrder'] = 0;

                //now add the section
                $endpoint = "companies/" . $company . "/jobs/" . $jobID . "/sections/";
                $response = json_decode($apiController->apiPost("2", $endpoint, json_encode($params)));
                if(is_object($response))
                {
                    if(isset($response->ID))
                    {
                        $sectionID = $response->ID;
                        $jobArray['sectionID'] = $sectionID;

                        $params = array();
                        $params['CostCenter'] = $costCenterID;
                        $params['Description'] = "Job logged from Nextrack for faulty " . $assetType->name . " asset";
                        $params['Notes'] = "Control location is " . $currentLocation . " \nUnit serial number is " . $control->serial;

                        //now add the cost center
                        $endpoint = "companies/" . $company . "/jobs/" . $jobID . "/sections/" . $sectionID . "/costCenters/";
                        $response = json_decode($apiController->apiPost("2", $endpoint, json_encode($params)));

                        if(is_object($response))
                        {
                            if(isset($response->ID))
                            {
                                $costCenterID = $response->ID;
                                $jobArray['costCenterID'] = $costCenterID;

                                settype($asset, "integer");
                                
                                $params = array();
                                $params['Asset'] = $asset;

                                //now add the cost center
                                $endpoint = "companies/" . $company . "/jobs/" . $jobID . "/sections/" . $sectionID . "/costCenters/" . $costCenterID . "/assets/";
                                $response = json_decode($apiController->apiPost("2", $endpoint, json_encode($params)));
                            }
                        }
                    }
                }

            }
            
        }

        return $jobArray; 
    }

    public function createContractorJob($company, $job, $section, $costCenter)
    {
        $contractorJob = 0;
        $params = array();
        $apiController = new ApiController();

        $params['Contractor'] = 13;
        //$params['Contractor'] = 182;
        $params['Description'] = "Job issued from Nextrack";
        
        $endpoint = "companies/" . $company . "/jobs/" . $job . "/sections/" . $section . "/costCenters/" . $costCenter . "/contractorJobs/";
        $response = json_decode($apiController->apiPost("2", $endpoint, json_encode($params)));
        
        if(is_object($response))
        {
            //print_r($response);
            $contractorJob = $response->ID;
        }

        return $contractorJob;
    }

    public function controlOrderJob($order)
    {
        /*
            This function controls the communication with simPRO when logging a new order
        */

        $nextrackCompany = 2;
        $defenderCompany = 3;

        $array = array();
        //$nextrackCompany = 35;
        //$defenderCompany = 74;

        $array = array();

        // $job1 = $this->createOrder($order, $nextrackCompany, 1, $order->order_no);
        $job1 = $this->createOrder($order, $nextrackCompany, 1, isset($order['order_no']));
        $contractorJob = $this->createContractorJob($nextrackCompany, $job1['jobID'], $job1['sectionID'], $job1['costCenterID']);
        $orderNo = $job1['jobID'] . " : " . $contractorJob;
        $job2 = $this->createOrder($order, $defenderCompany, 2, $orderNo);
        

        $array[1] = $job1['jobID'];
        $array[2] = $job2['jobID'];
        
        return json_encode($array);
    }

    public function createPrebuldJob($order, $customer, $site, $prebuild, $company, $simPROID, $orderNo)
    {

        $jobArray = array();
        if($simPROID == 1)
        {
            $costCenterName = "Maintenance";
            $costCenterID = 10;
            //$costCenterName = "Electrical";
            //$costCenterID = 195;
        }
        else
        {
            $costCenterName = "Nextrack";
            $costCenterID = 6;    
            //$costCenterName = "Electrical";
            //$costCenterID = 213;
        }

        $apiController = new ApiController();
        
        if($costCenterID == 0)
        {
            $endpoint = "companies/" . $company . "/setup/accounts/costCenters/";
            $response = json_decode($apiController->apiGet("2", $endpoint, "0"));
            foreach($response as $r)
            {
                if($r->Name == $costCenterName)
                {
                    $costCenterID = $r->ID;
                }
            }
        }

        settype($costCenterID, "integer");
        $endpoint = "companies/" . $company . "/jobs/";
        $jobID = 0;

        if(!empty($order->date_due))
        {
            $dueDate = date('Y-m-d', strtotime($order->date_due));
        }
        else
        {
            $dueDate = NULL;
        }

        $params['Type'] = "Service";
        $params['Customer'] = $customer;
        $params['Stage'] = "Pending";
        $params['Site'] = $site;
        $params['DueDate'] = $dueDate;
        if($orderNo > 0)
        {
            $params['OrderNo'] = $orderNo;
        }
        
        if($order->control_type > 0)
        {
            $params['Description'] = "New order logged in Nextrack for " . $order->Controls_Type->name;
        }
        else
        {
            $params['Description'] = "Removal requested, order logged in Nextrack";
        }

        $params['Notes'] = "Order number in Nextrack is " . $order->id . ". " . $order->notes;

        $response = json_decode($apiController->apiPost("2", $endpoint, json_encode($params)));

        if(is_object($response))
        {
            if(isset($response->ID))
            {
                $jobID = $response->ID;
                $jobArray['jobID'] = $jobID;

                $params = array();
                $params['DisplayOrder'] = 0;

                //now add the section
                $endpoint = "companies/" . $company . "/jobs/" . $jobID . "/sections/";
                $response = json_decode($apiController->apiPost("2", $endpoint, json_encode($params)));
                if(is_object($response))
                {
                    if(isset($response->ID))
                    {
                        $sectionID = $response->ID;
                        $jobArray['sectionID'] = $sectionID;

                        $params = array();
                        $params['CostCenter'] = $costCenterID;
                        if($order->control_type > 0)
                        {
                            $params['Description'] = "New order logged in Nextrack for " . $order->Controls_Type->name;
                        }
                        else
                        {
                            $params['Description'] = "Removal requested, order logged in Nextrack";
                        }

                        $params['Notes'] = "Order number in Nextrack is " . $order->id . ". " . $order->notes;

                        //now add the cost center
                        $endpoint = "companies/" . $company . "/jobs/" . $jobID . "/sections/" . $sectionID . "/costCenters/";
                        $response = json_decode($apiController->apiPost("2", $endpoint, json_encode($params)));

                            if(is_object($response))
                            {
                                if(isset($response->ID))
                                {
                                    $costCenterID = $response->ID;
                                    $jobArray['costCenterID'] = $costCenterID;

                                    if($prebuild > 0)
                                    {

                                        settype($prebuild, "integer");
                                        
                                        $params = array();
                                        $params['Prebuild'] = $prebuild;
                                        $params['Total']['Qty'] = $order->quantity;

                                        //now add the Prebuild
                                        $endpoint = "companies/" . $company . "/jobs/" . $jobID . "/sections/" . $sectionID . "/costCenters/" . $costCenterID . "/prebuilds/";
                                        $response = json_decode($apiController->apiPost("2", $endpoint, json_encode($params)));
                                    }
                                }
                            }
                        }
                    }
                }
            
            }
        
        return $jobArray; 
    }

    public function createInvoiceJob($customer, $site, $company, $settings, $usersCost, $usersPrebuild, $sitesCost, $sitesPrebuild, $marketplaces)
    {
        $apiController = new ApiController();

        $endpoint = "companies/" . $company . "/jobs/";
        $jobID = 0;
        $jobArray = array();

        settype($customer, "integer");
        settype($site, "integer");

        $params['Type'] = "Service";
        $params['Customer'] = $customer;
        $params['Stage'] = "Pending";
        $params['Site'] = $site;
        $params['Description'] = "Monthly Nextrack subscription";

        $response = json_decode($apiController->apiPost("2", $endpoint, json_encode($params)));

        if(is_object($response))
        {
            if(isset($response->ID))
            {
                $jobID = $response->ID;
                $jobArray['jobID'] = $jobID;

                $params = array();
                $params['DisplayOrder'] = 0;

                //now add the section
                $endpoint = "companies/" . $company . "/jobs/" . $jobID . "/sections/";
                $response = json_decode($apiController->apiPost("2", $endpoint, json_encode($params)));
                if(is_object($response))
                {
                    if(isset($response->ID))
                    {
                        $sectionID = $response->ID;
                        $jobArray['sectionID'] = $sectionID;
                        $subscriptions = $settings->subscriptions;
                        $marketplace = $settings->marketplace;
                        
                        settype($subscriptions, "integer");
                        settype($marketplace, "integer");

                        //now add the users and sites subscription                        
                        $params = array();
                        $params['CostCenter'] = $subscriptions;
                        $params['Description'] = "Monthly nextrack users and sites subscription";

                        //now add the cost center
                        $endpoint = "companies/" . $company . "/jobs/" . $jobID . "/sections/" . $sectionID . "/costCenters/";
                        $response = json_decode($apiController->apiPost("2", $endpoint, json_encode($params)));

                        if(is_object($response))
                        {
                            if(isset($response->ID))
                            {
                                $costCenterID = $response->ID;

                                settype($prebuild, "integer");
                                settype($usersCost, "float");
                                settype($sitesCost, "float");
                                        
                                $params = array();
                                $params['Prebuild'] = $usersPrebuild;
                                $params['Total']['Qty'] = 1;
                                $params['SellPrice'] = $usersCost;

                                //now add the Prebuild
                                $endpoint = "companies/" . $company . "/jobs/" . $jobID . "/sections/" . $sectionID . "/costCenters/" . $costCenterID . "/prebuilds/";
                                $response = json_decode($apiController->apiPost("2", $endpoint, json_encode($params)));

                                $params = array();
                                $params['Prebuild'] = $sitesPrebuild;
                                $params['Total']['Qty'] = 1;
                                $params['SellPrice'] = $sitesCost;

                                //now add the Prebuild
                                $endpoint = "companies/" . $company . "/jobs/" . $jobID . "/sections/" . $sectionID . "/costCenters/" . $costCenterID . "/prebuilds/";
                                $response = json_decode($apiController->apiPost("2", $endpoint, json_encode($params)));
                            }
                        }

                        if(!empty($marketplaces))
                        {
                            //now add the marketplace purchases
                            $params = array();
                            $params['CostCenter'] = $marketplace;
                            $params['Description'] = "Completed marketplace purchases for the past month";

                            //now add the cost center
                            $endpoint = "companies/" . $company . "/jobs/" . $jobID . "/sections/" . $sectionID . "/costCenters/";
                            $response = json_decode($apiController->apiPost("2", $endpoint, json_encode($params)));

                            if(is_object($response))
                            {
                                if(isset($response->ID))
                                {
                                    $costCenterID = $response->ID;

                                    foreach($marketplaces as $mp)
                                    {
                                        $price = $mp['price'];

                                        settype($price, "float");

                                        $params = array();
                                        $params['Type'] = "Material";
                                        $params['BillableStatus'] = "Billable";
                                        $params['Description'] = $mp['name'];
                                        $params['EstimatedCost'] = 1;
                                        $params['ActualCost'] = 1;
                                        $params['SellPrice'] = $price;                                   
                                        $params['Total']['Qty'] = 1;   

                                        //now add the item
                                        $endpoint = "companies/" . $company . "/jobs/" . $jobID . "/sections/" . $sectionID . "/costCenters/" . $costCenterID . "/oneOffs/";
                                        $response = json_decode($apiController->apiPost("2", $endpoint, json_encode($params)));
                                    }
                                }
                            }             
                        }
                    }
                }

            }
            
        }

        return $jobArray; 
    }

    public function createInvoiceJobCostCenter($costCenter, $company, $jobID, $sectionID, $prebuilds)
    {
        //now add controls to the job
        $apiController = new ApiController();
        $params = array();
        settype($costCenter, "integer");
        settype($jobID, "integer");
        settype($sectionID, "integer");

        $params['CostCenter'] = $costCenter;
        $params['Description'] = "Monthly nextrack controls subscription";

        //now add the cost center
        $endpoint = "companies/" . $company . "/jobs/" . $jobID . "/sections/" . $sectionID . "/costCenters/";
        $response = json_decode($apiController->apiPost("2", $endpoint, json_encode($params)));

        if(is_object($response))
        {
            if(isset($response->ID))
            {
                $costCenterID = $response->ID;

                foreach($prebuilds as $prebuild)
                {
                    $pbID = $prebuild['prebuildID'];
                    $cost = $prebuild['cost'];

                    settype($pbID, "integer");
                    settype($cost, "float");
                            
                    $params = array();
                    $params['Prebuild'] = $pbID;
                    $params['Total']['Qty'] = 1;
                    $params['SellPrice'] = $cost;

                    //now add the Prebuild
                    $endpoint = "companies/" . $company . "/jobs/" . $jobID . "/sections/" . $sectionID . "/costCenters/" . $costCenterID . "/prebuilds/";
                    $response = json_decode($apiController->apiPost("2", $endpoint, json_encode($params)));
                }
            }
        }

        return 1;
    }

    public function createOrder($order, $company, $simPROID, $orderNo)
    {
        /*
            this function checks all the prerequisits then creates a job in simPRO when a new order is placed
            $order is the control order ID we'll be using
        */

        $order = Controls_Orders::find($order);

        $customer = $this->lookupCustomer($order->organisation_id, $company, $simPROID);
        //echo "<br>Customer is " . $customer;
        $site = $this->lookupSite($order->site_id, $company, $customer, $simPROID);
        //echo "<br>Site is " . $site;
        $prebuild = $this->lookupPrebuild($order, $company, $simPROID);
        //echo "<br>Prebuild is " . $prebuild;
        $job = $this->createPrebuldJob($order, $customer, $site, $prebuild, $company, $simPROID, $orderNo);
        //echo "<br>job is ";
        //print_r($job);

        return $job;
    }

    public function retrieveCostCenters()
    {
        /*
            This function retrieves all the simPRO cost centers from the database and updates the local cost centers database
            Will only look at the Nextrack and Defendaer companies

            To add others, add them to the $companyArray
        */

        $apiController = new ApiController();
        $counter = 0;

        $companyArray[1] = 2; //Nextrack
        $companyArray[2] = 3; //Defender

        
        /*SANDBOX COMPANIES*/

        //$companyArray[1] = 35; //Nextrack
        //$companyArray[2] = 74; //Defender

        foreach($companyArray as $company)
        {
            
            $endPoint = "/companies/" . $company . "/setup/accounts/costCenters/?columns=ID,Name,Archived&pageSize=250";
            $response = json_decode($apiController->apiGet(2, $endPoint, 1));

            foreach($response as $costCenter)
            {
                if($costCenter->Archived == false)
                {
                    $option = Cost_Center::where('company_id', '=', $company)
                                            ->where('cost_center_id', '=', $costCenter->ID)
                                            ->first();
                    if(is_object($option))
                    {
                        //cost center exists, update it
                        $option->cost_center_name = $costCenter->Name;
                        $option->save();
                    }
                    else
                    {
                        //it doesn't exist, lets create it
                        $new = new Cost_Center();
                        $new->company_id = $company;
                        $new->cost_center_id = $costCenter->ID;
                        $new->cost_center_name = $costCenter->Name;
                        $new->save();
                    }
                    $counter++;
                }
            }
        }
        

        return $counter;
    }

    public function createInvoice($invoice)
    {
        /*
            This function controls the communication with simPRO when logging a new subscription / monthly invoice
        */

        $nextrackCompany = 2;
        $usersBillingPrebuidID = 45;
        $sitesBillingPrebuidID = 46;

        /////////////COMMENT THESE SETTINGS WHEN FINISHED TESTING//////////////////////
        //$nextrackCompany = 35;   
        //$usersBillingPrebuidID = 13375;
        //$sitesBillingPrebuidID = 13374;
        

        $invoice = Billing::find($invoice);
        $customer = $this->lookupCustomer($invoice->profile_id, $nextrackCompany, 1);
        $apiController = new ApiController();

        $api = Api::find(2);
        $settings = json_decode($api->settings);
        if(empty($settings->marketplace))
        {
            echo "Can't process, settings are not defined";
            exit;
        }

        //get the first site for the customer
        $site = $this->lookupBillingSite($customer, $nextrackCompany, 1);

        //compile the array of prebuilds to send
        $details = array();
        $x = 0;
        
        $sitesCost = 0;
        $usersCost = 0;
        $marketPlaces = array();
        $m = 0;
        $controlArray = array();
        $c = 0;


        $invoiceDetails = Billings_Details::where('billing_id', '=', $invoice->id)->get();
        foreach($invoiceDetails as $detail)
        {
            if($detail->type == "sites")
            {
                $sitesCost = $detail->cost;
            }
            if($detail->type == "users")
            {
                $usersCost = $detail->cost;
            }
            if($detail->type == "marketplace")
            {
                $marketPlaces[$m]['name'] = $detail->reference;
                $marketPlaces[$m]['price'] = $detail->cost;
                $m++;
            }
            if($detail->type == "controls")
            {
                $control = Control::find($detail->reference);
                if(is_object($control))
                {
                    if($control->billing != "no")
                    {
                        $prebuild = $this->lookupBillingPrebuild($detail, $nextrackCompany);

                        $controlArray[$c]['prebuildID'] = $prebuild['prebuildID'];
                        $controlArray[$c]['costCenter'] = $prebuild['costCenter'];
                        $controlArray[$c]['cost'] = $prebuild['cost'];
                        $c++;
                    }
                }
            }
        }

        //lets go ahead and create the job for invoicing
        $jobDetails = $this->createInvoiceJob($customer, $site, $nextrackCompany, $settings, $usersCost, $usersBillingPrebuidID, $sitesCost, $sitesBillingPrebuidID, $marketPlaces);

        //now go through cycle through cost centers to collate all relevant controls for billing into the correct cost centers
        $checkingArray = array();
        $ccArray = array();
        $cc = 0;
        $ca = 0;
        foreach($controlArray as $cKey => $cValue)
        {
            if(!in_array($cValue['costCenter'], $checkingArray))
            {
                $checkingArray[$ca] = $cValue['costCenter'];
                $ca++;

                foreach($controlArray as $value)
                {
                    $newEntries = array();
                    $ne = 0;
                    if($value['costCenter'] == $cValue['costCenter'])
                    {
                        $newEntry = array();
                        $newEntry['prebuildID'] = $value['prebuildID'];
                        $newEntry['cost'] = $value['cost'];

                        $newEntries[$ne] = $newEntry;
                        $ne++;                        
                    }
                    $ccArray[$cc]['CostCenter'] = $value['costCenter'];
                    $ccArray[$cc]['prebuilds'] = $newEntries;
                    $cc++;
                }
            }
        }

        foreach($ccArray as $cc)
        {
            //do nothing for now - will need to post all the prebuilds into the right cost centers.
            $response = $this->createInvoiceJobCostCenter($cc['CostCenter'], $nextrackCompany, $jobDetails['jobID'], $jobDetails['sectionID'], $cc['prebuilds']);            
        }

        $jobs['Jobs'] = $jobDetails['jobID'];

        $params = array();
        $params['Type'] = "TaxInvoice";
        $params['Stage'] = "Approved";
        $params['Jobs'] = $jobs;

        //now add the item
        $endpoint = "companies/" . $nextrackCompany . "/invoices/";
        $response = json_decode($apiController->apiPost("2", $endpoint, json_encode($params)));

        return $response;
    }

}