    @extends('backend.user_layout') {{-- Pastikan path layout Anda benar --}}

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

        /* Slider specific styles for detail page */
        .detail-image-slider-container {
            width: 180px; /* Match the original image width */
            height: 120px; /* Match the original image height */
            flex-shrink: 0;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
        }
        .detail-image-slide {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }
        .detail-image-slide.active {
            opacity: 1;
        }
        .detail-slider-control {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            z-index: 10;
            opacity: 0; /* Hide by default, show on hover */
            transition: opacity 0.3s ease-in-out;
        }
        .detail-image-slider-container:hover .detail-slider-control {
            opacity: 1;
        }
        .detail-slider-control.prev {
            left: 5px;
            border-radius: 5px 0 0 5px;
        }
        .detail-slider-control.next {
            right: 5px;
            border-radius: 0 5px 5px 0;
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
.qr-code-section{
    align-items: center; /* Vertical centering */
    justify-content: center; /* Horizontal centering */
    text-align: center;
}

.btn btn-primary btn-disabled{
    background: transparent !important;
}.btn btn-primary btn-disabled ::before{
    background: transparent !important;
}
        /* === MODIFIED STYLES FOR UNIFORM BUTTONS === */
        /* === MODIFIED STYLES FOR UNIFORM BUTTONS === */
.action-buttons {
    margin-top: 25px;
    display: flex;
    gap: 15px;
    justify-content: center; /* Center the group of buttons */
    align-items: stretch; /* Make all items (a or form) the same height */
    flex-wrap: wrap; /* Allow buttons to wrap to next line if space is limited */
}

.action-buttons > a,
.action-buttons > form {
    /* These are the direct flex items, make them grow equally */
    flex-grow: 1;
    flex-basis: 0; /* Important for equal distribution when flex-grow is used */
    min-width: 180px; /* Minimum width for each button to ensure readability */
    max-width: 300px; /* Optional: Max width to prevent buttons from becoming too wide */

    /* Apply button styling to the parent <a> or <form> */
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: bold;
    text-decoration: none;
    border: none;
    cursor: pointer;
    position: relative;
    overflow: hidden;

    /* Use flexbox for internal centering of text/button within the <a>/form */
    display: flex;
    align-items: center; /* Vertical centering */
    justify-content: center; /* Horizontal centering */
    text-align: center; /* Fallback for older browsers or non-flex items */

    /* Enhanced transition for smooth animations */
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    transform: translateY(0);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

/* Hover effects for all buttons */
.action-buttons > a:hover,
.action-buttons > form:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* Style the actual <button> inside a <form> */
.action-buttons > form > .btn {
    width: 100%; /* Make the inner <button> fill its <form> parent */
    height: 100%; /* Make the inner <button> fill its <form> parent */
    margin: 0; /* Remove default button margins */
    padding: 0; /* Remove default button padding, parent <form> handles it */
    background: transparent; /* Make inner button background transparent */
    color: inherit; /* Inherit text color from parent <form> */
    border: none; /* Remove inner button border */
    border-radius: inherit; /* Inherit border-radius from parent <form> */
    font-size: inherit; /* Inherit font size */
    font-weight: inherit; /* Inherit font weight */
    cursor: pointer; /* Ensure cursor is pointer */
    transition: inherit; /* Inherit transition from parent */
    
    /* CRUCIAL: Ensure inner button also uses flexbox for perfect centering */
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}

/* === PRIMARY BUTTON STYLES === */
.btn-primary {
    background: linear-gradient(135deg, #229954 0%, #27ae60 50%, #2ecc71 100%);
    color: white;
    border: 2px solid transparent;
    background-size: 200% 200%;
    animation: gradientShift 3s ease infinite;
}

.btn-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #1e8449 0%, #229954 50%, #27ae60 100%);
    border-color: rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 25px rgba(34, 153, 84, 0.4);
}

.btn-primary:hover::before {
    left: 100%;
}

/* === SECONDARY BUTTON STYLES === */
.btn-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #868e96 50%, #adb5bd 100%);
    color: white;
    border: 2px solid transparent;
    background-size: 200% 200%;
    animation: gradientShift 3s ease infinite reverse;
}

.btn-secondary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(124, 122, 108, 0.2), transparent);
    transition: left 0.5s;
}

