<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activities_Trades extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['activities_id', 'trades_id'];

    public function Activities()
    {
        return $this->belongsTo('App\Models\Activities', 'activities_id');
    }

    public function Trades()
    {
        return $this->belongsTo('App\Models\Trade', 'trades_id');
    }
}
