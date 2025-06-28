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
        grid-template-columns: 2fr 1fr; /* Default untuk desktop */
        gap: 30px;
        align-items: flex-start;
    }

    @media (max-width: 992px) {
        .dashboard-grid {
            grid-template-columns: 1fr; /* Single column for tablets/mobiles */
        }
    }

    .dashboard-section {
        background: #fff;
        padding: 25px;
        border-radius: 8px;
        box-shadow: var(--shadow);
        margin-bottom: 30px; /* Tambahkan margin bawah agar ada jarak antar section di mobile */
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
        background-color: #27ae60; /* Hijau */
    }

    .status.pending {
        background-color: #f39c12; /* Oranye */
    }

    .status.cancelled {
        background-color: #e74c3c; /* Merah */
    }
    .status.rejected {
        background-color: #e74c3c; /* Merah */
    }
    .status.completed {
        background-color: #3498db; /* Biru */
    }
    .status.paid {
        background-color: #27ae60; /* Hijau */
    }
    .status.unpaid {
        background-color: #e74c3c; /* Merah */
    }


    /* CSS untuk scrollable actions */
    .actions-container {
        display: flex; /* Mengubah ini dari grid atau block */
        flex-wrap: nowrap; /* Mencegah item wrap ke baris baru */
        overflow-x: auto; /* Memungkinkan scroll horizontal */
        gap: 15px; /* Jarak antar card */
        padding-bottom: 10px; /* Sedikit padding bawah untuk scrollbar */
        -webkit-overflow-scrolling: touch; /* Untuk smooth scrolling di iOS */
        scrollbar-width: thin; /* Firefox */
        scrollbar-color: var(--primary-color) #f1f1f1; /* Firefox */
    }

    /* Webkit (Chrome, Safari, Edge) scrollbar styles */
    .actions-container::-webkit-scrollbar {
        height: 8px; /* Tinggi scrollbar horizontal */
    }
    .actions-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .actions-container::-webkit-scrollbar-thumb {
        background: var(--primary-color);
        border-radius: 10px;
    }
    .actions-container::-webkit-scrollbar-thumb:hover {
        background: var(--primary-dark);
    }

    .actions-container .action-card {
        flex: 0 0 auto; /* Penting: mencegah card menyusut dan memungkinkan scroll */
        width: 280px; /* Lebar tetap untuk setiap card aksi */
        display: flex;
        align-items: center;
        gap: 15px;
        background: #fdfdfd;
        border: 1px solid var(--border-color);
        padding: 15px;
        border-radius: 8px;
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
        white-space: nowrap; /* Mencegah teks link wrap */
        transition: background-color 0.3s;
    }

    .actions-container .action-card a:hover {
        background-color: var(--primary-dark);
    }

    /* Media query untuk mengubah layout kembali ke grid di layar besar */
    @media (min-width: 768px) {
        .actions-container {
            display: grid; /* Kembali ke grid untuk layar besar */
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); /* Atur kolom sesuai keinginan */
            overflow-x: visible; /* Matikan scroll horizontal */
            padding-bottom: 0;
            gap: 20px; /* Jarak antar card */
        }
        .actions-container .action-card {
            width: auto; /* Biarkan lebar menyesuaikan grid */
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
            <div class="icon"><i class="fas fa-bed"></i></div>
            <div class="info">
                <h3>{{ $totalCabinRooms }}</h3>
                <p>Total Tipe Kamar</p>
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
                <p>Total Customer</p>
            </div>
        </article>
        
        {{-- Stat Card untuk Total Admin, hanya tampil jika user adalah superadmin --}}
        @if (Auth::user()->isSuperAdmin())
        <article class="stat-card">
            <div class="icon"><i class="fas fa-user-shield"></i></div>
            <div class="info">
                <h3>{{ $totalAdmins }}</h3>
                <p>Total Admin</p>
            </div>
        </article>
        @endif

        <article class="stat-card">
            <div class="icon"><i class="fas fa-wallet"></i></div>
            <div class="info">
                <h3>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                <p>Total Pendapatan</p>
            </div>
        </article>
    </section>

    <div class="dashboard-grid">
        {{-- Section Pemesanan Terbaru --}}
        <section class="dashboard-section">
            <h2><i class="fas fa-clock"></i> Pemesanan Terbaru</h2>
            <div class="table-wrapper">
                <table class="content-table">
                    <thead>
                        <tr>
                            <th>ID Booking</th>
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
                            <td>{{ $booking->id_booking }}</td>
                            <td>{{ $booking->room?->cabin?->cabin_name ?? 'Kabin Dihapus' }}</td>
                            <td>{{ $booking->room?->typeroom ?? 'Tipe Kamar Dihapus' }}</td>
                            <td>{{ $booking->user?->name ?? 'Pengguna Dihapus' }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') }}</td>
                            <td>
                                <span class="status {{ strtolower($booking->status) }}">{{ ucfirst($booking->status) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 20px;">Belum ada data pemesanan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Section Pembayaran Terbaru --}}
        <section class="dashboard-section">
            <h2><i class="fas fa-money-check-alt"></i> Pembayaran Terbaru</h2>
            <div class="table-wrapper">
                <table class="content-table">
                    <thead>
                        <tr>
                            <th>ID Transaction</th>
                            <th>ID Booking</th>
                            <th>Nama Pengguna</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentPayments as $payment)
                        <tr>
                            <td>{{ $payment->transaction_id }}</td>
                            <td>{{ $payment->id_booking }}</td>
                            <td>{{ $payment->booking?->user?->name ?? 'Pengguna Dihapus' }}</td>
                            <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                            <td>
                                <span class="status {{ strtolower($payment->status) }}">{{ ucfirst($payment->status) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 20px;">Belum ada data pembayaran.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Section Aksi Cepat --}}
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

                {{-- Tombol Kelola Karyawan, hanya tampil jika user adalah superadmin --}}
                @if (Auth::user()->isSuperAdmin())
                <article class="action-card">
                    <div class="icon"><i class="fas fa-users-cog"></i></div>
                    <h3>Kelola Karyawan</h3>
                    <a href="{{ route('admin.employees.index') }}" title="Tambah, edit, atau hapus karyawan admin">Kelola</a>
                </article>
                @endif
            </div>
        </section>
    </div>
</div>
@endsection