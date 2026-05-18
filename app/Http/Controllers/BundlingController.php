<?php

namespace App\Http\Controllers;

use App\Models\Bundling;
use Illuminate\Http\Request;

class BundlingController extends Controller
{
    public function index()
    {
        $bundles = Bundling::with('bundlingBookings')->paginate(10);
        return view('bundles.index', compact('bundles'));
    }

    public function create()
    {
        return view('bundles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|decimal:0,2|min:0',
        ]);

        Bundling::create($validated);

        return redirect()->route('bundles.index')->with('success', 'Bundle berhasil ditambahkan');
    }

    public function show(Bundling $bundling)
    {
        $bundling->load('bundlingBookings');
        return view('bundles.show', compact('bundling'));
    }

    public function edit(Bundling $bundling)
    {
        return view('bundles.edit', compact('bundling'));
    }

    public function update(Request $request, Bundling $bundling)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|decimal:0,2|min:0',
        ]);

        $bundling->update($validated);

        return redirect()->route('bundles.show', $bundling)->with('success', 'Bundle berhasil diperbarui');
    }

    public function destroy(Bundling $bundling)
    {
        $bundling->delete();
        return redirect()->route('bundles.index')->with('success', 'Bundle berhasil dihapus');
    }
}
