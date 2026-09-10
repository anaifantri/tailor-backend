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
        Schema::create('tailor_assignments', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('order_detail_id')->constrained('order_details')->onDelete('cascade');
            $table->foreignId('tailor_id')->constrained('tailors')->onDelete('cascade');
            $table->date('assignment_date');
            $table->integer('quantity_assigned')->default(1);
            $table->decimal('labor_cost', total: 12, places: 0)->default(0);
            $table->decimal('total_labor_cost', total: 12, places: 0)->default(0); 
            $table->string('status')->nullable()->default('queued');
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tailor_assignments');
    }
};
