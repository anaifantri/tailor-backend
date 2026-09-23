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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();

            // Relasi ULID
            $table->foreignUlid('order_ulid')->constrained('orders', 'ulid')->cascadeOnDelete();
            $table->foreignUlid('clothing_type_ulid')->nullable()->constrained('clothing_types', 'ulid')->nullOnDelete();
            $table->foreignUlid('material_ulid')->nullable()->constrained('materials', 'ulid')->nullOnDelete();

            // Relasi Internal ID (BigInteger)
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('clothing_type_id')->nullable()->constrained('clothing_types')->nullOnDelete();
            $table->foreignId('material_id')->nullable()->constrained('materials')->nullOnDelete();

            $table->integer('quantity')->default(1);
            $table->json('measurements');
            $table->decimal('price', 12, 2)->default(0.00); 
            $table->decimal('fabric_consumed_meter', 5, 2)->default(0.00); 
            $table->text('notes')->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};