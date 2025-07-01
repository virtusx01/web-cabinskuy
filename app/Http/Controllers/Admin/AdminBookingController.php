<?php

namespace App\Http\Controllers\Admin; // Pastikan namespace sesuai dengan struktur folder Anda

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\CabinRoom;
use App\Models\User; // Untuk relasi confirmed_by, rejected_by
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
        if (!$user || !in_array($user->role, ['admin', 'superadmin'])) { // <--- PERBAIKAN DI SINI
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melakukan aksi ini.');
        }

        try {
            // Gunakan id_user sebagai id_admin karena foreign key di users menggunakan id_user
            if ($booking->confirm($user->id_user, $request->admin_notes)) { // <--- Gunakan $user->id_user
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
     * Menolak booking.
     */
    public function reject(Request $request, Booking $booking)
    {
        // Pastikan hanya admin atau superadmin yang bisa melakukan ini
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'superadmin'])) { // <--- PERBAIKAN DI SINI
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melakukan aksi ini.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        try {
            // Gunakan id_user sebagai id_admin
            if ($booking->reject($user->id_user, $request->rejection_reason, $request->admin_notes)) { // <--- Gunakan $user->id_user
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
        if (!$user || !in_array($user->role, ['admin', 'superadmin'])) { // <--- PERBAIKAN DI SINI
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melakukan aksi ini.');
        }

        // Validate the request (e.g., admin notes)
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $adminId = Auth::id(); // Get the ID of the logged-in admin

        if ($booking->complete($user->id_user,$request->admin_notes)) {
            // Logic to update room availability for the current day or remaining days
            // If a booking is completed, the room becomes available immediately for the rest of the original booking period.
            // This is a crucial part for managing slot availability.
            
            // Example: Mark the room as available for the remaining nights from today
            // However, the check-in/check-out date range logic in your `scopeActiveOnDateRange`
            // already excludes 'completed' bookings. So, simply changing the status to 'completed'
            // in the booking record will automatically free up the slot for future checks.
            // No explicit 'increase_available_slots' is typically needed for each room day.

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
        if (!$user || !in_array($user->role, ['admin', 'superadmin'])) { // <--- PERBAIKAN DI SINI
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melakukan aksi ini.');
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        try {
            // Anda bisa menambahkan 'admin_notes' ke fungsi cancel di model Booking jika diperlukan
            // Atau update langsung di sini seperti yang sudah Anda lakukan sebelumnya
            if ($booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $request->cancellation_reason,
                'admin_notes' => $request->admin_notes, // Catatan admin untuk pembatalan
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
        if (!$user || $user->role !== 'superadmin') { // <--- PERBAIKAN DI SINI, HANYA SUPERADMIN
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
