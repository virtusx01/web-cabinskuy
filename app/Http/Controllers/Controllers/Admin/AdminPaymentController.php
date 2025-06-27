<?php

namespace App\Http\Controllers\Admin; // Perhatikan namespace yang disarankan

use App\Http\Controllers\Controller;
use App\Models\Payment; // Pastikan model Payment sudah di-import
use Illuminate\Http\Request;

class AdminPaymentController extends Controller
{
    /**
     * Menampilkan daftar pembayaran terbaru untuk dashboard admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Ambil 5 pembayaran terbaru, urutkan berdasarkan created_at descending
        // Dengan eager loading relasi booking, user, room, dan cabin untuk menghindari N+1 query
        $recentPayments = Payment::with([
                'booking.user',
                'booking.room.cabin'
            ])
            ->latest() // Urutkan berdasarkan created_at terbaru
            ->limit(5) // Ambil 5 data terbaru
            ->get();

        return view('admin.payments.index', compact('recentPayments')); // Contoh view untuk manajemen pembayaran
    }

    /**
     * Menampilkan daftar semua pembayaran.
     * Metode ini bisa Anda kembangkan untuk fitur manajemen pembayaran lengkap.
     *
     * @return \Illuminate\View\View
     */
    public function allPayments()
    {
        $payments = Payment::with(['booking.user', 'booking.room.cabin'])->latest()->paginate(10);
        return view('admin.payments.all', compact('payments'));
    }

    // Anda bisa menambahkan metode lain di sini seperti show, edit, update, delete
    // tergantung kebutuhan manajemen pembayaran Anda.
}