<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function index()
    {
        $genres = Genre::with('films')->paginate(10);
        return view('genres.index', compact('genres'));
    }

    public function create()
    {
        return view('genres.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'genre_name' => 'required|string|max:255|unique:genres,genre_name',
        ]);

        Genre::create($validated);

        return redirect()->route('genres.index')->with('success', 'Genre berhasil ditambahkan');
    }

    public function show(Genre $genre)
    {
        $genre->load('films');
        return view('genres.show', compact('genre'));
    }

    public function edit(Genre $genre)
    {
        return view('genres.edit', compact('genre'));
    }

    public function update(Request $request, Genre $genre)
    {
        $validated = $request->validate([
            'genre_name' => 'required|string|max:255|unique:genres,genre_name,' . $genre->id,
        ]);

        $genre->update($validated);

        return redirect()->route('genres.show', $genre)->with('success', 'Genre berhasil diperbarui');
    }

    public function destroy(Genre $genre)
    {
        $genre->delete();
        return redirect()->route('genres.index')->with('success', 'Genre berhasil dihapus');
    }
}
