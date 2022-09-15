<?php

namespace App\Http\Controllers;

use App\Models\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ApiController extends Controller
{

    public function apiGetPages($api, $endpoint)
    {
        $api = Api::find($api);

        $curl = curl_init();

        if($api->application_name == "thingsboard")
        {
            $auth = "X-Authorization: Bearer " . $api->token;
            $needle = "totalPages";
            $chars = 10;
        }
        else {
            $auth = "authorization: Bearer " . $api->token;
            $needle = "Result-Pages";
            $chars = 12;
        }
        
        curl_setopt_array($curl, array(
        CURLOPT_URL => $api->base_url . $endpoint, 
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_NOBODY => 1,
        CURLOPT_HEADER => 1,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            $auth,
            "content-type: application/json"
        ),
        ));

        $response = curl_exec($curl);

        $headers = [];
        $data = explode("\n",$response);
        print_r($data);
        exit;
        
        $pages = 1;

        foreach($data as $point)
        {
            if(substr($point, 0, $chars) == $needle)
            {
                $pages = TRIM(substr($point, 13, 10));  
            }
        }        

        $err = curl_error($curl);
        curl_close($curl);

        if ($err) 
        {
            echo "cURL Error #:" . $err;
        } 

        return $pages;
    }

    public function apiGetTBPages($api, $endpoint)
    {
        $api = Api::find($api);

        $curl = curl_init();

        $url = $api->base_url . $endpoint;

        if($api->application_name == "thingsboard")
        {
            $auth = "X-Authorization: Bearer " . $api->token;
        }
        else {
            $auth = "authorization: Bearer " . $api->token;
        }

        
        $auth = "X-Authorization: Bearer " . $api->token;
        $needle = "totalPages";
        $chars = 10;
        
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url, 
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_TIMEOUT => 180,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            $auth,
            "content-type: application/json"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) 
        {
            echo "cURL Error #:" . $err;
        }
        
        $response = json_decode($response);
        $pages = $response->$needle;

        return $pages;
    }

    public function apiGet($api, $endpoint, $page)
    {
        $api = Api::find($api);

        $curl = curl_init();
        
        $url = $api->base_url . $endpoint;
        if($page > 1)
        {
            $url = $url . "&page=" . $page;
        }

        //echo "<br>$url<br>";
        

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url, 
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_TIMEOUT => 180,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "authorization: Bearer " . $api->token,
            "content-type: application/json"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) 
        {
            echo "cURL Error #:" . $err;
        } 

        //print_r($response);
        //echo "<br><br>";

        return $response;
        
    }

    public function apiGetOAuth($api, $endpoint)
    {
        $api = Api::find($api);

        $curl = curl_init();
        
        $url = $api->base_url . $endpoint;

        if($api->application_name == "thingsboard")
        {
            $auth = "X-Authorization: Bearer " . $api->token;
        }
        else {
            $auth = "authorization: Bearer " . $api->token;
        }

        //echo "using token " . $api->token;
        //echo "<br>$url<br>";
        

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url, 
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 180,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            $auth,
            "content-type: application/json"
        ),
        ));

        $response = curl_exec($curl);

        $err = curl_error($curl);

        curl_close($curl);

        if ($err) 
        {
            echo "cURL Error #:" . $err;
        } 

        //print_r($response);
        //echo "<br><br>";

        return $response;
    }

    public function apiGetAndFilter($api, $endpoint, $filter)
    {
        $api = Api::find($api);

        $curl = curl_init();
        $url = $api->base_url . $endpoint . $filter;        

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url, 
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 180,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "authorization: Bearer " . $api->token,
            "content-type: application/json"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) 
        {
            echo "cURL Error #:" . $err;
        } 

        return $response;
        
    }

    public function apiPost($api, $endpoint, $params)
    {   
        $api = Api::find($api);

        $curl = curl_init();
        $url = $api->base_url . $endpoint;
                
        //echo "<br>Api is $api->application_name<br>endpoint is $endpoint<br>Params is $params<br>Url is $url<br>";
                
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 1000,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $params,
        CURLOPT_HTTPHEADER => array(
            "authorization: Bearer " . $api->token,
            "content-type: application/json",
        ),
        ));

        $response = curl_exec($curl);

        //echo "<br><br>Response is <br>";
        //print_r($response);

        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
        echo "cURL Error #:" . $err;
        }
        
        return $response;
    }

    public function apiPatch($api, $endpoint, $params)
    {   
        $api = Api::find($api);

        $curl = curl_init();
        $url = $api->base_url . $endpoint;

                
        //echo "<br>Api is $api->application_name<br>endpoint is $endpoint<br>Params is $params<br>Url is $url<br>";
        //exit;
        
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 1000,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "PATCH",
        CURLOPT_POSTFIELDS => $params,
        CURLOPT_HTTPHEADER => array(
            "authorization: Bearer " . $api->token,
            "content-type: application/json",
        ),
        ));

        $response = curl_exec($curl);

        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
        echo "cURL Error #:" . $err;
        }
        
        return $response;
    }

}