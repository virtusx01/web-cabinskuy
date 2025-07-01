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
            $table->integer('checkin_room'); // Asumsi ini jumlah slot yang diambil/kamar yang dipesan
            $table->integer('total_guests');
            $table->integer('total_nights');
            $table->decimal('total_price', 12, 2);
            $table->text('special_requests')->nullable();

            // --- Guest Contact Information ---
            $table->string('contact_name');
            $table->string('contact_phone', 20)->nullable();
            $table->string('contact_email');

            // --- Booking Status & Tracking ---
            $table->string('snap_token')->nullable();
            // --- UPDATED ENUM VALUES ---
            $table->enum('status', ['pending', 'confirmed', 'rejected', 'cancelled', 'completed', 'challenge', 'expired', 'failed'])->default('pending');
            $table->timestamp('booking_date')->useCurrent();

            // --- Admin & Confirmation/Rejection/Cancellation Tracking ---
            $table->text('admin_notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->string('confirmed_by')->nullable();
            $table->foreign('confirmed_by')->references('id_user')->on('users')->onDelete('set null');

            $table->timestamp('rejected_at')->nullable();
            $table->string('rejected_by')->nullable();
            $table->foreign('rejected_by')->references('id_user')->on('users')->onDelete('set null');
            $table->text('rejection_reason')->nullable();

            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->string('qr_validation_token', 64)->unique()->nullable();
            // --- NEW: COMPLETED STATUS TRACKING ---
            $table->timestamp('completed_at')->nullable(); // Add this line
            $table->string('completed_by')->nullable(); // This was already there, ensure correct placement
            $table->foreign('completed_by')->references('id_user')->on('users')->onDelete('set null');
            // --- END NEW ---

            $table->timestamps(); // created_at dan updated_at

            // --- Indexes for Performance ---
            $table->index(['id_user', 'status']);
            $table->index(['id_room', 'status']);
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