<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Controls_Field extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['control_id', 'control_field_id', 'value'];

    public function Controls_Type_Field()
    {
        return $this->belongsTo('App\Models\Controls_Type_Field', 'control_field_id');
    }

    public function Control()
    {
        return $this->belongsTo('App\Models\Control', 'control_id');
    }
}
