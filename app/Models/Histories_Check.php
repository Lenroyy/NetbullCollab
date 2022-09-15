<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Histories_Check extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['history_id'];

    public function History()
    {
        return $this->belongsTo('App\Models\History', 'history_id');
    }
}
