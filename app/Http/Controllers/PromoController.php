<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function index()
    {
        $promos = Promo::paginate(10);
        return view('promos.index', compact('promos'));
    }

    public function create()
    {
        return view('promos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:promos,code',
            'disc_amount' => 'required|decimal:0,2|min:0',
            'valid_until' => 'required|date|after:today',
        ]);

        Promo::create($validated);

        return redirect()->route('promos.index')->with('success', 'Promo berhasil ditambahkan');
    }

    public function show(Promo $promo)
    {
        return view('promos.show', compact('promo'));
    }

    public function edit(Promo $promo)
    {
        return view('promos.edit', compact('promo'));
    }

    public function update(Request $request, Promo $promo)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:promos,code,' . $promo->id,
            'disc_amount' => 'required|decimal:0,2|min:0',
            'valid_until' => 'required|date',
        ]);

        $promo->update($validated);

        return redirect()->route('promos.show', $promo)->with('success', 'Promo berhasil diperbarui');
    }

    public function destroy(Promo $promo)
    {
        $promo->delete();
        return redirect()->route('promos.index')->with('success', 'Promo berhasil dihapus');
    }
}
