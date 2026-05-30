<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Film;
use App\Models\Studio;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with('film', 'studio')->paginate(10);
        return view('schedules.index', compact('schedules'));
    }

    public function create()
    {
        $films = Film::all();
        $studios = Studio::all();
        return view('schedules.create', compact('films', 'studios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'film_id' => 'required|exists:films,id',
            'studio_id' => 'required|exists:studios,id',
            'schedule_date' => 'required|date|after:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'ticket_price' => 'required|decimal:0,2|min:0',
        ]);

        $startTime = \Carbon\Carbon::parse($validated['start_time'])->format('H:i:s');
        $endTime = \Carbon\Carbon::parse($validated['end_time'])->format('H:i:s');

        $overlap = Schedule::where('studio_id', $validated['studio_id'])
            ->where('schedule_date', $validated['schedule_date'])
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            })
            ->exists();

        if ($overlap) {
            return back()->with('error', 'Jadwal bentrok! Studio ini sudah memiliki film lain pada waktu tersebut.')->withInput();
        }

        Schedule::create($validated);

        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil ditambahkan');
    }

    public function show(Schedule $schedule)
    {
        $schedule->load('film', 'studio', 'ticketBookings');
        return view('schedules.show', compact('schedule'));
    }

    public function edit(Schedule $schedule)
    {
        $films = Film::all();
        $studios = Studio::all();
        return view('schedules.edit', compact('schedule', 'films', 'studios'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'film_id' => 'required|exists:films,id',
            'studio_id' => 'required|exists:studios,id',
            'schedule_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'ticket_price' => 'required|decimal:0,2|min:0',
        ]);

        $startTime = \Carbon\Carbon::parse($validated['start_time'])->format('H:i:s');
        $endTime = \Carbon\Carbon::parse($validated['end_time'])->format('H:i:s');

        $overlap = Schedule::where('studio_id', $validated['studio_id'])
            ->where('schedule_date', $validated['schedule_date'])
            ->where('id', '!=', $schedule->id)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            })
            ->exists();

        if ($overlap) {
            return back()->with('error', 'Jadwal bentrok! Studio ini sudah memiliki film lain pada waktu tersebut.')->withInput();
        }

        $schedule->update($validated);

        return redirect()->route('schedules.show', $schedule)->with('success', 'Jadwal berhasil diperbarui');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil dihapus');
    }
}
