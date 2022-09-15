<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['profile_id', 'total_cost', 'posted']; 

    public function Profile()
    {
        return $this->belongsTo('App\Models\Profile', 'profile_id');
    }

    public function Billing_Details()
    {
        return $this->hasMany('App\Models\Billing_Details', 'billing_id');
    }

}
