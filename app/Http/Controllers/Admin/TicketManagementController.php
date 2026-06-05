<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Film;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TicketManagementController extends Controller
{
    public function index(Request $request)
    {
        Schedule::autoUpdateStatuses();

        // ----- Daftar booking (tiket aktif) -----
        $bookingsQuery = Booking::with(['user', 'ticketBookings.schedule.film', 'ticketBookings.schedule.studio', 'ticketBookings.seat', 'payments'])
            ->where('status', 'confirmed');

        if ($request->filled('film_id')) {
            $filmId = $request->film_id;
            $bookingsQuery->whereHas('ticketBookings.schedule.film', fn ($q) => $q->where('id', $filmId));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $bookingsQuery->where(function ($q) use ($search) {
                $q->where('qr_redeem', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $bookingsQuery->latest('updated_at')->paginate(10)->withQueryString();
        $films = Film::orderBy('title')->get();

        // ----- Daftar jadwal aktif untuk dipantau (mirip schedule page) -----
        $today = Carbon::today();
        $range = $request->get('schedule_range', 'today');

        $schedulesQuery = Schedule::with([
            'film',
            'studio.type',
            'ticketBookings' => function ($q) {
                $q->whereHas('booking', fn ($b) => $b->where('status', 'confirmed'))
                  ->with(['booking.user', 'seat']);
            },
        ])
            ->withCount([
                'ticketBookings as tickets_sold' => function ($q) {
                    $q->whereHas('booking', fn ($b) => $b->where('status', 'confirmed'));
                },
                'ticketBookings as tickets_redeemed' => function ($q) {
                    $q->whereHas('booking', function ($b) {
                        $b->where('status', 'confirmed')
                          ->where('status_redeem', 'redeemed');
                    });
                },
            ]);

        switch ($range) {
            case 'now':
                $schedulesQuery->where('status', 'now playing');
                break;
            case 'upcoming':
                $schedulesQuery->whereDate('schedule_date', '>=', $today)
                    ->whereIn('status', ['on schedule', 'now playing']);
                break;
            case 'all':
                // tanpa filter tambahan
                break;
            case 'today':
            default:
                $schedulesQuery->whereDate('schedule_date', $today);
                break;
        }

        if ($request->filled('schedule_search')) {
            $s = $request->schedule_search;
            $schedulesQuery->where(function ($q) use ($s) {
                $q->whereHas('film',   fn ($f) => $f->where('title', 'like', "%{$s}%"))
                  ->orWhereHas('studio', fn ($st) => $st->where('name', 'like', "%{$s}%"));
            });
        }

        $schedules = $schedulesQuery->orderBy('schedule_date')->orderBy('start_time')->limit(12)->get();

        // ----- Stats global -----
        $stats = [
            'total_active' => Booking::where('status', 'confirmed')->count(),
            'redeemed_today' => Booking::where('status', 'confirmed')
                ->where('status_redeem', 'redeemed')
                ->whereDate('updated_at', $today)
                ->count(),
            'unredeemed' => Booking::where('status', 'confirmed')
                ->where(function ($q) {
                    $q->whereNull('status_redeem')->orWhere('status_redeem', '!=', 'redeemed');
                })->count(),
            'today_total' => Booking::where('status', 'confirmed')
                ->whereHas('ticketBookings.schedule', fn ($q) => $q->whereDate('schedule_date', $today))
                ->count(),
        ];

        return view('admin.tickets.index', compact('bookings', 'films', 'schedules', 'stats', 'range'));
    }

    public function scheduleDetail(Request $request, Schedule $schedule)
    {
        Schedule::autoUpdateStatuses();

        $schedule->load([
            'film',
            'studio.type',
            'ticketBookings' => function ($q) {
                $q->whereHas('booking', fn ($b) => $b->where('status', 'confirmed'))
                  ->with(['booking.user', 'seat']);
            },
        ]);

        $attendeesAll = $schedule->ticketBookings
            ->filter(fn ($t) => optional($t->booking)->status === 'confirmed')
            ->groupBy('booking_id')
            ->map(function ($group) {
                $first = $group->first();
                $bk = $first->booking;
                $rawName = optional($bk->user)->name ?? $bk->guest_email ?? 'Tamu';
                $cleanName = preg_replace('/^\d+\s*-\s*/', '', $rawName);
                $isRedeemed = $bk->status_redeem === 'redeemed';
                return [
                    'booking_id'  => $bk->id,
                    'name'        => $cleanName,
                    'email'       => optional($bk->user)->email ?? $bk->guest_email,
                    'phone'       => optional($bk->user)->contact ?? null,
                    'is_guest'    => is_null($bk->user_id),
                    'seats'       => $group->map(fn ($t) => optional($t->seat)->seat_code)->filter()->values()->all(),
                    'seat_count'  => $group->count(),
                    'status'      => $isRedeemed ? 'redeemed' : 'pending',
                    'updated_at'  => $bk->updated_at,
                    'qr_code'     => $bk->qr_redeem,
                ];
            })
            ->values();

        // Sudah scan di atas (urut waktu scan terbaru), belum scan di bawah (urut nama).
        $attendees = $attendeesAll->where('status', 'redeemed')->sortByDesc('updated_at')->values()
            ->concat(
                $attendeesAll->where('status', 'pending')->sortBy('name')->values()
            )->values();

        $stats = [
            'total'     => $attendees->count(),
            'redeemed'  => $attendees->where('status', 'redeemed')->count(),
            'pending'   => $attendees->where('status', 'pending')->count(),
            'capacity'  => optional($schedule->studio)->capacity ?? 0,
        ];

        return view('admin.tickets.schedule', compact('schedule', 'attendees', 'stats'));
    }

    public function scan(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $expectsJson = $request->wantsJson() || $request->ajax();

        $booking = Booking::with(['user', 'ticketBookings.schedule.film', 'ticketBookings.schedule.studio', 'ticketBookings.seat'])
            ->where('qr_redeem', $request->qr_code)
            ->first();

        if (!$booking) {
            $msg = 'Kode QR tiket tidak ditemukan!';
            return $expectsJson
                ? response()->json(['status' => 'error', 'message' => $msg], 404)
                : redirect()->route('admin.tickets.index')->with('error', $msg);
        }

        if ($booking->status !== 'confirmed') {
            $msg = 'Status pemesanan tiket ini belum dikonfirmasi atau dibatalkan.';
            return $expectsJson
                ? response()->json(['status' => 'error', 'message' => $msg], 422)
                : redirect()->route('admin.tickets.index')->with('error', $msg);
        }

        $alreadyRedeemed = $booking->status_redeem === 'redeemed';

        if (!$alreadyRedeemed) {
            $booking->status_redeem = 'redeemed';
            $booking->save();
        }

        $schedule = $booking->ticketBookings->first()?->schedule;
        $payload = [
            'status'        => $alreadyRedeemed ? 'warning' : 'success',
            'message'       => $alreadyRedeemed
                ? 'Tiket sudah pernah di-scan sebelumnya.'
                : 'Tiket berhasil diverifikasi!',
            'customer'      => $booking->customerName(),
            'email'         => $booking->customerEmail(),
            'is_guest'      => $booking->isGuest(),
            'film_title'    => $schedule?->film?->title ?? '-',
            'studio'        => $schedule?->studio?->name ?? '-',
            'studio_type'   => $schedule?->studio?->type?->name ?? null,
            'date'          => $schedule ? $schedule->schedule_date->translatedFormat('l, d F Y') : '-',
            'time'          => $schedule ? $schedule->start_time->format('H:i') . ' - ' . $schedule->end_time->format('H:i') : '-',
            'seats'         => $booking->ticketBookings->map(fn ($t) => $t->seat?->seat_code)->filter()->values()->toArray(),
            'booking_id'    => $booking->id,
            'schedule_id'   => $schedule?->id,
            'qr_code'       => $booking->qr_redeem,
            'redeemed_at'   => $booking->updated_at?->translatedFormat('d M Y H:i'),
        ];

        if ($expectsJson) {
            return response()->json($payload, $alreadyRedeemed ? 409 : 200);
        }

        // Fallback non-AJAX: tetap redirect dengan session flash
        $msg = $alreadyRedeemed
            ? "Tiket {$payload['customer']} sudah pernah di-scan."
            : "Berhasil! Tiket {$payload['customer']} (Kursi: " . implode(', ', $payload['seats']) . ") telah diverifikasi.";
        return redirect()->route('admin.tickets.index')->with($alreadyRedeemed ? 'warning' : 'success', $msg);
    }
}
