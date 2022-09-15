<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sites_Contractors extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['site_id', 'profile_id'];

    public function Site()
    {
        return $this->belongsTo('App\Models\Site', 'site_id');
    }

    public function Profile()
    {
        return $this->belongsTo('App\Models\Profile', 'profile_id');
    }
}
