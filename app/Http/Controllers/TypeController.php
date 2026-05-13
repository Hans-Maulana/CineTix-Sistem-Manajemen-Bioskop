<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    public function index()
    {
        $types = Type::with('studios')->paginate(10);
        return view('types.index', compact('types'));
    }

    public function create()
    {
        return view('types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:types,name',
        ]);

        Type::create($validated);

        return redirect()->route('types.index')->with('success', 'Tipe berhasil ditambahkan');
    }

    public function show(Type $type)
    {
        $type->load('studios');
        return view('types.show', compact('type'));
    }

    public function edit(Type $type)
    {
        return view('types.edit', compact('type'));
    }

    public function update(Request $request, Type $type)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:types,name,' . $type->id,
        ]);

        $type->update($validated);

        return redirect()->route('types.show', $type)->with('success', 'Tipe berhasil diperbarui');
    }

    public function destroy(Type $type)
    {
        $type->delete();
        return redirect()->route('types.index')->with('success', 'Tipe berhasil dihapus');
    }
}
