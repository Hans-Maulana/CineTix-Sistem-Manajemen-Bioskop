<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\User;
use App\Models\Film;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with('user', 'film')->paginate(10);
        return view('reviews.index', compact('reviews'));
    }

    public function create()
    {
        $users = User::all();
        $films = Film::all();
        return view('reviews.create', compact('users', 'films'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'film_id' => 'required|exists:films,id',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Review::create($validated);

        return redirect()->route('reviews.index')->with('success', 'Review berhasil ditambahkan');
    }

    public function show(Review $review)
    {
        $review->load('user', 'film');
        return view('reviews.show', compact('review'));
    }

    public function edit(Review $review)
    {
        $users = User::all();
        $films = Film::all();
        return view('reviews.edit', compact('review', 'users', 'films'));
    }

    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'film_id' => 'required|exists:films,id',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update($validated);

        return redirect()->route('reviews.show', $review)->with('success', 'Review berhasil diperbarui');
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return redirect()->route('reviews.index')->with('success', 'Review berhasil dihapus');
    }
}
