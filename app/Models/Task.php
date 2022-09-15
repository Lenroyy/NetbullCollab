<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['subject', 'description', 'notes', 'assigned_id', 'status', 'priority', 'progress', 'site_id', 'archived'];
    protected $dates = ['start_date', 'due_date', 'completed_date'];

    public function Assigned()
    {
        return $this->belongsTo('App\Models\Profile', 'assigned_id');
    }

    public function Site()
    {
        return $this->belongsTo('App\Models\Site', 'site_id');
    }
}
