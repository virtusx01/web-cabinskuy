<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // Pastikan model User diimpor
use Illuminate\Support\Facades\Hash; // Impor Fassade Hash

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        // Membuat atau mencari user 'owner'
        User::firstOrCreate(
            [
                // Kriteria untuk mencari user (harus unik)
                'email' => 'owner@gmail.com'
            ],
            [
                // Data yang akan diisi HANYA JIKA user belum ada.
                // 'id_user' akan dibuat otomatis oleh Model User.
                'name' => 'Aan',
                'role' => 'superadmin',
                'status' => true,
                'hp' => '081234567890',
                'password' => Hash::make('owner123'), // Gunakan Hash::make()
            ]
        );

        // Membuat atau mencari user 'owner'
        User::firstOrCreate(
            [
                'email' => 'admin@gmail.com'
            ],
            [
                'name' => 'Sires',
                'role' => 'admin',
                'status' => true,
                'hp' => '081234567891',
                'password' => Hash::make('admin123'), // Gunakan Hash::make()
            ]
        );

        // Membuat atau mencari user 'customer'
        User::firstOrCreate(
            [
                'email' => 'mnfaan88@gmail.com'
            ],
            [
                'name' => 'Muhammad Nur Fauzan',
                'role' => 'customer', // PERBAIKAN: Ubah '0' menjadi nilai enum yang valid
                'status' => true,
                'hp' => '081574422949',
                'password' => Hash::make('mnfaan123'), // Gunakan Hash::make()
            ]
        );

        // Anda juga bisa menggunakan factory untuk membuat data dummy dalam jumlah besar
        // \App\Models\User::factory(10)->create();
    }
}
