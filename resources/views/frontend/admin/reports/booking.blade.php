@extends('backend.admin_layout')

@section('admin_content')
    <div class="container">
        <div class="admin-header">
            <h1>Laporan Booking</h1>
            <p>Lihat semua informasi pemesanan (konfirmasi, ditolak, dibatalkan, selesai) untuk bulan dan tahun tertentu.</p>
        </div>

        <div class="report-filter-section" style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: var(--shadow); margin-bottom: 30px;">
            <form action="{{ route('admin.reports.booking') }}" method="GET" style="display: flex; gap: 15px; align-items: center;">
                <div class="form-group">
                    <label for="month">Pilih Bulan:</label>
                    <select name="month" id="month" style="padding: 8px; border: 1px solid var(--border-color); border-radius: 5px;">
                        @foreach ($months as $key => $name)
                            <option value="{{ $key }}" {{ $month == $key ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="year">Pilih Tahun:</label>
                    <select name="year" id="year" style="padding: 8px; border: 1px solid var(--border-color); border-radius: 5px;">
                        @for ($i = date('Y'); $i >= 2020; $i--)
                            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit" style="background-color: var(--primary-color); color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.3s;">Filter</button>
                <a href="{{ route('admin.reports.booking.pdf', ['month' => $month, 'year' => $year]) }}" target="_blank"
                   style="background-color: #e74c3c; color: white; padding: 8px 15px; border: none; border-radius: 5px; text-decoration: none; transition: background-color 0.3s;">
                    <i class="fas fa-file-pdf"></i> Cetak PDF
                </a>
            </form>
        </div>

        <div class="report-table-section" style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: var(--shadow);">
            <h3 style="color: var(--text-dark); margin-top: 0;">Detail Semua Booking</h3>
            <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                <thead>
                    <tr style="background-color: var(--light-green-bg);">
                        <th style="padding: 12px; border: 1px solid var(--border-color); text-align: left;">ID Booking</th>
                        <th style="padding: 12px; border: 1px solid var(--border-color); text-align: left;">Tanggal Booking</th>
                        <th style="padding: 12px; border: 1px solid var(--border-color); text-align: left;">Kabin</th>
                        <th style="padding: 12px; border: 1px solid var(--border-color); text-align: left;">Ruangan</th>
                        <th style="padding: 12px; border: 1px solid var(--border-color); text-align: left;">Jumlah Kamar</th>
                        <th style="padding: 12px; border: 1px solid var(--border-color); text-align: left;">Pemesan</th>
                        <th style="padding: 12px; border: 1px solid var(--border-color); text-align: left;">Check-in</th>
                        <th style="padding: 12px; border: 1px solid var(--border-color); text-align: left;">Check-out</th>
                        <th style="padding: 12px; border: 1px solid var(--border-color); text-align: right;">Total Tamu</th>
                        <th style="padding: 12px; border: 1px solid var(--border-color); text-align: right;">Biaya (Rp)</th>
                        <th style="padding: 12px; border: 1px solid var(--border-color); text-align: left;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr>
                            <td style="padding: 10px; border: 1px solid var(--border-color);">{{ $booking->id_booking }}</td>
                            <td style="padding: 10px; border: 1px solid var(--border-color);">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d-m-Y') }}</td>
                            <td style="padding: 10px; border: 1px solid var(--border-color);">{{ $booking->cabin->name ?? 'N/A' }}</td>
                            <td style="padding: 10px; border: 1px solid var(--border-color);">{{ $booking->room->typeroom ?? 'N/A' }}</td>
                            <td style="padding: 10px; border: 1px solid var(--border-color);">{{ $booking->checkin_room ?? 'N/A' }}</td>
                            <td style="padding: 10px; border: 1px solid var(--border-color);">{{ $booking->contact_name }}</td>
                            <td style="padding: 10px; border: 1px solid var(--border-color);">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d-m-Y') }}</td>
                            <td style="padding: 10px; border: 1px solid var(--border-color);">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d-m-Y') }}</td>
                            <td style="padding: 10px; border: 1px solid var(--border-color); text-align: right;">{{ $booking->total_guests }}</td>
                            <td style="padding: 10px; border: 1px solid var(--border-color); text-align: right;">{{ number_format($booking->total_price, 0, ',', '.') }}</td>
                            <td style="padding: 10px; border: 1px solid var(--border-color);">{{ ucfirst($booking->status) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="padding: 10px; text-align: center; border: 1px solid var(--border-color);">Tidak ada data booking untuk bulan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
