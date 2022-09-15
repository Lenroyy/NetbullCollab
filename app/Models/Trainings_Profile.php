<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainings_Profile extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['training_id', 'hygenist_id', 'training_hygenist_id',  'profile_id', 'active_organisation_id', 'instructions', 'status', 'paid']; 

    public function Training()
    {
        return $this->belongsTo('App\Models\Training', 'training_id');
    }

    public function Hygenist()
    {
        return $this->belongsTo('App\Models\Profile', 'hygenist_id');
    }

    public function Profile()
    {
        return $this->belongsTo('App\Models\Profile', 'profile_id');
    }

    public function ActiveOrganisation()
    {
        return $this->belongsTo('App\Models\Profile', 'active_organisation_id');
    }

    public function TrainingHygenist()
    {
        return $this->belongsTo('App\Models\Trainings_Hygenist', 'training_hygenist_id');
    }
}
