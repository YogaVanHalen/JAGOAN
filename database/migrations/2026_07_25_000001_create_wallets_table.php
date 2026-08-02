<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->string('bank_name')->default('Bank / Cash'); // e.g. BCA, Mandiri, BRI, GoPay, OVO, Tunai
            $table->string('account_number')->nullable();
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('color')->default('#6366f1');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
