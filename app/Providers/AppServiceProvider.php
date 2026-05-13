<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Seat;
use App\Observers\SeatObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers
        Seat::observe(SeatObserver::class);
    }
}
