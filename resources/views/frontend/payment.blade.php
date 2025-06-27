@extends('backend.user_layout') {{-- Pastikan path layout Anda benar --}}

@section('title', $title)

@push('styles')
<style>
    .payment-page-bg {
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

    .payment-layout {
        display: grid;
        grid-template-columns: 1.8fr 1fr;
        gap: 30px;
        align-items: flex-start;
    }

    .payment-options-section {
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    .payment-options-section h3 {
        font-size: 1.5em;
        margin-top: 0;
        margin-bottom: 20px;
        color: #223324;
        border-bottom: 2px solid #e9f5e9;
        padding-bottom: 10px;
    }

    .btn-process-payment {
        background-color: #229954;
        color: white;
        width: 100%;
        padding: 15px;
        font-size: 1.1em;
        font-weight: bold;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s;
        margin-top: 20px;
    }

    .btn-process-payment:hover {
        background-color: #1c7d43;
    }

    .btn-process-payment:disabled {
        background-color: #ccc;
        cursor: not-allowed;
    }

    /* Order Summary Card (reused from booking page, maybe slightly adjusted) */
    .order-summary-card {
        background: #fff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        position: sticky;
        top: 100px;
        align-self: start; /* Ensures it sticks to the top of its grid cell */
    }

    .order-summary-card h3 {
        font-size: 1.5em;
        margin-top: 0;
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
        color: #223324;
    }

    .summary-room-info {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
    }

    .summary-room-info img {
        width: 100px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
    }

    .summary-room-details h4 {
        margin: 0 0 5px 0;
        font-size: 1.1em;
        color: #223324;
    }

    .summary-room-details p {
        margin: 0;
        color: #777;
        font-size: 0.9em;
    }

    .summary-dates {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .summary-dates h5 {
        margin: 0 0 10px 0;
        color: #223324;
        font-size: 1em;
    }

    .summary-dates div {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
        font-size: 0.9em;
    }

    .price-details {
        margin-top: 20px;
    }

    .price-details .price-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 0.95em;
    }

    .price-details .total {
        font-size: 1.2em;
        font-weight: bold;
        color: #229954;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }

    /* Alert Messages */
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 10px; /* Space between icon and text */
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
    .alert i { /* Style for icons in alerts */
        font-size: 1.1em;
    }

    @media (max-width: 768px) {
        .payment-layout {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .order-summary-card {
            position: static;
            order: -1; /* Show summary first on mobile */
        }
    }

    /* Custom Modal Styles */
    .custom-modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1000; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        display: flex; /* Use flexbox to center content */
        justify-content: center;
        align-items: center;
    }

    .custom-modal-content {
        background-color: #fefefe;
        margin: auto; /* Remove fixed margin, flex handles centering */
        padding: 20px;
        border: 1px solid #888;
        border-radius: 10px;
        width: 80%; /* Could be responsive */
        max-width: 400px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        text-align: center;
    }

    .custom-modal-content h4 {
        margin-top: 0;
        color: #223324;
    }

    .custom-modal-content p {
        margin-bottom: 20px;
        color: #555;
    }

    .custom-modal-button {
        background-color: #229954;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1em;
        transition: background-color 0.3s;
    }

    .custom-modal-button:hover {
        background-color: #1c7d43;
    }
</style>
@endpush

@section('content')
<div class="payment-page-bg">
    <div class="container">
        <nav class="breadcrumb">
            <a href="{{ route('frontend.beranda') }}">Home</a> >
            <a href="{{ route('frontend.booking.index') }}">My Bookings</a> >
            <span>Pembayaran Booking #{{ $booking->id_booking }}</span>
        </nav>

        <h1 class="page-title">Pembayaran Booking Anda</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div><i class="fas fa-exclamation-triangle"></i> {{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-times-circle"></i> {{ session('error') }}
            </div>
        @endif

        <div class="payment-layout">
            <div class="payment-options-section">
                <form id="payment-form" onsubmit="event.preventDefault(); initiatePayment();">
                    @csrf
                    <h3>Lanjutkan Pembayaran Melalui Midtrans</h3>

                    <p>Klik tombol di bawah untuk melanjutkan ke proses pembayaran aman via Midtrans. Anda akan dapat memilih metode pembayaran (transfer bank, kartu kredit, e-wallet, dll.) di pop-up Midtrans.</p>

                    <button type="submit" class="btn-process-payment" id="process-payment-btn">
                        Bayar Sekarang Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                    </button>
                </form>
            </div>

            <div class="order-summary-card">
                <h3>Ringkasan Booking</h3>

                @php
                    // Function to safely decode JSON strings
                    function safeJsonDecode($data) {
                        if (is_string($data)) {
                            $decoded = json_decode($data, true);
                            // Check for double encoding if the first decode result is still a string array
                            if (is_array($decoded) && !empty($decoded) && is_string($decoded[0])) {
                                $decoded = json_decode($decoded[0], true);
                            }
                            return is_array($decoded) ? $decoded : [];
                        }
                        return is_array($data) ? $data : [];
                    }

                    $roomPhotoUrl = 'https://placehold.co/100x80/e9f5e9/333333?text=Room'; // Default placeholder
                    
                    // Check if room and room_photos exist and try to decode
                    if ($booking->room && !empty($booking->room->room_photos)) {
                        $photos = safeJsonDecode($booking->room->room_photos);
                        if (!empty($photos) && is_string($photos[0])) {
                            $roomPhotoUrl = asset('storage/' . str_replace('\\', '/', $photos[0]));
                        }
                    }

                    // Get location details from cabin, ensuring it exists
                    $cabinRegency = $booking->cabin->regency ?? 'N/A';
                    $cabinProvince = $booking->cabin->province ?? 'N/A';
                    $displayLocation = '';
                    if ($cabinRegency !== 'N/A' || $cabinProvince !== 'N/A') {
                        $displayLocation = ($cabinRegency !== 'N/A' ? $cabinRegency : '') .
                                           ($cabinRegency !== 'N/A' && $cabinProvince !== 'N/A' ? ', ' : '') .
                                           ($cabinProvince !== 'N/A' ? $cabinProvince : '');
                    }
                    if (empty($displayLocation)) {
                        $displayLocation = 'Lokasi Tidak Diketahui'; // Fallback if both are N/A
                    }
                @endphp

                <div class="summary-room-info">
                    <img src="{{ $roomPhotoUrl }}" alt="{{ $booking->room->typeroom ?? 'N/A' }}">
                    <div class="summary-room-details">
                        <h4>{{ $booking->room->typeroom ?? 'N/A' }} Kabin</h4>
                        <p>{{ $booking->cabin->name ?? 'N/A' }}</p>
                        <p>📍 {{ $displayLocation }}</p> {{-- Updated location display --}}
                    </div>
                </div>

                <div class="summary-dates">
                    <h5>Detail Tanggal</h5>
                    <div>
                        <span>Check-in:</span>
                        <span>{{ \Carbon\Carbon::parse($booking->check_in_date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
                    </div>
                    <div>
                        <span>Check-out:</span>
                        <span>{{ \Carbon\Carbon::parse($booking->check_out_date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
                    </div>
                    <div>
                        <span>Jumlah Malam:</span>
                        <span>{{ $booking->total_nights }}</span>
                    </div>
                    <div>
                        <span>Jumlah Tamu:</span>
                        <span>{{ $booking->total_guests }}</span>
                    </div>
                </div>

                <div class="price-details">
                    <div class="price-row">
                        <span>Rp {{ number_format($booking->room->price ?? 0, 0,',','.') }} x {{ $booking->total_nights }} malam</span>
                        <span>Rp {{ number_format(($booking->room->price ?? 0) * $booking->total_nights, 0,',','.') }}</span>
                    </div>
                    <div class="price-row">
                        <span>Pajak & Biaya Layanan</span>
                        <span>Rp 0</span> {{-- Assuming 0 for now, adjust if you have actual tax/service fees --}}
                    </div>
                    <div class="price-row total">
                        <span>Total Pembayaran</span>
                        <span>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="customAlertModal" class="custom-modal" style="display: none;"> {{-- Set display: none directly --}}
    <div class="custom-modal-content">
        <h4>Pemberitahuan!</h4>
        <p id="customAlertMessage">Silakan pilih metode pembayaran terlebih dahulu.</p>
        <button class="custom-modal-button" onclick="document.getElementById('customAlertModal').style.display='none'">OK</button>
    </div>
</div>
@endsection

@section('content')
<div class="payment-page-bg">
    <div class="container">
        <nav class="breadcrumb">
            <a href="{{ route('frontend.beranda') }}">Home</a> >
            <a href="{{ route('frontend.booking.index') }}">My Bookings</a> >
            <span>Pembayaran Booking #{{ $booking->id_booking }}</span>
        </nav>

        <h1 class="page-title">Pembayaran Booking Anda</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div><i class="fas fa-exclamation-triangle"></i> {{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-times-circle"></i> {{ session('error') }}
            </div>
        @endif

        <div class="payment-layout">
            <div class="payment-options-section">
                <form id="payment-form" onsubmit="event.preventDefault(); initiatePayment();">
                    @csrf
                    <h3>Lanjutkan Pembayaran Melalui Midtrans</h3>

                    <p>Klik tombol di bawah untuk melanjutkan ke proses pembayaran aman via Midtrans. Anda akan dapat memilih metode pembayaran (transfer bank, kartu kredit, e-wallet, dll.) di pop-up Midtrans.</p>

                    <button type="submit" class="btn-process-payment" id="process-payment-btn">
                        Bayar Sekarang Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                    </button>
                </form>
            </div>

            <div class="order-summary-card">
                <h3>Ringkasan Booking</h3>

                @php
                    function safeJsonDecode($data) {
                        if (is_string($data)) {
                            $decoded = json_decode($data, true);
                            if (is_array($decoded) && !empty($decoded) && is_string($decoded[0] ?? null)) {
                                $decoded = json_decode($decoded[0], true);
                            }
                            return is_array($decoded) ? $decoded : [];
                        }
                        return is_array($data) ? $data : [];
                    }

                    $roomPhotoUrl = 'https://placehold.co/100x80/e9f5e9/333333?text=Room'; // Default placeholder
                    if ($booking->room && !empty($booking->room->room_photos)) {
                        $photos = safeJsonDecode($booking->room->room_photos);
                        if (!empty($photos) && is_string($photos[0])) {
                            // Asumsi path foto disimpan relatif dari storage/app/public
                            $roomPhotoUrl = asset('storage/' . str_replace('\\', '/', $photos[0]));
                        }
                    }

                    $cabinRegency = $booking->cabin->regency ?? 'N/A';
                    $cabinProvince = $booking->cabin->province ?? 'N/A';
                    $displayLocation = '';
                    if ($cabinRegency !== 'N/A' || $cabinProvince !== 'N/A') {
                        $displayLocation = ($cabinRegency !== 'N/A' ? $cabinRegency : '') .
                                            ($cabinRegency !== 'N/A' && $cabinProvince !== 'N/A' ? ', ' : '') .
                                            ($cabinProvince !== 'N/A' ? $cabinProvince : '');
                    }
                    if (empty($displayLocation)) {
                        $displayLocation = 'Lokasi Tidak Diketahui'; // Fallback if both are N/A
                    }
                @endphp

                <div class="summary-room-info">
                    <img src="{{ $roomPhotoUrl }}" alt="{{ $booking->room->typeroom ?? 'N/A' }}">
                    <div class="summary-room-details">
                        <h4>{{ $booking->room->typeroom ?? 'N/A' }} Kabin</h4>
                        <p>{{ $booking->cabin->name ?? 'N/A' }}</p>
                        <p>📍 {{ $displayLocation }}</p>
                    </div>
                </div>

                <div class="summary-dates">
                    <h5>Detail Tanggal</h5>
                    <div>
                        <span>Check-in:</span>
                        <span>{{ \Carbon\Carbon::parse($booking->check_in_date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
                    </div>
                    <div>
                        <span>Check-out:</span>
                        <span>{{ \Carbon\Carbon::parse($booking->check_out_date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
                    </div>
                    <div>
                        <span>Jumlah Malam:</span>
                        <span>{{ $booking->total_nights }}</span>
                    </div>
                    <div>
                        <span>Jumlah Tamu:</span>
                        <span>{{ $booking->total_guests }}</span>
                    </div>
                </div>

                <div class="price-details">
                    <div class="price-row">
                        <span>Rp {{ number_format($booking->room->price ?? 0, 0,',','.') }} x {{ $booking->total_nights }} malam</span>
                        <span>Rp {{ number_format(($booking->room->price ?? 0) * $booking->total_nights, 0,',','.') }}</span>
                    </div>
                    <div class="price-row">
                        <span>Pajak & Biaya Layanan</span>
                        <span>Rp 0</span> {{-- Assuming 0 for now, adjust if you have actual tax/service fees --}}
                    </div>
                    <div class="price-row total">
                        <span>Total Pembayaran</span>
                        <span>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="customAlertModal" class="custom-modal"> {{-- Set display: none directly --}}
    <div class="custom-modal-content">
        <h4>Pemberitahuan!</h4>
        <p id="customAlertMessage">Silakan pilih metode pembayaran terlebih dahulu.</p>
        <button class="custom-modal-button" onclick="document.getElementById('customAlertModal').style.display='none'">OK</button>
    </div>
</div>
@endsection

@push('scripts')
{{-- Midtrans Snap.js script --}}
<script type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
    // Fungsi untuk menampilkan modal kustom
    function showCustomModal(message) {
        document.getElementById('customAlertMessage').textContent = message;
        document.getElementById('customAlertModal').style.display = 'flex'; // Set to flex to display
    }

    // Fungsi untuk memproses pembayaran
    function initiatePayment() {
        const submitBtn = document.getElementById('process-payment-btn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Memproses Pembayaran...';

        // Lakukan AJAX call ke endpoint Laravel untuk mendapatkan snap token
        fetch('{{ route('frontend.payment.process', ['booking' => $booking->id_booking]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({}) // Empty body as payment method is selected on Snap
        })
        .then(response => {
            if (!response.ok) {
                // Jika response tidak OK (misalnya 500 server error, 422 validation error)
                return response.json().then(errorData => {
                    throw new Error(errorData.error || errorData.message || 'Server error occurred.');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.snap_token) {
                // Tampilkan Midtrans Snap Pop-up
                window.snap.pay(data.snap_token, {
                    onSuccess: function(result){
                        showCustomModal("Pembayaran berhasil! Silakan tunggu sebentar...");
                        setTimeout(() => {
                            // Redirect ke halaman detail booking setelah sukses
                            window.location.href = '{{ route('frontend.booking.show', $booking->id_booking) }}';
                        }, 2000);
                    },
                    onPending: function(result){
                        showCustomModal("Pembayaran Anda sedang menunggu. Silakan selesaikan pembayaran di Midtrans. Anda akan diarahkan ke detail booking.");
                        setTimeout(() => {
                            // Redirect ke halaman detail booking setelah pending
                            window.location.href = '{{ route('frontend.booking.show', $booking->id_booking) }}';
                        }, 2000);
                    },
                    onError: function(result){
                        showCustomModal("Pembayaran gagal. Silakan coba lagi.");
                        console.error("Payment Error:", result);
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Bayar Sekarang Rp {{ number_format($booking->total_price, 0, ',', '.') }}';
                    },
                    onClose: function(){
                        showCustomModal('Anda menutup pop-up tanpa menyelesaikan pembayaran. Silakan coba lagi.');
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Bayar Sekarang Rp {{ number_format($booking->total_price, 0, ',', '.') }}';
                    }
                });
            } else {
                showCustomModal('Gagal mendapatkan token pembayaran: ' + (data.error || 'Terjadi kesalahan.'));
                submitBtn.disabled = false;
                submitBtn.textContent = 'Bayar Sekarang Rp {{ number_format($booking->total_price, 0, ',', '.') }}';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showCustomModal('Terjadi kesalahan jaringan atau server saat memproses pembayaran: ' + error.message);
            submitBtn.disabled = false;
            submitBtn.textContent = 'Bayar Sekarang Rp {{ number_format($booking->total_price, 0, ',', '.') }}';
        });
    }
</script>
@endpush