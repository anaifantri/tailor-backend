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
        Schema::create('production_progress', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();

            // Relasi ULID & Internal BigInteger Foreign Key
            $table->foreignUlid('order_detail_ulid')
                  ->constrained('order_details', 'ulid')
                  ->cascadeOnDelete();
                  
            $table->foreignId('order_detail_id')
                  ->constrained('order_details')
                  ->cascadeOnDelete();

            $table->string('status')->default('queued'); 
            $table->date('progress_date');
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_progress');
    }
};