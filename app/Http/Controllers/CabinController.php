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
                // Nama file unik
                $filename = time() . '_' . Str::random(10) . '.' . $photo->getClientOriginalExtension();

                // Simpan ke disk 's3' di dalam folder 'images/cabin'
                $path = $photo->storeAs('images/cabin', $filename, 's3');
                
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
            // 'photo_order' => 'nullable|json', // Removed from validation, handle manually if needed
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

        // Get current photos from the database
        $currentPhotoPathsInDb = (array)($cabin->cabin_photos ?? []);
        
        // Initialize an array to hold the final set of photo paths for the cabin
        $finalPhotoPaths = $currentPhotoPathsInDb;

        // --- Handle deletions first ---
        // The delete_photos array from frontend should contain actual relative paths (e.g., 'images/cabin/file.jpg')
        $photosToDelete = $request->input('delete_photos', []); // Get array of paths to delete

        if (!empty($photosToDelete)) {
            foreach ($photosToDelete as $photoPathToDelete) {
                // Ensure the path to delete is clean and exists in current photos
                // Remove any leading slashes or potential URL prefixes that might be added by JS
                $cleanedPhotoPathToDelete = ltrim(str_replace('\\', '/', $photoPathToDelete), '/');
                
                // Only delete if it's actually in the database's photo list AND exists on S3
                if (in_array($cleanedPhotoPathToDelete, $finalPhotoPaths) && Storage::disk('s3')->exists($cleanedPhotoPathToDelete)) {
                    Storage::disk('s3')->delete($cleanedPhotoPathToDelete);
                    // Remove from our working array (finalPhotoPaths)
                    $finalPhotoPaths = array_diff($finalPhotoPaths, [$cleanedPhotoPathToDelete]);
                }
            }
        }

        // --- Handle new photo uploads ---
        $newlyUploadedPaths = [];
        if ($request->hasFile('cabin_photos')) {
            foreach ($request->file('cabin_photos') as $photo) {
                // Generate a unique filename
                $filename = time() . '_' . Str::random(10) . '.' . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('images/cabin', $filename, 's3');
                $newlyUploadedPaths[] = $path; // Add new photo path to a temporary array
            }
        }

        // Combine existing non-deleted photos with newly uploaded photos
        $finalPhotoPaths = array_merge($finalPhotoPaths, $newlyUploadedPaths);

        // --- Handle photo order if provided ---
        // If photo_order is submitted, it contains the *desired order* of all *retained* photos (old and new).
        // It's usually a JSON string of paths.
        if ($request->has('photo_order')) {
            $orderedPathsFromFrontend = json_decode($request->photo_order, true);
            if (is_array($orderedPathsFromFrontend)) {
                $reorderedFinalPaths = [];
                // Filter the ordered paths to only include those that are actually present
                // in our $finalPhotoPaths (i.e., not deleted and actually uploaded)
                foreach ($orderedPathsFromFrontend as $pathInOrder) {
                    $cleanedPathInOrder = ltrim(str_replace('\\', '/', $pathInOrder), '/'); // Clean path for comparison
                    if (in_array($cleanedPathInOrder, $finalPhotoPaths)) {
                        $reorderedFinalPaths[] = $cleanedPathInOrder;
                    }
                }
                // Also, add any photos that were newly uploaded and might not be in the photo_order yet
                // (e.g., if JS didn't include them in the `photo_order` field for some reason)
                foreach ($finalPhotoPaths as $path) {
                    if (!in_array($path, $reorderedFinalPaths)) {
                        $reorderedFinalPaths[] = $path;
                    }
                }
                $finalPhotoPaths = $reorderedFinalPaths;
            }
        }
        
        // Update cabin details
        $cabin->update([
            'name' => $request->name,
            'description' => $request->description,
            'province' => $provinceName, // Now storing the name
            'regency' => $regencyName,   // Now storing the name
            'location_address' => $request->location_address,
            'status' => $request->status,
            'cabin_photos' => array_values($finalPhotoPaths), // Re-index array keys from 0
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
        $cabinPhotoPaths = (array)($cabin->cabin_photos ?? []);

        // Delete associated room photos first
        foreach ($cabin->rooms as $room) {
            $roomPhotoPaths = (array)($room->room_photos ?? []);
            foreach ($roomPhotoPaths as $roomPhoto) {
                if (is_string($roomPhoto)) {
                    // Ensure path is cleaned before deletion to avoid issues with backslashes or unexpected prefixes
                    $cleanedRoomPhoto = ltrim(str_replace('\\', '/', $roomPhoto), '/');
                    if (Storage::disk('s3')->exists($cleanedRoomPhoto)) {
                        Storage::disk('s3')->delete($cleanedRoomPhoto);
                    }
                }
            }
            // Delete the room entry itself (assuming onDelete('cascade') is not set on the foreign key)
            $room->delete();
        }

        // Delete cabin photos
        if (!empty($cabinPhotoPaths)) {
            foreach ($cabinPhotoPaths as $photo) {
                if (is_string($photo)) {
                    // Ensure path is cleaned before deletion
                    $cleanedPhoto = ltrim(str_replace('\\', '/', $photo), '/');
                    if (Storage::disk('s3')->exists($cleanedPhoto)) {
                        Storage::disk('s3')->delete($cleanedPhoto);
                    }
                }
            }
        }

        // Finally, delete the cabin itself
        $cabin->delete();

        return redirect()->route('admin.cabins.index')->with('success', 'Kabin berhasil dihapus!');
    }
}