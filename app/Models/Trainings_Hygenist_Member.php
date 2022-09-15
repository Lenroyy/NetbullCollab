<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainings_Hygenist_Member extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['training_id', 'hygenist_id', 'member_id', 'price']; 

    public function Training()
    {
        return $this->belongsTo('App\Models\Training', 'training_id');
    }

    public function Hygenist()
    {
        return $this->belongsTo('App\Models\Profile', 'hygenist_id');
    }

    public function Member()
    {
        return $this->belongsTo('App\Models\Profile', 'member_id');
    }
}
