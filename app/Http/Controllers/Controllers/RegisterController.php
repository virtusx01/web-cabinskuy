<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    public function create()
    {
        return view('frontend.register', ['title' => 'Register - Cabinskuy']);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', 'min:2'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.min' => 'Nama minimal harus 2 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Mohon berikan alamat email yang valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('backend.register')
                            ->withErrors($validator)
                            ->withInput($request->except('password', 'password_confirmation'));
        }

        try {
            $user = User::create([
                'name' => trim($request->name),
                'email' => strtolower(trim($request->email)),
                'password' => Hash::make($request->password),
            ]);

            event(new Registered($user));

            return redirect()->route('backend.register')
                            ->with('registration_success_trigger', true)
                            ->with('success_message', 'Registrasi berhasil! Anda akan segera diarahkan ke halaman Login .');

        } catch (\Exception $e) {
            Log::error('Kegagalan Registrasi: ' . $e->getMessage() . ' | Data: ' . json_encode($request->except('password', 'password_confirmation')) . ' | Stack Trace: ' . $e->getTraceAsString());
            
            return redirect()->route('backend.register')
                            ->withInput($request->except('password', 'password_confirmation'))
                            ->with('error', 'Registrasi gagal. Mohon coba lagi atau hubungi administrator.');
        }
    }
}