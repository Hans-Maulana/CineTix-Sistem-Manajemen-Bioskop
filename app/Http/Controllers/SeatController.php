<?php

namespace App\Http\Controllers;

use App\Models\Seat;
use App\Models\Studio;
use Illuminate\Http\Request;

class SeatController extends Controller
{
    public function index()
    {
        $seats = Seat::with('studio')->paginate(15);
        return view('seats.index', compact('seats'));
    }

    public function create()
    {
        $studios = Studio::all();
        return view('seats.create', compact('studios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'studio_id' => 'required|exists:studios,id',
            'row_label' => 'required|string|max:1',
            'seat_number' => 'required|integer|min:1',
            'seat_code' => 'required|string|max:10|unique:seats,seat_code',
            'status' => 'required|in:available,occupied,maintenance',
        ]);

        Seat::create($validated);

        return redirect()->route('seats.index')->with('success', 'Kursi berhasil ditambahkan');
    }

    public function show(Seat $seat)
    {
        $seat->load('studio');
        return view('seats.show', compact('seat'));
    }

    public function edit(Seat $seat)
    {
        $studios = Studio::all();
        return view('seats.edit', compact('seat', 'studios'));
    }

    public function update(Request $request, Seat $seat)
    {
        $validated = $request->validate([
            'studio_id' => 'required|exists:studios,id',
            'row_label' => 'required|string|max:1',
            'seat_number' => 'required|integer|min:1',
            'seat_code' => 'required|string|max:10|unique:seats,seat_code,' . $seat->id,
            'status' => 'required|in:available,occupied,maintenance',
        ]);

        $seat->update($validated);

        return redirect()->route('seats.show', $seat)->with('success', 'Kursi berhasil diperbarui');
    }

    public function destroy(Seat $seat)
    {
        $seat->delete();
        return redirect()->route('seats.index')->with('success', 'Kursi berhasil dihapus');
    }
}
