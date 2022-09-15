<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permits_Zone extends Model
{
    use HasFactory;

    protected $fillable = ['permits_id', 'zones_id', 'mandatory'];

    public function Zone()
    {
        return $this->belongsTo('App\Models\Sites_Maps_Zone', 'zones_id');
    }

    public function Permit()
    {
        return $this->belongsTo('App\Models\Permit', 'permits_id');
    }
}
