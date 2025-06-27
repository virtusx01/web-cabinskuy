<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan user sudah login
        if (! $request->user()) {
            // Jika belum login, bisa diarahkan ke halaman login
            return redirect('login');
        }

        // Loop melalui role yang diizinkan yang dikirim dari route
        foreach ($roles as $role) {
            // Ganti 'role' dengan nama kolom di tabel user Anda
            // Contoh: if ($request->user()->role == $role)
            if ($request->user()->hasRole($role)) { // Asumsi Anda punya method hasRole() di model User
                return $next($request);
            }
        }

        // Jika user tidak punya role yang diizinkan, tampilkan halaman 403 Forbidden
        abort(403, 'UNAUTHORIZED ACTION.');
    }
}