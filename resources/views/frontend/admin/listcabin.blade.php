@extends('backend.admin_layout')

@section('title', 'Kelola Kabin')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    :root {
        --primary-green: #008272;
        --light-green: #E6F3F2;
        --dark-text: #333;
        --light-text: #6c757d;
        --border-color: #E0E0E0;
        --background-color: #F9FAFB;
        --white: #FFFFFF;
        --danger: #e74c3c;
        --warning: #f1c40f;
        --info: #3498db;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--background-color);
        color: var(--dark-text);
    }

    .cabin-management-container {
        padding: 2rem 1.5rem;
        max-width: 1200px;
        margin: auto;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .page-header h1 {
        font-size: 1.75rem;
        font-weight: 600;
        color: var(--primary-green);
        margin: 0;
    }

    .header-buttons .btn {
        text-decoration: none;
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
        font-weight: 500;
        border: 1px solid var(--primary-green);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .header-buttons .btn-add-new {
        background-color: var(--primary-green);
        color: var(--white);
    }
    
    .header-buttons .btn-add-new:hover {
        opacity: 0.9;
    }
    
    .header-buttons .btn-dashboard {
        background-color: var(--white);
        color: var(--primary-green);
    }

    .header-buttons .btn-dashboard:hover {
        background-color: var(--light-green);
    }

    .search-form {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 2rem;
    }
    .search-input {
        flex-grow: 1;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 1rem;
    }
    .search-input:focus {
        outline: none;
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px var(--light-green);
    }
    .search-button {
        padding: 0.75rem 1.5rem;
        background-color: var(--primary-green);
        color: var(--white);
        border: none;
        border-radius: 8px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 500;
    }
    
    .cabin-list-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .cabin-list-item {
        display: flex;
        background-color: var(--white);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        flex-direction: column;
    }

    @media (min-width: 992px) {
        .cabin-list-item {
            flex-direction: row;
        }
    }

    .cabin-list-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    }

    .cabin-item-gallery {
        flex-shrink: 0;
        width: 100%;
        padding: 1rem;
        background-color: #f8f9fa;
    }
     @media (min-width: 992px) {
        .cabin-item-gallery {
            width: 280px;
        }
    }
    .main-image-container {
        width: 100%;
        height: 150px;
        border-radius: 8px;
        overflow: hidden;
        background-color: #e9ecef;
    }
    .main-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .thumbnail-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.5rem;
        margin-top: 0.5rem;
    }
    .thumbnail {
        width: 100%;
        height: 50px;
        object-fit: cover;
        cursor: pointer;
        border-radius: 4px;
        border: 2px solid transparent;
        transition: border-color 0.3s;
    }
    .thumbnail:hover {
        border-color: var(--primary-green);
    }

    .cabin-item-rooms-section {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #f0f0f0;
    }
    .cabin-item-rooms-section strong {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        font-size: 0.9em;
        color: var(--dark-text);
    }
    .no-rooms-text {
        font-size: 0.85em;
        color: var(--light-text);
    }
    .more-photos-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--light-green);
        color: var(--primary-green);
        font-weight: 600;
        font-size: 0.8rem;
        border-radius: 4px;
        text-decoration: none;
        height: 50px;
        text-align: center;
    }

    .cabin-item-content {
        flex-grow: 1;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
    }
    .cabin-item-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.75rem;
    }
    .cabin-item-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0;
    }
    .cabin-item-id {
        font-size: 0.85rem;
        color: var(--light-text);
    }
    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #fff;
        text-transform: uppercase;
    }
    .status-available { background-color: var(--primary-green); }
    .status-unavailable { background-color: var(--light-text); }
    .cabin-item-meta {
        font-size: 0.9rem;
        color: var(--light-text);
        margin-bottom: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .meta-item { display: flex; align-items: center; gap: 0.5rem; }
    .meta-item i { color: var(--primary-green); }
    .cabin-item-description {
        font-size: 0.9rem;
        color: var(--light-text);
        margin-bottom: 1rem;
    }
    
    .cabin-item-rooms {
        margin-top: auto;
        padding-top: 1rem;
        border-top: 1px solid var(--border-color);
    }
    .cabin-item-rooms h4 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--dark-text);
        margin-bottom: 0.75rem;
    }
    .room-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .room-detail {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.9rem;
        padding: 0.25rem 0;
    }
    .room-type {
        color: var(--dark-text);
    }
    .room-price {
        font-weight: 600;
        color: var(--primary-green);
    }
    .no-rooms {
        font-style: italic;
        color: var(--light-text);
        background-color: var(--background-color);
        padding: 0.75rem;
        border-radius: 8px;
        text-align: center;
        font-size: 0.9rem;
    }
    
    .cabin-item-actions {
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 0.5rem;
        padding: 1.5rem;
        background-color: #f8f9fa;
        border-left: 1px solid var(--border-color);
    }
    .btn-action {
        display: flex; align-items: center; justify-content: center;
        padding: 0.5rem 1rem; border-radius: 8px; text-decoration: none;
        color: white; font-size: 0.8rem; font-weight: 500;
        border: none; cursor: pointer; transition: all 0.2s ease;
    }
    .btn-action i { margin-right: 8px; }
    .btn-manage { background-color: var(--info); }
    .btn-manage:hover { background-color: #2980b9; }
    .btn-edit { background-color: var(--warning); }
    .btn-edit:hover { background-color: #d4ac0d; }
    .btn-delete { background-color: var(--danger); }
    .btn-delete:hover { background-color: #c0392b; }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background-color: var(--white);
        border: 2px dashed var(--border-color);
        border-radius: 12px;
    }
    .empty-state h3 { font-weight: 600; color: var(--dark-text); }
    .empty-state p { color: var(--light-text); margin-bottom: 1.5rem; }

</style>
@endpush

@section('admin_content')
<div class="cabin-management-container">

    <div class="page-header">
        <div>
            <h1>Daftar Properti Kabin</h1>
            <p style="color: var(--light-text); margin-top: 0.25rem;">Kelola semua properti kabin yang tersedia.</p>
        </div>
        <div class="header-buttons">
            <a href="{{ url('/admin/dashboard') }}" class="btn btn-dashboard">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>
            <a href="{{ route('admin.cabins.create') }}" class="btn btn-add-new">
                <i class="fas fa-plus"></i>
                Add New Cabin
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success" style="background-color: var(--light-green); border-left: 5px solid var(--primary-green); color: var(--dark-text); padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.cabins.index') }}" method="GET" class="search-form">
        <input type="text" name="search" class="search-input" placeholder="Cari berdasarkan nama kabin atau lokasi..." value="{{ request('search') }}">
        <button type="submit" class="search-button"><i class="fas fa-search"></i> Cari</button>
    </form>
    
    <div class="cabin-list-container">
        @forelse ($cabins as $cabin)
            @php
                // Ensure cabinPhotos is an array of strings, even if it's double-encoded JSON
                $cabinPhotos = [];
                $rawCabinPhotos = $cabin->cabin_photos;
                if (is_string($rawCabinPhotos)) {
                    // Try to decode once
                    $decodedOnce = json_decode($rawCabinPhotos, true);
                    if (is_array($decodedOnce)) {
                        // Check if it's an array where the first element is still a JSON string (double-encoded)
                        if (!empty($decodedOnce) && is_string($decodedOnce[0])) {
                            $decodedTwice = json_decode($decodedOnce[0], true);
                            if (is_array($decodedTwice)) {
                                $cabinPhotos = $decodedTwice;
                            }
                        } else {
                            $cabinPhotos = $decodedOnce;
                        }
                    }
                } elseif (is_array($rawCabinPhotos)) {
                    // If it's already an array (due to model casting), use it directly
                    $cabinPhotos = $rawCabinPhotos;
                }
                
                // Similar logic for room photos
                $allRoomPhotos = [];
                foreach($cabin->rooms as $room) {
                    $roomPhotos = [];
                    $rawRoomPhotos = $room->room_photos;
                    if (is_string($rawRoomPhotos)) {
                        $decodedOnceRoom = json_decode($rawRoomPhotos, true);
                        if (is_array($decodedOnceRoom)) {
                            if (!empty($decodedOnceRoom) && is_string($decodedOnceRoom[0])) {
                                $decodedTwiceRoom = json_decode($decodedOnceRoom[0], true);
                                if (is_array($decodedTwiceRoom)) {
                                    $roomPhotos = $decodedTwiceRoom;
                                }
                            } else {
                                $roomPhotos = $decodedOnceRoom;
                            }
                        }
                    } elseif (is_array($rawRoomPhotos)) {
                        $roomPhotos = $rawRoomPhotos;
                    }
                    $allRoomPhotos = array_merge($allRoomPhotos, $roomPhotos);
                }

                $defaultPlaceholder = asset('images/cabinskuy_placeholder.jpg'); // Your placeholder
            @endphp

            <div class="cabin-list-item">
                <div class="cabin-item-gallery">
                    <div class="main-image-container" id="main-image-{{ $cabin->id_cabin }}">
                        {{-- Directly access the first photo path and ensure it's a string --}}
                        <img src="{{ !empty($cabinPhotos) && is_string($cabinPhotos[0]) ? asset('storage/' . str_replace('\\', '/', $cabinPhotos[0])) : $defaultPlaceholder }}" alt="Foto {{ $cabin->name }}">
                    </div>
                    @if (!empty($cabinPhotos) && count($cabinPhotos) > 1)
                        <div class="thumbnail-container">
                            @foreach (array_slice($cabinPhotos, 0, 4) as $photo)
                                {{-- Ensure $photo is a string before using str_replace --}}
                                @if (is_string($photo))
                                    <img src="{{ asset('storage/' . str_replace('\\', '/', $photo)) }}" class="thumbnail" onclick="changeImage('{{ $cabin->id_cabin }}', '{{ asset('storage/' . str_replace('\\', '/', $photo)) }}')">
                                @else
                                    <img src="{{ $defaultPlaceholder }}" class="thumbnail">
                                @endif
                            @endforeach
                        </div>
                    @endif
                    <div class="cabin-item-rooms-section">
                        <strong>Foto Ruangan:</strong>
                        <div class="thumbnail-container">
                            @forelse (array_slice($allRoomPhotos, 0, 3) as $roomPhoto)
                                @if (is_string($roomPhoto))
                                    <img src="{{ asset('storage/' . str_replace('\\', '/', $roomPhoto)) }}" class="thumbnail">
                                @else
                                    <img src="{{ $defaultPlaceholder }}" class="thumbnail">
                                @endif
                            @empty
                                <span class="no-rooms-text" style="grid-column: 1 / -1;"><img src="{{ $defaultPlaceholder }}" alt="No Room Photo" style="width: 100%; height: 50px; object-fit: cover; border-radius: 4px;"></span>
                            @endforelse
                            @if(!empty($allRoomPhotos) && count($allRoomPhotos) > 3)
                                <a href="{{ route('admin.cabins.show', $cabin->id_cabin) }}" class="more-photos-badge">+{{ count($allRoomPhotos) - 3 }}</a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="cabin-item-content">
                    <div>
                        <div class="cabin-item-header">
                            <div class="cabin-item-title-block">
                                <h3 class="cabin-item-title">{{ $cabin->name }}</h3>
                                <span class="cabin-item-id">ID: {{ $cabin->id_cabin }}</span>
                            </div>
                            <span class="status-badge {{ $cabin->status ? 'status-available' : 'status-unavailable' }}">
                                {{ $cabin->status ? 'Tersedia' : 'Tidak Tersedia' }}
                            </span>
                        </div>

                        <div class="cabin-item-meta">
                            <div class="meta-item">
                                <i class="fas fa-map-marker-alt"></i> 
                                {{ $cabin->regency }}, {{ $cabin->province }}
                            </div>
                            <div class="meta-item"><i class="fas fa-home"></i> {{ $cabin->rooms->count() }} Tipe Ruangan</div>
                        </div>

                        <div class="cabin-item-description">
                           <p>{{ \Illuminate\Support\Str::limit($cabin->description, 150, '...') }}</p>
                        </div>
                    </div>
                    
                    <div class="cabin-item-rooms">
                        <h4>Tipe Ruangan & Harga</h4>
                        <div class="room-list">
                            @forelse($cabin->rooms->take(3) as $room)
                                <div class="room-detail">
                                    <span class="room-type">{{ $room->typeroom }}</span>
                                    <span class="room-price">Rp {{ number_format($room->price, 0, ',', '.') }}</span>
                                </div>
                            @empty
                                <div class="no-rooms">Belum ada tipe ruangan ditambahkan.</div>
                            @endforelse
                            @if ($cabin->rooms->count() > 3)
                                <div class="room-detail"><span class="room-type"><em>... dan lainnya</em></span></div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="cabin-item-actions">
                    <a href="{{ route('admin.cabins.show', $cabin->id_cabin) }}" class="btn-action btn-manage"><i class="fas fa-door-open"></i> Kelola</a>
                    <a href="{{ route('admin.cabins.edit', $cabin->id_cabin) }}" class="btn-action btn-edit"><i class="fas fa-edit"></i> Edit</a>
                    <form action="{{ route('admin.cabins.destroy', $cabin->id_cabin) }}" method="POST" onsubmit="return confirm('Yakin hapus kabin ini beserta semua ruangannya?');" style="margin: 0; width: 100%;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action btn-delete" style="width: 100%;"><i class="fas fa-trash"></i> Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <h3>Anda Belum Memiliki Properti Kabin</h3>
                <p>Mari mulai dengan menambahkan properti kabin pertama Anda ke dalam sistem.</p>
                <a href="{{ route('admin.cabins.create') }}" class="btn btn-add-new">
                    <i class="fas fa-plus"></i> Tambah Kabin Sekarang
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
    function changeImage(cabinId, newSrc) {
        const mainImageContainer = document.getElementById('main-image-' + cabinId);
        if (mainImageContainer) {
            mainImageContainer.querySelector('img').src = newSrc;
        }
    }   
</script>
@endpush