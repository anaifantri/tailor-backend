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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('number')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('customer_id')->constrained('orders')->onDelete('cascade');
            $table->date('order_date');
            $table->date('fitting_date')->nullable();
            $table->date('due_date');
            $table->decimal('discount', total: 12, places: 0)->default(0);
            $table->decimal('tax', total: 12, places: 0)->default(0);
            $table->decimal('total', total: 12, places: 0)->default(0);
            $table->text('notes')->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
