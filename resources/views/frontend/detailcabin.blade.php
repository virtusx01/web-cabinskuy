@extends('backend.user_layout')

@section('title', $title)

@push('styles')
<style>
    .detail-page-bg {
        background-color: #f4f7f6;
        padding: 20px 0 40px 0;
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
    .date-filter-section {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .date-filter-section h3 {
        font-size: 1.3em;
        color: #223324;
        margin-bottom: 15px;
    }
    .date-filter-group {
        display: flex;
        gap: 25px;
        align-items: end;
        flex-wrap: wrap;
    }
    .date-filter-group .form-group {
        flex: 1;
        min-width: 150px;
    }
    .date-filter-group label {
        display: block;
        font-weight: 500;
        margin-bottom: 5px;
        color: #333;
    }
    .date-filter-group input[type="date"],
    .date-filter-group input[type="number"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ced4da;
        border-radius: 8px;
        font-size: 1em;
    }
    .btn-check-availability {
        background-color: #229954;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: background-color 0.3s;
        min-width: 150px;
    }
    .btn-check-availability:hover {
        background-color: #1c7d43;
    }
    .detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }
    .detail-header-info h1 {
        font-size: 2.2em;
        color: #223324;
        margin: 0;
    }
    .detail-header-info p {
        margin: 5px 0 0 0;
        color: #555;
    }
    .detail-header-price {
        font-size: 1.8em;
        font-weight: bold;
        color: #229954;
        text-align: right;
    }
    .detail-header-price span {
        font-size: 0.6em;
        font-weight: 400;
        color: #777;
    }
    .photo-gallery {
        display: grid;
        grid-template-columns: 1fr;
        gap: 15px;
        margin-bottom: 30px;
    }
    @media (min-width: 768px) {
        .photo-gallery {
            grid-template-columns: 2.5fr 1fr;
        }
    }
    .main-photo-slider {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        background-color: #e0e0e0;
        height: 400px;
    }
    .main-photo-slider .slider-container {
        display: flex;
        transition: transform 0.5s ease;
        height: 100%;
    }
    .main-photo-slider .slide {
        min-width: 100%;
        height: 100%;
    }
    .main-photo-slider .slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .slider-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0,0,0,0.5);
        color: white;
        border: none;
        padding: 10px 15px;
        cursor: pointer;
        font-size: 18px;
        border-radius: 50%;
        transition: background-color 0.3s;
    }
    .slider-nav:hover {
        background: rgba(0,0,0,0.7);
    }
    .slider-nav.prev {
        left: 15px;
    }
    .slider-nav.next {
        right: 15px;
    }
    .slider-indicators {
        position: absolute;
        bottom: 15px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 10px;
    }
    .slider-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: rgba(255,255,255,0.5);
        cursor: pointer;
        transition: background-color 0.3s;
    }
    .slider-indicator.active {
        background: white;
    }
    .thumbnail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 15px;
    }
    .thumbnail-grid img {
        width: 100%;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
        border: 2px solid transparent;
    }
    .thumbnail-grid img:hover,
    .thumbnail-grid img.active-thumbnail {
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border-color: #229954;
    }
    .location-section, .room-selection-section {
        margin-bottom: 30px;
    }
    .section-title {
        font-size: 1.8em;
        color: #223324;
        margin-bottom: 20px;
        border-bottom: 2px solid #e9f5e9;
        padding-bottom: 10px;
    }
    .map-container {
        width: 100%;
        height: 350px;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .map-container iframe {
        width: 100%;
        height: 100%;
        border: 0;
    }
    .room-card {
        display: flex;
        flex-direction: column;
        gap: 20px;
        background-color: #fff;
        border: 1px solid #e9ecef;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 15px;
        align-items: flex-start;
        transition: box-shadow 0.3s ease;
    }
    .room-card:hover {
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }
    @media (min-width: 768px) {
        .room-card {
            flex-direction: row;
            align-items: center;
        }
    }
    .room-card-photo {
        width: 100%;
        height: 200px;
        flex-shrink: 0;
        margin-bottom: 15px;
        position: relative;
        overflow: hidden;
        border-radius: 8px;
    }
    .room-photo-slider {
        display: flex;
        transition: transform 0.5s ease;
        height: 100%;
    }
    .room-photo-slide {
        min-width: 100%;
        height: 100%;
    }
    .room-photo-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .room-slider-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0,0,0,0.5);
        color: white;
        border: none;
        padding: 8px 12px;
        cursor: pointer;
        font-size: 14px;
        border-radius: 50%;
        transition: background-color 0.3s;
        z-index: 2;
    }
    .room-slider-nav:hover {
        background: rgba(0,0,0,0.7);
    }
    .room-slider-nav.prev {
        left: 10px;
    }
    .room-slider-nav.next {
        right: 10px;
    }
    .room-photo-indicators {
        position: absolute;
        bottom: 10px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 6px;
        z-index: 2;
    }
    .room-photo-indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: rgba(255,255,255,0.5);
        cursor: pointer;
        transition: background-color 0.3s;
    }
    .room-photo-indicator.active {
        background: white;
    }
    @media (min-width: 768px) {
        .room-card-photo {
            width: 180px;
            height: 140px;
            margin-bottom: 0;
        }
    }
    .room-card-details {
        flex-grow: 1;
        width: 100%;
    }
    .room-card-details h4 {
        margin: 0 0 5px 0;
        font-size: 1.3em;
        color: #223324;
    }
    .room-card-details p {
        margin: 0 0 10px 0;
        color: #666;
        font-size: 0.9em;
        line-height: 1.5;
    }
    .room-card-amenities {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 10px;
    }
    .room-card-amenities span {
        display: inline-flex;
        align-items: center;
        font-size: 0.85em;
        color: #777;
    }
    .room-card-amenities i {
        margin-right: 5px;
        color: #229954;
    }
    .room-card-booking {
        text-align: left;
        flex-shrink: 0;
        width: 100%;
    }
    @media (min-width: 768px) {
        .room-card-booking {
            text-align: right;
            width: 200px;
        }
    }
    .room-card-booking .price {
        font-size: 1.4em;
        font-weight: bold;
        color: #229954;
        margin-bottom: 5px;
    }
    .room-card-booking .price span {
        font-size: 0.6em;
        font-weight: normal;
        color: #777;
    }
    .room-card-booking .btn-book {
        background-color: #229954;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        margin-top: 10px;
        transition: background-color 0.3s;
        width: 100%;
    }
    .room-card-booking .btn-book:hover {
        background-color: #1c7d43;
    }
    .room-card-booking .btn-book:disabled {
        background-color: #ccc;
        cursor: not-allowed;
    }
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .alert-warning {
        color: #856404;
        background-color: #fff3cd;
        border-color: #ffeaa7;
    }
    .alert-info {
        color: #0c5460;
        background-color: #d1ecf1;
        border-color: #bee5eb;
    }
    .availability-info {
        font-size: 0.9em;
        margin-top: 5px;
        padding: 5px 0;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .availability-info.available {
        color: #155724;
    }
    .availability-info.not-available {
        color: #721c24;
    }
    .availability-info i {
        font-size: 1.1em;
    }
    .single-image .slider-nav,
    .single-image .slider-indicators,
    .single-image .room-slider-nav,
    .single-image .room-photo-indicators {
        display: none;
    }

    /* --- [BARU] CSS untuk Modal Login --- */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 1050;
        display: none;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.show {
        display: flex;
    }
    .modal-content {
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        width: 90%;
        max-width: 450px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        transform: translateY(-50px);
        transition: transform 0.3s ease-out;
    }
    .modal-overlay.show .modal-content {
        transform: translateY(0);
    }
    .modal-content h4 {
        color: #223324;
        font-size: 1.5em;
        margin-top: 0;
        margin-bottom: 10px;
    }
    .modal-content p {
        color: #555;
        margin-bottom: 25px;
    }
    .modal-actions {
        display: flex;
        justify-content: center;
        gap: 15px;
    }
    .modal-actions .btn {
        padding: 10px 25px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .btn-login-confirm {
        background-color: #229954;
        color: #fff;
        text-decoration: none;
    }
    .btn-login-confirm:hover {
        background-color: #1c7d43;
    }
    .btn-login-cancel {
        background-color: #e9ecef;
        color: #333;
        font-weight: bold; 
    }
     .btn-login-cancel:hover {
        background-color: #ced4da;
    }
</style>
@endpush

@section('content')
<div class="detail-page-bg">
    <div class="container">
        {{-- Navigasi Breadcrumb --}}
        <nav class="breadcrumb">
            <a href="{{ route('frontend.beranda') }}">Home</a> >
            <a href="{{ route('frontend.kabin.index') }}">Kabin</a> >
            <span>{{ $cabin->name }}</span>
        </nav>

        {{-- Header Detail Kabin --}}
        <div class="detail-header">
            <div class="detail-header-info">
                <h1>{{ $cabin->name }}</h1>
                <p>📍 {{ $cabin->location_address }}</p>
            </div>
            @if($cabin->rooms->isNotEmpty())
            <div class="detail-header-price">
                Mulai dari IDR {{ number_format($cabin->rooms->sortBy('price')->first()->price, 0, ',', '.') }}
                <span>/malam</span>
            </div>
            @endif
        </div>

        {{-- PHP Helper & Inisialisasi Foto --}}
        @php
            function safeJsonDecode($data) {
                if (is_string($data)) {
                    $decoded = json_decode($data, true);
                    if (is_array($decoded) && !empty($decoded) && is_string($decoded[0])) {
                        $decoded = json_decode($decoded[0], true);
                    }
                    return is_array($decoded) ? $decoded : [];
                }
                return is_array($data) ? $data : [];
            }
            $cabinPhotos = safeJsonDecode($cabin->cabin_photos);
            $defaultPlaceholder = 'https://via.placeholder.com/800x500/e9f5e9/333333?text=Cabinskuy';
        @endphp

        {{-- Galeri Foto --}}
        <section class="photo-gallery">
            <div class="main-photo-slider {{ count($cabinPhotos) <= 1 ? 'single-image' : '' }}">
                <div class="slider-container" id="main-slider-container">
                    @if(!empty($cabinPhotos))
                        @foreach($cabinPhotos as $photo)
                            <div class="slide">
                                <img src="{{ asset('storage/' . str_replace('\\', '/', $photo)) }}" alt="Cabin Photo {{ $loop->iteration }}">
                            </div>
                        @endforeach
                    @else
                        <div class="slide">
                            <img src="{{ $defaultPlaceholder }}" alt="No Photo Available">
                        </div>
                    @endif
                </div>

                @if(count($cabinPhotos) > 1)
                    <button class="slider-nav prev" onclick="changeMainSlide(-1)">❮</button>
                    <button class="slider-nav next" onclick="changeMainSlide(1)">❯</button>
                    <div class="slider-indicators" id="main-indicators">
                        @foreach($cabinPhotos as $index => $photo)
                            <div class="slider-indicator {{ $index === 0 ? 'active' : '' }}" onclick="goToMainSlide({{ $index }})"></div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="thumbnail-grid" id="thumbnail-grid">
                @if(!empty($cabinPhotos))
                    @foreach($cabinPhotos as $photo)
                        <img src="{{ Storage::disk('s3')->url($photo)) }}" alt="Thumbnail {{ $loop->iteration }}" onclick="goToMainSlide({{ $loop->index }})">
                    @endforeach
                @else
                    <img src="{{ $defaultPlaceholder }}" alt="No Photo" class="thumbnail active-thumbnail">
                @endif
            </div>
        </section>

        {{-- Filter Tanggal --}}
        <section class="date-filter-section">
            <h3>Pilih Tanggal Menginap</h3>
            <div class="date-filter-group">
                <div class="form-group">
                    <label for="global_checkin_date">Check-in</label>
                    <input type="date" id="global_checkin_date" min="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group">
                    <label for="global_checkout_date">Check-out</label>
                    <input type="date" id="global_checkout_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                </div>
                <div class="form-group">
                    <label for="total_guests">Jumlah Tamu</label>
                    <input type="number" id="total_guests" min="1" max="10" value="2">
                </div>
                <button type="button" class="btn-check-availability" onclick="checkAllRoomsAvailabilityAndScroll()">
                    Cek Ketersediaan
                </button>
            </div>
        </section>

        {{-- Lokasi & Peta --}}
        <section class="location-section">
            <h3 class="section-title">Location</h3>
            <div class="map-container">
                <iframe src="https://maps.google.com/maps?q={{ urlencode($cabin->location_address) }}&t=&z=15&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
            </div>
        </section>

        {{-- Pilihan Kamar --}}
        <section class="room-selection-section" id="room-selection-section">
            <h3 class="section-title">Select Your Room</h3>
            <div class="rooms-list">
                @forelse ($cabin->rooms as $room)
                @php
                    $roomPhotos = safeJsonDecode($room->room_photos);
                    $roomDefaultPlaceholder = 'https://via.placeholder.com/180x140/e9f5e9/333333?text=Room';
                @endphp
                <div class="room-card" data-room-id="{{ $room->id_room }}">
                    <div class="room-card-photo {{ count($roomPhotos) <= 1 ? 'single-image' : '' }}">
                        <div class="room-photo-slider" id="room-slider-{{ $room->id_room }}">
                            @if(!empty($roomPhotos))
                                @foreach($roomPhotos as $photo)
                                    <div class="room-photo-slide">
                                        <img src="{{ asset('storage/' . str_replace('\\', '/', $photo)) }}" alt="{{ $room->typeroom }} Photo {{ $loop->iteration }}">
                                    </div>
                                @endforeach
                            @else
                                <div class="room-photo-slide">
                                    <img src="{{ $roomDefaultPlaceholder }}" alt="{{ $room->typeroom }}">
                                </div>
                            @endif
                        </div>
                        @if(count($roomPhotos) > 1)
                            <button class="room-slider-nav prev" data-room-id="{{ $room->id_room }}" data-direction="-1">❮</button>
                            <button class="room-slider-nav next" data-room-id="{{ $room->id_room }}" data-direction="1">❯</button>
                            <div class="room-photo-indicators" id="room-indicators-{{ $room->id_room }}">
                                @foreach($roomPhotos as $index => $photo)
                                    <div class="room-photo-indicator {{ $index === 0 ? 'active' : '' }}" data-room-id="{{ $room->id_room }}" data-slide-index="{{ $index }}"></div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="room-card-details">
                        <h4>{{ $room->typeroom }} Cabin</h4>
                        <p>{{ Str::limit($room->description, 150) }}</p>
                        <div class="room-card-amenities">
                            <span><i class="fas fa-users"></i> Max {{ $room->max_guests }} Tamu</span>
                            <span><i class="fas fa-bed"></i> {{ $room->bed_type ?? '1 King Bed' }}</span>
                            <span><i class="fas fa-bath"></i> {{ $room->bathroom_type ?? 'Private Bathroom' }}</span>
                        </div>
                        <div class="availability-info" id="availability-info-{{ $room->id_room }}"></div>
                    </div>
                    <div class="room-card-booking">
                        <div class="price">IDR {{ number_format($room->price, 0, ',', '.') }} <span>/malam</span></div>
                        <form action="{{ route('frontend.booking.start') }}" method="POST" class="booking-start-form">
                            @csrf
                            <input type="hidden" name="id_room" value="{{ $room->id_room }}">
                            <input type="hidden" name="checkin_date" class="hidden-checkin">
                            <input type="hidden" name="checkout_date" class="hidden-checkout">
                            <input type="hidden" name="total_guests" class="hidden-guests" value="2">
                            <button type="submit" class="btn-book" id="book-btn-{{ $room->id_room }}" disabled>
                                Pilih Tanggal Dulu
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> Maaf, saat ini tidak ada kamar yang tersedia untuk kabin ini.
                </div>
                @endforelse
            </div>
        </section>
    </div>
</div>

<div class="modal-overlay" id="loginModal">
    <div class="modal-content">
        <h4>Login Diperlukan</h4>
        <p>Anda harus login terlebih dahulu untuk melanjutkan proses pemesanan.</p>
        <div class="modal-actions">
            <button type="button" class="btn btn-login-cancel" id="cancelLogin">Batal</button>
            <a href="{{ route('backend.login', ['redirect_to' => url()->current()]) }}" class="btn btn-login-confirm">Login</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- [BARU] Logika Modal Login ---
    const loginModal = document.getElementById('loginModal');
    const cancelLoginBtn = document.getElementById('cancelLogin');
    const bookingForms = document.querySelectorAll('.booking-start-form');
    const isAuthenticated = @json(Auth::check()); // Cek status login dari backend

    // Fungsi untuk menampilkan modal
    function showLoginModal() {
        if(loginModal) loginModal.classList.add('show');
    }

    // Fungsi untuk menyembunyikan modal
    function hideLoginModal() {
        if(loginModal) loginModal.classList.remove('show');
    }

    // Tambahkan event listener untuk setiap form booking
    bookingForms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!isAuthenticated) {
                event.preventDefault(); // Hentikan submit form
                showLoginModal();       // Tampilkan modal login
            }
        });
    });

    // Event listener untuk tombol batal pada modal
    if (cancelLoginBtn) {
        cancelLoginBtn.addEventListener('click', hideLoginModal);
    }

    // Event listener untuk menutup modal saat mengklik area luar
    if (loginModal) {
        loginModal.addEventListener('click', function(event) {
            if (event.target === loginModal) {
                hideLoginModal();
            }
        });
    }
    // --- Akhir Logika Modal Login ---


    // --- Logika Halaman yang Sudah Ada (TANPA PERUBAHAN) ---
    const globalCheckin = document.getElementById('global_checkin_date');
    const globalCheckout = document.getElementById('global_checkout_date');
    const totalGuests = document.getElementById('total_guests');
    const thumbnails = document.querySelectorAll('#thumbnail-grid img');
    const roomSelectionSection = document.getElementById('room-selection-section');

    let mainSlideIndex = 0;
    const roomSlideIndexes = {};

    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(today.getDate() + 1);
    const todayStr = today.toISOString().split('T')[0];
    const tomorrowStr = tomorrow.toISOString().split('T')[0];

    globalCheckin.setAttribute('min', todayStr);
    globalCheckout.setAttribute('min', tomorrowStr);

    const initialCheckin = '{{ old('checkin_date', session('pending_booking.checkin_in')) }}' || todayStr;
    const initialCheckout = '{{ old('checkout_date', session('pending_booking.check_out')) }}' || tomorrowStr;
    const initialGuests = '{{ old('total_guests', session('pending_booking.guests') ?? 2) }}';

    globalCheckin.value = initialCheckin;
    globalCheckout.value = initialCheckout;
    totalGuests.value = initialGuests;

    document.querySelectorAll('.room-card').forEach(roomCard => {
        const roomId = roomCard.getAttribute('data-room-id');
        roomSlideIndexes[roomId] = 0;
        const roomSlider = roomCard.querySelector('.room-photo-slider');
        if (roomSlider) {
            roomSlider.style.transform = 'translateX(0%)';
        }
    });

    window.changeMainSlide = function(direction) {
        const container = document.getElementById('main-slider-container');
        if (!container) return;
        const slides = container.children;
        const totalSlides = slides.length;
        if (totalSlides <= 1) return;
        mainSlideIndex = (mainSlideIndex + direction + totalSlides) % totalSlides;
        updateMainSlider();
    }

    window.goToMainSlide = function(index) {
        const container = document.getElementById('main-slider-container');
        if (!container) return;
        const totalSlides = container.children.length;
        if (index >= 0 && index < totalSlides) {
            mainSlideIndex = index;
            updateMainSlider();
        }
    }

    function updateMainSlider() {
        const container = document.getElementById('main-slider-container');
        const indicators = document.querySelectorAll('#main-indicators .slider-indicator');
        if (container) {
            container.style.transform = `translateX(-${mainSlideIndex * 100}%)`;
        }
        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index === mainSlideIndex);
        });
        thumbnails.forEach((thumb, index) => {
            thumb.classList.toggle('active-thumbnail', index === mainSlideIndex);
        });
    }

    function changeRoomSlide(roomId, direction) {
        const roomCard = document.querySelector(`.room-card[data-room-id="${roomId}"]`);
        if (!roomCard) return;
        const slider = roomCard.querySelector('.room-photo-slider');
        if (!slider) return;
        const slides = slider.children;
        const totalSlides = slides.length;
        if (totalSlides <= 1) return;
        if (!(roomId in roomSlideIndexes)) {
            roomSlideIndexes[roomId] = 0;
        }
        roomSlideIndexes[roomId] = (roomSlideIndexes[roomId] + direction + totalSlides) % totalSlides;
        updateRoomSlider(roomId);
    }

    function goToRoomSlide(roomId, index) {
        const roomCard = document.querySelector(`.room-card[data-room-id="${roomId}"]`);
        if (!roomCard) return;
        const slider = roomCard.querySelector('.room-photo-slider');
        if (!slider) return;
        const totalSlides = slider.children.length;
        if (index >= 0 && index < totalSlides) {
            roomSlideIndexes[roomId] = index;
            updateRoomSlider(roomId);
        }
    }

    function updateRoomSlider(roomId) {
        const roomCard = document.querySelector(`.room-card[data-room-id="${roomId}"]`);
        if (!roomCard) return;
        const slider = roomCard.querySelector('.room-photo-slider');
        const indicators = roomCard.querySelectorAll('.room-photo-indicator');
        if (!slider) return;
        const currentIndex = roomSlideIndexes[roomId] || 0;
        slider.style.transform = `translateX(-${currentIndex * 100}%)`;
        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index === currentIndex);
        });
    }

    document.querySelectorAll('.room-slider-nav').forEach(button => {
        button.addEventListener('click', function() {
            const roomId = this.getAttribute('data-room-id');
            const direction = parseInt(this.getAttribute('data-direction'));
            changeRoomSlide(roomId, direction);
        });
    });

    document.querySelectorAll('.room-photo-indicator').forEach(indicator => {
        indicator.addEventListener('click', function() {
            const roomId = this.getAttribute('data-room-id');
            const slideIndex = parseInt(this.getAttribute('data-slide-index'));
            goToRoomSlide(roomId, slideIndex);
        });
    });

    if (thumbnails.length > 0) {
        thumbnails[0].classList.add('active-thumbnail');
    }

    function handleDateChange() {
        const checkinDateVal = globalCheckin.value;
        if(checkinDateVal) {
            const checkinDate = new Date(checkinDateVal);
            checkinDate.setDate(checkinDate.getDate() + 1);
            const minCheckout = checkinDate.toISOString().split('T')[0];
            globalCheckout.setAttribute('min', minCheckout);
            if (globalCheckout.value && new Date(globalCheckout.value) < new Date(minCheckout)) {
                globalCheckout.value = minCheckout;
            }
        }
        updateHiddenFields();
        checkAllRoomsAvailability();
    }

    globalCheckin.addEventListener('change', handleDateChange);
    globalCheckout.addEventListener('change', handleDateChange);
    totalGuests.addEventListener('input', handleDateChange);

    function updateHiddenFields() {
        document.querySelectorAll('.booking-start-form').forEach(form => {
            form.querySelector('.hidden-checkin').value = globalCheckin.value;
            form.querySelector('.hidden-checkout').value = globalCheckout.value;
            form.querySelector('.hidden-guests').value = totalGuests.value;
        });
    }

    window.checkAllRoomsAvailability = function() {
        const checkinDate = globalCheckin.value;
        const checkoutDate = globalCheckout.value;
        const guests = totalGuests.value;

        if (!checkinDate || !checkoutDate) {
            document.querySelectorAll('.btn-book').forEach(button => {
                button.disabled = true;
                button.textContent = 'Pilih Tanggal Dulu';
            });
            return;
        }

        if (new Date(checkoutDate) <= new Date(checkinDate)) {
             document.querySelectorAll('.availability-info').forEach(info => {
                info.innerHTML = '<i class="fas fa-exclamation-circle"></i> <small>Tanggal check-out harus setelah check-in.</small>';
                info.className = 'availability-info not-available';
            });
            return;
        }

        document.querySelectorAll('.room-card').forEach(roomCard => {
            const id_room = roomCard.getAttribute('data-room-id');
            checkRoomAvailability(id_room, checkinDate, checkoutDate, guests);
        });
    }

    window.checkAllRoomsAvailabilityAndScroll = function() {
        checkAllRoomsAvailability();
        if (roomSelectionSection) {
            roomSelectionSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function checkRoomAvailability(id_room, checkinDate, checkoutDate, guests) {
        const availabilityInfo = document.getElementById(`availability-info-${id_room}`);
        const bookButton = document.getElementById(`book-btn-${id_room}`);

        if (!availabilityInfo || !bookButton) return;

        availabilityInfo.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <small>Mengecek ketersediaan...</small>';
        availabilityInfo.className = 'availability-info';
        bookButton.disabled = true;
        bookButton.textContent = 'Mengecek...';

        fetch('{{ route("api.booking.check-availability") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                id_room: id_room,
                check_in_date: checkinDate,
                check_out_date: checkoutDate,
                slots_needed: parseInt(guests)
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errorData => {
                    throw new Error(errorData.message || 'Server error.');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.available) {
                availabilityInfo.innerHTML = `<i class="fas fa-check-circle"></i> <small>✅ ${data.message}</small>`;
                availabilityInfo.className = 'availability-info available';
                bookButton.disabled = false;
                bookButton.textContent = 'Booking Sekarang';
            } else {
                availabilityInfo.innerHTML = `<i class="fas fa-times-circle"></i> <small>❌ ${data.message}</small>`;
                availabilityInfo.className = 'availability-info not-available';
                bookButton.disabled = true;
                bookButton.textContent = 'Tidak Tersedia';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            availabilityInfo.innerHTML = `<i class="fas fa-exclamation-triangle"></i> <small>❌ Gagal: ${error.message}</small>`;
            availabilityInfo.className = 'availability-info not-available';
            bookButton.disabled = true;
            bookButton.textContent = 'Error';
        });
    }

    let autoSlideInterval = setInterval(() => changeMainSlide(1), 5000);

    const mainSlider = document.querySelector('.main-photo-slider');
    if (mainSlider) {
        mainSlider.addEventListener('mouseenter', () => clearInterval(autoSlideInterval));
        mainSlider.addEventListener('mouseleave', () => autoSlideInterval = setInterval(() => changeMainSlide(1), 5000));
    }

    updateHiddenFields();
    setTimeout(checkAllRoomsAvailability, 100);
});
</script>
@endpush