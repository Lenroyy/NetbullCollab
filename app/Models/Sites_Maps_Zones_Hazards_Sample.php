<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sites_Maps_Zones_Hazards_Sample extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['zone_hazard_id', 'sample_id', 'result', 'archived'];
    protected $dates = ['date'];

    public function Hazard()
    {
        return $this->belongsTo('App\Models\Sites_Maps_Zones_Hazard', 'zone_hazard_id');
    }

    public function Sample()
    {
        return $this->belongsTo('App\Models\Sample', 'sample_id');
    }
}
