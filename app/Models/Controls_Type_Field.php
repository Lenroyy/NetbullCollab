<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Controls_Type_Field extends Model
{
    use HasFactory;

    protected $primaryKey = 'id'; 
    protected $fillable = ['name', 'controls_type_id', 'archived'];

    public function Controls_Type()
    {
        return $this->belongsTo('App\Models\Controls_Type', 'controls_type_id');
    }

    public function Control_Field()
    {
        return $this->hasMany('App\Models\Control_Field');
    }
}
