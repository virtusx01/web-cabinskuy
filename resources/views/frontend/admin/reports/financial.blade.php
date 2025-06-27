@extends('backend.admin_layout')

@section('admin_content')
    <div class="container">
        <div class="admin-header">
            <h1>Laporan Keuangan</h1>
            <p>Lihat ringkasan dan detail transaksi pembayaran untuk bulan dan tahun tertentu.</p>
        </div>

        <div class="report-filter-section" style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: var(--shadow); margin-bottom: 30px;">
            <form action="{{ route('admin.reports.financial') }}" method="GET" style="display: flex; gap: 15px; align-items: center;">
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
                <a href="{{ route('admin.reports.financial.pdf', ['month' => $month, 'year' => $year]) }}" target="_blank"
                   style="background-color: #e74c3c; color: white; padding: 8px 15px; border: none; border-radius: 5px; text-decoration: none; transition: background-color 0.3s;">
                    <i class="fas fa-file-pdf"></i> Cetak PDF
                </a>
            </form>
        </div>

        <div class="report-summary" style="background-color: #e9f5e9; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid var(--primary-color);">
            <h3 style="color: var(--primary-dark); margin-top: 0;">Ringkasan Keuangan Bulan {{ $months[$month] }} {{ $year }}</h3>
            <p style="font-size: 1.2em; font-weight: bold;">Total Pemasukan: Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
        </div>

        <div class="report-table-section" style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: var(--shadow);">
            <h3 style="color: var(--text-dark); margin-top: 0;">Detail Transaksi</h3>
            <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                <thead>
                    <tr style="background-color: var(--light-green-bg);">
                        <th style="padding: 12px; border: 1px solid var(--border-color); text-align: left;">Tanggal</th>
                        <th style="padding: 12px; border: 1px solid var(--border-color); text-align: left;">Deskripsi</th>
                        <th style="padding: 12px; border: 1px solid var(--border-color); text-align: left;">Jenis</th>
                        <th style="padding: 12px; border: 1px solid var(--border-color); text-align: right;">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($detailedTransactions as $item)
                        <tr>
                            <td style="padding: 10px; border: 1px solid var(--border-color);">{{ $item['date'] }}</td>
                            <td style="padding: 10px; border: 1px solid var(--border-color);">{{ $item['description'] }}</td>
                            <td style="padding: 10px; border: 1px solid var(--border-color);">{{ $item['type'] }}</td>
                            <td style="padding: 10px; border: 1px solid var(--border-color); text-align: right;">{{ number_format($item['amount'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding: 10px; text-align: center; border: 1px solid var(--border-color);">Tidak ada data keuangan untuk bulan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
