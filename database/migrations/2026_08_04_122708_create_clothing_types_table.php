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
        Schema::create('clothing_types', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->string('code')->unique();
            $table->string('type')->unique();
            $table->string('category');
            $table->decimal('base_price', 10, 2)->default(0.00); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clothing_types');
    }
};