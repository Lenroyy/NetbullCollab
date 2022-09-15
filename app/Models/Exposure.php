<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exposure extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['name', 'archived', 'reading_type_id', 'time_period', 'level'];

    public function ReadingType()
    {
        return $this->belongsTo('App\Models\Thingsboards_Readings_Type', 'reading_type_id');
    }
}
