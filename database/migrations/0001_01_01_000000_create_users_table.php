<?php

// File: database/migrations/xxxx_xx_xx_xxxxxx_create_users_table.php
// Catatan: Nama file migrasi akan berbeda sesuai tanggal pembuatan.

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
            $table->id(); // Kolom ID auto-increment primary
            $table->string('id_user')->unique(); // Kolom ID unik kustom Anda
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable(); // Pindahkan ke atas agar lebih rapi
            $table->string('password')->nullable(); // Dibuat nullable karena pengguna Google tidak punya password
            $table->enum('role', ['superadmin', 'admin', 'customer'])->default('customer');
            $table->boolean('status')->default(true); // Perbaikan sintaks. Default(true) sama dengan 1.
            $table->string('hp', 15)->nullable(); // Dibuat nullable dan panjangnya ditambah sedikit
            // Kolom spesifik untuk Google Auth
            $table->string('google_id')->nullable();
            $table->string('google_avatar_url')->nullable();
            $table->string('profile_photo_path')->nullable();

            // Kolom bawaan Laravel
            $table->rememberToken();
            $table->timestamps();
            
            // Hapus kolom duplikat dan yang tidak perlu dari file asli Anda
            // $table->string('profile_photo_path')->nullable(); // Anda bisa gunakan google_avatar_url
        });

        // Tabel password_reset_tokens dan sessions tidak perlu diubah, sudah benar.
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