<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thingsboards_Device_Reading_Types extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = ['device_id', 'reading_type_id', 'calculation'];

    public function Thingsboards_Device()
    {
        return $this->belongsTo('App\Models\Thingsboards_Device', 'device_id');
    }

    public function ReadingType()
    {
        return $this->belongsTo('App\Models\Thingsboards_Readings_Type', 'reading_type_id');
    }
}
