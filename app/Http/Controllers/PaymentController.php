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
        // Set Midtrans configuration from Laravel's config files
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Menampilkan halaman pembayaran untuk booking tertentu.
     *
     * @param \App\Models\Booking $booking
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showPaymentForm(Booking $booking)
    {
        try {
            // Ensure the authenticated user owns this booking
            if ($booking->id_user !== (Auth::user()->id_user ?? null)) {
                abort(403, 'Anda tidak memiliki akses untuk mengakses pembayaran ini.');
            }

            // Prevent access if booking is not in a payable status
            if (!in_array($booking->status, ['pending', 'challenge'])) {
                return redirect()->route('frontend.booking.show', $booking->id_booking)
                    ->withErrors(['error' => 'Booking ini tidak lagi dalam status menunggu pembayaran atau sudah selesai. Status saat ini: ' . $booking->status_label]);
            }

            // Load necessary relationships for the view
            $booking->load(['user', 'cabin', 'room', 'payments']);

            // Get the latest payment that is still 'pending' or 'challenge'
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
     * Initiates a Midtrans transaction or retrieves an existing one.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Booking $booking
     * @return \Illuminate\Http\JsonResponse
     */
    public function processPayment(Request $request, Booking $booking)
    {
        // Basic validation for user access and booking status
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

            // If a new transaction is requested (e.g., change payment method) and there's an existing pending transaction
            if ($forceNew && $payment) {
                // Attempt to cancel the old transaction in Midtrans and update DB
                try {
                    $midtransStatus = Transaction::status($payment->transaction_id);

                    // Check if Midtrans status is an object and has the transaction_status property
                    if (is_object($midtransStatus) && isset($midtransStatus->transaction_status)) {
                        if (in_array($midtransStatus->transaction_status, ['pending', 'challenge'])) {
                            Transaction::cancel($payment->transaction_id);
                            Log::info("Midtrans transaction {$payment->transaction_id} successfully cancelled from processPayment (forceNew).");
                        } else {
                            Log::info("Midtrans transaction {$payment->transaction_id} not in cancellable state for forceNew ({$midtransStatus->transaction_status}). Skipping API cancel.");
                        }
                    } else {
                        // Log if Midtrans status response is not in the expected format
                        Log::warning("Midtrans status response for transaction {$payment->transaction_id} was not an object or missing transaction_status in processPayment (forceNew). Response: " . json_encode($midtransStatus));
                    }
                } catch (\Exception $midtransE) {
                    Log::warning("Failed to get status or cancel Midtrans transaction {$payment->transaction_id} in processPayment (forceNew) via API: {$midtransE->getMessage()}");
                }

                $payment->update(['status' => 'cancelled']);
                Log::info("User forced new payment. Old payment #{$payment->id_payment} cancelled in DB.");
                $payment = null; // Set to null to trigger new transaction creation
            }

            // If no pending payment exists, or a new one was forced, create a new transaction
            if (!$payment) {
                $midtransOrderId = $booking->id_booking . Str::random(5) . time(); // More descriptive order ID

                $payment = Payment::create([
                    'id_booking'     => $booking->id_booking,
                    'amount'         => $booking->total_price, // total_price should already include tax
                    'transaction_id' => $midtransOrderId,
                    'status'         => 'pending',
                    'id_user'        => Auth::user()->id_user,
                ]);

                $params = $this->buildMidtransParameters($booking, $payment);
                $snapToken = Snap::getSnapToken($params);

                $booking->update(['snap_token' => $snapToken]); // Update booking with the new snap token
            } else {
                // If a pending payment already exists, reuse its snap_token
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
     * Helper method to build Midtrans parameters, including tax as a separate item.
     *
     * @param \App\Models\Booking $booking
     * @param \App\Models\Payment $payment
     * @return array
     */
    private function buildMidtransParameters(Booking $booking, Payment $payment): array
    {
        // Ensure user, room, and cabin relationships are loaded
        $booking->load(['user', 'room', 'cabin']);

        // Default values for customer details if relationships are null
        $customerFirstName = $booking->user->name ?? $booking->contact_name;
        $customerEmail = $booking->user->email ?? $booking->contact_email;
        $customerPhone = $booking->contact_phone;

        // Calculate subtotal and tax amount
        $roomPricePerNight = (int) ($booking->room->price ?? 0);
        $totalNights = (int) $booking->total_nights;
        $subtotal = $roomPricePerNight * $totalNights;
        $taxRate = 0.05; // 5% tax
        $taxAmount = round($subtotal * $taxRate); // Round tax amount to nearest integer

        // Ensure the gross_amount matches the sum of item_details
        // The booking->total_price should already be (subtotal + taxAmount)
        $expectedGrossAmount = $subtotal + $taxAmount;

        $itemDetails = [
            [
                'id'       => $booking->room->id_room ?? 'ROOM-UNKNOWN',
                'name'     => ($booking->room->typeroom ?? 'Unknown Room') . ' - ' . ($booking->cabin->name ?? 'Unknown Cabin'),
                'price'    => $roomPricePerNight,
                'quantity' => $totalNights,
            ],
            [
                'id'       => 'TAX-5PERCENT',
                'name'     => 'Pajak & Biaya Layanan (5%)',
                'price'    => $taxAmount, // Tax amount as a separate item
                'quantity' => 1,
            ]
        ];

        return [
            'transaction_details' => [
                'order_id'     => $payment->transaction_id,
                'gross_amount' => (int) $expectedGrossAmount, // Ensure this is the final total including tax
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
     * Handle notifications from Midtrans webhook.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleNotification(Request $request)
    {
        try {
            // Read notification payload automatically
            $notif = new Notification();

            $transactionStatus = $notif->transaction_status;
            $fraudStatus = $notif->fraud_status;
            $orderId = $notif->order_id;
            $statusCode = $notif->status_code;
            $grossAmount = $notif->gross_amount;

            // Log notification payload for debugging
            Log::info("Midtrans Notification Received: " . json_encode($notif));

            // Validate Signature Key (CRUCIAL IN PRODUCTION)
            $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . Config::$serverKey);
            if ($notif->signature_key != $signatureKey) {
                Log::error("Invalid signature key for order_id: {$orderId}. Expected: {$signatureKey}, Received: {$notif->signature_key}");
                return response()->json(['message' => 'Invalid signature.'], 403);
            }

            // Find payment by transaction_id
            $payment = Payment::where('transaction_id', $orderId)->first();

            if (!$payment) {
                Log::warning("Webhook received for unknown transaction_id: {$orderId}");
                return response()->json(['message' => 'Transaction not found.'], 404);
            }

            // Prevent double processing if status is already final
            if (in_array($payment->status, ['completed', 'failed', 'cancelled', 'expired', 'rejected'])) {
                Log::info("Notification for order_id: {$orderId} already processed with status: {$payment->status}");
                return response()->json(['message' => 'Notification already processed.'], 200);
            }

            DB::transaction(function () use ($payment, $notif, $transactionStatus, $fraudStatus) {
                $newPaymentStatus = $payment->status; // Default to current status
                $booking = $payment->booking; // Assuming a booking relationship in Payment model
                $newBookingStatus = $booking->status; // Default to current booking status

                if ($transactionStatus == 'capture') {
                    if ($fraudStatus == 'challenge') {
                        $newPaymentStatus = 'challenge';
                        $newBookingStatus = 'challenge'; // Booking also becomes challenge
                    } else if ($fraudStatus == 'accept') {
                        $newPaymentStatus = 'completed';
                        $newBookingStatus = 'confirmed'; // Booking confirmed if payment completed
                    }
                } elseif ($transactionStatus == 'settlement') {
                    $newPaymentStatus = 'completed';
                    $newBookingStatus = 'confirmed'; // Booking confirmed
                } elseif ($transactionStatus == 'pending') {
                    $newPaymentStatus = 'pending';
                    $newBookingStatus = 'pending'; // Booking remains pending
                } elseif ($transactionStatus == 'deny') {
                    $newPaymentStatus = 'failed';
                    $newBookingStatus = 'rejected'; // Booking rejected
                } elseif ($transactionStatus == 'expire') {
                    $newPaymentStatus = 'expired';
                    $newBookingStatus = 'cancelled'; // Booking cancelled
                } elseif ($transactionStatus == 'cancel') {
                    $newPaymentStatus = 'cancelled';
                    $newBookingStatus = 'cancelled'; // Booking cancelled
                }

                $payment->update([
                    'status'         => $newPaymentStatus,
                    'payment_method' => $notif->payment_type,
                    'payment_details' => (array) $notif, // Store entire notification payload
                ]);

                if ($booking) {
                    // Only update booking status if there's a change
                    if ($booking->status !== $newBookingStatus) {
                        $booking->update(['status' => $newBookingStatus]);
                        Log::info("Booking #{$booking->id_booking} status updated to '{$newBookingStatus}' from Midtrans webhook.");
                    }
                }
            });

            return response()->json(['message' => 'Notification processed successfully.'], 200);

        } catch (\Exception $e) {
            Log::error('Error handling Midtrans notification: ' . $e->getMessage(), [
                'order_id'        => $request->input('order_id') ?? 'N/A', // Use request input for order_id if notif object not filled
                'trace'           => $e->getTraceAsString(),
                'request_payload' => $request->all(), // Log entire request payload
            ]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * API to get payment status (based on Booking ID).
     * Used for frontend polling.
     *
     * @param \App\Models\Booking $booking
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaymentStatus(Booking $booking)
    {
        // Ensure only authorized user can access
        if ($booking->id_user !== (Auth::user()->id_user ?? null)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get the latest payment status for this booking
        $latestPayment = $booking->payments()->latest()->first();

        return response()->json([
            'booking_status' => $booking->status, // Include booking status for reference
            'payment_status' => $latestPayment ? $latestPayment->status : 'no_payment_found',
            'status_label'   => $latestPayment ? $latestPayment->status_label : 'Belum Ada Pembayaran',
        ]);
    }

    /**
     * Allows a user to change their payment method by cancelling the previous pending transaction.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Booking $booking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePaymentMethod(Request $request, Booking $booking)
    {
        // Basic validation
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
                // Cancel transaction in Midtrans only if its status allows
                try {
                    $midtransStatus = Transaction::status($payment->transaction_id);

                    // Check if Midtrans status is an object and has the transaction_status property
                    if (is_object($midtransStatus) && isset($midtransStatus->transaction_status)) {
                        if (in_array($midtransStatus->transaction_status, ['pending', 'challenge'])) {
                            Transaction::cancel($payment->transaction_id);
                            Log::info("Midtrans transaction {$payment->transaction_id} cancelled by user request.");
                        } else {
                            Log::info("Midtrans transaction {$payment->transaction_id} not in cancellable state ({$midtransStatus->transaction_status}). Skipping API cancel.");
                        }
                    } else {
                        // Log if Midtrans response is not in the expected format
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
