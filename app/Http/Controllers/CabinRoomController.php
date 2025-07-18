<?php

namespace App\Http\Controllers;

use App\Models\Cabin;
use App\Models\CabinRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // Make sure to import Str for slug generation or other string manipulation

class CabinRoomController extends Controller
{
    public function create(Cabin $cabin)
    {
        return view('frontend.admin.addroom', compact('cabin'));
    }

    public function store(Request $request, Cabin $cabin)
    {
        $request->validate([
            'typeroom' => 'required|string|in:Standard,Deluxe,Executive,Family Suite',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'max_guests' => 'required|integer|min:1',
            'slot_room' => 'required|integer|min:1',
            'room_photos' => 'nullable|array',
            'room_photos.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|boolean',
        ]);

        $photoPaths = [];
        if ($request->hasFile('room_photos')) {
            foreach ($request->file('room_photos') as $photo) {
                // Generate a unique filename using Str::random
                $filename = time() . '_' . Str::random(10) . '.' . $photo->getClientOriginalExtension();
                
                // Simpan ke disk 's3' di dalam folder 'images/cabin_rooms'
                $path = $photo->storeAs('images/cabin_rooms', $filename, 's3');
                $photoPaths[] = $path;
            }
        }

        // Generate automatic id_room
        $latestRoom = CabinRoom::orderBy('created_at', 'desc')->first();
        $newRoomNumber = $latestRoom ? (int) substr($latestRoom->id_room, 4) + 1 : 1;
        $generatedIdRoom = 'ROOM' . str_pad($newRoomNumber, 3, '0', STR_PAD_LEFT); // ROOM001, ROOM002, etc.

        // Ensure the generated ID is unique (though with auto-incrementing numbers, collision is unlikely)
        while (CabinRoom::where('id_room', $generatedIdRoom)->exists()) {
            $newRoomNumber++;
            $generatedIdRoom = 'ROOM' . str_pad($newRoomNumber, 3, '0', STR_PAD_LEFT);
        }

        $cabin->rooms()->create([
            'id_room' => $generatedIdRoom, // Use the generated ID
            'typeroom' => $request->typeroom,
            'description' => $request->description,
            'price' => $request->price,
            'max_guests' => $request->max_guests,
            'slot_room' => $request->slot_room,
            'status' => $request->status,
            'room_photos' => $photoPaths, // Model casting handles JSON encoding
        ]);

        // Redirect ke halaman detail kabin setelah menambahkan ruangan
        return redirect()->route('admin.cabins.show', $cabin->id_cabin)->with('success', 'Ruangan berhasil ditambahkan!');
    }

    public function edit(CabinRoom $room)
    {
        // Eager load the 'cabin' relationship
        $room->load('cabin');

        return view('frontend.admin.editroom', compact('room'));
    }

    public function update(Request $request, CabinRoom $room)
    {
        $request->validate([
            'typeroom' => 'required|string|in:Standard,Deluxe,Executive,Family Suite',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'max_guests' => 'required|integer|min:1',
            'slot_room' => 'required|integer|min:1',
            'room_photos' => 'nullable|array', // New photos being uploaded
            'room_photos.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|boolean',
            'delete_photos' => 'nullable|array', // Array of photo paths to delete
            'delete_photos.*' => 'string', // Each element should be a string path
        ]);

        // Get existing photos from DB (which should be an array due to model casting)
        $currentPhotoPathsInDb = (array)($room->room_photos ?? []); 
        
        // This array will hold the final set of photo paths to be saved back to the database
        $finalPhotoPaths = $currentPhotoPathsInDb;

        // --- Handle deletions first ---
        $photosToDelete = $request->input('delete_photos', []); // Get array of paths to delete

        if (!empty($photosToDelete)) {
            foreach ($photosToDelete as $photoPathToDelete) {
                // IMPORTANT: Clean the path received from frontend before comparing and deleting.
                // It might contain backslashes or other unexpected characters if not carefully handled in JS.
                $cleanedPhotoPathToDelete = ltrim(str_replace('\\', '/', $photoPathToDelete), '/');
                
                // Only delete if the path exists in the current room's photos from DB
                // AND if the file actually exists on S3.
                if (in_array($cleanedPhotoPathToDelete, $finalPhotoPaths) && Storage::disk('s3')->exists($cleanedPhotoPathToDelete)) {
                    Storage::disk('s3')->delete($cleanedPhotoPathToDelete);
                    
                    // Remove the path from our working array ($finalPhotoPaths)
                    $finalPhotoPaths = array_diff($finalPhotoPaths, [$cleanedPhotoPathToDelete]);
                }
            }
        }

        // --- Handle new photo uploads ---
        $newlyUploadedPaths = [];
        if ($request->hasFile('room_photos')) {
            foreach ($request->file('room_photos') as $photo) {
                // Generate a unique filename using Str::random
                $filename = time() . '_' . Str::random(10) . '.' . $photo->getClientOriginalExtension();
                
                // Simpan ke disk 's3' di dalam folder 'images/cabin_rooms'
                $path = $photo->storeAs('images/cabin_rooms', $filename, 's3');
                $newlyUploadedPaths[] = $path; // Add new photo path to a temporary array
            }
        }

        // Combine existing non-deleted photos with newly uploaded photos
        $finalPhotoPaths = array_merge($finalPhotoPaths, $newlyUploadedPaths);

        $room->update([
            'typeroom' => $request->typeroom,
            'description' => $request->description,
            'price' => $request->price,
            'max_guests' => $request->max_guests,
            'slot_room' => $request->slot_room,
            'status' => $request->status,
            'room_photos' => array_values($finalPhotoPaths), // Re-index array keys from 0
        ]);

        return redirect()->route('admin.cabins.show', $room->id_cabin)->with('success', 'Ruangan berhasil diperbarui!');
    }

    public function destroy(CabinRoom $room)
    {
        // Get all photo paths associated with this room
        $photoPaths = (array)($room->room_photos ?? []);

        if (!empty($photoPaths)) {
            foreach ($photoPaths as $photo) {
                if (is_string($photo)) { // Ensure it's a string before attempting to delete
                    // IMPORTANT: Clean the path before deleting from S3
                    // The path stored in the DB should be relative (e.g., 'images/cabin_rooms/file.jpg')
                    // If your DB stores URLs, you need to extract the path.
                    // Assuming DB stores relative paths for S3
                    $cleanedPhoto = ltrim(str_replace('\\', '/', $photo), '/');
                    
                    if (Storage::disk('s3')->exists($cleanedPhoto)) {
                        Storage::disk('s3')->delete($cleanedPhoto);
                    }
                }
            }
        }

        // Delete the room record from the database
        $room->delete();

        // Redirect ke halaman detail kabin setelah menghapus ruangan
        return redirect()->route('admin.cabins.show', $room->id_cabin)->with('success', 'Ruangan berhasil dihapus!');
    }
}