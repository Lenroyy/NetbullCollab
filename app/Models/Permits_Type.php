<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permits_Type extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['name'];

    public function Permit()
    {
        return $this->hasMany('App\Models\Permit');
    }
}
