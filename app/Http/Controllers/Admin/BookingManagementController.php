<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;

class BookingManagementController extends Controller
{
    public function index(Request $request)
    {
        $allowedPaymentStatuses = ['success', 'pending', 'failed'];
        $allowedBookingStatuses = ['pending', 'confirmed', 'cancelled'];
        $allowedMethods = ['cash', 'transfer', 'ewallet', 'qris', 'virtual_account'];
        $allowedPerPage = [10, 15, 25, 50];

        $query = Booking::with([
            'user',
            'latestPayment',
            'ticketBookings.schedule.film',
            'ticketBookings.schedule.studio',
            'ticketBookings.seat',
        ]);

        // -- Pencarian (ID, email guest, nama/email member, judul film)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                  ->orWhere('guest_email', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('ticketBookings.schedule.film', function ($f) use ($search) {
                      $f->where('title', 'like', '%' . $search . '%');
                  });
            });
        }

        // -- Filter status pembayaran
        if ($request->filled('status') && in_array($request->status, $allowedPaymentStatuses, true)) {
            $status = $request->status;
            $query->whereHas('payments', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }

        // -- Filter status booking
        if ($request->filled('booking_status') && in_array($request->booking_status, $allowedBookingStatuses, true)) {
            $query->where('status', $request->booking_status);
        }

        // -- Filter metode pembayaran
        if ($request->filled('method') && in_array($request->method, $allowedMethods, true)) {
            $method = $request->method;
            $query->whereHas('payments', function ($q) use ($method) {
                $q->where('method', $method);
            });
        }

        // -- Filter tipe customer (member / guest)
        if ($request->filled('type')) {
            if ($request->type === 'guest') {
                $query->whereNull('user_id')->whereNotNull('guest_email');
            } elseif ($request->type === 'member') {
                $query->whereNotNull('user_id');
            }
        }

        // -- Filter rentang tanggal transaksi
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // -- Pengurutan
        switch ($request->get('sort')) {
            case 'updated_asc':
                $query->orderBy('updated_at');
                break;
            case 'updated_desc':
                $query->orderByDesc('updated_at');
                break;
            case 'amount_high':
                $query->orderByDesc('total_amount')->orderByDesc('updated_at');
                break;
            case 'amount_low':
                $query->orderBy('total_amount')->orderByDesc('updated_at');
                break;
            default:
                $query->orderByDesc('updated_at');
                break;
        }

        $perPage = $request->integer('per_page', 15);
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 15;
        }

        $bookings = $query->paginate($perPage)->withQueryString();

        // -- Ringkasan statistik (mengikuti filter aktif kecuali status, untuk konteks penuh)
        $stats = [
            'total'   => Booking::count(),
            'success' => Payment::where('status', 'success')->distinct('booking_id')->count('booking_id'),
            'pending' => Payment::where('status', 'pending')->distinct('booking_id')->count('booking_id'),
            'revenue' => Payment::where('status', 'success')->sum('amount'),
        ];

        $filterOptions = [
            'payment_statuses' => $allowedPaymentStatuses,
            'booking_statuses' => $allowedBookingStatuses,
            'payment_methods' => $allowedMethods,
            'per_page' => $allowedPerPage,
        ];

        return view('admin.bookings.index', compact('bookings', 'stats', 'filterOptions'));
    }
}
