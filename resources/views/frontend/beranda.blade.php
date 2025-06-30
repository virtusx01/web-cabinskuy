@extends('backend.user_layout')

@section('title', 'Beranda - Cabinskuy')

@push('styles')
<style>
    /* Hero Section */
    .hero-section {
        background-color: #e9f5e9;
        padding: 100px 0;
        overflow: hidden; /* Mengatasi potensi overflow gambar */
    }
    .hero-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 30px;
        flex-wrap: wrap; /* Pastikan konten hero wrap pada ukuran kecil */
    }
    .hero-text {
        flex-basis: 50%;
        max-width: 50%; /* Memastikan tidak melebihi 50% */
    }
    .hero-text h1 {
        font-size: 2.8em;
        color: var(--darkest-green);
        margin-bottom: 30px;
        font-weight: 700;
        line-height: 1.2;
    }
    .hero-text p {
        font-size: 1.1em;
        color: #556055;
        line-height: 1.6;
        margin-bottom: 15px;
    }
    .hero-image {
        flex-basis: 45%;
        max-width: 45%; /* Memastikan tidak melebihi 45% */
    }
    .hero-image img {
        width: 100%;
        border-radius: var(--border-radius-md);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        display: block; /* Menghilangkan spasi bawah gambar */
    }

    /* Search Bar Section */
    .search-bar-section {
        padding: 30px 0;
        background-color: #ffffff;
    }
    .search-bar-container {
        background-color: var(--light-green);
        padding: 25px;
        border-radius: var(--border-radius-md);
        box-shadow: var(--box-shadow-md);
    }
    .search-bar-container h2 {
        font-size: 1.5em;
        color: var(--darkest-green);
        margin-top: 0;
        margin-bottom: 20px;
    }
    .search-form {
        display: flex;
        gap: 15px;
        align-items: flex-end;
        flex-wrap: wrap; /* Memungkinkan wrap pada layar kecil */
    }
    .search-form .form-group {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        min-width: 150px; /* Minimal lebar untuk setiap input group */
    }
    .search-form label {
        font-size: 0.85em;
        color: #555;
        margin-bottom: 5px;
        font-weight: 500;
    }
    .search-form input,
    .search-form select {
        padding: 12px;
        border: 1px solid #ced4da;
        border-radius: var(--border-radius-sm);
        font-size: 0.95em;
        width: 100%;
        box-sizing: border-box;
        transition: border-color var(--transition-speed);
    }
    .search-form input:focus,
    .search-form select:focus {
        outline: none;
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(34, 153, 84, 0.2);
    }
    .search-form .btn-search {
        background-color: var(--primary-green);
        color: white;
        padding: 12px 25px;
        border: none;
        border-radius: var(--border-radius-sm);
        font-size: 1em;
        font-weight: 500;
        cursor: pointer;
        transition: background-color var(--transition-speed);
        height: 48px; /* Match input height approx */
        flex-shrink: 0; /* Jangan menyusut */
    }
    .search-form .btn-search:hover {
        background-color: var(--dark-green);
    }

    /* Cabin Types Section */
    .cabin-types-section {
        padding: 70px 0;
        background-color: #ffffff;
    }
    .cabin-types-tabs {
        display: flex;
        justify-content: center; /* Pusatkan tab */
        margin-bottom: 30px;
        border-bottom: 1px solid #eee;
        gap: 10px; /* Jarak antar tab */
        flex-wrap: wrap;
    }
    .cabin-types-tabs .tab-link {
        padding: 10px 20px;
        cursor: pointer;
        font-size: 1.1em;
        font-weight: 500;
        color: #555;
        border-bottom: 3px solid transparent;
        transition: color var(--transition-speed), border-color var(--transition-speed);
        white-space: nowrap; /* Mencegah tab patah baris */
    }
    .cabin-types-tabs .tab-link.active,
    .cabin-types-tabs .tab-link:hover {
        color: var(--primary-green);
        border-bottom-color: var(--primary-green);
    }
    .tab-content {
        background-color: #fff;
        padding: 25px;
        border-radius: var(--border-radius-md);
        box-shadow: var(--box-shadow-sm);
        display: none;
        overflow: hidden; /* Penting untuk animasi */
    }
    .tab-content.active {
        display: block;
    }
    .cabin-type-details {
        display: flex;
        gap: 30px;
        align-items: center;
        flex-wrap: wrap; /* Memungkinkan wrap pada layar kecil */
    }
    .cabin-type-details .image-container {
        flex-basis: 50%;
        min-width: 300px; /* Lebar minimum untuk gambar */
    }
    .cabin-type-details img {
        width: 100%;
        max-height: 400px; /* Tinggi maksimal yang lebih terkontrol */
        height: auto; /* Memastikan rasio aspek terjaga */
        object-fit: cover;
        border-radius: var(--border-radius-md);
        box-shadow: var(--box-shadow-sm);
    }
    .cabin-type-details .text-container {
        flex-basis: 45%; /* Sesuaikan agar pas dengan gambar */
        min-width: 300px; /* Lebar minimum untuk teks */
    }
    .cabin-type-details h3 {
        font-size: 1.8em;
        color: var(--darkest-green);
        margin-top: 0;
        margin-bottom: 10px;
    }
    .cabin-type-details p {
        font-size: 1em;
        color: #555;
        line-height: 1.6;
        margin-bottom: 10px;
    }
    .cabin-type-details ul {
        list-style: none; /* Hapus bullet default */
        padding: 0;
        margin-left: 0;
        color: #555;
    }
    .cabin-type-details ul li {
        margin-bottom: 5px;
        position: relative;
        padding-left: 20px; /* Ruang untuk ikon */
    }
    .cabin-type-details ul li::before {
        content: '\f00c'; /* Font Awesome checkmark icon */
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        color: var(--primary-green);
        position: absolute;
        left: 0;
        top: 0;
    }
    .cabin-type-details p strong {
        color: var(--primary-green); /* Warna hijau untuk "Offers" */
    }

    /* Find Near You Section */
    .find-near-you-section {
        padding: 50px 0;
        background-color: #f8f9fa;
    }
    .find-near-you-section h2, .how-to-book-section h2 {
        text-align: center;
        font-size: 2em;
        color: var(--darkest-green);
        margin-bottom: 30px;
    }
    .cabin-cards-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); /* Mengurangi minwidth */
        gap: 25px;
    }
    .cabin-card {
        background-color: #fff;
        border-radius: var(--border-radius-md);
        box-shadow: var(--box-shadow-md);
        overflow: hidden;
        transition: transform var(--transition-speed) ease, box-shadow var(--transition-speed) ease;
        display: flex;
        flex-direction: column;
    }
    .cabin-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15); /* Shadow lebih menonjol */
    }
    .cabin-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
    }
    .cabin-card-content {
        padding: 20px;
        flex-grow: 1; /* Memastikan konten mengisi ruang yang tersisa */
        display: flex;
        flex-direction: column;
    }
    .cabin-card-content h3 {
        font-size: 1.3em;
        color: var(--darkest-green);
        margin-top: 0;
        margin-bottom: 10px;
    }
    .cabin-card-content p {
        font-size: 0.9em;
        color: #555;
        line-height: 1.5;
        margin-bottom: 0;
    }
    .see-more-button-container {
        text-align: center;
        margin-top: 30px;
    }
    .btn-see-more {
        background-color: var(--primary-green);
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: var(--border-radius-sm);
        font-size: 1em;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        transition: background-color var(--transition-speed);
        display: inline-block; /* Agar padding dan margin bekerja */
        position: relative; /* Untuk ripple effect */
        overflow: hidden;
    }
    .btn-see-more:hover {
        background-color: var(--dark-green);
    }

    /* How to Book Section */
    .how-to-book-section {
        padding: 50px 0;
        background-color: var(--light-green);
    }
    .booking-steps-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); /* Penyesuaian minwidth */
        gap: 30px;
        text-align: center;
    }
    .booking-step {
        background-color: #fff;
        padding: 25px 20px;
        border-radius: var(--border-radius-md);
        box-shadow: var(--box-shadow-sm);
        transition: transform var(--transition-speed) ease, box-shadow var(--transition-speed) ease;
    }
    .booking-step:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }
    .booking-step .step-icon-placeholder {
        width: 60px;
        height: 60px;
        background-color: var(--primary-green);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px auto;
        font-size: 1.8em;
        font-weight: bold;
        box-shadow: 0 2px 5px rgba(34, 153, 84, 0.3);
    }
    .booking-step h3 {
        font-size: 1.1em;
        color: var(--darkest-green);
        margin-bottom: 5px;
    }
    .booking-step p {
        font-size: 0.9em;
        color: #555;
        line-height: 1.4;
    }

    /* Responsive adjustments */
    @media (max-width: 992px) {
        .hero-content, .cabin-type-details {
            flex-direction: column;
            text-align: center;
        }
        .hero-text, .hero-image, .cabin-type-details .image-container, .cabin-type-details .text-container {
            flex-basis: 100%;
            max-width: 100%; /* Agar mengisi lebar penuh */
            padding-inline: 0; /* Hapus padding horizontal yang mungkin ada */
        }
        .hero-image { margin-top: 30px; }
        .hero-text h1 { font-size: 2.5em; } /* Penyesuaian ukuran font */
        .search-form { flex-direction: column; align-items: stretch; }
        .search-form .form-group, .search-form .btn-search { width: 100%; }
        .search-form .btn-search { margin-top: 10px; }
        .cabin-type-details ul { text-align: left; /* Biarkan daftar tetap kiri */ }
        .cabin-type-details ul li { margin-left: auto; margin-right: auto; } /* Untuk memusatkan list item jika perlu, tetapi lebih baik left-aligned */
    }
    @media (max-width: 768px) {
        .hero-text h1 { font-size: 2em; } /* Lebih kecil lagi untuk layar sangat kecil */
        .cabin-types-tabs { justify-content: flex-start; overflow-x: auto; /* Gulir jika terlalu banyak tab */ }
        .cabin-type-details .image-container, .cabin-type-details .text-container { min-width: unset; } /* Hapus min-width */
    }
