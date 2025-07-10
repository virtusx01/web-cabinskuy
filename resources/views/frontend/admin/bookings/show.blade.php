@extends('backend.admin_layout') {{-- Asumsi ini adalah layout admin atau layout dasar --}}

@section('title', $title)

@push('styles')
<style>
    :root {
        --primary-color: #229954;
        --success-color: #28a745;
        --danger-color: #dc3545;
        --warning-color: #ffc107;
        --info-color: #17a2b8;
        --secondary-color: #6c757d;
        --dark-green: #223324;
        --light-grey-bg: #f4f7f6;
        --white-bg: #fff;
        --text-dark: #333;
        --text-medium: #555;
        --border-light: #eee;
        --box-shadow: rgba(0,0,0,0.05);
        --modal-shadow: rgba(0,0,0,0.4);
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        color: var(--text-dark);
    }

    .admin-page-bg {
        background-color: var(--light-grey-bg);
        padding: 15px; /* Adjusted for mobile */
        min-height: 100vh;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 10px; /* Added horizontal padding */
    }

    .page-title {
        font-size: 1.8em; /* Adjusted for mobile */
        color: var(--dark-green);
        margin-bottom: 20px; /* Adjusted */
        text-align: center;
    }

    .card {
        background: var(--white-bg);
        padding: 20px; /* Adjusted for mobile */
        border-radius: 12px;
        box-shadow: 0 4px 15px var(--box-shadow);
        margin-bottom: 20px;
    }

    .card h3 {
        font-size: 1.4em; /* Adjusted for mobile */
        color: var(--dark-green);
        margin-top: 0;
        margin-bottom: 15px;
        border-bottom: 1px solid var(--border-light);
        padding-bottom: 10px;
    }

    .detail-row {
        display: flex;
        flex-wrap: wrap; /* Allows wrapping on smaller screens */
        margin-bottom: 8px; /* Adjusted */
        font-size: 0.95em; /* Adjusted for mobile */
        border-bottom: 1px dotted var(--border-light);
        padding-bottom: 5px;
    }

    .detail-row strong {
        flex: 1 1 120px; /* Flexible width for labels */
        color: var(--text-dark);
        margin-right: 10px;
    }

    .detail-row span {
        flex: 2 1 calc(100% - 130px); /* Flexible width for values */
        color: var(--text-medium);
        word-break: break-word; /* Prevents long words from overflowing */
        text-align: left; /* Ensure left alignment for all spans */
    }

    @media (max-width: 768px) {
        .detail-row {
            flex-direction: column; /* Stack on smaller screens */
            align-items: flex-start;
        }
        .detail-row strong {
            margin-bottom: 3px;
        }
    }

    /* The .status-badge class is intentionally kept for payment history,
       but it will be removed from the main booking status display. */
    .status-badge {
        display: inline-block;
        padding: 0.3em 0.7em; /* Adjusted */
        font-size: 0.85em; /* Adjusted */
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.5rem;
        color: #fff;
    }

    /* Status badge colors */
    .bg-warning { background-color: var(--warning-color); color: var(--text-dark); }
    .bg-success { background-color: var(--success-color); }
    .bg-danger { background-color: var(--danger-color); }
    .bg-secondary { background-color: var(--secondary-color); }
    .bg-info { background-color: var(--info-color); }
    .bg-primary { background-color: var(--primary-color); }

    .action-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 10px; /* Space between buttons */
        justify-content: center; /* Center buttons on mobile */
    }

    .action-buttons button,
    .action-buttons a {
        flex-grow: 1; /* Allow buttons to grow */
        max-width: 100%; /* Limit width on smaller screens */
        padding: 10px 15px; /* Adjusted */
        border-radius: 8px;
        font-size: 0.95em; /* Adjusted */
        font-weight: bold;
        text-decoration: none;
        transition: background-color 0.3s ease, transform 0.2s ease;
        border: none;
        cursor: pointer;
        text-align: center;
    }

    .action-buttons button:hover,
    .action-buttons a:hover {
        transform: translateY(-2px); /* Slight lift on hover */
    }

    /* Button colors */
    .btn-success { background-color: #229954; color: white; }
    .btn-success:hover { background-color: #1c7d43; }
    .btn-danger { background-color: var(--danger-color); color: white; }
    .btn-danger:hover { background-color: #c82333; }
    .btn-secondary { background-color: var(--secondary-color); color: white; }
    .btn-secondary:hover { background-color: #5a6268; }
    .btn-primary { background-color: var(--primary-color); color: white; }
    .btn-primary:hover { background-color: #0056b3; }
    .btn-info { background-color: var(--info-color); color: white; }
    .btn-info:hover { background-color: #138496; }
    .btn-warning { background-color: var(--warning-color); color: var(--text-dark); }
    .btn-warning:hover { background-color: #e0a800; }

    /* Table styles */
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .table th,
    .table td {
        padding: 10px 8px; /* Adjusted padding */
        border: 1px solid var(--border-light);
        text-align: left;
        font-size: 0.9em; /* Adjusted for mobile */
    }

    .table th {
        background-color: var(--light-grey-bg);
        font-weight: bold;
        color: var(--text-dark);
    }

    /* Responsive table for small screens */
    @media (max-width: 600px) {
        .table thead {
            display: none; /* Hide table headers */
        }

        .table, .table tbody, .table tr, .table td {
            display: block; /* Make table elements act like block elements */
            width: 100%;
        }

        .table tr {
            margin-bottom: 15px;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px var(--box-shadow);
        }

        .table td {
            text-align: right;
            padding-left: 50%; /* Space for pseudo-element label */
            position: relative;
        }

        .table td::before {
            content: attr(data-label); /* Use data-label for content */
            position: absolute;
            left: 8px;
            width: calc(50% - 16px);
            padding-right: 10px;
            white-space: nowrap;
            text-align: left;
            font-weight: bold;
            color: var(--text-dark);
        }
    }

    /* Modal styles */
    .modal-overlay {
        display: none;
        position: fixed;
        z-index: 1001;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.6);
        justify-content: center;
        align-items: center;
        animation: fadeIn 0.3s ease-out;
    }

    .modal-content {
        background-color: var(--white-bg);
        margin: auto;
        padding: 25px; /* Adjusted */
        border-radius: 12px;
        width: 95%; /* Increased width for mobile */
        max-width: 500px;
        box-shadow: 0 5px 15px var(--modal-shadow);
        position: relative;
        animation: slideIn 0.3s ease-out;
    }

    .modal-close-btn {
        position: absolute;
        top: 10px; /* Adjusted */
        right: 15px; /* Adjusted */
        color: #aaa;
        font-size: 32px; /* Slightly larger */
        font-weight: bold;
        cursor: pointer;
        transition: color 0.2s ease;
    }
    .modal-close-btn:hover,
    .modal-close-btn:focus {
        color: #555; /* Darker on hover */
        text-decoration: none;
    }

    .modal-content h3 {
        margin-top: 0;
        color: var(--dark-green);
        margin-bottom: 15px; /* Adjusted */
        border-bottom: 1px solid var(--border-light);
        padding-bottom: 10px;
        font-size: 1.3em; /* Adjusted */
    }

    .modal-content label {
        display: block;
        margin-bottom: 6px; /* Adjusted */
        font-weight: 600;
        font-size: 0.95em; /* Adjusted */
    }

    .modal-content textarea,
    .modal-content input[type="text"],
    .modal-content select {
        width: calc(100% - 20px); /* Adjusted width */
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 0.95em; /* Adjusted */
        box-sizing: border-box; /* Include padding in width */
    }

    .modal-buttons {
        text-align: right;
        display: flex;
        justify-content: flex-end;
        gap: 10px; /* Space between buttons */
        flex-wrap: wrap;
    }

    .modal-buttons button {
        padding: 8px 15px; /* Adjusted */
        font-size: 0.9em; /* Adjusted */
        border-radius: 6px;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    /* Alert messages */
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 8px;
        font-size: 0.95em; /* Adjusted */
    }
    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }
    .alert-success {
        color: #155724;
        background-color: #d4edda;
        border-color: #c3e6cb;
    }

    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideIn {
        from { transform: translateY(-30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    /* Alert box for manual confirmation */
    .alert-info {
        color: #0c5460;
        background-color: #d1ecf1;
        border-color: #bee5eb;
    }
</style>
@endpush

@section('admin_content')
<div class="admin-page-bg">
    <div class="container">
        <h1 class="page-title">Detail Booking #{{ $booking->id_booking }}</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="card">
            <h3>Informasi Booking</h3>
            <div class="detail-row">
                <strong>ID Booking:</strong> <span>{{ $booking->id_booking }}</span>
            </div>
            <div class="detail-row">
                {{-- Removed 'status-badge' class to make it left-aligned and without badge styling --}}
                <strong>Status:</strong> <span>{{ $booking->status_label }}</span>
            </div>
            <div class="detail-row">
                <strong>Tanggal Booking:</strong> <span>{{ \Carbon\Carbon::parse($booking->booking_date)->locale('id')->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') }}</span>
            </div>
            <div class="detail-row">
                <strong>Kabin:</strong> <span>{{ $booking->cabin->name ?? 'N/A' }} ({{ $booking->cabin->location_address ?? 'N/A' }})</span>
            </div>
            <div class="detail-row">
                <strong>Kamar:</strong> <span>{{ $booking->room->typeroom ?? 'N/A' }}
            </div>
            <div class="detail-row">
                <strong>Tanggal Check-in:</strong> <span>{{ \Carbon\Carbon::parse($booking->check_in_date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
            </div>
            <div class="detail-row">
                <strong>Tanggal Check-out:</strong> <span>{{ \Carbon\Carbon::parse($booking->check_out_date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
            </div>
            <div class="detail-row">
                <strong>Jumlah Malam:</strong> <span>{{ $booking->total_nights }}</span>
            </div>
            <div class="detail-row">
                <strong>Jumlah Tamu:</strong> <span>{{ $booking->total_guests }}</span>
            </div>
            <div class="detail-row">
                <strong>Total Biaya:</strong> <span>{{ $booking->formatted_total_price }}</span>
            </div>
        </div>

        <div class="card">
            <h3>Detail Kontak Pemesan</h3>
            <div class="detail-row">
                <strong>Nama:</strong> <span>{{ $booking->contact_name }}</span>
            </div>
            <div class="detail-row">
                <strong>Email:</strong> <span>{{ $booking->contact_email }}</span>
            </div>
            <div class="detail-row">
                <strong>Telepon:</strong> <span>{{ $booking->contact_phone ?? '-' }}</span>
            </div>
            <div class="detail-row">
                <strong>User ID:</strong> <span>{{ $booking->user->id ?? 'Guest' }} ({{ $booking->user->name ?? 'N/A' }})</span>
            </div>
            @if($booking->special_requests)
            <div class="detail-row">
                <strong>Permintaan Khusus:</strong> <span>{{ $booking->special_requests }}</span>
            </div>
            @endif
        </div>

        <div class="card">
            <h3>Log Admin</h3>
            <div class="detail-row">
                <strong>Dikonfirmasi Oleh:</strong> <span>{{ $booking->confirmedBy->name ?? '-' }} pada {{ $booking->confirmed_at ? \Carbon\Carbon::parse($booking->confirmed_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : '-' }}</span>
            </div>
            <div class="detail-row">
                <strong>Ditolak Oleh:</strong> <span>{{ $booking->rejectedBy->name ?? '-' }} pada {{ $booking->rejected_at ? \Carbon\Carbon::parse($booking->rejected_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : '-' }}</span>
            </div>
            @if($booking->rejection_reason)
            <div class="detail-row">
                <strong>Alasan Penolakan:</strong> <span>{{ $booking->rejection_reason }}</span>
            </div>
            @endif
            <div class="detail-row">
                <strong>Dibatalkan Oleh (User):</strong> <span>{{ $booking->cancelled_at ? 'Pada ' . \Carbon\Carbon::parse($booking->cancelled_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : '-' }}</span>
            </div>
            @if($booking->cancellation_reason)
            <div class="detail-row">
                <strong>Alasan Pembatalan:</strong> <span>{{ $booking->cancellation_reason }}</span>
            </div>
            @endif
            @if($booking->admin_notes)
            <div class="detail-row">
                <strong>Catatan Admin:</strong> <span>{{ $booking->admin_notes }}</span>
            </div>
            @endif
            {{-- New section for completed status log --}}
            <div class="detail-row">
                <strong>Diselesaikan Oleh:</strong> <span>{{ $booking->completedBy->name ?? '-' }} pada {{ $booking->completed_at ? \Carbon\Carbon::parse($booking->completed_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : '-' }}</span>
            </div>
        </div>

        <div class="card">
            <h3>Riwayat Pembayaran</h3>
            @if($booking->payments->isNotEmpty())
                <div class="table-responsive"> {{-- Added a responsive wrapper for table --}}
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID Pembayaran</th>
                                <th>Jumlah</th>
                                <th>Metode</th>
                                <th>ID Transaksi</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($booking->payments as $payment)
                            <tr>
                                <td data-label="ID Pembayaran">{{ $payment->id_payment }}</td>
                                <td data-label="Jumlah">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                <td data-label="Metode">{{ $payment->payment_method }}</td>
                                <td data-label="ID Transaksi">{{ $payment->transaction_id ?? '-' }}</td>
                                <td data-label="Status"><span class="status-badge {{ $payment->status == 'paid' || $payment->status == 'settlement' ? 'bg-success' : ($payment->status == 'pending' ? 'bg-warning' : 'bg-danger') }}">{{ ucfirst($payment->status) }}</span></td>
                                <td data-label="Tanggal">{{ \Carbon\Carbon::parse($payment->created_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p>Belum ada pembayaran untuk booking ini.</p>
            @endif
        </div>

        <div class="card action-buttons">
            @if ($booking->status === 'pending')
                <button class="btn btn-warning" onclick="openModal('manualConfirmModal')">Konfirmasi</button>
                <button class="btn btn-danger" onclick="openModal('rejectModal')">Tolak Booking</button>
            @endif
            {{-- Allow cancellation for pending, challenge, or confirmed bookings --}}
            @if (in_array($booking->status, ['pending', 'challenge', 'confirmed']) && $booking->status !== 'completed' && $booking->status !== 'rejected')
                <button class="btn btn-secondary" onclick="openModal('cancelModal')">Batalkan Booking</button>
            @endif
            
            {{-- New 'Selesaikan Booking' button --}}
            {{-- Only show if booking is confirmed and check-in date is today or past, and not already completed/rejected/cancelled --}}
            @php
                $canComplete = ($booking->status === 'confirmed' || $booking->status === 'paid') &&
                                \Carbon\Carbon::now()->startOfDay()->gte($booking->check_in_date->startOfDay()) &&
                                $booking->status !== 'completed' &&
                                $booking->status !== 'rejected' &&
                                $booking->status !== 'cancelled';
            @endphp
            @if ($canComplete)
                <button class="btn btn-primary" onclick="openModal('completeModal')">Selesaikan Booking (Check-out)</button>
            @endif

            {{-- PERBAIKAN: HANYA SUPERADMIN YANG BISA MELIHAT DAN MELAKUKAN AKSI HAPUS PERMANEN --}}
            @if (Auth::check() && Auth::user()->isSuperAdmin())
                <form action="{{ route('admin.bookings.destroy', $booking->id_booking) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus booking ini secara permanen? Tindakan ini tidak bisa dibatalkan dan akan menghapus semua data terkait pembayaran.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus Permanen</button>
                </form>
            @endif
            
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-info">Kembali ke Daftar Booking</a>
        </div>
    </div>
</div>

{{-- Confirm Modal --}}

{{-- Manual Confirm Modal (NEW) --}}
<div id="manualConfirmModal" class="modal-overlay">
    <div class="modal-content">
        <span class="modal-close-btn" onclick="closeModal('manualConfirmModal')">&times;</span>
        <h3>Konfirmasi</h3>
        <div class="alert alert-info">
            <strong>Perhatian:</strong> Fitur ini akan mengkonfirmasi booking dan secara otomatis mengupdate status pembayaran menjadi 'paid'. Gunakan ketika payment gateway callback gagal atau untuk konfirmasi pembayaran offline.
        </div>
        <form action="{{ route('admin.bookings.confirm-manually', $booking->id_booking) }}" method="POST">
            @csrf
            <label for="manual_admin_notes">Catatan Admin (Opsional):</label>
            <textarea id="manual_admin_notes" name="admin_notes" rows="4" placeholder="Catatan admin tentang konfirmasi manual ini..."></textarea>
            
            <label for="manual_payment_method">Metode Pembayaran:</label>
            <select id="manual_payment_method" name="payment_method">
                <option value="manual_confirmation">Konfirmasi Manual</option>
                <option value="bank_transfer">Transfer Bank</option>
                <option value="cash">Tunai</option>
                <option value="credit_card">Kartu Kredit</option>
                <option value="debit_card">Kartu Debit</option>
                <option value="e_wallet">E-Wallet</option>
                <option value="other">Lainnya</option>
            </select>
            
            <div class="modal-buttons">
                <button type="button" class="btn btn-secondary" onclick="closeModal('manualConfirmModal')">Batal</button>
                <button type="submit" class="btn btn-warning">Konfirmasi Manual</button>
            </div>
        </form>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="modal-overlay">
    <div class="modal-content">
        <span class="modal-close-btn" onclick="closeModal('rejectModal')">&times;</span>
        <h3>Tolak Booking</h3>
        <form action="{{ route('admin.bookings.reject', $booking->id_booking) }}" method="POST">
            @csrf
            <label for="rejection_reason">Alasan Penolakan:</label>
            <textarea id="rejection_reason" name="rejection_reason" rows="4" required></textarea>
            <label for="reject_admin_notes">Catatan Admin (Opsional):</label>
            <textarea id="reject_admin_notes" name="admin_notes" rows="4"></textarea>
            <div class="modal-buttons">
                <button type="button" class="btn btn-secondary" onclick="closeModal('rejectModal')">Batal</button>
                <button type="submit" class="btn btn-danger">Tolak</button>
            </div>
        </form>
    </div>
</div>

{{-- Cancel Modal (Admin) --}}
<div id="cancelModal" class="modal-overlay">
    <div class="modal-content">
        <span class="modal-close-btn" onclick="closeModal('cancelModal')">&times;</span>
        <h3>Batalkan Booking (Admin)</h3>
        <form action="{{ route('admin.bookings.cancel', $booking->id_booking) }}" method="POST">
            @csrf
            <label for="cancel_reason">Alasan Pembatalan:</label>
            <textarea id="cancel_reason" name="cancellation_reason" rows="4" required></textarea>
            <label for="cancel_admin_notes">Catatan Admin (Opsional):</label>
            <textarea id="cancel_admin_notes" name="admin_notes" rows="4"></textarea>
            <div class="modal-buttons">
                <button type="button" class="btn btn-secondary" onclick="closeModal('cancelModal')">Batal</button>
                <button type="submit" class="btn btn-danger">Batalkan</button>
            </div>
        </form>
    </div>
</div>

{{-- Complete Modal (New) --}}
<div id="completeModal" class="modal-overlay">
    <div class="modal-content">
        <span class="modal-close-btn" onclick="closeModal('completeModal')">&times;</span>
        <h3>Selesaikan Booking (Check-out)</h3>
        <form action="{{ route('admin.bookings.complete', $booking->id_booking) }}" method="POST">
            @csrf
            <label for="complete_admin_notes">Catatan Admin (Opsional):</label>
            <textarea id="complete_admin_notes" name="admin_notes" rows="4"></textarea>
            <div class="modal-buttons">
                <button type="button" class="btn btn-secondary" onclick="closeModal('completeModal')">Batal</button>
                <button type="submit" class="btn btn-primary">Selesaikan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'flex';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    // Close modal if clicked outside of modal-content
    window.onclick = function(event) {
        const modals = document.querySelectorAll('.modal-overlay');
        modals.forEach(modal => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    }
</script>
@endpush