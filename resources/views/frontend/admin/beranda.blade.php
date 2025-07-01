@extends('backend.admin_layout')

@section('title', 'Admin Dashboard')

@push('styles')
<style>
    .container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 15px;
    }

    /* Stats Grid - Improved responsiveness */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }
    }

    .stat-card {
        background: linear-gradient(135deg, #fff 0%, #f8fffe 100%);
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        gap: 20px;
        transition: all 0.3s ease;
        border: 1px solid rgba(39, 174, 96, 0.1);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), #2ecc71);
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(39, 174, 96, 0.15);
        border-color: var(--primary-color);
    }

    @media (max-width: 768px) {
        .stat-card {
            padding: 20px;
            gap: 15px;
        }
    }

    .stat-card .icon {
        font-size: 2.2em;
        color: var(--primary-color);
        width: 65px;
        height: 65px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--light-green-bg), rgba(39, 174, 96, 0.1));
        border-radius: 16px;
        box-shadow: 0 4px 8px rgba(39, 174, 96, 0.2);
        flex-shrink: 0;
    }

    @media (max-width: 768px) {
        .stat-card .icon {
            width: 55px;
            height: 55px;
            font-size: 1.8em;
        }
    }

    .stat-card .info {
        flex: 1;
        min-width: 0;
    }

    .stat-card .info h3 {
        margin: 0 0 5px 0;
        font-size: 1.9em;
        font-weight: 700;
        color: var(--text-dark);
        line-height: 1.2;
    }

    .stat-card .info p {
        margin: 0;
        color: #666;
        font-weight: 500;
        font-size: 0.95em;
        line-height: 1.3;
    }

    @media (max-width: 768px) {
        .stat-card .info h3 {
            font-size: 1.6em;
        }
        .stat-card .info p {
            font-size: 0.9em;
        }
    }

    /* Dashboard Grid - Adjusted for vertical stacking */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr; /* Changed to 1fr for vertical stacking */
        gap: 30px;
        margin-bottom: 30px;
    }

    @media (max-width: 992px) {
        .dashboard-grid {
            gap: 20px;
        }
    }

    .dashboard-section {
        background: #fff;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border: 1px solid #e0e0e0;
        /* Removed margin-bottom here as gap in grid handles it for sections */
    }

    @media (max-width: 768px) {
        .dashboard-section {
            padding: 20px;
        }
    }

    .dashboard-section h2 {
        margin: 0 0 20px 0;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--primary-color);
        font-size: 1.2em;
        font-weight: 600;
        color: var(--text-dark);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    @media (max-width: 768px) {
        .dashboard-section h2 {
            font-size: 1.1em;
            margin-bottom: 15px;
        }
    }

    .dashboard-section h2 i {
        color: var(--primary-color);
        font-size: 0.9em;
    }

    /* Table improvements */
    .table-wrapper {
        overflow-x: auto; /* Ensures horizontal scroll when content is too wide */
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .content-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9em;
        background: #fff;
        min-width: 800px; /* Increased min-width for tables to ensure content fits without squishing */
    }

    @media (max-width: 768px) {
        .content-table {
            font-size: 0.85em;
            min-width: 700px; /* Adjusted min-width for mobile, if needed for complex tables */
        }
    }

    .content-table thead tr {
        background: linear-gradient(135deg, var(--primary-color), #2ecc71);
        color: #ffffff;
        text-align: left;
    }

    .content-table th {
        padding: 15px 12px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8em;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .content-table td {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
        white-space: nowrap;
    }

    @media (max-width: 768px) {
        .content-table th,
        .content-table td {
            padding: 10px 8px;
        }
    }

    .content-table tbody tr:nth-of-type(even) {
        background-color: #fafafa;
    }

    .content-table tbody tr:hover {
        background-color: rgba(39, 174, 96, 0.05);
        transition: background-color 0.2s ease;
    }

    .content-table tbody tr:last-of-type td {
        border-bottom: 3px solid var(--primary-color);
    }

    /* Status badges - improved design */
    .status {
        padding: 6px 12px;
        border-radius: 20px;
        color: white;
        font-size: 0.75em;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
        min-width: 70px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
    }

    .status.confirmed { background: linear-gradient(135deg, #27ae60, #2ecc71); }
    .status.pending { background: linear-gradient(135deg, #f39c12, #e67e22); }
    .status.cancelled, .status.rejected { background: linear-gradient(135deg, #e74c3c, #c0392b); }
    .status.completed { background: linear-gradient(135deg, #3498db, #2980b9); }
    .status.paid { background: linear-gradient(135deg, #27ae60, #2ecc71); }
    .status.unpaid { background: linear-gradient(135deg, #e74c3c, #c0392b); }

    /* Actions Container - Better mobile handling */
    .actions-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    @media (max-width: 992px) {
        .actions-container {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            gap: 15px;
            padding-bottom: 15px;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: var(--primary-color) #f1f1f1;
        }
    }

    @media (max-width: 992px) {
        .actions-container::-webkit-scrollbar {
            height: 8px;
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
    }

    .action-card {
        background: linear-gradient(135deg, #fff 0%, #f8fffe 100%);
        border: 1px solid rgba(39, 174, 96, 0.15);
        padding: 20px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
    }

    @media (max-width: 992px) {
        .action-card {
            flex: 0 0 280px;
            width: 280px;
        }
    }

    @media (max-width: 768px) {
        .action-card {
            flex: 0 0 260px;
            width: 260px;
            padding: 18px;
        }
    }

    .action-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-color), #2ecc71);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .action-card:hover::before {
        transform: scaleX(1);
    }

    .action-card:hover {
        background: #fff;
        border-color: var(--primary-color);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(39, 174, 96, 0.15);
    }

    .action-card .icon {
        font-size: 1.6em;
        color: var(--primary-color);
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--light-green-bg), rgba(39, 174, 96, 0.1));
        border-radius: 12px;
        flex-shrink: 0;
    }

    @media (max-width: 768px) {
        .action-card .icon {
            width: 45px;
            height: 45px;
            font-size: 1.4em;
        }
    }

    .action-card h3 {
        margin: 0;
        font-size: 1.1em;
        font-weight: 600;
        color: var(--text-dark);
        flex: 1;
        line-height: 1.3;
    }

    @media (max-width: 768px) {
        .action-card h3 {
            font-size: 1em;
        }
    }

    .action-card a {
        text-decoration: none;
        background: linear-gradient(135deg, var(--primary-color), #2ecc71);
        color: white;
        padding: 10px 16px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.85em;
        white-space: nowrap;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 6px rgba(39, 174, 96, 0.3);
    }

    .action-card a:hover {
        background: linear-gradient(135deg, #229954, var(--primary-color));
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(39, 174, 96, 0.4);
    }

    @media (max-width: 768px) {
        .action-card a {
            padding: 8px 14px;
            font-size: 0.8em;
        }
    }

    /* Admin Header improvements */
    .admin-header {
        text-align: center;
        margin-bottom: 40px;
        padding: 30px 0;
        background: linear-gradient(135deg, rgba(39, 174, 96, 0.05), rgba(46, 204, 113, 0.05));
        border-radius: 12px;
        border: 1px solid rgba(39, 174, 96, 0.1);
    }

    @media (max-width: 768px) {
        .admin-header {
            margin-bottom: 30px;
            padding: 20px 15px;
        }
    }

    .admin-header h1 {
        color: var(--text-dark);
        font-size: 2.2em;
        font-weight: 700;
        margin-bottom: 10px;
        background: linear-gradient(135deg, var(--primary-color), #2ecc71);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    @media (max-width: 768px) {
        .admin-header h1 {
            font-size: 1.8em;
        }
    }

    .admin-header p {
        color: #666;
        font-size: 1.1em;
        margin: 0;
        font-weight: 400;
    }

    @media (max-width: 768px) {
        .admin-header p {
            font-size: 1em;
        }
    }

    /* Empty state styling */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #666;
        font-style: italic;
    }

    @media (max-width: 768px) {
        .empty-state {
            padding: 30px 15px;
            font-size: 0.9em;
        }
    }

    /* Utility classes for better spacing */
    .mb-4 { margin-bottom: 1.5rem; }
    .mb-3 { margin-bottom: 1rem; }
    .mb-2 { margin-bottom: 0.5rem; }

    /* Loading states */
    .loading {
        opacity: 0.7;
        pointer-events: none;
    }

    /* Focus states for accessibility */
    .action-card a:focus,
    .content-table:focus {
        outline: 2px solid var(--primary-color);
        outline-offset: 2px;
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

    {{-- The dashboard-grid now handles vertical stacking for its direct children --}}
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
                            <td colspan="7" class="empty-state">Belum ada data pemesanan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Section Pembayaran Terbaru (now automatically below due to grid change) --}}
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
                            <td colspan="5" class="empty-state">Belum ada data pembayaran.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    {{-- Section Aksi Cepat --}}
    <section class="dashboard-section">
        <h2><i class="fas fa-bolt"></i> Aksi Cepat</h2>
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
@endsection