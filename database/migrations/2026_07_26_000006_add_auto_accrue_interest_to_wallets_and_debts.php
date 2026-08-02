<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            if (!Schema::hasColumn('wallets', 'auto_accrue_interest')) {
                $table->boolean('auto_accrue_interest')->default(false)->after('credit_limit');
            }
            if (!Schema::hasColumn('wallets', 'last_interest_accrued_at')) {
                $table->date('last_interest_accrued_at')->nullable()->after('auto_accrue_interest');
            }
        });

        Schema::table('debts', function (Blueprint $table) {
            if (!Schema::hasColumn('debts', 'interest_rate_percent')) {
                $table->decimal('interest_rate_percent', 5, 2)->default(0)->after('remaining_amount');
            }
            if (!Schema::hasColumn('debts', 'auto_accrue_interest')) {
                $table->boolean('auto_accrue_interest')->default(false)->after('interest_rate_percent');
            }
            if (!Schema::hasColumn('debts', 'last_interest_accrued_at')) {
                $table->date('last_interest_accrued_at')->nullable()->after('auto_accrue_interest');
            }
        });
    }

    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn(['auto_accrue_interest', 'last_interest_accrued_at']);
        });

        Schema::table('debts', function (Blueprint $table) {
            $table->dropColumn(['interest_rate_percent', 'auto_accrue_interest', 'last_interest_accrued_at']);
        });
    }
};
