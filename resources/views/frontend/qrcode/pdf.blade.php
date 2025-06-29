<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; margin: 20px; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #223324; font-size: 24px; margin-bottom: 5px; }
        .header p { color: #666; font-size: 14px; }
        .section { margin-bottom: 20px; }
        .section h2 { border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 15px; color: #223324; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total-price { text-align: right; font-size: 16px; font-weight: bold; }
        .total-price strong { color: #229954; }
        .footer { text-align: center; margin-top: 50px; font-size: 10px; color: #888; }
        .invoice-number { text-align: right; font-size: 14px; font-weight: bold; margin-bottom: 10px; }
        .status-confirmed { color: #155724; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Konfirmasi Booking Anda</h1>
        <p>ID Booking: #{{ $booking->id_booking }}</p>
        @if ($transaction__id !== 'N/A')
            <div class="invoice-number">Invoice Number: <strong>{{ $transaction__id }}</strong></div>
        @endif
        <p>Tanggal Booking: {{ \Carbon\Carbon::parse($booking->booking_date)->locale('id')->isoFormat('dddd, D MMMM YYYY, HH:mm') }} WIB</p>
    </div>

    <div class="section">
        <h2>Informasi Booking</h2>
        <table>
            <tr>
                <th>Status</th>
                <td><span class="status-confirmed">{{ $booking->status_label }}</span></td>
            </tr>
            <tr>
                <th>Check-in</th>
                <td>{{ \Carbon\Carbon::parse($booking->check_in_date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</td>
            </tr>
            <tr>
                <th>Check-out</th>
                <td>{{ \Carbon\Carbon::parse($booking->check_out_date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</td>
            </tr>
            <tr>
                <th>Jumlah Malam</th>
                <td>{{ $booking->total_nights }} malam</td>
            </tr>
            <tr>
                <th>Jumlah Tamu</th>
                <td>{{ $booking->total_guests }} orang</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Informasi Kabin & Kamar</h2>
        <table>
            <tr>
                <th>Nama Kabin</th>
                <td>{{ $booking->cabin->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Tipe Kamar</th>
                <td>{{ $booking->room->typeroom ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Lokasi</th>
                <td>{{ $booking->cabin->location ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Biaya per Malam</th>
                <td>Rp {{ number_format($booking->room->price, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Deskripsi Kamar</th>
                <td>{{ $booking->room->description ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Detail Kontak</h2>
        <table>
            <tr>
                <th>Nama Lengkap</th>
                <td>{{ $booking->contact_name }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $booking->contact_email }}</td>
            </tr>
            <tr>
                <th>Telepon</th>
                <td>{{ $booking->contact_phone ?: '-' }}</td>
            </tr>
            <tr>
                <th>Permintaan Khusus</th>
                <td>{{ $booking->special_requests ?: '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="total-price">
        Total Pembayaran: <strong>{{ $booking->formatted_total_price }}</strong>
    </div>

    <div class="footer">
        <p>Terima kasih telah melakukan booking bersama kami.</p>
        <p>Dokumen ini adalah bukti konfirmasi booking Anda.</p>
    </div>
</body>
</html>