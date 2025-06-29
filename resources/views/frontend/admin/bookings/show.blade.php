@extends('backend.admin_layout') {{-- Asumsi ini adalah layout admin atau layout dasar --}}

@section('title', $title)

@push('styles')
<style>
    .admin-page-bg {
        background-color: #f4f7f6;
        padding: 20px;
        min-height: 100vh;
    }
    .page-title {
        font-size: 2.2em;
        color: #223324;
        margin-bottom: 25px;
    }
    .card {
        background: #fff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    .detail-row {
        display: flex;
        margin-bottom: 10px;
        font-size: 1.1em;
        border-bottom: 1px dotted #eee;
        padding-bottom: 5px;
    }
    .detail-row strong {
        flex: 1;
        color: #333;
    }
    .detail-row span {
        flex: 2;
        color: #555;
    }
    .status-badge {
        display: inline-block;
        padding: 0.4em 0.8em;
        font-size: 0.9em;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.5rem;
        color: #fff;
    }
    .bg-warning { background-color: #ffc107; color: #212529; }
    .bg-success { background-color: #28a745; }
    .bg-danger { background-color: #dc3545; }
    .bg-secondary { background-color: #6c757d; }
    .bg-info { background-color: #17a2b8; }

    .action-buttons button, .action-buttons a {
        margin-right: 10px;
        margin-bottom: 10px;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 1em;
        font-weight: bold;
        text-decoration: none;
        transition: background-color 0.3s;
        border: none;
        cursor: pointer;
    }
    .btn-success { background-color: #229954; color: white; }
    .btn-success:hover { background-color: #1c7d43; }
    .btn-danger { background-color: #dc3545; color: white; }
    .btn-danger:hover { background-color: #c82333; }
    .btn-secondary { background-color: #6c757d; color: white; }
    .btn-secondary:hover { background-color: #5a6268; }

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
    }
    .modal-content {
        background-color: #fefefe;
        margin: auto;
        padding: 30px;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.4);
        position: relative;
    }
    .modal-close-btn {
        position: absolute;
        top: 15px;
        right: 20px;
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }
    .modal-close-btn:hover,
    .modal-close-btn:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
    .modal-content h3 {
        margin-top: 0;
        color: #223324;
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }
    .modal-content label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
    }
    .modal-content textarea,
    .modal-content input[type="text"] {
        width: calc(100% - 22px);
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 1em;
    }
    .modal-buttons {
        text-align: right;
    }
    .modal-buttons button {
        margin-left: 10px;
    }

    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 8px;
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
</style>
@endpush

@section('admin_content') {{-- CHANGE THIS LINE --}}
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

        <div class="card">
            <h3>Informasi Booking</h3>
            <div class="detail-row">
                <strong>ID Booking:</strong> <span>{{ $booking->id_booking }}</span>
            </div>
            <div class="detail-row">
                <strong>Status:</strong> <span class="status-badge {{ $booking->status_badge_class }}">{{ $booking->status_label }}</span>
            </div>
            <div class="detail-row">
                <strong>Tanggal Booking:</strong> <span>{{ \Carbon\Carbon::parse($booking->booking_date)->locale('id')->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') }}</span>
            </div>
            <div class="detail-row">
                <strong>Kabin:</strong> <span>{{ $booking->cabin->name ?? 'N/A' }} ({{ $booking->cabin->location ?? 'N/A' }})</span>
            </div>
            <div class="detail-row">
                <strong>Kamar:</strong> <span>{{ $booking->room->room_name ?? 'N/A' }} (Tipe: {{ $booking->room->typeroom ?? 'N/A' }}, Biaya: {{ number_format($booking->room->price ?? 0, 0, ',', '.') }}/malam)</span>
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
        </div>

        <div class="card">
            <h3>Riwayat Pembayaran</h3>
            @if($booking->payments->isNotEmpty())
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
                            <td>{{ $payment->id_payment }}</td>
                            <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                            <td>{{ $payment->payment_method }}</td>
                            <td>{{ $payment->transaction_id ?? '-' }}</td>
                            <td><span class="badge {{ $payment->status == 'completed' ? 'bg-success' : ($payment->status == 'pending' ? 'bg-warning' : 'bg-danger') }}">{{ ucfirst($payment->status) }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($payment->created_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>Belum ada pembayaran untuk booking ini.</p>
            @endif
        </div>

        <div class="card action-buttons">
    <h3>Aksi Admin</h3>
    @if ($booking->status === 'pending')
        <button class="btn btn-success" onclick="openModal('confirmModal')">Konfirmasi Booking</button>
        <button class="btn btn-danger" onclick="openModal('rejectModal')">Tolak Booking</button>
    @endif
    @if ($booking->status !== 'cancelled' && $booking->status !== 'rejected' && $booking->status !== 'completed')
        <button class="btn btn-secondary" onclick="openModal('cancelModal')">Batalkan Booking</button>
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
<div id="confirmModal" class="modal-overlay">
    <div class="modal-content">
        <span class="modal-close-btn" onclick="closeModal('confirmModal')">&times;</span>
        <h3>Konfirmasi Booking</h3>
        <form action="{{ route('admin.bookings.confirm', $booking->id_booking) }}" method="POST">
            @csrf
            <label for="confirm_admin_notes">Catatan Admin (Opsional):</label>
            <textarea id="confirm_admin_notes" name="admin_notes" rows="4"></textarea>
            <div class="modal-buttons">
                <button type="button" class="btn btn-secondary" onclick="closeModal('confirmModal')">Batal</button>
                <button type="submit" class="btn btn-success">Konfirmasi</button>
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