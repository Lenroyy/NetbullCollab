<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Controls_Orders extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['order_no', 'control_type', 'quantity', 'notes', 'site_id', 'user_id', 'organisation_id', 'simpro_id', 'archived'];
    protected $dates = ['date_due'];

    public function Controls_Type()
    {
        return $this->belongsTo('App\Models\Controls_Type', 'control_type');
    }

    public function Site()
    {
        return $this->belongsTo('App\Models\Site', 'site_id');
    }

    public function User()
    {
        return $this->belongsTo('App\Models\Profile', 'user_id');
    }

    public function Organisation()
    {
        return $this->belongsTo('App\Models\Profile', 'organisation_id');
    }
}
