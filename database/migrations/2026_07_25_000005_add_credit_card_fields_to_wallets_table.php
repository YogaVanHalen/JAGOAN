<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->boolean('is_credit')->default(false)->after('balance'); // true for Kartu Kredit, Paylater, Pinjol
            $table->string('card_network')->nullable()->after('is_credit'); // Visa, Mastercard, Amex, JCB, Paylater, Pinjol
            $table->string('first_four_digits')->nullable()->after('card_network'); // e.g. 4123 (BIN)
            $table->decimal('interest_rate_percent', 5, 2)->default(0)->after('first_four_digits'); // Suku bunga % / bulan
            $table->decimal('credit_limit', 15, 2)->default(0)->after('interest_rate_percent'); // Limit kredit
        });
    }

    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn([
                'is_credit',
                'card_network',
                'first_four_digits',
                'interest_rate_percent',
                'credit_limit',
            ]);
        });
    }
};
