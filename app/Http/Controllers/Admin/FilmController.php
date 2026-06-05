<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Film;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FilmController extends Controller
{
    public function index(Request $request)
    {
        $query = Film::with('genres')->latest();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('director', 'like', '%' . $search . '%')
                  ->orWhereHas('genres', function ($g) use ($search) {
                      $g->where('genre_name', 'like', '%' . $search . '%');
                  });
            });
        }

        $films = $query->paginate(10)->withQueryString();
        return view('admin.films.index', compact('films'));
    }

    public function create()
    {
        $genres = Genre::all();
        return view('admin.films.create', compact('genres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'synopsis' => 'required',
            'duration' => 'required|integer',
            'director' => 'required|string',
            'release_date' => 'required|date',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
            'genres' => 'required|array',
        ]);

        $data = $request->all();

        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/cover', $filename);
            $data['cover'] = $filename;
        }

        $data['rating'] = 0; // Default rating for new films

        $film = Film::create($data);
        $film->genres()->sync($request->genres);

        return redirect()->route('admin.films.index')->with('success', 'Film berhasil ditambahkan!');
    }

    public function edit(Film $film)
    {
        $genres = Genre::all();
        $selectedGenres = $film->genres->pluck('id')->toArray();
        return view('admin.films.edit', compact('film', 'genres', 'selectedGenres'));
    }

    public function update(Request $request, Film $film)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'synopsis' => 'required',
            'duration' => 'required|integer',
            'director' => 'required|string',
            'release_date' => 'required|date',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
            'genres' => 'required|array',
        ]);

        $data = $request->all();

        if ($request->hasFile('cover')) {
            // Hapus cover lama jika ada
            if ($film->cover && Storage::exists('public/cover/' . $film->cover)) {
                Storage::delete('public/cover/' . $film->cover);
            }

            $file = $request->file('cover');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/cover', $filename);
            $data['cover'] = $filename;
        }

        $film->update($data);
        $film->genres()->sync($request->genres);

        return redirect()->route('admin.films.index')->with('success', 'Film berhasil diperbarui!');
    }

    public function destroy(Film $film)
    {
        if ($film->cover && Storage::exists('public/cover/' . $film->cover)) {
            Storage::delete('public/cover/' . $film->cover);
        }

        $film->genres()->detach();
        $film->delete();

        return redirect()->route('admin.films.index')->with('success', 'Film berhasil dihapus!');
    }
}
