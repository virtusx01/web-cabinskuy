<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str; // Still needed for `generateUniqueToken` if you keep it.
use Illuminate\Support\Facades\DB;
use chillerlan\QRCode\QRCode; // Not strictly needed if this controller doesn't render QR images
use chillerlan\QRCode\QROptions;

use Barryvdh\DomPDF\Facade\Pdf; // Not strictly needed if this controller doesn't render QR images

class QRCodeController extends Controller
{
    /**
     * The `generateQRToken` method's logic (generating and assigning token to booking)
     * has been moved to the Booking model's `generateAndGetQrTokenUrl()` method.
     * This method could still be used as an API endpoint if you want to explicitly
     * trigger token generation for a booking, but it would call the Booking model's method.
     * I'm keeping a simplified version for demonstration, but you might remove it
     * if its only consumer was `BookingController::show`.
     */
    public function generateQRToken(Booking $booking)
    {
        $qrUrl = $booking->generateAndGetQrTokenUrl();
        if ($qrUrl) {
            return response()->json([
                'success' => true,
                'token' => $booking->qr_validation_token,
                'qr_url' => $qrUrl
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'QR Code can only be generated for confirmed and paid bookings.'
        ], 400);
    }


    /**
     * Validate QR Code and display the detail page (frontend view)
     */
    public function validateQRCode($token)
    {
        try {
            // Find booking by token
            $booking = Booking::with(['cabin', 'room', 'user', 'latestPayment'])
                ->where('qr_validation_token', $token)
                ->first();

            if (!$booking) {
                return $this->showValidationPage([
                    'status' => 'invalid',
                    'title' => 'Invalid QR Code',
                    'message' => 'The QR Code you scanned is invalid or no longer valid.',
                    'booking' => null
                ]);
            }

            // Additional validation: ensure booking is still confirmed
            if ($booking->status !== 'confirmed') {
                return $this->showValidationPage([
                    'status' => 'unverified',
                    'title' => 'Booking Not Confirmed',
                    'message' => 'This booking has not been confirmed or its status has changed.',
                    'booking' => $booking
                ]);
            }

            // Payment validation
            $latestPayment = $booking->latestPayment;
            if (!$latestPayment || $latestPayment->status !== 'completed') { // Assuming 'completed' means successful payment
                return $this->showValidationPage([
                    'status' => 'unverified',
                    'title' => 'Payment Not Cleared',
                    'message' => 'Payment for this booking has not been cleared or confirmed.',
                    'booking' => $booking
                ]);
            }

            // Check-in date validation
            $checkInDate = \Carbon\Carbon::parse($booking->check_in_date);
            $today = \Carbon\Carbon::today();

            // For example, QR Code is valid from 1 day before check-in until check-out
            if ($today->lt($checkInDate->copy()->subDay()->startOfDay()) || $today->gt(\Carbon\Carbon::parse($booking->check_out_date)->endOfDay())) {
                return $this->showValidationPage([
                    'status' => 'unverified',
                    'title' => 'QR Code Not Yet / No Longer Valid',
                    'message' => 'This QR Code is only valid from 1 day before check-in until the check-out date.',
                    'booking' => $booking
                ]);
            }

            // All validations passed
            return $this->showValidationPage([
                'status' => 'verified',
                'title' => 'Booking Verified',
                'message' => 'This booking has been verified by the system and payment is complete.',
                'booking' => $booking
            ]);

        } catch (\Exception $e) {
            Log::error('Error validating QR code: ' . $e->getMessage(), ['token' => $token]);

            return $this->showValidationPage([
                'status' => 'error',
                'title' => 'An Error Occurred',
                'message' => 'An error occurred while validating the QR Code. Please try again or contact customer service.',
                'booking' => null
            ]);
        }
    }

    /**
     * API endpoint for QR Code validation (for mobile app or AJAX)
     */
    public function validateQRCodeAPI($token)
    {
        try {
            $booking = Booking::with(['cabin', 'room', 'latestPayment'])
                ->where('qr_validation_token', $token)
                ->first();

            if (!$booking) {
                return response()->json([
                    'valid' => false,
                    'status' => 'invalid',
                    'message' => 'QR Code is invalid or no longer valid.'
                ], 404);
            }

            // Validate booking status
            if ($booking->status !== 'confirmed') {
                return response()->json([
                    'valid' => false,
                    'status' => 'unverified',
                    'message' => 'Booking not confirmed.',
                    'booking_status' => $booking->status
                ], 400);
            }

            // Payment validation
            $latestPayment = $booking->latestPayment;
            if (!$latestPayment || $latestPayment->status !== 'completed') {
                return response()->json([
                    'valid' => false,
                    'status' => 'unverified',
                    'message' => 'Payment not cleared.',
                    'payment_status' => $latestPayment->status ?? 'no_payment'
                ], 400);
            }

            // Date validation
            $checkInDate = \Carbon\Carbon::parse($booking->check_in_date);
            $today = \Carbon\Carbon::today();

            if ($today->lt($checkInDate->copy()->subDay()->startOfDay()) || $today->gt(\Carbon\Carbon::parse($booking->check_out_date)->endOfDay())) {
                return response()->json([
                    'valid' => false,
                    'status' => 'expired',
                    'message' => 'QR Code is no longer valid for this date.',
                    'valid_from' => $checkInDate->subDay()->toDateString(),
                    'valid_until' => $booking->check_out_date
                ], 400);
            }

            // Return verified booking data
            return response()->json([
                'valid' => true,
                'status' => 'verified',
                'message' => 'Booking verified.',
                'data' => [
                    'id_booking' => $booking->id_booking,
                    'contact_name' => $booking->name,
                    'cabin_name' => $booking->cabin->cabin_name ?? 'N/A',
                    'room_name' => $booking->room->room_name ?? 'N/A',
                    'total_guests' => $booking->total_guests,
                    'check_in_date' => $booking->check_in_date,
                    'check_out_date' => $booking->check_out_date,
                    'total_nights' => $booking->total_nights,
                    'total_price' => $booking->total_price,
                    'booking_status' => $booking->status_label,
                    'payment_status' => $latestPayment->status_label ?? 'N/A'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in QR validation API: ' . $e->getMessage(), ['token' => $token]);

            return response()->json([
                'valid' => false,
                'status' => 'error',
                'message' => 'An error occurred during validation.'
            ], 500);
        }
    }

    /**
     * Invalidate QR token (e.g., when a booking is cancelled)
     * This method is still useful as an external trigger if needed,
     * but the primary method for this should be on the Booking model now.
     */
    public function invalidateQRToken(Booking $booking)
    {
        try {
            $booking->invalidateQrToken(); // Call the model method
            return response()->json([
                'success' => true,
                'message' => 'QR Token invalidated successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error invalidating QR token: ' . $e->getMessage(), ['booking_id' => $booking->id_booking]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to invalidate QR token.'
            ], 500);
        }
    }

    /**
     * Display the QR Code validation page
     */
    public function showValidationPage($data)
    {
        return view('frontend.qr-validation', $data);
    }

    /**
     * Generate a unique token string. This could be public or protected.
     * Its primary consumer is now the Booking model.
     * If you only need it for the Booking model, consider making it private or
     * moving it into a helper or trait. For now, keep it here.
     */
    public function generateUniqueToken()
    {
        do {
            // Generate token with format: QR + timestamp + random string
            $token = 'QR' . time() . Str::random(16);
        } while (Booking::where('qr_validation_token', $token)->exists());

        return $token;
    }

    /**
     * The `getQRCodeUrl` method has been removed from here
     * and its core logic moved to the Booking model.
     */

    /**
     * Bulk invalidate expired QR tokens (for cron job)
     */
    public function cleanupExpiredTokens()
    {
        try {
            // Invalidate token for bookings that checked out > 7 days ago
            $expiredDate = \Carbon\Carbon::now()->subDays(7)->toDateString();

            $affected = Booking::where('check_out_date', '<', $expiredDate)
                ->whereNotNull('qr_validation_token')
                ->update(['qr_validation_token' => null]);

            Log::info("QR Token cleanup: {$affected} expired tokens invalidated.");

            return response()->json([
                'success' => true,
                'message' => "{$affected} expired tokens cleaned up."
            ]);

        } catch (\Exception $e) {
            Log::error('Error in QR token cleanup: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error during cleanup.'
            ], 500);
        }
    }

    public function downloadValidationPDF($token)
    {
        try {
            // 1. Ambil data booking berdasarkan token
            $booking = Booking::with(['cabin', 'room', 'user', 'latestPayment'])
                ->where('qr_validation_token', $token)
                ->firstOrFail(); // Gunakan firstOrFail untuk otomatis 404 jika tidak ketemu

            // 2. Validasi status booking dan pembayaran (menggunakan logika 'settlement' yang sudah kita perbaiki)
            if ($booking->status !== 'confirmed' || !$booking->successfulPayment()->exists()) {
                // Jika booking tidak valid, redirect kembali dengan error
                return redirect()->back()->withErrors(['error' => 'PDF tidak dapat dibuat. Status booking atau pembayaran tidak valid.']);
            }

            // 3. Generate gambar QR Code dalam format base64 untuk disisipkan di PDF
            $qrValidationUrl = route('qr.validate', ['token' => $booking->qr_validation_token]);
            $options = new QROptions([
                'outputType'  => QRCode::OUTPUT_IMAGE_PNG,
                'imageBase64' => true,
                'scale'       => 8, // Buat QR code sedikit lebih besar untuk PDF
            ]);
            $qrCodeImage = (new QRCode($options))->render($qrValidationUrl);

            // Data yang akan dikirim ke view PDF
            $data = [
                'booking'     => $booking,
                'qrCodeImage' => $qrCodeImage,
                'title'       => 'Validasi Booking - ' . $booking->id_booking,
            ];

            // 4. Load view PDF dan kirim sebagai download
            $pdf = Pdf::loadView('frontend.qrcode-pdf', $data);

            // Nama file yang akan di-download
            $fileName = 'bukti_validasi_' . $booking->id_booking . '.pdf';

            return $pdf->download($fileName);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle jika booking dengan token itu tidak ada
            return view('frontend.qr-validation', [
                'status' => 'invalid',
                'title' => 'Booking Tidak Ditemukan',
                'message' => 'Booking dengan QR Code ini tidak ditemukan.',
                'booking' => null
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating PDF for QR validation: ' . $e->getMessage(), ['token' => $token]);
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat membuat file PDF.']);
        }
    }
}