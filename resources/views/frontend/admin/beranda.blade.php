@extends('backend.admin_layout')

@section('title', 'Admin Dashboard')

@push('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background-color: #fff;
        padding: 25px;
        border-radius: 8px;
        box-shadow: var(--shadow);
        display: flex;
        align-items: center;
        gap: 20px;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.07);
    }

    .stat-card .icon {
        font-size: 2.5em;
        color: var(--primary-color);
        width: 60px;
        height: 60px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: var(--light-green-bg);
        border-radius: 50%;
    }

    .stat-card .info h3 {
        margin: 0 0 5px 0;
        font-size: 1.8em;
        color: var(--text-dark);
    }

    .stat-card .info p {
        margin: 0;
        color: #666;
        font-weight: 500;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
        align-items: flex-start;
    }

    .dashboard-section {
        background: #fff;
        padding: 25px;
        border-radius: 8px;
        box-shadow: var(--shadow);
    }

    .dashboard-section h2 {
        margin-top: 0;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border-color);
        font-size: 1.2em;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .content-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.95em;
    }

    .content-table thead tr {
        background-color: var(--primary-color);
        color: #ffffff;
        text-align: left;
    }

    .content-table th,
    .content-table td {
        padding: 12px 15px;
    }

    .content-table tbody tr {
        border-bottom: 1px solid #f0f0f0;
    }

    .content-table tbody tr:nth-of-type(even) {
        background-color: #f9f9f9;
    }

    .content-table tbody tr:last-of-type {
        border-bottom: 2px solid var(--primary-color);
    }

    .content-table tbody tr:hover {
        background-color: var(--light-green-bg);
    }

    .status {
        padding: 5px 10px;
        border-radius: 5px;
        color: white;
        font-size: 0.8em;
        font-weight: bold;
        text-transform: uppercase;
    }

    .status.confirmed {
        background-color: #27ae60;
    }

    .status.pending {
        background-color: #f39c12;
    }

    .status.cancelled {
        background-color: #e74c3c;
    }
    .status.rejected {
        background-color: #e74c3c;
    }

    .actions-container .action-card {
        display: flex;
        align-items: center;
        gap: 15px;
        background: #fdfdfd;
        border: 1px solid var(--border-color);
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        transition: background-color 0.3s, border-color 0.3s;
    }

    .actions-container .action-card:hover {
        background-color: #fff;
        border-color: var(--primary-color);
    }

    .actions-container .action-card .icon {
        font-size: 1.5em;
        color: var(--primary-dark);
    }

    .actions-container .action-card h3 {
        margin: 0;
        font-size: 1.1em;
        color: var(--text-dark);
    }

    .actions-container .action-card a {
        margin-left: auto;
        text-decoration: none;
        background-color: var(--primary-color);
        color: white;
        padding: 8px 15px;
        border-radius: 5px;
        font-weight: 500;
        transition: background-color 0.3s;
    }

    .actions-container .action-card a:hover {
        background-color: var(--primary-dark);
    }

    @media (max-width: 992px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .admin-header {
            text-align: center;
        }
    }
</style>
@endpush

@section('admin_content')
<div class="container">

    <section class="admin-header">
        @auth
        <h1>Selamat Datang, {{ $user->name }}!</h1>
        <p>Kelola semua aspek situs Cabinskuy dari satu tempat yang terpusat.</p>
        @endauth
    </section>

    <section class="stats-grid">
        <article class="stat-card">
            <div class="icon"><i class="fas fa-home"></i></div>
            <div class="info">
                <h3>{{ $totalCabins }}</h3>
                <p>Total Kabin</p>
            </div>
        </article>
        <article class="stat-card">
            <div class="icon"><i class="fas fa-home"></i></div>
            <div class="info">
                <h3>{{ $totalCabinRooms }}</h3>
                <p>Total Cabin Rooms</p>
            </div>
        </article>
        <article class="stat-card">
            <div class="icon"><i class="fas fa-calendar-check"></i></div>
            <div class="info">
                <h3>{{ $totalBookings }}</h3>
                <p>Total Pemesanan</p>
            </div>
        </article>
        <article class="stat-card">
            <div class="icon"><i class="fas fa-users"></i></div>
            <div class="info">
                <h3>{{ $totalUsers }}</h3>
                <p>Total Pengguna</p>
            </div>
        </article>
        <article class="stat-card">
            <div class="icon"><i class="fas fa-wallet"></i></div>
            <div class="info">
                <h3>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                <p>Total Pendapatan</p>
            </div>
        </article>
    </section>

    <div class="dashboard-grid">
        <section class="dashboard-section">
            <h2><i class="fas fa-clock"></i> Pemesanan Terbaru</h2>
            <div class="table-wrapper">
                <table class="content-table">
                    <thead>
                        <tr>
                            <th>Nama Kabin</th>
                            <th>Tipe Kamar</th>
                            <th>Nama Tamu</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentBookings as $booking)
                        <tr>
                            <td>{{ $booking->room?->cabin?->name ?? 'Kabin Dihapus' }}</td>
                            <td>{{ $booking->room?->typeroom ?? 'Kabin Dihapus' }}</td>
                            <td>{{ $booking->contact_name ?? 'Pengguna Dihapus' }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') }}</td>
                            <td>
                                <span class="status {{ strtolower($booking->status) }}">{{ ucfirst($booking->status) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 20px;">Belum ada data pemesanan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        

        <section class="dashboard-section">
            <h2>Aksi Cepat</h2>
            <div class="actions-container">
                   <article class="action-card">
                       <div class="icon"><i class="fas fa-plus-circle"></i></div>
                       <h3>Tambah Kabin Baru</h3>
                       <a href="{{ route('admin.cabins.create') }}" title="Buat daftar kabin baru">Tambah</a>
                   </article>

                   <article class="action-card">
                       <div class="icon"><i class="fas fa-edit"></i></div>
                       <h3>Kelola Kabin</h3>
                       <a href="{{ route('admin.cabins.index') }}" title="Edit atau hapus kabin">Kelola</a>
                   </article>

                   <article class="action-card">
                       <div class="icon"><i class="fas fa-book-open"></i></div>
                       <h3>Kelola Booking</h3>
                       <a href="{{ route('admin.bookings.index') }}" title="Lihat semua pemesanan">Lihat</a>
                   </article>

                   <article class="action-card">
                    <div class="icon"><i class="fas fa-file-invoice-dollar"></i></div>
                    <h3>Laporan Keuangan</h3>
                    <a href="{{ route('admin.reports.financial') }}" title="Lihat laporan keuangan">Lihat</a>
                </article>

                <article class="action-card">
                    <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                    <h3>Laporan Booking</h3>
                    <a href="{{ route('admin.reports.booking') }}" title="Lihat laporan booking">Lihat</a>
                </article>
                   
            </div>
        </section>
    </div>
</div>
@endsection