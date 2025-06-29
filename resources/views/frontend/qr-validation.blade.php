<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .validation-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 600px;
            width: 100%;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .validation-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .status-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            font-weight: bold;
            color: white;
        }

        .status-verified { background: linear-gradient(135deg, #4CAF50, #45a049); }
        .status-invalid { background: linear-gradient(135deg, #f44336, #da190b); }
        .status-unverified { background: linear-gradient(135deg, #ff9800, #f57c00); }
        .status-error { background: linear-gradient(135deg, #9e9e9e, #757575); }

        .validation-title {
            font-size: 28px;
            margin-bottom: 15px;
            color: #333;
            font-weight: 600;
        }

        .validation-message {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .booking-details {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            text-align: left;
        }

        .booking-details h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 20px;
            text-align: center;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .detail-label {
            font-weight: 600;
            color: #555;
            flex-basis: 40%;
        }

        .detail-value {
            color: #333;
            flex-basis: 60%;
            text-align: right;
        }

        .booking-status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        /* Add other status styles if needed for 'pending', 'cancelled', etc. */
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .status-challenge {
            background-color: #cfe2ff;
            color: #084298;
            border: 1px solid #b6d4fe;
        }


        .verification-badge {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            display: inline-block;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .timestamp {
            font-size: 14px;
            color: #888;
            margin-top: 20px;
            font-style: italic;
        }

        .back-button {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .security-note {
            background: #e3f2fd;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin-top: 20px;
            border-radius: 4px;
        }

        .security-note p {
            margin: 0;
            color: #1976D2;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .validation-container {
                padding: 30px 20px;
                margin: 10px;
            }

            .validation-title {
                font-size: 24px;
            }

            .validation-message {
                font-size: 16px;
            }

            .detail-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .detail-value {
                text-align: left;
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="validation-container">
        <div class="status-icon status-{{ $status }}">
            @if($status === 'verified')
                ✓
            @elseif($status === 'invalid')
                ✗
            @elseif($status === 'unverified')
                ⚠
            @else
                ?
            @endif
        </div>

        <h1 class="validation-title">{{ $title }}</h1>
        <p class="validation-message">{{ $message }}</p>

        @if($status === 'verified')
            <div class="verification-badge">
                ✓ TERVERIFIKASI SISTEM
            </div>
        @endif

        @if($booking)
            <div class="booking-details">
                <h3>Detail Booking</h3>
                
                <div class="detail-row">
                    <span class="detail-label">ID Booking:</span>
                    <span class="detail-value">#{{ $booking->id_booking }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">
                        <span class="booking-status status-{{ $booking->status }}">
                            {{ $booking->status_label }}
                        </span>
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Nama Pemesan:</span>
                    <span class="detail-value">{{ $booking->contact_name }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Email Pemesan:</span>
                    <span class="detail-value">{{ $booking->contact_email }}</span>
                </div>

                @if($booking->contact_phone)
                    <div class="detail-row">
                        <span class="detail-label">Telepon Pemesan:</span>
                        <span class="detail-value">{{ $booking->contact_phone }}</span>
                    </div>
                @endif

                <div class="detail-row">
                    <span class="detail-label">Kabin:</span>
                    <span class="detail-value">{{ $booking->cabin->cabin_name ?? 'N/A' }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Tipe Kamar:</span>
                    <span class="detail-value">{{ $booking->room->room_name ?? 'N/A' }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Jumlah Tamu:</span>
                    <span class="detail-value">{{ $booking->total_guests }} orang</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Check-in:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($booking->check_in_date)->translatedFormat('d M Y') }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Check-out:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($booking->check_out_date)->translatedFormat('d M Y') }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Total Malam:</span>
                    <span class="detail-value">{{ $booking->total_nights }} malam</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Total Harga:</span>
                    <span class="detail-value">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                </div>

                @if($booking->special_requests)
                    <div class="detail-row">
                        <span class="detail-label">Permintaan Khusus:</span>
                        <span class="detail-value">{{ $booking->special_requests }}</span>
                    </div>
                @endif

                @if($booking->latestPayment)
                    <div class="detail-row">
                        <span class="detail-label">Status Pembayaran:</span>
                        <span class="detail-value">{{ $booking->latestPayment->status_label ?? 'N/A' }}</span>
                    </div>
                @endif
            </div>

            <p class="timestamp">Diverifikasi pada: {{ now()->translatedFormat('d F Y H:i:s') }} WIB</p>
        @endif

        <div class="security-note">
            <p><strong>Penting:</strong> Halaman ini hanya untuk tujuan validasi. Pastikan informasi cocok dengan data reservasi Anda.</p>
        </div>

        <a href="{{ route('frontend.beranda') }}" class="back-button">Kembali ke Beranda</a>
    </div>
</body>
</html>