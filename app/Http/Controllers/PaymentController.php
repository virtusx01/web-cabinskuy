<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Midtrans\Notification;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Menampilkan halaman pembayaran untuk booking tertentu.
     */
    public function showPaymentForm(Booking $booking)
    {
        try {
            // Perbaikan di sini: Auth::user()->id_user
            if ($booking->id_user !== (Auth::user()->id_user ?? null)) {
                abort(403, 'Anda tidak memiliki akses untuk mengakses pembayaran ini.');
            }

            if (!in_array($booking->status, ['pending', 'challenge'])) {
                return redirect()->route('frontend.booking.show', $booking->id_booking)
                    ->withErrors(['error' => 'Booking ini tidak lagi dalam status menunggu pembayaran atau sudah selesai. Status saat ini: ' . $booking->status_label]);
            }

            $booking->load(['user', 'cabin', 'room', 'payments']); // Load payments to check latest payment status

            // Dapatkan pembayaran terbaru yang masih 'pending' atau 'challenge'
            $latestPayment = $booking->payments()->whereIn('status', ['pending', 'challenge'])->latest()->first();

            return view('frontend.payment', [
                'booking'       => $booking,
                'latestPayment' => $latestPayment, // Pass latest payment to view
                'title'         => 'Pembayaran Booking #' . $booking->id_booking
            ]);

        } catch (\Exception $e) {
            Log::error('Error showing payment form for booking ' . ($booking->id_booking ?? 'N/A') . ': ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('frontend.booking.index')
                ->withErrors(['error' => 'Terjadi kesalahan saat memuat halaman pembayaran.']);
        }
    }

    /**
     * Memproses pembayaran untuk booking tertentu.
     */
    public function processPayment(Request $request, Booking $booking)
    {
        // Validasi dasar
        if ($booking->id_user !== (Auth::user()->id_user ?? null)) {
            return response()->json(['error' => 'Akses tidak diizinkan.'], 403);
        }
        if ($booking->isPaid()) {
            return response()->json(['error' => 'Booking ini sudah lunas.'], 400);
        }

        DB::beginTransaction();
        try {
            $payment = $booking->payments()->whereIn('status', ['pending', 'challenge'])->latest()->first();
            $forceNew = $request->input('force_new', false);

            // Jika diminta membuat transaksi baru (Ganti Metode Pembayaran) dan ada transaksi pending
            if ($forceNew && $payment) {
                // Batalkan transaksi lama di Midtrans dan update DB
                try {
                    $midtransStatus = Transaction::status($payment->transaction_id);

                    // PERBAIKAN DI SINI: Cek apakah $midtransStatus adalah objek dan punya properti
                    if (is_object($midtransStatus) && isset($midtransStatus->transaction_status)) {
                        if (in_array($midtransStatus->transaction_status, ['pending', 'challenge'])) {
                            Transaction::cancel($payment->transaction_id);
                            Log::info("Midtrans transaction {$payment->transaction_id} successfully cancelled from processPayment (forceNew).");
                        } else {
                            Log::info("Midtrans transaction {$payment->transaction_id} not in cancellable state for forceNew ({$midtransStatus->transaction_status}). Skipping API cancel.");
                        }
                    } else {
                        // Log jika respons status Midtrans tidak sesuai format yang diharapkan
                        Log::warning("Midtrans status response for transaction {$payment->transaction_id} was not an object or missing transaction_status in processPayment (forceNew). Response: " . json_encode($midtransStatus));
                    }
                } catch (\Exception $midtransE) {
                    Log::warning("Failed to get status or cancel Midtrans transaction {$payment->transaction_id} in processPayment (forceNew) via API: {$midtransE->getMessage()}");
                }

                $payment->update(['status' => 'cancelled']);
                Log::info("User forced new payment. Old payment #{$payment->id_payment} cancelled in DB.");
                $payment = null; // Set ke null agar transaksi baru dibuat
            }

            // Jika tidak ada payment pending, atau dipaksa buat baru, maka buat transaksi baru
            if (!$payment) {
                $midtransOrderId = Str::random(5) . time(); // Gunakan Str::random untuk keunikan

                $payment = Payment::create([
                    'id_booking'     => $booking->id_booking,
                    'amount'         => $booking->total_price,
                    'transaction_id' => $midtransOrderId,
                    'status'         => 'pending',
                    'id_user'        => Auth::user()->id_user,
                ]);

                $params = $this->buildMidtransParameters($booking, $payment);
                $snapToken = Snap::getSnapToken($params);

                $booking->update(['snap_token' => $snapToken]); // Update booking with the new snap token
            } else {
                // Jika sudah ada payment pending, gunakan snap_token yang sudah ada
                $snapToken = $booking->snap_token;
            }

            DB::commit();
            return response()->json(['snap_token' => $snapToken]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error processing payment for booking #' . $booking->id_booking . ': ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Method helper untuk membangun parameter Midtrans
     */
    private function buildMidtransParameters(Booking $booking, Payment $payment): array
    {
        // Pastikan relasi user, room, dan cabin sudah di-load di showPaymentForm
        // Jika tidak, Anda mungkin perlu me-loadnya di sini juga:
        $booking->load(['user', 'room', 'cabin']);

        // Jika user atau relasinya bisa null, berikan nilai default atau lakukan pengecekan
        $customerFirstName = $booking->user->name ?? $booking->contact_name;
        $customerEmail = $booking->user->email ?? $booking->contact_email;
        $customerPhone = $booking->contact_phone; // Sudah nullable di DB

        // Pastikan item_details memiliki array items jika lebih dari 1 item, atau array tunggal jika 1 item
        $itemDetails = [
            [
                'id'       => $booking->room->id_room ?? 'ROOM-UNKNOWN', // Fallback jika relasi room tidak ada
                'name'     => ($booking->room->typeroom ?? 'Unknown Room') . ' - ' . ($booking->cabin->name ?? 'Unknown Cabin'),
                'price'    => (int) ($booking->room->price ?? 0), // Pastikan int
                'quantity' => (int) $booking->total_nights,
            ]
        ];

        return [
            'transaction_details' => [
                'order_id'     => $payment->transaction_id,
                'gross_amount' => (int) $booking->total_price, // Pastikan ini integer
            ],
            'customer_details'    => [
                'first_name' => $customerFirstName,
                'email'      => $customerEmail,
                'phone'      => $customerPhone,
            ],
            'item_details'        => $itemDetails,
            'callbacks'           => [
                'finish'   => route('frontend.booking.show', $booking->id_booking),
                'unfinish' => route('frontend.booking.show', $booking->id_booking),
                'error'    => route('frontend.booking.show', $booking->id_booking),
            ],
            'custom_field1'       => (string) $payment->id_payment,
            'custom_field2'       => (string) $booking->id_booking,
        ];
    }

    /**
     * Handle notifikasi dari Midtrans.
     */
    public function handleNotification(Request $request)
    {
        try {
            // Ini akan membaca dari php://input secara otomatis
            $notif = new Notification();

            $transactionStatus = $notif->transaction_status;
            $fraudStatus = $notif->fraud_status;
            $orderId = $notif->order_id;
            $statusCode = $notif->status_code;
            $grossAmount = $notif->gross_amount;

            // Log payload notifikasi untuk debugging
            Log::info("Midtrans Notification Received: " . json_encode($notif));

            // Validasi Signature Key (SANGAT PENTING DI PRODUCTION)
            // Uncomment dan setel 'midtrans.server_key' di config/midtrans.php
            $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . Config::$serverKey);
            if ($notif->signature_key != $signatureKey) {
                Log::error("Invalid signature key for order_id: {$orderId}. Expected: {$signatureKey}, Received: {$notif->signature_key}");
                return response()->json(['message' => 'Invalid signature.'], 403);
            }

            // Cari payment berdasarkan transaction_id
            $payment = Payment::where('transaction_id', $orderId)->first();

            if (!$payment) {
                Log::warning("Webhook received for unknown transaction_id: {$orderId}");
                return response()->json(['message' => 'Transaction not found.'], 404);
            }

            // Hindari pemrosesan ganda jika status sudah final
            if (in_array($payment->status, ['completed', 'failed', 'cancelled', 'expired', 'rejected'])) {
                Log::info("Notification for order_id: {$orderId} already processed with status: {$payment->status}");
                return response()->json(['message' => 'Notification already processed.'], 200);
            }

            DB::transaction(function () use ($payment, $notif, $transactionStatus, $fraudStatus) {
                $newPaymentStatus = $payment->status; // Default to current status
                $booking = $payment->booking; // Asumsi ada relasi booking di model Payment
                $newBookingStatus = $booking->status; // Default to current booking status

                if ($transactionStatus == 'capture') {
                    if ($fraudStatus == 'challenge') {
                        $newPaymentStatus = 'challenge';
                        $newBookingStatus = 'challenge'; // Booking juga jadi challenge
                    } else if ($fraudStatus == 'accept') {
                        $newPaymentStatus = 'completed';
                        $newBookingStatus = 'confirmed'; // Booking dikonfirmasi jika pembayaran selesai
                    }
                } elseif ($transactionStatus == 'settlement') {
                    $newPaymentStatus = 'completed';
                    $newBookingStatus = 'confirmed'; // Booking dikonfirmasi
                } elseif ($transactionStatus == 'pending') {
                    $newPaymentStatus = 'pending';
                    $newBookingStatus = 'pending'; // Booking tetap pending
                } elseif ($transactionStatus == 'deny') {
                    $newPaymentStatus = 'failed';
                    $newBookingStatus = 'rejected'; // Booking ditolak
                } elseif ($transactionStatus == 'expire') {
                    $newPaymentStatus = 'expired';
                    $newBookingStatus = 'cancelled'; // Booking dibatalkan
                } elseif ($transactionStatus == 'cancel') {
                    $newPaymentStatus = 'cancelled';
                    $newBookingStatus = 'cancelled'; // Booking dibatalkan
                }

                $payment->update([
                    'status'         => $newPaymentStatus,
                    'payment_method' => $notif->payment_type,
                    'payment_details' => (array) $notif, // Simpan seluruh payload notif
                ]);

                if ($booking) {
                    // Hanya update status booking jika ada perubahan
                    if ($booking->status !== $newBookingStatus) {
                        $booking->update(['status' => $newBookingStatus]);
                        Log::info("Booking #{$booking->id_booking} status updated to '{$newBookingStatus}' from Midtrans webhook.");
                    }
                }
            });

            return response()->json(['message' => 'Notification processed successfully.'], 200);

        } catch (\Exception $e) {
            Log::error('Error handling Midtrans notification: ' . $e->getMessage(), [
                'order_id'        => $request->input('order_id') ?? 'N/A', // Gunakan request input untuk order_id jika notif object belum terisi
                'trace'           => $e->getTraceAsString(),
                'request_payload' => $request->all(), // Log entire request payload
            ]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * API untuk mendapatkan status pembayaran (berdasarkan Booking ID).
     * Akan digunakan oleh polling di frontend.
     */
    public function getPaymentStatus(Booking $booking)
    {
        // Pastikan hanya user yang berhak yang bisa mengakses
        if ($booking->id_user !== (Auth::user()->id_user ?? null)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Ambil status pembayaran terbaru untuk booking ini
        $latestPayment = $booking->payments()->latest()->first();

        return response()->json([
            'booking_status' => $booking->status, // Include booking status for reference
            'payment_status' => $latestPayment ? $latestPayment->status : 'no_payment_found',
            'status_label'   => $latestPayment ? $latestPayment->status_label : 'Belum Ada Pembayaran',
        ]);
    }

    public function changePaymentMethod(Request $request, Booking $booking)
    {
        // Validasi dasar
        if ($booking->id_user !== (Auth::user()->id_user ?? null)) {
            abort(403, 'Akses tidak diizinkan.');
        }
        if ($booking->isPaid()) {
            return redirect()->back()->withErrors(['error' => 'Booking ini sudah lunas, metode pembayaran tidak dapat diganti.']);
        }

        try {
            $payment = $booking->payments()->whereIn('status', ['pending', 'challenge'])->latest()->first();

            if ($payment) {
                DB::beginTransaction();
                // Membatalkan transaksi di Midtrans hanya jika statusnya memungkinkan
                try {
                    $midtransStatus = Transaction::status($payment->transaction_id);

                    // PERBAIKAN DI SINI: Cek apakah $midtransStatus adalah objek dan punya properti
                    if (is_object($midtransStatus) && isset($midtransStatus->transaction_status)) {
                        if (in_array($midtransStatus->transaction_status, ['pending', 'challenge'])) {
                            Transaction::cancel($payment->transaction_id);
                            Log::info("Midtrans transaction {$payment->transaction_id} cancelled by user request.");
                        } else {
                            Log::info("Midtrans transaction {$payment->transaction_id} not in cancellable state ({$midtransStatus->transaction_status}). Skipping API cancel.");
                        }
                    } else {
                        // Log jika respons dari Midtrans tidak sesuai format yang diharapkan
                        Log::warning("Midtrans status response for transaction {$payment->transaction_id} was not an object or missing transaction_status. Response: " . json_encode($midtransStatus));
                    }
                } catch (\Exception $midtransE) {
                    Log::warning("Failed to get status or cancel Midtrans transaction {$payment->transaction_id} via API: {$midtransE->getMessage()}");
                }

                $payment->update(['status' => 'cancelled']);
                DB::commit();
                Log::info("User requested to change payment method. Old payment #{$payment->id_payment} for booking #{$booking->id_booking} marked as cancelled in DB.");
            }

            return redirect()->route('frontend.payment.show', $booking->id_booking)
                ->with('success', 'Silakan pilih metode pembayaran yang baru.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error changing payment method for booking #{$booking->id_booking}: " . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Gagal mengganti metode pembayaran. Silakan coba lagi.']);
        }
    }
}