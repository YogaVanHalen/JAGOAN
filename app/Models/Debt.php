<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'type',
        'initial_amount',
        'remaining_amount',
        'monthly_installment',
        'tenor_months',
        'remaining_tenor_months',
        'due_day',
        'color',
        'interest_rate_percent',
        'auto_accrue_interest',
        'last_interest_accrued_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function goal()
    {
        return $this->hasOne(Goal::class);
    }

    public function syncGoalProgress()
    {
        if ($this->goal) {
            $initial = (float) $this->initial_amount;
            $remaining = (float) $this->remaining_amount;
            $progress = max(0, $initial - $remaining);

            $this->goal->update([
                'target' => $initial > 0 ? $initial : $this->goal->target,
                'progress' => $progress,
            ]);
        }
    }

    public static function checkAndAccrueInterestForUser($userId)
    {
        $today = \Carbon\Carbon::now();
        $currentMonthYear = $today->format('Y-m');

        $debts = self::where('user_id', $userId)
            ->where('auto_accrue_interest', true)
            ->where('remaining_amount', '>', 0)
            ->where('interest_rate_percent', '>', 0)
            ->get();

        foreach ($debts as $debt) {
            $dueDay = $debt->due_day ?? 10;
            // H+1 setelah jatuh tempo (day after due_day)
            $dayAfterDueDay = $dueDay + 1;

            $alreadyAccruedThisMonth = $debt->last_interest_accrued_at && \Carbon\Carbon::parse($debt->last_interest_accrued_at)->format('Y-m') === $currentMonthYear;

            if (!$alreadyAccruedThisMonth && $today->day >= $dayAfterDueDay) {
                $interestAmount = round($debt->remaining_amount * ($debt->interest_rate_percent / 100));
                if ($interestAmount > 0) {
                    $debt->remaining_amount += $interestAmount;
                    $debt->last_interest_accrued_at = $today->toDateString();
                    $debt->save();
                    $debt->syncGoalProgress();
                }
            }
        }

        $wallets = Wallet::where('user_id', $userId)
            ->where('is_credit', true)
            ->where('auto_accrue_interest', true)
            ->where('balance', '>', 0)
            ->where('interest_rate_percent', '>', 0)
            ->get();

        foreach ($wallets as $wallet) {
            $alreadyAccruedThisMonth = $wallet->last_interest_accrued_at && \Carbon\Carbon::parse($wallet->last_interest_accrued_at)->format('Y-m') === $currentMonthYear;

            if (!$alreadyAccruedThisMonth && $today->day >= 11) {
                $interestAmount = round($wallet->balance * ($wallet->interest_rate_percent / 100));
                if ($interestAmount > 0) {
                    $wallet->balance += $interestAmount;
                    $wallet->last_interest_accrued_at = $today->toDateString();
                    $wallet->save();

                    $debt = self::where('user_id', $userId)->where('name', $wallet->name)->first();
                    if ($debt) {
                        $debt->remaining_amount = $wallet->balance;
                        $debt->last_interest_accrued_at = $today->toDateString();
                        $debt->save();
                        $debt->syncGoalProgress();
                    }
                }
            }
        }
    }
}
