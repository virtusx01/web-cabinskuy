<?php

namespace App\Http\Controllers;

use App\Models\Cabin;
use App\Models\CabinRoom;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QRImage;

class BookingController extends Controller
{
    /**
     * Langkah 1: Menerima permintaan booking & menyimpan ke session jika tamu.
     * Route ini akan di-pointing dari form di halaman detail.
     */
    public function startBooking(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_room'       => 'required|exists:cabin_rooms,id_room',
                'checkin_date'  => 'required|date|after_or_equal:today',
                'checkout_date' => 'required|date|after:checkin_date',
                'total_guests'  => 'required|integer|min:1|max:20',
            ]);

            // Simpan detail booking ke session
            session(['pending_booking' => $validated]);

            $room = CabinRoom::findOrFail($validated['id_room']);

            // Arahkan ke halaman pembuatan booking. Middleware 'auth' di route
            // akan otomatis menangani redirect ke login jika user adalah tamu.
            return Redirect::route('frontend.booking.create', ['room' => $room->id_room]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error in startBooking: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat memproses permintaan booking.'])
                ->withInput();
        }
    }

    /**
     * Langkah 2: Menampilkan form booking setelah user login.
     * Mengambil data dari session jika ada.
     */
    public function create(CabinRoom $room)
    {
        try {
            // Ambil data dari session yang disimpan oleh startBooking
            $bookingDetails = session('pending_booking');

            // Jika user akses URL ini langsung tanpa melalui startBooking,
            // kita berikan tanggal default atau redirect.
            if (!$bookingDetails || ($bookingDetails['id_room'] ?? null) != $room->id_room) {
                return redirect()->route('frontend.beranda')
                    ->withErrors(['error' => 'Mohon pilih kamar dan tanggal booking terlebih dahulu.']);
            }

            // Load relasi cabin dengan eager loading
            $room->load('cabin');

            // Pastikan cabin ada
            if (!$room->cabin) {
                return redirect()->route('frontend.beranda')
                    ->withErrors(['error' => 'Kabin tidak ditemukan untuk ruangan ini.']);
            }

            return view('frontend.booking', [
                'room'           => $room,
                'cabin'          => $room->cabin,
                'bookingDetails' => $bookingDetails,
                'title'          => 'Konfirmasi Booking Anda'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in create booking: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('frontend.beranda')
                ->withErrors(['error' => 'Terjadi kesalahan saat memuat halaman booking.']);
        }
    }

    /**
     * API endpoint untuk cek ketersediaan kamar
     */
    public function checkAvailability(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_room'        => 'required|string|exists:cabin_rooms,id_room',
                'check_in_date'  => 'required|date|after_or_equal:today',
                'check_out_date' => 'required|date|after:check_in_date',
                'slots_needed'   => 'required|integer|min:1',
            ]);

            $room = CabinRoom::findOrFail($validated['id_room']);

            // KONDISI 1: Cek apakah jumlah tamu yang diminta melebihi kapasitas maksimal kamar
            if ($validated['slots_needed'] > $room->max_guests) {
                return response()->json([
                    'available' => false,
                    'message'   => "Jumlah tamu melebihi kapasitas maksimal ({$room->max_guests} orang) untuk kamar ini.",
                ]);
            }

            // KONDISI 2: Cek apakah unit kamar sudah di-booking pada tanggal tersebut
            $bookedUnitsCount = Booking::where('id_room', $validated['id_room'])
                ->activeOnDateRange($validated['check_in_date'], $validated['check_out_date'])
                ->count();

            $isAvailable = $bookedUnitsCount < $room->slot_room;

            return response()->json([
                'available' => $isAvailable,
                'message'   => $isAvailable
                    ? "Kamar tersedia untuk tanggal yang Anda pilih!"
                    : "Maaf, kamar ini sudah dipesan pada tanggal tersebut.",
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'available' => false,
                'message'   => 'Validasi gagal: ' . $e->getMessage(),
                'errors'    => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in checkAvailability: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'available' => false,
                'message'   => 'Terjadi kesalahan saat mengecek ketersediaan.',
                'error'     => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Langkah 3: Menyimpan booking ke database.
     * Mengarahkan ke halaman pembayaran Midtrans.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'id_room'            => 'required|exists:cabin_rooms,id_room',
                'checkin_date'       => 'required|date|after_or_equal:today',
                'checkout_date'      => 'required|date|after:checkin_date',
                'total_guests'       => 'required|integer|min:1',
                'contact_name'       => 'required|string|max:255',
                'contact_email'      => 'required|email|max:255',
                'contact_phone'      => 'nullable|string|max:20',
                'total_price'        => 'required|numeric|min:0',
                'special_requests'   => 'nullable|string|max:1000',
            ]);

            $room = CabinRoom::with('cabin')->findOrFail($validated['id_room']);

            if (!$room->cabin) {
                throw new \Exception('Kabin tidak ditemukan untuk ruangan ini.');
            }

            $checkinDate = Carbon::parse($validated['checkin_date']);
            $checkoutDate = Carbon::parse($validated['checkout_date']);
            $totalNights = $checkinDate->diffInDays($checkoutDate);

            if ($totalNights < 1) {
                return redirect()->back()
                    ->withErrors(['error' => 'Booking minimal 1 malam.'])
                    ->withInput();
            }

            if ($validated['total_guests'] > $room->max_guests) {
                return redirect()->back()
                    ->withErrors(['error' => "Jumlah tamu tidak boleh melebihi kapasitas maksimal ({$room->max_guests} orang) untuk kamar ini."])
                    ->withInput();
            }

            // KONDISI VALIDASI ULANG: Validasi ulang ketersediaan unit kamar
            $bookedUnitsCount = Booking::where('id_room', $validated['id_room'])
                ->activeOnDateRange($validated['checkin_date'], $validated['checkout_date'])
                ->count();

            if ($bookedUnitsCount >= $room->slot_room) {
                return redirect()->back()
                    ->withErrors(['error' => 'Maaf, kamar ini sudah tidak tersedia pada tanggal yang dipilih. Silakan pilih tanggal atau kamar lain.'])
                    ->withInput();
            }

            $expectedTotalPrice = $room->price * $totalNights;
            if (abs($validated['total_price'] - $expectedTotalPrice) > 0.01) {
                Log::warning("Price mismatch for booking. Expected: {$expectedTotalPrice}, Received: {$validated['total_price']}");
                return redirect()->back()
                    ->withErrors(['error' => 'Terjadi kesalahan dalam perhitungan harga. Harga yang benar: Rp ' . number_format($expectedTotalPrice, 0, ',', '.')])
                    ->withInput();
            }

            $booking = Booking::create([
                'id_user'        => Auth::user()->id_user,
                'id_cabin'       => $room->cabin->id_cabin,
                'id_room'        => $validated['id_room'],
                'check_in_date'  => $validated['checkin_date'],
                'check_out_date' => $validated['checkout_date'],
                'checkin_room'   => 1,
                'total_guests'   => $validated['total_guests'],
                'total_nights'   => $totalNights,
                'total_price'    => $expectedTotalPrice,
                'contact_name'   => $validated['contact_name'],
                'contact_phone'  => $validated['contact_phone'] ?? null,
                'contact_email'  => $validated['contact_email'],
                'special_requests' => $validated['special_requests'],
                'status'         => 'pending',
                'booking_date'   => now(),
            ]);

            session()->forget('pending_booking');

            DB::commit();

            return redirect()->route('frontend.payment.show', ['booking' => $booking->id_booking])
                ->with('success', 'Booking Anda telah berhasil dibuat. Silakan lanjutkan ke pembayaran.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in store booking: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan booking: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Menampilkan daftar booking user
     */
    public function index()
    {
        try {
            $bookings = Booking::with(['cabin', 'room', 'latestPayment'])
                ->where('id_user', Auth::user()->id_user)
                ->orderBy('booking_date', 'desc')
                ->paginate(10);

            return view('frontend.my-bookings', [
                'bookings' => $bookings,
                'title'    => 'Booking Saya'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in index bookings: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('frontend.beranda')
                ->withErrors(['error' => 'Terjadi kesalahan saat memuat daftar booking.']);
        }
    }

    /**
     * Menampilkan detail booking (tanpa QR Code di halaman ini)
     */
    public function show(Booking $booking)
    {
        try {
            if ($booking->id_user !== Auth::user()->id_user) {
                abort(403, 'Anda tidak memiliki akses untuk melihat booking ini.');
            }

            $booking->load(['cabin', 'room', 'user', 'payments']);

            return view('frontend.booking-detail', [
                'booking' => $booking,
                'title'   => 'Detail Booking #' . $booking->id_booking,
            ]);

        } catch (\Exception $e) {
            Log::error('Error in show booking: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('frontend.beranda')
                ->withErrors(['error' => 'Terjadi kesalahan saat memuat detail booking.']);
        }
    }

    /**
     * Menampilkan halaman QR Code untuk booking yang sudah confirmed
     */
    public function showQRCodePage(Booking $booking)
    {
        try {
            // Pastikan hanya user yang berhak yang bisa mengakses
            if ($booking->id_user !== Auth::user()->id_user) {
                abort(403, 'Anda tidak memiliki akses untuk melihat QR Code booking ini.');
            }

            // Load relasi yang diperlukan
            $booking->load(['cabin', 'room', 'user', 'payments']);

            // Pastikan booking sudah confirmed dan ada pembayaran berhasil
            if ($booking->status !== 'confirmed' || !$booking->successfulPayment()->exists()) {
                return redirect()->route('frontend.booking.show', ['booking' => $booking->id_booking])
                    ->withErrors(['error' => 'QR Code hanya tersedia untuk booking yang sudah dikonfirmasi dan pembayaran berhasil.']);
            }

            // Generate atau ambil QR validation token jika belum ada
            if (empty($booking->qr_validation_token)) {
                $booking->update([
                    'qr_validation_token' => Str::random(64)
                ]);
            }

            // Konfigurasi QR Code
            $options = new QROptions([
                'outputType'    => QRCode::OUTPUT_IMAGE_PNG,
                'eccLevel'      => QRCode::ECC_L,
                'scale'         => 8,
                'imageBase64'   => true,
            ]);

            $qr = new QRCode($options);

            // URL tujuan scan QR Code (halaman validasi publik)
            $qrContent = route('frontend.qr.validate', ['token' => $booking->qr_validation_token]);

            $qrCode = $qr->render($qrContent);

            return view('frontend.booking-qrcode', [
                'booking' => $booking,
                'title'   => 'QR Code Booking #' . $booking->id_booking,
                'qrCode'  => $qrCode,
                'qrUrl'   => $qrContent,
            ]);

        } catch (\Exception $e) {
            Log::error('Error in showQRCodePage: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('frontend.booking.show', ['booking' => $booking->id_booking])
                ->withErrors(['error' => 'Terjadi kesalahan saat memuat QR Code.']);
        }
    }

    /**
     * Download QR Code sebagai file PNG
     */
    public function downloadQRCode(Booking $booking)
    {
        try {
            // Pastikan hanya user yang berhak yang bisa mengakses
            if ($booking->id_user !== Auth::user()->id_user) {
                abort(403, 'Anda tidak memiliki akses untuk mendownload QR Code booking ini.');
            }

            // Pastikan booking sudah confirmed dan ada pembayaran berhasil
            if ($booking->status !== 'confirmed' || !$booking->successfulPayment()->exists()) {
                return redirect()->back()
                    ->withErrors(['error' => 'QR Code hanya tersedia untuk booking yang sudah dikonfirmasi dan pembayaran berhasil.']);
            }

            // Pastikan ada QR validation token
            if (empty($booking->qr_validation_token)) {
                $booking->update([
                    'qr_validation_token' => Str::random(64)
                ]);
            }

            // Konfigurasi QR Code untuk download (resolusi tinggi)
            $options = new QROptions([
                'outputType'    => QRCode::OUTPUT_IMAGE_PNG,
                'eccLevel'      => QRCode::ECC_M,
                'scale'         => 10,
                'imageBase64'   => false,
            ]);

            $qr = new QRCode($options);
            $qrContent = route('frontend.qr.validate', ['token' => $booking->qr_validation_token]);
            $qrImage = $qr->render($qrContent);

            $filename = 'qr-booking-' . $booking->id_booking . '.png';

            return response($qrImage)
                ->header('Content-Type', 'image/png')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            Log::error('Error in downloadQRCode: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat mendownload QR Code.']);
        }
    }

    /**
     * Membatalkan booking (hanya untuk status pending, challenge)
     */
    public function cancel(Request $request, Booking $booking)
    {
        try {
            if ($booking->id_user !== Auth::user()->id_user) {
                abort(403, 'Anda tidak memiliki akses untuk membatalkan booking ini.');
            }

            $validated = $request->validate([
                'cancellation_reason' => 'nullable|string|max:500',
            ]);

            if (!$booking->canBeCancelled()) {
                return redirect()->back()
                    ->withErrors(['error' => 'Booking tidak dapat dibatalkan. Status saat ini: ' . $booking->status_label]);
            }

            $booking->cancel($validated['cancellation_reason'] ?? 'Dibatalkan oleh user');

            return redirect()->back()
                ->with('success', 'Booking berhasil dibatalkan.');

        } catch (\Exception $e) {
            Log::error('Error in cancel booking: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat membatalkan booking.']);
        }
    }

    /**
     * API untuk mendapatkan room berdasarkan cabin (untuk AJAX)
     */
    public function getRoomsByCabin(Request $request)
    {
        try {
            $cabinId = $request->get('cabin_id');
            if (empty($cabinId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID Cabin tidak boleh kosong.'
                ], 400);
            }

            $rooms = CabinRoom::where('id_cabin', $cabinId)
                ->orderBy('room_name')
                ->get(['id_room', 'room_name', 'slot_room', 'price']);

            return response()->json([
                'success' => true,
                'rooms'   => $rooms
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getRoomsByCabin: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data ruangan.'
            ], 500);
        }
    }

    /**
     * API untuk mendapatkan status booking (berdasarkan Booking ID).
     * Akan digunakan oleh polling di frontend.
     */
    public function getBookingStatus(Booking $booking)
    {
        // Pastikan hanya user yang berhak yang bisa mengakses
        if ($booking->id_user !== (Auth::user()->id_user ?? null)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'status'       => $booking->status,
            'status_label' => $booking->status_label,
            'payment_status_label' => $booking->latestPayment ? $booking->latestPayment->status : null,
        ]);
    }
}