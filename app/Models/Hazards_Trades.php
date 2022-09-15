<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hazards_Trades extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['trade_id', 'hazard_id'];

    public function Trade()
    {
        return $this->belongsTo('App\Models\Trade', 'trade_id');
    }

    public function Hazard()
    {
        return $this->belongsTo('App\Models\Hazard', 'hazard_id');
    }
}
