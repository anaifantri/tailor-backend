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
        Schema::create('order_cancellations', function (Blueprint $table) {
            $table->id();
            // 1. Relasi ke nota utama (Wajib terisi untuk semua skenario pembatalan)
            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->onDelete('cascade'); // Jika pesanan dihapus permanen dari sistem, log batal ikut terhapus
            
            // 2. Relasi ke detail item pakaian (HANYA TERISI jika pembatalan bersifat PARSIAL)
            $table->foreignId('order_detail_id')
                  ->nullable() // Null berarti pembatalan total satu nota
                  ->constrained('order_details')
                  ->onDelete('cascade');
            
            // 3. Relasi ke user admin/kasir yang menyetujui pembatalan
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('restrict'); // Akun admin tidak bisa dihapus jika ada riwayat pembatalan
            
            // 4. Informasi Finansial Pembatalan
            $table->string('cancellation_type'); // 'full' atau 'partial'
            $table->decimal('refund_amount', total: 12, places: 0)->default(0); // Nominal uang kembalian ke pelanggan (jika sudah DP)
            
            // 5. Alasan pembatalan (misal: "Bahan kain habis", "Pelanggan berubah pikiran")
            $table->text('reason');
            
            $table->dateTime('cancelled_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_cancellations');
    }
};
