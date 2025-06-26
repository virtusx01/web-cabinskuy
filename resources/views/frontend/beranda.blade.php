@extends('backend.user_layout')

@section('title', 'Beranda - Cabinskuy')

@push('styles')
<style>
    /* Hero Section */
    .hero-section {
        background-color: #e9f5e9;
        padding: 50px 0;
    }
    .hero-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 30px;
    }
    .hero-text {
        flex-basis: 50%;
    }
    .hero-text h1 {
        font-size: 2.8em;
        color: #223324;
        margin-bottom: 15px;
        font-weight: 700;
    }
    .hero-text p {
        font-size: 1.1em;
        color: #556055;
        line-height: 1.6;
        margin-bottom: 25px;
    }
    .hero-image {
        flex-basis: 45%;
    }
    .hero-image img {
        width: 100%;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    /* Search Bar Section */
    .search-bar-section {
        padding: 30px 0;
        background-color: #ffffff;
    }
    .search-bar-container {
        background-color: #e9f5e9;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    .search-bar-container h2 {
        font-size: 1.5em;
        color: #223324;
        margin-top: 0;
        margin-bottom: 20px;
    }
    .search-form {
        display: flex;
        gap: 15px;
        align-items: flex-end;
    }
    .search-form .form-group {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
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
        border-radius: 6px;
        font-size: 0.95em;
        width: 100%;
        box-sizing: border-box;
    }
    .search-form .btn-search {
        background-color: #229954;
        color: white;
        padding: 12px 25px;
        border: none;
        border-radius: 6px;
        font-size: 1em;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.3s;
        height: 48px; /* Match input height approx */
    }
    .search-form .btn-search:hover {
        background-color: #1c7d43;
    }

    /* Cabin Types Section */
    .cabin-types-section {
        padding: 50px 0;
        background-color: #ffffff;
    }
    .cabin-types-tabs {
        display: flex;
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
    }
    .cabin-types-tabs .tab-link {
        padding: 10px 20px;
        cursor: pointer;
        font-size: 1.1em;
        font-weight: 500;
        color: #555;
        border-bottom: 3px solid transparent;
        margin-right: 10px;
        transition: color 0.3s, border-color 0.3s;
    }
    .cabin-types-tabs .tab-link.active,
    .cabin-types-tabs .tab-link:hover {
        color: #229954;
        border-bottom-color: #229954;
    }
    .tab-content {
        background-color: #fff;
        padding: 25px;
        border-radius: 8px;
        display: none;
    }
    .tab-content.active {
        display: block;
    }
    .cabin-type-details {
        display: flex;
        gap: 30px;
        align-items: center;
    }
    .cabin-type-details .image-container { flex-basis: 50%; }
    .cabin-type-details img { width: 100%; max-height: 500px; border-radius: 8px; }
    .cabin-type-details .text-container { flex-basis: 50%; }
    .cabin-type-details h3 { font-size: 1.8em; color: #223324; margin-top: 0; margin-bottom: 10px; }
    .cabin-type-details p { font-size: 1em; color: #555; line-height: 1.6; margin-bottom: 10px; }
    .cabin-type-details ul { list-style: disc; margin-left: 20px; padding-left: 0; color: #555; }
    .cabin-type-details ul li { margin-bottom: 5px; }

    /* Find Near You Section */
    .find-near-you-section {
        padding: 50px 0;
        background-color: #f8f9fa;
    }
    .find-near-you-section h2, .how-to-book-section h2 {
        text-align: center;
        font-size: 2em;
        color: #223324;
        margin-bottom: 30px;
    }
    .cabin-cards-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
    }
    .cabin-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .cabin-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }
    .cabin-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    .cabin-card-content { padding: 20px; }
    .cabin-card-content h3 { font-size: 1.3em; color: #223324; margin-top: 0; margin-bottom: 10px; }
    .cabin-card-content p { font-size: 0.9em; color: #555; line-height: 1.5; margin-bottom: 0; }
    .see-more-button-container {
        text-align: center;
        margin-top: 30px;
    }
    .btn-see-more {
        background-color: #229954;
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 6px;
        font-size: 1em;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        transition: background-color 0.3s;
    }
    .btn-see-more:hover {
        background-color: #1c7d43;
    }

    /* How to Book Section */
    .how-to-book-section {
        padding: 50px 0;
        background-color: #e9f5e9;
    }
    .booking-steps-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 30px;
        text-align: center;
    }
    .booking-step {
        background-color: #fff;
        padding: 25px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .booking-step .step-icon-placeholder {
        width: 60px;
        height: 60px;
        background-color: #229954;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px auto;
        font-size: 1.8em;
        font-weight: bold;
    }
    .booking-step h3 {
        font-size: 1.1em;
        color: #223324;
        margin-bottom: 5px;
    }
    .booking-step p {
        font-size: 0.9em;
        color: #555;
        line-height: 1.4;
    }

    /* Responsive adjustments (basic) */
    @media (max-width: 992px) {
        .hero-content, .cabin-type-details {
            flex-direction: column;
            text-align: center;
        }
        .hero-text, .hero-image, .cabin-type-details .image-container, .cabin-type-details .text-container {
            flex-basis: 100%;
        }
        .hero-image { margin-top: 30px; }
        .search-form { flex-direction: column; align-items: stretch; }
        .search-form .form-group, .search-form .btn-search { width: 100%; }
        .search-form .btn-search { margin-top: 10px; }
    }
    @media (max-width: 768px) {
        .hero-text h1 { font-size: 2.2em; }
    }
</style>
@endpush

@section('content')
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>Live Out The Adventure With Cabinskuy</h1>
                    <p>Discover unique cabin stays nestled in nature. Your perfect getaway for tranquility and adventure awaits. Book your escape today!</p>
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
            <h2>Good Morning! Where Do You Want To Stay?</h2>
            
            {{-- ================= FORM YANG DIPERBARUI (GUNAKAN INI) ================= --}}
            {{-- Mengubah method menjadi POST dan action ke route baru --}}
            {{-- File: resources/views/frontend/beranda.blade.php --}}

<form class="search-form" action="{{ route('frontend.kabin.search') }}" method="POST">
    @csrf
    
    {{-- Input untuk province --}}
    <div class="form-group">
        <label for="filter-province">Provinsi:</label>
        <select id="filter-province" name="province">
            <option value="">Semua Provinsi</option>
            @isset($provinces)
                @foreach($provinces as $prov)
                    <option value="{{ $prov->province }}">{{ $prov->province }}</option>
                @endforeach
            @endisset
        </select>
    </div>

    {{-- Input untuk check_in_date, check_out_date, guests --}}
    <div class="form-group">
        <label for="check_in_date">Check In</label>
        <input type="date" id="check_in_date" name="check_in_date">
    </div>
    <div class="form-group">
        <label for="check_out_date">Check Out</label>
        <input type="date" id="check_out_date" name="check_out_date">
    </div>
    <div class="form-group">
        <label for="guests">Guests</label>
        <select id="guests" name="guests">
            <option value="">Pilih Jumlah Tamu</option>
            <option value="1">1 Tamu</option>
            <option value="2">2 Tamu</option>
            <option value="3">3 Tamu</option>
            <option value="4">4+ Tamu</option>
        </select>
    </div>

    <button type="submit" class="btn-search">Search</button>
</form>
            {{-- ================= AKHIR FORM YANG DIPERBARUI ================= --}}

        </div>
    </div>
</section>

    <section class="cabin-types-section">
        <div class="container">
            <div class="cabin-types-tabs">
                <span class="tab-link active" data-tab="standard">Standard</span>
                <span class="tab-link" data-tab="deluxe">Deluxe</span>
                <span class="tab-link" data-tab="executive">Executive</span>
            </div>

            <div id="standard" class="tab-content active">
                <div class="cabin-type-details">
                    <div class="image-container">
                        <img src="{{ asset('images/assets/cabinroom1.jpg') }}" alt="Standard Cabin">
                    </div>
                    <div class="text-container">
                        <h3>Standard Cabin</h3>
                        <p>Our Standard Cabin offers a cozy and comfortable retreat, perfect for solo travelers or couples. Enjoy essential amenities amidst serene natural surroundings.</p>
                        <ul>
                            <li>Comfortable queen-sized bed</li>
                            <li>Private bathroom with hot shower</li>
                            <li>Small kitchenette area</li>
                            <li>Wi-Fi access</li>
                        </ul>
                        <p><strong>Offers:</strong> Free breakfast for two, 10% off on weekday stays.</p>
                    </div>
                </div>
            </div>
            <div id="deluxe" class="tab-content">
                   <div class="cabin-type-details">
                    <div class="image-container">
                        <img src="{{ asset('images/assets/cabinroom2.jpg') }}" alt="Deluxe Cabin">
                    </div>
                    <div class="text-container">
                        <h3>Deluxe Cabin</h3>
                        <p>Experience enhanced comfort and more space in our Deluxe Cabin. Ideal for families or those seeking extra amenities and a touch of luxury.</p>
                        <ul>
                            <li>Spacious king-sized bed & sofa bed</li>
                            <li>En-suite bathroom with premium toiletries</li>
                            <li>Fully equipped kitchenette</li>
                            <li>Private balcony with a view</li>
                            <li>Smart TV and high-speed Wi-Fi</li>
                        </ul>
                           <p><strong>Offers:</strong> Welcome drink & fruit basket, 15% off for stays over 3 nights.</p>
                    </div>
                </div>
            </div>
            <div id="executive" class="tab-content">
                <div class="cabin-type-details">
                    <div class="image-container">
                        <img src="{{ asset('images/assets/cabinroom3.jpg') }}" alt="Executive Cabin">
                    </div>
                    <div class="text-container">
                        <h3>Executive Cabin</h3>
                        <p>Indulge in the ultimate cabin experience with our Executive Cabin. Featuring top-tier amenities, expansive space, and breathtaking views for an unforgettable stay.</p>
                        <ul>
                            <li>Luxurious super king-sized bed</li>
                            <li>Jacuzzi in a large en-suite bathroom</li>
                            <li>Gourmet kitchen with modern appliances</li>
                            <li>Expansive private deck with outdoor seating</li>
                            <li>Personalized concierge service</li>
                        </ul>
                        <p><strong>Offers:</strong> Complimentary minibar, private bonfire setup, 20% off spa services.</p>
                    </div>
                </div>
            </div>

            <div class="see-more-button-container">
                <a href="{{ route('frontend.kabin.index') }}" class="btn-see-more">Explore All Cabins</a>
            </div>
        </div>
    </section>

    <section class="find-near-you-section">
        <div class="container">
            <h2>Find CABINSKUY Near You</h2>
            <div class="cabin-cards-container">
                <div class="cabin-card">
                    <img src="{{ asset('images/assets/cabinimg1.jpg') }}" alt="Cabinskuy Bandung">
                    <div class="cabin-card-content">
                        <h3>Cabinskuy, Bandung</h3>
                        <p>Escape to the cool highlands of Bandung. Our cabins offer a perfect blend of modern comfort and natural beauty, ideal for a refreshing getaway.</p>
                    </div>
                </div>
                <div class="cabin-card">
                    <img src="{{ asset('images/assets/cabinimg2.jpg') }}" alt="Cabinskuy Bogor">
                    <div class="cabin-card-content">
                        <h3>Cabinskuy, Bogor</h3>
                        <p>Discover tranquility just a short drive from the city. Our Bogor cabins are surrounded by lush greenery, offering a peaceful retreat.</p>
                    </div>
                </div>
                <div class="cabin-card">
                    <img src="{{ asset('images/assets/cabinimg3.jpg') }}" alt="Cabinskuy Bali">
                    <div class="cabin-card-content">
                        <h3>Cabinskuy, Bali</h3>
                        <p>Immerse yourself in the magical island atmosphere of Bali. Our unique cabins provide an exotic and serene base for your island adventures.</p>
                    </div>
                </div>
            </div>
            <div class="see-more-button-container">
                <a href="{{ route('frontend.kabin.index') }}" class="btn-see-more">See More Locations</a>
            </div>
        </div>
    </section>

    <section class="how-to-book-section">
        <div class="container">
            <h2>How to Book Your Getaway</h2>
            <div class="booking-steps-container">
                <div class="booking-step">
                    <div class="step-icon-placeholder">1</div>
                    <h3>Select Your Favorite Cabin</h3>
                    <p>Browse our diverse collection and choose the cabin that suits your style and needs.</p>
                </div>
                <div class="booking-step">
                    <div class="step-icon-placeholder">2</div>
                    <h3>Book and Pay</h3>
                    <p>Secure your dates with our easy and secure online payment system.</p>
                </div>
                <div class="booking-step">
                    <div class="step-icon-placeholder">3</div>
                    <h3>Verify Your ID</h3>
                    <p>Complete a simple verification process for a smooth check-in experience.</p>
                </div>
                <div class="booking-step">
                    <div class="step-icon-placeholder">4</div>
                    <h3>Check-in and Enjoy!</h3>
                    <p>Arrive at your cabin, check-in seamlessly, and start your unforgettable getaway.</p>
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

    provinceSelect.addEventListener('change', function() {
        const province = this.value;
        
        // Kosongkan dan nonaktifkan dropdown kabupaten/kota saat mengambil data
        regencySelect.innerHTML = '<option value="">Memuat...</option>';
        regencySelect.disabled = true;

        if (province) {
            // Lakukan request ke API untuk mendapatkan data kabupaten/kota
            fetch(`/api/regencies?province=${encodeURIComponent(province)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Aktifkan kembali dropdown dan isi dengan data baru
                    regencySelect.disabled = false;
                    regencySelect.innerHTML = '<option value="">Semua Kabupaten/Kota</option>';
                    
                    data.forEach(regency => {
                        const option = document.createElement('option');
                        option.value = regency.regency;
                        option.textContent = regency.regency;
                        regencySelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching regencies:', error);
                    // Handle error, misalnya tampilkan pesan
                    regencySelect.innerHTML = '<option value="">Gagal memuat data</option>';
                });
        } else {
            // Jika "Semua Provinsi" dipilih, reset dropdown kabupaten/kota
            regencySelect.innerHTML = '<option value="">Semua Kabupaten/Kota</option>';
            regencySelect.disabled = false;
        }
    });

    // Kode JavaScript Anda yang lain (untuk tanggal, dll.)
    const today = new Date().toISOString().split('T')[0];
    const check_in_dateInput = document.getElementById('check_in_date');
    const check_out_dateInput = document.getElementById('check_out_date');
    
    if(check_in_dateInput) {
        check_in_dateInput.setAttribute('min', today);
    }
    if(check_out_dateInput) {
        check_out_dateInput.setAttribute('min', today);
    }
    if(check_in_dateInput && check_out_dateInput) {
        check_in_dateInput.addEventListener('change', function() {
            check_out_dateInput.setAttribute('min', this.value);
            if (check_out_dateInput.value < this.value) {
                check_out_dateInput.value = this.value;
            }
        });
    }
});
</script>
@endpush