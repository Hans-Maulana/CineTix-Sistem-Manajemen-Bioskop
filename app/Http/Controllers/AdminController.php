<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Booking;
use App\Models\User;
use App\Models\Payment;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil data statistik dasar
        $totalFilms = Film::count();
        $totalBookings = Booking::where('status', 'confirmed')->count();
        $totalCustomers = User::whereHas('role', function($query) {
            $query->where('name', 'customer');
        })->count();

        // Extra stats
        $totalRevenue = Payment::where('status', 'success')->sum('amount');
        $totalTicketsSold = DB::table('ticket_bookings')
            ->join('bookings', 'ticket_bookings.booking_id', '=', 'bookings.id')
            ->where('bookings.status', 'confirmed')
            ->count();
        $totalActiveSchedules = Schedule::where('schedule_date', '>=', now()->toDateString())->count();

        // 2. Ambil daftar film terbaru (4 film terakhir)
        $films = Film::with('genres')->latest()->take(4)->get();

        // 3. Data untuk Chart dengan filter
        $filter = $request->get('filter', 'weekly');
        $salesQuery = Payment::where('status', 'success');
        
        if ($filter === 'yearly') {
            $salesQuery->where('created_at', '>=', now()->subMonths(12));
            $salesData = $salesQuery->get()
                ->groupBy(function($item) {
                    return $item->created_at->format('Y-m');
                })
                ->map(function($group) {
                    return $group->sum('amount');
                })
                ->sortKeys();
            
            $chartLabels = $salesData->keys()->map(function($key) {
                return date('M Y', strtotime($key . '-01'));
            })->toArray();
            $chartTotals = $salesData->values()->toArray();
        } elseif ($filter === 'monthly') {
            $salesQuery->where('created_at', '>=', now()->subDays(30));
            
            // Build last 30 days to display 0 on empty days
            $days = [];
            for ($d = 29; $d >= 0; $d--) {
                $days[now()->subDays($d)->format('Y-m-d')] = 0;
            }
            
            $groupedSales = $salesQuery->get()
                ->groupBy(function($item) {
                    return $item->created_at->format('Y-m-d');
                })
                ->map(function($group) {
                    return $group->sum('amount');
                })
                ->toArray();
                
            $salesData = array_merge($days, $groupedSales);
            ksort($salesData);
            
            $chartLabels = array_map(function($key) {
                return date('d M', strtotime($key));
            }, array_keys($salesData));
            $chartTotals = array_values($salesData);
        } else {
            // Default: weekly
            $salesQuery->where('created_at', '>=', now()->subDays(7));
            
            // Build last 7 days to display 0 on empty days
            $days = [];
            for ($d = 6; $d >= 0; $d--) {
                $days[now()->subDays($d)->format('Y-m-d')] = 0;
            }
            
            $groupedSales = $salesQuery->get()
                ->groupBy(function($item) {
                    return $item->created_at->format('Y-m-d');
                })
                ->map(function($group) {
                    return $group->sum('amount');
                })
                ->toArray();
                
            $salesData = array_merge($days, $groupedSales);
            ksort($salesData);
            
            $chartLabels = array_map(function($key) {
                return date('d M', strtotime($key));
            }, array_keys($salesData));
            $chartTotals = array_values($salesData);
        }

        // 4. Booking terbaru yang butuh konfirmasi (Status: pending)
        $recentBookings = Booking::with(['user', 'payments'])
            ->whereHas('payments', function($q) {
                $q->where('status', 'pending');
            })
            ->latest()
            ->take(5)
            ->get();

        // 5. Tren Film Terpopuler (Top 5)
        $topFilms = Film::select('films.id', 'films.title', 'films.cover')
            ->withCount(['schedules as tickets_sold' => function ($query) {
                $query->join('ticket_bookings', 'schedules.id', '=', 'ticket_bookings.schedule_id')
                      ->join('bookings', 'ticket_bookings.booking_id', '=', 'bookings.id')
                      ->where('bookings.status', 'confirmed');
            }])
            ->withSum(['schedules as total_revenue' => function ($query) {
                $query->join('ticket_bookings', 'schedules.id', '=', 'ticket_bookings.schedule_id')
                      ->join('bookings', 'ticket_bookings.booking_id', '=', 'bookings.id')
                      ->where('bookings.status', 'confirmed');
            }], 'ticket_bookings.price_at_sale')
            ->orderBy('tickets_sold', 'desc')
            ->take(5)
            ->get();

        // 6. Distribusi Metode Pembayaran
        $paymentMethods = Payment::select('method', DB::raw('count(*) as count'))
            ->where('status', 'success')
            ->groupBy('method')
            ->get();

        $paymentLabels = $paymentMethods->pluck('method')->map(function($m) {
            return strtoupper(str_replace('_', ' ', $m));
        })->toArray();
        $paymentCounts = $paymentMethods->pluck('count')->toArray();

        return view('admin.dashboard', compact(
            'totalFilms', 
            'totalBookings', 
            'totalCustomers', 
            'totalRevenue',
            'totalTicketsSold',
            'totalActiveSchedules',
            'films', 
            'chartLabels', 
            'chartTotals',
            'recentBookings',
            'filter',
            'topFilms',
            'paymentLabels',
            'paymentCounts'
        ));
    }
}
