@php
    $rating = round($film->reviews->avg('rating') ?? (float) ($film->rating ?? 0), 1);
    $status = $film->status ?? 'now_playing';
    $statusLabel = match ($status) {
        'now_playing' => 'Now Playing',
        'coming_soon' => 'Segera Tayang',
        default => ucfirst(str_replace('_', ' ', $status)),
    };
    $isNowPlaying = $status === 'now_playing';
    $rank = $rank ?? null;
    $ticketsSold = $ticketsSold ?? ($film->tickets_sold ?? null);
    $rankClass = match ((int) $rank) {
        1 => 'rank-1',
        2 => 'rank-2',
        3 => 'rank-3',
        default => 'rank-x',
    };

    // Jadwal hari ini (dari eager-load todaySchedules di HomeController)
    $todaySchedules = $film->relationLoaded('todaySchedules')
        ? $film->todaySchedules
        : collect();

    // Cek apakah ada jadwal yang sedang bermain sekarang (status 'now playing')
    $isLiveNow = $film->schedules()->where('status', 'now playing')->exists() ?? false;
    // Simpel: cek dari todaySchedules apakah ada yang sedang live (start <= now <= end)
    $nowTime = \Carbon\Carbon::now();
    $liveSchedule = $todaySchedules->first(function ($s) use ($nowTime) {
        $start = \Carbon\Carbon::parse($s->schedule_date->format('Y-m-d') . ' ' . $s->start_time->format('H:i:s'));
        $end   = \Carbon\Carbon::parse($s->schedule_date->format('Y-m-d') . ' ' . $s->end_time->format('H:i:s'));
        if ($end->lte($start)) $end->addDay();
        return $nowTime->between($start, $end);
    });
@endphp

<div class="cx-film-card {{ $rank ? 'cx-film-card--ranked' : '' }}" data-aos="fade-up" data-aos-duration="800"
     @if($todaySchedules->count()) data-genre="{{ $film->genres->pluck('genre_name')->implode(',') }}" data-classification="{{ $film->classification }}" @endif>
    @if($rank)
        <span class="cx-rank-badge {{ $rankClass }}" aria-label="Peringkat {{ $rank }}">{{ $rank }}</span>
    @endif
    <div class="cx-film-poster">
        <img src="{{ $film->cover_url }}" alt="{{ $film->title }}" loading="lazy">
        @if($film->classification)
            <span class="cx-classification">{{ strtoupper($film->classification) }}</span>
        @endif

        {{-- Live now badge --}}
        @if($liveSchedule)
            <span class="cx-live-badge">
                <span class="cx-live-dot"></span> LIVE
            </span>
        @endif

        <div class="cx-poster-overlay">
            <div class="cx-poster-bottom">
                <span class="cx-status-badge {{ $isNowPlaying ? 'cx-status-now' : 'cx-status-soon' }}">
                    <iconify-icon icon="{{ $isNowPlaying ? 'lucide:play' : 'lucide:clock' }}"></iconify-icon>
                    {{ $statusLabel }}
                </span>
                @if($rating > 0)
                    <span class="cx-rating">
                        <iconify-icon icon="lucide:star"></iconify-icon>
                        {{ number_format($rating, 1) }}
                    </span>
                @endif
            </div>
        </div>
        <a href="{{ route('films.detail', $film) }}" class="cx-poster-link" aria-label="Detail {{ $film->title }}"></a>
    </div>

    <div class="cx-film-body">
        <h3 class="cx-film-title" title="{{ $film->title }}">{{ $film->title }}</h3>

        <div class="cx-film-meta">
            @if($film->duration)
                <span><iconify-icon icon="lucide:clock"></iconify-icon>{{ $film->duration }} mnt</span>
            @endif
            @if($ticketsSold !== null && $ticketsSold > 0)
                <span><iconify-icon icon="lucide:ticket"></iconify-icon>{{ number_format($ticketsSold, 0, ',', '.') }} tiket</span>
            @endif
        </div>

        <div class="cx-film-genres">
            @forelse($film->genres->take(3) as $genre)
                <span class="cx-genre-tag">{{ $genre->genre_name }}</span>
            @empty
                <span class="cx-genre-tag cx-genre-muted">Film</span>
            @endforelse
            @if($film->genres->count() > 3)
                <span class="cx-genre-tag">+{{ $film->genres->count() - 3 }}</span>
            @endif
        </div>

        {{-- Showtime pills (hanya untuk now playing) --}}
        @if($isNowPlaying && $todaySchedules->count())
            <div class="cx-showtime-row">
                <span class="cx-showtime-label">
                    <iconify-icon icon="lucide:calendar-check"></iconify-icon>
                    Hari ini:
                </span>
                <div class="cx-showtime-pills">
                    @foreach($todaySchedules->take(3) as $sched)
                        @php
                            $schedStart = \Carbon\Carbon::parse($sched->schedule_date->format('Y-m-d') . ' ' . $sched->start_time->format('H:i:s'));
                            $schedEnd   = \Carbon\Carbon::parse($sched->schedule_date->format('Y-m-d') . ' ' . $sched->end_time->format('H:i:s'));
                            if ($schedEnd->lte($schedStart)) $schedEnd->addDay();
                            $isThisLive = $nowTime->between($schedStart, $schedEnd);
                        @endphp
                        <a href="{{ route('booking.show', $sched) }}"
                           class="cx-showtime-pill {{ $isThisLive ? 'cx-showtime-pill--live' : '' }}"
                           title="{{ $sched->studio->name ?? 'Studio' }} · Rp {{ number_format($sched->ticket_price, 0, ',', '.') }}">
                            @if($isThisLive)
                                <span class="cx-pill-dot"></span>
                            @endif
                            {{ $sched->start_time->format('H:i') }}
                        </a>
                    @endforeach
                    @if($todaySchedules->count() > 3)
                        <a href="{{ route('films.detail', $film) }}" class="cx-showtime-pill cx-showtime-pill--more">
                            +{{ $todaySchedules->count() - 3 }}
                        </a>
                    @endif
                </div>
            </div>
        @elseif($isNowPlaying && $todaySchedules->isEmpty())
            {{-- Tidak ada jadwal hari ini, cari jadwal terdekat --}}
            @php
                $nextSched = $film->schedules()
                    ->upcomingForBooking()
                    ->orderBy('schedule_date')->orderBy('start_time')
                    ->first();
            @endphp
            @if($nextSched)
                <div class="cx-showtime-row">
                    <span class="cx-showtime-label" style="color:#8a93a6;">
                        <iconify-icon icon="lucide:calendar"></iconify-icon>
                        Jadwal berikutnya:
                    </span>
                    <span class="cx-showtime-pill" style="background:#f4f6fa;color:#5c6478;border-color:#e0e4ee;">
                        {{ $nextSched->schedule_date->translatedFormat('d M') }}, {{ $nextSched->start_time->format('H:i') }}
                    </span>
                </div>
            @endif
        @endif

        <div class="cx-film-actions">
            @if($isNowPlaying)
                <a href="{{ route('films.detail', $film) }}" class="cx-btn-book">
                    <iconify-icon icon="lucide:ticket"></iconify-icon>
                    Pesan Sekarang
                </a>
            @else
                <a href="{{ route('films.detail', $film) }}" class="cx-btn-book cx-btn-outline">
                    <iconify-icon icon="lucide:eye"></iconify-icon>
                    Lihat Detail
                </a>
            @endif
        </div>
    </div>
</div>

