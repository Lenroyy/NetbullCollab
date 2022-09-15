<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Security_Group_Details extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = ['security_group_id', 'module', 'action'];

    public function Group()
    {
        return $this->belongsTo('App\Security_Groups', 'security_group_id');
    }
}
