<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sites_Maps_Zones_Hazards_Plan_Step extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['zone_hazard_id', 'step'];

    public function Hazard()
    {
        return $this->belongsTo('App\Models\Sites_Maps_Zones_Hazard', 'zone_hazard_id');
    }
}
