<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'id';
    protected $fillable = ['filename', 'original_name', 'module_id', 'module', 'file_size'];
}
