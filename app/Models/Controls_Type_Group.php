<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Controls_Type_Group extends Model
{
    use HasFactory;

    protected $primaryKey = 'id'; 
    protected $fillable = ['name', 'archived'];

    public function Controls_Type()
    {
        return $this->hasMany('App\Models\Controls_Type', 'control_type_group');
    }
}
