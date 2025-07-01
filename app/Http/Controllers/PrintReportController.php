<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment; // Import model Payment
use App\Models\User;   // Import model User jika diperlukan untuk relasi user di payment
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Untuk query agregasi jika diperlukan

class PrintReportController extends Controller
{
    /**
     * Menampilkan halaman laporan keuangan dalam format HTML.
     * Mengambil data pembayaran yang berstatus 'paid' sebagai pendapatan.
     */
    public function financial(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        // Ambil semua pembayaran yang berstatus 'paid' untuk bulan dan tahun yang dipilih
        $paidPayments = Payment::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', 'paid')
            ->orderBy('created_at', 'asc')
            ->get();

        // Hitung total pendapatan
        $totalIncome = $paidPayments->sum('amount');

        // Siapkan data detail transaksi untuk ditampilkan
        $detailedTransactions = $paidPayments->map(function ($payment) {
            return [
                'date' => Carbon::parse($payment->created_at)->format('d-m-Y H:i'),
                'description' => 'Pembayaran Booking #' . $payment->id_booking,
                'amount' => $payment->amount,
                'type' => 'Pemasukan' // Asumsikan semua pembayaran selesai adalah pemasukan
            ];
        })->toArray();

        // Daftar bulan untuk filter dropdown
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = Carbon::create(null, $m, 1)->translatedFormat('F');
        }

        return view('frontend.admin.reports.financial', compact('detailedTransactions', 'totalIncome', 'month', 'year', 'months'));
    }

    /**
     * Menampilkan halaman laporan booking dalam format HTML.
     * Mengambil semua booking (konfirmasi, tolak, batal, selesai).
     */
    public function booking(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        // Ambil semua booking untuk bulan dan tahun yang dipilih, termasuk semua status
        $bookings = Booking::with(['cabin', 'room', 'user'])
            ->whereMonth('booking_date', $month)
            ->whereYear('booking_date', $year)
            ->orderBy('booking_date', 'asc')
            ->get();

        // Daftar bulan untuk filter dropdown
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = Carbon::create(null, $m, 1)->translatedFormat('F');
        }

        return view('frontend.admin.reports.booking', compact('bookings', 'month', 'year', 'months'));
    }

    /**
     * Menghasilkan laporan keuangan dalam format PDF.
     */
    public function financialPdf(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $paidPayments = Payment::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', 'paid')
            ->orderBy('created_at', 'asc')
            ->get();

        $totalIncome = $paidPayments->sum('amount');

        $detailedTransactions = $paidPayments->map(function ($payment) {
            return [
                'date' => Carbon::parse($payment->created_at)->format('d-m-Y H:i'),
                'description' => 'Pembayaran Booking #' . $payment->id_booking,
                'amount' => $payment->amount,
                'type' => 'Pemasukan'
            ];
        })->toArray();

        $monthName = Carbon::create(null, $month, 1)->translatedFormat('F');

        $pdf = Pdf::loadView('frontend.admin.reports.financial_pdf', compact('detailedTransactions', 'totalIncome', 'monthName', 'year'));

        return $pdf->download('laporan_keuangan_' . $year . '_' . $monthName . '.pdf');
    }

    /**
     * Menghasilkan laporan booking dalam format PDF.
     */
    public function bookingPdf(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $bookings = Booking::with(['cabin', 'room', 'user'])
            ->whereMonth('booking_date', $month)
            ->whereYear('booking_date', $year)
            ->orderBy('booking_date', 'asc')
            ->get();

        $monthName = Carbon::create(null, $month, 1)->translatedFormat('F');

        $pdf = Pdf::loadView('frontend.admin.reports.booking_pdf', compact('bookings', 'monthName', 'year'));
        return $pdf->download('laporan_booking_' . $year . '_' . $monthName . '.pdf');
    }
}
