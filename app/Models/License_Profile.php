<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License_Profile extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['profile_id', 'site_cost', 'no_sites', 'no_users', 'hardware_discount', 'marketplace_discount', 'user_discount', 'changed_by', 'changed'];

    public function Profile()
    {
        return $this->belongsTo('App\Models\Profile', 'profile_id');
    }
}
