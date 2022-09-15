<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainings_Types extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['name', 'archived']; 

    public function Training()
    {
        return $this->hasMany('App\Models\Training', 'training_type_id');
    }
}
