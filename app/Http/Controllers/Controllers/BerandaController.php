<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cabin; // <-- 1. IMPORT THE CABIN MODEL

class BerandaController extends Controller
{
    public function berandaFrontend()
    {
        // 2. FETCH UNIQUE PROVINCES FROM THE CABINS TABLE
        // This query selects the 'province' column, ensures each province
        // appears only once (distinct), and orders them alphabetically.
        $provinces = Cabin::select('province')
                          ->whereNotNull('province') // Optional: ignore cabins with no province set
                          ->distinct()
                          ->orderBy('province', 'asc')
                          ->get();

        // You can also fetch other data needed for the homepage, like featured cabins
        $featuredCabins = Cabin::take(3)->get(); // Example to get 3 cabins for the "Find Near You" section

        // 3. PASS THE $provinces VARIABLE TO THE VIEW
        // The 'compact' function is a clean way to create an array of variables.
        return view('frontend.beranda', compact('provinces', 'featuredCabins'));
    }
    public function getRegencies(Request $request)
    {
        // Validate that the province parameter exists
        $request->validate(['province' => 'required|string']);

        $regencies = Cabin::select('regency')
                          ->where('province', $request->province)
                          ->distinct()
                          ->orderBy('regency', 'asc')
                          ->get();

        return response()->json($regencies);
    }
    public function userBeranda()
    {
        return view('frontend.beranda', [
            'title' => 'Beranda Cabinskuy',
        ]);
    }

    public function listcabinBackend()
    {
        return view('frontend.listcabin', [
            'title' => 'List Cabin - Cabinskuy',
        ]);
    }
    
    public function detailcabinBackend()
    {
        return view('frontend.detailcabin', [
            'title' => 'Detail Cabin - Cabinskuy',
        ]);
    }
}