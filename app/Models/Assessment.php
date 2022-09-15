<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['name', 'archived', 'once_per']; 

    public function Assessments_Activity()
    {
        return $this->hasMany('App\Models\Assessments_Activities');
    }

    public function Assessments_Site()
    {
        return $this->hasMany('App\Models\Assessments_Site');
    }

    public function Assessments_Permit()
    {
        return $this->hasMany('App\Models\Assessments_Permit');
    }

    public function Assessments_Group()
    {
        return $this->hasMany('App\Models\Assessments_Questions_Group');
    }

    public function Assessments_Question()
    {
        return $this->hasMany('App\Models\Assessments_Question');
    }

    public function Actions_Assessment()
    {
        return $this->hasMany('App\Models\Actions_Assessment');
    }

    public function Actions_Time_Entry()
    {
        return $this->hasMany('App\Models\Actions_Time_Entry');
    }

    
}
