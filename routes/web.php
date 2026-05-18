<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;

// Route Landing Page dengan controller
Route::get('/', [HomeController::class, 'index'])->name('landing-page');
Route::get('/search', [HomeController::class, 'search'])->name('films.search');
Route::get('/films/{film}', [HomeController::class, 'filmDetail'])->name('films.detail');
Route::get('/about', function () { return view('about'); })->name('about');
Route::get('/faq', function () { return view('faq'); })->name('faq');

// Route Google Auth
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('login.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Route Dashboard & Auth Middleware
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return redirect('/admin');
    })->name('dashboard');

    Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.dashboard');
    Route::resource('admin/films', App\Http\Controllers\Admin\FilmController::class)->names('admin.films');
    Route::resource('admin/studios', App\Http\Controllers\Admin\StudioController::class)->names('admin.studios');
    Route::resource('admin/schedules', App\Http\Controllers\Admin\ScheduleController::class)->names('admin.schedules');
    Route::get('admin/bookings', [App\Http\Controllers\Admin\BookingManagementController::class, 'index'])->name('admin.bookings.index');
    Route::get('admin/customers', [App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('admin.customers.index');


    Route::get('/customer', [HomeController::class, 'index'])->name('customer.dashboard');

    // Proses saat user klik kursi
    Route::post('/seat/select', [BookingController::class, 'selectSeat'])->name('seat.select');

    // Halaman payment (yang dibuat Hasan)
    Route::get('/payment/{seat_id}', [BookingController::class, 'showPayment'])->name('payment.page');

    // Konfirmasi pembayaran
    Route::post('/payment/confirm', [BookingController::class, 'confirmPayment'])->name('payment.confirm');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Booking Routes - Only for authenticated customers
    Route::prefix('booking')->name('booking.')->group(function () {
        Route::get('schedule/{schedule}', [BookingController::class, 'show'])->name('show');
        Route::post('store', [BookingController::class, 'store'])->name('store');

        // Payment flow (Strategy Pattern)
        Route::get('payment/{booking}', [BookingController::class, 'payment'])->name('payment');
        Route::post('initiate-payment/{booking}', [BookingController::class, 'initiatePayment'])->name('initiate-payment');
        Route::get('process-payment/{booking}/{payment}', [BookingController::class, 'processPayment'])->name('process-payment');
        Route::post('confirm-payment/{booking}/{payment}', [BookingController::class, 'confirmPayment'])->name('confirm-payment');

        Route::get('confirmation/{booking}', [BookingController::class, 'confirmation'])->name('confirmation');
        Route::get('history', [BookingController::class, 'history'])->name('history');
        Route::get('tickets', [BookingController::class, 'tickets'])->name('tickets');
        Route::post('cancel/{booking}', [BookingController::class, 'cancel'])->name('cancel');

        // AJAX endpoints
        Route::get('available-seats/{schedule}', [BookingController::class, 'getAvailableSeats'])->name('available-seats');
        Route::get('details/{booking}', [BookingController::class, 'getBookingDetails'])->name('details');
        Route::get('check-payment/{payment}', [BookingController::class, 'checkPaymentStatus'])->name('check-payment');
    });
});

require __DIR__.'/auth.php';
