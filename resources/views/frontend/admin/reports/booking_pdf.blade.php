<!DOCTYPE html>
<html>
<head>
    <title>Laporan Booking Selesai Cabinskuy</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 40px;
            font-size: 10pt;
            color: #333;
            line-height: 1.6;
        }
        .page-container {
            display: flex;
            flex-direction: column;
            min-height: 95vh; /* Adjust height for each page */
            position: relative;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header h1 {
            margin: 0;
            color: #000;
            font-size: 30pt;
            font-weight: 700;
            letter-spacing: 2px;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 14pt;
            color: #555;
            font-weight: 400;
        }
        .report-details {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ccc;
        }
        .report-title-left {
            text-align: left;
            font-size: 18pt;
            color: #000;
            font-weight: 600;
            flex-grow: 1;
        }
        .print-date {
            text-align: right;
            font-size: 9pt;
            color: #777;
        }
        table {
            width: 100%; /* Changed to 100% to utilize full available width */
            margin-left: auto;
            margin-right: auto;
            border-collapse: collapse;
            margin-top: 25px;
            background-color: #fff;
            font-size: 8pt; /* Base font size for table */
            table-layout: fixed; /* Added to help with column width distribution */
        }
        th, td {
            border: 1px solid #e0e0e0;
            padding: 7px 5px; /* Reduced padding for more content space */
            text-align: left;
            color: #333;
            word-wrap: break-word; /* Ensure long words break */
        }
        th {
            background-color: #f5f5f5;
            font-weight: 600;
            color: #000;
            text-align: center;
            font-size: 7.5pt; /* Slightly reduced font size for headers */
        }
        td {
            font-size: 7pt; /* Further reduced font size for table data */
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        /* Style untuk baris kosong agar tingginya sama */
        tr.empty-row td {
            padding: 7px 5px; /* Match padding of data rows */
            height: 30px; /* Adjusted height to be roughly consistent */
            color: #fff; /* Sembunyikan &nbsp; */
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .signature-block-wrapper {
            margin-top: auto; /* Mendorong blok tanda tangan dan footer ke bawah */
        }
        .horizontal-line-separator {
            border-top: 1px solid rgba(0, 0, 0, 0.2);
            margin-top: 60px;
            margin-bottom: 40px;
            width: 100%;
            box-sizing: border-box;
        }
        .signature-block {
            text-align: right;
            margin-bottom: 50px;
            padding-right: 20px;
        }
        .signature-block p {
            margin: 0;
            font-size: 10pt;
            color: #333;
        }
        .signature-line {
            margin-top: 60px;
            border-bottom: 1px solid #777;
            width: 200px;
            display: inline-block;
            margin-bottom: 5px;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 8.5pt;
            color: #888;
        }
        .page-break {
            page-break-after: always;
        }

        /* Column Widths (example, adjust as needed) */
        /* These widths are percentages of the overall table width (100%) */
        .col-id-booking { width: 12%; }
        .col-tanggal-booking { width: 10%; }
        .col-kabin { width: 8%; }
        .col-ruangan { width: 8%; }
        .col-jumlah-kamar { width: 7%; }
        .col-pemesan { width: 15%; } /* Can be larger */
        .col-check-in { width: 10%; }
        .col-check-out { width: 10%; }
        .col-total-tamu { width: 6%; }
        .col-biaya { width: 10%; }
        .col-status { width: 7%; }

        /* Total Income Styling */
        .total-income-section {
            margin-top: 20px;
            text-align: right;
            font-size: 12pt;
            font-weight: bold;
            color: #000;
            padding-right: 20px; /* Align with signature block */
        }
        .total-income-section span {
            color: #229954; /* Primary color for income */
        }

        /* Aturan khusus untuk cetak */
        @media print {
            body { padding: 20px; }
            .page-break { page-break-after: always; }
            .page-container { min-height: 90vh; } /* Optimalkan untuk A4 */
            /* Further reduce font size for print if necessary, though 7pt should be small enough */
            td { font-size: 6.5pt; }
            th { font-size: 7pt; }
        }
    </style>
</head>
<body>

    @php
        $rowsPerPage = 6;
        $bookingsCollection = collect($bookings);
        $totalBookings = $bookingsCollection->count();
        $chunkedBookings = $bookingsCollection->chunk($rowsPerPage);
    @endphp

    @if($totalBookings > 0)
        @foreach($chunkedBookings as $pageIndex => $pageOfBookings)
            <div class="page-container">
                <div class="header">
                    <h1>CABINSKUY</h1>
                    <p>Laporan Booking Selesai</p>
                </div>

                <div class="report-details">
                    <div class="report-title-left">
                        Laporan Booking Selesai Bulan {{ $monthName }} {{ $year }}
                    </div>
                    <div class="print-date">
                        Dicetak pada: {{ $printDate ?? (\Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y H:i:s') . ' WIB') }}
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th class="text-center col-id-booking">ID Booking</th>
                            <th class="text-center col-tanggal-booking">Tanggal Booking</th>
                            <th class="text-center col-kabin">Kabin</th>
                            <th class="text-center col-ruangan">Ruangan</th>
                            <th class="text-center col-jumlah-kamar">Jumlah Kamar</th>
                            <th class="col-pemesan">Pemesan</th>
                            <th class="text-center col-check-in">Check-in</th>
                            <th class="text-center col-check-out">Check-out</th>
                            <th class="text-center col-total-tamu">Total Tamu</th>
                            <th class="text-right col-biaya">Biaya (Rp)</th>
                            <th class="text-center col-status">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pageOfBookings as $booking)
                            <tr>
                                <td class="text-center">{{ $booking->id_booking }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d-m-Y') }}</td>
                                <td class="text-center">{{ $booking->cabin->name ?? 'N/A' }}</td>
                                <td class="text-center">{{ $booking->room->typeroom ?? 'N/A' }}</td>
                                <td class="text-center">{{ $booking->checkin_room ?? 'N/A' }}</td>
                                <td>{{ $booking->contact_name }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d-m-Y') }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d-m-Y') }}</td>
                                <td class="text-center">{{ $booking->total_guests }}</td>
                                <td class="text-right">{{ number_format($booking->total_price, 0, ',', '.') }}</td>
                                <td class="text-center">{{ ucfirst($booking->status) }}</td>
                            </tr>
                        @endforeach

                        {{-- Tambahkan baris kosong jika data di halaman ini kurang dari 10 --}}
                        @if ($pageOfBookings->count() < $rowsPerPage)
                            @for ($i = 0; $i < ($rowsPerPage - $pageOfBookings->count()); $i++)
                                <tr class="empty-row">
                                    <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td>
                                    <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                            @endfor
                        @endif
                    </tbody>
                </table>
                
                {{-- Total pendapatan hanya ditampilkan di halaman terakhir --}}
                @if ($loop->last)
                    <div class="total-income-section">
                        Total Pendapatan: <span>{{ 'Rp' . number_format($totalIncome, 0, ',', '.') }}</span>
                    </div>
                    <div class="signature-block-wrapper">
                        <div class="horizontal-line-separator"></div>
                        <div class="signature-block">
                            <p>Hormat kami,</p>
                            <div class="signature-line"></div>
                            <p>Direktur Cabinskuy</p>
                        </div>
                        <div class="footer">
                            &copy; {{ \Carbon\Carbon::now()->year }} Cabinskuy. Semua Hak Dilindungi.
                        </div>
                    </div>
                @endif
            </div>

            {{-- Tambahkan page break jika ini bukan halaman terakhir --}}
            @if (!$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach
    @else
        {{-- Tampilan jika tidak ada data booking sama sekali --}}
        <div class="page-container">
            <div class="header">
                <h1>CABINSKUY</h1>
                <p>Laporan Booking Selesai</p>
            </div>
            <div class="report-details">
                <div class="report-title-left">
                    Laporan Booking Selesai Bulan {{ $monthName }} {{ $year }}
                </div>
                <div class="print-date">
                    Dicetak pada: {{ $printDate ?? (\Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y H:i:s') . ' WIB') }}
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th class="text-center col-id-booking">ID Booking</th>
                        <th class="text-center col-tanggal-booking">Tanggal Booking</th>
                        <th class="text-center col-kabin">Kabin</th>
                        <th class="text-center col-ruangan">Ruangan</th>
                        <th class="text-center col-jumlah-kamar">Jumlah Kamar</th>
                        <th class="col-pemesan">Pemesan</th>
                        <th class="text-center col-check-in">Check-in</th>
                        <th class="text-center col-check-out">Check-out</th>
                        <th class="text-center col-total-tamu">Total Tamu</th>
                        <th class="text-right col-biaya">Biaya (Rp)</th>
                        <th class="text-center col-status">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < $rowsPerPage; $i++)
                        <tr class="empty-row">
                            <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td>
                            <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    @endfor
                </tbody>
            </table>
            <div class="total-income-section">
                Total Pendapatan: Rp <span>0</span>
            </div>
            <div class="signature-block-wrapper">
                <div class="horizontal-line-separator"></div>
                <div class="signature-block">
                    <p>Hormat kami,</p>
                    <div class="signature-line"></div>
                    <p>Direktur Cabinskuy</p>
                </div>
                <div class="footer">
                    &copy; {{ \Carbon\Carbon::now()->year }} Cabinskuy. Semua Hak Dilindungi.
                </div>
            </div>
        </div>
    @endif
</body>
</html>
