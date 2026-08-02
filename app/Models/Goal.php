<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $fillable = [
        'title', 'description', 'target', 'progress', 'user_id', 'debt_id',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function debt()
    {
        return $this->belongsTo(Debt::class);
    }

    public function syncFromDebt()
    {
        if ($this->debt_id && $this->debt) {
            $initial = (float) $this->debt->initial_amount;
            $remaining = (float) $this->debt->remaining_amount;
            $progress = max(0, $initial - $remaining);

            $this->update([
                'target' => $initial > 0 ? $initial : $this->target,
                'progress' => $progress,
            ]);
        }
    }
}