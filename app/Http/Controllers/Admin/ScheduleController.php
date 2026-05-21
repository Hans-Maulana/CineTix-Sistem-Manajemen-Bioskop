<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Film;
use App\Models\Studio;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with(['film', 'studio'])->latest()->paginate(15);
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
        $request->validate([
            'film_id' => 'required|exists:films,id',
            'studio_id' => 'required|exists:studios,id',
            'schedule_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'ticket_price' => 'required|numeric',
        ]);

        $startTime = \Carbon\Carbon::parse($request->start_time)->format('H:i:s');
        $endTime = \Carbon\Carbon::parse($request->end_time)->format('H:i:s');

        if ($startTime >= $endTime) {
            return back()->with('error', 'Jam selesai harus setelah jam mulai.')->withInput();
        }

        $overlap = Schedule::where('studio_id', $request->studio_id)
            ->where('schedule_date', $request->schedule_date)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            })
            ->exists();

        if ($overlap) {
            return back()->with('error', 'Jadwal bentrok! Studio ini sudah memiliki film lain pada waktu tersebut.')->withInput();
        }

        Schedule::create($request->all());

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
            'end_time' => 'required',
            'ticket_price' => 'required|numeric',
        ]);

        $startTime = \Carbon\Carbon::parse($request->start_time)->format('H:i:s');
        $endTime = \Carbon\Carbon::parse($request->end_time)->format('H:i:s');

        if ($startTime >= $endTime) {
            return back()->with('error', 'Jam selesai harus setelah jam mulai.')->withInput();
        }

        $overlap = Schedule::where('studio_id', $request->studio_id)
            ->where('schedule_date', $request->schedule_date)
            ->where('id', '!=', $schedule->id)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            })
            ->exists();

        if ($overlap) {
            return back()->with('error', 'Jadwal bentrok! Studio ini sudah memiliki film lain pada waktu tersebut.')->withInput();
        }

        $schedule->update($request->all());

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil diperbarui!');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil dihapus!');
    }
}
