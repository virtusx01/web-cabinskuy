@extends('backend.admin_layout') {{-- Asumsi ini adalah layout admin atau layout dasar --}}

@section('title', $title)

@push('styles')
<style>
    .admin-page-bg {
        background-color: #f4f7f6;
        padding: 20px;
        min-height: 100vh;
    }
    .page-title {
        font-size: 2.2em;
        color: #223324;
        margin-bottom: 25px;
    }
    .card {
        background: #fff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    .table th, .table td {
        padding: 12px 15px;
        border-bottom: 1px solid #e0e0e0;
        text-align: left;
        vertical-align: middle;
    }
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #555;
    }
    .table tbody tr:hover {
        background-color: #f0f0f0;
    }
    .badge {
        display: inline-block;
        padding: 0.35em 0.65em;
        font-size: 0.75em;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25rem;
        color: #fff;
    }
    .badge-warning { background-color: #ffc107; color: #212529; }
    .badge-success { background-color: #28a745; }
    .badge-danger { background-color: #dc3545; }
    .badge-secondary { background-color: #6c757d; }
    .badge-info { background-color: #17a2b8; }

    .btn {
        display: inline-block;
        font-weight: 400;
        color: #212529;
        text-align: center;
        vertical-align: middle;
        cursor: pointer;
        user-select: none;
        background-color: transparent;
        border: 1px solid transparent;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        border-radius: 0.25rem;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .btn-info { color: #fff; background-color: #17a2b8; border-color: #17a2b8; }
    .btn-info:hover { background-color: #138496; border-color: #117a8b; }
    .btn-danger { color: #fff; background-color: #dc3545; border-color: #dc3545; }
    .btn-danger:hover { background-color: #c82333; border-color: #bd2130; }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
        border-radius: 0.2rem;
    }

    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }
    .pagination {
        display: flex;
        padding-left: 0;
        list-style: none;
        border-radius: 0.25rem;
    }
    .page-item .page-link {
        position: relative;
        display: block;
        padding: 0.5rem 0.75rem;
        margin-left: -1px;
        line-height: 1.25;
        color: #007bff;
        background-color: #fff;
        border: 1px solid #dee2e6;
        text-decoration: none;
    }
    .page-item.active .page-link {
        z-index: 3;
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
    }
    .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }

    .filter-form {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
        flex-wrap: wrap;
        align-items: flex-end;
    }
    .filter-form label {
        font-weight: 600;
        margin-bottom: 5px;
        display: block;
    }
    .filter-form select,
    .filter-form input[type="text"],
    .filter-form input[type="number"] {
        padding: 8px 12px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 1em;
        width: 100%; /* Default width */
        max-width: 200px; /* Max width for inputs */
    }
    .filter-form button {
        padding: 8px 15px;
        border: none;
        border-radius: 5px;
        background-color: #229954;
        color: white;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    .filter-form button:hover {
        background-color: #1c7d43;
    }
    .form-group {
        flex: 1;
        min-width: 150px; /* Minimum width for each filter group */
    }
</style>
@endpush

@section('admin_content') {{-- CHANGE THIS LINE --}}
<div class="admin-page-bg">
    <div class="container">
        <h1 class="page-title">Manajemen Booking</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <h3>Filter Booking</h3>
            <form action="{{ route('admin.bookings.index') }}" method="GET" class="filter-form">
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select name="status" id="status">
                        <option value="all" {{ $currentStatus == 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="pending" {{ $currentStatus == 'pending' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                        <option value="confirmed" {{ $currentStatus == 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                        <option value="completed" {{ $currentStatus == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="rejected" {{ $currentStatus == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        <option value="cancelled" {{ $currentStatus == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="booking_id">ID Booking:</label>
                    <input type="number" name="booking_id" id="booking_id" value="{{ $bookingIdSearch }}" placeholder="Cari ID Booking">
                </div>
                <div class="form-group">
                    <label for="search">Cari Kontak:</label>
                    <input type="text" name="search" id="search" value="{{ $searchQuery }}" placeholder="Nama, Email, atau Telepon">
                </div>
                <div class="form-group">
                    <button type="submit">Filter</button>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary" style="background-color: #6c757d; color: white; text-decoration: none;">Reset</a>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID Booking</th>
                            <th>User</th>
                            <th>Kabin / Kamar</th>
                            <th>Check-in / Check-out</th>
                            <th>Total Tamu</th>
                            <th>Biaya</th>
                            <th>Status</th>
                            <th>Tanggal Booking</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                        <tr>
                            <td>{{ $booking->id_booking }}</td>
                            <td>
                                {{ $booking->contact_name }} <br>
                                <small>{{ $booking->contact_email }}</small>
                            </td>
                            <td>
                                {{ $booking->cabin->name ?? 'N/A' }} <br>
                                <small>{{ $booking->room->typeroom ?? 'N/A' }}</small>
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }} <br>
                                {{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') }}
                            </td>
                            <td>{{ $booking->total_guests }}</td>
                            <td>{{ $booking->formatted_total_price }}</td>
                            <td><span class="badge {{ $booking->status_badge_class }}">{{ $booking->status_label }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.bookings.show', $booking->id_booking) }}" class="btn btn-info btn-sm">Detail</a>
                                {{-- PERBAIKAN: HANYA SUPERADMIN YANG BISA MELIHAT DAN MELAKUKAN AKSI HAPUS --}}
                                @if (Auth::check() && Auth::user()->isSuperAdmin())
                                    {{-- Cek status booking untuk tombol delete di halaman index (opsional) --}}
                                    {{-- Misalnya, Anda hanya ingin memungkinkan penghapusan booking yang sudah selesai, ditolak, atau dibatalkan --}}
                                    {{-- Atau Anda ingin memungkinkan penghapusan regardless of status for superadmin --}}
                                    {{-- Untuk tujuan ini, kita akan tampilkan untuk superadmin saja --}}
                                    <form action="{{ route('admin.bookings.destroy', $booking->id_booking) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus booking ini? Tindakan ini tidak bisa dibatalkan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada booking ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-container">
                {{ $bookings->links('pagination::bootstrap-4') }} {{-- Gunakan tema pagination Bootstrap 4 --}}
            </div>
        </div>
    </div>
</div>
@endsection