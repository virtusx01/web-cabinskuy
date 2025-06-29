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
        Schema::create('bookings', function (Blueprint $table) {
            $table->string('id_booking', 25)->primary();

            // PERBAIKAN: Menggunakan foreignId untuk cara Laravel yang lebih modern dan ringkas
            // Asumsi tabel 'users' memiliki primary key 'id_user' dengan tipe string.
            // Jika PK 'users' adalah `id` (integer) maka gunakan $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->string('id_user');
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');

            $table->string('id_cabin', 10);
            $table->foreign('id_cabin')->references('id_cabin')->on('cabins')->onDelete('cascade');

            $table->string('id_room', 10);
            $table->foreign('id_room')->references('id_room')->on('cabin_rooms')->onDelete('cascade');

            // --- Booking Details ---
            $table->date('check_in_date');
            $table->date('check_out_date');
            // 'checkin_room' perlu dikonfirmasi penggunaannya. Jika untuk jumlah tamu, gunakan total_guests.
            // Jika ini memang merepresentasikan "slot" yang diambil, maka biarkan, tapi namanya ambigu.
            // Saya asumsikan ini adalah 'total_guests' atau 'occupancy_count'.
            // Kalau ini adalah jumlah kamar yang dipesan, dan slot_room adalah jumlah kamar yang tersedia, maka ini benar.
            // Untuk saat ini saya biarkan sesuai aslinya.
            $table->integer('checkin_room');
            $table->integer('total_guests');
            $table->integer('total_nights');
            $table->decimal('total_price', 12, 2);
            $table->text('special_requests')->nullable();

            // --- Guest Contact Information ---
            $table->string('contact_name');
            $table->string('contact_phone', 20)->nullable(); // Make nullable as per model fillable
            $table->string('contact_email');

            // --- Booking Status & Tracking ---
            // PENJELASAN: Status 'confirmed' dan 'pending' akan kita gunakan untuk menghitung slot yang terisi.
            // PERBAIKAN: Tambahkan 'initiated', 'challenge' (dari Midtrans), 'failed', 'expired' untuk konsistensi dengan model Payment.
            $table->string('snap_token')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'rejected', 'cancelled', 'completed', 'challenge', 'expired', 'failed'])->default('pending');
            $table->timestamp('booking_date')->useCurrent(); // Default to current timestamp

            // --- Admin & Confirmation/Rejection/Cancellation Tracking ---
            $table->text('admin_notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            // PERBAIKAN: Pastikan referensi ke tabel users dengan id_user.
            $table->string('confirmed_by')->nullable();
            $table->foreign('confirmed_by')->references('id_user')->on('users')->onDelete('set null');

            $table->timestamp('rejected_at')->nullable();
            // PERBAIKAN: Pastikan referensi ke tabel users dengan id_user.
            $table->string('rejected_by')->nullable();
            $table->foreign('rejected_by')->references('id_user')->on('users')->onDelete('set null');
            $table->text('rejection_reason')->nullable();

            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();

            $table->timestamps(); // created_at dan updated_at

            // --- Indexes untuk Performa ---
            $table->index(['id_user', 'status']); // Untuk mencari booking user
            $table->index(['id_room', 'status']); // Untuk mencari booking berdasarkan kamar & status

            // PENJELASAN: Ini adalah index paling PENTING untuk logika slot.
            // Ini akan mempercepat query pengecekan ketersediaan slot pada rentang tanggal tertentu.
            $table->index(['id_room', 'check_in_date', 'check_out_date', 'status'], 'bookings_availability_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
