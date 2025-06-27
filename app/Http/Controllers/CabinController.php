<?php

namespace App\Http\Controllers;

use App\Models\Cabin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Str; // Import Str facade for string manipulation

class CabinController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $location = $request->input('location');
        $checkin = $request->input('checkin'); // New
        $checkout = $request->input('checkout'); // New
        $guests = $request->input('guests'); // New

        $query = Cabin::with('rooms');

        // Apply search filter (existing logic)
        $query->when($search, function ($q, $search) {
            return $q->where('name', 'like', "%{$search}%")
                     ->orWhere('location_address', 'like', "%{$search}%")
                     ->orWhere('province', 'like', "%{$search}%")
                     ->orWhere('regency', 'like', "%{$search}%");
        });

        // Apply combined location filter
        $query->when($location, function ($q, $location) {
            $parts = explode(',', $location);
            $regency = trim($parts[0]);
            $province = isset($parts[1]) ? trim($parts[1]) : null;

            if ($regency && $province) {
                $q->where('regency', $regency)->where('province', $province);
            } elseif ($regency) {
                $q->where('regency', $regency);
            } elseif ($province) {
                $q->where('province', $province);
            }
        });

        // --- NEW LOGIC FOR CHECKIN, CHECKOUT, AND GUESTS ---
        $query->when($checkin && $checkout && $guests, function ($q) use ($checkin, $checkout, $guests) {
            // We want to find cabins that have *at least one* room available
            // that meets the guest capacity and is not booked for the requested dates.
            $q->whereHas('rooms', function ($roomQuery) use ($checkin, $checkout, $guests) {
                $roomQuery->where('capacity', '>=', $guests) // Room must have enough capacity
                          ->whereDoesntHave('bookings', function ($bookingQuery) use ($checkin, $checkout) {
                              // Check for overlapping bookings
                              // A booking overlaps if:
                              // (booking_check_in < requested_check_out) AND (booking_check_out > requested_check_in)
                              $bookingQuery->where(function ($q2) use ($checkin, $checkout) {
                                  $q2->where('check_in_date', '<', $checkout)
                                     ->where('check_out_date', '>', $checkin);
                              });
                          });
            });
        });
        // --- END NEW LOGIC ---

        $cabins = $query->latest()->paginate(10);

        // Fetch unique regency and province combinations for the location filter dropdown
        $allLocations = Cabin::select('regency', 'province')
                                ->distinct()
                                ->orderBy('province')
                                ->orderBy('regency')
                                ->get()
                                ->map(function ($item) {
                                    return ['regency' => $item->regency, 'province' => $item->province];
                                })
                                ->toArray();

        $cabins->appends($request->except('page')); // Append all current filter parameters

        return view('frontend.admin.listcabin', compact('cabins', 'allLocations', 'checkin', 'checkout', 'guests', 'location', 'search'));
    }

    public function create()
    {
        // Fetch provinces from API for the 'create cabin' form
        try {
            $response = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json");
            $provinces = $response->json();
        } catch (\Exception $e) {
            $provinces = []; // Handle error gracefully
        }
        return view('frontend.admin.addcabin', compact('provinces'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'province' => 'required|string|max:255', // This will be the ID from frontend
            'regency' => 'required|string|max:255',   // This will be the ID from frontend
            'location_address' => 'required|string|max:255',
            'cabin_photos' => 'nullable|array',
            'cabin_photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|boolean',
        ]);

        $provinceId = $request->input('province');
        $regencyId = $request->input('regency');

        // --- Fetch Province Name ---
        $provinceName = null;
        try {
            $response = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json");
            $provinces = $response->json();
            foreach ($provinces as $prov) {
                if ($prov['id'] == $provinceId) {
                    $provinceName = $prov['name'];
                    break;
                }
            }
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['province' => 'Failed to fetch province data. Please try again.']);
        }
        if (!$provinceName) {
            return back()->withInput()->withErrors(['province' => 'Invalid province selected.']);
        }

        // --- Fetch Regency Name ---
        $regencyName = null;
        try {
            $response = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/regencies/{$provinceId}.json");
            $regencies = $response->json();
            foreach ($regencies as $reg) {
                if ($reg['id'] == $regencyId) {
                    $regencyName = $reg['name'];
                    break;
                }
            }
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['regency' => 'Failed to fetch regency data. Please try again.']);
        }
        if (!$regencyName) {
            return back()->withInput()->withErrors(['regency' => 'Invalid regency selected.']);
        }

        // Generate id_cabin using the static method from the Cabin model
        $idCabin = Cabin::generateCabinId($provinceName, $regencyName);

        $photoPaths = [];
        if ($request->hasFile('cabin_photos')) {
            foreach ($request->file('cabin_photos') as $photo) {
                $filename = time() . '_' . Str::random(10) . '.' . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('images/cabin', $filename, 'public');
                $photoPaths[] = $path;
            }
        }

        Cabin::create([
            'id_cabin' => $idCabin, // Set the auto-generated ID here
            'name' => $request->name,
            'description' => $request->description,
            'province' => $provinceName, // Store province name
            'regency' => $regencyName,   // Store regency name
            'location_address' => $request->location_address,
            'status' => $request->status,
            'cabin_photos' => $photoPaths,
        ]);

        return redirect()->route('admin.cabins.index')->with('success', 'Kabin berhasil ditambahkan!');
    }

    public function edit(Cabin $cabin)
    {
        return view('frontend.admin.editcabin', compact('cabin'));
    }

    public function update(Request $request, Cabin $cabin)
    {
       $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'province' => 'required|string|max:255', // This will be the ID from frontend
            'regency' => 'required|string|max:255',   // This will be the ID from frontend
            'location_address' => 'required|string|max:255',
            'cabin_photos' => 'nullable|array',
            'cabin_photos.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|boolean',
            'delete_photos' => 'nullable|array',
            'delete_photos.*' => 'string',
            'photo_order' => 'nullable|json',
        ]);

        $provinceId = $request->input('province');
        $regencyId = $request->input('regency');

        // --- Fetch Province Name for Update ---
        $provinceName = null;
        try {
            $response = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json");
            $provinces = $response->json();
            foreach ($provinces as $prov) {
                if ($prov['id'] == $provinceId) {
                    $provinceName = $prov['name'];
                    break;
                }
            }
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['province' => 'Could not fetch province data for update.']);
        }
        if (!$provinceName) {
            return back()->withInput()->withErrors(['province' => 'Invalid province selected for update.']);
        }

        // --- Fetch Regency Name for Update ---
        $regencyName = null;
        try {
            $response = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/regencies/{$provinceId}.json");
            $regencies = $response->json();
            foreach ($regencies as $reg) {
                if ($reg['id'] == $regencyId) {
                    $regencyName = $reg['name'];
                    break;
                }
            }
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['regency' => 'Could not fetch regency data for update.']);
        }
        if (!$regencyName) {
            return back()->withInput()->withErrors(['regency' => 'Invalid regency selected for update.']);
        }

        $currentPhotoPaths = (array)($cabin->cabin_photos ?? []);
        $updatedPhotoPaths = $currentPhotoPaths;

        // Handle deletions first
        if ($request->has('delete_photos') && is_array($request->delete_photos)) {
            foreach ($request->delete_photos as $photoToDelete) {
                // Ensure path is clean
                $cleanedPhotoToDelete = ltrim(str_replace(['\\', url('storage/') . '/'], ['/', ''], $photoToDelete), '/');

                // Only delete if the path exists in the current cabin's photos
                if (in_array($cleanedPhotoToDelete, $updatedPhotoPaths) && Storage::disk('public')->exists($cleanedPhotoToDelete)) {
                    Storage::disk('public')->delete($cleanedPhotoToDelete);
                    // Remove from our working array
                    $updatedPhotoPaths = array_diff($updatedPhotoPaths, [$cleanedPhotoToDelete]);
                }
            }
        }

        // Handle new photo uploads
        if ($request->hasFile('cabin_photos')) {
            foreach ($request->file('cabin_photos') as $photo) {
                // Generate a unique filename
                $filename = time() . '_' . Str::random(10) . '.' . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('images/cabin', $filename, 'public');
                $updatedPhotoPaths[] = $path; // Add new photo path
            }
        }

        // Reorder photos based on photo_order if provided
        if ($request->has('photo_order')) {
            $orderedPaths = json_decode($request->photo_order, true);
            if (is_array($orderedPaths)) {
                $finalPhotoOrder = [];
                // Prioritize the order from JS, but only include photos that are not deleted
                // and exist in the combined list (old and new).
                foreach ($orderedPaths as $path) {
                    $cleanedPath = ltrim(str_replace('\\', '/', $path), '/'); // Clean path for comparison
                    if (in_array($cleanedPath, $updatedPhotoPaths)) {
                        $finalPhotoOrder[] = $cleanedPath;
                    }
                }
                // Add any newly uploaded photos that might not be in the `photo_order` yet
                // (e.g., if user uploads new photos after setting order, they'll be appended)
                foreach ($updatedPhotoPaths as $path) {
                    if (!in_array($path, $finalPhotoOrder)) {
                        $finalPhotoOrder[] = $path;
                    }
                }
                $updatedPhotoPaths = $finalPhotoOrder;
            }
        }

        $cabin->update([
            'name' => $request->name,
            'description' => $request->description,
            'province' => $provinceName, // Now storing the name
            'regency' => $regencyName,   // Now storing the name
            'location_address' => $request->location_address,
            'status' => $request->status,
            'cabin_photos' => array_values($updatedPhotoPaths),
        ]);

        return redirect()->route('admin.cabins.index')->with('success', 'Kabin berhasil diperbarui!');
    }

    public function show(Cabin $cabin)
    {
        $cabin->load('rooms');
        return view('frontend.admin.detailcabin', compact('cabin'));
    }

    public function destroy(Cabin $cabin)
    {
        // Get all cabin photos
        $photoPaths = (array)($cabin->cabin_photos ?? []);

        // Delete associated room photos first
        foreach ($cabin->rooms as $room) {
            $roomPhotoPaths = (array)($room->room_photos ?? []);
            foreach ($roomPhotoPaths as $roomPhoto) {
                // Ensure path is cleaned before deletion to avoid issues with backslashes or unexpected prefixes
                $cleanedRoomPhoto = ltrim(str_replace(['\\', url('storage/') . '/'], ['/', ''], $roomPhoto), '/');
                if (Storage::disk('public')->exists($cleanedRoomPhoto)) {
                    Storage::disk('public')->delete($cleanedRoomPhoto);
                }
            }
            // Delete the room entry itself (assuming onDelete('cascade') is not set on the foreign key)
            $room->delete();
        }

        // Delete cabin photos
        if (!empty($photoPaths)) {
            foreach ($photoPaths as $photo) {
                if (is_string($photo)) {
                    // Ensure path is cleaned before deletion
                    $cleanedPhoto = ltrim(str_replace(['\\', url('storage/') . '/'], ['/', ''], $photo), '/');
                    if (Storage::disk('public')->exists($cleanedPhoto)) {
                        Storage::disk('public')->delete($cleanedPhoto);
                    }
                }
            }
        }

        // Finally, delete the cabin itself
        $cabin->delete();

        return redirect()->route('admin.cabins.index')->with('success', 'Kabin berhasil dihapus!');
    }
}