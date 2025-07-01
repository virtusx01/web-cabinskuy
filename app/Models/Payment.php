<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';
    protected $primaryKey = 'id_payment';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'id_booking',
        'amount',
        'payment_method',
        'transaction_id',
        'payment_details',
        'status',
        'id_user',
    ];

    protected $casts = [
        'amount'          => 'decimal:2',
        'payment_details' => 'array',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    /**
     * Relasi ke Booking
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'id_booking', 'id_booking');
    }

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Status accessor for human readable format.
     */
    public function getStatusLabelAttribute()
    {
        $statusLabels = [
            'pending'   => 'Menunggu Pembayaran',
            'paid' => 'Pembayaran Berhasil',
            'failed'    => 'Pembayaran Gagal',
            'cancelled' => 'Pembayaran Dibatalkan',
            'expired'   => 'Pembayaran Kadaluarsa',
            'challenge' => 'Perlu Verifikasi Fraud',
        ];

        return $statusLabels[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Badge class accessor for UI styling.
     */
    public function getStatusBadgeClassAttribute()
    {
        $statusClasses = [
            'pending'   => 'badge-warning',
            'paid' => 'badge-success',
            'failed'    => 'badge-danger',
            'cancelled' => 'badge-secondary',
            'expired'   => 'badge-danger',
            'challenge' => 'badge-info',
        ];

        return $statusClasses[$this->status] ?? 'badge-secondary';
    }

    /**
     * Scope untuk filter berdasarkan status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk payment yang berhasil.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope untuk payment yang pending (termasuk challenge).
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'challenge']);
    }

    /**
     * Scope untuk payment yang gagal.
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['failed', 'cancelled', 'expired']);
    }

    /**
     * Method untuk cek apakah payment berhasil.
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Method untuk cek apakah payment sedang pending.
     */
    public function isPending(): bool
    {
        return in_array($this->status, ['pending', 'challenge']);
    }

    /**
     * Method untuk cek apakah payment gagal.
     */
    public function isFailed(): bool
    {
        return in_array($this->status, ['failed', 'cancelled', 'expired']);
    }

    /**
     * Method untuk cek apakah payment bisa dibatalkan.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'challenge']);
    }

    /**
     * Formatted amount accessor.
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
}