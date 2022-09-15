<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['profiles_id', 'site_id', 'zone_id', 'activity_id', 'archived', 'checked', 'type'];
    protected $dates = ['time_start', 'time_end'];

    public function Assessment()
    {
        return $this->hasMany('App\Models\Histories_Assessments', 'history_id');
    }

    public function Times()
    {
        return $this->hasMany('App\Models\Actions_Time_Entry', 'history_id');
    }
    
    public function Site()
    {
        return $this->belongsTo('App\Models\Site', 'site_id');
    }

    public function Zone()
    {
        return $this->belongsTo('App\Models\Sites_Maps_Zone', 'zone_id');
    }

    public function Activity()
    {
        return $this->belongsTo('App\Models\Activities', 'activity_id');
    }

    public function Profile()
    {
        return $this->belongsTo('App\Models\Profile', 'profiles_id');
    }

    
}