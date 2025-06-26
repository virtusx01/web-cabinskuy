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
           $table->increments('id_payment'); // Primary key untuk payment

            // Foreign Key untuk Booking
            // Menghubungkan ke tabel 'bookings' menggunakan id_booking sebagai primary key di tabel bookings
            $table->unsignedInteger('id_booking'); // Menggunakan unsignedInteger karena id_booking di bookings adalah increments()
            $table->foreign('id_booking')->references('id_booking')->on('bookings')->onDelete('cascade');

            // --- Payment Details ---
            $table->decimal('amount', 12, 2); // Jumlah pembayaran
            $table->string('payment_method')->nullable(); // Contoh: 'Credit Card', 'Bank Transfer', 'E-Wallet'
            $table->string('transaction_id')->nullable()->unique(); // ID transaksi dari gateway pembayaran, bisa null jika pembayaran tunai
            $table->json('payment_details')->nullable(); // Menggunakan 'json' cast di model, jadi lebih baik tipe kolom ini juga 'json'
                                                         // atau 'text' jika database lama dan belum mendukung 'json'
            // PERBAIKAN: Enum status harus mencakup semua status yang mungkin dari Midtrans dan logika aplikasi.
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled', 'expired', 'challenge'])->default('pending');

            // Foreign Key untuk user yang melakukan pembayaran (bisa sama dengan user_id di booking atau admin yang memproses)
            // PERBAIKAN: Ini harus mereferensikan tabel 'users', bukan 'bookings'.
            // Asumsi tabel 'users' memiliki primary key 'id_user' dengan tipe string.
            $table->string('id_user');
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');

            $table->timestamps(); // created_at (tanggal pembayaran), updated_at

            // --- Indexes untuk Performa ---
            $table->index('id_booking'); // Untuk mencari pembayaran berdasarkan booking
            $table->index('status');     // Untuk mencari pembayaran berdasarkan status
            $table->index('id_user');    // Untuk mencari pembayaran berdasarkan pembayar
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
