<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App; 
use Illuminate\Support\Facades\Session;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\CabinController;
use App\Http\Controllers\CabinRoomController;
use App\Http\Controllers\UserCabinController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PrintReportController; 
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;

Route::get('/register', [RegisterController::class, 'create'])->name('backend.register');
Route::post('/register', [RegisterController::class, 'store'])->name('backend.register.store');
Route::get('/register/success', [RegisterController::class, 'registrationSuccess'])->name('register.success');

Route::get('login', [LoginController::class, 'loginBackend'])->name('backend.login');
Route::post('login', [LoginController::class, 'loginAttempt'])->name('backend.login.attempt');
Route::post('logout', [LoginController::class, 'logoutBackend'])->name('backend.logout');

Route::get('auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

Route::get('/', [BerandaController::class, 'berandaFrontend'])->name('frontend.beranda');

Route::get('lang/{locale}', function ($locale) {
    // Pastikan locale yang diminta didukung (misal: 'en', 'id')
    if (! in_array($locale, ['en', 'id'])) { 
        abort(400); // Bad Request jika locale tidak valid
    }

    // Simpan locale yang dipilih di session
    Session::put('locale', $locale); 

    // Atur locale aplikasi untuk request saat ini
    App::setLocale($locale); 

    // Redirect kembali ke halaman sebelumnya
    return redirect()->back(); 
});