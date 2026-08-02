<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('name');
            $table->string('type')->default('KPR'); // KPR, Kartu Kredit, KKB, Pinjaman Bank, Paylater, Lainnya
            $table->decimal('initial_amount', 15, 2);
            $table->decimal('remaining_amount', 15, 2);
            $table->decimal('monthly_installment', 15, 2)->default(0);
            $table->integer('tenor_months')->default(12);
            $table->integer('remaining_tenor_months')->default(12);
            $table->integer('due_day')->default(10); // Tanggal jatuh tempo per bulan (1 - 31)
            $table->string('color')->default('#ef4444');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
