<?php

namespace App\Jobs;

use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\History;

use App\Http\Controllers\ActivityController;

class historyChecker implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public $tries = 3;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $timeToCheck = 8; //will check this number of hours, if the activity is older than this it will be closed off
        $reverseTime = 24 - $timeToCheck; //this calculated the hour in the day to start thinking about yesterdays retrievals

        $histories = History::where('checked', '=', 0)->get();

        foreach($histories as $history)
        {
            if($history->time_start == $history->time_end || is_null($history->time_end))
            {
                //does not appear as if this history has been finished
                $now = date('H');
                $checkHour = substr($history->time_start, 11, 2);
                
                $checkDifference = $now-$checkHour;

                if($checkDifference > $timeToCheck OR ($checkHour > $reverseTime && $now > $timeToCheck))
                {
                    if($history->site_id > 0 && $history->zone_id > 0 && $history->activity_id > 0)
                    {
                        $startHour = $checkHour;
                        $startMinutes = substr($history->time_start, 14, 2);

                        if($startHour < (24-$timeToCheck))
                        {
                            $endHour = $startHour + $timeToCheck;
                        }
                        else 
                        {
                            $endHour = ($startHour + $timeToCheck) - 24;
                        }
    
                        $endMinutes = date('i');

                        echo PHP_EOL . $endHour . PHP_EOL;
                        exit;
    
                        $request = new Request();
                        $request->loggedActivity = $history->id;
                        $request->site = $history->site_id;
                        $request->zone = $history->zone_id;
                        $request->activity = $history->activity_id;
                        $request->startHour = $startHour;
                        $request->startMinute = $startMinutes;
                        $request->endHour = $endHour;
                        $request->endMinute = $endMinutes;
    
                        $activityController = new ActivityController();
                        $activityController->saveActivity($request, 2);

                        $history->checked = 1;
                        $history->save();
                        
                    }
                    else 
                    {
                        //junk data, someone started a new activity 8 hours ago and didn't put any information in at all.
                        $history->delete();
                    }
                }
            }
            else
            {
                $history->checked = 1;                
                $history->save();
            }
        }
    }
}