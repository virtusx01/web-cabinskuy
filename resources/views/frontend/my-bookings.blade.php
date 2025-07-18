@extends('backend.user_layout')

@section('title', $title)

@push('styles')
<style>
    .my-bookings-page-bg {
        background-color: #f4f7f6;
        padding: 20px 0 40px 0;
        min-height: 100vh;
    }
    .breadcrumb {
        padding: 10px 0;
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

    .booking-list-container {
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    .booking-card {
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        margin-bottom: 20px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease-in-out;
    }

    .booking-card:hover {
        transform: translateY(-5px);
    }

    .booking-card-header {
        background-color: #f8f9fa;
        padding: 15px 20px;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .booking-card-header h5 {
        margin: 0;
        font-size: 1.1em;
        color: #223324;
    }

    .booking-card-header .booking-id {
        font-weight: bold;
        color: #229954;
    }

    .booking-status {
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 0.85em;
        font-weight: bold;
        text-transform: capitalize;
    }

    .status-pending { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
    .status-confirmed { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .status-rejected { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .status-cancelled { background-color: #e2e3e5; color: #495057; border: 1px solid #d6d8db; }
    .status-completed { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }


    .booking-card-body {
        padding: 20px;
        display: flex;
        gap: 20px;
        flex-wrap: wrap; /* Allow wrapping on smaller screens */
        /* ADD THIS LINE TO VERTICALLY CENTER ITEMS */
        align-items: center;
    }

    .booking-card-image {
        flex-shrink: 0;
        width: 150px;
        height: 150px;
        border-radius: 8px;
        object-fit: cover;
    }

    .booking-card-details {
        flex-grow: 1;
    }

    .booking-card-details h4 {
        margin-top: 0;
        margin-bottom: 5px;
        font-size: 1.2em;
        color: #223324;
    }

    .booking-card-details p {
        margin-bottom: 5px;
        font-size: 0.9em;
        color: #666;
    }

    .booking-card-summary {
        flex-basis: 100%; /* Take full width on smaller screens, then adjust */
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 10px;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px dashed #e0e0e0;
    }

    .summary-item strong {
        display: block;
        color: #333;
        font-size: 0.9em;
        margin-bottom: 3px;
    }

    .summary-item span {
        font-size: 0.85em;
        color: #555;
    }

    .booking-card-footer {
        padding: 15px 20px;
        background-color: #f8f9fa;
        border-top: 1px solid #e0e0e0;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .booking-card-footer .btn {
        padding: 8px 15px;
        border-radius: 8px;
        font-weight: 500;
        text-decoration: none;
        display: inline-block;
        transition: background-color 0.2s;
    }

    .btn-detail {
        background-color: #007bff;
        color: white;
    }
    .btn-detail:hover {
        background-color: #0056b3;
    }

    .btn-cancel {
        background-color: #dc3545;
        color: white;
    }
    .btn-cancel:hover {
        background-color: #c82333;
    }
    .btn-disabled {
        background-color: #6c757d;
        color: white;
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

    .pagination {
        margin-top: 30px;
        display: flex;
        justify-content: center;
        list-style: none;
        padding: 0;
    }
    .pagination li {
        margin: 0 5px;
    }
    .pagination li a, .pagination li span {
        display: block;
        padding: 8px 12px;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        text-decoration: none;
        color: #007bff;
        transition: all 0.3s;
    }
    .pagination li a:hover {
        background-color: #e9ecef;
    }
    .pagination li.active span {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }
    .pagination li.disabled span {
        color: #6c757d;
        cursor: not-allowed;
    }

    @media (max-width: 768px) {

        .page-title{
            text-align: center;
        }
        .booking-card-body {
            flex-direction: column;
            align-items: center; /* Keep this for centering on small screens too */
        }
        .booking-card-image {
            width: 100%;
            max-width: 250px; /* Limit image size on small screens */
            height: 150px;
            margin-bottom: 15px;
        }
        .booking-card-summary {
            grid-template-columns: 1fr; /* Stack summary items */
        }
        .booking-card-footer {
            flex-direction: column;
            align-items: stretch;
        }
        .booking-card-footer .btn {
            width: 100%;
            margin-bottom: 10px;
        }

        .btn-detail{
            flex-direction: column;
            align-items: stretch;
            text-align: center;
        }
    }
</style>
@endpush

@section('content')
<div class="my-bookings-page-bg">
    <div class="container">
        <nav class="breadcrumb">
            <a href="{{ route('frontend.beranda') }}">Home</a> >
            <span>My Bookings</span>
        </nav>

        <h1 class="page-title" style="text-align: center; padding-block: 20px;">Booking Saya</h1>

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

        <div class="booking-list-container">
            @forelse ($bookings as $booking)
                <div class="booking-card">
                    <div class="booking-card-header">
                        <h5>Booking ID: <span class="booking-id">#{{ $booking->id_booking }}</span></h5>
                        <span class="booking-status status-{{ $booking->status }}">
                            {{ $booking->status_label }}
                        </span>
                    </div>
                    <div class="booking-card-body">
                        <img src="{{ !empty($booking->room->room_photos) && is_array($booking->room->room_photos) && count($booking->room->room_photos) > 0 ? Storage::disk('s3')->url($booking->room->room_photos[0]) : 'https://via.placeholder.com/150x100/e9f5e9/333333?text=Room' }}"
                             alt="{{ $booking->room->typeroom }}" class="booking-card-image">
                        <div class="booking-card-details">
                            <h4>{{ $booking->room->typeroom }} Cabin at {{ $booking->cabin->name }}</h4>
                            <p>Lokasi: {{ $booking->cabin->location_address }}</p>
                            <div class="booking-card-summary">
                                <div class="summary-item">
                                    <strong>Check-in:</strong>
                                    <span>{{ \Carbon\Carbon::parse($booking->check_in_date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
                                </div>
                                <div class="summary-item">
                                    <strong>Check-out:</strong>
                                    <span>{{ \Carbon\Carbon::parse($booking->check_out_date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
                                </div>
                                <div class="summary-item">
                                    <strong>Jumlah Malam:</strong>
                                    <span>{{ $booking->total_nights }} malam</span>
                                </div>
                                <div class="summary-item">
                                    <strong>Jumlah Tamu:</strong>
                                    <span>{{ $booking->total_guests }} orang</span>
                                </div>
                                <div class="summary-item">
                                    <strong>Total Biaya:</strong>
                                    <span>{{ $booking->formatted_total_price }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="booking-card-footer">
                        <a href="{{ route('frontend.booking.show', $booking->id_booking) }}" class="btn btn-detail">
                            Lihat Detail
                        </a>
                        @if ($booking->canBeCancelled())
                            <form action="{{ route('frontend.booking.cancel', $booking->id_booking) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan booking ini?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-cancel">Batalkan Booking</button>
                            </form>
                        @else
                            <button type="button" class="btn btn-cancel btn-disabled" disabled>Tidak Dapat Dibatalkan</button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="alert alert-info text-center" role="alert">
                    Anda belum memiliki booking apa pun saat ini.
                    <a href="{{ route('frontend.kabin.index') }}" class="alert-link">Mulai jelajahi kabin sekarang!</a>
                </div>
            @endforelse

            {{-- Pagination Links --}}
            {{ $bookings->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection