@extends('backend.user_layout')

{{-- Mengatur judul spesifik untuk halaman ini --}}
@section('title', 'Daftar Kabin - Cabinskuy')

@push('styles')
{{-- Menambahkan beberapa style untuk tampilan yang lebih baik --}}
<style>
    
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
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 2.5rem;
    }
    .cabin-list-item {
        background-color: #fff;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.07);
        transition: transform 0.3s, box-shadow 0.3s;
        display: flex;
        flex-direction: column;
    }
    .cabin-list-item:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.1);
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
        height: 220px;
        overflow: hidden;
    }
    .cabin-list-item .img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .cabin-list-item:hover .img-container img {
        transform: scale(1.05);
    }
    .cabin-list-item .location-tag {
        position: absolute;
        top: 15px;
        left: 15px;
        background-color: rgba(0, 0, 0, 0.6);
        color: #fff;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }
    .cabin-list-item-content {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    .cabin-list-item-content h3 {
        font-size: 1.4rem;
        font-weight: 600;
        margin: 0 0 0.5rem 0;
        color: #223324;
    }
    .cabin-meta {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .cabin-meta span {
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .cabin-price {
        font-size: 1.3rem;
        font-weight: 700;
        color: #229954;
        margin-top: auto;
        padding-top: 1rem;
        margin-bottom: 1rem;
    }
    .cabin-price span {
        font-weight: 400;
        font-size: 0.9rem;
        color: #6c757d;
    }
    .btn-view-details {
        display: block;
        background-color: #229954;
        color: #fff !important;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        text-align: center;
        font-weight: 600;
        text-decoration: none;
        transition: background-color 0.3s;
    }
    .btn-view-details:hover {
        background-color: #1c7d43;
    }

    .no-results {
        grid-column: 1 / -1;
        text-align: center;
        padding: 4rem 2rem;
        background-color: #fff;
        border: 2px dashed #e9f5e9;
        border-radius: 12px;
        color: #6c757d;
    }
    .no-results-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
    .pagination-container {
        margin-top: 3rem;
    }
</style>
@endpush

{{-- Memulai bagian konten yang akan dimasukkan ke @yield('content') --}}
@section('content')

<section class="page-header">
    <div class="container">
        <h1>Temukan Kabin Impian Anda</h1>
        <p>Jelajahi berbagai pilihan kabin di lokasi terbaik.</p>
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
            <noscript>
                <button type="submit" class="btn">Filter</button>
            </noscript>
        </form>
    </section>

    <section class="cabin-list-container">
        @forelse ($cabins as $cabin)
            @php
                $cheapestRoom = $cabin->rooms->sortBy('price')->first();
                $mainPhoto = (is_array($cabin->cabin_photos) && !empty($cabin->cabin_photos[0]))
                             ? asset('storage/' . $cabin->cabin_photos[0])
                             : 'https://placehold.co/400x250/008272/FFFFFF?text=Cabinskuy';
            @endphp
            <article class="cabin-list-item">
                <a href="{{ route('frontend.kabin.show', $cabin) }}">
                    <div class="img-container">
                        <img src="{{ $mainPhoto }}" alt="{{ $cabin->name }}" onerror="this.onerror=null;this.src='https://placehold.co/400x250/dc3545/FFFFFF?text=Image+Error';">
                        <div class="location-tag">
                            <i class="fas fa-map-marker-alt"></i>&nbsp;{{ $cabin->regency }}, {{ $cabin->province }}
                        </div>
                    </div>
                    <div class="cabin-list-item-content">
                        <h3>{{ $cabin->name }}</h3>
                        
                        @if($cheapestRoom)
                            <div class="cabin-meta">
                                <span><i class="fas fa-home"></i> {{ $cheapestRoom->typeroom }}</span>
                                <span><i class="fas fa-users"></i> {{ $cheapestRoom->max_guests }} Tamu</span>
                            </div>
                            <p class="cabin-description">{{ \Illuminate\Support\Str::limit($cabin->description, 100, '...') }}</p>
                            <p class="cabin-price">
                                Rp {{ number_format($cheapestRoom->price, 0, ',', '.') }}
                                <span>/ malam</span>
                            </p>
                        @else
                            <p class="cabin-description">{{ \Illuminate\Support\Str::limit($cabin->description, 100, '...') }}</p>
                            <p class="cabin-price">Harga tidak tersedia</p>
                        @endif
                        
                        <div class="btn-view-details">
                            Lihat Detail & Pesan
                        </div>
                    </div>
                </a>
            </article>
        @empty
            <div class="no-results">
                <div class="no-results-icon">☹️</div>
                <h4>Tidak Ada Kabin Ditemukan</h4>
                <p>Maaf, kami tidak dapat menemukan kabin yang sesuai dengan kriteria filter Anda. Coba ubah filter Anda.</p>
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
    document.getElementById('filter-province').addEventListener('change', function() {
        const province = this.value;
        const regencySelect = document.getElementById('filter-regency');
        
        // Clear current regency options
        regencySelect.innerHTML = '<option value="">Semua Kabupaten/Kota</option>';

        if (province) {
            // Make an AJAX request to get regencies for the selected province
            // This is a simplified example; you might need to create a dedicated API endpoint
            // for fetching regencies in a real application.
            fetch(`/api/regencies?province=${province}`) // Assuming you have an API route like this
                .then(response => response.json())
                .then(data => {
                    data.forEach(regency => {
                        const option = document.createElement('option');
                        option.value = regency.regency;
                        option.textContent = regency.regency;
                        regencySelect.appendChild(option);
                    });
                    // Submit the form after regencies are loaded if the regency was previously selected
                    // This is to maintain the state of the regency filter after province changes
                    const currentRegency = '{{ request('regency') }}';
                    if (currentRegency && Array.from(regencySelect.options).some(option => option.value === currentRegency)) {
                        regencySelect.value = currentRegency;
                    }
                    document.getElementById('filter-form').submit();
                })
                .catch(error => console.error('Error fetching regencies:', error));
        } else {
            // If "Semua Provinsi" is selected, submit the form to clear regency filter
            document.getElementById('filter-form').submit();
        }
    });

    // To ensure the form submits when other filters change, keep the existing onchange
    document.getElementById('filter-type').addEventListener('change', function() {
        document.getElementById('filter-form').submit();
    });
</script>
@endpush

@endsection