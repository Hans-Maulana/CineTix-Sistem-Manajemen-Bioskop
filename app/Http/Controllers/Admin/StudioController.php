<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seat;
use App\Models\Studio;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudioController extends Controller
{
    public function index()
    {
        $studios = Studio::with('type')
            ->withCount('seats')
            ->latest('id')
            ->get();

        $stats = [
            'total'    => $studios->count(),
            'active'   => $studios->where('status', 'active')->count(),
            'capacity' => $studios->sum('capacity'),
            'types'    => Type::count(),
        ];

        return view('admin.studios.index', compact('studios', 'stats'));
    }

    public function create()
    {
        $types = Type::all();
        return view('admin.studios.create', compact('types'));
    }

    public function store(Request $request)
    {
        $data = $this->validateStudioRequest($request);
        $layout = $this->parseLayout($request);
        $capacity = $this->countSeats($layout);

        if ($capacity < 1) {
            return back()
                ->withInput()
                ->withErrors(['seat_layout' => 'Layout harus memiliki minimal 1 kursi.']);
        }

        DB::transaction(function () use ($data, $layout, $capacity) {
            $studio = Studio::create([
                'type_id'     => $data['type_id'],
                'name'        => $data['name'],
                'capacity'    => $capacity,
                'seat_layout' => $layout,
                'status'      => $data['status'],
            ]);

            $this->syncSeatsFromLayout($studio);
        });

        return redirect()->route('admin.studios.index')
            ->with('success', 'Studio berhasil ditambahkan dengan ' . $capacity . ' kursi.');
    }

    public function edit(Studio $studio)
    {
        $types = Type::all();
        $hasBookings = $studio->seats()
            ->whereHas('ticketBookings')
            ->exists();

        return view('admin.studios.edit', compact('studio', 'types', 'hasBookings'));
    }

    public function update(Request $request, Studio $studio)
    {
        $data = $this->validateStudioRequest($request);
        $layout = $this->parseLayout($request);
        $capacity = $this->countSeats($layout);

        if ($capacity < 1) {
            return back()
                ->withInput()
                ->withErrors(['seat_layout' => 'Layout harus memiliki minimal 1 kursi.']);
        }

        $hasBookings = $studio->seats()
            ->whereHas('ticketBookings')
            ->exists();

        DB::transaction(function () use ($studio, $data, $layout, $capacity, $hasBookings) {
            $studio->update([
                'type_id'     => $data['type_id'],
                'name'        => $data['name'],
                'capacity'    => $capacity,
                'seat_layout' => $layout,
                'status'      => $data['status'],
            ]);

            if (!$hasBookings) {
                $this->syncSeatsFromLayout($studio);
            }
        });

        $message = $hasBookings
            ? 'Studio diperbarui. Layout disimpan, namun kursi tidak diregenerasi karena studio ini sudah memiliki transaksi tiket.'
            : 'Studio berhasil diperbarui dengan ' . $capacity . ' kursi.';

        return redirect()->route('admin.studios.index')->with('success', $message);
    }

    public function destroy(Studio $studio)
    {
        $studio->delete();
        return redirect()->route('admin.studios.index')->with('success', 'Studio berhasil dihapus!');
    }

    /**
     * Validasi field umum studio (selain layout).
     */
    private function validateStudioRequest(Request $request): array
    {
        return $request->validate([
            'name'        => 'required|string|max:255',
            'type_id'     => 'required|exists:types,id',
            'status'      => 'required|in:active,inactive',
            'seat_layout' => 'required|string',
        ]);
    }

    /**
     * Parse dan normalisasi seat_layout dari request (string JSON -> array of arrays of 0/1).
     */
    private function parseLayout(Request $request): array
    {
        $raw = json_decode($request->input('seat_layout'), true) ?: [];
        $layout = [];

        foreach ($raw as $row) {
            if (!is_array($row)) {
                continue;
            }
            $normalRow = [];
            foreach ($row as $cell) {
                $normalRow[] = (int) (((int) $cell) === 1 ? 1 : 0);
            }
            $layout[] = $normalRow;
        }

        return $layout;
    }

    /**
     * Hitung jumlah seat (cell bernilai 1) dalam layout.
     */
    private function countSeats(array $layout): int
    {
        $total = 0;
        foreach ($layout as $row) {
            foreach ($row as $cell) {
                if ($cell === 1) {
                    $total++;
                }
            }
        }
        return $total;
    }

    /**
     * Regenerasi seat berdasarkan seat_layout studio.
     * (Dipanggil hanya jika tidak ada booking terkait.)
     */
    private function syncSeatsFromLayout(Studio $studio): void
    {
        $studio->seats()->delete();

        if (!$studio->seat_layout) {
            return;
        }

        foreach ($studio->seat_layout as $rowIndex => $row) {
            $rowLabel = chr(65 + $rowIndex);
            $seatCounter = 1;

            foreach ($row as $cell) {
                if ((int) $cell === 1) {
                    Seat::create([
                        'studio_id'   => $studio->id,
                        'row_label'   => $rowLabel,
                        'seat_number' => $seatCounter,
                        'seat_code'   => $rowLabel . $seatCounter,
                        'status'      => 'available',
                    ]);
                    $seatCounter++;
                }
            }
        }
    }
}
