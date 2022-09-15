<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Security_Groups extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'type', 'billable', 'archived'];

    public function profile()
    {
        return $this->hasMany('App\profile');
    }  

    public function Details()
    {
        return $this->hasMany('App\Security_Group_Details', 'security_group_id');
    }  
}
