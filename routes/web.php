<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;

// Route Landing Page
Route::get('/', function () {
    return view('landing-page');
})->name('landing-page');

// Route Google Auth
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('login.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Route Dashboard  Middleware Auth
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return redirect('/admin');
    })->name('dashboard');

    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/resepsionis', function () {
        return view('resepsionis.dashboard');
    })->name('resepsionis.dashboard');

    Route::get('/customer', function () {
        return view('customer.dashboard');
    })->name('customer.dashboard'); 

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
