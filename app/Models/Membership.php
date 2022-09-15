<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'organisation_id', 'organisation_type', 'security_group', 'membership_status'];
    protected $dates = ['joined', 'exitted'];

    public function Profile()
    {
        return $this->belongsTo('App\Models\Profile', 'user_id');
    }

    public function Security_Group()
    {
        return $this->belongsTo('App\Models\security_groups', 'security_group');
    }

    public function Organisation()
    {
        return $this->belongsTo('App\Models\Profile', 'organisation_id');
    }
}
