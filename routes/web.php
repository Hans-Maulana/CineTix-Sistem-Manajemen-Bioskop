<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\FilmController;
use App\Http\Controllers\Admin\StudioController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\BookingManagementController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Route;


// 1. ROUTE PUBLIK (Bisa Diakses Login & Guest)

Route::get('/', [HomeController::class, 'index'])->name('landing-page');
Route::get('/search', [HomeController::class, 'search'])->name('films.search');
Route::get('/films/{film}', [HomeController::class, 'filmDetail'])->name('films.detail');
Route::get('/about', function () { return view('about'); })->name('about');
Route::get('/faq', function () { return view('faq'); })->name('faq');

// Route Google Auth
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('login.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Jalur Tiket Bioskop Yang Terbuka Untuk Guest & Member
Route::prefix('booking')->name('booking.')->group(function () {
    Route::get('schedule/{schedule}', [BookingController::class, 'show'])->name('show');
    Route::post('store', [BookingController::class, 'store'])->name('store');

    // Alur Pembayaran
    Route::get('payment/{booking}', [BookingController::class, 'payment'])->name('payment');
    Route::post('initiate-payment/{booking}', [BookingController::class, 'initiatePayment'])->name('initiate-payment');
    Route::get('process-payment/{booking}/{payment}', [BookingController::class, 'processPayment'])->name('process-payment');
    Route::post('confirm-payment/{booking}/{payment}', [BookingController::class, 'confirmPayment'])->name('confirm-payment');
    Route::get('confirmation/{booking}', [BookingController::class, 'confirmation'])->name('confirmation');

    // AJAX endpoints
    Route::get('available-seats/{schedule}', [BookingController::class, 'getAvailableSeats'])->name('available-seats');
    Route::get('details/{booking}', [BookingController::class, 'getBookingDetails'])->name('details');
    Route::get('check-payment/{payment}', [BookingController::class, 'checkPaymentStatus'])->name('check-payment');
});



// 2. ROUTE PRIVAT 

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return redirect('/admin');
    })->name('dashboard');

    // Ruang Admin
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::resource('admin/films', FilmController::class)->names('admin.films');
    Route::resource('admin/studios', StudioController::class)->names('admin.studios');
    Route::resource('admin/schedules', ScheduleController::class)->names('admin.schedules');
    Route::get('admin/bookings', [BookingManagementController::class, 'index'])->name('admin.bookings.index');
    Route::get('admin/customers', [CustomerController::class, 'index'])->name('admin.customers.index');

    Route::get('/customer', [HomeController::class, 'index'])->name('customer.dashboard');

    // Proses internal klik kursi
    Route::post('/seat/select', [BookingController::class, 'selectSeat'])->name('seat.select');
    Route::get('/payment/{seat_id}', [BookingController::class, 'showPayment'])->name('payment.page');
    Route::post('/payment/confirm', [BookingController::class, 'confirmPayment'])->name('payment.confirm');

    // Profile Akun
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Fitur Booking yang HANYA boleh diakses Member Terdaftar
    Route::prefix('booking')->name('booking.')->group(function () {
        Route::get('history', [BookingController::class, 'history'])->name('history');
        Route::get('tickets', [BookingController::class, 'tickets'])->name('tickets');
        Route::post('cancel/{booking}', [BookingController::class, 'cancel'])->name('cancel');
    });
});

require __DIR__.'/auth.php';
