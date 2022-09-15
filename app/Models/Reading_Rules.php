<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reading_Rules extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['name', 'reading_type_id', 'rule_type', 'assessment_id', 'question_id', 'answer_id', 'within_range_max', 'within_range_min', 'above_max', 'below_min', 'formula', 'outcome', 'archived', 'order'];

    public function Reading_Type()
    {
        return $this->belongsTo('App\Models\Thingsboards_Readings_Type', 'reading_type_id');
    }
}
