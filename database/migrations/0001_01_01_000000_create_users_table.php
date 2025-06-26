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
        Schema::create('users', function (Blueprint $table) {
            // Kolom-kolom yang disesuaikan dari Laravel 10
            $table->id(); // Primary key auto-increment standar
            $table->string('id_user')->unique(); // ID kustom Anda
            $table->string('name');
            $table->string('email')->unique();
            $table->enum('role', ['owner', 'admin', 'customer'])->default('customer');
            $table->boolean('status')->default(true); // Menggunakan boolean lebih baik, defaultnya true (1)
            $table->string('google_id')->nullable(); // Untuk menyimpan ID dari Google
            $table->string('google_avatar_url')->nullable(); // Untuk menyimpan URL avatar dari Google
            $table->string('password')->nullable(); // Dibuat nullable untuk mengakomodasi login via Google
            $table->string('hp', 15)->nullable(); // Panjang disarankan 15, dibuat nullable
            $table->string('profile_photo_path')->nullable();
            
            // Kolom-kolom standar Laravel yang tetap dipertahankan
            $table->timestamp('email_verified_at')->nullable(); // Sebaiknya tetap ada
            $table->rememberToken(); // Penting untuk fitur "Ingat Saya"
            $table->timestamps();
        });

        // Tabel-tabel bawaan Laravel 11/12 ini sebaiknya tetap ada karena penting untuk fungsionalitas inti
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
