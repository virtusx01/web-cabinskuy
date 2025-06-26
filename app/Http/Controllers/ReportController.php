<?php

namespace App\Http\Controllers;

// Import all necessary Models and classes
use App\Models\User;
use App\Models\Cabin;
use App\Models\CabinRoom;
use App\Models\Booking;
use App\Models\CabinReport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     * Display a paginated list of all generated reports.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch all reports from the database, newest first, and paginate them
        $reports = CabinReport::orderBy('report_date', 'desc')->paginate(15);

        return view('admin.reports.index', [
            'title' => 'Laporan Harian',
            'reports' => $reports,
        ]);
    }

    /**
     * Generate and store a new report for the current day.
     * This method contains the logic moved from AdminController.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            // 1. Calculate all the required statistics
            $totalCabins = Cabin::count();
            $totalCabinRooms = CabinRoom::count();
            $totalUsers = User::where('role', 'customer')->count(); // Assuming role 0 is regular user
            $totalBookings = Booking::count();
            $totalRevenue = Booking::where('status', 'confirmed')->sum('total_price');
            
            // 2. Get a snapshot of the 5 most recent bookings
            $recentBookings = Booking::with(['user', 'room.cabin'])->latest()->take(5)->get();

            // 3. Format the snapshot into a clean array to be stored as JSON
            $bookingsSnapshot = $recentBookings->map(function ($booking) {
                return [
                    'cabin_name' => $booking->room?->cabin?->name ?? 'N/A',
                    'guest_name' => $booking->user?->name ?? 'N/A',
                    'check_in' => $booking->check_in_date,
                    'status' => $booking->status,
                    // You might also want to add the room type here for more detail:
                    'room_type' => $booking->room?->typeroom ?? 'N/A',
                ];
            });

            // 4. Use updateOrCreate to prevent duplicate reports for the same day.
            // It will CREATE a report if one doesn't exist for today,
            // or UPDATE it if it already exists.
            CabinReport::updateOrCreate(
                ['report_date' => Carbon::today()], // The condition to find the record
                [                                   // The data to insert or update
                    'total_cabins' => $totalCabins,
                    'total_cabinrooms' => $totalCabinRooms,
                    'total_users' => $totalUsers,
                    'total_bookings' => $totalBookings,
                    'total_revenue' => $totalRevenue,
                    'recent_bookings_snapshot' => $bookingsSnapshot,
                ]
            );

            // 5. Redirect back to the reports list with a success message
            return redirect()->route('admin.reports.index')->with('success', 'Laporan untuk hari ini telah berhasil dibuat/diperbarui.');

        } catch (\Exception $e) {
            // If something goes wrong, log the error and redirect with an error message.
            Log::error('Failed to generate report: ' . $e->getMessage());
            return redirect()->route('admin.reports.index')->with('error', 'Gagal membuat laporan. Silakan cek log.');
        }
    }

    /**
     * Display the details of a single report.
     *
     * @param  \App\Models\CabinReport  $report
     * @return \Illuminate\View\View
     */
    public function show(CabinReport $report)
    {
        return view('admin.reports.show', [
            'title' => 'Detail Laporan - ' . $report->report_date->format('d M Y'),
            'report' => $report,
        ]);
    }
}