<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\FilmController as AdminFilmController;
use App\Http\Controllers\Admin\StudioController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\BookingManagementController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\TicketManagementController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\FilmController;
use Illuminate\Support\Facades\Route;

// --- Route publik (guest & member) ---
Route::get('/', [HomeController::class, 'index'])->name('landing-page');
Route::get('/search', [HomeController::class, 'search'])->name('films.search');
Route::get('/films/filter-now-playing', [FilmController::class, 'filterNowPlaying'])->name('films.filter');
Route::get('/films/{film}', [HomeController::class, 'filmDetail'])->name('films.detail');
Route::get('/about', fn () => view('about'))->name('about');
Route::get('/faq', fn () => view('faq'))->name('faq');
Route::get('/promos', [PromoController::class, 'myPromos'])->name('customer.promos');

// Booking & pembayaran (guest & user login)
Route::get('/booking/schedule/{schedule}', [BookingController::class, 'show'])->name('booking.show');
Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/payment/{booking}', [BookingController::class, 'payment'])->name('booking.payment');
Route::post('/booking/initiate-payment/{booking}', [BookingController::class, 'initiatePayment'])->name('booking.initiate-payment');
Route::get('/booking/process-payment/{booking}/{payment}', [BookingController::class, 'processPayment'])->name('booking.process-payment');
Route::post('/booking/confirm-payment/{booking}/{payment}', [BookingController::class, 'confirmPayment'])->name('booking.confirm-payment');
Route::get('/booking/guest-ticket/{booking}', [BookingController::class, 'guestTicket'])->name('booking.guest-ticket');

// Booking AJAX (guest & user)
Route::get('/booking/available-seats/{schedule}', [BookingController::class, 'getAvailableSeats'])->name('booking.available-seats');
Route::get('/booking/details/{booking}', [BookingController::class, 'getBookingDetails'])->name('booking.details');
Route::get('/booking/check-payment/{payment}', [BookingController::class, 'checkPaymentStatus'])->name('booking.check-payment');

// Google Auth
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('login.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// --- Route privat (wajib login) ---
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user?->role?->name === 'admin') {
            return redirect('/admin');
        }
        if ($user?->role?->name === 'customer') {
            return redirect('/customer');
        }

        return redirect('/');
    })->name('dashboard');

    // Admin
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::resource('admin/films', AdminFilmController::class)->names('admin.films');
    Route::resource('admin/studios', StudioController::class)->names('admin.studios');
    Route::resource('admin/schedules', ScheduleController::class)->names('admin.schedules');
    Route::resource('admin/promos', PromoController::class)->names('admin.promos');
    Route::get('admin/bookings', [BookingManagementController::class, 'index'])->name('admin.bookings.index');
    Route::get('admin/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::get('admin/tickets', [TicketManagementController::class, 'index'])->name('admin.tickets.index');
    Route::post('admin/tickets/scan', [TicketManagementController::class, 'scan'])->name('admin.tickets.scan');
    Route::get('admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('admin/reports/export', [ReportController::class, 'export'])->name('admin.reports.export');

    Route::get('/customer', [HomeController::class, 'index'])->name('customer.dashboard');

    Route::post('/promo/validate', [PromoController::class, 'validate'])->name('promo.validate');

    Route::post('/seat/select', [BookingController::class, 'selectSeat'])->name('seat.select');
    Route::get('/payment/{seat_id}', [BookingController::class, 'showPayment'])->name('payment.page');
    Route::post('/payment/confirm', [BookingController::class, 'confirmPayment'])->name('payment.confirm');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Booking — hanya member terdaftar (pilih kursi & bayar ada di route publik di atas)
    Route::prefix('booking')->name('booking.')->group(function () {
        Route::get('confirmation/{booking}', [BookingController::class, 'confirmation'])->name('confirmation');
        Route::get('history', [BookingController::class, 'history'])->name('history');
        Route::get('tickets', [BookingController::class, 'tickets'])->name('tickets');
        Route::post('cancel/{booking}', [BookingController::class, 'cancel'])->name('cancel');
        Route::post('store-review', [BookingController::class, 'storeReview'])->name('store-review');
    });
});

require __DIR__.'/auth.php';
