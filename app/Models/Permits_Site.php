<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permits_Site extends Model
{
    use HasFactory;

    protected $fillable = ['permits_id', 'sites_id', 'mandatory'];

    public function Site()
    {
        return $this->belongsTo('App\Models\Site', 'sites_id');
    }

    public function Permit()
    {
        return $this->belongsTo('App\Models\Permit', 'permits_id');
    }

}
