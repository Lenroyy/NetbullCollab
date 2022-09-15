<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessments_Permit extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['assessment_id', 'permits_id'];

    public function Assessment()
    {
        return $this->belongsTo('App\Models\Assessment', 'assessment_id');
    }

    public function Permit()
    {
        return $this->belongsTo('App\Models\Permit', 'permits_id');
    }
}
