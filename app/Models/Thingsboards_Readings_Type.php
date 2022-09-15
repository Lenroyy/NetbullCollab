<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thingsboards_Readings_Type extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = ['name'];

    public function DeviceReadings()
    {
        return $this->hasMany('App\Models\Thingsboards_Device_Reading', 'reading_type_id');
    }
    
    public function DeviceReadingTypes()
    {
        return $this->belongsTo('App\Models\Thingsboards_Device_Reading_Types', 'reading_type_id');
    }
}