</style>
@endpush

@section('content')
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1 data-translate="hero_title">Live Out The Adventure With Cabinskuy</h1>
                    <p data-translate="hero_description">Discover unique cabin stays nestled in nature. Your perfect getaway for tranquility and adventure awaits. Book your escape today!</p>
                </div>
                <div class="hero-image">
                    <img src="{{ asset('images/assets/master.jpg') }}" alt="Hero Cabin Image">
                </div>
            </div>
        </div>
    </section>

    <section class="search-bar-section">
        <div class="container">
            <div class="search-bar-container">
                <h2 data-translate="search_title">Good Morning! Where Do You Want To Stay?</h2>

                <form class="search-form" action="{{ route('frontend.kabin.search') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="filter-province" data-translate="province_label">Provinsi:</label>
                        <select id="filter-province" name="province">
                            <option value="" data-translate="all_provinces">Semua Provinsi</option>
                            @isset($provinces)
                                @foreach($provinces as $prov)
                                    <option value="{{ $prov->province }}">{{ $prov->province }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                    {{-- Menambahkan dropdown untuk Kabupaten/Kota --}}
                    <div class="form-group">
                        <label for="filter-regency" data-translate="regency_label">Kabupaten/Kota:</label>
                        <select id="filter-regency" name="regency" disabled>
                            <option value="" data-translate="all_regencies">Semua Kabupaten/Kota</option>
                            {{-- Opsi akan diisi via JavaScript --}}
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="check_in_date" data-translate="checkin_label">Check In</label>
                        <input type="date" id="check_in_date" name="check_in_date">
                    </div>
                    <div class="form-group">
                        <label for="check_out_date" data-translate="checkout_label">Check Out</label>
                        <input type="date" id="check_out_date" name="check_out_date">
                    </div>
                    <div class="form-group">
                        <label for="guests" data-translate="guests_label">Guests</label>
                        <select id="guests" name="guests">
                            {{-- Opsi akan diisi via JavaScript di user_layout --}}
                        </select>
                    </div>

                    <button type="submit" class="btn-search" data-translate="search_btn">Search</button>
                </form>

            </div>
        </div>
    </section>

    <section class="cabin-types-section">
        <div class="container">
            <div class="cabin-types-tabs">
                <span class="tab-link active" data-tab="standard" data-translate="standard">Standard</span>
                <span class="tab-link" data-tab="deluxe" data-translate="deluxe">Deluxe</span>
                <span class="tab-link" data-tab="executive" data-translate="executive">Executive</span>
            </div>

            <div id="standard" class="tab-content">
                <div class="cabin-type-details">
                    <div class="image-container">
                        <img src="{{ asset('images/assets/cabinroom1.jpg') }}" alt="Standard Cabin">
                    </div>
                    <div class="text-container">
                        <h3 data-translate="standard_title">Standard Cabin</h3>
                        <p data-translate="standard_desc">Our Standard Cabin offers a cozy and comfortable retreat, perfect for solo travelers or couples. Enjoy essential amenities amidst serene natural surroundings.</p>
                        <ul>
                            <li data-translate="standard_feature1">Comfortable queen-sized bed</li>
                            <li data-translate="standard_feature2">Private bathroom with hot shower</li>
                            <li data-translate="standard_feature3">Small kitchenette area</li>
                            <li data-translate="standard_feature4">Wi-Fi access</li>
                        </ul>
                        <p><strong data-translate="offers">Offers:</strong> <span data-translate="standard_offers">Free breakfast for two, 10% off on weekday stays.</span></p>
                    </div>
                </div>
            </div>
            <div id="deluxe" class="tab-content">
                <div class="cabin-type-details">
                    <div class="image-container">
                        <img src="{{ asset('images/assets/cabinroom2.jpg') }}" alt="Deluxe Cabin">
                    </div>
                    <div class="text-container">
                        <h3 data-translate="deluxe_title">Deluxe Cabin</h3>
                        <p data-translate="deluxe_desc">Experience enhanced comfort and more space in our Deluxe Cabin. Ideal for families or those seeking extra amenities and a touch of luxury.</p>
                        <ul>
                            <li data-translate="deluxe_feature1">Spacious king-sized bed & sofa bed</li>
                            <li data-translate="deluxe_feature2">En-suite bathroom with premium toiletries</li>
                            <li data-translate="deluxe_feature3">Fully equipped kitchenette</li>
                            <li data-translate="deluxe_feature4">Private balcony with a view</li>
                            <li data-translate="deluxe_feature5">Smart TV and high-speed Wi-Fi</li>
                        </ul>
                        <p><strong data-translate="offers">Offers:</strong> <span data-translate="deluxe_offers">Welcome drink & fruit basket, 15% off for stays over 3 nights.</span></p>
                    </div>
                </div>
            </div>
            <div id="executive" class="tab-content">
                <div class="cabin-type-details">
                    <div class="image-container">
                        <img src="{{ asset('images/assets/cabinroom3.jpg') }}" alt="Executive Cabin">
                    </div>
                    <div class="text-container">
                        <h3 data-translate="executive_title">Executive Cabin</h3>
                        <p data-translate="executive_desc">Indulge in the ultimate cabin experience with our Executive Cabin. Featuring top-tier amenities, expansive space, and breathtaking views for an unforgettable stay.</p>
                        <ul>
                            <li data-translate="executive_feature1">Luxurious super king-sized bed</li>
                            <li data-translate="executive_feature2">Jacuzzi in a large en-suite bathroom</li>
                            <li data-translate="executive_feature3">Gourmet kitchen with modern appliances</li>
                            <li data-translate="executive_feature4">Expansive private deck with outdoor seating</li>
                            <li data-translate="executive_feature5">Personalized concierge service</li>
                        </ul>
                        <p><strong data-translate="offers">Offers:</strong> <span data-translate="executive_offers">Complimentary minibar, private bonfire setup, 20% off spa services.</span></p>
                    </div>
                </div>
            </div>

            <div class="see-more-button-container">
                <a href="{{ route('frontend.kabin.index') }}" class="btn-see-more" data-translate="explore_cabins">Explore All Cabins</a>
            </div>
        </div>
    </section>

    <section class="find-near-you-section">
        <div class="container">
            <h2 data-translate="find_near_title">Find CABINSKUY Near You</h2>
            <div class="cabin-cards-container">
                <div class="cabin-card">
                    <img src="{{ asset('images/assets/cabinimg1.jpg') }}" alt="Cabinskuy Bandung">
                    <div class="cabin-card-content">
                        <h3>Cabinskuy, Bandung</h3>
                        <p data-translate="bandung_desc">Escape to the cool highlands of Bandung. Our cabins offer a perfect blend of modern comfort and natural beauty, ideal for a refreshing getaway.</p>
                    </div>
                </div>
                <div class="cabin-card">
                    <img src="{{ asset('images/assets/cabinimg2.jpg') }}" alt="Cabinskuy Bogor">
                    <div class="cabin-card-content">
                        <h3>Cabinskuy, Bogor</h3>
                        <p data-translate="bogor_desc">Discover tranquility just a short drive from the city. Our Bogor cabins are surrounded by lush greenery, offering a peaceful retreat.</p>
                    </div>
                </div>
                <div class="cabin-card">
                    <img src="{{ asset('images/assets/cabinimg3.jpg') }}" alt="Cabinskuy Bali">
                    <div class="cabin-card-content">
                        <h3>Cabinskuy, Bali</h3>
                        <p data-translate="bali_desc">Immerse yourself in the magical island atmosphere of Bali. Our unique cabins provide an exotic and serene base for your island adventures.</p>
                    </div>
                </div>
            </div>
            <div class="see-more-button-container">
                <a href="{{ route('frontend.kabin.index') }}" class="btn-see-more" data-translate="see_more_locations">See More Locations</a>
            </div>
        </div>
    </section>

    <section class="how-to-book-section">
        <div class="container">
            <h2 data-translate="how_to_book">How to Book Your Getaway</h2>
            <div class="booking-steps-container">
                <div class="booking-step">
                    <div class="step-icon-placeholder">1</div>
                    <h3 data-translate="step1_title">Select Your Favorite Cabin</h3>
                    <p data-translate="step1_desc">Browse our diverse collection and choose the cabin that suits your style and needs.</p>
                </div>
                <div class="booking-step">
                    <div class="step-icon-placeholder">2</div>
                    <h3 data-translate="step2_title">Book and Pay</h3>
                    <p data-translate="step2_desc">Secure your dates with our easy and secure online payment system.</p>
                </div>
                <div class="booking-step">
                    <div class="step-icon-placeholder">3</div>
                    <h3 data-translate="step3_title">Verify Your ID</h3>
                    <p data-translate="step3_desc">Complete a simple verification process for a smooth check-in experience.</p>
                </div>
                <div class="booking-step">
                    <div class="step-icon-placeholder">4</div>
                    <h3 data-translate="step4_title">Check-in and Enjoy!</h3>
                    <p data-translate="step4_desc">Arrive at your cabin, check-in seamlessly, and start your unforgettable getaway.</p>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const provinceSelect = document.getElementById('filter-province');
    const regencySelect = document.getElementById('filter-regency');

    // Initial state for regency select
    if (regencySelect && provinceSelect && !provinceSelect.value) {
        regencySelect.disabled = true;
    }

    if (provinceSelect) {
        provinceSelect.addEventListener('change', async function() {
            const province = this.value;

            // Kosongkan dan nonaktifkan dropdown kabupaten/kota saat mengambil data
            regencySelect.innerHTML = '<option value="" data-translate="all_regencies">' + (translations[localStorage.getItem('preferred_language') || 'en'].all_regencies || 'All Regencies/Cities') + '</option>';
            regencySelect.disabled = true;

            if (province) {
                try {
                    const response = await fetch(`/api/regencies?province=${encodeURIComponent(province)}`);
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    const data = await response.json();

                    regencySelect.disabled = false;
                    // Pastikan opsi 'Semua Kabupaten/Kota' ada dan pertama
                    regencySelect.innerHTML = '<option value="" data-translate="all_regencies">' + (translations[localStorage.getItem('preferred_language') || 'en'].all_regencies || 'All Regencies/Cities') + '</option>';

                    data.forEach(regency => {
                        const option = document.createElement('option');
                        option.value = regency.regency;
                        option.textContent = regency.regency;
                        regencySelect.appendChild(option);
                    });
                } catch (error) {
                    console.error('Error fetching regencies:', error);
                    regencySelect.innerHTML = '<option value="">Gagal memuat data</option>';
                    regencySelect.disabled = true; // Nonaktifkan jika gagal
                }
            } else {
                // Jika "Semua Provinsi" dipilih, reset dropdown kabupaten/kota
                regencySelect.innerHTML = '<option value="" data-translate="all_regencies">' + (translations[localStorage.getItem('preferred_language') || 'en'].all_regencies || 'All Regencies/Cities') + '</option>';
                regencySelect.disabled = true; // Atau false jika Anda ingin user bisa memilih "Semua Kabupaten/Kota" tanpa provinsi
            }
            // Re-apply translations for the default option after update
            applyTranslations(localStorage.getItem('preferred_language') || 'en');
        });
    }

    // Kode JavaScript Anda yang lain (untuk tanggal, dll.) sudah di user_layout.blade.php
    // Tidak perlu duplikasi di sini kecuali ada kebutuhan spesifik untuk beranda.
    // Jika Anda ingin menginisialisasi nilai date input pada halaman beranda, bisa ditambahkan di sini.
    const check_in_dateInput = document.getElementById('check_in_date');
    const check_out_dateInput = document.getElementById('check_out_date');

    if(check_in_dateInput && check_out_dateInput) {
        const today = new Date().toISOString().split('T')[0];
        check_in_dateInput.setAttribute('min', today);
        check_out_dateInput.setAttribute('min', today);

        check_in_dateInput.addEventListener('change', function() {
            const checkinDate = new Date(this.value);
            const minCheckoutDate = new Date(checkinDate);
            minCheckoutDate.setDate(minCheckoutDate.getDate() + 1);

            const minCheckoutDateString = minCheckoutDate.toISOString().split('T')[0];
            check_out_dateInput.setAttribute('min', minCheckoutDateString);

            if (check_out_dateInput.value < minCheckoutDateString) {
                check_out_dateInput.value = minCheckoutDateString;
            }
        });
    }
});
</script>
@endpush