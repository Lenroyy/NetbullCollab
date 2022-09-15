<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'type', 'name', 'archived', 'simpro_id_1', 'simpro_id_2', 'simpro_billing_site_id', 'primary_contact', 'address', 'city', 'state', 'postcode', 'country', 'email', 'phone', 'mobile', 'tax_id', 'logo', 'security_group', 'member_hash', 'provider_type', 'theme'];
    protected $dates = ['billing_start'];

    public function Membership()
    {
        return $this->hasMany('App\Models\Membership', 'user_id');
    }

    public function Members()
    {
        return $this->hasMany('App\Models\Membership', 'organisation_id');
    }

    public function Permits_Profile()
    {
        return $this->hasMany('App\Models\Permits_Profile');
    }

    public function Site()
    {
        return $this->hasMany('App\Models\Site');
    }

    public function Training_Hygenist()
    {
        return $this->hasMany('App\Models\Training_Hygenist');
    }

    public function Profiles_Trade()
    {
        return $this->hasMany('App\Models\Profiles_Trade', 'profiles_id');
    }

    public function Log()
    {
        return $this->hasMany('App\Models\Log');
    }

    public function Security_Group()
    {
        return $this->belongsTo('App\Models\Security_Groups', 'security_group');
    }

    public function Actions_Assessment()
    {
        return $this->hasMany('App\Models\Actions_Assessment');
    }

    public function Actions_Time_Entry()
    {
        return $this->hasMany('App\Models\Actions_Time_Entry');
    }

    public function Sites_Logon()
    {
        return $this->hasMany('App\Models\Sites_Logon', 'profile_id');
    }

    public function Licenses()
    {
        return $this->hasMany('App\Models\License_Profile', 'profile_id');
    }
    
    
}
