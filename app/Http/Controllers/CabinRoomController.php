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
            // 'id_room' => 'required|string|max:10|unique:cabin_rooms,id_room', // Remove this validation
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
                $filename = time() . '_' . $photo->getClientOriginalName();
                $path = $photo->storeAs('images/cabin_rooms', $filename, 'public');
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

        // $room->room_photos should already be an array due to model casting.
        // The Blade @php block has its own fallback, which is fine.
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

        $currentPhotoPaths = $room->room_photos ?? []; // Get existing photos from DB

        // Handle deletions first
        if ($request->has('delete_photos') && is_array($request->delete_photos)) {
            foreach ($request->delete_photos as $photoToDelete) {
                // Ensure the path is clean before attempting deletion
                $cleanedPhotoToDelete = ltrim(str_replace('\\', '/', $photoToDelete), '/');
                
                // Only delete if the path exists in the current room's photos
                // and if the file actually exists on disk.
                if (in_array($cleanedPhotoToDelete, $currentPhotoPaths) && Storage::disk('public')->exists($cleanedPhotoToDelete)) {
                    Storage::disk('public')->delete($cleanedPhotoToDelete);
                    // Remove from our working array
                    $currentPhotoPaths = array_diff($currentPhotoPaths, [$cleanedPhotoToDelete]);
                }
            }
        }

        // Handle new photo uploads
        if ($request->hasFile('room_photos')) {
            foreach ($request->file('room_photos') as $photo) {
                $filename = time() . '_' . Str::random(10) . '.' . $photo->getClientOriginalExtension(); // Add random string to filename
                $path = $photo->storeAs('images/cabin_rooms', $filename, 'public');
                $currentPhotoPaths[] = $path;
            }
        }

        $room->update([
            'typeroom' => $request->typeroom,
            'description' => $request->description,
            'price' => $request->price,
            'max_guests' => $request->max_guests,
            'slot_room' => $request->slot_room,
            'status' => $request->status,
            'room_photos' => array_values($currentPhotoPaths), // Re-index array after diff and add
        ]);

        return redirect()->route('admin.cabins.show', $room->id_cabin)->with('success', 'Ruangan berhasil diperbarui!');
    }

    public function destroy(CabinRoom $room)
    {
        $photoPaths = $room->room_photos ?? [];

        if (!empty($photoPaths)) {
            foreach ($photoPaths as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }

        $room->delete();

        // Redirect ke halaman detail kabin setelah menghapus ruangan
        return redirect()->route('admin.cabins.show', $room->id_cabin)->with('success', 'Ruangan berhasil dihapus!');
    }
}