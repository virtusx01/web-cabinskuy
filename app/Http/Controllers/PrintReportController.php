<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PrintReportController extends Controller
{
    /**
     * Menampilkan halaman laporan booking dalam format HTML.
     * Mengambil semua booking yang berstatus 'completed'.
     */
    public function booking(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        // Ambil semua booking yang berstatus 'completed' untuk bulan dan tahun yang dipilih
        $bookings = Booking::with(['cabin', 'room', 'user'])
            ->whereMonth('booking_date', $month)
            ->whereYear('booking_date', $year)
            ->where('status', 'completed') // Filter hanya status 'completed'
            ->orderBy('booking_date', 'asc')
            ->get();

        // Hitung total pendapatan dari booking yang selesai
        $totalIncome = $bookings->sum('total_price');

        // Daftar bulan untuk filter dropdown
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = Carbon::create(null, $m, 1)->translatedFormat('F');
        }

        return view('frontend.admin.reports.booking', compact('bookings', 'month', 'year', 'months', 'totalIncome'));
    }

    /**
     * Menghasilkan laporan booking dalam format PDF.
     */
    public function bookingPdf(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        // Ambil semua booking yang berstatus 'completed' untuk bulan dan tahun yang dipilih
        $bookings = Booking::with(['cabin', 'room', 'user'])
            ->whereMonth('booking_date', $month)
            ->whereYear('booking_date', $year)
            ->where('status', 'completed') // Filter hanya status 'completed'
            ->orderBy('booking_date', 'asc')
            ->get();

        // Hitung total pendapatan dari booking yang selesai
        $totalIncome = $bookings->sum('total_price');

        $monthName = Carbon::create(null, $month, 1)->translatedFormat('F');

        $pdf = Pdf::loadView('frontend.admin.reports.booking_pdf', compact('bookings', 'monthName', 'year', 'totalIncome'));
        return $pdf->download('laporan_booking_selesai_' . $year . '_' . $monthName . '.pdf');
    }
}
