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
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        $weekStart = now()->startOfWeek();

        // =========================================================
        // 1. STATISTIK UTAMA (FOKUS HARI INI)
        // =========================================================

        // -- Pendapatan hari ini vs kemarin
        $revenueToday = Payment::where('status', 'success')
            ->whereDate('created_at', $today)
            ->sum('amount');
        $revenueYesterday = Payment::where('status', 'success')
            ->whereDate('created_at', $yesterday)
            ->sum('amount');
        $revenueTrend = $this->percentChange($revenueToday, $revenueYesterday);

        // -- Tiket terjual hari ini vs kemarin (booking confirmed)
        $ticketsTodayQuery = fn ($date) => DB::table('ticket_bookings')
            ->join('bookings', 'ticket_bookings.booking_id', '=', 'bookings.id')
            ->where('bookings.status', 'confirmed')
            ->whereDate('bookings.created_at', $date)
            ->count();
        $ticketsToday = $ticketsTodayQuery($today);
        $ticketsYesterday = $ticketsTodayQuery($yesterday);
        $ticketsTrend = $this->percentChange($ticketsToday, $ticketsYesterday);

        // -- Transaksi (booking lunas) hari ini vs kemarin
        $transactionsToday = Booking::where('status', 'confirmed')
            ->whereDate('created_at', $today)->count();
        $transactionsYesterday = Booking::where('status', 'confirmed')
            ->whereDate('created_at', $yesterday)->count();
        $transactionsTrend = $this->percentChange($transactionsToday, $transactionsYesterday);

        // -- Booking pending yang butuh tindakan
        $pendingBookingsCount = Booking::whereHas('payments', function ($q) {
            $q->where('status', 'pending');
        })->count();

        // =========================================================
        // 2. STATISTIK SEKUNDER & WIDGET BARU
        // =========================================================

        // -- Tingkat okupansi kursi untuk penayangan hari ini
        $todayCapacity = (int) Schedule::whereDate('schedule_date', $today)
            ->join('studios', 'schedules.studio_id', '=', 'studios.id')
            ->sum('studios.capacity');
        $todayOccupied = DB::table('ticket_bookings')
            ->join('schedules', 'ticket_bookings.schedule_id', '=', 'schedules.id')
            ->join('bookings', 'ticket_bookings.booking_id', '=', 'bookings.id')
            ->whereIn('bookings.status', ['confirmed', 'pending'])
            ->whereDate('schedules.schedule_date', $today)
            ->count();
        $occupancyRate = $todayCapacity > 0 ? round(($todayOccupied / $todayCapacity) * 100) : 0;

        // -- Penayangan hari ini & member baru minggu ini
        $todaySchedulesCount = Schedule::whereDate('schedule_date', $today)->count();
        $newMembersWeek = User::whereHas('role', function ($q) {
            $q->where('name', 'customer');
        })->where('created_at', '>=', $weekStart)->count();

        // -- Statistik agregat (informasi tambahan)
        $totalFilms = Film::count();
        $totalCustomers = User::whereHas('role', function ($query) {
            $query->where('name', 'customer');
        })->count();
        $totalRevenue = Payment::where('status', 'success')->sum('amount');

        // =========================================================
        // 3. GRAFIK PENJUALAN (dengan filter)
        // =========================================================
        $filter = $request->get('filter', 'weekly');
        $salesQuery = Payment::where('status', 'success');

        if ($filter === 'yearly') {
            $salesQuery->where('created_at', '>=', now()->subMonths(12));
            $salesData = $salesQuery->get()
                ->groupBy(fn ($item) => $item->created_at->format('Y-m'))
                ->map(fn ($group) => $group->sum('amount'))
                ->sortKeys();

            $chartLabels = $salesData->keys()->map(fn ($key) => date('M Y', strtotime($key . '-01')))->toArray();
            $chartTotals = $salesData->values()->toArray();
        } elseif ($filter === 'monthly') {
            $salesQuery->where('created_at', '>=', now()->subDays(30));

            $days = [];
            for ($d = 29; $d >= 0; $d--) {
                $days[now()->subDays($d)->format('Y-m-d')] = 0;
            }

            $groupedSales = $salesQuery->get()
                ->groupBy(fn ($item) => $item->created_at->format('Y-m-d'))
                ->map(fn ($group) => $group->sum('amount'))
                ->toArray();

            $salesData = array_merge($days, $groupedSales);
            ksort($salesData);

            $chartLabels = array_map(fn ($key) => date('d M', strtotime($key)), array_keys($salesData));
            $chartTotals = array_values($salesData);
        } else {
            // Default: weekly
            $salesQuery->where('created_at', '>=', now()->subDays(7));

            $days = [];
            for ($d = 6; $d >= 0; $d--) {
                $days[now()->subDays($d)->format('Y-m-d')] = 0;
            }

            $groupedSales = $salesQuery->get()
                ->groupBy(fn ($item) => $item->created_at->format('Y-m-d'))
                ->map(fn ($group) => $group->sum('amount'))
                ->toArray();

            $salesData = array_merge($days, $groupedSales);
            ksort($salesData);

            $chartLabels = array_map(fn ($key) => date('d M', strtotime($key)), array_keys($salesData));
            $chartTotals = array_values($salesData);
        }

        // =========================================================
        // 4. PEMBAYARAN MASUK (transaksi sukses terbaru)
        // =========================================================
        $recentPayments = Payment::with(['booking.user'])
            ->where('status', 'success')
            ->latest('paid_at')
            ->latest()
            ->take(10)
            ->get();

        // =========================================================
        // 5. TOP 5 FILM MINGGU INI (tiket terjual minggu berjalan)
        // =========================================================
        $weeklyTicketScope = function ($query) use ($weekStart) {
            $query->join('ticket_bookings', 'schedules.id', '=', 'ticket_bookings.schedule_id')
                ->join('bookings', 'ticket_bookings.booking_id', '=', 'bookings.id')
                ->where('bookings.status', 'confirmed')
                ->where('bookings.created_at', '>=', $weekStart);
        };

        $topFilms = Film::select('films.id', 'films.title', 'films.cover')
            ->withCount(['schedules as tickets_sold' => $weeklyTicketScope])
            ->withSum(['schedules as total_revenue' => $weeklyTicketScope], 'ticket_bookings.price_at_sale')
            ->orderByDesc('tickets_sold')
            ->take(5)
            ->get();

        // =========================================================
        // 6. DISTRIBUSI METODE PEMBAYARAN
        // =========================================================
        $paymentMethods = Payment::select('method', DB::raw('count(*) as count'))
            ->where('status', 'success')
            ->groupBy('method')
            ->get();

        $paymentLabels = $paymentMethods->pluck('method')->map(function ($m) {
            return strtoupper(str_replace('_', ' ', $m));
        })->toArray();
        $paymentCounts = $paymentMethods->pluck('count')->toArray();

        return view('admin.dashboard', compact(
            'revenueToday',
            'revenueTrend',
            'ticketsToday',
            'ticketsTrend',
            'transactionsToday',
            'transactionsTrend',
            'pendingBookingsCount',
            'occupancyRate',
            'todayOccupied',
            'todayCapacity',
            'todaySchedulesCount',
            'newMembersWeek',
            'totalFilms',
            'totalCustomers',
            'totalRevenue',
            'chartLabels',
            'chartTotals',
            'recentPayments',
            'filter',
            'topFilms',
            'paymentLabels',
            'paymentCounts'
        ));
    }

    /**
     * Hitung persentase perubahan antara dua nilai.
     * Mengembalikan array berisi nilai persen & arah (up/down/flat).
     */
    private function percentChange($current, $previous): array
    {
        if ($previous == 0) {
            $percent = $current > 0 ? 100 : 0;
        } else {
            $percent = round((($current - $previous) / $previous) * 100);
        }

        $direction = 'flat';
        if ($current > $previous) {
            $direction = 'up';
        } elseif ($current < $previous) {
            $direction = 'down';
        }

        return ['percent' => abs($percent), 'direction' => $direction];
    }
}
