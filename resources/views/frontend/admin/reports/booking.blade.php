@extends('backend.admin_layout')

@section('admin_content')
    <style>
        /* Variabel CSS untuk kemudahan pengelolaan */
        :root {
            --primary-color: #229954;
            --danger-color: #dc3545; /* Contoh warna bahaya/error */
            --success-color: #28a745; /* Contoh warna sukses */
            --warning-color: #ffc107; /* Contoh warna peringatan */
            --info-color: #17a2b8; /* Contoh warna informasi */
            --text-dark: #343a40;
            --text-light: #6c757d;
            --border-color: #dee2e6;
            --light-bg: #f8f9fa;
            --white-bg: #ffffff;
            --shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --border-radius: 0.3rem;
        }

        /* General Body Styling */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
            background-color: var(--light-bg);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 15px;
        }

        /* Header Admin */
        .admin-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background-color: var(--white-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        .admin-header h1 {
            color: var(--primary-color);
            margin-bottom: 10px;
            font-size: 2.2rem; /* Ukuran teks yang lebih besar untuk judul */
        }

        .admin-header p {
            color: var(--text-light);
            font-size: 1rem;
        }

        /* Filter Section */
        .report-filter-section {
            background-color: var(--white-bg);
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 30px;
        }

        .report-filter-section form {
            display: flex;
            flex-wrap: wrap; /* Memungkinkan item untuk wrap di mobile */
            gap: 15px;
            align-items: flex-end; /* Menyelaraskan item ke bawah */
        }

        .report-filter-section .form-group {
            flex: 1; /* Memberikan fleksibilitas pada form group */
            min-width: 150px; /* Lebar minimum untuk setiap form group */
        }

        .report-filter-section label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: var(--text-dark);
            font-size: 0.95rem;
        }

        .report-filter-section select,
        .report-filter-section button,
        .report-filter-section .btn-pdf {
            width: 100%; /* Memastikan elemen form mengambil lebar penuh */
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: all 0.3s ease;
            box-sizing: border-box; /* Penting untuk perhitungan lebar */
        }

        .report-filter-section select:focus,
        .report-filter-section button:focus,
        .report-filter-section .btn-pdf:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .report-filter-section button {
            background-color: var(--primary-color);
            color: white;
            cursor: pointer;
            border: none;
        }

        .report-filter-section button:hover {
            background-color: #0056b3; /* Warna hover yang sedikit lebih gelap */
        }

        .report-filter-section .btn-pdf {
            background-color: var(--danger-color);
            color: white;
            text-decoration: none;
            display: inline-flex; /* Menggunakan flex untuk ikon dan teks */
            align-items: center;
            justify-content: center;
            border: none;
        }

        .report-filter-section .btn-pdf i {
            margin-right: 8px;
        }

        .report-filter-section .btn-pdf:hover {
            background-color: #c82333; /* Warna hover yang sedikit lebih gelap */
        }

        /* Table Section */
        .report-table-section {
            background-color: var(--white-bg);
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow-x: auto; /* Memungkinkan tabel di-scroll secara horizontal di mobile */
        }

        .report-table-section h3 {
            color: var(--text-dark);
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .report-table-section table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            min-width: 800px; /* Minimal lebar tabel agar tidak terlalu sempit */
        }

        .report-table-section th,
        .report-table-section td {
            padding: 12px 15px; /* Padding yang lebih baik untuk sel tabel */
            border: 1px solid var(--border-color);
            text-align: left;
            font-size: 0.9rem; /* Ukuran teks yang disesuaikan untuk tabel */
            vertical-align: middle; /* Penyelarasan vertikal */
        }

        .report-table-section thead tr {
            background-color: var(--primary-color); /* Warna yang lebih kontras untuk header tabel */
            color: white;
        }

        .report-table-section tbody tr:nth-child(even) {
            background-color: var(--light-bg); /* Warna latar belakang selang-seling */
        }

        .report-table-section tbody tr:hover {
            background-color: #e9ecef; /* Warna hover pada baris tabel */
        }

        /* Status colors */
        .status-completed { color: var(--success-color); font-weight: bold; }

        /* Total Income Section */
        .total-income-section {
            background-color: var(--white-bg);
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-top: 30px;
            text-align: right;
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--text-dark);
        }
        .total-income-section span {
            color: var(--primary-color);
        }


        /* Responsive Adjustments for Mobile */
        @media (max-width: 768px) {
            .admin-header h1 {
                font-size: 1.8rem;
            }

            .admin-header p {
                font-size: 0.9rem;
            }

            .report-filter-section form {
                flex-direction: column; /* Mengubah tata letak form menjadi kolom */
                align-items: stretch; /* Meregangkan item agar memenuhi lebar */
            }

            .report-filter-section .form-group {
                min-width: unset; /* Menghapus lebar minimum */
                width: 100%; /* Memastikan setiap grup mengambil lebar penuh */
            }

            .report-filter-section select,
            .report-filter-section button,
            .report-filter-section .btn-pdf {
                padding: 12px; /* Padding yang lebih besar untuk touch target */
                font-size: 1.1rem; /* Ukuran teks lebih besar untuk keterbacaan */
            }

            .report-table-section th,
            .report-table-section td {
                padding: 10px; /* Mengurangi padding sel tabel di mobile */
                font-size: 0.85rem; /* Ukuran teks lebih kecil agar lebih banyak konten muat */
            }

            /* Menyembunyikan beberapa kolom di mobile untuk mengurangi kepadatan tabel */
            .report-table-section th:nth-child(3), /* Kabin */
            .report-table-section td:nth-child(3),
            .report-table-section th:nth-child(4), /* Ruangan */
            .report-table-section td:nth-child(4),
            .report-table-section th:nth-child(5), /* Jumlah Kamar */
            .report-table-section td:nth-child(5),
            .report-table-section th:nth-child(9), /* Total Tamu */
            .report-table-section td:nth-child(9) {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0 10px;
            }

            .report-table-section th,
            .report-table-section td {
                font-size: 0.8rem;
                padding: 8px;
            }
        }
    </style>

    <div class="container">
        <div class="admin-header">
            <h1>Laporan Booking Selesai</h1>
            <p>Lihat semua informasi pemesanan yang telah selesai untuk bulan dan tahun tertentu.</p>
        </div>

        <div class="report-filter-section">
            <form action="{{ route('admin.reports.booking') }}" method="GET">
                <div class="form-group">
                    <label for="month">Pilih Bulan:</label>
                    <select name="month" id="month">
                        @foreach ($months as $key => $name)
                            <option value="{{ $key }}" {{ $month == $key ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="year">Pilih Tahun:</label>
                    <select name="year" id="year">
                        @for ($i = date('Y'); $i >= 2020; $i--)
                            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit">Filter</button>
                <a href="{{ route('admin.reports.booking.pdf', ['month' => $month, 'year' => $year]) }}" target="_blank" class="btn-pdf">
                    <i class="fas fa-file-pdf"></i> Cetak PDF
                </a>
            </form>
        </div>

        <div class="report-table-section">
            <h3>Detail Booking Selesai</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID Booking</th>
                        <th>Tanggal Booking</th>
                        <th>Kabin</th>
                        <th>Ruangan</th>
                        <th>Jumlah Kamar</th>
                        <th>Pemesan</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th style="text-align: right;">Total Tamu</th>
                        <th style="text-align: right;">Biaya (Rp)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr>
                            <td>{{ $booking->id_booking }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d-m-Y') }}</td>
                            <td>{{ $booking->cabin->name ?? 'N/A' }}</td>
                            <td>{{ $booking->room->typeroom ?? 'N/A' }}</td>
                            <td>{{ $booking->checkin_room ?? 'N/A' }}</td>
                            <td>{{ $booking->contact_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d-m-Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d-m-Y') }}</td>
                            <td style="text-align: right;">{{ $booking->total_guests }}</td>
                            <td style="text-align: right;">{{ number_format($booking->total_price, 0, ',', '.') }}</td>
                            <td>
                                <span class="status-{{ strtolower($booking->status) }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" style="text-align: center;">Tidak ada data booking selesai untuk bulan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="total-income-section">
            Total Pendapatan: <span>{{ 'Rp' . number_format($totalIncome, 0, ',', '.') }}</span>
        </div>
    </div>
@endsection
