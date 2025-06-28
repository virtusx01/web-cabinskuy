<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     * This method might be used for an admin view to list all users,
     * or could be repurposed/removed if not needed in the frontend.
     */
    public function index()
    {
        // For demonstration, this returns a list of all users.
        // If 'frontend.profile' is specifically for a single logged-in user,
        // consider using showProfile() or editProfile() instead for the main user profile link.
        $users = User::orderBy('updated_at', 'desc')->get();
        return view('frontend.profile', [
            'judul' => 'Data User',
            'index' => $users
        ]);
    }

    /**
     * Show the detailed profile of the currently authenticated user.
     * This method is intended to display the user's main profile page,
     * including their name, email, and a link to edit their profile.
     */
    public function showProfile()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Ensure the user is logged in before accessing their profile
        if (!$user) {
            return redirect()->route('backend.login')->with('error', 'Anda harus login untuk melihat profil Anda.');
        }
        $daysJoined = Carbon::parse($user->created_at)->diffInDays(Carbon::now());

        // Get total SUCCESSFUL bookings for the user
        // Assuming your 'Booking' model has a 'status' column and 'confirmed' is the status for success.
        $totalSuccessBookings = $user->bookings()->where('status', 'confirmed')->count();

        // Determine member role based on successful bookings
        $memberRole = ($totalSuccessBookings > 2) ? 'Member Premium' : 'Member Classic';

        // Return the view for showing the user's profile details
        return view('frontend.profile_user_show', [
            'judul' => 'Profil Pengguna Saya',
            'user' => $user,
            'daysJoined' => $daysJoined,
            'totalSuccessBookings' => $totalSuccessBookings, // Renamed variable
            'memberRole' => $memberRole, // Pass the determined role to the view
        ]);
    }

    /**
     * Show the form for editing the currently authenticated user's profile.
     * This will typically display the user's current email and allow password changes.
     */
    public function editProfile()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Ensure the user is logged in before accessing the edit profile form
        if (!$user) {
            return redirect()->route('backend.login')->with('error', 'Anda harus login untuk mengedit profil.');
        }

        // Return the view for the user's profile edit form
        return view('frontend.profile_user_edit', [
            'judul' => 'Edit Profil Pengguna',
            'user' => $user
        ]);
    }

    /**
     * Update the authenticated user's profile including name, email, password, and photo.
     * This method handles the POST request from the edit profile form.
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Ensure the user is logged in before attempting to update their profile
        if (!$user) {
            return redirect()->route('backend.login')->with('error', 'Anda harus login untuk memperbarui profil.');
        }

        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            // current_password is only required if the user has an existing password AND they are trying to set a new one
            'current_password' => ($user->password !== null && $request->filled('password')) ? 'required|string' : 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max, changed field name
        ]);

        // Update name if it's different from the current name
        if ($request->name !== $user->name) {
            $user->name = $request->name;
        }

        // Update email if it's different from the current email
        if ($request->email !== $user->email) {
            $user->email = $request->email;
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old custom profile photo if it exists
            if ($user->profile_photo_path && !str_starts_with($user->profile_photo_path, 'http')) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            // Store new profile photo
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
            $user->google_avatar_url = null; // Clear Google avatar if a custom photo is uploaded
        }

        // Handle photo removal
        if ($request->has('remove_photo') && $request->remove_photo == '1') {
            if ($user->profile_photo_path && !str_starts_with($user->profile_photo_path, 'http')) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $user->profile_photo_path = null;
            // Optionally, if you want to revert to google_avatar_url after removing custom photo,
            // you'd need to fetch it again or store it separately. For now, it just becomes null.
        }

        // Handle password update logic
        if ($request->filled('password')) {
            // Only require current_password if the user HAS a password set in the database
            if ($user->password !== null) {
                // Verify if the provided current password matches the user's actual password
                if (!Hash::check($request->current_password, $user->password)) {
                    throw ValidationException::withMessages([
                        'current_password' => 'Kata sandi saat ini tidak cocok.',
                    ]);
                }
            } else {
                // If user->password is null (e.g., Google login), then current_password is not applicable
                // No need to check current_password in this case, they are creating one.
            }

            // Hash and set the new password
            $user->password = Hash::make($request->password);
        }

        // Save all changes to the database
        $user->save();

        // Prepare success message based on what was updated
        $updatedItems = [];
        if ($user->wasChanged('name')) {
            $updatedItems[] = 'nama';
        }
        if ($user->wasChanged('email')) {
            $updatedItems[] = 'email';
        }
        if ($request->filled('password')) {
            $updatedItems[] = 'kata sandi';
        }
        if ($request->hasFile('profile_photo')) {
            $updatedItems[] = 'foto profil';
        }
        if ($request->has('remove_photo') && $request->remove_photo == '1') {
            $updatedItems[] = 'foto profil dihapus';
        }

        $message = 'Profil berhasil diperbarui!';
        if (!empty($updatedItems)) {
            $message = 'Berhasil memperbarui: ' . implode(', ', $updatedItems) . '.';
        }

        // Redirect back to the user's detailed profile page with a success message
        return redirect()->route('profile.user.show')->with('success', $message);
    }

    /**
     * Handle avatar upload via AJAX (optional enhancement)
     */
    public function uploadAvatar(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Delete old custom profile photo if it exists and is not a Google avatar URL
            if ($user->profile_photo_path && !str_starts_with($user->profile_photo_path, 'http')) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            // Store new avatar
            $path = $request->file('avatar')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
            $user->google_avatar_url = null; // Clear Google avatar if a custom photo is uploaded
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Avatar berhasil diperbarui!',
                'avatar_url' => asset('storage/' . $path)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload avatar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     * (Currently not implemented for frontend user profile management directly)
     */
    public function create()
    {
        // This method can be implemented if there's a need for a frontend user creation form.
        // Currently, user registration is handled by RegisterController.
    }

    /**
     * Store a newly created resource in storage.
     * (Currently not implemented for frontend user profile management directly)
     */
    public function store(Request $request)
    {
        // This method can be implemented to store new user data,
        // but is typically handled by RegisterController for new registrations.
    }

    /**
     * Display the specified resource.
     * (Generic show method, consider if this is distinct from showProfile for specific use cases)
     */
    public function show(string $id)
    {
        // If needed to show profiles of other users by ID.
        // For logged-in user's own profile, showProfile() is preferred.
    }

    /**
     * Show the form for editing the specified resource.
     * (Generic edit method, consider if this is distinct from editProfile for specific use cases)
     */
    public function edit(string $id)
    {
        // If needed to edit profiles of other users by ID (e.g., by admin).
        // For logged-in user's own profile, editProfile() is preferred.
    }

    /**
     * Update the specified resource in storage.
     * (Generic update method, consider if this is distinct from updateProfile for specific use cases)
     */
    public function update(Request $request, string $id)
    {
        // If needed to update profiles of other users by ID (e.g., by admin).
        // For logged-in user's own profile, updateProfile() is preferred.
    }

    /**
     * Remove the specified resource from storage.
     * (Currently not implemented for frontend user profile management directly)
     */
    public function destroy(string $id)
    {
        // This method can be implemented if users are allowed to delete their own accounts
        // or for admin functionality to delete user accounts.
    }
}