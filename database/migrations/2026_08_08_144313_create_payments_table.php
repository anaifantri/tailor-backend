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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();

            // Foreign Key ULID
            $table->foreignUlid('user_ulid')->constrained('users', 'ulid')->cascadeOnDelete();
            $table->foreignUlid('order_ulid')->constrained('orders', 'ulid')->cascadeOnDelete();

            // Foreign Key Internal ID (BigInteger)
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();

            $table->date('payment_date');
            $table->decimal('amount_paid', 12, 2)->default(0.00);
            $table->string('payment_method');
            $table->string('payment_status');
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};