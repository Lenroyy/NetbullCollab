<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Controls_Type extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['name', 'manufacturer', 'simpro_asset_type_id_1', 'simpro_asset_type_id_2', 'simpro_default_cost_center_id', 'image', 'shape', 'archived', 'billing_frequency', 'billing_amount', 'external_billing_amount', 'monitoring_only_billing_amount', 'sale_amount', 'control_type_group', 'simpro_prebuild_id_1', 'simpro_prebuild_id_2', 'internal_lease_cost_center_id', 'external_lease_cost_center_id', 'sale_cost_center_id', 'monitoring_only_cost_center_id'];

    public function Controls_Type_Field()
    {
        return $this->hasMany('App\Models\Controls_Type_Field');
    }

    public function Control()
    {
        return $this->hasMany('App\Models\Control');
    }

    public function ControlGroup()
    {
        return $this->belongsTo('App\Models\Controls_Type_Group', 'control_type_group');
    }

    public function Videos()
    {
        return $this->hasMany('App\Models\Video', 'control_type_id');
    }

    public function Internal_Cost_Center()
    {
        return $this->belongsTo('App\Models\Cost_Center', 'internal_lease_cost_center_id');
    }

    public function External_Cost_Center()
    {
        return $this->belongsTo('App\Models\Cost_Center', 'external_lease_cost_center_id');
    }

    public function Sale_Cost_Center()
    {
        return $this->belongsTo('App\Models\Cost_Center', 'sale_cost_center_id');
    }
}
