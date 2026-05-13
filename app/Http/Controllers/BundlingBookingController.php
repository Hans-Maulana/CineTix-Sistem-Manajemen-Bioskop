<?php

namespace App\Http\Controllers;

use App\Models\BundlingBooking;
use App\Models\Booking;
use App\Models\Bundling;
use Illuminate\Http\Request;

class BundlingBookingController extends Controller
{
    public function index()
    {
        $bundlingBookings = BundlingBooking::with('booking', 'bundling')->paginate(10);
        return view('bundling-bookings.index', compact('bundlingBookings'));
    }

    public function create()
    {
        $bookings = Booking::all();
        $bundles = Bundling::all();
        return view('bundling-bookings.create', compact('bookings', 'bundles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'bundling_id' => 'required|exists:bundlings,id',
        ]);

        BundlingBooking::create($validated);

        return redirect()->route('bundling-bookings.index')->with('success', 'Bundle Booking berhasil ditambahkan');
    }

    public function show(BundlingBooking $bundlingBooking)
    {
        $bundlingBooking->load('booking', 'bundling');
        return view('bundling-bookings.show', compact('bundlingBooking'));
    }

    public function edit(BundlingBooking $bundlingBooking)
    {
        $bookings = Booking::all();
        $bundles = Bundling::all();
        return view('bundling-bookings.edit', compact('bundlingBooking', 'bookings', 'bundles'));
    }

    public function update(Request $request, BundlingBooking $bundlingBooking)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'bundling_id' => 'required|exists:bundlings,id',
        ]);

        $bundlingBooking->update($validated);

        return redirect()->route('bundling-bookings.show', $bundlingBooking)->with('success', 'Bundle Booking berhasil diperbarui');
    }

    public function destroy(BundlingBooking $bundlingBooking)
    {
        $bundlingBooking->delete();
        return redirect()->route('bundling-bookings.index')->with('success', 'Bundle Booking berhasil dihapus');
    }
}
