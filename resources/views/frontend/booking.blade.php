@extends('backend.user_layout')

@section('title', $title)

@push('styles')
<style>
    .booking-page-bg {
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

    .booking-layout {
        display: grid;
        grid-template-columns: 1.8fr 1fr;
        gap: 30px;
        align-items: flex-start;
    }

    .booking-form-section {
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    .booking-form-section h3 {
        font-size: 1.5em;
        margin-top: 0;
        margin-bottom: 20px;
        color: #223324;
        border-bottom: 2px solid #e9f5e9;
        padding-bottom: 10px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        margin-bottom: 8px;
        color: #333;
    }

    .form-group input, .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ced4da;
        border-radius: 8px;
        font-size: 1em;
        box-sizing: border-box;
        transition: border-color 0.3s;
    }

    .form-group input:focus, .form-group textarea:focus {
        outline: none;
        border-color: #229954;
        box-shadow: 0 0 0 2px rgba(34, 153, 84, 0.1);
    }

    .form-group input[readonly] {
        background-color: #f8f9fa;
        color: #6c757d;
        cursor: not-allowed; /* Indicate it's not editable */
    }

    .date-group {
        display: flex;
        gap: 15px; /* Adjust gap if needed for design */
    }

    .date-group .form-group {
        flex: 1;
    }

    .btn-confirm-booking {
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
    }

    .btn-confirm-booking:hover {
        background-color: #1c7d43;
    }

    .btn-confirm-booking:disabled {
        background-color: #ccc;
        cursor: not-allowed;
    }

    /* Order Summary Card */
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
        gap: 10px;
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

    /* Responsive Design */
    @media (max-width: 768px) {
        .booking-layout {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .date-group {
            flex-direction: column;
            gap: 0; /* Remove gap when stacking */
        }

        .order-summary-card {
            position: static;
            order: -1; /* Show summary first on mobile */
        }
    }
</style>
@endpush

@section('content')
<div class="booking-page-bg">
    <div class="container">
        <nav class="breadcrumb">
            <a href="{{ route('frontend.beranda') }}">Home</a> >
            <a href="{{ route('frontend.kabin.show', $cabin) }}">{{ $cabin->name }}</a> >
            <span>Booking</span>
        </nav>

        <h1 class="page-title">Konfirmasi Booking Anda</h1>

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

        <div class="booking-layout">
            <div class="booking-form-section">
                <form action="{{ route('frontend.booking.store') }}" method="POST" id="booking-form">
                    @csrf
                    <input type="hidden" name="id_room" value="{{ $room->id_room }}">
                    <input type="hidden" id="price_per_night" value="{{ $room->price }}">
                    <input type="hidden" id="total_price_input" name="total_price" value="">
                    <input type="hidden" id="total_nights_input" name="total_nights" value="">

                    <h3>Detail Pemesan</h3>

                    <div class="form-group">
                        <label for="contact_name">Nama Lengkap</label>
                        <input type="text" id="contact_name" name="contact_name"
                               value="{{ old('contact_name', Auth::user()->name ?? '') }}" required readonly>
                    </div>

                    <div class="form-group">
                        <label for="contact_email">Alamat Email</label>
                        <input type="email" id="contact_email" name="contact_email"
                               value="{{ old('contact_email', Auth::user()->email ?? '') }}" required readonly>
                    </div>

                    <div class="form-group">
                        <label for="contact_phone">Nomor Telepon</label>
                        <input type="tel" id="contact_phone" name="contact_phone"
                               value="{{ old('contact_phone') }}"
                               placeholder="Contoh: +62812345678xx" required>
                    </div>

                    <h3>Detail Menginap</h3>

                    <div class="date-group">
                        <div class="form-group">
                            <label for="checkin_date">Tanggal Check-in</label>
                            <input type="date" id="checkin_date" name="checkin_date"
                                   value="{{ old('checkin_date', $bookingDetails['checkin_date'] ?? '') }}"
                                   readonly required>
                        </div>
                        <div class="form-group">
                            <label for="checkout_date">Tanggal Check-out</label>
                            <input type="date" id="checkout_date" name="checkout_date"
                                   value="{{ old('checkout_date', $bookingDetails['checkout_date'] ?? '') }}"
                                   readonly required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="total_guests">Jumlah Tamu</label>
                        <input type="number" id="total_guests" name="total_guests"
                               value="{{ old('total_guests', $bookingDetails['total_guests'] ?? 2) }}"
                               min="1" max="{{ $room->max_guests }}" readonly required>
                        <small style="color: #777;">Maksimal {{ $room->max_guests }} tamu untuk kamar ini</small>
                    </div>

                    <div class="form-group">
                        <label for="special_requests">Permintaan Khusus (Opsional)</label>
                        <textarea id="special_requests" name="special_requests" rows="3"
                                  placeholder="Contoh: Kamar di lantai bawah, tempat tidur tambahan, dll.">{{ old('special_requests') }}</textarea>
                    </div>

                    <button type="submit" class="btn-confirm-booking" id="confirm-btn">
                        Konfirmasi Booking
                    </button>
                </form>
            </div>

            <div class="order-summary-card">
                <h3>Ringkasan Pesanan</h3>

                @php
                    // Function to safely decode JSON strings (copied from previous logic)
                    function safeJsonDecode($data) {
                        if (is_string($data)) {
                            $decoded = json_decode($data, true);
                            // Check for double encoding
                            if (is_array($decoded) && !empty($decoded) && is_string($decoded[0])) {
                                $decoded = json_decode($decoded[0], true);
                            }
                            return is_array($decoded) ? $decoded : [];
                        }
                        return is_array($data) ? $data : [];
                    }

                    $roomPhotos = safeJsonDecode($room->room_photos);
                    $roomDefaultPlaceholder = 'https://via.placeholder.com/100x80/e9f5e9/333333?text=Room';
                @endphp

                <div class="summary-room-info">
                    <img src="{{ !empty($roomPhotos) ? asset('storage/' . str_replace('\\', '/', $roomPhotos[0])) : $roomDefaultPlaceholder }}"
                         alt="{{ $room->typeroom }}">
                    <div class="summary-room-details">
                        <h4>{{ $room->typeroom }} Kabin</h4>
                        <p>{{ $cabin->name }}</p>
                        <p>📍 {{ $cabin->location }}</p>
                    </div>
                </div>

                <div class="summary-dates">
                    <h5>Detail Tanggal</h5>
                    <div>
                        <span>Check-in:</span>
                        <span id="summary-checkin">-</span>
                    </div>
                    <div>
                        <span>Check-out:</span>
                        <span id="summary-checkout">-</span>
                    </div>
                    <div>
                        <span>Jumlah Malam:</span>
                        <span id="summary-nights">-</span>
                    </div>
                    <div>
                        <span>Jumlah Tamu:</span>
                        <span id="summary-guests">-</span>
                    </div>
                </div>

                <div class="price-details">
                    <div class="price-row">
                        <span>Rp {{ number_format($room->price, 0,',','.') }} x <span id="num_nights">0</span> malam</span>
                        <span id="subtotal">Rp 0</span>
                    </div>
                    <div class="price-row">
                        <span>Pajak & Biaya Layanan</span>
                        <span>Rp 0</span> {{-- Adjust if you have actual tax/service fees --}}
                    </div>
                    <div class="price-row total">
                        <span>Total Pembayaran</span>
                        <span id="total_price">Rp 0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const checkinInput = document.getElementById('checkin_date');
    const checkoutInput = document.getElementById('checkout_date');
    const totalGuestsInput = document.getElementById('total_guests');
    const pricePerNight = parseFloat(document.getElementById('price_per_night').value);

    const summaryCheckin = document.getElementById('summary-checkin');
    const summaryCheckout = document.getElementById('summary-checkout');
    const summaryNights = document.getElementById('summary-nights');
    const summaryGuests = document.getElementById('summary-guests');
    const numNightsSpan = document.getElementById('num_nights');
    const subtotalSpan = document.getElementById('subtotal');
    const totalPriceSpan = document.getElementById('total_price');
    const totalPriceInput = document.getElementById('total_price_input');
    const totalNightsInput = document.getElementById('total_nights_input');
    const confirmBtn = document.getElementById('confirm-btn');

    function calculatePrice() {
        const checkinDateStr = checkinInput.value;
        const checkoutDateStr = checkoutInput.value;
        const guests = parseInt(totalGuestsInput.value);

        // Parse dates carefully to avoid timezone issues, or use a library
        // For simplicity, directly using new Date() with YYYY-MM-DD should work fine for local dates
        const checkinDate = new Date(checkinDateStr + 'T00:00:00'); // Add T00:00:00 to treat as local date
        const checkoutDate = new Date(checkoutDateStr + 'T00:00:00'); // Add T00:00:00

        if (checkinDateStr && checkoutDateStr && checkoutDate > checkinDate && !isNaN(guests)) {
            // Calculate nights
            const timeDiff = checkoutDate.getTime() - checkinDate.getTime();
            const nights = Math.round(timeDiff / (1000 * 3600 * 24)); // Use Math.round to avoid potential floating point issues

            const subtotal = pricePerNight * nights;
            const taxAndService = 0; // For now, assumed to be 0 as per your template
            const totalPrice = subtotal + taxAndService;

            // Format dates for display
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            summaryCheckin.textContent = checkinDate.toLocaleDateString('id-ID', options);
            summaryCheckout.textContent = checkoutDate.toLocaleDateString('id-ID', options);
            
            summaryNights.textContent = nights;
            summaryGuests.textContent = guests;
            numNightsSpan.textContent = nights;
            subtotalSpan.textContent = `Rp ${subtotal.toLocaleString('id-ID')}`;
            totalPriceSpan.textContent = `Rp ${totalPrice.toLocaleString('id-ID')}`;

            totalPriceInput.value = totalPrice;
            totalNightsInput.value = nights;
            
            confirmBtn.disabled = false; // Enable button if valid dates/guests exist
        } else {
            summaryCheckin.textContent = '-';
            summaryCheckout.textContent = '-';
            summaryNights.textContent = '-';
            summaryGuests.textContent = '-';
            numNightsSpan.textContent = '0';
            subtotalSpan.textContent = 'Rp 0';
            totalPriceSpan.textContent = 'Rp 0';
            totalPriceInput.value = '';
            totalNightsInput.value = '';
            
            confirmBtn.disabled = true; // Disable button if dates/guests are invalid
        }
    }

    // Initial calculation on page load based on session data
    document.addEventListener('DOMContentLoaded', calculatePrice);

    // No event listeners for checkin/checkout/guests as they are readonly now
    // The confirm button is enabled/disabled based on the calculatePrice function
</script>
@endpush