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



Route::get('/register', [RegisterController::class, 'create'])->name('backend.register');
Route::post('/register', [RegisterController::class, 'store'])->name('backend.register.store');
Route::get('/register/success', [RegisterController::class, 'registrationSuccess'])->name('register.success');

Route::get('login', [LoginController::class, 'loginBackend'])->name('backend.login');
Route::post('login', [LoginController::class, 'loginAttempt'])->name('backend.login.attempt');
Route::post('logout', [LoginController::class, 'logoutBackend'])->name('backend.logout');

Route::get('auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

Route::get('/', [BerandaController::class, 'berandaFrontend'])->name('frontend.beranda');
Route::get('/api/regencies', [BerandaController::class, 'getRegencies'])->name('api.regencies');

// Route untuk menampilkan hasil list/pencarian kabin
Route::get('/kabin', [UserCabinController::class, 'index'])->name('frontend.kabin.index');

// Route untuk menerima data dari form pencarian di beranda
Route::post('/kabin/search', [UserCabinController::class, 'search'])->name('frontend.kabin.search');

// Route untuk menampilkan detail kabin
Route::get('/kabin/{cabin}', [UserCabinController::class, 'show'])->name('frontend.kabin.show');

Route::post('/booking/start', [BookingController::class, 'startBooking'])->name('frontend.booking.start');

Route::post('/api/booking/check-availability', [BookingController::class, 'checkAvailability'])->name('api.booking.check-availability');

Route::get('/api/rooms-by-cabin', [BookingController::class, 'getRoomsByCabin'])->name('api.rooms-by-cabin');

// PERBAIKAN: Pindahkan webhook Midtrans ke luar middleware auth
// Webhook harus dapat diakses tanpa autentikasi karena dipanggil oleh server Midtrans
Route::prefix('api')->group(function () {
    // Webhook should be accessible without authentication
    Route::post('/midtrans-notification', [PaymentController::class, 'handleNotification'])
        ->name('midtrans.notification'); // Name this whatever you prefer, 'api.midtrans.notification' also works
});

Route::middleware(['auth','role:customer'])->group(function () {
    Route::get('/profile', [UserController::class, 'showProfile'])->name('profile.user.show');
    Route::get('/profile/edit', [UserController::class, 'editProfile'])->name('profile.user.edit');
    Route::post('/profile/update', [UserController::class, 'updateProfile'])->name('profile.user.update');

    Route::get('/booking/create/{room:id_room}', [BookingController::class, 'create'])->name('frontend.booking.create');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('frontend.booking.store');
    Route::get('/my-bookings', [BookingController::class, 'index'])->name('frontend.booking.index');
    Route::get('/booking/{booking:id_booking}', [BookingController::class, 'show'])->name('frontend.booking.show');
    Route::patch('/booking/{booking:id_booking}/cancel', [BookingController::class, 'cancel'])->name('frontend.booking.cancel');

    Route::get('/payment/{booking:id_booking}', [PaymentController::class, 'showPaymentForm'])->name('frontend.payment.show');
    Route::post('/payment/{booking:id_booking}/process', [PaymentController::class, 'processPayment'])->name('frontend.payment.process');
    // RUTE BARU
    Route::post('/payment/{booking:id_booking}/change-method', [PaymentController::class, 'changePaymentMethod'])->name('frontend.payment.change');
    // RUTE BARU UNTUK POLLING
    Route::get('/booking/{booking:id_booking}/status', [BookingController::class, 'getBookingStatus'])->name('frontend.booking.status');

});

Route::middleware(['auth', 'role:admin,superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'adminBackend'])->name('beranda');

    Route::resource('/cabins', CabinController::class)->parameters(['cabins' => 'cabin']);
    Route::resource('/cabins.rooms', CabinRoomController::class)->except(['index', 'show'])->shallow();

    // Rombak Admin Booking Management Routes menggunakan resource
    Route::resource('/bookings', \App\Http\Controllers\Admin\AdminBookingController::class);

    // Tambahkan rute untuk aksi spesifik jika ingin memisahkan dari resource
    Route::post('/bookings/{booking}/confirm', [\App\Http\Controllers\Admin\AdminBookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{booking}/reject', [\App\Http\Controllers\Admin\AdminBookingController::class, 'reject'])->name('bookings.reject');
    Route::post('/bookings/{booking}/cancel', [\App\Http\Controllers\Admin\AdminBookingController::class, 'cancel'])->name('bookings.cancel');

    Route::get('/reports/financial', [PrintReportController::class, 'financial'])->name('reports.financial');
    Route::get('/reports/booking', [PrintReportController::class, 'booking'])->name('reports.booking');
    Route::get('/reports/financial/pdf', [PrintReportController::class, 'financialPdf'])->name('reports.financial.pdf');
    Route::get('/reports/booking/pdf', [PrintReportController::class, 'bookingPdf'])->name('reports.booking.pdf');


    Route::middleware(['role:superadmin'])->group(function () {
        Route::resource('employees', SAController::class);
    });

});



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



// Public QR Code Access (NON-AUTHENTICATED)
// This route allows anyone with the correct token to view a basic booking confirmation.
// The QR code will link to this route.
Route::get('/qr-booking/{token}', [BookingController::class, 'showQrCodeAccessPage'])->name('frontend.qrcode.show');

// Public PDF Access (can be accessed via token from QR page)
// This route can be accessed by authenticated users (via id_booking) or non-authenticated (via qr_access_token)
// It needs to be outside the 'auth' middleware so the QR code can use it directly.
Route::get('/booking-pdf/{identifier}', [BookingController::class, 'generateBookingPdf'])->name('frontend.booking.pdf');
