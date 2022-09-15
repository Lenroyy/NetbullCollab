<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessments_Activities extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['assessment_id', 'activities_id'];

    public function Assessment()
    {
        return $this->belongsTo('App\Models\Assessment', 'assessment_id');
    }

    public function Activities()
    {
        return $this->belongsTo('App\Models\Activities', 'activities_id');
    }
}
