<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function loginBackend()
    {
        return view('frontend.login', [
            'title' => 'Login Page',
        ]);
    }

    public function loginAttempt(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to log the user in
        if (Auth::attempt($credentials)) {
            // Check if the authenticated user's status is 0 (inactive)
            if (Auth::user()->status == 0) {
                Auth::logout(); // Log out the inactive user immediately
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->with('error', 'Akun Anda tidak aktif. Silakan hubungi administrator.');
            }

            // If the user is active, proceed with role-based redirection
            if (Auth::user()->role == 'admin') {
                return redirect()->route('admin.beranda');
            } else {
                return redirect()->route('frontend.beranda');
            }
        }

        // If Auth::attempt failed for any other reason (e.g., wrong credentials)
        return back()->with('error', 'Email atau kata sandi salah. Silakan coba lagi.');
    }

    public function logoutBackend(Request $request) // Inject Request for session handling
    {
        Auth::logout();
        $request->session()->invalidate(); // Invalidate the current session
        $request->session()->regenerateToken(); // Regenerate the CSRF token
        return redirect()->route('backend.login');
    }
}