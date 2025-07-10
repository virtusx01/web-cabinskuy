<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str; 
class ResetPasswordController extends Controller
{
    
    public function showLinkRequestForm()
    {
        // Pastikan view 'auth.passwords.email' ada
        return view('frontend.password.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Menggunakan fasad Password bawaan Laravel untuk mengirim email
        $status = Password::sendResetLink($request->only('email'));

        // Cek status pengiriman email
        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        // Jika email tidak ditemukan atau ada error lain
        return back()->withInput($request->only('email'))
                     ->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, $token = null)
    {
        // Pastikan view 'auth.passwords.reset' ada
        return view('frontend.password.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        // Menggunakan fasad Password bawaan Laravel untuk mereset
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // Logika untuk memperbarui kata sandi pengguna
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60), // Opsional: reset remember token
                ])->save();
            }
        );

        // Arahkan pengguna berdasarkan status reset
        if ($status == Password::PASSWORD_RESET) {
            // Ganti 'login' dengan nama rute halaman login Anda
            return redirect()->route('backend.login')->with('status', __($status));
        }

        // Jika token tidak valid atau ada error lain
        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}
