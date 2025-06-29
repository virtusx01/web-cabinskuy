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

    .qr-code-section {
        text-align: center;
        margin-top: 30px;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 8px;
        border: 1px dashed #ccc;
    }
    .qr-code-section img {
        display: block;
        margin: 0 auto 15px auto;
        border: 5px solid #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-radius: 4px;
    }
    .qr-code-section p {
        font-size: 1.1em;
        color: #333;
        font-weight: bold;
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
                @if ($booking->status === 'pending' || $booking->status === 'challenge')
                    <div id="payment-polling-status" class="alert alert-info" style="margin-top: 15px; display: flex; align-items: center; gap: 10px;">
                        <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><style>.spinner_V8m1{transform-origin:center;animation:spinner_zKoa 2s linear infinite}.spinner_V8m1 circle{stroke-linecap:round;animation:spinner_YpZS 1.5s ease-in-out infinite}@keyframes spinner_zKoa{100%{transform:rotate(360deg)}}@keyframes spinner_YpZS{0%{stroke-dasharray:0 150;stroke-dashoffset:0}47.5%{stroke-dasharray:42 150;stroke-dashoffset:-16}95%,100%{stroke-dasharray:42 150;stroke-dashoffset:-59}}</style><g class="spinner_V8m1"><circle cx="12" cy="12" r="9.5" fill="none" stroke="#229954" stroke-width="3"></circle></g></svg>
                        <span>Menunggu konfirmasi pembayaran. Halaman akan diperbarui secara otomatis...</span>
                    </div>
                @endif
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
                    <img src="{{ !empty($booking->room->room_photos) ? asset($booking->room->room_photos[0]) : 'https://via.placeholder.com/180x120/e9f5e9/333333?text=Room' }}"
                                 alt="{{ $booking->room->typeroom }}">
                    <div class="cabin-room-details">
                        <h4>{{ $booking->cabin->name }}</h4>
                        <p>Tipe: {{ $booking->room->typeroom }}</p>
                        <p>Lokasi: {{ $booking->cabin->location }}</p>
                        <ul>
                            <li>Kapasitas: {{ $booking->room->slot_room }} tamu</li>
                            <li>Biaya per malam: Rp {{ number_format($booking->room->price, 0, ',', '.') }}</li>
                        </ul>
                    </div>
                </div>
                <p style="font-size:0.9em; color:#777;">{{ $booking->room->description }}</p>
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
                    <strong>Permintaan Khusus:</strong>
                    <span>{{ $booking->special_requests ?: '-' }}</span>
                </div>
            </div>

            @if ($booking->status === 'rejected' || $booking->status === 'cancelled')
            <div class="detail-section">
                <h3>Informasi Pembatalan/Penolakan</h3>
                @if ($booking->status === 'rejected')
                    <div class="detail-item">
                        <strong>Alasan Penolakan:</strong>
                        <span>{{ $booking->rejection_reason ?: '-' }}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Ditolak Oleh:</strong>
                        <span>{{ $booking->rejectedBy ? $booking->rejectedBy->name : 'Sistem' }} pada {{ \Carbon\Carbon::parse($booking->rejected_at)->locale('id')->isoFormat('dddd, D MMMM YYYY, HH:mm') }} WIB</span>
                    </div>
                @endif
                @if ($booking->status === 'cancelled')
                    <div class="detail-item">
                        <strong>Alasan Pembatalan:</strong>
                        <span>{{ $booking->cancellation_reason ?: '-' }}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Dibatalkan Pada:</strong>
                        <span>{{ \Carbon\Carbon::parse($booking->cancelled_at)->locale('id')->isoFormat('dddd, D MMMM YYYY, HH:mm') }} WIB</span>
                    </div>
                @endif
                @if ($booking->admin_notes)
                    <div class="detail-item">
                        <strong>Catatan Admin:</strong>
                        <span>{{ $booking->admin_notes }}</span>
                    </div>
                @endif
            </div>
            @endif

            <div class="detail-item total-price">
                <strong>Total Pembayaran:</strong>
                <span>{{ $booking->formatted_total_price }}</span>
            </div>

            {{-- New section for QR Code --}}
            {{-- Now qrCodeImageBase64 contains the base64 image of QR code linking to PDF --}}
            @if ($qrCodeImageBase64)
                <div class="qr-code-section">
                    <h3>QR Code Booking</h3>
                    <img src="{{ $qrCodeImageBase64 }}" alt="QR Code for Booking #{{ $booking->id_booking }}">
                    <p>Scan QR code ini untuk melihat detail booking Anda.</p>
                    <p class="mt-2 text-muted" style="font-size: 0.85em;">Atau <a href="{{ route('frontend.booking.pdf', ['identifier' => $booking->qr_access_token]) }}" target="_blank">klik di sini untuk mengunduh PDF</a>.</p>
                </div>
            @endif

            <div class="action-buttons">
                @if(in_array($booking->status, ['pending', 'challenge']))
                    {{-- Tombol utama untuk melanjutkan pembayaran --}}
                    <a href="{{ route('frontend.payment.show', $booking->id_booking) }}" class="btn btn-primary">Lanjutkan Pembayaran</a>

                    {{-- Tombol sekunder untuk ganti metode pembayaran --}}
                    <form action="{{ route('frontend.payment.change', $booking->id_booking) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengganti metode pembayaran? Transaksi yang sedang berjalan akan dibatalkan.');">
                        @csrf
                        <button type="submit" class="btn btn-secondary">Ganti Metode Pembayaran</button>
                    </form>

                    {{-- Tombol untuk membatalkan booking --}}
                    <form action="{{ route('frontend.booking.cancel', $booking->id_booking) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan booking ini?');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger">Batalkan Booking</button>
                    </form>

                @elseif($booking->status === 'confirmed')
                    <button type="button" class="btn btn-primary btn-disabled" disabled>Telah Dikonfirmasi</button>
                    {{-- Optionally, add a direct PDF download button here too for convenience --}}
                    @if ($booking->qr_access_token)
                        <a href="{{ route('frontend.booking.pdf', ['identifier' => $booking->qr_access_token]) }}" target="_blank" class="btn btn-secondary">Unduh PDF Konfirmasi</a>
                    @endif
                @else
                    <a href="{{ route('frontend.booking.index') }}" class="btn btn-secondary">Kembali ke Daftar Booking</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


{{-- di dalam booking_detail.blade.php --}}
@push('scripts')
@if ($booking->status === 'pending' || $booking->status === 'challenge')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bookingId = '{{ $booking->id_booking }}';
        const pollingUrl = '{{ route("frontend.booking.status", $booking->id_booking) }}';
        let pollingInterval; // Variabel untuk menyimpan interval

        function pollStatus() {
            console.log('Polling for status...');
            fetch(pollingUrl)
                .then(response => {
                    if (!response.ok) {
                        // If response is not OK, stop polling and log error
                        clearInterval(pollingInterval);
                        console.error('Network response was not ok during polling:', response.statusText);
                        const pollingDiv = document.getElementById('payment-polling-status');
                        if (pollingDiv) {
                            pollingDiv.classList.remove('alert-info');
                            pollingDiv.classList.add('alert-danger');
                            pollingDiv.innerHTML = '<span>Terjadi kesalahan saat mengecek status. Silakan muat ulang halaman.</span>';
                        }
                        return Promise.reject('Network error'); // Propagate error
                    }
                    return response.json();
                })
                .then(data => {
                    // Jika status BUKAN lagi 'pending' atau 'challenge'
                    if (data.status !== 'pending' && data.status !== 'challenge') {
                        console.log('Status changed to ' + data.status + '. Reloading page.');
                        // Hentikan polling
                        clearInterval(pollingInterval);
                        // Tampilkan pesan sukses dan reload halaman
                        const pollingDiv = document.getElementById('payment-polling-status');
                        if (pollingDiv) {
                            pollingDiv.classList.remove('alert-info');
                            pollingDiv.classList.add('alert-success');
                            pollingDiv.innerHTML = '<span>Pembayaran berhasil! Memuat ulang halaman...</span>';
                        }
                        // Reload halaman setelah 2 detik untuk user melihat pesan
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }
                })
                .catch(error => {
                    console.error('Error during polling:', error);
                    // You might want to stop polling after a few errors, or show a more specific message
                    // For now, it logs and continues if it's a fetch error, but stops for network errors.
                    // clearInterval(pollingInterval); // Uncomment to stop polling on any error
                });
        }

        // Mulai polling setiap 5 detik (5000 milidetik)
        pollingInterval = setInterval(pollStatus, 5000);

        // Jalankan polling pertama kali tanpa menunggu 5 detik
        // Removed initial call, as the spinner already indicates pending.
        // The first update will happen after the first interval (5 seconds).
    });
</script>
@endif
@endpush