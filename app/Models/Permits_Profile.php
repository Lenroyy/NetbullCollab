<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permits_Profile extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['permits_id', 'profiles_id', 'status', 'reference'];
    protected $dates = ['effective_date', 'expiry_date'];

    public function Permit()
    {
        return $this->belongsTo('App\Models\Permit', 'permits_id');
    }

    public function Profile()
    {
        return $this->belongsTo('App\Models\Profile', 'profiles_id'); 
    }
}
