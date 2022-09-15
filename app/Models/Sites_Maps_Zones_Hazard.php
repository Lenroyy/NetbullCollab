<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sites_Maps_Zones_Hazard extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['zone_id', 'hazard_id', 'plan', 'archived'];

    public function Zone()
    {
        return $this->belongsTo('App\Models\Sites_Maps_Zone', 'zone_id');
    }

    public function Hazard()
    {
        return $this->belongsTo('App\Models\Hazard', 'hazard_id');
    }

    public function Steps()
    {
        return $this->hasMany('App\Models\Sites_Maps_Zones_Hazards_Plan_Step', 'zone_hazard_id');
    }

}
