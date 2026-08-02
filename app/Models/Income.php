<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'amount',
        'date',
        'category_id',
        'wallet_id',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
