<?php

namespace App\Http\Controllers;

use App\Models\Cabin;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class UserCabinController extends Controller
{
    /**
     * Menangani request POST dari form pencarian di beranda.
     * Tugasnya adalah me-redirect ke method index() dengan parameter GET.
     */
    public function search(Request $request)
    {
        // Ambil semua input dari form, kecuali token CSRF
        $filters = $request->except('_token');

        // Buang parameter yang nilainya kosong (null atau string kosong)
        $queryParameters = array_filter($filters, function($value) {
            return $value !== null && $value !== '';
        });

        // Redirect ke route index (GET) dengan membawa parameter filter
        return redirect()->route('frontend.kabin.index', $queryParameters);
    }

    /**
     * Menampilkan halaman daftar kabin dan menangani semua filter.
     * Method ini menerima request GET, baik langsung maupun dari redirect.
     */
    public function index(Request $request)
    {
        // Query dasar untuk kabin yang statusnya aktif
        $query = Cabin::where('status', true);

        // --- APLIKASIKAN SEMUA FILTER DARI URL ---

        // Filter berdasarkan Provinsi
        if ($request->filled('province')) {
            $query->where('province', $request->province);
        }

        // Filter berdasarkan Kabupaten/Kota (dari filter di halaman listcabin)
        if ($request->filled('regency')) {
            $query->where('regency', $request->regency);
        }
        
        // Filter berdasarkan Tipe Kamar (dari filter di halaman listcabin)
        if ($request->filled('typeroom')) {
            $query->whereHas('rooms', function ($q) use ($request) {
                $q->where('typeroom', $request->typeroom);
            });
        }

        // Filter berdasarkan Jumlah Tamu
        if ($request->filled('guests')) {
            $query->whereHas('rooms', function ($q) use ($request) {
                $q->where('status', true)->where('max_guests', '>=', $request->guests);
            });
        }

        // Filter berdasarkan Ketersediaan Tanggal (Check-in & Check-out)
        if ($request->filled('check_in_date') && $request->filled('check_out_date')) {
            $check_in_date = Carbon::parse($request->check_in_date);
            $check_out_date = Carbon::parse($request->check_out_date);

            $query->whereHas('rooms', function ($roomQuery) use ($check_in_date, $check_out_date, $request) {
                $roomQuery->where('status', true)
                    ->whereDoesntHave('bookings', function ($bookingQuery) use ($check_in_date, $check_out_date) {
                        $bookingQuery->where('check_out_date', '>', $check_in_date)
                                     ->where('check_in_date', '<', $check_out_date)
                                     ->whereNotIn('status', ['cancelled', 'rejected', 'expired']);
                    });

                // Jika ada filter tamu, pastikan kamar yang tersedia juga memenuhi kapasitas
                if ($request->filled('guests')) {
                    $roomQuery->where('max_guests', '>=', $request->guests);
                }
            });
        }

        // Eager load relasi 'rooms' agar efisien
        $query->with(['rooms' => function($q) {
            $q->where('status', true);
        }]);

        // Eksekusi query dan ambil hasil dengan paginasi
        // `paginate(9)` berarti 9 kabin per halaman
        $cabins = $query->latest('id_cabin')->paginate(9);

        // --- SIAPKAN DATA UNTUK DROPDOWN FILTER DI VIEW ---

        // Ambil daftar provinsi unik untuk filter
        $provinces = Cabin::select('province')->where('status', true)->whereNotNull('province')->distinct()->orderBy('province', 'asc')->get();

        // Ambil daftar kabupaten/kota HANYA jika provinsi sudah dipilih di filter
        $regencies = collect();
        if ($request->filled('province')) {
            $regencies = Cabin::select('regency')->where('status', true)->where('province', $request->province)->distinct()->orderBy('regency', 'asc')->get();
        }
        
        // Kirim semua data yang diperlukan ke view 'listcabin'
        return view('frontend.listcabin', [
            'cabins' => $cabins,
            'provinces' => $provinces,
            'regencies' => $regencies,
            'title' => 'Hasil Pencarian Kabin' // Judul halaman
        ]);
    }

    /**
     * Menampilkan halaman detail untuk satu kabin.
     */
    public function show(Cabin $cabin)
    {
        // Pastikan hanya kabin aktif yang bisa diakses
        if (!$cabin->status) {
            abort(404);
        }

        // Eager load relasi rooms yang aktif
        $cabin->load(['rooms' => function ($query) {
            $query->where('status', true)->orderBy('price', 'asc');
        }]);
        
        $allPhotos = is_array($cabin->cabin_photos) ? $cabin->cabin_photos : [];
        foreach ($cabin->rooms as $room) {
            if (!empty($room->room_photos) && is_array($room->room_photos)) {
                $allPhotos = array_merge($allPhotos, $room->room_photos);
            }
        }
        
        return view('frontend.detailcabin', [
            'cabin' => $cabin,
            'allPhotos' => array_unique($allPhotos),
            'title' => $cabin->name . ' - Detail Kabin'
        ]);
    }
}