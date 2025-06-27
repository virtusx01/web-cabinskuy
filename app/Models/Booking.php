<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';
    protected $primaryKey = 'id_booking';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
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
    ];

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

    // --- RELATIONSHIPS ---

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function cabin()
    {
        return $this->belongsTo(Cabin::class, 'id_cabin', 'id_cabin');
    }

    public function room()
    {
        return $this->belongsTo(CabinRoom::class, 'id_room', 'id_room');
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by', 'id_user');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by', 'id_user');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'id_booking', 'id_booking');
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class, 'id_booking', 'id_booking')
                    ->latest();
    }

    public function successfulPayment()
    {
        return $this->hasOne(Payment::class, 'id_booking', 'id_booking')
                    ->where('status', 'completed');
    }

    // --- SCOPES ---

    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'challenge']);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActiveOnDateRange($query, $checkIn, $checkOut)
    {
        return $query->whereIn('status', ['pending', 'confirmed', 'challenge'])
                     ->where(function ($q) use ($checkIn, $checkOut) {
                         $q->where('check_in_date', '<', $checkOut)
                           ->where('check_out_date', '>', $checkIn);
                     });
    }

    // --- ACCESSORS ---

    public function getStatusLabelAttribute()
    {
        $statusLabels = [
            'pending'   => 'Menunggu Pembayaran',
            'confirmed' => 'Dikonfirmasi',
            'rejected'  => 'Ditolak',
            'cancelled' => 'Dibatalkan',
            'completed' => 'Selesai',
            'challenge' => 'Verifikasi Fraud',
            'expired'   => 'Pembayaran Kadaluarsa',
            'failed'    => 'Pembayaran Gagal',
        ];
        return $statusLabels[$this->status] ?? 'Status Tidak Dikenal';
    }

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

    public function getFormattedTotalPriceAttribute()
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    // --- BUSINESS LOGIC METHODS ---

    public function isPaid(): bool
    {
        return $this->successfulPayment()->exists();
    }

    public function getPaidAmount(): float
    {
        return (float) $this->payments()
                             ->where('status', 'completed')
                             ->sum('amount');
    }

    public function hasPendingPayment(): bool
    {
        return $this->payments()
                     ->whereIn('status', ['pending', 'challenge'])
                     ->exists();
    }

    public function isPending(): bool
    {
        return in_array($this->status, ['pending', 'challenge']);
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return in_array($this->status, ['failed', 'rejected', 'cancelled', 'expired']);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'challenge']);
    }

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

    public function cancel($reason = null): bool
    {
        // Admin cancel method in AdminBookingController might directly update status.
        // This method is primarily for user-initiated cancel.
        if (!$this->canBeCancelled()) {
            return false;
        }
        
        return $this->update([
            'status'            => 'cancelled',
            'cancelled_at'      => now(),
            'cancellation_reason' => $reason ?? 'Dibatalkan oleh user',
        ]);
    }

    public function markAsExpired(): bool
    {
        return $this->update([
            'status' => 'expired'
        ]);
    }

    public function markAsFailed(): bool
    {
        return $this->update([
            'status' => 'failed'
        ]);
    }

    public function markAsCompleted(): bool
    {
        return $this->update([
            'status' => 'completed'
        ]);
    }
}