<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Booking;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        // 1. Ambil data statistik dasar
        $totalFilms = Film::count();
        $totalBookings = Booking::count();
        $totalCustomers = User::whereHas('role', function($query) {
            $query->where('name', 'customer');
        })->count();

        // 2. Ambil daftar film terbaru (4 film terakhir)
        $films = Film::with('genres')->latest()->take(4)->get();

        // 3. Data untuk Chart (Penjualan 7 hari terakhir)
        $salesData = Payment::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(amount) as total')
        )
        ->where('status', 'success')
        ->where('created_at', '>=', now()->subDays(7))
        ->groupBy('date')
        ->orderBy('date', 'ASC')
        ->get();

        $chartLabels = $salesData->pluck('date')->map(function($date) {
            return date('d M', strtotime($date));
        });
        $chartTotals = $salesData->pluck('total');

        // 4. Booking terbaru yang butuh konfirmasi (Status: pending)
        // Menggunakan relasi 'payments' (sesuai Booking.php)
        $recentBookings = Booking::with(['user', 'payments'])
            ->whereHas('payments', function($q) {
                $q->where('status', 'pending');
            })
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalFilms', 
            'totalBookings', 
            'totalCustomers', 
            'films', 
            'chartLabels', 
            'chartTotals',
            'recentBookings'
        ));
    }
}
