<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thingsboards_Device_Reading extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = ['device_id', 'history_id', 'reading', 'reading_type_id', 'reading_timestamp', 'control_id', 'type', 'outcome'];

    public function Control()
    {
        return $this->belongsTo('App\Models\Control', 'control_id');
    }

    public function History()
    {
        return $this->belongsTo('App\Models\History', 'history_id');
    }

    public function Device()
    {
        return $this->belongsTo('App\Models\Thingsboards_Device', 'device_id');
    }

    public function ReadingType()
    {
        return $this->belongsTo('App\Models\Thingsboards_Readings_Type', 'reading_type_id');
    }
}
