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
use Illuminate\Support\Str; // Penting: Tambahkan Str Facade

class PaymentController extends Controller
{
    public function __construct()
    {
        // Konfigurasi Midtrans dari config/midtrans.php
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
            // Otorisasi: Pastikan user yang login adalah pemilik booking
            if ($booking->id_user !== (Auth::user()->id_user ?? null)) {
                abort(403, 'Anda tidak memiliki akses untuk mengakses pembayaran ini.');
            }

            // Validasi status booking agar hanya status tertentu yang bisa dibayar
            if (!in_array($booking->status, ['pending', 'challenge'])) {
                return redirect()->route('frontend.booking.show', $booking->id_booking)
                    ->withErrors(['error' => 'Booking ini tidak lagi dalam status menunggu pembayaran atau sudah selesai. Status saat ini: ' . $booking->status_label]);
            }

            // Load relasi yang diperlukan untuk tampilan
            $booking->load(['user', 'cabin', 'room']);

            return view('frontend.payment', [
                'booking' => $booking,
                'title' => 'Pembayaran Booking #' . $booking->id_booking
            ]);

        } catch (\Exception $e) {
            Log::error('Error showing payment form for booking ' . ($booking->id_booking ?? 'N/A') . ': ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('frontend.booking.index')
                ->withErrors(['error' => 'Terjadi kesalahan saat memuat halaman pembayaran.']);
        }
    }

