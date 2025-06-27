@extends('backend.user_layout')

@section('title', $title)

@push('styles')
<style>
    .booking-detail-page-bg {
        background-color: #f4f7f6;
        padding: 20px 0 40px 0;
        min-height: 100vh;
    }
    .breadcrumb {
        padding: 15px 0;
        font-size: 0.9em;
        color: #777;
    }
    .breadcrumb a {
        color: #229954;
        text-decoration: none;
    }
    .breadcrumb a:hover {
        text-decoration: underline;
    }
    .page-title {
        font-size: 2.2em;
        color: #223324;
        margin-bottom: 25px;
    }

    .booking-detail-card {
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }

    .detail-section {
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }

    .detail-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .detail-section h3 {
        font-size: 1.5em;
        color: #223324;
        margin-top: 0;
        margin-bottom: 15px;
    }

    .detail-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 1em;
    }

    .detail-item strong {
        color: #333;
        flex-basis: 40%;
        min-width: 150px;
    }

    .detail-item span {
        color: #555;
        flex-basis: 60%;
        text-align: right;
    }

    .detail-item.total-price {
        font-size: 1.4em;
        font-weight: bold;
        color: #229954;
        border-top: 2px solid #e9f5e9;
        padding-top: 15px;
        margin-top: 20px;
    }
    .detail-item.total-price span {
        color: #229954;
    }


    .booking-status {
        display: inline-block;
        padding: 8px 15px;
        border-radius: 8px;
        font-size: 1em;
        font-weight: bold;
        text-transform: capitalize;
        margin-top: 10px;
    }

    .status-pending { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
    .status-confirmed { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .status-rejected { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .status-cancelled { background-color: #e2e3e5; color: #495057; border: 1px solid #d6d8db; }
    .status-completed { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }

    .cabin-room-info {
        display: flex;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px dashed #e0e0e0;
    }
    .cabin-room-info img {
        width: 180px;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
    }
    .cabin-room-details h4 {
        margin: 0 0 5px 0;
        font-size: 1.3em;
        color: #223324;
    }
    .cabin-room-details p {
        margin: 0 0 8px 0;
        color: #666;
        font-size: 0.95em;
    }
    .cabin-room-details ul {
        list-style: none;
        padding: 0;
        margin: 0;
        font-size: 0.9em;
        color: #777;
    }
    .cabin-room-details ul li {
        margin-bottom: 5px;
    }

    .action-buttons {
        margin-top: 25px;
        display: flex;
        gap: 15px;
        justify-content: flex-end;
    }

    .action-buttons .btn {
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: bold;
        text-decoration: none;
        transition: background-color 0.2s;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background-color: #229954;
        color: white;
    }
    .btn-primary:hover {
        background-color: #1c7d43;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }
    .btn-secondary:hover {
        background-color: #5a6268;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }
    .btn-danger:hover {
        background-color: #c82333;
    }
    .btn-disabled {
        background-color: #e0e0e0;
        color: #999;
        cursor: not-allowed;
        opacity: 0.7;
    }

    /* Alert Messages */
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 8px;
    }
    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }
    .alert-success {
        color: #155724;
        background-color: #d4edda;
        border-color: #c3e6cb;
    }

    @media (max-width: 768px) {
        .detail-item {
            flex-direction: column;
            align-items: flex-start;
        }
        .detail-item span {
            text-align: left;
            margin-top: 5px;
        }
        .cabin-room-info {
            flex-direction: column;
            align-items: center;
        }
        .cabin-room-info img {
            width: 100%;
            max-width: 300px;
        }
        .action-buttons {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>
@endpush

@section('content')
<div class="booking-detail-page-bg">
    <div class="container">
        <nav class="breadcrumb">
            <a href="{{ route('frontend.beranda') }}">Home</a> >
            <a href="{{ route('frontend.booking.index') }}">My Bookings</a> >
            <span>Detail Booking #{{ $booking->id_booking }}</span>
        </nav>

        <h1 class="page-title">Detail Booking Anda #{{ $booking->id_booking }}</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="booking-detail-card">
            <div class="detail-section">
                <h3>Informasi Booking</h3>
                <div class="detail-item">
                    <strong>Status:</strong>
                    <span class="booking-status status-{{ $booking->status }}">
                        {{ $booking->status_label }}
                    </span>
                </div>
                {{-- KODE POLLING SEKARANG DIHAPUS DARI SINI --}}
                <div class="detail-item">
                    <strong>Tanggal Booking:</strong>
                    <span>{{ \Carbon\Carbon::parse($booking->booking_date)->locale('id')->isoFormat('dddd, D MMMM YYYY, HH:mm') }} WIB</span>
                </div>
                <div class="detail-item">
                    <strong>Check-in:</strong>
                    <span>{{ \Carbon\Carbon::parse($booking->check_in_date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
                </div>
                <div class="detail-item">
                    <strong>Check-out:</strong>
                    <span>{{ \Carbon\Carbon::parse($booking->check_out_date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
                </div>
                <div class="detail-item">
                    <strong>Jumlah Malam:</strong>
                    <span>{{ $booking->total_nights }} malam</span>
                </div>
                <div class="detail-item">
                    <strong>Jumlah Tamu:</strong>
                    <span>{{ $booking->total_guests }} orang</span>
                </div>
            </div>

            <div class="detail-section">
                <h3>Informasi Kabin & Kamar</h3>
                <div class="cabin-room-info">
                    @php
                        // Function to safely decode JSON strings for photos
                        function safeJsonDecodeBookingDetail($data) {
                            if (is_string($data)) {
                                $decoded = json_decode($data, true);
                                // Check for double encoding if the first decode result is still a string array
                                if (is_array($decoded) && !empty($decoded) && is_string($decoded[0] ?? null)) {
                                    $decoded = json_decode($decoded[0], true);
                                }
                                return is_array($decoded) ? $decoded : [];
                            }
                            return is_array($data) ? $data : [];
                        }

                        $roomPhotoUrl = 'https://placehold.co/180x120/e9f5e9/333333?text=Room'; // Default placeholder
                        if ($booking->room && !empty($booking->room->room_photos)) {
                            $photos = safeJsonDecodeBookingDetail($booking->room->room_photos);
                            if (!empty($photos) && is_string($photos[0])) {
                                // Asumsi path foto disimpan relatif dari storage/app/public
                                $roomPhotoUrl = asset('storage/' . str_replace('\\', '/', $photos[0]));
                            }
                        }
                        
                        $cabinLocation = ($booking->cabin->regency ?? 'N/A') . ', ' . ($booking->cabin->province ?? 'N/A');
                        if ($cabinLocation === 'N/A, N/A') $cabinLocation = 'Lokasi Tidak Diketahui';
                    @endphp
                    <img src="{{ $roomPhotoUrl }}"
                            alt="{{ $booking->room->typeroom ?? 'N/A' }}">
                    <div class="cabin-room-details">
                        <h4>{{ $booking->room->typeroom ?? 'N/A' }} Kabin</h4>
                        <p>Di: {{ $booking->cabin->name ?? 'N/A' }}</p>
                        <p>Lokasi: {{ $cabinLocation }}</p>
                        <ul>
                            <li>Kapasitas: {{ $booking->room->max_guests ?? 'N/A' }} tamu</li> {{-- Menggunakan max_guests dari room --}}
                            <li>Biaya per malam: Rp {{ number_format($booking->room->price ?? 0, 0, ',', '.') }}</li>
                        </ul>
                    </div>
                </div>
                <p style="font-size:0.9em; color:#777;">{{ $booking->room->description ?? 'Tidak ada deskripsi kamar.' }}</p>
            </div>

            <div class="detail-section">
                <h3>Detail Kontak</h3>
                <div class="detail-item">
                    <strong>Nama Lengkap:</strong>
                    <span>{{ $booking->contact_name }}</span>
                </div>
                <div class="detail-item">
                    <strong>Email:</strong>
                    <span>{{ $booking->contact_email }}</span>
                </div>
                <div class="detail-item">
                    <strong>Telepon:</strong>
                    <span>{{ $booking->contact_phone ?: '-' }}</span>
                </div>
                <div class="detail-item">
                    <strong>User ID:</strong> <span>{{ $booking->user->id_user ?? 'Guest' }} ({{ $booking->user->name ?? 'N/A' }})</span> {{-- Pastikan menggunakan id_user --}}
                </div>
                <div class="detail-item">
                    <strong>Permintaan Khusus:</strong>
                    <span>{{ $booking->special_requests ?: '-' }}</span>
                </div>
            </div>

            @if ($booking->confirmed_at || $booking->rejected_at || $booking->cancelled_at || $booking->admin_notes)
            <div class="detail-section">
                <h3>Log Admin</h3>
                <div class="detail-item">
                    <strong>Dikonfirmasi Oleh:</strong> <span>{{ $booking->confirmedBy->name ?? '-' }} pada {{ $booking->confirmed_at ? \Carbon\Carbon::parse($booking->confirmed_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : '-' }}</span>
                </div>
                <div class="detail-item">
                    <strong>Ditolak Oleh:</strong> <span>{{ $booking->rejectedBy->name ?? '-' }} pada {{ $booking->rejected_at ? \Carbon\Carbon::parse($booking->rejected_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : '-' }}</span>
                </div>
                @if($booking->rejection_reason)
                <div class="detail-item">
                    <strong>Alasan Penolakan:</strong> <span>{{ $booking->rejection_reason }}</span>
                </div>
                @endif
                <div class="detail-item">
                    <strong>Dibatalkan Oleh (User):</strong> <span>{{ $booking->cancelled_at ? 'Pada ' . \Carbon\Carbon::parse($booking->cancelled_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : '-' }}</span>
                </div>
                @if($booking->cancellation_reason)
                <div class="detail-item">
                    <strong>Alasan Pembatalan:</strong> <span>{{ $booking->cancellation_reason }}</span>
                </div>
                @endif
                @if($booking->admin_notes)
                <div class="detail-item">
                    <strong>Catatan Admin:</strong> <span>{{ $booking->admin_notes }}</span>
                </div>
                @endif
            </div>
            @endif

            <div class="detail-section">
                <h3>Riwayat Pembayaran</h3>
                @if($booking->payments->isNotEmpty())
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID Pembayaran</th>
                                <th>Jumlah</th>
                                <th>Metode</th>
                                <th>ID Transaksi</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($booking->payments as $payment)
                            <tr>
                                <td>{{ $payment->id_payment }}</td>
                                <td>{{ $payment->formatted_amount }}</td>
                                <td>{{ $payment->payment_method ?? '-' }}</td>
                                <td>{{ $payment->transaction_id ?? '-' }}</td>
                                <td><span class="badge {{ $payment->getStatusBadgeClassAttribute() }}">{{ $payment->status_label }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($payment->created_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>Belum ada pembayaran untuk booking ini.</p>
                @endif
            </div>

            <div class="detail-item total-price">
                <strong>Total Pembayaran:</strong>
                <span>{{ $booking->formatted_total_price }}</span>
            </div>

            <div class="action-buttons">
                @if(in_array($booking->status, ['pending', 'challenge']))
                    <a href="{{ route('frontend.payment.show', $booking->id_booking) }}" class="btn btn-primary">Lanjutkan Pembayaran</a>
                    
                    <form action="{{ route('frontend.payment.change', $booking->id_booking) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengganti metode pembayaran? Transaksi yang sedang berjalan akan dibatalkan.');">
                        @csrf
                        <button type="submit" class="btn btn-secondary">Ganti Metode Pembayaran</button>
                    </form>

                    <form action="{{ route('frontend.booking.cancel', $booking->id_booking) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan booking ini?');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger">Batalkan Booking</button>
                    </form>

                @elseif($booking->status === 'completed')
                    <button type="button" class="btn btn-primary btn-disabled" disabled>Telah Lunas</button>
                    <a href="{{ route('frontend.booking.index') }}" class="btn btn-secondary">Kembali ke Daftar Booking</a>
                @elseif($booking->status === 'confirmed')
                    <button type="button" class="btn btn-primary btn-disabled" disabled>Telah Dikonfirmasi</button>
                    <a href="{{ route('frontend.booking.index') }}" class="btn btn-secondary">Kembali ke Daftar Booking</a>
                @else {{-- Status rejected, cancelled, expired, failed --}}
                    <a href="{{ route('frontend.booking.index') }}" class="btn btn-secondary">Kembali ke Daftar Booking</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Tidak ada lagi script polling di sini --}}
@endpush