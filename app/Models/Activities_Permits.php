<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activities_Permits extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['activities_id', 'permits_id'];

    public function Activities()
    {
        return $this->belongsTo('App\Models\Activities', 'activities_id');
    }

    public function Permit()
    {
        return $this->belongsTo('App\Models\Permit', 'permits_id');
    }
}
