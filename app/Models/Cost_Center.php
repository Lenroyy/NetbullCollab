<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cost_Center extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['company_id', 'cost_center_id', 'cost_center_name'];

    public function Internals()
    {
        return $this->hasMany('App\Models\Controls_Type', 'internal_lease_cost_center_id');
    }

    public function Externals()
    {
        return $this->hasMany('App\Models\Controls_Type', 'external_lease_cost_center_id');
    }

    public function Sales()
    {
        return $this->hasMany('App\Models\Controls_Type', 'sale_cost_center_id');
    }
}
