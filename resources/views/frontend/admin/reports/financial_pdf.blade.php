<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan Cabinskuy</title>
    <link href="https:
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 40px;
            font-size: 10pt;
            color:
            line-height: 1.6;
        }
        .page-container {
            display: flex;
            flex-direction: column;
            min-height: 95vh;
            position: relative;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header h1 {
            margin: 0;
            color:
            font-size: 30pt;
            font-weight: 700;
            letter-spacing: 2px;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 14pt;
            color:
            font-weight: 400;
        }
        .report-details {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 1px solid
        }
        .report-title-left {
            text-align: left;
            font-size: 18pt;
            color:
            font-weight: 600;
            flex-grow: 1;
        }
        .print-date {
            text-align: right;
            font-size: 9pt;
            color:
        }
        table {
            width: 95%;
            margin-left: auto;
            margin-right: auto;
            border-collapse: collapse;
            margin-top: 25px;
            background-color:
            font-size: 9pt;
        }
        th, td {
            border: 1px solid
            padding: 10px 12px;
            text-align: left;
            color:
        }
        th {
            background-color:
            font-weight: 600;
            color:
            text-align: center;
        }
        tbody tr:nth-child(even) {
            background-color:
        }
        tr.empty-row td {
            padding: 10px 12px;
            height: 37px;
            color:
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .summary-box {
            margin-top: 15px;
            text-align: left;
            padding-left: auto;
            margin-left: auto;
            margin-right: auto;
        }
        .summary-box p {
            margin: 20px;
            font-size: 15pt;
            font-weight: 600;
            color:
        }
        .signature-block-wrapper {
            margin-top: auto;
        }
        .horizontal-line-separator {
            border-top: 1px solid rgba(0, 0, 0, 0.2);
            margin-top: 40px;
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
            color:
        }
        .signature-line {
            margin-top: 60px;
            border-bottom: 1px solid
            width: 200px;
            display: inline-block;
            margin-bottom: 5px;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid
            font-size: 8.5pt;
            color:
        }
        .page-break {
            page-break-after: always;
        }
        @media print {
            body { padding: 20px; }
            .page-break { page-break-after: always; }
            .page-container { min-height: 90vh; }
        }
    </style>
</head>
<body>

    @php
        $rowsPerPage = 5;
        $transactionsCollection = collect($detailedTransactions);
        $totalTransactions = $transactionsCollection->count();
        $chunkedTransactions = $transactionsCollection->chunk($rowsPerPage);
    @endphp

    @if($totalTransactions > 0)
        @foreach($chunkedTransactions as $pageIndex => $pageOfTransactions)
            <div class="page-container">
                <div class="header">
                    <h1>CABINSKUY</h1>
                    <p>Laporan Keuangan Perusahaan</p>
                </div>

                <div class="report-details">
                    <div class="report-title-left">
                        Laporan Keuangan Bulan {{ $monthName }} {{ $year }}
                    </div>
                    <div class="print-date">
                        Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y H:i:s') }} WIB
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">Deskripsi</th>
                            <th class="text-center">Jenis</th>
                            <th class="text-right">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pageOfTransactions as $item)
                            <tr>
                                <td class="text-center">{{ $item['date'] }}</td>
                                <td>{{ $item['description'] }}</td>
                                <td class="text-center">{{ $item['type'] }}</td>
                                <td class="text-right">{{ number_format($item['amount'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach

                        @if ($pageOfTransactions->count() < $rowsPerPage)
                            @for ($i = 0; $i < ($rowsPerPage - $pageOfTransactions->count()); $i++)
                                <tr class="empty-row">
                                    <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td>
                                </tr>
                            @endfor
                        @endif
                    </tbody>
                </table>
                
                @if ($loop->last)
                    <div class="summary-box">
                        <p>Total Pemasukan Bulan {{ $monthName }} {{ $year }}: Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                    </div>

                    <div class="signature-block-wrapper">
                        <div class="horizontal-line-separator"></div>
                        <div class="signature-block">
                            <p>Hormat kami,</p>
                            <div class="signature-line"></div>
                            <p>Direktur Cabinskuy</p>
                        </div>
                        <div class="footer">
                            &copy; {{ date('Y') }} Cabinskuy. Semua Hak Dilindungi.
                        </div>
                    </div>
                @endif
            </div>

            @if (!$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach
    @else
        <div class="page-container">
               <div class="header">
                 <h1>CABINSKUY</h1>
                 <p>Laporan Keuangan Perusahaan</p>
             </div>
             <div class="report-details">
                 <div class="report-title-left">
                     Laporan Keuangan Bulan {{ $monthName }} {{ $year }}
                 </div>
                 <div class="print-date">
                     Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y H:i:s') }} WIB
                 </div>
             </div>
             <table>
                 <thead>
                     <tr>
                         <th class="text-center">Tanggal</th> <th class="text-center">Deskripsi</th> <th class="text-center">Jenis</th>
                         <th class="text-right">Jumlah (Rp)</th>
                     </tr>
                 </thead>
                 <tbody>
                     @for ($i = 0; $i < $rowsPerPage; $i++)
                         <tr class="empty-row">
                             <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td>
                         </tr>
                     @endfor
                 </tbody>
             </table>
            <div class="summary-box">
                <p>Total Pemasukan Bulan {{ $monthName }} {{ $year }}: Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
            </div>
             <div class="signature-block-wrapper">
                 <div class="horizontal-line-separator"></div>
                 <div class="signature-block">
                     <p>Hormat kami,</p>
                     <div class="signature-line"></div>
                     <p>Direktur Cabinskuy</p>
                 </div>
                 <div class="footer">
                     &copy; {{ date('Y') }} Cabinskuy. Semua Hak Dilindungi.
                 </div>
             </div>
        </div>
    @endif
</body>
</html>