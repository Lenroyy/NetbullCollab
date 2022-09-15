<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actions_Control extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'control_id', 'zone_id', 'site_id','active_organisation_id', 'history_id'];
    protected $dates = ['date'];

    public function Profile()
    {
        return $this->belongsTo('App\Models\Profile', 'user_id');
    }

    public function History()
    {
        return $this->belongsTo('App\Models\History', 'history_id'); 
    }

    public function Organisation()
    {
        return $this->belongsTo('App\Models\Profile', 'active_organisation_id');
    }

    public function Zone()
    {
        return $this->belongsTo('App\Models\Sites_Maps_Zone', 'zone_id');
    }

    public function Control()
    {
        return $this->belongsTo('App\Models\Control', 'control_id');
    }

    public function Site()
    {
        return $this->belongsTo('App\Models\Site', 'site_id');
    }


}
