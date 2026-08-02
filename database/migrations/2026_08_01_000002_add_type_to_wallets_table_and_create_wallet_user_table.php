<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            if (!Schema::hasColumn('wallets', 'type')) {
                $table->string('type')->default('personal')->after('user_id');
            }
        });

        if (!Schema::hasTable('wallet_user')) {
            Schema::create('wallet_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('role')->default('member'); // 'owner', 'member'
                $table->timestamps();

                $table->unique(['wallet_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_user');

        Schema::table('wallets', function (Blueprint $table) {
            if (Schema::hasColumn('wallets', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
