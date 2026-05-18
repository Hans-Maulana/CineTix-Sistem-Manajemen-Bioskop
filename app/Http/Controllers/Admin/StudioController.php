<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Studio;
use App\Models\Type;
use Illuminate\Http\Request;

class StudioController extends Controller
{
    public function index()
    {
        $studios = Studio::with('type')->get();
        return view('admin.studios.index', compact('studios'));
    }

    public function create()
    {
        $types = Type::all();
        return view('admin.studios.create', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type_id' => 'required|exists:types,id',
            'capacity' => 'required|integer|min:1',
        ]);

        Studio::create($request->all());

        return redirect()->route('admin.studios.index')->with('success', 'Studio berhasil ditambahkan!');
    }

    public function edit(Studio $studio)
    {
        $types = Type::all();
        return view('admin.studios.edit', compact('studio', 'types'));
    }

    public function update(Request $request, Studio $studio)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type_id' => 'required|exists:types,id',
            'capacity' => 'required|integer|min:1',
        ]);

        $studio->update($request->all());

        return redirect()->route('admin.studios.index')->with('success', 'Studio berhasil diperbarui!');
    }

    public function destroy(Studio $studio)
    {
        $studio->delete();
        return redirect()->route('admin.studios.index')->with('success', 'Studio berhasil dihapus!');
    }
}
