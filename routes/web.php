<?php

use App\Http\Controllers\AdminBookingController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\SuperAdmin\SAController;
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
use App\Http\Controllers\QRCodeController; // Make sure this is imported

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Authentication and Registration Routes
Route::get('/register', [RegisterController::class, 'create'])->name('backend.register');
Route::post('/register', [RegisterController::class, 'store'])->name('backend.register.store');
Route::get('/register/success', [RegisterController::class, 'registrationSuccess'])->name('register.success');

Route::get('login', [LoginController::class, 'loginBackend'])->name('backend.login');
Route::post('login', [LoginController::class, 'loginAttempt'])->name('backend.login.attempt');
Route::post('logout', [LoginController::class, 'logoutBackend'])->name('backend.logout');

// Google OAuth
Route::get('auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

// Frontend Public Routes
Route::get('/', [BerandaController::class, 'berandaFrontend'])->name('frontend.beranda');
Route::get('/api/regencies', [BerandaController::class, 'getRegencies'])->name('api.regencies');

// Cabin & Room Browse
Route::get('/kabin', [UserCabinController::class, 'index'])->name('frontend.kabin.index');
Route::post('/kabin/search', [UserCabinController::class, 'search'])->name('frontend.kabin.search');
Route::get('/kabin/{cabin}', [UserCabinController::class, 'show'])->name('frontend.kabin.show');

// Booking Start (stores details in session for guests)
Route::post('/booking/start', [BookingController::class, 'startBooking'])->name('frontend.booking.start');

// API for availability check and room data
Route::post('/api/booking/check-availability', [BookingController::class, 'checkAvailability'])->name('api.booking.check-availability');
Route::get('/api/rooms-by-cabin', [BookingController::class, 'getRoomsByCabin'])->name('api.rooms-by-cabin');

// Midtrans Webhook (MUST be outside 'auth' middleware)
Route::post('/api/midtrans-notification', [PaymentController::class, 'handleNotification'])->name('midtrans.notification');


// Authenticated User Routes (Customer Role)
Route::middleware(['auth','role:customer'])->group(function () {
    // User Profile Management
    Route::get('/profile', [UserController::class, 'showProfile'])->name('profile.user.show');
    Route::get('/profile/edit', [UserController::class, 'editProfile'])->name('profile.user.edit');
    Route::post('/profile/update', [UserController::class, 'updateProfile'])->name('profile.user.update');

    // Booking Process for Authenticated Users
    Route::get('/booking/create/{room:id_room}', [BookingController::class, 'create'])->name('frontend.booking.create');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('frontend.booking.store');
    Route::get('/my-bookings', [BookingController::class, 'index'])->name('frontend.booking.index');
    Route::get('/booking/{booking:id_booking}', [BookingController::class, 'show'])->name('frontend.booking.show');
    Route::patch('/booking/{booking:id_booking}/cancel', [BookingController::class, 'cancel'])->name('frontend.booking.cancel');

    // Payment Processing
    Route::get('/payment/{booking:id_booking}', [PaymentController::class, 'showPaymentForm'])->name('frontend.payment.show');
    Route::post('/payment/{booking:id_booking}/process', [PaymentController::class, 'processPayment'])->name('frontend.payment.process');
    Route::post('/payment/{booking:id_booking}/change-method', [PaymentController::class, 'changePaymentMethod'])->name('frontend.payment.change');

    // Polling route for booking status (from frontend)
    Route::get('/booking/{booking:id_booking}/status', [BookingController::class, 'getBookingStatus'])->name('frontend.booking.status');
});

// Admin & Superadmin Routes
Route::middleware(['auth', 'role:admin,superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'adminBackend'])->name('beranda');

    // Cabin and Room Management
    Route::resource('/cabins', CabinController::class)->parameters(['cabins' => 'cabin']);
    Route::resource('/cabins.rooms', CabinRoomController::class)->except(['index', 'show'])->shallow();

    // Admin Booking Management
    Route::resource('/bookings', \App\Http\Controllers\Admin\AdminBookingController::class);
    Route::post('/bookings/{booking}/confirm', [\App\Http\Controllers\Admin\AdminBookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{booking}/reject', [\App\Http\Controllers\Admin\AdminBookingController::class, 'reject'])->name('bookings.reject');
    Route::post('/bookings/{booking}/cancel', [\App\Http\Controllers\Admin\AdminBookingController::class, 'cancel'])->name('bookings.cancel');

    // Reports
    Route::get('/reports/financial', [PrintReportController::class, 'financial'])->name('reports.financial');
    Route::get('/reports/booking', [PrintReportController::class, 'booking'])->name('reports.booking');
    Route::get('/reports/financial/pdf', [PrintReportController::class, 'financialPdf'])->name('reports.financial.pdf');
    Route::get('/reports/booking/pdf', [PrintReportController::class, 'bookingPdf'])->name('reports.booking.pdf');

    // Superadmin Specific Routes
    Route::middleware(['role:superadmin'])->group(function () {
        Route::resource('employees', SAController::class);
    });
});

// Language Switcher
Route::get('lang/{locale}', function ($locale) {
    if (! in_array($locale, ['en', 'id'])) {
        abort(400);
    }
    Session::put('locale', $locale);
    App::setLocale($locale);
    return redirect()->back();
});

// QR Code Validation Routes (Public - No Authentication Required)
Route::get('/qr/validate/{token}', [QRCodeController::class, 'validateQRCode'])->name('qr.validate');
Route::get('/api/qr/validate/{token}', [QRCodeController::class, 'validateQRCodeAPI'])->name('api.qr.validate');

// ROUTE BARU UNTUK DOWNLOAD PDF VALIDASI
Route::get('/qr/pdf/{token}', [QRCodeController::class, 'generatePdf'])->name('qr.pdf');