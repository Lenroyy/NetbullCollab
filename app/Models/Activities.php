<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activities extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['name', 'archived'];

    public function Activities_Permits()
    {
        return $this->hasMany('App\Models\Activities_Permits');
    }

    public function Activities_Trades()
    {
        return $this->hasMany('App\Models\Activities_Trades');
    }
}
