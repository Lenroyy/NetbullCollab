<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Api extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['application_name', 'archived', 'base_url', 'username', 'password', 'token', 'token_ttl', 'refresh_token', 'settings'];

}
