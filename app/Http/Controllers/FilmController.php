<?php

namespace App\Http\Controllers;

use App\Models\Film;
use Illuminate\Http\Request;

class FilmController extends Controller
{
    public function index()
    {
        $films = Film::with('genres', 'schedules')->paginate(10);
        return view('films.index', compact('films'));
    }

    public function create()
    {
        return view('films.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'required|integer|min:1',
            'rating' => 'required|decimal:0,1|between:0,10',
            'actors' => 'required|string',
            'director' => 'required|string',
            'production' => 'required|string',
            'status' => 'required|in:active,inactive',
            'classification' => 'required|string',
            'cover' => 'nullable|string',
        ]);

        Film::create($validated);

        return redirect()->route('films.index')->with('success', 'Film berhasil ditambahkan');
    }

    public function show(Film $film)
    {
        $film->load('genres', 'schedules', 'reviews');
        return view('films.show', compact('film'));
    }

    public function edit(Film $film)
    {
        return view('films.edit', compact('film'));
    }

    public function update(Request $request, Film $film)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'required|integer|min:1',
            'rating' => 'required|decimal:0,1|between:0,10',
            'actors' => 'required|string',
            'director' => 'required|string',
            'production' => 'required|string',
            'status' => 'required|in:active,inactive',
            'classification' => 'required|string',
            'cover' => 'nullable|string',
        ]);

        $film->update($validated);

        return redirect()->route('films.show', $film)->with('success', 'Film berhasil diperbarui');
    }

    public function destroy(Film $film)
    {
        $film->delete();
        return redirect()->route('films.index')->with('success', 'Film berhasil dihapus');
    }

    // SOLID - SRP (Single Responsibility): mengurus request filter AJAX & return data partial HTML
    public function filterNowPlaying(Request $request)
    {
        $today = \Carbon\Carbon::today()->toDateString();

        // Builder Pattern: Menyusun query filter dinamis menggunakan Scope dari Model
        $nowPlayingFilms = Film::with([
                'genres',
                'reviews',
                'todaySchedules' => fn ($q) => $q
                    ->where('schedule_date', $today)
                    ->where('status', 'on schedule')
                    ->orderBy('start_time'),
            ])
            ->where('status', 'now_playing')
            ->whereHas('schedules', fn ($query) => $query->upcomingForBooking())
            ->filterGenre($request->genre)
            ->filterClassification($request->classification)
            ->get();

        // Penerapan DRY: Menggunakan method render() pada partial view struktur kartu HTML tidak ditulis ulang
        return view('partials.film_items', compact('nowPlayingFilms'))->render();
    }
}
