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

        .status-verified { 
            background: linear-gradient(135deg, #4CAF50, #45a049);
            animation: pulse 2s infinite;
        }
        .status-invalid { background: linear-gradient(135deg, #f44336, #da190b); }
        .status-unverified { background: linear-gradient(135deg, #ff9800, #f57c00); }
        .status-error { background: linear-gradient(135deg, #9e9e9e, #757575); }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

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

        .verification-badge {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            display: inline-block;
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from { box-shadow: 0 0 10px rgba(76, 175, 80, 0.5); }
            to { box-shadow: 0 0 20px rgba(76, 175, 80, 0.8); }
        }

        .booking-details {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
            text-align: left;
            border: 2px solid #e9ecef;
        }

        .booking-details h3 {
            color: #333;
            margin-bottom: 25px;
            font-size: 22px;
            text-align: center;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
            position: relative;
        }

        .booking-details h3::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: #764ba2;
            border-radius: 2px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .detail-item {
            background: white;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .detail-label {
            font-weight: 600;
            color: #555;
            font-size: 14px;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            color: #333;
            font-size: 16px;
            font-weight: 500;
        }

        .booking-status {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            border: 2px solid #ffeeba;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
        }

        .status-challenge {
            background-color: #cfe2ff;
            color: #084298;
            border: 2px solid #b6d4fe;
        }

        .price-highlight {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            font-size: 18px;
            font-weight: 600;
        }

        .timestamp {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 10px;
            font-size: 14px;
            color: #1976D2;
            margin: 20px 0;
            border-left: 4px solid #2196F3;
        }

        .security-note {
            background: linear-gradient(135deg, #e8f5e8, #f0f8f0);
            border: 2px solid #4CAF50;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
            text-align: left;
        }

        .security-note h4 {
            color: #2e7d32;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .security-note p {
            margin: 5px 0;
            color: #388e3c;
            font-size: 14px;
            line-height: 1.5;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            min-width: 150px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .refresh-btn {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 14px;
            cursor: pointer;
            margin-top: 15px;
        }

        .loading {
            display: none;
            color: #666;
            font-style: italic;
        }
        
        /* Elemen ini hanya akan terlihat saat mencetak */
        .printable-only {
            display: none;
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

            .detail-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .action-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 250px;
            }
        }

        /* Alert styles */
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            font-weight: 500;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        {{-- ========================================================= --}}
        {{-- ============ KODE MEDIA PRINT YANG DIPERBAIKI ============ --}}
        {{-- ========================================================= --}}
        @media print {
            /* 1. Sembunyikan semua elemen yang tidak perlu */
            .action-buttons, .refresh-btn, .loading, .timestamp, .security-note, 
            .status-icon, .validation-message, .validation-title, .verification-badge {
                display: none !important;
            }

            /* Sembunyikan semua di luar area cetak */
            body > *:not(#printable-area) {
                display: none !important;
            }

            /* 2. Atur ulang body dan container utama untuk cetak */
            body {
                background: #fff !important;
                padding: 0;
                margin: 0;
                font-family: 'Helvetica', 'Arial', sans-serif; /* Gunakan font yang hemat ruang */
                font-size: 9.5pt; /* Kecilkan ukuran font dasar */
                color: #000;
                line-height: 1.3;
            }

            #printable-area, .validation-container {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 0 !important;
                margin: 0 !important;
                position: static !important;
            }

            /* 3. Atur ulang detail booking agar lebih rapat */
            .booking-details {
                box-shadow: none !important;
                border: 1px solid #000 !important;
                padding: 15px !important;
                margin: 0 !important;
                border-radius: 0 !important;
            }

            .booking-details h3 {
                text-align: center;
                font-size: 14pt; /* Kecilkan judul */
                border-bottom: 2px solid #000;
                padding-bottom: 8px; /* Kurangi padding bawah */
                margin-bottom: 15px; /* Kurangi margin bawah */
            }

            .booking-details h3::after {
                display: none !important;
            }
            
            /* 4. Buat grid lebih efisien */
            .detail-grid {
                display: grid;
                grid-template-columns: 1fr 1fr; /* Tetap 2 kolom agar tidak terlalu tinggi */
                gap: 0 15px; /* Hapus gap vertikal, beri gap horizontal */
            }

            .detail-item {
                background: #fff !important;
                color: #000 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                border-left: none !important;
                border-bottom: 1px dotted #888; /* Ganti border agar lebih hemat tinta */
                padding: 6px 0; /* Kurangi padding secara drastis */
                margin: 0;
            }
            
            /* Atur detail item terakhir agar tidak ada border */
            .detail-item:last-child {
              border-bottom: none;
            }
            
            /* Atur item permintaan khusus jika ada */
            .detail-item[style*="grid-column"] {
                grid-column: 1 / -1;
            }

            .detail-label {
                font-size: 8.5pt;
                font-weight: normal;
                color: #333;
                margin-bottom: 2px;
            }

            .detail-value {
                font-size: 10pt;
                font-weight: bold;
            }

            /* 5. Atur ulang status dan highlight harga */
            .booking-status {
                background-color: transparent !important;
                border: 1px solid #000 !important;
                color: #000 !important;
                padding: 3px 8px;
                font-size: 8.5pt;
                border-radius: 5px;
                font-weight: bold;
            }

            .price-highlight {
                background: #fff !important;
                color: #000 !important;
                text-align: right;
                font-size: 12pt; /* Kecilkan font total */
                font-weight: bold;
                border-top: 2px solid #000;
                padding: 10px 0 0 0; /* Kurangi padding */
                margin: 15px 0 0 0; /* Kurangi margin */
            }

            /* 6. Atur QR Code dan elemen khusus cetak lainnya */
            .printable-only {
                display: block !important; /* Tampilkan elemen yang hanya untuk cetak */
                page-break-before: auto;
                page-break-inside: avoid; /* Hindari QR code terpotong */
            }
            
            #booking-qr-code {
                display: block;
                max-width: 120px !important; /* Kecilkan QR Code agar hemat ruang */
                height: auto;
                margin: 5px auto 0;
            }

            /* Hindari halaman terpotong di tengah elemen penting */
            h3, .detail-grid, .price-highlight {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    {{-- PERUBAHAN: Tambahkan id="printable-area" di sini --}}
    <div class="validation-container" id="printable-area">
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
                ✓ BOOKING TERVERIFIKASI SISTEM
            </div>
        @endif

        @if($booking)
            <div class="booking-details">
                <h3>📋 Detail Booking</h3>
                
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">ID Booking</div>
                        <div class="detail-value">#{{ $booking->id_booking }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Status Booking</div>
                        <div class="detail-value">
                            <span class="booking-status status-{{ $booking->status }}">
                                {{ $booking->status_label }}
                            </span>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Nama Pemesan</div>
                        <div class="detail-value">{{ $booking->contact_name }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Email Pemesan</div>
                        <div class="detail-value">{{ $booking->contact_email }}</div>
                    </div>

                    @if($booking->contact_phone)
                    <div class="detail-item">
                        <div class="detail-label">Telepon</div>
                        <div class="detail-value">{{ $booking->contact_phone }}</div>
                    </div>
                    @endif

                    <div class="detail-item">
                        <div class="detail-label">Kabin</div>
                        <div class="detail-value">{{ $booking->cabin->name ?? 'N/A' }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Tipe Kamar</div>
                        <div class="detail-value">{{ $booking->room->typeroom ?? 'N/A' }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Jumlah Tamu</div>
                        <div class="detail-value">{{ $booking->total_guests }} orang</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Check-in</div>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($booking->check_in_date)->translatedFormat('d M Y') }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Check-out</div>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($booking->check_out_date)->translatedFormat('d M Y') }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Total Malam</div>
                        <div class="detail-value">{{ $booking->total_nights }} malam</div>
                    </div>

                    @if($booking->latestPayment)
                    <div class="detail-item">
                        <div class="detail-label">Status Pembayaran</div>
                        <div class="detail-value">
                            <span class="booking-status status-confirmed">
                                {{ $booking->latestPayment->status_label ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                    @endif
                </div>

                @if($booking->special_requests)
                <div class="detail-item" style="grid-column: 1 / -1;">
                    <div class="detail-label">Permintaan Khusus</div>
                    <div class="detail-value">{{ $booking->special_requests }}</div>
                </div>
                @endif

                <div class="price-highlight">
                    💰 Total Pembayaran: Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                </div>

                {{-- PERUBAHAN: Menambahkan blok untuk QR Code yang hanya akan muncul saat dicetak --}}
                @if($status === 'verified')
                <div class="printable-only" style="text-align: center; margin-top: 15px;">
                    <p style="font-size: 9pt; margin-bottom: 5px;">Pindai untuk validasi ulang</p>
                    <img id="booking-qr-code" 
                         src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ url()->current() }}" 
                         alt="QR Code Booking">
                </div>
                @endif
            </div>

            <div class="timestamp">
                🕐 Diverifikasi pada: {{ now()->translatedFormat('d F Y H:i:s') }} WIB
            </div>
        @endif

        @if($status === 'verified')
            <div class="security-note">
                <h4>🔒 Informasi Keamanan</h4>
                <p>✅ Booking ini telah terverifikasi oleh sistem dan pembayaran sudah lunas.</p>
                <p>✅ QR Code ini valid dan dapat digunakan untuk check-in.</p>
                <p>⚠️ Harap tunjukkan identitas yang sesuai dengan nama pemesan saat check-in.</p>
            </div>
        @elseif($status === 'unverified')
            <div class="alert alert-warning">
                <strong>⚠️ Perhatian:</strong> Booking ini belum dapat diverifikasi. Silakan hubungi customer service untuk bantuan lebih lanjut.
            </div>
        @elseif($status === 'invalid')
            <div class="alert alert-danger">
                <strong>❌ QR Code Tidak Valid:</strong> QR Code yang Anda scan tidak valid atau sudah kedaluwarsa.
            </div>
        @endif

        <div class="action-buttons">
            <a href="{{ route('frontend.beranda') }}" class="btn btn-primary">🏠 Kembali ke Beranda</a>
            
            @if($booking && $status === 'verified')
                <button onclick="window.print()" class="btn btn-secondary">🖨️ Cetak Validasi</button>
            @endif
        </div>

        <button onclick="refreshValidation()" class="refresh-btn" id="refreshBtn">
            🔄 Refresh Validasi
        </button>
        <div class="loading" id="loading">Memuat ulang...</div>
    </div>

    <script>
        function refreshValidation() {
            const refreshBtn = document.getElementById('refreshBtn');
            const loading = document.getElementById('loading');
            
            refreshBtn.style.display = 'none';
            loading.style.display = 'block';
            
            // Reload halaman setelah 1 detik
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }

        // Auto-refresh setiap 30 detik jika status unverified
        @if($status === 'unverified')
        setTimeout(() => {
            window.location.reload();
        }, 30000);
        @endif
    </script>
</body>
</html>