<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sites_Map extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['name', 'image', 'width', 'height', 'site_id', 'archived']; 

    public function Site()
    {
        return $this->belongsTo('App\Models\Site', 'site_id');
    }

    public function Controls_Sites()
    {
        return $this->hasMany('App\Models\Controls_Sites');
    }

    public function Zone()
    {
        return $this->hasMany('App\Models\Sites_Maps_Zone');
    }
}
