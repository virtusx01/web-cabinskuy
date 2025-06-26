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
            if ($booking->id_user !== Auth::id()) {
                abort(403, 'Anda tidak memiliki akses untuk mengakses pembayaran ini.');
            }

            // PERBAIKAN: Izinkan status 'pending' dan 'challenge' juga untuk pembayaran
            if (!in_array($booking->status, ['pending', 'challenge'])) {
                return redirect()->route('frontend.booking.show', $booking->id_booking)
                    ->withErrors(['error' => 'Booking ini tidak lagi dalam status menunggu pembayaran atau sudah selesai. Status saat ini: ' . $booking->status_label]);
            }

            $booking->load(['user', 'cabin', 'room']);

            return view('frontend.payment', [
                'booking' => $booking,
                'title' => 'Pembayaran Booking #' . $booking->id_booking
            ]);

        } catch (\Exception $e) {
            Log::error('Error showing payment form for booking ' . ($booking->id_booking ?? 'N/A') . ': ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('frontend.my-bookings')
                ->withErrors(['error' => 'Terjadi kesalahan saat memuat halaman pembayaran.']);
        }
    }

    /**
     * Memproses pembayaran untuk booking tertentu.
     */
     public function processPayment(Request $request, Booking $booking)
    {
        // Validasi dasar
        if ($booking->id_user !== Auth::id()) {
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
                Transaction::cancel($payment->transaction_id);
                $payment->update(['status' => 'cancelled']);
                Log::info("User forced new payment. Old payment #{$payment->id_payment} cancelled.");
                $payment = null; // Set ke null agar transaksi baru dibuat
            }
            
            // Jika tidak ada payment pending, atau dipaksa buat baru, maka buat transaksi baru
            if (!$payment) {
                $midtransOrderId = $booking->id_booking . '-' . time(); // Gunakan time() agar lebih singkat

                $payment = Payment::create([
                    'id_booking'     => $booking->id_booking,
                    'amount'         => $booking->total_price,
                    'transaction_id' => $midtransOrderId,
                    'status'         => 'pending',
                    'id_user'        => Auth::id(),
                ]);

                $params = $this->buildMidtransParameters($booking, $payment);
                $snapToken = Snap::getSnapToken($params);

                $booking->update(['snap_token' => $snapToken]);
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
        return [
            'transaction_details' => [
                'order_id' => $payment->transaction_id,
                'gross_amount' => (int) $booking->total_price,
            ],
            'customer_details' => [
                'first_name' => $booking->user->name ?? $booking->contact_name,
                'email' => $booking->user->email ?? $booking->contact_email,
                'phone' => $booking->contact_phone,
            ],
            'item_details' => [[
                'id' => $booking->room->id_room,
                'name' => $booking->room->typeroom . ' - ' . $booking->cabin->name,
                'price' => (int) $booking->room->price,
                'quantity' => (int) $booking->total_nights,
            ]],
            'callbacks' => [
                'finish' => route('frontend.booking.show', $booking->id_booking),
            ],
            'custom_field1' => (string) $payment->id_payment,
            'custom_field2' => (string) $booking->id_booking,
        ];
    }

    /**
     * Handle notifikasi dari Midtrans (disederhanakan).
     */
    public function handleNotification(Request $request)
    {
        try {
            $notif = new Notification();
            
            $payment = Payment::where('transaction_id', $notif->order_id)->first();

            if (!$payment) {
                Log::warning("Webhook received for unknown transaction_id: {$notif->order_id}");
                return response()->json(['message' => 'Transaction not found.'], 404);
            }
            
            // Cek signature key (SANGAT PENTING DI PRODUCTION)
            // $signatureKey = hash('sha512', $notif->order_id . $notif->status_code . $notif->gross_amount . config('midtrans.server_key'));
            // if ($notif->signature_key != $signatureKey) {
            //     Log::error("Invalid signature key for order_id: {$notif->order_id}");
            //     return response()->json(['message' => 'Invalid signature.'], 403);
            // }

            // Logika utama: Update record Payment. Observer akan menangani sisanya.
            DB::transaction(function () use ($payment, $notif) {
                $newStatus = 'pending';
                if ($notif->transaction_status == 'capture' || $notif->transaction_status == 'settlement') {
                    $newStatus = ($notif->fraud_status == 'challenge') ? 'challenge' : 'completed';
                } elseif (in_array($notif->transaction_status, ['cancel', 'deny'])) {
                    $newStatus = 'failed';
                } elseif ($notif->transaction_status == 'expire') {
                    $newStatus = 'expired';
                }

                // Update payment record. Observer akan dipicu dari sini.
                $payment->update([
                    'status' => $newStatus,
                    'payment_method' => $notif->payment_type,
                    'payment_details' => (array) $notif,
                ]);
            });

            return response()->json(['message' => 'Notification processed successfully.'], 200);

        } catch (\Exception $e) {
            Log::error('Error handling Midtrans notification: ' . $e->getMessage(), [
                'order_id' => $notif->order_id ?? 'N/A',
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function getBookingStatus(Booking $booking)
    {
        // Pastikan hanya user yang berhak yang bisa mengakses
        if ($booking->id_user !== auth()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'status' => $booking->status,
            'status_label' => $booking->status_label, // Asumsi Anda punya accessor 'status_label' di model Booking
        ]);
    }
    
    public function changePaymentMethod(Request $request, Booking $booking)
    {
        // Validasi dasar
        if ($booking->id_user !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan.');
        }
        if ($booking->isPaid()) {
            return redirect()->back()->withErrors(['error' => 'Booking ini sudah lunas, metode pembayaran tidak dapat diganti.']);
        }

        try {
            // Cari pembayaran terakhir yang masih pending atau challenge
            $payment = $booking->payments()->whereIn('status', ['pending', 'challenge'])->latest()->first();

            if ($payment) {
                DB::beginTransaction();

                // 1. Batalkan transaksi di Midtrans
                Transaction::cancel($payment->transaction_id);

                // 2. Update status payment di database kita menjadi 'cancelled'
                // Observer akan menangani update status booking secara otomatis.
                $payment->update(['status' => 'cancelled']);

                DB::commit();
                Log::info("User requested to change payment method. Old payment #{$payment->id_payment} for booking #{$booking->id_booking} cancelled.");
            }

            // 3. Arahkan pengguna ke halaman pembayaran untuk membuat transaksi baru
            return redirect()->route('frontend.payment.show', $booking->id_booking)
                            ->with('success', 'Silakan pilih metode pembayaran yang baru.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error changing payment method for booking #{$booking->id_booking}: " . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Gagal mengganti metode pembayaran. Silakan coba lagi.']);
        }
    }
}