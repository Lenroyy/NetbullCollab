<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Control extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['serial', 'controls_type_id', 'simpro_asset_id', 'archived', 'deployed', 'billing', 'billing_amount', 'billing_frequency', 'current_site', 'colour', 'x', 'y']; 
    protected $dates = ['commission_date', 'billing_commencement'];

    public function Controls_Type()
    {
        return $this->belongsTo('App\Models\Controls_Type', 'controls_type_id');
    }

    public function Control_Field()
    {
        return $this->hasMany('App\Models\Control_Field');
    }

    public function Site()
    {
        return $this->belongsTo('App\Models\Site', 'current_site');
    }

    public function Controls_Sites()
    {
        return $this->hasMany('App\Models\Controls_Sites');
    }
}
