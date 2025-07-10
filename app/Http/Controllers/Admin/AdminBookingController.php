<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\CabinRoom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AdminBookingController extends Controller
{
    /**
     * Menampilkan daftar semua booking untuk admin.
     * Dapat difilter berdasarkan status atau pencarian.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $query = Booking::with(['user', 'cabin', 'room', 'payments']);

            // Filter berdasarkan status
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Filter berdasarkan ID Booking
            if ($request->has('id_booking') && $request->id_booking) {
                $query->where('id_booking', $request->id_booking);
            }

            // Filter berdasarkan nama/email/telepon kontak
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('contact_name', 'like', '%' . $search . '%')
                      ->orWhere('contact_email', 'like', '%' . $search . '%')
                      ->orWhere('contact_phone', 'like', '%' . $search . '%');
                });
            }

            $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

            return view('frontend.admin.bookings.index', [
                'bookings' => $bookings,
                'title' => 'Manajemen Booking',
                'currentStatus' => $request->status ?? 'all',
                'searchQuery' => $request->search ?? '',
                'bookingIdSearch' => $request->id_booking ?? '',
            ]);
        } catch (\Exception $e) {
            Log::error('Error in AdminBookingController@index: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat memuat daftar booking.']);
        }
    }

    /**
     * Menampilkan detail booking tertentu.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(Booking $booking)
    {
        try {
            $booking->load(['user', 'cabin', 'room', 'confirmedBy', 'rejectedBy', 'payments']);

            return view('frontend.admin.bookings.show', [
                'booking' => $booking,
                'title' => 'Detail Booking #' . $booking->id_booking
            ]);
        } catch (\Exception $e) {
            Log::error('Error in AdminBookingController@show for booking ' . $booking->id_booking . ': ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat memuat detail booking.']);
        }
    }

    /**
     * Mengkonfirmasi booking.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirm(Request $request, Booking $booking)
    {
        // Pastikan hanya admin atau superadmin yang bisa melakukan ini
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'superadmin'])) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melakukan aksi ini.');
        }

        try {
            // Gunakan id_user sebagai id_admin karena foreign key di users menggunakan id_user
            if ($booking->confirm($user->id_user, $request->admin_notes)) {
                return redirect()->back()->with('success', 'Booking berhasil dikonfirmasi.');
            } else {
                return redirect()->back()->withErrors(['error' => 'Booking tidak dapat dikonfirmasi (mungkin statusnya sudah berubah atau sudah dibayar).']);
            }
        } catch (\Exception $e) {
            Log::error('Error confirming booking ' . $booking->id_booking . ': ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat mengkonfirmasi booking.']);
        }
    }

    /**
     * Manual confirmation by admin when payment gateway callback fails.
     * This method allows admin to confirm booking and automatically update payment status.
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Booking $booking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirmBookingManually(Request $request, Booking $booking)
    {
        // Pastikan hanya admin atau superadmin yang bisa melakukan ini
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'superadmin'])) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melakukan aksi ini.');
        }

        // Validasi input
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
            'payment_method' => 'nullable|string|max:50',
        ]);

        try {
            DB::beginTransaction();

            // Update booking status to confirmed
            $booking->update([
                'status' => 'confirmed',
                'admin_notes' => $request->admin_notes,
                'confirmed_by' => $user->id_user,
                'confirmed_at' => now()
            ]);

            Log::info("Admin {$user->name} (ID: {$user->id_user}) manually confirmed booking #{$booking->id_booking}");

            // Update latest payment to paid
            $this->updateLatestPaymentToPaid($booking, $request->payment_method);

            DB::commit();

            // Return redirect response
            return redirect()->back()->with('success', "Booking #{$booking->id_booking} berhasil dikonfirmasi secara manual dan status pembayaran diperbarui menjadi 'paid'");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error manually confirming booking #{$booking->id_booking}: " . $e->getMessage());
            
            return redirect()->back()->withErrors(['error' => 'Gagal mengkonfirmasi booking: ' . $e->getMessage()]);
        }
    }

    /**
     * Update latest payment status to 'paid' when booking is confirmed manually.
     *
     * @param \App\Models\Booking $booking
     * @param string|null $paymentMethod
     * @return bool
     */
    private function updateLatestPaymentToPaid(Booking $booking, $paymentMethod = null)
    {
        try {
            // Get the latest payment for this booking that's not already paid
            $latestPayment = $booking->payments()
                ->whereNotIn('status', ['paid', 'failed', 'cancelled', 'expired'])
                ->latest()
                ->first();

            if ($latestPayment) {
                $latestPayment->update([
                    'status' => 'paid',
                    'payment_method' => $paymentMethod ?? $latestPayment->payment_method ?? 'manual_confirmation',
                    'payment_details' => array_merge(
                        (array) $latestPayment->payment_details,
                        [
                            'manual_confirmation' => true,
                            'confirmed_by_admin' => Auth::user()->name,
                            'confirmed_at' => now()->toISOString()
                        ]
                    ),
                    'updated_at' => now()
                ]);
                
                Log::info("Payment #{$latestPayment->id_payment} for booking #{$booking->id_booking} updated to 'paid' status after manual admin confirmation.");
                return true;
            } else {
                // If no payment exists, create a new one
                $payment = \App\Models\Payment::create([
                    'id_booking' => $booking->id_booking,
                    'amount' => $booking->total_price,
                    'transaction_id' => 'MANUAL-' . time() . '-' . $booking->id_booking,
                    'status' => 'paid',
                    'payment_method' => $paymentMethod ?? 'manual_confirmation',
                    'id_user' => $booking->id_user,
                    'payment_details' => [
                        'manual_confirmation' => true,
                        'confirmed_by_admin' => Auth::user()->name,
                        'confirmed_at' => now()->toISOString()
                    ]
                ]);
                
                Log::info("New payment #{$payment->id_payment} created for booking #{$booking->id_booking} with 'paid' status after manual admin confirmation.");
                return true;
            }
        } catch (\Exception $e) {
            Log::error("Error updating payment status to paid for booking #{$booking->id_booking}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Menolak booking.
     */
    public function reject(Request $request, Booking $booking)
    {
        // Pastikan hanya admin atau superadmin yang bisa melakukan ini
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'superadmin'])) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melakukan aksi ini.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        try {
            // Gunakan id_user sebagai id_admin
            if ($booking->reject($user->id_user, $request->rejection_reason, $request->admin_notes)) {
                return redirect()->back()->with('success', 'Booking berhasil ditolak.');
            } else {
                return redirect()->back()->withErrors(['error' => 'Booking tidak dapat ditolak (mungkin statusnya sudah berubah).']);
            }
        } catch (\Exception $e) {
            Log::error('Error rejecting booking ' . $booking->id_booking . ': ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menolak booking.']);
        }
    }

    public function complete(Request $request, Booking $booking)
    {
        // Ensure only authenticated admins can complete a booking
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'superadmin'])) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melakukan aksi ini.');
        }

        // Validate the request (e.g., admin notes)
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        if ($booking->complete($user->id_user, $request->admin_notes)) {
            return redirect()->route('admin.bookings.show', $booking->id_booking)->with('success', 'Booking berhasil ditandai sebagai selesai (Completed).');
        } else {
            return redirect()->back()->with('error', 'Booking tidak dapat ditandai sebagai selesai pada saat ini. Pastikan status booking sudah dikonfirmasi dan tanggal check-in sudah tiba atau terlewati.');
        }
    }

    /**
     * Membatalkan booking dari sisi admin.
     */
    public function cancel(Request $request, Booking $booking)
    {
        // Pastikan hanya admin atau superadmin yang bisa melakukan ini
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'superadmin'])) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melakukan aksi ini.');
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        try {
            if ($booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $request->cancellation_reason,
                'admin_notes' => $request->admin_notes,
            ])) {
                return redirect()->back()->with('success', 'Booking berhasil dibatalkan oleh admin.');
            } else {
                return redirect()->back()->withErrors(['error' => 'Booking tidak dapat dibatalkan.']);
            }
        } catch (\Exception $e) {
            Log::error('Error cancelling booking ' . $booking->id_booking . ' by admin: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat membatalkan booking.']);
        }
    }

    /**
     * Menghapus booking secara permanen.
     */
    public function destroy(Booking $booking)
    {
        // Pastikan hanya owner (superadmin) yang bisa menghapus permanen
        $user = Auth::user();
        if (!$user || $user->role !== 'superadmin') {
            abort(403, 'Akses ditolak. Hanya owner yang dapat menghapus booking secara permanen.');
        }

        try {
            $bookingId = $booking->id_booking;
            $booking->delete();
            return redirect()->route('admin.bookings.index')->with('success', 'Booking #' . $bookingId . ' berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting booking ' . $booking->id_booking . ': ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus booking.']);
        }
    }
}