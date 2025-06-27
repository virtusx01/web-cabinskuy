<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


// Pastikan semua 'use' statement ini ada
use App\Models\User;
use App\Models\Cabin;
use App\Models\CabinRoom;
use App\Models\Booking;
use App\Models\Payment; // <-- Tambahkan ini
use App\Models\CabinReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Number;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function adminBackend()
    {
        // Get the authenticated user
        $user = Auth::user(); 

        // --- Fetch Statistics ---
        $totalCabins = Cabin::count();
        $totalCabinRooms = CabinRoom::count();
        $totalUsers = User::where('role','customer')->count(); 
        $totalBookings = Booking::count();
        $totalRevenue = Booking::where('status', 'confirmed')->sum('total_price'); 
        
        // --- Fetch Recent Bookings ---
        $recentBookings = Booking::with(['user', 'room.cabin'])
            ->latest()
            ->take(5)
            ->get();

        // --- Fetch Recent Payments ---
        // Ambil 5 pembayaran terbaru, urutkan berdasarkan created_at terbaru
        $recentPayments = Payment::with(['booking.user', 'booking.room.cabin'])
                                ->latest()
                                ->take(5)
                                ->get();


        // --- (Opsional) Blok kode untuk menyimpan laporan ---
        try {
            $bookingsSnapshot = $recentBookings->map(function ($booking) {
                return [
                    'cabin_name' => $booking->room?->cabin?->cabin_name,
                    'guest_name' => $booking->user?->name,
                    'check_in' => $booking->check_in_date,
                    'status' => $booking->status,
                ];
            });

            CabinReport::updateOrCreate(
                ['report_date' => Carbon::today()],
                [                                   
                    'total_cabins' => $totalCabins,
                    'total_cabinrooms' => $totalCabinRooms,
                    'total_users' => $totalUsers,
                    'total_bookings' => $totalBookings,
                    'total_revenue' => $totalRevenue,
                    'recent_bookings_snapshot' => $bookingsSnapshot,
                ]
            );
        } catch (\Exception $e) {
            // Log the error if saving the report fails
            Log::error('Failed to create or update cabin report: ' . $e->getMessage());
        }

        return view('frontend.admin.beranda', [
            'title' => 'Admin Dashboard - Cabinskuy',
            'user' => $user,
            'totalCabins' => $totalCabins,
            'totalCabinRooms' => $totalCabinRooms, 
            'totalUsers' => $totalUsers,
            'totalBookings' => $totalBookings,
            'totalRevenue' => $totalRevenue,
            'recentBookings' => $recentBookings,
            'recentPayments' => $recentPayments, // <-- Tambahkan ini
        ]);
    }
}