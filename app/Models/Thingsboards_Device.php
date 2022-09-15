<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thingsboards_Device extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['thingsboard_id', 'name', 'type', 'control_id', 'archived'];

    public function Control()
    {
        return $this->belongsTo('App\Models\Control', 'control_id');
    }
    
}
