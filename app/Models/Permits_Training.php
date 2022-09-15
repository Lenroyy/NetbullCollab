<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permits_Training extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['permit_id', 'trainings_id'];

    public function Permit()
    {
        return $this->belongsTo('App\Models\Permit', 'permit_id');
    }

    public function Training()
    {
        return $this->belongsTo('App\Models\Training', 'trainings_id');
    }
}
