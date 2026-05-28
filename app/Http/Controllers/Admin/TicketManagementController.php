<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Film;
use Illuminate\Http\Request;

class TicketManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'ticketBookings.schedule.film', 'ticketBookings.schedule.studio', 'ticketBookings.seat', 'payments'])
            ->where('status', 'confirmed'); // Only confirmed bookings have valid tickets

        if ($request->filled('film_id')) {
            $filmId = $request->film_id;
            $query->whereHas('ticketBookings.schedule.film', function ($q) use ($filmId) {
                $q->where('id', $filmId);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('qr_redeem', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $query->latest()->paginate(15);
        $films = Film::orderBy('title')->get();

        return view('admin.tickets.index', compact('bookings', 'films'));
    }

    public function scan(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $booking = Booking::with(['user', 'ticketBookings.schedule.film', 'ticketBookings.schedule.studio', 'ticketBookings.seat'])
            ->where('qr_redeem', $request->qr_code)
            ->first();

        if (!$booking) {
            return redirect()->route('admin.tickets.index')->with('error', 'Kode QR Tiket tidak ditemukan!');
        }

        if ($booking->status !== 'confirmed') {
            return redirect()->route('admin.tickets.index')->with('error', 'Status pemesanan tiket ini belum dikonfirmasi atau dibatalkan.');
        }

        if ($booking->status_redeem === 'redeemed') {
            return redirect()->route('admin.tickets.index')->with('warning', 'Tiket ini sudah pernah di-scan (digunakan) sebelumnya pada ' . $booking->updated_at->format('d M Y H:i'));
        }

        $booking->status_redeem = 'redeemed';
        $booking->save();

        $filmTitle = $booking->ticketBookings->first()?->schedule->film->title ?? 'Film';
        $customer = $booking->customerName();

        return redirect()->route('admin.tickets.index')->with('success', "Berhasil! Tiket untuk {$filmTitle} (Customer: {$customer}) telah di-scan dan dinyatakan valid.");
    }
}
