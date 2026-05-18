<?php

namespace App\Http\Controllers;

use App\Models\Studio;
use App\Models\Type;
use Illuminate\Http\Request;

class StudioController extends Controller
{
    public function index()
    {
        $studios = Studio::with('type', 'seats', 'schedules')->paginate(10);
        return view('studios.index', compact('studios'));
    }

    public function create()
    {
        $types = Type::all();
        return view('studios.create', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type_id' => 'required|exists:types,id',
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        Studio::create($validated);

        return redirect()->route('studios.index')->with('success', 'Studio berhasil ditambahkan');
    }

    public function show(Studio $studio)
    {
        $studio->load('type', 'seats', 'schedules');
        return view('studios.show', compact('studio'));
    }

    public function edit(Studio $studio)
    {
        $types = Type::all();
        return view('studios.edit', compact('studio', 'types'));
    }

    public function update(Request $request, Studio $studio)
    {
        $validated = $request->validate([
            'type_id' => 'required|exists:types,id',
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        $studio->update($validated);

        return redirect()->route('studios.show', $studio)->with('success', 'Studio berhasil diperbarui');
    }

    public function destroy(Studio $studio)
    {
        $studio->delete();
        return redirect()->route('studios.index')->with('success', 'Studio berhasil dihapus');
    }
}
