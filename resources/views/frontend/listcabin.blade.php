@extends('backend.user_layout')

{{-- Mengatur judul spesifik untuk halaman ini --}}
@section('title', 'Daftar Kabin - Cabinskuy')

@push('styles')
{{-- Menambahkan beberapa style untuk tampilan yang lebih baik --}}
<style>
    .breadcrumb {
        padding-block: 25px 0;
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
    .filter-bar {
        display: flex;
        gap: 1.5rem;
        padding: 1.5rem;
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        margin-bottom: 2.5rem;
        flex-wrap: wrap;
    }
    .filter-bar .form-group {
        flex: 1;
        min-width: 200px;
    }
    .filter-bar label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
        color: #555;
    }
    .filter-bar select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: #f9f9f9;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 1rem;
    }

    .cabin-list-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 2.5rem;
    }

    .cabin-list-item {
        background-color: #fff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        border: 1px solid #f0f0f0;
    }

    .cabin-list-item:hover {
        transform: translateY(-12px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        border-color: #229954;
    }

    .cabin-list-item a {
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .cabin-list-item .img-container {
        position: relative;
        height: 240px;
        overflow: hidden;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    /* Cabin Image Slider Styles */
    .cabin-images-slider {
        position: relative;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }

    .cabin-images-container {
        display: flex;
        transition: transform 0.3s ease;
        height: 100%;
    }

    .cabin-image-item {
        flex-shrink: 0;
        width: 100%;
        height: 100%;
        position: relative;
    }

    .cabin-image-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .cabin-list-item:hover .cabin-image-item img {
        transform: scale(1.08);
    }

    .cabin-slider-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0, 0, 0, 0.6);
        color: white;
        border: none;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.8rem;
        z-index: 10;
        backdrop-filter: blur(10px);
        outline: none;
        user-select: none;
    }

    .cabin-slider-nav:hover {
        background: rgba(34, 153, 84, 0.8);
        transform: translateY(-50%) scale(1.1);
    }

    .cabin-slider-nav:active {
        transform: translateY(-50%) scale(0.95);
    }

    .cabin-slider-nav.prev {
        left: 10px;
    }

    .cabin-slider-nav.next {
        right: 10px;
    }

    .cabin-slider-dots {
        position: absolute;
        bottom: 15px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 6px;
        z-index: 2;
    }

    .cabin-slider-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.5);
        cursor: pointer;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.3);
        user-select: none;
    }

    .cabin-slider-dot:hover {
        background: rgba(255, 255, 255, 0.8);
        transform: scale(1.1);
    }

    .cabin-slider-dot.active {
        background: #229954;
        transform: scale(1.2);
        box-shadow: 0 0 10px rgba(34, 153, 84, 0.6);
    }

    .cabin-list-item .location-tag {
        position: absolute;
        top: 15px;
        left: 15px;
        background: linear-gradient(135deg, rgba(34, 153, 84, 0.95), rgba(28, 125, 67, 0.95));
        color: #fff;
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 15px rgba(34, 153, 84, 0.3);
        z-index: 3;
    }

    .cabin-image-counter {
        position: absolute;
        top: 15px;
        right: 15px;
        color: #fff;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        backdrop-filter: blur(10px);
        z-index: 3;
    }

    .cabin-list-item-content {
        padding: 2rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        background: linear-gradient(180deg, #fff 0%, #fafafa 100%);
    }

    .cabin-list-item-content h3 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 1rem 0;
        color: #2c3e50;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .cabin-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 1.2rem;
        margin-bottom: 1.2rem;
        background-color: #f8fffe;
    }

    .cabin-meta span {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: #5a6c7d;
        font-weight: 500;
    }

    .cabin-meta i {
        color: #229954;
        font-size: 1rem;
    }

    .cabin-description {
        font-size: 0.95rem;
        color: #6c757d;
        line-height: 1.6;
        margin-bottom: 1.5rem;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .room-types-list {
        margin-bottom: 1.5rem;
    }

    .room-types-list-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.8rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: #34495e;
    }

    .room-types-list ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .room-types-list li {
        background-color: #e9f5e9;
        color: #1e7e3f;
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .room-preview {
        margin-bottom: 1.5rem;
    }

    .room-preview-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.8rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: #34495e;
    }

    .room-photos-container {
        display: flex;
        gap: 0.5rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
        scrollbar-width: thin;
        scrollbar-color: #229954 #f1f1f1;
    }

    .room-photos-container::-webkit-scrollbar {
        height: 4px;
    }

    .room-photos-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 2px;
    }

    .room-photos-container::-webkit-scrollbar-thumb {
        background: #229954;
        border-radius: 2px;
    }

    .room-photo-item {
        flex-shrink: 0;
        width: 80px;
        height: 60px;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .room-photo-item:hover {
        border-color: #229954;
        transform: scale(1.05);
    }

    .room-photo-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .pricing-section {
        margin-top: auto;
        padding-top: 1.5rem;
        border-top: 2px solid #f8f9fa;
    }

    .cabin-price {
        font-size: 1.4rem;
        font-weight: 800;
        background: linear-gradient(135deg, #229954, #1e7e3f);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 1.2rem;
        display: flex;
        align-items: baseline;
        gap: 0.5rem;
    }

    .cabin-price .price-label {
        font-weight: 500;
        font-size: 0.85rem;
        color: #6c757d;
        -webkit-text-fill-color: #6c757d;
    }

    .price-details {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-view-details {
        display: block;
        background: linear-gradient(135deg, #229954, #1e7e3f);
        color: #fff !important;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        text-align: center;
        font-weight: 700;
        font-size: 1rem;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(34, 153, 84, 0.3);
        position: relative;
        overflow: hidden;
    }

    .btn-view-details::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s;
    }

    .btn-view-details:hover::before {
        left: 100%;
    }

    .btn-view-details:hover {
        background: linear-gradient(135deg, #1e7e3f, #229954);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(34, 153, 84, 0.4);
    }

    .no-results {
        grid-column: 1 / -1;
        text-align: center;
        padding: 4rem 2rem;
        background: linear-gradient(135deg, #fff, #f8f9fa);
        border: 2px dashed #e9f5e9;
        border-radius: 20px;
        color: #6c757d;
    }

    .no-results-icon {
        font-size: 3.5rem;
        margin-bottom: 1.5rem;
        opacity: 0.7;
    }

    .no-results h4 {
        color: #34495e;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .pagination-container {
        margin-top: 3rem;
    }

    .room-count-badge {
        color: rgb(4, 148, 9);
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 1rem;
    }

    @media (max-width: 768px) {
        .cabin-list-container {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .cabin-list-item-content {
            padding: 1.5rem;
        }

        .cabin-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.8rem;
        }

        .room-photos-container {
            gap: 0.3rem;
        }

        .room-photo-item {
            width: 70px;
            height: 50px;
        }

        .cabin-slider-nav {
            width: 30px;
            height: 30px;
            font-size: 0.7rem;
        }

        .cabin-slider-nav.prev {
            left: 8px;
        }

        .cabin-slider-nav.next {
            right: 8px;
        }
        .page-title{
            text-align: center;
        }
        #page-explaintitle{
            text-align: center;
        }
    }

</style>
@endpush

{{-- Memulai bagian konten yang akan dimasukkan ke @yield('content') --}}
@section('content')

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="{{ route('frontend.beranda') }}">Home</a> >
            <span>List Cabin</span>
        </nav>
        <h1 class="page-title" style="padding-block: 10px; text-align: center;font-size: 2.5rem;">Temukan Kabin Impian Anda</h1>
        <p id="page-explaintitle" style="padding-bottom: 20px; text-align: center;">Jelajahi berbagai pilihan kabin di lokasi terbaik.</p>
    </div>
</section>

<div class="container">
    <section class="filter-bar">
        <form action="{{ route('frontend.kabin.index') }}" method="GET" id="filter-form" style="display: contents;">
            <div class="form-group">
                <label for="filter-province">Provinsi:</label>
                <select id="filter-province" name="province" onchange="this.form.submit()">
                    <option value="">Semua Provinsi</option>
                    @isset($provinces)
                        @foreach($provinces as $prov)
                            <option value="{{ $prov->province }}" {{ request('province') == $prov->province ? 'selected' : '' }}>
                                {{ $prov->province }}
                            </option>
                        @endforeach
                    @endisset
                </select>
            </div>
            <div class="form-group">
                <label for="filter-regency">Kabupaten/Kota:</label>
                <select id="filter-regency" name="regency" onchange="this.form.submit()">
                    <option value="">Semua Kabupaten/Kota</option>
                    @isset($regencies)
                        @foreach($regencies as $reg)
                            <option value="{{ $reg->regency }}" {{ request('regency') == $reg->regency ? 'selected' : '' }}>
                                {{ $reg->regency }}
                            </option>
                        @endforeach
                    @endisset
                </select>
            </div>
            <div class="form-group">
                <label for="filter-type">Tipe Kamar:</label>
                <select id="filter-type" name="typeroom" onchange="this.form.submit()">
                    <option value="">Semua Tipe</option>
                    <option value="Standard" {{ request('typeroom') == 'Standard' ? 'selected' : '' }}>Standard</option>
                    <option value="Deluxe" {{ request('typeroom') == 'Deluxe' ? 'selected' : '' }}>Deluxe</option>
                    <option value="Executive" {{ request('typeroom') == 'Executive' ? 'selected' : '' }}>Executive</option>
                    <option value="Family Suite" {{ request('typeroom') == 'Family Suite' ? 'selected' : '' }}>Family Suite</option>
                </select>
            </div>

            <div class="form-group">
            <label for="guests" data-translate="guests_label">Jumlah Tamu:</label>
            <select id="guests" name="guests" onchange="this.form.submit()">
            </select>
        </div>
                
            <noscript>
                <button type="submit" class="btn">Filter</button>
            </noscript>
        </form>
    </section>

    <section class="cabin-list-container">
        @forelse ($cabins as $cabin)
            @php
                $cheapestRoom = $cabin->rooms->sortBy('price')->first();

                // Process cabin photos
                $cabinPhotos = [];
                if (is_array($cabin->cabin_photos) && !empty($cabin->cabin_photos)) {
                    foreach($cabin->cabin_photos as $photo) {
                        if (!empty($photo)) {
                            $cabinPhotos[] = asset('storage/' . $photo);
                        }
                    }
                }

                // Fallback if no cabin photos
                if (empty($cabinPhotos)) {
                    $cabinPhotos = ['https://placehold.co/400x250/008272/FFFFFF?text=Cabinskuy'];
                }

                // Get the first photo of each room for preview
                $roomPreviewPhotos = [];
                foreach($cabin->rooms as $room) {
                    if(is_array($room->room_photos) && !empty($room->room_photos)) {
                        // Take only the first photo from each room's room_photos array
                        $roomPreviewPhotos[] = $room->room_photos[0];
                    }
                }
                // Ensure unique photos and limit to maximum 5 for preview
                $roomPreviewPhotos = array_unique(array_slice($roomPreviewPhotos, 0, 5));
            @endphp
            <article class="cabin-list-item">
                <a href="{{ route('frontend.kabin.show', $cabin) }}">
                    <div class="img-container">
                        <div class="cabin-images-slider" data-cabin-id="{{ $cabin->id }}">
                            <div class="cabin-images-container">
                                @foreach($cabinPhotos as $index => $photo)
                                    <div class="cabin-image-item">
                                        <img src="{{ Storage::disk('s3')->url($photo) }}" alt="{{ $cabin->name }} - Foto {{ $index + 1 }}"
                                            onerror="this.onerror=null;this.src='https://placehold.co/400x250/dc3545/FFFFFF?text=Image+Error';">
                                    </div>
                                @endforeach
                            </div>

                            @if(count($cabinPhotos) > 1)
                                <button class="cabin-slider-nav prev" type="button">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button class="cabin-slider-nav next" type="button">
                                    <i class="fas fa-chevron-right"></i>
                                </button>

                                <div class="cabin-slider-dots">
                                    @foreach($cabinPhotos as $index => $photo)
                                        <div class="cabin-slider-dot {{ $index === 0 ? 'active' : '' }}"
                                             data-index="{{ $index }}"></div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="location-tag">
                            <i class="fas fa-map-marker-alt"></i>&nbsp;{{ $cabin->regency }}, {{ $cabin->province }}
                        </div>

                        @if(count($cabinPhotos) > 1)
                            <div class="cabin-image-counter">
                                <i class="fas fa-camera"></i>
                                <span class="current-image">1</span> / {{ count($cabinPhotos) }}
                            </div>
                        @endif
                    </div>
                    <div class="cabin-list-item-content">
                        <h3>{{ $cabin->name }}</h3>

                        <div class="room-count-badge">
                            <i class="fas fa-door-open"></i> {{ $cabin->rooms->count() }} Tipe Kamar Tersedia
                        </div>

                        {{-- Display list of available room types --}}
                        @if($cabin->rooms->isNotEmpty())
                            <div class="room-types-list">
                                <div class="room-types-list-header">
                                    <i class="fas fa-door-open"></i>
                                    Tipe Kamar:
                                </div>
                                <ul>
                                    @foreach($cabin->rooms->unique('typeroom') as $roomType)
                                        <li>{{ $roomType->typeroom }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <p class="cabin-description">{{ \Illuminate\Support\Str::limit($cabin->description, 120, '...') }}</p>

                        @if(!empty($roomPreviewPhotos))
                            <div class="room-preview">
                                <div class="room-preview-header">
                                    <i class="fas fa-images"></i>
                                    Preview Kamar
                                </div>
                                <div class="room-photos-container">
                                    @foreach($roomPreviewPhotos as $photo)
                                        <div class="room-photo-item">
                                            <img src="{{ Storage::disk('s3')->url($photo)) }}"
                                                 alt="Room Preview"
                                                 onerror="this.onerror=null;this.src='https://placehold.co/80x60/e9ecef/6c757d?text=No+Image';">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="pricing-section">
                            @if($cheapestRoom)
                                <div class="cabin-price">
                                    Rp {{ number_format($cheapestRoom->price, 0, ',', '.') }}
                                    <span class="price-label">/ malam</span>
                                </div>
                                <div class="price-details">
                                    <span>Mulai dari harga termurah</span>
                                    @if($cabin->rooms->count() > 1)
                                        <span>+{{ $cabin->rooms->count() - 1 }} tipe lainnya</span>
                                    @endif
                                </div>
                            @else
                                <p class="cabin-price">Harga tidak tersedia</p>
                            @endif

                            <div class="btn-view-details">
                                <i class="fas fa-eye"></i> Lihat Detail & Pesan
                            </div>
                        </div>
                    </div>
                </a>
            </article>
        @empty
            <div class="no-results">
                <div class="no-results-icon">🏠</div>
                <h4>Tidak Ada Kabin Ditemukan</h4>
                <p>Maaf, kami tidak dapat menemukan kabin yang sesuai dengan kriteria filter Anda. Coba ubah filter Anda untuk hasil yang lebih luas.</p>
            </div>
        @endforelse
    </section>

    @if ($cabins->hasPages())
        <nav class="pagination-container">
            {{ $cabins->withQueryString()->links() }}
        </nav>
    @endif
</div>

@push('scripts')
<script>
    class CabinSlider {
        constructor(sliderElement) {
            this.slider = sliderElement;
            this.cabinId = sliderElement.dataset.cabinId;
            this.container = sliderElement.querySelector('.cabin-images-container');
            this.images = sliderElement.querySelectorAll('.cabin-image-item');
            this.dots = sliderElement.querySelectorAll('.cabin-slider-dot');
            this.prevBtn = sliderElement.querySelector('.cabin-slider-nav.prev');
            this.nextBtn = sliderElement.querySelector('.cabin-slider-nav.next');
            this.counter = sliderElement.parentElement.querySelector('.cabin-image-counter .current-image');

            this.currentIndex = 0;
            this.totalImages = this.images.length;

            this.init();
        }

        init() {
            if (this.totalImages <= 1) return;

            // Add event listeners
            if (this.prevBtn) {
                this.prevBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.prev();
                });
            }

            if (this.nextBtn) {
                this.nextBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.next();
                });
            }

            // Add dots event listeners
            this.dots.forEach((dot, index) => {
                dot.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const targetIndex = parseInt(dot.dataset.index) || index;
                    this.goTo(targetIndex);
                });
            });

            // Add touch/swipe support
            this.addTouchSupport();

            // Add keyboard support
            this.slider.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    this.prev();
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    this.next();
                }
            });
        }

        addTouchSupport() {
            let startX = 0;
            let isDragging = false;

            this.container.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
                isDragging = true;
            });

            this.container.addEventListener('touchmove', (e) => {
                if (!isDragging) return;
                e.preventDefault();
            });

            this.container.addEventListener('touchend', (e) => {
                if (!isDragging) return;

                const endX = e.changedTouches[0].clientX;
                const diff = startX - endX;

                if (Math.abs(diff) > 50) { // Minimum swipe distance
                    if (diff > 0) {
                        this.next();
                    } else {
                        this.prev();
                    }
                }

                isDragging = false;
            });
        }

        next() {
            this.currentIndex = (this.currentIndex + 1) % this.totalImages;
            this.updateSlider();
        }

        prev() {
            this.currentIndex = (this.currentIndex - 1 + this.totalImages) % this.totalImages;
            this.updateSlider();
        }

        goTo(index) {
            if (index >= 0 && index < this.totalImages) {
                this.currentIndex = index;
                this.updateSlider();
            }
        }

        updateSlider() {
            // Update container position
            const translateX = -this.currentIndex * 100;
            this.container.style.transform = `translateX(${translateX}%)`;

            // Update dots
            this.dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === this.currentIndex);
            });

            // Update counter
            if (this.counter) {
                this.counter.textContent = this.currentIndex + 1;
            }
        }
    }

    // Initialize all cabin sliders
    document.addEventListener('DOMContentLoaded', function() {
        const sliders = document.querySelectorAll('.cabin-images-slider');
        sliders.forEach(slider => {
            new CabinSlider(slider);
        });
    });

    // Auto-slide functionality (optional)
    let autoSlideInterval = null;

    function startAutoSlide() {
        const sliders = document.querySelectorAll('.cabin-images-slider');
        sliders.forEach(sliderElement => {
            const cabinId = sliderElement.dataset.cabinId;
            const totalImages = sliderElement.querySelectorAll('.cabin-image-item').length;

            if (totalImages > 1) {
                sliderElement.addEventListener('mouseenter', () => {
                    clearInterval(autoSlideInterval);
                });

                sliderElement.addEventListener('mouseleave', () => {
                    // Optional: restart auto-slide on mouse leave
                    // Uncomment below lines if you want auto-slide
                    /*
                    autoSlideInterval = setInterval(() => {
                        const slider = new CabinSlider(sliderElement);
                        slider.next();
                    }, 3000);
                    */
                });
            }
        });
    }

    // Existing filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        const provinceSelect = document.getElementById('filter-province');
        const regencySelect = document.getElementById('filter-regency');
        const typeSelect = document.getElementById('filter-type');

        if (provinceSelect) {
            provinceSelect.addEventListener('change', function() {
                const province = this.value;

                // Clear current regency options
                regencySelect.innerHTML = '<option value="">Semua Kabupaten/Kota</option>';

                if (province) {
                    // Make an AJAX request to get regencies for the selected province
                    fetch(`/api/regencies?province=${province}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(regency => {
                                const option = document.createElement('option');
                                option.value = regency.regency;
                                option.textContent = regency.regency;
                                regencySelect.appendChild(option);
                            });
                            const currentRegency = '{{ request('regency') }}';
                            if (currentRegency && Array.from(regencySelect.options).some(option => option.value === currentRegency)) {
                                regencySelect.value = currentRegency;
                            }
                            document.getElementById('filter-form').submit();
                        })
                        .catch(error => console.error('Error fetching regencies:', error));
                } else {
                    document.getElementById('filter-form').submit();
                }
            });
        }

        if (typeSelect) {
            typeSelect.addEventListener('change', function() {
                document.getElementById('filter-form').submit();
            });
        }
    });
</script>
@endpush

@endsection