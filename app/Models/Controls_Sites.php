<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Controls_Sites extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['control_id', 'from_site_id', 'to_site_id', 'from_map_id', 'to_map_id', 'from_zone_id', 'to_zone_id', 'from_hazard_id', 'to_hazard_id'];

    public function From_Site()
    {
        return $this->belongsTo('App\Models\Site', 'from_site_id');
    }

    public function To_Site()
    {
        return $this->belongsTo('App\Models\Site', 'to_site_id');
    }

    public function From_Map()
    {
        return $this->belongsTo('App\Models\Sites_Map', 'from_map_id');
    }

    public function To_Map()
    {
        return $this->belongsTo('App\Models\Sites_Map', 'to_map_id');
    }

    public function From_Zone()
    {
        return $this->belongsTo('App\Models\Sites_Maps_Zone', 'from_zone_id');
    }

    public function To_Zone()
    {
        return $this->belongsTo('App\Models\Sites_Maps_Zone', 'to_zone_id');
    }

    public function From_Hazard()
    {
        return $this->belongsTo('App\Models\Sites_Maps_Zones_Hazard', 'from_hazard_id');
    }

    public function To_Hazard()
    {
        return $this->belongsTo('App\Models\Sites_Maps_Zones_Hazard', 'to_hazard_id');
    }

    public function Control()
    {
        return $this->belongsTo('App\Models\Control', 'control_id');
    }
}
