<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth; // Tambahkan ini jika Anda ingin mendapatkan inisial dari Auth::user()
use Illuminate\Support\Str; // Tambahkan ini untuk helper string

class Booking extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     */
    protected $table = 'bookings';

    /**
     * Primary key untuk model ini.
     */
    protected $primaryKey = 'id_booking';

    /**
     * Auto-incrementing ID.
     * Ubah ini menjadi false karena kita akan mengisi manual.
     */
    public $incrementing = false; // <<< UBAH INI KE FALSE

    /**
     * The "type" of the auto-incrementing ID.
     * Ubah ini menjadi string karena id_booking akan berisi huruf dan angka.
     */
    protected $keyType = 'string'; // <<< UBAH INI KE STRING

    /**
     * Atribut yang dapat diisi secara massal.
     * Pastikan 'id_booking' ada di fillable.
     */
    protected $fillable = [
        'id_booking', // <<< PASTIKAN INI ADA
        'id_user',
        'id_cabin',
        'id_room',
        'check_in_date',
        'check_out_date',
        'total_guests',
        'total_nights',
        'total_price',
        'special_requests',
        'contact_name',
        'contact_phone',
        'contact_email',
        'status',
        'checkin_room',
        'snap_token',
        'booking_date',
        'admin_notes',
        'confirmed_at',
        'confirmed_by',
        'rejected_at',
        'rejected_by',
        'rejection_reason',
        'cancelled_at',
        'cancellation_reason',
        'qr_validation_token',
    ];

    /**
     * Tipe data casting.
     */
    protected $casts = [
        'check_in_date'  => 'date',
        'check_out_date' => 'date',
        'booking_date'   => 'datetime',
        'confirmed_at'   => 'datetime',
        'rejected_at'    => 'datetime',
        'cancelled_at'   => 'datetime',
        'total_price'    => 'decimal:2',
        'total_guests'   => 'integer',
        'total_nights'   => 'integer',
    ];

    /**
     * The "booted" method of the model.
     * Used to add event listeners when the model is initialized.
     * Kita akan generate ID di sini.
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            // Pastikan id_booking belum diisi secara manual, agar tidak menimpa jika diisi dari luar
            if (empty($booking->id_booking)) {
                // Ambil nama pengguna yang membuat booking
                // Mengambil dari relasi user() lebih aman jika booking dibuat oleh admin atas nama user lain
                // Jika booking selalu dibuat oleh Auth::user(), bisa juga pakai Auth::user()->name
                $userName = $booking->contact_name; // Menggunakan contact_name sebagai sumber nama

                // Generate inisial dari nama
                $initials = '';
                foreach (explode(' ', $userName) as $word) {
                    $initials .= strtoupper(substr($word, 0, 1));
                }
                
                // Jika nama kosong atau tidak menghasilkan inisial, fallback ke CUS
                if (empty($initials)) {
                    $initials = 'CUS'; 
                }

                // Format tanggal dan waktu saat ini
                $timestamp = Carbon::now()->format('ymdHis'); // TahunBulanTanggalJamMenitDetik

                // Gabungkan untuk membentuk id_booking
                $booking->id_booking = $initials . $timestamp;
            }
        });
    }

    // --- RELATIONSHIPS ---
    // ... (relasi Anda yang sudah ada tetap sama)

    /**
     * Relasi ke model User (pemesan).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Relasi ke model Cabin.
     */
    public function cabin()
    {
        return $this->belongsTo(Cabin::class, 'id_cabin', 'id_cabin');
    }

    /**
     * Relasi ke model CabinRoom.
     */
    public function room()
    {
        return $this->belongsTo(CabinRoom::class, 'id_room', 'id_room');
    }

    /**
     * Relasi ke User yang mengkonfirmasi (admin).
     */
    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by', 'id_user');
    }

    /**
     * Relasi ke User yang menolak (admin).
     */
    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by', 'id_user');
    }

    /**
     * Relasi ke model Payment.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'id_booking', 'id_booking');
    }

    /**
     * Relasi ke Payment terbaru.
     */
    public function latestPayment()
    {
        return $this->hasOne(Payment::class, 'id_booking', 'id_booking')
                    ->latest();
    }

    /**
     * Relasi ke Payment yang berhasil.
     */
    public function successfulPayment()
    {
        return $this->hasOne(Payment::class, 'id_booking', 'id_booking')
                    ->where('status', 'completed');
    }

    // --- SCOPES ---
    // ... (scopes Anda yang sudah ada tetap sama)

    /**
     * Scope untuk booking yang pending.
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'challenge']);
    }

    /**
     * Scope untuk booking yang sudah dikonfirmasi.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope untuk booking yang berhasil (completed).
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope untuk filter berdasarkan status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk booking yang aktif pada rentang tanggal tertentu.
     * PENTING: Untuk mengecek ketersediaan slot.
     */
    public function scopeActiveOnDateRange($query, $checkIn, $checkOut)
    {
        return $query->whereIn('status', ['pending', 'confirmed', 'challenge'])
                     ->where(function ($q) use ($checkIn, $checkOut) {
                         $q->where('check_in_date', '<', $checkOut)
                           ->where('check_out_date', '>', $checkIn);
                     });
    }

    // --- ACCESSORS ---
    // ... (accessors Anda yang sudah ada tetap sama)

    /**
     * Accessor untuk label status yang user-friendly.
     */
    public function getStatusLabelAttribute()
    {
        $statusLabels = [
            'pending'   => 'Menunggu Pembayaran',
            'confirmed' => 'Dikonfirmasi',
            'rejected'  => 'Ditolak',
            'cancelled' => 'Dibatalkan',
            'completed' => 'Pembayaran Berhasil',
            'challenge' => 'Verifikasi Fraud',
            'expired'   => 'Pembayaran Kadaluarsa',
            'failed'    => 'Pembayaran Gagal',
        ];
        return $statusLabels[$this->status] ?? 'Status Tidak Dikenal';
    }

    /**
     * Accessor untuk class badge status.
     */
    public function getStatusBadgeClassAttribute()
    {
        $statusClasses = [
            'pending'   => 'badge-warning',
            'confirmed' => 'badge-success',
            'rejected'  => 'badge-danger',
            'cancelled' => 'badge-secondary',
            'completed' => 'badge-primary',
            'challenge' => 'badge-info',
            'expired'   => 'badge-danger',
            'failed'    => 'badge-danger',
        ];
        return $statusClasses[$this->status] ?? 'badge-secondary';
    }

    /**
     * Accessor untuk format harga.
     */
    public function getFormattedTotalPriceAttribute()
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    // --- BUSINESS LOGIC METHODS ---
    // ... (metode bisnis Anda yang sudah ada tetap sama)

    /**
     * Cek apakah booking sudah lunas.
     */
    public function isPaid(): bool
    {
        return $this->successfulPayment()->exists();
    }

    /**
     * Mengambil total jumlah yang telah dibayarkan.
     */
    public function getPaidAmount(): float
    {
        return (float) $this->payments()
                             ->where('status', 'completed')
                             ->sum('amount');
    }

    /**
     * Method untuk mengecek apakah ada payment yang sedang pending.
     */
    public function hasPendingPayment(): bool
    {
        return $this->payments()
                    ->whereIn('status', ['pending', 'challenge'])
                    ->exists();
    }

    /**
     * Method untuk cek apakah booking ini sedang pending.
     */
    public function isPending(): bool
    {
        return in_array($this->status, ['pending', 'challenge']);
    }

    /**
     * Method untuk cek apakah booking ini berhasil (completed).
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Method untuk cek apakah booking ini gagal.
     */
    public function isFailed(): bool
    {
        return in_array($this->status, ['failed', 'rejected', 'cancelled', 'expired']);
    }

    /**
     * Cek apakah booking bisa dibatalkan oleh user.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'challenge']);
    }

    /**
     * Konfirmasi booking.
     */
    public function confirm($adminId, $notes = null): bool
    {
        if (!in_array($this->status, ['pending', 'challenge'])) {
            return false;
        }
        
        return $this->update([
            'status'        => 'confirmed',
            'confirmed_at'  => now(),
            'confirmed_by'  => $adminId,
            'admin_notes'   => $notes,
        ]);
    }

    /**
     * Tolak booking.
     */
    public function reject($adminId, $reason, $notes = null): bool
    {
        if (!in_array($this->status, ['pending', 'challenge'])) {
            return false;
        }
        
        return $this->update([
            'status'           => 'rejected',
            'rejected_at'      => now(),
            'rejected_by'      => $adminId,
            'rejection_reason' => $reason,
            'admin_notes'      => $notes,
        ]);
    }

    /**
     * Batalkan booking.
     */
    public function cancel($reason = null): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }
        
        return $this->update([
            'status'            => 'cancelled',
            'cancelled_at'      => now(),
            'cancellation_reason' => $reason ?? 'Dibatalkan oleh user',
        ]);
    }

    /**
     * Method untuk menandai booking sebagai expired.
     */
    public function markAsExpired(): bool
    {
        return $this->update([
            'status' => 'expired'
        ]);
    }

    /**
     * Method untuk menandai booking sebagai failed.
     */
    public function markAsFailed(): bool
    {
        return $this->update([
            'status' => 'failed'
        ]);
    }

    /**
     * Method untuk menandai booking sebagai completed.
     */
    public function markAsCompleted(): bool
    {
        return $this->update([
            'status' => 'completed'
        ]);
    }
}