<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['name', 'price', 'link', 'archived', 'description', 'training_type_id'];

    public function Training_Hygenist()
    {
        return $this->hasMany('App\Models\Trainings_Hygenist', 'training_id');
    }

    public function Permits_Training()
    {
        return $this->hasMany('App\Models\Permits_Training');
    }

    public function Training_Types()
    {
        return $this->belongsTo('App\Models\Trainings_Types', 'training_type_id');
    }
}
