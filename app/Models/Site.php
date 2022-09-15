<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['name', 'builder_id', 'hygenist_id', 'simpro_site_id_1', 'simpro_site_id_2', 'address', 'city', 'state', 'postcode', 'country', 'primary_contact_id', 'phone', 'mobile', 'image', 'archived', 'relinquish_id', 'zone_qr_code_function'];

    public function Hygenist()
    {
        return $this->belongsTo('App\Models\Profile', 'hygenist_id');
    }

    public function Builder()
    {
        return $this->belongsTo('App\Models\Profile', 'builder_id');
    }

    public function Contact()
    {
        return $this->belongsTo('App\Models\Profile', 'primary_contact_id');
    }

    public function Sites_Map()
    {
        return $this->hasMany('App\Models\Sites_Map');
    }

    public function Sites_Maps_Zone()
    {
        return $this->hasMany('App\Models\Sites_Maps_Zone');
    }

    public function Site_Permit()
    {
        return $this->hasMany('App\Models\Site_Permit');
    }

    public function Controls_Sites()
    {
        return $this->hasMany('App\Models\Controls_Sites');
    }

    public function Assessments_Site()
    {
        return $this->hasMany('App\Models\Assessments_Site');
    }

    public function Actions_Assessment()
    {
        return $this->hasMany('App\Models\Actions_Assessment');
    }

    public function Actions_Time_Entry()
    {
        return $this->hasMany('App\Models\Actions_Time_Entry');
    }

    public function Permits_Site()
    {
        return $this->hasMany('App\Models\Permits_Site');
    }

    public function Sites_Logon()
    {
        return $this->hasMany('App\Models\Sites_Logon', 'site_id');
    }

    public function Sites_Reports()
    {
        return $this->hasMany('App\Models\Sites_Reports', 'site_id');
    }

    
}
