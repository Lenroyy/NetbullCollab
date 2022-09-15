<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainings_Hygenist extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['profile_id', 'training_id', 'price', 'link', 'active_provider']; 

    public function Training()
    {
        return $this->belongsTo('App\Models\Training', 'training_id');
    }

    public function Profile()
    {
        return $this->belongsTo('App\Models\Profile', 'profile_id');
    }
}
