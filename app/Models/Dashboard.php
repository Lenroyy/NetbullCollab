<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model
{
    use HasFactory;

    protected $primaryKey = 'id'; 
    protected $fillable = ['col', 'row', 'size_x', 'size_y', 'content', 'profile_id', 'type', 'module_id'];
}
