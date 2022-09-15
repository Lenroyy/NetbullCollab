<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hazards_Activities extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['activity_id', 'hazard_id'];

    public function Activity()
    {
        return $this->belongsTo('App\Models\Activities', 'activity_id'); 
    }

    public function Hazard()
    {
        return $this->belongsTo('App\Models\Hazard', 'hazard_id');
    }
}
