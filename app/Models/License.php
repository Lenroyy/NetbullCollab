<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['default_site_cost', 'default_user_cost'];

}
