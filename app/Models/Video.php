<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['code', 'control_type_id', 'type']; 

    public function Control_Type()
    {
        return $this->belongsTo('App\Models\Controls_Type', 'control_type_id');
    }
}
