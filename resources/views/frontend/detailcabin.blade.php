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

    /* Date Filter Section */
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

    /* Header Detail Kabin */
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

    /* Enhanced Photo Gallery with Slider */
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

    /* Main Photo Slider */
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

    /* Location Section */
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

    /* Enhanced Room Selection with Image Slider */
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

    /* Room Photo Slider */
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

    /* Hide slider controls when only one image */
    .single-image .slider-nav,
    .single-image .slider-indicators,
    .single-image .room-slider-nav,
    .single-image .room-photo-indicators {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="detail-page-bg">
    <div class="container">
        <nav class="breadcrumb">
            <a href="{{ route('frontend.beranda') }}">Home</a> >
            <a href="{{ route('frontend.kabin.index') }}">Kabin</a> >
            <span>{{ $cabin->name }}</span>
        </nav>

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

        @php
            // Function to safely decode JSON strings
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

            $cabinPhotos = safeJsonDecode($cabin->cabin_photos);
            $defaultPlaceholder = 'https://via.placeholder.com/800x500/e9f5e9/333333?text=Cabinskuy';
        @endphp

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
                        <img src="{{ asset('storage/' . str_replace('\\', '/', $photo)) }}" alt="Thumbnail {{ $loop->iteration }}" onclick="goToMainSlide({{ $loop->index }})">
                    @endforeach
                @else
                    <img src="{{ $defaultPlaceholder }}" alt="No Photo" class="thumbnail active-thumbnail">
                @endif
            </div>
        </section>

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
                <button type="button" class="btn-check-availability" onclick="checkAllRoomsAvailability()">
                    Cek Ketersediaan
                </button>
            </div>
        </section>

        <section class="location-section">
            <h3 class="section-title">Location</h3>
            <div class="map-container">
                <iframe src="https://maps.google.com/maps?q={{ urlencode($cabin->location_address) }}&t=&z=15&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
            </div>
        </section>

        <section class="room-selection-section">
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
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const globalCheckin = document.getElementById('global_checkin_date');
    const globalCheckout = document.getElementById('global_checkout_date');
    const totalGuests = document.getElementById('total_guests');
    const thumbnails = document.querySelectorAll('#thumbnail-grid img');

    // Initialize slider states
    let mainSlideIndex = 0;
    const roomSlideIndexes = {};

    // Set minimal dates
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(today.getDate() + 1);

    globalCheckin.setAttribute('min', today.toISOString().split('T')[0]);
    globalCheckout.setAttribute('min', tomorrow.toISOString().split('T')[0]);

    // Initialize with default values if available from session or current date
    const initialCheckin = '{{ old('checkin_date', session('pending_booking.checkin_in')) }}';
    const initialCheckout = '{{ old('checkout_date', session('pending_booking.check_out')) }}';
    const initialGuests = '{{ old('total_guests', session('pending_booking.guests') ?? 2) }}';

    if (initialCheckin) {
        globalCheckin.value = initialCheckin;
    } else {
        globalCheckin.value = today.toISOString().split('T')[0];
    }

    if (initialCheckout) {
        globalCheckout.value = initialCheckout;
    } else {
        globalCheckout.value = tomorrow.toISOString().split('T')[0];
    }
    totalGuests.value = initialGuests;

    // Initialize room slide indexes for each room card found in the DOM
    document.querySelectorAll('.room-card').forEach(roomCard => {
        const roomId = roomCard.getAttribute('data-room-id');
        roomSlideIndexes[roomId] = 0;

        // Initialize room slider position (important for correct initial display)
        const roomSlider = roomCard.querySelector('.room-photo-slider');
        if (roomSlider) {
            roomSlider.style.transform = 'translateX(0%)';
        }
    });

    // Main slider functions
    window.changeMainSlide = function(direction) {
        const container = document.getElementById('main-slider-container');
        if (!container) return;

        const slides = container.children;
        const totalSlides = slides.length;

        if (totalSlides <= 1) return;

        mainSlideIndex += direction;

        if (mainSlideIndex >= totalSlides) {
            mainSlideIndex = 0;
        } else if (mainSlideIndex < 0) {
            mainSlideIndex = totalSlides - 1;
        }

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

        // Update indicators
        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index === mainSlideIndex);
        });

        // Update thumbnails
        thumbnails.forEach((thumb, index) => {
            thumb.classList.toggle('active-thumbnail', index === mainSlideIndex);
        });
    }

    // Room slider functions (Revised)
    function changeRoomSlide(roomId, direction) {
        const roomCard = document.querySelector(`.room-card[data-room-id="${roomId}"]`);
        if (!roomCard) {
            console.error(`Room card not found for room ID: ${roomId}`);
            return;
        }
        const slider = roomCard.querySelector('.room-photo-slider');
        if (!slider) {
            console.error(`Room slider not found within room card for room ID: ${roomId}`);
            return;
        }

        const slides = slider.children;
        const totalSlides = slides.length;

        if (totalSlides <= 1) return; // No need to slide if only one image

        // Initialize if not exists
        if (!(roomId in roomSlideIndexes)) {
            roomSlideIndexes[roomId] = 0;
        }

        roomSlideIndexes[roomId] += direction;

        if (roomSlideIndexes[roomId] >= totalSlides) {
            roomSlideIndexes[roomId] = 0;
        } else if (roomSlideIndexes[roomId] < 0) {
            roomSlideIndexes[roomId] = totalSlides - 1;
        }

        updateRoomSlider(roomId);
    }

    function goToRoomSlide(roomId, index) {
        const roomCard = document.querySelector(`.room-card[data-room-id="${roomId}"]`);
        if (!roomCard) {
            console.error(`Room card not found for room ID: ${roomId}`);
            return;
        }
        const slider = roomCard.querySelector('.room-photo-slider');
        if (!slider) {
            console.error(`Room slider not found within room card for room ID: ${roomId}`);
            return;
        }

        const totalSlides = slider.children.length;
        if (index >= 0 && index < totalSlides) {
            roomSlideIndexes[roomId] = index;
            updateRoomSlider(roomId);
        }
    }

    function updateRoomSlider(roomId) {
        const roomCard = document.querySelector(`.room-card[data-room-id="${roomId}"]`);
        if (!roomCard) {
            console.error(`Room card not found for room ID: ${roomId}`);
            return;
        }
        const slider = roomCard.querySelector('.room-photo-slider');
        const indicators = roomCard.querySelectorAll('.room-photo-indicator');

        if (!slider) {
            console.error(`Room slider element not found for room ID: ${roomId}`);
            return;
        }

        const currentIndex = roomSlideIndexes[roomId] || 0;
        slider.style.transform = `translateX(-${currentIndex * 100}%)`;

        // Update indicators
        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index === currentIndex);
        });
    }

    // Attach event listeners to the room slider navigation buttons and indicators
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

    // Set the first thumbnail as active on load if available
    if (thumbnails.length > 0) {
        thumbnails[0].classList.add('active-thumbnail');
    }

    // Update checkout minimal date when checkin changes
    globalCheckin.addEventListener('change', function() {
        const checkinDate = new Date(this.value);
        checkinDate.setDate(checkinDate.getDate() + 1);
        const minCheckout = checkinDate.toISOString().split('T')[0];
        globalCheckout.setAttribute('min', minCheckout);

        if (globalCheckout.value && new Date(globalCheckout.value) <= checkinDate) {
            globalCheckout.value = minCheckout;
        }

        updateHiddenFields();
        checkAllRoomsAvailability();
    });

    globalCheckout.addEventListener('change', function() {
        updateHiddenFields();
        checkAllRoomsAvailability();
    });

    totalGuests.addEventListener('change', function() {
        updateHiddenFields();
        checkAllRoomsAvailability();
    });

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
            document.querySelectorAll('.availability-info').forEach(info => {
                info.innerHTML = '<i class="fas fa-info-circle"></i> <small>Pilih tanggal check-in dan check-out.</small>';
                info.className = 'availability-info alert-info';
            });
            return;
        }

        if (new Date(checkoutDate) <= new Date(checkinDate)) {
            document.querySelectorAll('.btn-book').forEach(button => {
                button.disabled = true;
                button.textContent = 'Tanggal Tidak Valid';
            });
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
                    throw new Error(errorData.message || 'Server error occurred.');
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
            availabilityInfo.innerHTML = `<i class="fas fa-exclamation-triangle"></i> <small>❌ Gagal mengecek ketersediaan: ${error.message || 'Terjadi kesalahan'}</small>`;
            availabilityInfo.className = 'availability-info not-available';
            bookButton.disabled = true;
            bookButton.textContent = 'Error';
        });
    }

    // Auto-slide functionality for main slider only
    let autoSlideInterval;

    function startAutoSlide() {
        const container = document.getElementById('main-slider-container');
        if (container && container.children.length > 1) {
            autoSlideInterval = setInterval(() => {
                changeMainSlide(1);
            }, 5000); // Change slide every 5 seconds
        }
    }

    function stopAutoSlide() {
        if (autoSlideInterval) {
            clearInterval(autoSlideInterval);
        }
    }

    // Start auto-slide on page load
    startAutoSlide();

    // Pause auto-slide when user interacts with main slider
    const mainSlider = document.querySelector('.main-photo-slider');
    if (mainSlider) {
        mainSlider.addEventListener('mouseenter', stopAutoSlide);
        mainSlider.addEventListener('mouseleave', startAutoSlide);
    }

    // Touch/swipe support for mobile - Main slider
    let touchStartX = 0;
    let touchEndX = 0;

    function handleMainSliderTouch() {
        if (touchEndX < touchStartX - 50) {
            changeMainSlide(1); // Swipe left - next slide
        }
        if (touchEndX > touchStartX + 50) {
            changeMainSlide(-1); // Swipe right - previous slide
        }
    }

    if (mainSlider) {
        mainSlider.addEventListener('touchstart', e => {
            touchStartX = e.changedTouches[0].screenX;
        });

        mainSlider.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            handleMainSliderTouch();
        });
    }

    // Room slider touch support (Revised)
    document.querySelectorAll('.room-card-photo').forEach(roomPhotoContainer => {
        const roomCard = roomPhotoContainer.closest('.room-card');
        if (!roomCard) return;

        const roomId = roomCard.getAttribute('data-room-id');
        if (!roomId) return;

        let roomTouchStartX = 0;
        let roomTouchEndX = 0;

        roomPhotoContainer.addEventListener('touchstart', e => {
            roomTouchStartX = e.changedTouches[0].screenX;
        });

        roomPhotoContainer.addEventListener('touchend', e => {
            roomTouchEndX = e.changedTouches[0].screenX;
            const swipeThreshold = 50;

            if (roomTouchEndX < roomTouchStartX - swipeThreshold) {
                changeRoomSlide(roomId, 1); // Swipe left - next slide
            }
            if (roomTouchEndX > roomTouchStartX + swipeThreshold) {
                changeRoomSlide(roomId, -1); // Swipe right - previous slide
            }
        });

        // Prevent default touch behavior that might interfere
        roomPhotoContainer.addEventListener('touchmove', e => {
            e.preventDefault();
        }, { passive: false });
    });

    // Keyboard navigation for main slider only
    document.addEventListener('keydown', function(e) {
        // Only handle arrow keys when not focused on input elements
        if (document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'BUTTON') {
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                changeMainSlide(-1);
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                changeMainSlide(1);
            }
        }
    });

    // Initial update and availability check on page load
    updateHiddenFields();

    // Add a small delay to ensure DOM is fully rendered
    setTimeout(() => {
        checkAllRoomsAvailability();
    }, 100);
});
</script>
@endpush