<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessments_Questions_Group extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['name', 'parent_id', 'assessment_id'];

    public function Assessments_Questions_Answer()
    {
        return $this->hasMany('App\Models\Assessments_Question');
    }

    public function Parent()
    {
        return $this->belongsTo('App\Models\Assessments_Questions_Group', 'parent_id');
    }

    public function Assessment()
    {
        return $this->belongsTo('App\Models\Assessment', 'assessment_id');
    }
}
