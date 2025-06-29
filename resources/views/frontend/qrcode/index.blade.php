@extends('backend.user_layout') {{-- Anda mungkin ingin menggunakan layout yang berbeda untuk halaman publik --}}

@section('title', $title)

@push('styles')
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f0f2f5;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
    }
    .qr-access-container {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        padding: 40px;
        text-align: center;
        max-width: 600px;
        width: 90%;
    }
    .qr-access-container h1 {
        color: #223324;
        font-size: 2em;
        margin-bottom: 20px;
    }
    .qr-access-container p {
        color: #555;
        font-size: 1.1em;
        line-height: 1.6;
        margin-bottom: 25px;
    }
    .booking-summary {
        background-color: #f9f9f9;
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 30px;
        text-align: left;
    }
    .booking-summary h2 {
        font-size: 1.5em;
        color: #223324;
        margin-top: 0;
        margin-bottom: 15px;
        border-bottom: 1px solid #ddd;
        padding-bottom: 10px;
    }
    .booking-summary ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .booking-summary ul li {
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 1em;
    }
    .booking-summary ul li strong {
        color: #333;
        flex-basis: 40%;
    }
    .booking-summary ul li span {
        color: #666;
        flex-basis: 60%;
        text-align: right;
    }
    .qr-image-container {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px dashed #e0e0e0;
    }
    .qr-image-container img {
        max-width: 150px; /* Make QR smaller on this page, as it's a link to PDF */
        height: auto;
        border: 3px solid #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-radius: 4px;
        margin-bottom: 20px;
    }
    .btn-download-pdf {
        background-color: #229954;
        color: white;
        padding: 12px 25px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: bold;
        transition: background-color 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    .btn-download-pdf:hover {
        background-color: #1c7d43;
    }
    .status-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: bold;
        font-size: 0.9em;
    }
    .status-confirmed { background-color: #d4edda; color: #155724; }
    .status-completed { background-color: #d1ecf1; color: #0c5460; }

    @media (max-width: 768px) {
        .qr-access-container {
            padding: 20px;
        }
        .qr-access-container h1 {
            font-size: 1.8em;
        }
    }
</style>
@endpush

@section('content')
<div class="qr-access-container">
    <h1>Konfirmasi Booking Anda</h1>
    <p>Berikut adalah detail booking Anda. Mohon tunjukkan halaman ini saat check-in.</p>

    <div class="booking-summary">
        <h2>Detail Booking #{{ $booking->id_booking }}</h2>
        <ul>
            <li><strong>Status:</strong> <span class="status-badge status-{{ $booking->status }}">{{ $booking->status_label }}</span></li>
            <li><strong>Nama Pemesan:</strong> <span>{{ $booking->contact_name }}</span></li>
            <li><strong>Email:</strong> <span>{{ $booking->contact_email }}</span></li>
            <li><strong>Kabin:</strong> <span>{{ $booking->cabin->name ?? 'N/A' }}</span></li>
            <li><strong>Tipe Kamar:</strong> <span>{{ $booking->room->typeroom ?? 'N/A' }}</span></li>
            <li><strong>Tanggal Check-in:</strong> <span>{{ \Carbon\Carbon::parse($booking->check_in_date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span></li>
            <li><strong>Tanggal Check-out:</strong> <span>{{ \Carbon\Carbon::parse($booking->check_out_date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span></li>
            <li><strong>Jumlah Tamu:</strong> <span>{{ $booking->total_guests }} orang</span></li>
            <li><strong>Total Harga:</strong> <span>{{ $booking->formatted_total_price }}</span></li>
            @if ($booking->successfulPayment)
                <li><strong>Metode Pembayaran:</strong> <span>{{ $booking->successfulPayment->payment_method ?? 'N/A' }}</span></li>
                <li><strong>ID Transaksi Pembayaran:</strong> <span>{{ $booking->successfulPayment->transaction_id ?? 'N/A' }}</span></li>
            @endif
        </ul>
    </div>

    @if ($qrCodeForPdf)
    <div class="qr-image-container">
        <p>Anda juga dapat mengunduh konfirmasi booking ini dalam bentuk PDF:</p>
        <img src="{{ $qrCodeForPdf }}" alt="QR Code to PDF" style="max-width: 150px;">
        <a href="{{ route('frontend.booking.pdf', $booking->qr_access_token) }}" class="btn-download-pdf" target="_blank">
            <i class="fas fa-file-pdf"></i> Unduh PDF Konfirmasi
        </a>
    </div>
    @endif
</div>
@endsection

@push('scripts')
{{-- Optional: Add any specific scripts for this public QR page here if needed --}}
@endpush