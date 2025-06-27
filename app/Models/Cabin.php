<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Make sure to import Str facade

class Cabin extends Model
{
    use HasFactory;

    /**
     * Beri tahu Eloquent bahwa Primary Key kita BUKAN integer dan tidak auto-increment.
     */
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id_cabin'; // Tentukan nama primary key

    /**
     * Kolom yang boleh diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'id_cabin',
        'name',
        'description',
        'province',
        'regency',
        'location_address',
        'cabin_photos',
        'status',
    ];

    /**
     * Boot method untuk mendaftarkan model event.
     * Kode di sini akan berjalan secara otomatis.
     */
    protected static function boot()
    {
        parent::boot();

        // Event 'creating' berjalan SEBELUM data baru disimpan ke database
        static::creating(function ($cabin) {
            // We remove the generation of id_cabin here because it's handled in the controller
            // to ensure it uses the actual province and regency names for the slug.
            // The controller will now be responsible for setting id_cabin before saving.
        });
    }

    /**
     * Fungsi ini sekarang tidak lagi digunakan untuk generating ID_Cabin
     * karena logicnya dipindahkan ke controller untuk akses data request.
     *
     * Jika Anda ingin tetap di model, Anda perlu memastikan 'province' dan 'regency'
     * tersedia di objek $cabin saat 'creating' event terpicu.
     *
     * @param string $provinceName
     * @param string $regencyName
     * @return string
     */
    public static function generateCabinId(string $provinceName, string $regencyName): string
    {
        // Use a combination of province and regency for a more unique and descriptive code
        $baseString = Str::slug($regencyName . '-' . $provinceName);
        // Take the first few characters of the slug for the base ID
        $baseCode = strtoupper(substr($baseString, 0, 5)); // e.g., "BOGOR-J" for Bogor, West Java

        // Find the last cabin with a similar base code to determine the next sequence number
        $lastCabin = self::where('id_cabin', 'LIKE', $baseCode . '%')
                             ->orderByDesc('id_cabin') // Order by id_cabin descending to get the truly last one
                             ->first();

        $nextNumber = 1;
        if ($lastCabin) {
            // Extract the numeric part from the last ID, e.g., from "BOGOR-J001", get "001"
            preg_match('/(\d+)$/', $lastCabin->id_cabin, $matches);
            if (isset($matches[1])) {
                $lastNumber = (int)$matches[1];
                $nextNumber = $lastNumber + 1;
            }
        }

        // Combine the base code with the sequential number, padded with zeros
        return $baseCode . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    protected $casts = [
        'cabin_photos' => 'array',
        'status' => 'boolean',
    ];
    // Tambahkan method ini di dalam class Cabin
    public function rooms()
    {
        // Setiap kabin bisa memiliki banyak ruangan (hasMany)
        return $this->hasMany(CabinRoom::class, 'id_cabin', 'id_cabin');
    }
}