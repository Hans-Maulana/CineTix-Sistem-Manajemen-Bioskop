<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Seat;
use App\Models\Payment;
use App\Observers\SeatObserver;
use App\Observers\PaymentObserver;
use Illuminate\Pagination\Paginator;

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
        // Use Bootstrap 5 for pagination
        Paginator::useBootstrapFive();

        // Register observers
        Seat::observe(SeatObserver::class);
        Payment::observe(PaymentObserver::class);

        // Auto update schedule statuses dynamically
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('schedules')) {
                \App\Models\Schedule::autoUpdateStatuses();
            }
        } catch (\Exception $e) {
            // Ignore database connection/migration errors during booting
        }
    }
}
