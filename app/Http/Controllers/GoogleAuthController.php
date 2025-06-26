<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str; 

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();

            // Cek apakah user sudah ada di database berdasarkan google_id
            $existingUser = User::where('google_id', $user->id)->first();

            if ($existingUser) {
                // Jika user sudah ada, login kan
                Auth::login($existingUser);
                return redirect()->intended('/')->with('success', 'Login successful with Google!');
            } else {
                // Jika user belum ada, cek berdasarkan email
                $emailUser = User::where('email', $user->email)->first();

                if ($emailUser) {
                    // Jika email sudah terdaftar tapi belum ada google_id, update user
                    $emailUser->google_id = $user->id;
                    $emailUser->avatar = $user->avatar;
                    $emailUser->save();
                    Auth::login($emailUser);
                    return redirect()->intended('/')->with('success', 'Account linked and logged in with Google!');
                } else {
                    // Jika user belum ada sama sekali, buat user baru
                    $newUser = User::create([
                        'name' => $user->name,
                        'email' => $user->email,
                        'google_id' => $user->id,
                        'avatar' => $user->avatar,
                        'password' => bcrypt(Str::random(16)), // Buat password acak
                        // Kamu bisa menambahkan kolom lain jika diperlukan
                    ]);

                    Auth::login($newUser);
                    return redirect()->intended('/')->with('success', 'Account created and logged in with Google!');
                }
            }
        } catch (\Exception $e) {
            // Tangani error, misalnya gagal koneksi ke Google atau callback bermasalah
            return redirect()->route('backend.login')->with('error', 'Unable to login with Google. Please try again or use another method. Error: ' . $e->getMessage());
        }
    }
}
