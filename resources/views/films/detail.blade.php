@extends('layouts.app')

@push('styles')
@include('partials.customer_film_styles')
<style>
    body {
        background-color: #e4e8ef !important;
    }

    .fd-page {
        padding-bottom: 2.5rem;
    }

    .fd-surface {
        background: #fff;
        border: 1px solid rgba(26, 25, 83, 0.1);
        box-shadow: 0 4px 16px rgba(26, 25, 83, 0.07);
    }

    .fd-poster-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid rgba(26, 25, 83, 0.1);
        box-shadow: 0 8px 24px rgba(26, 25, 83, 0.1);
        overflow: hidden;
        position: sticky;
        top: 110px;
    }
    .fd-poster-wrap {
        position: relative;
        aspect-ratio: 2 / 3;
        background: linear-gradient(135deg, #1A1953, #3a37a0);
    }
    .fd-poster-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .fd-poster-badges {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 14px;
        background: linear-gradient(to top, rgba(0,0,0,0.85), transparent);
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }
    .fd-poster-meta {
        padding: 16px 18px 18px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .fd-meta-row {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.88rem;
        color: #5c6478;
        font-weight: 600;
    }
    .fd-meta-row iconify-icon { color: #1A1953; font-size: 1.1rem; }
    .fd-genre-list { display: flex; flex-wrap: wrap; gap: 6px; }

    .fd-title {
        font-size: clamp(1.75rem, 4vw, 2.5rem);
        font-weight: 800;
        color: #1f2533;
        line-height: 1.15;
        margin-bottom: 0;
    }
    .fd-badge-soon {
        background: rgba(212, 176, 106, 0.2);
        color: #8a6d2b;
        font-weight: 800;
        font-size: 0.72rem;
        letter-spacing: 0.06em;
        padding: 6px 12px;
        border-radius: 999px;
    }

    .fd-panel {
        background: #fff;
        border: 1px solid rgba(26, 25, 83, 0.1);
        border-radius: 18px;
        padding: 20px 22px;
        box-shadow: 0 4px 16px rgba(26, 25, 83, 0.07);
        margin-bottom: 1.5rem;
    }
    .fd-panel-title {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #8a93a6;
        margin-bottom: 10px;
    }
    .fd-synopsis {
        color: #4a5568;
        line-height: 1.75;
        margin: 0;
        font-size: 0.95rem;
    }

    .fd-crew-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 14px;
        margin-bottom: 1.5rem;
    }
    .fd-crew-item {
        background: #fff;
        border: 1px solid rgba(26, 25, 83, 0.1);
        border-radius: 14px;
        padding: 14px 16px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        box-shadow: 0 2px 10px rgba(26, 25, 83, 0.06);
    }
    .fd-crew-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: rgba(26, 25, 83, 0.07);
        color: #1A1953;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .fd-crew-label { font-size: 0.72rem; color: #8a93a6; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; }
    .fd-crew-value { font-size: 0.9rem; font-weight: 700; color: #1f2533; line-height: 1.35; }

    .fd-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 1.25rem;
    }
    .fd-section-head h3 {
        font-size: 1.25rem;
        font-weight: 800;
        color: #1f2533;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .fd-section-head h3::before {
        content: "";
        width: 4px;
        height: 22px;
        background: #1A1953;
        border-radius: 4px;
    }

    .fd-day-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 1.25rem;
    }
    .fd-day-tab {
        border: 1.5px solid rgba(26, 25, 83, 0.15);
        background: #fff;
        color: #1A1953;
        font-size: 0.82rem;
        font-weight: 700;
        padding: 8px 16px;
        border-radius: 999px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .fd-day-tab:hover, .fd-day-tab.active {
        background: #1A1953;
        color: #fff;
        border-color: #1A1953;
    }
    .fd-day-tab-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 1.35rem;
        height: 1.35rem;
        padding: 0 5px;
        margin-left: 6px;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 800;
        background: rgba(26, 25, 83, 0.1);
    }
    .fd-day-tab:hover .fd-day-tab-count,
    .fd-day-tab.active .fd-day-tab-count {
        background: rgba(255, 255, 255, 0.22);
    }

    .fd-schedule-panel,
    .fd-cinema-schedule {
        position: relative;
    }

    .fd-cinema-day {
        display: flex;
        flex-direction: column;
        gap: 0;
        background: #fff;
        border: 1px solid rgba(26, 25, 83, 0.1);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(26, 25, 83, 0.07);
    }

    .fd-cinema-studio-row {
        display: grid;
        grid-template-columns: minmax(120px, 160px) 1fr;
        gap: 16px;
        align-items: center;
        padding: 16px 18px;
        border-bottom: 1px solid rgba(26, 25, 83, 0.06);
    }
    .fd-cinema-studio-row:last-child {
        border-bottom: none;
    }

    .fd-cinema-studio-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .fd-cinema-studio-name {
        font-size: 0.92rem;
        font-weight: 800;
        color: #1f2533;
        line-height: 1.2;
    }
    .fd-cinema-studio-type {
        display: inline-flex;
        align-self: flex-start;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: #1A1953;
        background: rgba(26, 25, 83, 0.08);
        padding: 3px 8px;
        border-radius: 6px;
    }

    .fd-cinema-slots {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .fd-showtime-chip {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-width: 78px;
        padding: 9px 12px;
        border-radius: 10px;
        border: 1.5px solid rgba(26, 25, 83, 0.22);
        background: #fff;
        text-decoration: none;
        transition: all 0.18s ease;
        cursor: pointer;
    }
    .fd-showtime-chip.is-ok {
        border-color: #19a75f;
        background: rgba(25, 167, 95, 0.06);
    }
    .fd-showtime-chip.is-low {
        border-color: #fb8c00;
        background: rgba(251, 140, 0, 0.07);
    }
    .fd-showtime-chip.is-ok:hover,
    .fd-showtime-chip.is-low:hover {
        background: #1A1953;
        border-color: #1A1953;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(26, 25, 83, 0.18);
    }
    .fd-showtime-chip.is-ok:hover .fd-showtime-chip-time,
    .fd-showtime-chip.is-ok:hover .fd-showtime-chip-meta,
    .fd-showtime-chip.is-low:hover .fd-showtime-chip-time,
    .fd-showtime-chip.is-low:hover .fd-showtime-chip-meta {
        color: #fff;
    }

    .fd-showtime-chip.is-disabled {
        border-color: #e2e5ec;
        background: #f6f7fa;
        cursor: not-allowed;
        opacity: 0.85;
    }
    .fd-showtime-chip.is-playing {
        border-color: #d4b06a;
        background: rgba(212, 176, 106, 0.12);
    }

    .fd-showtime-chip-time {
        font-size: 0.95rem;
        font-weight: 800;
        color: #1A1953;
        line-height: 1.1;
    }
    .fd-showtime-chip-meta {
        font-size: 0.62rem;
        font-weight: 700;
        color: #8a93a6;
        margin-top: 3px;
        white-space: nowrap;
    }
    .fd-showtime-chip.is-disabled .fd-showtime-chip-time {
        color: #8a93a6;
    }

    .fd-cinema-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        margin-top: 12px;
        font-size: 0.72rem;
        font-weight: 600;
        color: #8a93a6;
    }
    .fd-cinema-legend span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .fd-cinema-legend i {
        width: 10px;
        height: 10px;
        border-radius: 3px;
        flex-shrink: 0;
    }
    .fd-cinema-legend .leg-ok { background: #19a75f; }
    .fd-cinema-legend .leg-low { background: #fb8c00; }
    .fd-cinema-legend .leg-full { background: #c5cad6; }

    @media (max-width: 575px) {
        .fd-cinema-studio-row {
            grid-template-columns: 1fr;
            gap: 10px;
            padding: 14px 16px;
        }
        .fd-cinema-studio-info {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
        .fd-showtime-chip {
            min-width: 72px;
            padding: 8px 10px;
        }
    }

    .fd-review-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 14px;
    }
    .fd-review-card {
        background: #fff;
        border: 1px solid rgba(26, 25, 83, 0.1);
        border-radius: 16px;
        padding: 18px;
        box-shadow: 0 2px 10px rgba(26, 25, 83, 0.06);
    }
    .fd-empty {
        text-align: center;
        padding: 3rem 1.5rem;
        background: #fff;
        border-radius: 18px;
        border: 1px dashed rgba(26, 25, 83, 0.18);
        box-shadow: 0 2px 10px rgba(26, 25, 83, 0.05);
        color: #8a93a6;
    }

    .btn-back-custom {
        border: 2px solid #1A1953 !important;
        color: #1A1953 !important;
        font-weight: bold;
        background: transparent;
        transition: all 0.3s ease;
    }
    .btn-back-custom:hover {
        background-color: #1A1953 !important;
        color: #ffffff !important;
    }
    .breadcrumb-item+.breadcrumb-item::before {
        content: "›";
        font-size: 1.25rem;
        vertical-align: middle;
    }

    @media (max-width: 991px) {
        .fd-poster-card { position: static; margin-bottom: 1.5rem; }
    }
</style>
@endpush

@section('content')
<div class="fd-page">
<div class="container py-4 py-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-down">
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('landing-page') }}" class="text-primary text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('films.search') }}" class="text-primary text-decoration-none">Film</a></li>
                <li class="breadcrumb-item active">{{ $film->title }}</li>
            </ol>
        </nav>
        <a href="{{ route('landing-page') }}" class="btn btn-back-custom rounded-pill px-4 py-2 d-flex align-items-center gap-2">
            <iconify-icon icon="lucide:arrow-left"></iconify-icon>
            <span>Kembali</span>
        </a>
    </div>

    <div class="row g-4 g-lg-5">
        {{-- Poster --}}
        <div class="col-lg-4" data-aos="fade-right">
            <div class="fd-poster-card">
                <div class="fd-poster-wrap">
                    <img src="{{ $film->cover_url }}" alt="{{ $film->title }}">
                    <div class="fd-poster-badges">
                        @if($film->classification)
                            <span class="cx-classification" style="position:static;">{{ strtoupper($film->classification) }}</span>
                        @endif
                        @if($film->status !== 'coming_soon' && $avgRating > 0)
                            <span class="cx-rating" style="position:static;">
                                <iconify-icon icon="lucide:star"></iconify-icon>
                                {{ number_format($avgRating, 1) }}/5
                            </span>
                        @endif
                    </div>
                </div>
                <div class="fd-poster-meta">
                    <div class="fd-meta-row">
                        <iconify-icon icon="lucide:clock"></iconify-icon>
                        {{ $film->duration }} menit
                    </div>
                    @if($film->status === 'coming_soon' && $film->release_date)
                        <div class="fd-meta-row">
                            <iconify-icon icon="lucide:calendar"></iconify-icon>
                            Rilis {{ \Carbon\Carbon::parse($film->release_date)->translatedFormat('d F Y') }}
                        </div>
                    @endif
                    <div class="fd-genre-list">
                        @foreach($film->genres as $genre)
                            <span class="cx-genre-tag">{{ $genre->genre_name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail --}}
        <div class="col-lg-8" data-aos="fade-up">
            <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
                <h1 class="fd-title">{{ $film->title }}</h1>
                @if($film->status === 'coming_soon')
                    <span class="fd-badge-soon">COMING SOON</span>
                @endif
            </div>

            @if($film->synopsis)
                <div class="fd-panel">
                    <div class="fd-panel-title">Sinopsis</div>
                    <p class="fd-synopsis">{{ $film->synopsis }}</p>
                </div>
            @endif

            <div class="fd-crew-grid">
                @if($film->director)
                    <div class="fd-crew-item">
                        <div class="fd-crew-icon"><iconify-icon icon="lucide:clapperboard"></iconify-icon></div>
                        <div>
                            <div class="fd-crew-label">Sutradara</div>
                            <div class="fd-crew-value">{{ $film->director }}</div>
                        </div>
                    </div>
                @endif
                @if($film->production)
                    <div class="fd-crew-item">
                        <div class="fd-crew-icon"><iconify-icon icon="lucide:building-2"></iconify-icon></div>
                        <div>
                            <div class="fd-crew-label">Produksi</div>
                            <div class="fd-crew-value">{{ $film->production }}</div>
                        </div>
                    </div>
                @endif
                @if($film->actors)
                    <div class="fd-crew-item" style="grid-column: 1 / -1;">
                        <div class="fd-crew-icon"><iconify-icon icon="lucide:users"></iconify-icon></div>
                        <div>
                            <div class="fd-crew-label">Pemeran</div>
                            <div class="fd-crew-value">{{ $film->actors }}</div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Jadwal --}}
            <div class="fd-section-head">
                <h3>{{ $film->status === 'coming_soon' ? 'Informasi Rilis' : 'Jadwal Tayang' }}</h3>
                @if($film->status !== 'coming_soon')
                    <span class="cx-section-footer-meta">
                        @if($film->schedules->isNotEmpty())
                            {{ $film->schedules->count() }} jadwal tersedia
                        @else
                            Tiket dibuka {{ \App\Models\Schedule::BOOKING_WINDOW_DAYS }} hari sebelum tayang
                        @endif
                    </span>
                @endif
            </div>

            @if($film->status === 'coming_soon')
                <div class="fd-panel text-center" style="background: linear-gradient(120deg, #1A1953, #2d2b7a); color: #fff; border: none;">
                    <iconify-icon icon="lucide:calendar-clock" style="font-size: 2.5rem; opacity: 0.9;"></iconify-icon>
                    <h4 class="fw-bold text-white mt-3 mb-2">Segera Tayang di CineTix</h4>
                    <p class="text-white-50 mb-0 mx-auto" style="max-width: 480px;">
                        Penjualan tiket dibuka mendekati tanggal rilis. Pantau halaman ini atau daftar promo untuk info terbaru.
                    </p>
                </div>
            @elseif($film->schedules->isNotEmpty())
                @php
                    $schedulesByDate = $film->schedules->groupBy(fn ($schedule) => $schedule->schedule_date->format('Y-m-d'));
                    $scheduleDates = $schedulesByDate->keys()->sort()->values();
                @endphp

                <div class="fd-day-tabs">
                    @foreach($scheduleDates as $index => $dateStr)
                        @php
                            $date = \Carbon\Carbon::parse($dateStr);
                            $dayCount = $schedulesByDate[$dateStr]->count();
                        @endphp
                        <button type="button"
                                class="fd-day-tab day-tab {{ $index === 0 ? 'active' : '' }}"
                                data-date="{{ $dateStr }}">
                            {{ $date->translatedFormat('D, d M') }}
                            <span class="fd-day-tab-count">{{ $dayCount }}</span>
                        </button>
                    @endforeach
                </div>

                <div class="fd-cinema-schedule">
                    @foreach($scheduleDates as $index => $dateStr)
                        @php
                            $daySchedules = $schedulesByDate[$dateStr]->sortBy(
                                fn ($schedule) => $schedule->start_time->format('H:i:s')
                            );
                            $byStudio = $daySchedules->groupBy('studio_id');
                        @endphp
                        <div class="fd-cinema-day schedule-day-panel"
                             data-date="{{ $dateStr }}"
                             @if($index !== 0) style="display:none" @endif>
                            @foreach($byStudio as $studioSchedules)
                                @php
                                    $studio = $studioSchedules->first()->studio;
                                    $studioType = $studio->type->name ?? '2D';
                                @endphp
                                <div class="fd-cinema-studio-row">
                                    <div class="fd-cinema-studio-info">
                                        <span class="fd-cinema-studio-name">{{ $studio->name }}</span>
                                        <span class="fd-cinema-studio-type">{{ $studioType }}</span>
                                    </div>
                                    <div class="fd-cinema-slots">
                                        @foreach($studioSchedules as $schedule)
                                            @php
                                                $percent = $schedule->studio->capacity > 0
                                                    ? ($schedule->available_seats / $schedule->studio->capacity) * 100
                                                    : 0;
                                                $canBook = $schedule->available_seats > 0
                                                    && in_array($schedule->status, ['on schedule', 'active']);
                                                $chipClass = match (true) {
                                                    ! $canBook => 'is-disabled',
                                                    $schedule->status === 'now playing' => 'is-playing',
                                                    $percent > 30 => 'is-ok',
                                                    $percent > 0 => 'is-low',
                                                    default => 'is-disabled',
                                                };
                                                $priceLabel = 'Rp ' . number_format($schedule->ticket_price, 0, ',', '.');
                                                $tooltip = $priceLabel . ' · ' . $schedule->available_seats . '/' . $schedule->studio->capacity . ' kursi';
                                            @endphp
                                            @if($canBook)
                                                <a href="{{ route('booking.show', $schedule) }}"
                                                   class="fd-showtime-chip {{ $chipClass }}"
                                                   title="{{ $tooltip }}">
                                                    <span class="fd-showtime-chip-time">{{ $schedule->start_time->format('H:i') }}</span>
                                                    <span class="fd-showtime-chip-meta">{{ $priceLabel }}</span>
                                                </a>
                                            @else
                                                <span class="fd-showtime-chip {{ $chipClass }}"
                                                      title="{{ $tooltip }}">
                                                    <span class="fd-showtime-chip-time">{{ $schedule->start_time->format('H:i') }}</span>
                                                    <span class="fd-showtime-chip-meta">
                                                        @if($schedule->status === 'canceled')
                                                            Batal
                                                        @elseif($schedule->status === 'complete')
                                                            Selesai
                                                        @else
                                                            Habis
                                                        @endif
                                                    </span>
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach

                    <div class="fd-cinema-legend">
                        <span><i class="leg-ok"></i> Tersedia</span>
                        <span><i class="leg-low"></i> Hampir penuh</span>
                        <span><i class="leg-full"></i> Habis / nonaktif</span>
                        <span>Klik jam tayang untuk pilih kursi</span>
                    </div>
                </div>
            @else
                <div class="fd-empty">
                    <iconify-icon icon="lucide:calendar-x" style="font-size: 2.5rem; opacity: 0.4;"></iconify-icon>
                    <p class="mb-0 mt-2 fw-semibold">Belum ada jadwal tayang untuk film ini.</p>
                </div>
            @endif

            {{-- Ulasan --}}
            @if($film->status !== 'coming_soon')
                <div class="fd-section-head mt-5 pt-2">
                    <h3>Ulasan Penonton</h3>
                    <span class="cx-genre-tag">{{ $film->reviews->count() }} ulasan</span>
                </div>

                @if($film->reviews->isNotEmpty())
                    <div class="fd-review-grid">
                        @foreach($film->reviews as $review)
                            <div class="fd-review-card">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width:36px;height:36px;font-size:0.85rem;background:#1A1953!important;">
                                            {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                        </div>
                                        <span class="fw-bold text-dark">{{ $review->user->name }}</span>
                                    </div>
                                    <span class="cx-rating" style="position:static;font-size:0.75rem;">
                                        <iconify-icon icon="lucide:star"></iconify-icon>
                                        {{ $review->rating }}
                                    </span>
                                </div>
                                @if($review->comment)
                                    <p class="text-muted small mb-2">{{ $review->comment }}</p>
                                @endif
                                <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="fd-empty py-4">
                        <p class="mb-0">Belum ada ulasan untuk film ini.</p>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.day-tab');
        const dayPanels = document.querySelectorAll('.schedule-day-panel');

        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                const date = this.getAttribute('data-date');
                dayPanels.forEach(panel => {
                    panel.style.display = panel.getAttribute('data-date') === date ? '' : 'none';
                });
            });
        });
    });
</script>
@endpush
@endsection
