<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // Pastikan model User diimpor

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Membuat atau mencari user admin
        User::firstOrCreate(
            [
                // Kriteria untuk mencari user (email harus unik)
                'email' => 'admin@gmail.com'
            ],
            [
                // Data yang akan diisi HANYA JIKA user dengan email di atas belum ada
                'id_user' => 'ADM' . date('ymd') . mt_rand(1000, 9999), // Tambahkan id_user karena ini adalah primary key
                'name' => 'Administrator',
                'role' => 'admin',
                'status' => true, // PERBAIKAN: Ubah 1 menjadi true (boolean)
                'hp' => '0812345678901',
                'password' => bcrypt('admin123'),
            ]
        );

        User::firstOrCreate(
            [
                // Kriteria untuk mencari user (email harus unik)
                'email' => 'mnfaan88@gmail.com'
            ],
            [
                // Data yang akan diisi HANYA JIKA user dengan email di atas belum ada
                'id_user' => 'USR' . date('ymd') . mt_rand(1000, 9999), // Tambahkan id_user karena ini adalah primary key
                'name' => 'Muhammad Nur Fauzan',
                'role' => '0', 
                'status' => true, // PERBAIKAN: Ubah 1 menjadi true (boolean)
                'hp' => '081574422949',
                'password' => bcrypt('mnfaan123'),
            ]
        );

        // Anda juga bisa menggunakan factory untuk membuat data dummy dalam jumlah besar
        // User::factory(10)->create();
    }
}
