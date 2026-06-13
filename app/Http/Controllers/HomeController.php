<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Genre;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Landing page untuk customer login dan belum login
     */
    public function index()
    {
        $nowPlayingBaseQuery = Film::where('status', 'now_playing')
            ->whereHas('schedules', fn ($query) => $query->upcomingForBooking());

        $nowPlayingTotal = (clone $nowPlayingBaseQuery)->count();

        $today = \Carbon\Carbon::today()->toDateString();

        $nowPlayingFilms = (clone $nowPlayingBaseQuery)
            ->with([
                'genres',
                'reviews',
                // Jadwal hari ini (untuk tampilkan jam di film card)
                'todaySchedules' => fn ($q) => $q
                    ->where('schedule_date', $today)
                    ->where('status', 'on schedule')
                    ->orderBy('start_time'),
            ])
            ->latest()
            ->take(12)
            ->get();

        $comingSoonTotal = Film::where('status', 'coming_soon')->count();

        $comingSoonFilms = Film::where('status', 'coming_soon')
            ->with(['genres', 'reviews'])
            ->latest()
            ->take(8)
            ->get();

        $upcomingSchedules = Schedule::upcomingForBooking()
            ->with('film', 'studio')
            ->orderBy('schedule_date')
            ->take(10)
            ->get();

        $topFilms = $this->getTopFilms(5);

        $filterGenres = Genre::orderBy('genre_name')->pluck('genre_name');
        $filterClassifications = Film::classificationOptions();

        return view('landing-page', compact(
            'nowPlayingFilms',
            'nowPlayingTotal',
            'comingSoonFilms',
            'comingSoonTotal',
            'upcomingSchedules',
            'topFilms',
            'filterGenres',
            'filterClassifications'
        ));
    }

    /**
     * Top 5 film berdasarkan tiket terjual minggu ini (fallback: film sedang tayang terbaru).
     */
    private function getTopFilms(int $limit = 5)
    {
        $weekStart = Carbon::now()->startOfWeek();

        $weeklyTicketScope = function ($query) use ($weekStart) {
            $query->join('ticket_bookings', 'schedules.id', '=', 'ticket_bookings.schedule_id')
                ->join('bookings', 'ticket_bookings.booking_id', '=', 'bookings.id')
                ->where('bookings.status', 'confirmed')
                ->where('bookings.created_at', '>=', $weekStart);
        };

        $topFilms = Film::query()
            ->select('films.*')
            ->with(['genres', 'reviews'])
            ->withCount(['schedules as tickets_sold' => $weeklyTicketScope])
            ->where('status', 'now_playing')
            ->whereHas('schedules', fn ($query) => $query->upcomingForBooking())
            ->orderByDesc('tickets_sold')
            ->take($limit)
            ->get();

        if ($topFilms->isEmpty() || $topFilms->every(fn ($film) => (int) $film->tickets_sold === 0)) {
            return Film::where('status', 'now_playing')
                ->whereHas('schedules', fn ($query) => $query->upcomingForBooking())
                ->with(['genres', 'reviews'])
                ->latest()
                ->take($limit)
                ->get()
                ->each(fn ($film) => $film->tickets_sold = 0);
        }

        return $topFilms;
    }

    /**
     * Search films berdasarkan title atau genre
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        $films = Film::whereIn('status', ['now_playing', 'coming_soon'])
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('synopsis', 'like', "%{$query}%")
                  ->orWhereHas('genres', function ($subQuery) use ($query) {
                      $subQuery->where('genre_name', 'like', "%{$query}%");
                  });
            })
            ->with(['genres', 'schedules' => function ($q) {
                $q->upcomingForBooking()
                    ->orderBy('schedule_date')
                    ->with('studio');
            }])
            ->paginate(12);

        return view('films.search', compact('films', 'query'));
    }

    /**
     * Detail film dengan jadwal tersedia
     */
    public function filmDetail(Film $film)
    {
        $film->load(['genres', 'reviews' => function ($q) {
            $q->orderBy('created_at', 'desc')->take(5);
        }, 'schedules' => function ($q) {
            $q->upcomingForBooking()
                ->orderBy('schedule_date')
                ->with('studio');
        }]);

        $avgRating = $film->reviews()->avg('rating') ?? 0;

        return view('films.detail', compact('film', 'avgRating'));
    }
}
