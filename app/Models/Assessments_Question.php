<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessments_Question extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['assessment_id', 'question', 'assessments_questions_group_id', 'answer_type'];

    public function Assessment()
    {
        return $this->belongsTo('App\Models\Assessment', 'assessment_id');
    }

    public function Assessments_Questions_Group()
    {
        return $this->belongsTo('App\Models\Assessments_Questions_Group', 'assessments_questions_group_id');
    }
}
