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
            
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('clothing_type_id')->constrained('clothing_types');
            $table->foreignId('material_id')->nullable()->constrained('materials')->onDelete('set null');
            $table->integer('quantity')->default(1);
            $table->decimal('price', total: 10, places: 2)->default(0.00); 
            $table->decimal('fabric_consumed_meter', total: 5, places: 2)->default(0.00); 
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
