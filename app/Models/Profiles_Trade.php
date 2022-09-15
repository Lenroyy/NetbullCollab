<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profiles_Trade extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['profiles_id', 'trades_id'];

    public function Profile()
    {
        return $this->belongsTo('App\Models\Profile', 'profiles_id');
    }

    public function Trade()
    {
        return $this->belongsTo('App\Models\Trade', 'trades_id');
    }
}
