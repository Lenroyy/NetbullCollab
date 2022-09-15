<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessments_Site extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['assessments_id', 'sites_id'];

    public function Assessment()
    {
        return $this->belongsTo('App\Models\Assessment', 'assessments_id');
    }

    public function Site()
    {
        return $this->belongsTo('App\Models\Site', 'sites_id');
    }
}
