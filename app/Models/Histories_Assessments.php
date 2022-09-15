<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Histories_Assessments extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['history_id', 'assessment_id', 'status', 'score', 'signature'];
    protected $dates = ['time_start', 'time_end'];

    public function History()
    {
        return $this->belongsTo('App\Models\History', 'history_id');
    }

    public function Assessment()
    {
        return $this->belongsTo('App\Models\Assessment', 'assessment_id');
    }
}
