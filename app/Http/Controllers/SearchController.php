<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Site;
use App\Models\Task;
use App\Models\Training;


use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    



     //Results


    public function results(Request $request)
    {
        $results = array();
        $x = 0;

        //seach users
            $usersArray = array();
            $profiles = Profile::where('archived', '=', 0)
                                ->where('type', '=', 'user')
                                ->where(function($query) use ($request) {
                                    $query->where('name', 'like', '%' . $request->search . '%')
                                    ->orWhere('address', 'like', '%' . $request->search . '%')
                                    ->orWhere('city', 'like', '%' . $request->search . '%')
                                    ->orWhere('state', 'like', '%' . $request->search . '%')
                                    ->orWhere('postcode', 'like', '%' . $request->search . '%')
                                    ->orWhere('country', 'like', '%' . $request->search . '%')
                                    ->orWhere('email', 'like', '%' . $request->search . '%')
                                    ->orWhere('phone', 'like', '%' . $request->search . '%')
                                    ->orWhere('mobile', 'like', '%' . $request->search . '%')
                                    ->orWhere('tax_id', 'like', '%' . $request->search . '%')
                                    ->orWhere('member_hash', 'like', '%' . $request->search . '%');
                                })
                                ->get();
            foreach($profiles as $profile)
            {
                $usersArray[$x]['name'] = $this->checkUserVisibility($profile->id);
                $usersArray[$x]['id'] = $profile->id;
                $x++;
            }
            if(count($usersArray) >0)
            {
                $results[$x]['type'] = "Users";
                $results[$x]['baseURL'] = "/editProfile";
                $results[$x]['results'] = $usersArray;
                $results[$x]['urlTail'] = '';
                $x++;
            }


        //search sites
            $sitesArray = array();
            $sites = Site::where('archived', '=', 0)
                                ->where(function($query) use ($request) {
                                    $query->where('name', 'like', '%' . $request->search . '%')
                                    ->orWhere('address', 'like', '%' . $request->search . '%')
                                    ->orWhere('city', 'like', '%' . $request->search . '%')
                                    ->orWhere('state', 'like', '%' . $request->search . '%')
                                    ->orWhere('postcode', 'like', '%' . $request->search . '%')
                                    ->orWhere('country', 'like', '%' . $request->search . '%')
                                    ->orWhere('phone', 'like', '%' . $request->search . '%')
                                    ->orWhere('mobile', 'like', '%' . $request->search . '%');
                                })
                                ->get();
            foreach($sites as $site)
            {
                $sitesArray[$x]['name'] = $site->name;
                $sitesArray[$x]['id'] = $site->id;
                $x++;
            }
            if(count($sitesArray) >0)
            {
                $results[$x]['type'] = "Sites";
                $results[$x]['baseURL'] = "/editSite";
                $results[$x]['results'] = $sitesArray;
                $results[$x]['urlTail'] = '';
                $x++;
            }

        //search contractors
            $contractorArray = array();
            $profiles = Profile::where('archived', '=', 0)
                                ->where('type', '=', 'contractor')
                                ->where(function($query) use ($request) {
                                    $query->where('name', 'like', '%' . $request->search . '%')
                                    ->orWhere('address', 'like', '%' . $request->search . '%')
                                    ->orWhere('city', 'like', '%' . $request->search . '%')
                                    ->orWhere('state', 'like', '%' . $request->search . '%')
                                    ->orWhere('postcode', 'like', '%' . $request->search . '%')
                                    ->orWhere('country', 'like', '%' . $request->search . '%')
                                    ->orWhere('email', 'like', '%' . $request->search . '%')
                                    ->orWhere('phone', 'like', '%' . $request->search . '%')
                                    ->orWhere('mobile', 'like', '%' . $request->search . '%')
                                    ->orWhere('tax_id', 'like', '%' . $request->search . '%')
                                    ->orWhere('member_hash', 'like', '%' . $request->search . '%');
                                })
                                ->get();
            foreach($profiles as $profile)
            {
                $contractorArray[$x]['name'] = $this->checkUserVisibility($profile->id);
                $contractorArray[$x]['id'] = $profile->id;
                $x++;
            }
            if(count($contractorArray) >0)
            {
                $results[$x]['type'] = "Contractors";
                $results[$x]['baseURL'] = "/editContractor";
                $results[$x]['results'] = $contractorArray;
                $results[$x]['urlTail'] = '';
                $x++;
            }
        

        //search builders
            $builderArray = array();
            $profiles = Profile::where('archived', '=', 0)
                                ->where('type', '=', 'builder')
                                ->where(function($query) use ($request) {
                                    $query->where('name', 'like', '%' . $request->search . '%')
                                    ->orWhere('address', 'like', '%' . $request->search . '%')
                                    ->orWhere('city', 'like', '%' . $request->search . '%')
                                    ->orWhere('state', 'like', '%' . $request->search . '%')
                                    ->orWhere('postcode', 'like', '%' . $request->search . '%')
                                    ->orWhere('country', 'like', '%' . $request->search . '%')
                                    ->orWhere('email', 'like', '%' . $request->search . '%')
                                    ->orWhere('phone', 'like', '%' . $request->search . '%')
                                    ->orWhere('mobile', 'like', '%' . $request->search . '%')
                                    ->orWhere('tax_id', 'like', '%' . $request->search . '%')
                                    ->orWhere('member_hash', 'like', '%' . $request->search . '%');
                                })
                                ->get();
            foreach($profiles as $profile)
            {
                $builderArray[$x]['name'] = $this->checkUserVisibility($profile->id);
                $builderArray[$x]['id'] = $profile->id;
                $x++;
            }
            if(count($builderArray) >0)
            {
                $results[$x]['type'] = "Builders";
                $results[$x]['baseURL'] = "/editBuilder";
                $results[$x]['results'] = $builderArray;
                $results[$x]['urlTail'] = '';
                $x++;
            }

        //search hygienists
            $hygienistsArray = array();
            $profiles = Profile::where('archived', '=', 0)
                                ->where('type', '=', 'hygenist')
                                ->where('provider_type', '=', 'h')
                                ->where(function($query) use ($request) {
                                    $query->where('name', 'like', '%' . $request->search . '%')
                                    ->orWhere('address', 'like', '%' . $request->search . '%')
                                    ->orWhere('city', 'like', '%' . $request->search . '%')
                                    ->orWhere('state', 'like', '%' . $request->search . '%')
                                    ->orWhere('postcode', 'like', '%' . $request->search . '%')
                                    ->orWhere('country', 'like', '%' . $request->search . '%')
                                    ->orWhere('email', 'like', '%' . $request->search . '%')
                                    ->orWhere('phone', 'like', '%' . $request->search . '%')
                                    ->orWhere('mobile', 'like', '%' . $request->search . '%')
                                    ->orWhere('tax_id', 'like', '%' . $request->search . '%')
                                    ->orWhere('member_hash', 'like', '%' . $request->search . '%');
                                })
                                ->get();
            foreach($profiles as $profile)
            {
                $hygienistsArray[$x]['name'] = $this->checkUserVisibility($profile->id);
                $hygienistsArray[$x]['id'] = $profile->id;
                $x++;
            }
            if(count($hygienistsArray) >0)
            {
                $results[$x]['type'] = "Hygienists";
                $results[$x]['baseURL'] = "/editHygenist";
                $results[$x]['results'] = $hygienistsArray;
                $results[$x]['urlTail'] = '/0';
                $x++;
            }

        //search service providers
            $providersArray = array();
            $profiles = Profile::where('archived', '=', 0)
                                ->where('type', '=', 'hygenist')
                                ->where('provider_type', '=', 's')
                                ->where(function($query) use ($request) {
                                    $query->where('name', 'like', '%' . $request->search . '%')
                                    ->orWhere('address', 'like', '%' . $request->search . '%')
                                    ->orWhere('city', 'like', '%' . $request->search . '%')
                                    ->orWhere('state', 'like', '%' . $request->search . '%')
                                    ->orWhere('postcode', 'like', '%' . $request->search . '%')
                                    ->orWhere('country', 'like', '%' . $request->search . '%')
                                    ->orWhere('email', 'like', '%' . $request->search . '%')
                                    ->orWhere('phone', 'like', '%' . $request->search . '%')
                                    ->orWhere('mobile', 'like', '%' . $request->search . '%')
                                    ->orWhere('tax_id', 'like', '%' . $request->search . '%')
                                    ->orWhere('member_hash', 'like', '%' . $request->search . '%');
                                })
                                ->get();
            foreach($profiles as $profile)
            {
                $providersArray[$x]['name'] = $this->checkUserVisibility($profile->id);
                $providersArray[$x]['id'] = $profile->id;
                $x++;
            }
            if(count($providersArray) >0)
            {
                $results[$x]['type'] = "Hygienists";
                $results[$x]['baseURL'] = "/editHygenist";
                $results[$x]['results'] = $providersArray;
                $results[$x]['urlTail'] = '/1';
                $x++;
            }

        //search controls

        //search tasks
            $taskArray = array();
            $tasks = Task::where('archived', '=', 0)
                                ->where(function($query) use ($request) {
                                    $query->where('subject', 'like', '%' . $request->search . '%')
                                    ->orWhere('description', 'like', '%' . $request->search . '%')
                                    ->orWhere('notes', 'like', '%' . $request->search . '%');
                                })
                                ->get();
            foreach($tasks as $task)
            {
                $taskArray[$x]['name'] = $task->subject;
                $taskArray[$x]['id'] = $task->id;
                $x++;
            }
            if(count($taskArray) >0)
            {
                $results[$x]['type'] = "Tasks";
                $results[$x]['baseURL'] = "/editTask";
                $results[$x]['results'] = $taskArray;
                $results[$x]['urlTail'] = '';
                $x++;
            }

        //search marketplace
            $trainingArray = array();
            $training = Training::where('archived', '=', 0)
                                ->where(function($query) use ($request) {
                                    $query->where('name', 'like', '%' . $request->search . '%')
                                    ->orWhere('description', 'like', '%' . $request->search . '%')
                                    ->orWhere('link', 'like', '%' . $request->search . '%');
                                })
                                ->get();
            foreach($training as $tr)
            {
                $trainingArray[$x]['name'] = $tr->name;
                $trainingArray[$x]['id'] = $tr->id;
                $x++;
            }
            if(count($trainingArray) >0)
            {
                $results[$x]['type'] = "Marketplace";
                $results[$x]['baseURL'] = "/buyTraining";
                $results[$x]['results'] = $trainingArray;
                $results[$x]['urlTail'] = '';
                $x++;
            }

        

        $standardDisplay = Controller::standardDisplay();
        $breadcrumbs = Controller::createBreadcrumbs("Search Results", NULL);        
        
        return view('search.results', [
            'breadcrumbs' => $breadcrumbs,
            'standardDisplay' => $standardDisplay,
            'results' => $results,
        ]);
    }
}