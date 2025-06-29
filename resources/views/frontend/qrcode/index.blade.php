<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .qr-page-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 90%;
            text-align: center;
        }
        .qr-page-container h1 {
            color: #223324;
            font-size: 1.8em;
            margin-bottom: 20px;
        }
        .qr-page-container p {
            color: #555;
            font-size: 1em;
            margin-bottom: 10px;
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.95em;
            text-align: left; /* Align text left within detail item */
            align-items: flex-start;
        }
        .detail-item strong {
            color: #333;
            flex-basis: 40%;
            min-width: 120px;
            padding-right: 10px;
        }
        .detail-item span {
            color: #555;
            flex-basis: 60%;
            text-align: right; /* Value text align right */
        }
        .btn-download {
            background-color: #229954;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            margin-top: 25px;
            transition: background-color 0.2s;
        }
        .btn-download:hover {
            background-color: #1c7d43;
            color: white; /* Ensure text remains white on hover */
        }
        .alert-info-custom {
            background-color: #e0f2f7;
            color: #0c5460;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 0.9em;
        }
        .verification-status {
            font-size: 1.5em;
            font-weight: bold;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 8px;
        }
        .status-verified {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }
        .status-not-verified {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="qr-page-container">
        <h1>Detail Booking Anda</h1>

        @if ($verified)
            <div class="verification-status status-verified">
                TERVERIFIKASI!
            </div>
            <p>{{ $message }}</p>

            <div class="detail-section">
                <div class="detail-item">
                    <strong>ID Booking:</strong>
                    <span>#{{ $booking->id_booking }}</span>
                </div>
                <div class="detail-item">
                    <strong>Kabin:</strong>
                    <span>{{ $booking->cabin->name ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <strong>Tipe Kamar:</strong>
                    <span>{{ $booking->room->typeroom ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <strong>Check-in:</strong>
                    <span>{{ \Carbon\Carbon::parse($booking->check_in_date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
                </div>
                <div class="detail-item">
                    <strong>Check-out:</strong>
                    <span>{{ \Carbon\Carbon::parse($booking->check_out_date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
                </div>
                <div class="detail-item">
                    <strong>Jumlah Tamu:</strong>
                    <span>{{ $booking->total_guests }} orang</span>
                </div>
                <div class="detail-item">
                    <strong>Total Pembayaran:</strong>
                    <span>{{ $booking->formatted_total_price }}</span>
                </div>
                @if ($booking->latestSuccessfulPayment)
                    <div class="detail-item">
                        <strong>ID Transaksi Pembayaran:</strong>
                        <span>{{ $booking->latestSuccessfulPayment->transaction_id ?? 'N/A' }}</span>
                    </div>
                @endif
            </div>

            <div class="alert-info-custom">
                <p>Untuk detail lengkap dan konfirmasi resmi, silakan unduh dokumen PDF di bawah ini.</p>
            </div>

            <a href="{{ route('frontend.booking.pdf', ['identifier' => $booking->qr_access_token]) }}" class="btn-download" target="_blank">
                Unduh Konfirmasi Booking (PDF)
            </a>
        @else
            <div class="verification-status status-not-verified">
                TIDAK TERVERIFIKASI
            </div>
            <p>{{ $message }}</p>
            <p>Silakan hubungi administrator jika Anda memiliki pertanyaan.</p>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>