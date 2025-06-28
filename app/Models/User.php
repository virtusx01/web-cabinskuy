<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     * Disesuaikan agar cocok dengan kolom pada file migrasi.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_user',
        'name',
        'email',
        'password', // Diperlukan untuk pendaftaran biasa
        'role',
        'status',
        'hp',
        'google_id', // Untuk menyimpan ID unik dari Google
        'google_avatar_url', // Untuk menyimpan URL avatar dari Google
        'email_verified_at', // Bisa diisi saat login via Google
    ];

    /**
     * Atribut yang harus disembunyikan saat serialisasi.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atribut yang harus di-casting ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'boolean', // Ditambahkan karena tipe kolomnya boolean
    ];

    /**
     * Method "booted" dari model.
     * Digunakan untuk menambahkan event listener saat model diinisialisasi.
     *
     * @return void
     */
    protected static function booted(): void
    {
        /**
         * Event 'creating' akan berjalan TEPAT SEBELUM user baru disimpan ke database.
         * Ini memastikan setiap user baru akan memiliki 'id_user' yang unik.
         */
        static::creating(function ($user) {
            // Jika id_user belum diisi, maka buat ID baru.
            if (empty($user->id_user)) {
                $prefix = '';
                switch ($user->role) {
                    case 'admin':
                        $prefix = 'ADM';
                        break;
                    case 'superadmin':
                        $prefix = 'SPADM';
                        break;
                    case 'customer': // Asumsi default jika tidak ada role atau role lain
                    default:
                        $prefix = 'CUS';
                        break;
                }

                // Format: 'PREFIX' + Tahun (2 digit) + Bulan + 4 angka acak
                // Contoh: CUS25061234, ADM25065678, SPADM25069012
                $user->id_user = $prefix . date('ym') . mt_rand(1000, 9999);
            }
        });
    }

    /**
     * Cek apakah user memiliki peran 'admin'.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user memiliki peran 'superadmin'.
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    /**
     * Cek apakah user memiliki peran 'customer'.
     *
     * @return bool
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }
    
    public function bookings()
{
    return $this->hasMany(Booking::class, 'id_user'); // Use your actual foreign key name
}
}