<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permit extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['name', 'archived', 'qty', 'permits_types_id'];

    public function Permits_Type()
    {
        return $this->belongsTo('App\Models\Permits_Type', 'permits_types_id');
    }
    
    public function Permits_Training()
    {
        return $this->hasMany('App\Models\Permits_Training');
    }

    public function Permits_Profile()
    {
        return $this->hasMany('App\Models\Permits_Profile');
    }

    public function Permits_Site()
    {
        return $this->hasMany('App\Models\Permits_Site', 'permits_id');
    }
}
