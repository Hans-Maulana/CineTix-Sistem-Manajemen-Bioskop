<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use App\Models\PromoUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromoController extends Controller
{
    private function ensureAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403, 'Akses ditolak. Hanya admin yang dapat mengelola promo.');
    }

    /**
     * Admin: List semua promo
     */
    public function index(Request $request)
    {
        $this->ensureAdmin();

        $now = now();
        $query = Promo::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->where('valid_from', '<=', $now)
                          ->where('valid_until', '>=', $now)
                          ->where(function ($q) {
                              $q->whereNull('max_usage')
                                ->orWhereColumn('usage_count', '<', 'max_usage');
                          });
                    break;
                case 'upcoming':
                    $query->where('valid_from', '>', $now);
                    break;
                case 'expired':
                    $query->where(function ($q) use ($now) {
                        $q->where('valid_until', '<', $now)
                          ->orWhere(function ($qq) {
                              $qq->whereNotNull('max_usage')
                                 ->whereColumn('usage_count', '>=', 'max_usage');
                          });
                    });
                    break;
            }
        }

        if ($request->filled('type') && in_array($request->type, ['percentage', 'fixed'])) {
            $query->where('discount_type', $request->type);
        }

        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest':       $query->orderBy('created_at', 'asc'); break;
            case 'most_used':    $query->orderBy('usage_count', 'desc'); break;
            case 'expires_soon': $query->orderBy('valid_until', 'asc'); break;
            case 'code_asc':     $query->orderBy('code', 'asc'); break;
            case 'newest':
            default:             $query->orderBy('created_at', 'desc');
        }

        $promos = $query->paginate(12)->withQueryString();

        $stats = [
            'total'       => Promo::count(),
            'active'      => Promo::where('valid_from', '<=', $now)
                                  ->where('valid_until', '>=', $now)
                                  ->count(),
            'expired'     => Promo::where('valid_until', '<', $now)->count(),
            'redemptions' => (int) Promo::sum('usage_count'),
        ];

        return view('admin.promos.index', compact('promos', 'stats'));
    }

    /**
     * Admin: Create promo form
     */
    public function create()
    {
        $this->ensureAdmin();
        return view('admin.promos.create');
    }

    /**
     * Admin: Store promo baru
     */
    public function store(Request $request)
    {
        $this->ensureAdmin();
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:promos,code',
            'description' => 'nullable|string|max:255',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'max_usage' => 'nullable|integer|min:1',
            'max_usage_per_customer' => 'required|integer|min:1',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        Promo::create($validated);

        return redirect()->route('admin.promos.index')->with('success', 'Promo berhasil ditambahkan');
    }

    /**
     * Admin: Show promo detail dengan usage info
     */
    public function show(Promo $promo)
    {
        $this->ensureAdmin();
        $promo->load('usages.user');
        return view('admin.promos.show', compact('promo'));
    }

    /**
     * Admin: Edit promo form
     */
    public function edit(Promo $promo)
    {
        $this->ensureAdmin();
        return view('admin.promos.edit', compact('promo'));
    }

    /**
     * Admin: Update promo
     */
    public function update(Request $request, Promo $promo)
    {
        $this->ensureAdmin();
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:promos,code,' . $promo->id,
            'description' => 'nullable|string|max:255',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'max_usage' => 'nullable|integer|min:1',
            'max_usage_per_customer' => 'required|integer|min:1',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $promo->update($validated);

        return redirect()->route('admin.promos.show', $promo)->with('success', 'Promo berhasil diperbarui');
    }

    /**
     * Admin: Delete promo
     */
    public function destroy(Promo $promo)
    {
        $this->ensureAdmin();
        // Delete related usages
        $promo->usages()->delete();
        $promo->delete();
        
        return redirect()->route('admin.promos.index')->with('success', 'Promo berhasil dihapus');
    }

    /**
     * Customer: daftar kode promo yang bisa dipakai
     */
    public function myPromos()
    {
        $userId = Auth::id();

        $promos = Promo::query()
            ->where('valid_until', '>=', now()->startOfDay())
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Promo $promo) => [
                'promo' => $promo,
                'status' => $promo->statusForUser($userId),
                'remaining' => $promo->remainingUsesFor($userId),
                'user_usage' => $userId ? $promo->getUserUsageCount($userId) : 0,
            ]);

        return view('customer.promos', [
            'promos' => $promos,
            'isAuthenticated' => Auth::check(),
        ]);
    }

    /**
     * AJAX: Ambil daftar promo yang tersedia & bisa digunakan user saat ini (untuk modal picker).
     */
    public function availableForBooking(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['promos' => []]);
        }

        $userId = Auth::id();
        $ticketPrice = (float) $request->input('ticket_price', 0);
        $seatCount   = (int) $request->input('seat_count', 1);
        $subtotal    = $ticketPrice * max(1, $seatCount);

        $promos = Promo::query()
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->where(function ($q) {
                $q->whereNull('max_usage')
                  ->orWhereColumn('usage_count', '<', 'max_usage');
            })
            ->orderByDesc('discount_value')
            ->get()
            ->filter(fn (Promo $promo) => $promo->canBeUsedBy($userId))
            ->map(fn (Promo $promo) => [
                'id'             => $promo->id,
                'code'           => $promo->code,
                'description'    => $promo->description,
                'discount_type'  => $promo->discount_type,
                'discount_value' => (float) $promo->discount_value,
                'discount_label' => $promo->discountLabel(),
                'valid_until'    => $promo->valid_until->translatedFormat('d M Y'),
                'savings'        => $subtotal > 0 ? $promo->calculateDiscount($subtotal) : 0,
                'remaining_uses' => $promo->remainingUsesFor($userId),
            ])
            ->values();

        return response()->json(['promos' => $promos]);
    }

    /**
     * AJAX: Check apakah promo code valid untuk authenticated user
     * Guest akan redirect ke login
     */
    public function validate(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        // Jika user tidak login (guest), tidak bisa pakai promo
        if (!Auth::check()) {
            return response()->json([
                'valid' => false,
                'message' => 'Silakan login untuk menggunakan kode promo. Dapatkan diskon Rp 20.000 dengan kode WELCOME2026!'
            ]);
        }

        $promo = Promo::where('code', strtoupper(trim($request->code)))->first();

        if (!$promo) {
            return response()->json([
                'valid' => false,
                'message' => 'Kode promo tidak ditemukan'
            ]);
        }

        // Check validity
        if (!$promo->isValid()) {
            return response()->json([
                'valid' => false,
                'message' => 'Kode promo tidak valid atau sudah expired'
            ]);
        }

        // Check apakah user sudah mencapai max usage
        if (!$promo->canBeUsedBy(Auth::id())) {
            return response()->json([
                'valid' => false,
                'message' => 'Anda sudah mencapai batas penggunaan kode promo ini'
            ]);
        }

        return response()->json([
            'valid' => true,
            'code' => $promo->code,
            'discount_type' => $promo->discount_type,
            'discount_value' => $promo->discount_value,
            'description' => $promo->description,
            'message' => 'Kode promo valid! ' . 
                ($promo->discount_type === 'percentage' 
                    ? 'Diskon ' . $promo->discount_value . '%' 
                    : 'Diskon Rp ' . number_format($promo->discount_value, 0, ',', '.'))
        ]);
    }
}
