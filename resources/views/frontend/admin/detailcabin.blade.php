@extends('backend.detailcabin_layout')

@section('title', 'Detail Kabin: ' . $cabin->name)

@section('detail_content')
<div class="breadcrumb-nav">
    <a href="{{ route('admin.cabins.index') }}">
        <i class="fas fa-arrow-left"></i>
        Kembali ke Daftar Kabin
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

@php
    $cabinPhotos = $cabin->cabin_photos ?? [];
    // Pastikan $cabinPhotos[0] berisi path file di S3/R2
    $firstCabinPhoto = !empty($cabinPhotos) && isset($cabinPhotos[0]) ? Storage::disk('s3')->url($cabinPhotos[0]) : 'https://placehold.co/180x120/e9ecef/adb5bd?text=No+Image';
@endphp

<div class="cabin-header-card">
    <div class="cabin-header-info">
        <img src="{{ $firstCabinPhoto }}" alt="Foto {{ $cabin->name }}" class="cabin-main-photo">
        
        <div class="cabin-details-text">
            <h1>{{ $cabin->name }}</h1>
            <div class="cabin-location">
                <i class="fas fa-map-marker-alt"></i>
                {{ $cabin->location }} - {{ $cabin->location_address }}
            </div>
            <div class="cabin-meta-tags">
                <span class="meta-tag">
                    <i class="fas fa-door-open"></i>
                    {{ $cabin->rooms->count() }} Ruangan
                </span>
                <span class="meta-tag">
                    <i class="fas fa-calendar-check"></i>
                    {{ $cabin->rooms->where('status', true)->count() }} Tersedia
                </span>
                @if($cabin->created_at)
                <span class="meta-tag">
                    <i class="fas fa-clock"></i>
                    Dibuat {{ $cabin->created_at->format('M Y') }}
                </span>
                @endif
            </div>
        </div>
        <a href="{{ route('admin.cabins.edit', $cabin->id_cabin) }}" class="btn-action btn-edit"><i class="fas fa-edit"></i> Edit</a>
    </div>
</div>

<div class="content-section">
    <div class="section-header">
        <h2 class="section-title">
            <i class="fas fa-bed"></i>
            Daftar Ruangan
        </h2>
        <a href="{{ route('admin.cabins.rooms.create', $cabin->id_cabin) }}" class="btn-primary-custom">
            <i class="fas fa-plus"></i>
            Tambah Ruangan
        </a>
    </div>
    
    @if($cabin->rooms->count() > 0)
        <div class="rooms-grid">
            @foreach ($cabin->rooms as $room)
                @php
                    $roomPhotos = $room->room_photos ?? [];
                    // Pastikan $roomPhotos[0] berisi path file di S3/R2
                    $photoUrl = !empty($roomPhotos) && isset($roomPhotos[0]) ? Storage::disk('s3')->url($roomPhotos[0]) : 'https://placehold.co/320x200/e9ecef/adb5bd?text=No+Image';
                @endphp
                
                <div class="room-card">
                    <div class="room-image-container">
                        <img src="{{ $photoUrl }}" alt="Foto {{ $room->typeroom }}" class="room-image">
                        
                        <div class="room-status-badge {{ $room->status ? 'status-available' : 'status-unavailable' }}">
                            @if($room->status)
                                <i class="fas fa-check-circle"></i> Tersedia
                            @else
                                <i class="fas fa-times-circle"></i> Tidak Tersedia
                            @endif
                        </div>
                    </div>
                    
                    <div class="room-content">
                        <div class="room-id">ID: {{ $room->id_room }}</div>
                        <h3 class="room-type">{{ $room->typeroom }}</h3>
                        
                        <div class="room-price">
                            Rp {{ number_format($room->price, 0, ',', '.') }}
                            <span class="room-price-unit">/malam</span>
                        </div>
                        
                        <div class="room-actions">
                            <a href="{{ route('admin.rooms.edit', $room->id_room) }}" class="btn-action btn-edit">
                                <i class="fas fa-edit"></i>
                                Edit
                            </a>
                            <form action="{{ route('admin.rooms.destroy', $room->id_room) }}" 
                                    method="POST" 
                                    onsubmit="return confirm('Yakin ingin menghapus ruangan ini?');"
                                    style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action btn-delete">
                                    <i class="fas fa-trash"></i>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-bed"></i>
            </div>
            <h3>Belum Ada Ruangan</h3>
            <p>Kabin ini belum memiliki ruangan. Tambahkan ruangan pertama untuk memulai.</p>
            <a href="{{ route('admin.cabins.rooms.create', $cabin->id_cabin) }}" class="btn-primary-custom">
                <i class="fas fa-plus"></i>
                Tambah Ruangan Pertama
            </a>
        </div>
    @endif
</div>
@endsection