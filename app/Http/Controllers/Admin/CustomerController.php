<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereHas('role', fn ($q) => $q->where('name', 'customer'))
            ->withCount(['bookings as confirmed_bookings_count' => function ($q) {
                $q->where('status', 'confirmed');
            }])
            ->withSum(['bookings as total_spent' => function ($q) {
                $q->where('status', 'confirmed');
            }], 'total_amount');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('contact', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('activity')) {
            switch ($request->activity) {
                case 'active':
                    $query->having('confirmed_bookings_count', '>', 0);
                    break;
                case 'inactive':
                    $query->having('confirmed_bookings_count', '=', 0);
                    break;
            }
        }

        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest':       $query->orderBy('created_at', 'asc'); break;
            case 'name_asc':     $query->orderBy('name', 'asc'); break;
            case 'name_desc':    $query->orderBy('name', 'desc'); break;
            case 'most_active':  $query->orderBy('confirmed_bookings_count', 'desc'); break;
            case 'top_spender':  $query->orderBy('total_spent', 'desc'); break;
            case 'newest':
            default:             $query->orderBy('created_at', 'desc');
        }

        $customers = $query->paginate(15)->withQueryString();

        $now = now();
        $stats = [
            'total'        => User::whereHas('role', fn ($q) => $q->where('name', 'customer'))->count(),
            'new_month'    => User::whereHas('role', fn ($q) => $q->where('name', 'customer'))
                                  ->whereMonth('created_at', $now->month)
                                  ->whereYear('created_at', $now->year)->count(),
            'active'       => User::whereHas('role', fn ($q) => $q->where('name', 'customer'))
                                  ->whereHas('bookings', fn ($q) => $q->where('status', 'confirmed'))
                                  ->count(),
            'total_revenue' => (float) Booking::where('status', 'confirmed')
                                  ->whereNotNull('user_id')
                                  ->sum('total_amount'),
        ];

        return view('admin.customers.index', compact('customers', 'stats'));
    }
}
