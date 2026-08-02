<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->unsignedBigInteger('wallet_id')->nullable()->after('category_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('wallet_id')->nullable()->after('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropColumn('wallet_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('wallet_id');
        });
    }
};
