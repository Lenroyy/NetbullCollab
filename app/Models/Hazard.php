<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hazard extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['name', 'archived'];

    public function Zone_Hazards()
    {
        return $this->hasMany('App\Models\Sites_Maps_Zones_Hazard', 'hazard_id');
    }

    public function Trades()
    {
        return $this->hasMany('App\Models\Hazards_Trades', 'hazard_id');
    }
}
