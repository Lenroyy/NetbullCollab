<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billings_Details extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['billing_id', 'type', 'reference', 'cost']; 

    public function Profile()
    {
        return $this->belongsTo('App\Models\Billing', 'billing_id');
    }

}
