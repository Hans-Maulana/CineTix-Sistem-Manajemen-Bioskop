<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingManagementController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user', 'payments', 'ticketBookings.schedule.film', 'ticketBookings.schedule.studio', 'ticketBookings.seat'])
            ->latest()
            ->paginate(15);
        return view('admin.bookings.index', compact('bookings'));
    }
}
