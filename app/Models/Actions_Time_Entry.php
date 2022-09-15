<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actions_Time_Entry extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'site_id', 'zone_id', 'start', 'finish', 'active_organisation_id', 'history_id'];
    protected $dates = ['date'];

    public function Profile()
    {
        return $this->belongsTo('App\Models\Profile', 'user_id');
    }

    public function History()
    {
        return $this->belongsTo('App\Models\History', 'history_id');
    }

    public function Site()
    {
        return $this->belongsTo('App\Models\Site', 'site_id');
    }

    public function Assessment()
    {
        return $this->belongsTo('App\Models\Assessment', 'assessment_id');
    }

    public function Organisation()
    {
        return $this->belongsTo('App\Models\Profile', 'active_organisation_id');
    }

    public function Zone()
    {
        return $this->belongsTo('App\Models\Sites_Maps_Zone', 'zone_id');
    }


    public function calcHours()
    {
        $time = 0;
        
        if(!empty($this->finish))
        {
            $startTime = strtotime($this->start);
            $finishTime = strtotime($this->finish);
            $diff = $finishTime - $startTime;
            $timeInSeconds = abs($diff);    
            $time = ($timeInSeconds/60)/60;
        }
        
        
        return round($time, 2);
    }
}
