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
        Schema::create('measurement_histories', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            
            // Relasi ke Customer & ClothingType menggunakan ULID
            $table->foreignUlid('customer_ulid')
                  ->constrained('customers', 'ulid')
                  ->cascadeOnDelete();

            $table->foreignUlid('clothing_type_ulid')
                  ->nullable()
                  ->constrained('clothing_types', 'ulid')
                  ->nullOnDelete();

            // Foreign Key Internal (Auto-Increment) untuk performa join DB
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('clothing_type_id')->nullable()->constrained('clothing_types')->nullOnDelete();

            $table->string('category');
            $table->string('measured_by');
            $table->date('measured_at');
            $table->json('measurement_details');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('measurement_histories');
    }
};