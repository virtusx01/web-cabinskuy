<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Pastikan pengguna sudah login
        if (!Auth::check()) {
            return redirect('/login'); // Arahkan ke halaman login jika belum login
        }

        $user = Auth::user();

        // 2. Pastikan pengguna aktif (sesuai logika LoginController Anda)
        if ($user->status == 0) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/login')->with('error', 'Akun Anda tidak aktif. Silakan hubungi administrator.');
        }

        // 3. Periksa apakah peran pengguna ada di dalam daftar peran yang diizinkan
        if (!in_array($user->role, $roles)) {
            // Jika peran tidak diizinkan, kembalikan 403 Forbidden
            abort(403, 'Unauthorized access: You do not have the necessary role to access this resource.');
        }

        return $next($request);
    }
}
