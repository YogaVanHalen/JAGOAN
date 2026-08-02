<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'name',
        'bank_name',
        'account_number',
        'balance',
        'color',
        'is_credit',
        'card_network',
        'first_four_digits',
        'interest_rate_percent',
        'credit_limit',
        'auto_accrue_interest',
        'last_interest_accrued_at',
    ];

    public function getAvailableLimitAttribute()
    {
        if (!$this->is_credit || $this->credit_limit <= 0) {
            return 0;
        }
        return max(0, $this->credit_limit - $this->balance);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'wallet_user')->withPivot('role')->withTimestamps();
    }

    public function isOwner($userId): bool
    {
        return (int) $this->user_id === (int) $userId;
    }

    public static function getWalletsForUser($userId)
    {
        if (!$userId) return collect();
        return self::where('user_id', $userId)
            ->orWhereHas('members', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })
            ->with(['user', 'members'])
            ->get();
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public static function ensureDefaultWalletExists($userId)
    {
        if (!$userId) return;
        
        $cashWallet = self::where('user_id', $userId)->where('name', 'Tunai / Cash')->first();
        if (!$cashWallet) {
            self::create([
                'user_id' => $userId,
                'type' => 'personal',
                'name' => 'Tunai / Cash',
                'bank_name' => 'Tunai / Cash',
                'account_number' => 'DOMPET-TUNAI',
                'balance' => 0,
                'color' => '#10b981',
                'is_credit' => false,
            ]);
        }
    }
}
