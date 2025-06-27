<?php

namespace App\Http\Controllers\Admin; // Pastikan namespace sesuai dengan struktur folder Anda

use App\Http\Controllers\Controller;
use App\Models\Booking;
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
        // Pastikan hanya admin yang bisa melakukan ini
        if (!Auth::check() || !Auth::user()->role == 'admin') { // Sesuaikan dengan cara Anda mengecek admin
            abort(403, 'Akses ditolak.');
        }

        try {
            if ($booking->confirm(Auth::id(), $request->admin_notes)) {
                return redirect()->back()->with('success', 'Booking berhasil dikonfirmasi.');
            } else {
                return redirect()->back()->withErrors(['error' => 'Booking tidak dapat dikonfirmasi (mungkin statusnya sudah berubah).']);
            }
        } catch (\Exception $e) {
            Log::error('Error confirming booking ' . $booking->id_booking . ': ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat mengkonfirmasi booking.']);
        }
    }

    /**
     * Menolak booking.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, Booking $booking)
    {
        // Pastikan hanya admin yang bisa melakukan ini
        if (!Auth::check() || !Auth::user()->role == 'admin') {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        try {
            if ($booking->reject(Auth::id(), $request->rejection_reason, $request->admin_notes)) {
                return redirect()->back()->with('success', 'Booking berhasil ditolak.');
            } else {
                return redirect()->back()->withErrors(['error' => 'Booking tidak dapat ditolak (mungkin statusnya sudah berubah).']);
            }
        } catch (\Exception $e) {
            Log::error('Error rejecting booking ' . $booking->id_booking . ': ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menolak booking.']);
        }
    }

    /**
     * Membatalkan booking dari sisi admin.
     * Ini berbeda dengan pembatalan oleh user, mungkin memiliki hak istimewa lebih.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Request $request, Booking $booking)
    {
        // Pastikan hanya admin yang bisa melakukan ini
        if (!Auth::check() || !Auth::user()->role == 'admin') {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        try {
            // Admin bisa membatalkan booking di berbagai status, tergantung kebijakan
            // Jika Anda ingin membatasi hanya status tertentu, tambahkan logika di sini.
            // Contoh: $booking->status === 'confirmed' || $booking->status === 'pending'
            if ($booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $request->cancellation_reason,
                'admin_notes' => $request->admin_notes, // Admin bisa menambahkan catatan
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
     * PERHATIAN: Gunakan dengan hati-hati! Ini akan menghapus record secara permanen.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Booking $booking)
    {
        // Pastikan hanya admin yang bisa melakukan ini
        if (!Auth::check() || !Auth::user()-> role == 'admin') {
            abort(403, 'Akses ditolak.');
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
