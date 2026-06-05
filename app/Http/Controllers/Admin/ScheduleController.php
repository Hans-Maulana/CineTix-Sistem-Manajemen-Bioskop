<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Film;
use App\Models\Studio;
use Illuminate\Http\Request;
use Carbon\Carbon; 

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = Schedule::with(['film', 'studio'])->latest();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('film', function ($f) use ($search) {
                    $f->where('title', 'like', '%' . $search . '%');
                })
                ->orWhereHas('studio', function ($s) use ($search) {
                    $s->where('name', 'like', '%' . $search . '%');
                })
                ->orWhere('schedule_date', 'like', '%' . $search . '%');
            });
        }

        $schedules = $query->paginate(15)->withQueryString();
        return view('admin.schedules.index', compact('schedules'));
    }

    public function create()
    {
        $films = Film::all();
        $studios = Studio::all();
        return view('admin.schedules.create', compact('films', 'studios'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'film_id' => 'required|exists:films,id',
            'studio_id' => 'required|exists:studios,id',
            'schedule_date' => 'required|date',
            'start_time' => 'required',
            'ticket_price' => 'required|numeric',
        ]);

        // 2. Hitung end_time
        $film = Film::findOrFail($request->film_id);
        $startTime = Carbon::parse($request->start_time);
        $endTime = (clone $startTime)->addMinutes($film->duration);

        $startFormatted = $startTime->format('H:i:s');
        $endFormatted = $endTime->format('H:i:s');

        // 3. Cek Bentrok Jadwal
        $isConflict = Schedule::where('studio_id', $request->studio_id)
            ->whereDate('schedule_date', $request->schedule_date)
            ->where(function ($query) use ($startFormatted, $endFormatted) {
                $query->whereTime('start_time', '<', $endFormatted)
                      ->whereTime('end_time', '>', $startFormatted);
            })
            ->exists();

        if ($isConflict) {
            return back()->withInput()->with('error', 'Gagal! Jadwal bentrok dengan film lain di studio ini.');
        }

        // 4. Simpan Data
        Schedule::create([
            'film_id' => $request->film_id,
            'studio_id' => $request->studio_id,
            'schedule_date' => $request->schedule_date,
            'start_time' => $startFormatted,
            'end_time' => $endFormatted,
            'ticket_price' => $request->ticket_price,
            'status' => $request->status ?? 'active'
        ]);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function edit(Schedule $schedule)
    {
        $films = Film::all();
        $studios = Studio::all();
        return view('admin.schedules.edit', compact('schedule', 'films', 'studios'));
    }

    public function update(Request $request, Schedule $schedule)
    {

        $request->validate([
            'film_id' => 'required|exists:films,id',
            'studio_id' => 'required|exists:studios,id',
            'schedule_date' => 'required|date',
            'start_time' => 'required',
            'ticket_price' => 'required|numeric',
        ]);

        $film = Film::findOrFail($request->film_id);
        $startTime = Carbon::parse($request->start_time);
        $endTime = (clone $startTime)->addMinutes($film->duration);

        $startFormatted = $startTime->format('H:i:s');
        $endFormatted = $endTime->format('H:i:s');


        $isConflict = Schedule::where('studio_id', $request->studio_id)
            ->where('id', '!=', $schedule->id)
            ->whereDate('schedule_date', $request->schedule_date)
            ->where(function ($query) use ($startFormatted, $endFormatted) {
                $query->whereTime('start_time', '<', $endFormatted)
                      ->whereTime('end_time', '>', $startFormatted);
            })
            ->exists();

        if ($isConflict) {
            return back()->withInput()->with('error', 'Gagal! Jadwal bentrok dengan film lain di studio ini.');
        }

        $schedule->update([
            'film_id' => $request->film_id,
            'studio_id' => $request->studio_id,
            'schedule_date' => $request->schedule_date,
            'start_time' => $startFormatted,
            'end_time' => $endFormatted,
            'ticket_price' => $request->ticket_price,
            'status' => $request->status ?? $schedule->status
        ]);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil diperbarui!');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil dihapus!');
    }
}
