<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['profiles_id', 'module', 'module_id', 'action', 'notes', 'log_level'];

    public function Profile()
    {
        return $this->belongsTo('App\Models\Profile', 'profiles_id');
    }
}
