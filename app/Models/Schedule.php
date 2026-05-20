<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'film_id',
        'studio_id',
        'schedule_date',
        'start_time',
        'end_time',
        'ticket_price',
        'status',
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'start_time'    => 'datetime:H:i',
        'end_time'      => 'datetime:H:i',
        'ticket_price'  => 'decimal:2',
    ];



    public function film()
    {
        return $this->belongsTo(Film::class);
    }

    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }

    public function ticketBookings()
    {
        return $this->hasMany(TicketBooking::class);
    }

    /**
     * Get remaining available seats for this schedule.
     */
    public function getAvailableSeatsAttribute(): int
    {
        $occupiedCount = $this->ticketBookings()
            ->whereHas('booking', function ($query) {
                $query->whereIn('status', ['pending', 'confirmed']);
            })
            ->count();

        return max(0, $this->studio->capacity - $occupiedCount);
    }

    /**
     * Check if the schedule has started or ended (is in the past).
     */
    public function isPast(): bool
    {
        if ($this->status === 'complete') {
            return true;
        }

        $dateStr = $this->schedule_date->format('Y-m-d');
        $startStr = $this->start_time ? $this->start_time->format('H:i:s') : '00:00:00';
        $timeStr = $this->end_time ? $this->end_time->format('H:i:s') : '23:59:59';

        $startDateTime = \Carbon\Carbon::parse($dateStr . ' ' . $startStr);
        $endDateTime = \Carbon\Carbon::parse($dateStr . ' ' . $timeStr);

        // Handle midnight rollover (e.g. start 22:20, end 01:21 next day)
        if ($endDateTime->lessThanOrEqualTo($startDateTime)) {
            $endDateTime->addDay();
        }

        return $endDateTime->isPast();
    }

    /**
     * Auto update statuses of schedules based on current time.
     */
    public static function autoUpdateStatuses()
    {
        // 1. Mark past dates as complete (but exclude today — handled below)
        self::whereIn('status', ['on schedule', 'now playing'])
            ->where('schedule_date', '<', \Carbon\Carbon::today()->toDateString())
            ->get()
            ->each(function ($schedule) {
                $dateStr = $schedule->schedule_date->format('Y-m-d');
                $startStr = $schedule->start_time->format('H:i:s');
                $endStr = $schedule->end_time->format('H:i:s');

                $startDateTime = \Carbon\Carbon::parse($dateStr . ' ' . $startStr);
                $endDateTime = \Carbon\Carbon::parse($dateStr . ' ' . $endStr);

                // Handle midnight rollover
                if ($endDateTime->lessThanOrEqualTo($startDateTime)) {
                    $endDateTime->addDay();
                }

                // Only mark complete if the end time has truly passed
                if ($endDateTime->isPast()) {
                    $schedule->update(['status' => 'complete']);
                }
            });

        // 2. Mark today's schedules
        $todaySchedules = self::whereIn('status', ['on schedule', 'now playing'])
            ->where('schedule_date', \Carbon\Carbon::today()->toDateString())
            ->get();

        $now = \Carbon\Carbon::now();
        foreach ($todaySchedules as $schedule) {
            $dateStr = $schedule->schedule_date->format('Y-m-d');
            $startStr = $schedule->start_time->format('H:i:s');
            $endStr = $schedule->end_time->format('H:i:s');
            
            $startDateTime = \Carbon\Carbon::parse($dateStr . ' ' . $startStr);
            $endDateTime = \Carbon\Carbon::parse($dateStr . ' ' . $endStr);

            // Handle midnight rollover (e.g. start 22:20, end 01:21 next day)
            if ($endDateTime->lessThanOrEqualTo($startDateTime)) {
                $endDateTime->addDay();
            }

            $tenMinutesBeforeStart = $startDateTime->copy()->subMinutes(10);

            if ($now->greaterThanOrEqualTo($endDateTime)) {
                $schedule->update(['status' => 'complete']);
            } elseif ($now->greaterThanOrEqualTo($tenMinutesBeforeStart)) {
                $schedule->update(['status' => 'now playing']);
            }
        }
    }
}
