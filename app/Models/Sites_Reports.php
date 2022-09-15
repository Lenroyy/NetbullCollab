<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sites_Reports extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = ['site_id', 'report_name', 'frequency', 'format', 'email_address'];

    public function Site()
    {
        return $this->belongsTo('App\Models\Site', 'site_id');
    }


}
