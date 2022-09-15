<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'archived', 'qty', 'est_hazards'];

    public function Profiles_Trade()
    {
        return $this->hasMany('App\Models\Profiles_Trade');
    }

    public function Contractors()
    {
        return $this->hasMany('App\Models\Contractors');
    }
    
}