    /**
     * Memproses pembayaran untuk booking tertentu.
     * Mengembalikan snap token untuk inisiasi pop-up Midtrans.
     */
    public function processPayment(Request $request, Booking $booking)
    {
        // Otorisasi: Pastikan user yang login adalah pemilik booking
        if ($booking->id_user !== (Auth::user()->id_user ?? null)) {
            return response()->json(['error' => 'Akses tidak diizinkan.'], 403);
        }
        // Validasi: Jika booking sudah lunas, tidak perlu proses pembayaran lagi
        if ($booking->isPaid()) {
            return response()->json(['error' => 'Booking ini sudah lunas.'], 400);
        }

        DB::beginTransaction();
        try {
            // Cari pembayaran terakhir yang masih pending atau challenge
            $payment = $booking->payments()->whereIn('status', ['pending', 'challenge'])->latest()->first();
            $forceNew = $request->input('force_new', false); // Digunakan untuk 'Ganti Metode Pembayaran'

            // Jika diminta membuat transaksi baru (Ganti Metode Pembayaran) dan ada transaksi pending sebelumnya
            if ($forceNew && $payment) {
                // Coba batalkan transaksi lama di Midtrans via API
                try {
                    $midtransStatus = Transaction::status($payment->transaction_id);

                    if (is_object($midtransStatus) && isset($midtransStatus->transaction_status)) {
                        if (in_array($midtransStatus->transaction_status, ['pending', 'challenge'])) {
                            Transaction::cancel($payment->transaction_id);
                            Log::info("Midtrans transaction {$payment->transaction_id} successfully cancelled from processPayment (forceNew).");
                        } else {
                            Log::info("Midtrans transaction {$payment->transaction_id} not in cancellable state for forceNew ({$midtransStatus->transaction_status}). Skipping API cancel.");
                        }
                    } else {
                        Log::warning("Midtrans status response for transaction {$payment->transaction_id} was not an object or missing transaction_status in processPayment (forceNew). Response: " . json_encode($midtransStatus));
                    }
                } catch (\Exception $midtransE) {
                    Log::warning("Failed to get status or cancel Midtrans transaction {$payment->transaction_id} in processPayment (forceNew) via API: {$midtransE->getMessage()}");
                }
                
                // Update status payment lama di DB lokal menjadi 'cancelled'
                $payment->update(['status' => 'cancelled']);
                Log::info("User forced new payment. Old payment #{$payment->id_payment} cancelled in DB.");
                $payment = null; // Set ke null agar transaksi baru akan dibuat
            }
            
            // Jika tidak ada payment pending, atau dipaksa buat baru, maka buat transaksi baru
            if (!$payment) {
                // Buat Order ID unik untuk Midtrans
                $midtransOrderId = $booking->id_booking . '-' . Str::random(5) . '-' . time();

                // Buat record Payment baru di DB lokal
                $payment = Payment::create([
                    'id_booking'     => $booking->id_booking,
                    'amount'         => $booking->total_price,
                    'transaction_id' => $midtransOrderId,
                    'status'         => 'pending', // Status awal pembayaran di DB lokal
                    'id_user'        => Auth::user()->id_user, // Menggunakan id_user string dari user yang login
                ]);

                // Bangun parameter untuk Midtrans Snap dan dapatkan snap token
                $params = $this->buildMidtransParameters($booking, $payment);
                $snapToken = Snap::getSnapToken($params);

                // Simpan snap token ke booking agar bisa digunakan jika perlu
                $booking->update(['snap_token' => $snapToken]);
            } else {
                // Jika sudah ada payment pending (dan tidak dipaksa buat baru), gunakan snap_token yang sudah ada
                $snapToken = $booking->snap_token;
            }

            DB::commit(); // Commit transaksi database
            return response()->json(['snap_token' => $snapToken]); // Kirim snap token ke frontend

        } catch (\Exception $e) {
            DB::rollback(); // Rollback transaksi database jika ada error
            Log::error('Error processing payment for booking #' . $booking->id_booking . ': ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Method helper untuk membangun parameter Midtrans.
     * Pastikan relasi user, room, dan cabin sudah di-load di method pemanggil.
     */
    private function buildMidtransParameters(Booking $booking, Payment $payment): array
    {
        // Pastikan relasi user, room, dan cabin sudah di-load di showPaymentForm atau metode pemanggil lainnya
        // Jika tidak, Anda mungkin perlu me-loadnya di sini:
        // $booking->load(['user', 'room', 'cabin']);

        $customerFirstName = $booking->user->name ?? $booking->contact_name;
        $customerEmail = $booking->user->email ?? $booking->contact_email;
        $customerPhone = $booking->contact_phone;

        $itemDetails = [
            [
                'id'       => $booking->room->id_room ?? 'ROOM-UNKNOWN',
                'name'     => ($booking->room->typeroom ?? 'Unknown Room') . ' - ' . ($booking->cabin->name ?? 'Unknown Cabin'),
                'price'    => (int) ($booking->room->price ?? 0),
                'quantity' => (int) $booking->total_nights,
            ]
        ];

        return [
            'transaction_details' => [
                'order_id'     => $payment->transaction_id,
                'gross_amount' => (int) $booking->total_price,
            ],
            'customer_details' => [
                'first_name' => $customerFirstName,
                'email'      => $customerEmail,
                'phone'      => $customerPhone,
            ],
            'item_details' => $itemDetails,
            'callbacks' => [
                // URL Finish, Unfinish, dan Error adalah opsional.
                // Jika tidak diset, user akan dikembalikan ke URL asal pop-up.
                // Jika diset, Midtrans akan redirect ke URL ini setelah interaksi.
                'finish'   => route('frontend.booking.show', $booking->id_booking),
                // 'unfinish' => route('frontend.booking.show', $booking->id_booking),
                // 'error'    => route('frontend.booking.show', $booking->id_booking),
            ],
            'custom_field1' => (string) $payment->id_payment, // Contoh custom field
            'custom_field2' => (string) $booking->id_booking, // Contoh custom field
        ];
    }

    /**
     * Handle notifikasi dari Midtrans (Webhook).
     * Dipanggil oleh server Midtrans.
     */
    public function handleNotification(Request $request)
    {
        try {
            // Inisialisasi Midtrans Notification. Ini akan membaca payload dari php://input.
            $notif = new Notification();
            
            $transactionStatus = $notif->transaction_status;
            $fraudStatus = $notif->fraud_status;
            $orderId = $notif->order_id;
            $statusCode = $notif->status_code;
            $grossAmount = $notif->gross_amount;

            // Log payload notifikasi untuk debugging
            Log::info("Midtrans Notification Received: " . json_encode($notif));

            // Validasi Signature Key (SANGAT PENTING DI PRODUCTION)
            $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . Config::$serverKey);
            if ($notif->signature_key != $signatureKey) {
                Log::error("Invalid signature key for order_id: {$orderId}. Expected: {$signatureKey}, Received: {$notif->signature_key}. Request Payload: " . json_encode($request->all()));
                return response()->json(['message' => 'Invalid signature.'], 403);
            }

            // Cari payment berdasarkan transaction_id (order_id dari Midtrans)
            $payment = Payment::where('transaction_id', $orderId)->first();

            if (!$payment) {
                Log::warning("Webhook received for unknown transaction_id: {$orderId}. Request Payload: " . json_encode($request->all()));
                return response()->json(['message' => 'Transaction not found.'], 404);
            }

            // Hindari pemrosesan ganda jika status sudah final di DB lokal
            if (in_array($payment->status, ['completed', 'failed', 'cancelled', 'expired', 'rejected'])) {
                Log::info("Notification for order_id: {$orderId} already processed with status: {$payment->status}. Skipping further processing.");
                return response()->json(['message' => 'Notification already processed.'], 200);
            }
            
            DB::transaction(function () use ($payment, $notif, $transactionStatus, $fraudStatus) {
                $newPaymentStatus = $payment->status; // Default ke status saat ini

                // Tentukan status pembayaran baru berdasarkan notifikasi Midtrans
                if ($transactionStatus == 'capture') { // Untuk kartu kredit atau e-money yang langsung sukses
                    if ($fraudStatus == 'challenge') {
                        $newPaymentStatus = 'challenge';
                    } else if ($fraudStatus == 'accept') {
                        $newPaymentStatus = 'completed';
                    }
                } elseif ($transactionStatus == 'settlement') { // Untuk metode pembayaran non-kartu kredit yang sukses
                    $newPaymentStatus = 'completed';
                } elseif ($transactionStatus == 'pending') {
                    $newPaymentStatus = 'pending';
                } elseif ($transactionStatus == 'deny') {
                    $newPaymentStatus = 'failed';
                } elseif ($transactionStatus == 'expire') {
                    $newPaymentStatus = 'expired';
                } elseif ($transactionStatus == 'cancel') {
                    $newPaymentStatus = 'cancelled';
                }

                // Update record Payment di database lokal
                $payment->update([
                    'status'         => $newPaymentStatus,
                    'payment_method' => $notif->payment_type,
                    'payment_details' => (array) $notif, // Simpan seluruh payload notif dari Midtrans
                ]);

                // Opsional: Update status booking berdasarkan status payment terbaru
                $booking = $payment->booking; // Asumsi ada relasi booking di model Payment
                if ($booking) {
                    if ($newPaymentStatus === 'completed') {
                        $booking->markAsCompleted(); // Method di model Booking untuk set status 'completed'
                    } elseif (in_array($newPaymentStatus, ['cancelled', 'expired', 'failed'])) {
                        // Jika payment gagal/kadaluarsa/dibatalkan, batalkan booking jika belum final
                        if (!in_array($booking->status, ['confirmed', 'completed', 'rejected'])) {
                            $booking->cancel('Pembayaran gagal atau kadaluarsa oleh sistem Midtrans.'); // Method di model Booking
                        }
                    }
                    // Anda bisa menambahkan logika lain untuk status 'challenge' atau 'pending'
                }
            });

            return response()->json(['message' => 'Notification processed successfully.'], 200);

        } catch (\Exception $e) {
            Log::error('Error handling Midtrans notification: ' . $e->getMessage(), [
                'order_id' => $request->input('order_id') ?? ($notif->order_id ?? 'N/A'),
                'trace' => $e->getTraceAsString(),
                'request_payload' => $request->all(),
            ]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
    
    /**
     * Mengubah metode pembayaran untuk booking.
     * Akan membatalkan transaksi Midtrans yang lama dan mengarahkan ke halaman pembayaran baru.
     */
    public function changePaymentMethod(Request $request, Booking $booking)
    {
        // Otorisasi: Pastikan user yang login adalah pemilik booking
        if ($booking->id_user !== (Auth::user()->id_user ?? null)) {
            abort(403, 'Akses tidak diizinkan.');
        }
        // Validasi: Jika booking sudah lunas, tidak bisa ganti metode pembayaran
        if ($booking->isPaid()) {
            return redirect()->back()->withErrors(['error' => 'Booking ini sudah lunas, metode pembayaran tidak dapat diganti.']);
        }

        DB::beginTransaction();
        try {
            // Cari pembayaran terakhir yang masih pending atau challenge
            $payment = $booking->payments()->whereIn('status', ['pending', 'challenge'])->latest()->first();

            if ($payment) {
                // Membatalkan transaksi di Midtrans hanya jika statusnya memungkinkan
                try {
                    $midtransStatus = Transaction::status($payment->transaction_id);

                    if (is_object($midtransStatus) && isset($midtransStatus->transaction_status)) {
                        if (in_array($midtransStatus->transaction_status, ['pending', 'challenge'])) {
                            Transaction::cancel($payment->transaction_id);
                            Log::info("Midtrans transaction {$payment->transaction_id} cancelled by user request.");
                        } else {
                            Log::info("Midtrans transaction {$payment->transaction_id} not in cancellable state ({$midtransStatus->transaction_status}). Skipping API cancel.");
                        }
                    } else {
                        Log::warning("Midtrans status response for transaction {$payment->transaction_id} was not an object or missing transaction_status. Response: " . json_encode($midtransStatus));
                    }
                } catch (\Exception $midtransE) {
                    Log::warning("Failed to get status or cancel Midtrans transaction {$payment->transaction_id} via API: {$midtransE->getMessage()}");
                }

                // Update status payment di database lokal menjadi 'cancelled'
                $payment->update(['status' => 'cancelled']);
                Log::info("User requested to change payment method. Old payment #{$payment->id_payment} for booking #{$booking->id_booking} marked as cancelled in DB.");
            }

            DB::commit();
            // Arahkan pengguna ke halaman pembayaran untuk membuat transaksi baru
            return redirect()->route('frontend.payment.show', $booking->id_booking)
                ->with('success', 'Silakan pilih metode pembayaran yang baru.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error changing payment method for booking #{$booking->id_booking}: " . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Gagal mengganti metode pembayaran. Silakan coba lagi.']);
        }
    }
}