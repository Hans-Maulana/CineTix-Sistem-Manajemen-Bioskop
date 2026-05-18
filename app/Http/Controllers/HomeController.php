<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Schedule;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Landing page untuk customer login dan belum login
     */
    public function index()
    {
        $nowPlayingFilms = Film::where('status', 'now_playing')
            ->with(['genres', 'reviews'])
            ->latest()
            ->take(8)
            ->get();

        $comingSoonFilms = Film::where('status', 'coming_soon')
            ->with(['genres'])
            ->latest()
            ->take(4)
            ->get();

        $upcomingSchedules = Schedule::where('schedule_date', '>=', now())
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
        
        $films = Film::where('status', 'active')
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('synopsis', 'like', "%{$query}%")
                  ->orWhereHas('genres', function ($subQuery) use ($query) {
                      $subQuery->where('genre_name', 'like', "%{$query}%");
                  });
            })
            ->with('genres', 'schedules')
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
            $q->where('schedule_date', '>=', now())
              ->orderBy('schedule_date')
              ->with('studio');
        }]);

        $avgRating = $film->reviews()->avg('rating') ?? 0;

        return view('films.detail', compact('film', 'avgRating'));
    }
}
