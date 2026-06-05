<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Film;
use App\Models\Studio;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    private const VALID_STATUSES = ['on schedule', 'now playing', 'complete', 'canceled'];

    public function index(Request $request)
    {
        Schedule::autoUpdateStatuses();

        $query = Schedule::with(['film', 'studio.type'])
            ->withCount([
                'ticketBookings as ticket_bookings_count',
                'ticketBookings as tickets_sold' => function ($q) {
                    $q->whereHas('booking', fn ($b) => $b->where('status', 'confirmed'));
                },
                'ticketBookings as tickets_redeemed' => function ($q) {
                    $q->whereHas('booking', function ($b) {
                        $b->where('status', 'confirmed')
                          ->where('status_redeem', 'redeemed');
                    });
                },
            ])
            ->withSum([
                'ticketBookings as revenue' => function ($q) {
                    $q->whereHas('booking', fn ($b) => $b->where('status', 'confirmed'));
                },
            ], 'price_at_sale');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('film', fn ($f) => $f->where('title', 'like', "%{$search}%"))
                  ->orWhereHas('studio', fn ($s) => $s->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('film_id')) {
            $query->where('film_id', $request->film_id);
        }

        if ($request->filled('studio_id')) {
            $query->where('studio_id', $request->studio_id);
        }

        if ($request->filled('status') && in_array($request->status, self::VALID_STATUSES, true)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('range')) {
            $today = Carbon::today();
            switch ($request->range) {
                case 'today':
                    $query->whereDate('schedule_date', $today);
                    break;
                case 'upcoming':
                    $query->whereDate('schedule_date', '>', $today);
                    break;
                case 'past':
                    $query->whereDate('schedule_date', '<', $today);
                    break;
                case 'this_week':
                    $query->whereBetween('schedule_date', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()]);
                    break;
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('schedule_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('schedule_date', '<=', $request->date_to);
        }

        switch ($request->get('sort')) {
            case 'date_desc':
                $query->orderByDesc('schedule_date')->orderByDesc('start_time');
                break;
            case 'price_high':
                $query->orderByDesc('ticket_price');
                break;
            case 'price_low':
                $query->orderBy('ticket_price');
                break;
            case 'date_asc':
            default:
                $query->orderBy('schedule_date')->orderBy('start_time');
                break;
        }

        $schedules = $query->paginate(15)->withQueryString();

        $today = Carbon::today();
        $realizedRevenue = \App\Models\TicketBooking::query()
            ->whereHas('booking', fn ($b) => $b->where('status', 'confirmed'))
            ->sum('price_at_sale');

        $stats = [
            'total'    => Schedule::count(),
            'today'    => Schedule::whereDate('schedule_date', $today)->count(),
            'upcoming' => Schedule::whereDate('schedule_date', '>', $today)->count(),
            'revenue'  => $realizedRevenue,
        ];

        $films = Film::orderBy('title')->get(['id', 'title']);
        $studios = Studio::orderBy('name')->get(['id', 'name']);

        return view('admin.schedules.index', compact('schedules', 'stats', 'films', 'studios'));
    }

    public function create()
    {
        $films = Film::orderBy('title')->get();
        $studios = Studio::with('type')->orderBy('name')->get();
        return view('admin.schedules.create', compact('films', 'studios'));
    }

    public function store(Request $request)
    {
        $data = $this->validateScheduleRequest($request);
        [$startFormatted, $endFormatted] = $this->resolveTimes($data);

        if ($this->hasConflict($data['studio_id'], $data['schedule_date'], $startFormatted, $endFormatted)) {
            return back()
                ->withInput()
                ->with('error', 'Gagal! Jadwal bentrok dengan film lain di studio ini.');
        }

        Schedule::create([
            'film_id'       => $data['film_id'],
            'studio_id'     => $data['studio_id'],
            'schedule_date' => $data['schedule_date'],
            'start_time'    => $startFormatted,
            'end_time'      => $endFormatted,
            'ticket_price'  => $data['ticket_price'],
            'status'        => $data['status'] ?? 'on schedule',
        ]);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function edit(Schedule $schedule)
    {
        $films = Film::orderBy('title')->get();
        $studios = Studio::with('type')->orderBy('name')->get();
        return view('admin.schedules.edit', compact('schedule', 'films', 'studios'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $data = $this->validateScheduleRequest($request);
        [$startFormatted, $endFormatted] = $this->resolveTimes($data);

        if ($this->hasConflict($data['studio_id'], $data['schedule_date'], $startFormatted, $endFormatted, $schedule->id)) {
            return back()
                ->withInput()
                ->with('error', 'Gagal! Jadwal bentrok dengan film lain di studio ini.');
        }

        $schedule->update([
            'film_id'       => $data['film_id'],
            'studio_id'     => $data['studio_id'],
            'schedule_date' => $data['schedule_date'],
            'start_time'    => $startFormatted,
            'end_time'      => $endFormatted,
            'ticket_price'  => $data['ticket_price'],
            'status'        => $data['status'] ?? $schedule->status,
        ]);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil diperbarui!');
    }

    public function destroy(Schedule $schedule)
    {
        if ($schedule->status !== 'on schedule') {
            return back()->with('error', 'Jadwal yang sedang/sudah berjalan, selesai, atau dibatalkan tidak dapat dihapus.');
        }

        if ($schedule->ticketBookings()->exists()) {
            return back()->with('error', 'Jadwal tidak dapat dihapus karena sudah memiliki transaksi tiket. Batalkan jadwal terlebih dahulu jika perlu.');
        }

        $schedule->delete();
        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil dihapus!');
    }

    private function validateScheduleRequest(Request $request): array
    {
        return $request->validate([
            'film_id'       => 'required|exists:films,id',
            'studio_id'     => 'required|exists:studios,id',
            'schedule_date' => 'required|date',
            'start_time'    => 'required',
            'ticket_price'  => 'required|numeric|min:0',
            'status'        => 'nullable|in:' . implode(',', self::VALID_STATUSES),
        ]);
    }

    /**
     * Tentukan start_time & end_time (end_time = start + duration film).
     */
    private function resolveTimes(array $data): array
    {
        $film = Film::findOrFail($data['film_id']);
        $startTime = Carbon::parse($data['start_time']);
        $endTime = (clone $startTime)->addMinutes((int) $film->duration);

        return [$startTime->format('H:i:s'), $endTime->format('H:i:s')];
    }

    /**
     * Cek apakah ada jadwal bentrok di studio yang sama pada tanggal yang sama.
     */
    private function hasConflict(int $studioId, string $date, string $startFormatted, string $endFormatted, ?int $excludeId = null): bool
    {
        $query = Schedule::where('studio_id', $studioId)
            ->whereDate('schedule_date', $date)
            ->where(function ($query) use ($startFormatted, $endFormatted) {
                $query->whereTime('start_time', '<', $endFormatted)
                      ->whereTime('end_time', '>', $startFormatted);
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
