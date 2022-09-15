<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sites_Maps_Zone extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['name', 'map_id', 'site_id', 'archived'];

    public function Site()
    {
        return $this->belongsTo('App\Models\Site', 'site_id');
    }

    public function Sites_Map()
    {
        return $this->belongsTo('App\Models\Sites_Map', 'map_id');
    }

    public function Controls_Sites()
    {
        return $this->hasMany('App\Models\Controls_Sites'); 
    }

    public function Hazards()
    {
        return $this->hasMany('App\Models\Sites_Maps_Zones_Hazard', 'zone_id'); 
    }

    public function Actions_Time_Entry()
    {
        return $this->hasMany('App\Models\Actions_Time_Entry', 'zone_id'); 
    }

    public function Actions_Assessment()
    {
        return $this->hasMany('App\Models\Actions_Assessment', 'zone_id'); 
    }
}
