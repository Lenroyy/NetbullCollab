<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['send_to', 'send_email', 'subject', 'content', 'status'];
}
