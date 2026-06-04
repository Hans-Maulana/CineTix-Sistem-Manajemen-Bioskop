<?php

namespace App\Http\Controllers;

use App\Models\Film;
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
        $startOfWeek = Carbon::today();
        $endOfWeek = Carbon::now()->endOfWeek();

        $nowPlayingFilms = Film::where('status', 'now_playing')
            ->whereHas('schedules', function ($query) use ($startOfWeek, $endOfWeek) {
                $query->whereBetween('schedule_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()]);
            })
            ->with(['genres', 'reviews'])
            ->latest()
            ->take(8)
            ->get();

        $comingSoonFilms = Film::where('status', 'coming_soon')
            ->whereHas('schedules', function ($query) use ($startOfWeek, $endOfWeek) {
                $query->whereBetween('schedule_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()]);
            })
            ->with(['genres'])
            ->latest()
            ->take(4)
            ->get();

        $upcomingSchedules = Schedule::whereBetween('schedule_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->with('film', 'studio')
            ->orderBy('schedule_date')
            ->take(10)
            ->get();

        return view('landing-page', compact('nowPlayingFilms', 'comingSoonFilms', 'upcomingSchedules'));
    }

    /**
     * Search films berdasarkan title atau genre
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $startOfWeek = Carbon::today();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $films = Film::whereIn('status', ['now_playing', 'coming_soon'])
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('synopsis', 'like', "%{$query}%")
                  ->orWhereHas('genres', function ($subQuery) use ($query) {
                      $subQuery->where('genre_name', 'like', "%{$query}%");
                  });
            })
            ->with(['genres', 'schedules' => function ($q) use ($startOfWeek, $endOfWeek) {
                $q->whereBetween('schedule_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
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
        $startOfWeek = Carbon::today();
        $endOfWeek = Carbon::now()->endOfWeek();

        $film->load(['genres', 'reviews' => function ($q) {
            $q->orderBy('created_at', 'desc')->take(5);
        }, 'schedules' => function ($q) use ($startOfWeek, $endOfWeek) {
            $q->whereBetween('schedule_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
              ->orderBy('schedule_date')
              ->with('studio');
        }]);

        $avgRating = $film->reviews()->avg('rating') ?? 0;

        return view('films.detail', compact('film', 'avgRating'));
    }
}
