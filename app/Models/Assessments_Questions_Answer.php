<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessments_Questions_Answer extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['question_id', 'option_id', 'action', 'score', 'comments', 'goto_id'];

    public function Assessments_Question()
    {
        return $this->belongsTo('App\Models\Assessments_Question', 'question_id');
    }

    public function goto()
    {
        return $this->belongsTo('App\Models\Assessments_Question', 'goto_id');
    }

    public function Assessments_Questions_Answers_Option()
    {
        return $this->belongsTo('App\Models\Assessments_Questions_Answers_Option', 'option_id');
    }
}
