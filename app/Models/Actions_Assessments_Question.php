<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actions_Assessments_Question extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = ['assessment_id', 'history_assessment_id', 'history_id', 'question_id', 'answer'];

    public function Assessment()
    {
        return $this->belongsTo('App\Models\Assessment', 'assessment_id');
    }

    public function History_Assessment()
    {
        return $this->belongsTo('App\Models\Histories_Assessments', 'history_assessment_id');
    }

    public function History()
    {
        return $this->belongsTo('App\Models\History', 'history_id');
    }

    public function Question()
    {
        return $this->belongsTo('App\Models\Assessments_Question', 'question_id');
    }

    public function Answer()
    {
        return $this->belongsTo('App\Models\Assessments_Questions_Answer', 'answer');
    }

    public function Option()
    {
        return $this->belongsTo('App\Models\Assessments_Questions_Answers_Option', 'answer');
    }
}
