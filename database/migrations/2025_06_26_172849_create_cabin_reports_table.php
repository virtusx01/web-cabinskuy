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
        Schema::create('cabin_reports', function (Blueprint $table) {
           $table->id(); // Kunci utama (ID) untuk setiap laporan
            
            // Tanggal laporan ini dibuat, unik agar tidak ada laporan ganda di hari yang sama
            $table->date('report_date')->unique(); 
            
            // Kolom untuk menyimpan statistik
            $table->unsignedInteger('total_cabins');
            $table->unsignedInteger('total_users');
            $table->unsignedInteger('total_bookings');
            
            // Gunakan tipe data DECIMAL untuk total pendapatan agar presisi
            $table->decimal('total_revenue', 15, 2)->default(0); 
            
            // Kolom JSON untuk menyimpan snapshot dari pemesanan terbaru.
            // Ini lebih baik daripada menyimpannya sebagai teks biasa.
            $table->json('recent_bookings_snapshot')->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cabin_reports');
    }
};
