<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['goal_id', 'title', 'amount', 'date'];

    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }    
}
