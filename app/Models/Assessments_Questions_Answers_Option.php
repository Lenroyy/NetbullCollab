<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessments_Questions_Answers_Option extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['name', 'archived'];

    public function Assessments_Questions_Answer()
    {
        return $this->hasMany('App\Models\Assessments_Questions_Answer');
    }
}