.btn-secondary:hover {
    border-color: rgba(255, 255, 255, 0.3);
    color: grey !important;
}

.btn-secondary:hover::before {
    left: 100%;
}

/* === DANGER BUTTON STYLES === */
.btn-danger {
    background: linear-gradient(135deg, #dc3545 0%, #e74c3c 50%, #f39c12 100%);
    color: white;
    border: 2px solid transparent;
    background-size: 200% 200%;
    animation: gradientShift 3s ease infinite;
}

.btn-danger::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 38, 38, 0.2), transparent);
    transition: left 0.5s;
}

.btn-danger:hover {
    border-color: rgba(255, 255, 255, 0.3);
    color: red !important;
}

.btn-danger:hover::before {
    left: 100%;
}

/* === DISABLED BUTTON STYLES === */
.btn-disabled {
    background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 50%, #e9ecef 100%);
    color: #6c757d;
    cursor: not-allowed;
    opacity: 0.7;
    border: 2px solid #dee2e6;
}

.btn-disabled:hover {
    transform: none;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

/* === GRADIENT ANIMATION === */
@keyframes gradientShift {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

/* === MOBILE RESPONSIVE === */
@media (max-width: 768px) {
    .action-buttons {
        flex-direction: column; /* Stack vertically */
        align-items: stretch; /* Make them fill the width */
        gap: 12px; /* Adjust gap for stacked buttons */
        /* Pastikan container tetap center */
        justify-content: center;
    }
    
    .action-buttons > a,
    .action-buttons > form {
        /* Reset flex properties untuk mobile stack */
        flex-grow: 0;
        flex-basis: auto;
        min-width: unset; /* Remove min-width restriction */
        max-width: unset; /* Remove max-width restriction */
        width: 100%; /* Full width on mobile */
        
        /* Maintain centering */
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        
        /* Consistent padding for mobile */
        padding: 14px 20px;
    }

    /* Ensure inner buttons maintain perfect centering on mobile */
    .action-buttons > form > .btn {
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        width: 100%;
        height: 100%;
    }

    /* Reduce animation intensity on mobile for better performance */
    .action-buttons > a:hover,
    .action-buttons > form:hover {
        transform: translateY(-1px);
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

                    <div class="detail-item">
                        <strong>Invoice:</strong>
                        <span>
                            {{ $booking->latestPayment->transaction_id ?? 'Belum Ada Invoice' }}
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
                        {{-- START: Image Slider for Room Photos --}}
                        <div class="detail-image-slider-container">
                            @if (!empty($booking->room->room_photos) && is_array($booking->room->room_photos) && count($booking->room->room_photos) > 0)
                                @foreach ($booking->room->room_photos as $index => $photo)
                                    <img src="{{ Storage::url($photo) }}"
                                            alt="{{ $booking->room->typeroom }} - Foto {{ $index + 1 }}"
                                            class="detail-image-slide {{ $index === 0 ? 'active' : '' }}"
                                            data-slide="{{ $index }}">
                                @endforeach
                                @if (count($booking->room->room_photos) > 1)
                                    <button class="detail-slider-control prev" onclick="changeDetailSlide(this, -1)">&#10094;</button>
                                    <button class="detail-slider-control next" onclick="changeDetailSlide(this, 1)">&#10095;</button>
                                @endif
                            @else
                                {{-- Placeholder image if no photos exist --}}
                                <img src="https://via.placeholder.com/180x120/e9f5e9/333333?text=Room"
                                        alt="Room Placeholder" class="detail-image-slide active">
                            @endif
                        </div>
                        {{-- END: Image Slider for Room Photos --}}

                        <div class="cabin-room-details">
                            <h4>{{ $booking->cabin->name }}</h4>
                            <p>Tipe: {{ $booking->room->typeroom }}</p>
                            <p>Lokasi: {{ $booking->cabin->location_address }}, {{ $booking->cabin->regency }}, {{ $booking->cabin->province }}</p>
                            <ul>
                                {{-- PHP calculations for tax --}}
                                @php
                                    $roomPrice = $booking->room->price ?? 0;
                                    $totalNights = $booking->total_nights;
                                    $subtotal = $roomPrice * $totalNights;
                                    $taxRate = 0.05; // 5% tax
                                    $taxAmount = round($subtotal * $taxRate); // Round tax amount for display
                                @endphp
                                <li>Kapasitas: {{ $booking->room->slot_room }} tamu</li>
                                <li>Biaya per malam: Rp {{ number_format($roomPrice, 0, ',', '.') }}</li>
                                <li>Biaya pajak: Rp {{ number_format($taxAmount, 0, ',', '.') }}</li>
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
                @if ($qrCodeImage)
                    <div class="qr-code-section">
                        <h3>QR Code Booking</h3>
                        {{-- The src attribute directly uses the base64 string --}}
                        <img src="{{ $qrCodeImage }}" alt="QR Code for Booking #{{ $booking->id_booking }}">
                        <p>Tunjukkan QR Code Booking ini ke petugas.</p>
                    </div>
                @else
                    {{-- Optional: Display a message if no QR code is available --}}
                    <p>QR Code akan tersedia setelah booking dikonfirmasi</p>
                @endif

                <div class="action-buttons">
                    @if(in_array($booking->status, ['pending', 'challenge']))
                        {{-- Tombol utama untuk melanjutkan pembayaran --}}
                        <a href="{{ route('frontend.payment.show', $booking->id_booking) }}" class="btn btn-primary">Lanjutkan Pembayaran</a>

                        {{-- Tombol sekunder untuk ganti metode pembayaran --}}
                        <form action="{{ route('frontend.payment.change', $booking->id_booking) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengganti metode pembayaran? Transaksi yang sedang berjalan akan dibatalkan.');">
                            @csrf
                            @method('PATCH')
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
                    @else
                        <a href="{{ route('frontend.booking.index') }}" class="btn btn-secondary">Kembali ke Daftar Booking</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endsection


    @push('scripts')
    <script>
        // Function for the image slider on the detail page
        function changeDetailSlide(control, direction) {
            const container = control.closest('.detail-image-slider-container');
            const slides = container.querySelectorAll('.detail-image-slide');
            let activeSlide = container.querySelector('.detail-image-slide.active');
            let currentIndex = parseInt(activeSlide.dataset.slide);
            let newIndex = currentIndex + direction;

            if (newIndex < 0) {
                newIndex = slides.length - 1;
            } else if (newIndex >= slides.length) {
                newIndex = 0;
            }

            activeSlide.classList.remove('active');
            slides.forEach(slide => {
                if (parseInt(slide.dataset.slide) === newIndex) {
                    slide.classList.add('active');
                }
            });
        }

        // Polling script (existing, just ensuring it's here)
        @if ($booking->status === 'pending' || $booking->status === 'challenge')
        document.addEventListener('DOMContentLoaded', function () {
            const bookingId = '{{ $booking->id_booking }}';
            const pollingUrl = '{{ route("frontend.booking.status", $booking->id_booking) }}';
            let pollingInterval; // Variabel untuk menyimpan interval

            function pollStatus() {
                console.log('Polling for status...');
                fetch(pollingUrl)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
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
                        // Anda bisa memutuskan untuk menghentikan polling jika terjadi error
                        // clearInterval(pollingInterval);
                    });
            }

            // Mulai polling setiap 5 detik (5000 milidetik)
            pollingInterval = setInterval(pollStatus, 5000);

            // Jalankan polling pertama kali tanpa menunggu 5 detik
            pollStatus();
        });
        @endif
    </script>
    @endpush