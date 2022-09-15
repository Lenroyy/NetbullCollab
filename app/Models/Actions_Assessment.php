<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actions_Assessment extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'site_id', 'assessment_id', 'active_organisation_id', 'zone_id', 'history_assessment_id'];

    public function Profile()
    {
        return $this->belongsTo('App\Models\Profile', 'user_id');
    }

    public function Site()
    {
        return $this->belongsTo('App\Models\Site', 'site_id');
    }

    public function Assessment()
    {
        return $this->belongsTo('App\Models\Assessment', 'assessment_id');
    }

    public function Organisation()
    {
        return $this->belongsTo('App\Models\Profile', 'active_organisation_id');
    }

    public function Zone()
    {
        return $this->belongsTo('App\Models\Sites_Maps_Zone', 'zone_id');
    }

    public function History_Assessment()
    {
        return $this->belongsTo('App\Models\Histories_Assessments', 'history_assessment_id');
    }
}
