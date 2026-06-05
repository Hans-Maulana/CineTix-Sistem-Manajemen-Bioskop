@extends('layouts.admin')

@section('title', 'Detail Pengunjung Jadwal')

@push('styles')
<style>
    .sd-hero {
        background: linear-gradient(120deg, #1A1953 0%, #2d2b7a 60%, #3a37a0 100%);
        border-radius: 24px;
        color: #fff;
        padding: 28px 32px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(26, 25, 83, 0.25);
    }
    .sd-hero::after {
        content: ""; position: absolute;
        right: -60px; top: -60px;
        width: 220px; height: 220px;
        background: rgba(212, 176, 106, 0.18);
        border-radius: 50%;
    }
    .sd-back {
        display: inline-flex; align-items: center; gap: 8px;
        color: rgba(255,255,255,0.85);
        font-size: 0.85rem; font-weight: 600;
        text-decoration: none;
        padding: 6px 12px; border-radius: 999px;
        background: rgba(255,255,255,0.1);
        transition: all 0.2s;
    }
    .sd-back:hover { background: rgba(255,255,255,0.18); color: #fff; }

    .sd-info-grid {
        display: grid;
        grid-template-columns: 220px 1fr;
        gap: 24px;
        margin-top: 18px;
        position: relative; z-index: 1;
        align-items: start;
    }
    @media (max-width: 768px) { .sd-info-grid { grid-template-columns: 1fr; } }
    .sd-poster {
        aspect-ratio: 2 / 3;
        border-radius: 18px;
        overflow: hidden;
        background: rgba(255,255,255,0.08);
        box-shadow: 0 14px 32px rgba(0, 0, 0, 0.3);
    }
    .sd-poster img { width: 100%; height: 100%; object-fit: cover; }
    .sd-poster .placeholder {
        height: 100%; display: flex; align-items: center; justify-content: center;
        color: rgba(255,255,255,0.4); font-size: 2rem;
    }
    .sd-info h1 { font-size: 1.7rem; font-weight: 800; margin: 6px 0 10px; }
    .sd-meta {
        display: flex; gap: 16px; flex-wrap: wrap;
        font-size: 0.92rem; opacity: 0.92;
        margin-bottom: 14px;
    }
    .sd-meta span { display: inline-flex; align-items: center; gap: 8px; }

    .sd-stat-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-top: 10px;
    }
    @media (max-width: 768px) { .sd-stat-row { grid-template-columns: repeat(2, 1fr); } }
    .sd-stat-card {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(8px);
        border-radius: 14px;
        padding: 12px 16px;
        border: 1px solid rgba(255,255,255,0.12);
    }
    .sd-stat-card .lbl {
        font-size: 0.7rem; letter-spacing: 0.06em; text-transform: uppercase;
        opacity: 0.8; font-weight: 700;
    }
    .sd-stat-card .val {
        font-size: 1.3rem; font-weight: 800; margin-top: 2px;
    }
    .sd-stat-card .val small { font-size: 0.75rem; opacity: 0.7; font-weight: 500; }

    .sd-toolbar {
        background: #fff;
        border-radius: 16px;
        padding: 14px 18px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 6px 18px rgba(26, 25, 83, 0.04);
        display: flex; gap: 12px; flex-wrap: wrap; align-items: center;
        margin: 24px 0 16px;
    }
    .sd-search { flex: 1; min-width: 220px; position: relative; }
    .sd-search i {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: #9aa3b6;
    }
    .sd-search input {
        width: 100%;
        border: 1px solid rgba(26, 25, 83, 0.1);
        border-radius: 10px;
        padding: 10px 14px 10px 38px;
        font-size: 0.92rem;
    }
    .sd-search input:focus {
        outline: 0; border-color: #d4b06a;
        box-shadow: 0 0 0 4px rgba(212, 176, 106, 0.15);
    }
    .sd-filters { display: flex; gap: 6px; flex-wrap: wrap; }
    .sd-filter {
        background: #fff; border: 1px solid rgba(26, 25, 83, 0.1);
        padding: 8px 14px; border-radius: 10px;
        font-size: 0.84rem; font-weight: 600; color: #6b7280;
        cursor: pointer; transition: all 0.2s;
        display: inline-flex; align-items: center; gap: 8px;
    }
    .sd-filter:hover { color: #1A1953; border-color: rgba(26, 25, 83, 0.25); }
    .sd-filter.active {
        background: linear-gradient(120deg, #1A1953, #3a37a0);
        color: #fff; border-color: transparent;
    }
    .sd-filter .count {
        background: rgba(255,255,255,0.2);
        padding: 1px 8px; border-radius: 999px;
        font-size: 0.72rem; font-weight: 700;
    }
    .sd-filter:not(.active) .count {
        background: rgba(26, 25, 83, 0.08); color: #1A1953;
    }

    .sd-list {
        background: #fff;
        border-radius: 18px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 6px 18px rgba(26, 25, 83, 0.04);
        overflow: hidden;
    }
    .sd-row {
        display: grid;
        grid-template-columns: 60px 1fr auto auto;
        gap: 16px; align-items: center;
        padding: 14px 22px;
        border-bottom: 1px solid rgba(26, 25, 83, 0.05);
    }
    .sd-row:last-child { border-bottom: 0; }
    .sd-row:hover { background: #fafbfd; }
    .sd-avatar {
        width: 48px; height: 48px;
        border-radius: 14px;
        background: linear-gradient(135deg, #1A1953, #3a37a0);
        color: #fff; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.05rem;
    }
    .sd-row.redeemed .sd-avatar {
        background: linear-gradient(135deg, #16a34a, #22c55e);
    }
    .sd-name {
        font-weight: 700; color: #1f2533; font-size: 0.98rem;
    }
    .sd-sub {
        font-size: 0.78rem; color: #6b7280; margin-top: 2px;
        display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
    }
    .sd-sub .guest-tag {
        background: rgba(212, 176, 106, 0.18); color: #b18a3f;
        padding: 1px 7px; border-radius: 6px;
        font-size: 0.7rem; font-weight: 700;
    }
    .sd-seats { display: flex; gap: 5px; flex-wrap: wrap; max-width: 240px; justify-content: flex-end; }
    .sd-seats .seat {
        background: rgba(212, 176, 106, 0.18); color: #b18a3f;
        padding: 3px 9px; border-radius: 7px;
        font-weight: 700; font-size: 0.78rem;
        font-family: 'Courier New', monospace;
    }
    .sd-status { text-align: right; }
    .sd-badge {
        font-size: 0.72rem; font-weight: 700; padding: 5px 11px;
        border-radius: 999px;
        text-transform: uppercase; letter-spacing: 0.04em;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .sd-badge.redeemed { background: rgba(34, 197, 94, 0.12); color: #16a34a; }
    .sd-badge.pending { background: rgba(245, 158, 11, 0.12); color: #d97706; }
    .sd-time { font-size: 0.72rem; color: #8a93a6; font-weight: 600; margin-top: 4px; }

    .sd-empty {
        text-align: center; padding: 60px 24px; color: #8a93a6;
    }
    .sd-empty .ico {
        width: 70px; height: 70px;
        margin: 0 auto 12px;
        background: #f1f3f9; color: #1A1953;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.6rem;
    }

    .sd-divider {
        padding: 10px 22px;
        background: linear-gradient(90deg, rgba(26, 25, 83, 0.04), transparent);
        font-size: 0.72rem; font-weight: 700; color: #6b7280;
        text-transform: uppercase; letter-spacing: 0.06em;
        display: flex; align-items: center; gap: 8px;
    }
    .sd-divider .line { flex: 1; height: 1px; background: rgba(26, 25, 83, 0.08); }

    @media (max-width: 640px) {
        .sd-row {
            grid-template-columns: 48px 1fr;
            grid-template-areas: "av main" "seats seats" "status status";
            row-gap: 8px;
        }
        .sd-avatar { grid-area: av; width: 42px; height: 42px; }
        .sd-info-cell { grid-area: main; }
        .sd-seats { grid-area: seats; max-width: 100%; justify-content: flex-start; }
        .sd-status { grid-area: status; text-align: left; }
    }
</style>
@endpush

@section('content')
@php
    $redeemedList = $attendees->where('status', 'redeemed')->values();
    $pendingList = $attendees->where('status', 'pending')->values();
@endphp

<div class="container-fluid py-4">
    <!-- Hero -->
    <div class="sd-hero mb-4">
        <a href="{{ route('admin.tickets.index') }}" class="sd-back">
            <i class="fas fa-arrow-left"></i> Kembali ke Scan Tiket
        </a>
        <div class="sd-info-grid">
            <div class="sd-poster">
                @if (optional($schedule->film)->cover_url)
                    <img src="{{ $schedule->film->cover_url }}" alt="{{ $schedule->film->title }}">
                @else
                    <div class="placeholder"><i class="fas fa-film"></i></div>
                @endif
            </div>
            <div class="sd-info">
                <div style="font-size:0.78rem; font-weight:700; letter-spacing:0.06em; text-transform:uppercase; opacity:0.78;">
                    Detail Pengunjung
                </div>
                <h1>{{ optional($schedule->film)->title ?? 'Tanpa Film' }}</h1>
                <div class="sd-meta">
                    <span><i class="fas fa-door-open"></i> {{ optional($schedule->studio)->name ?? '-' }}@if (optional(optional($schedule->studio)->type)->name)
                        <span style="opacity:0.7;">· {{ $schedule->studio->type->name }}</span>
                    @endif</span>
                    <span><i class="fas fa-calendar-day"></i> {{ optional($schedule->schedule_date)->translatedFormat('l, d F Y') }}</span>
                    <span><i class="fas fa-clock"></i> {{ optional($schedule->start_time)->format('H:i') }} - {{ optional($schedule->end_time)->format('H:i') }}</span>
                    <span><i class="fas fa-tag"></i> Rp{{ number_format($schedule->ticket_price, 0, ',', '.') }}</span>
                </div>
                <div class="sd-stat-row">
                    <div class="sd-stat-card">
                        <div class="lbl">Total Tiket</div>
                        <div class="val">{{ $stats['total'] }} <small>/{{ $stats['capacity'] }}</small></div>
                    </div>
                    <div class="sd-stat-card">
                        <div class="lbl">Sudah Hadir</div>
                        <div class="val">{{ $stats['redeemed'] }}</div>
                    </div>
                    <div class="sd-stat-card">
                        <div class="lbl">Belum Scan</div>
                        <div class="val">{{ $stats['pending'] }}</div>
                    </div>
                    <div class="sd-stat-card">
                        <div class="lbl">Status Jadwal</div>
                        <div class="val" style="font-size:1rem; line-height:1.3; padding-top:4px;">{{ ucfirst($schedule->status) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="sd-toolbar">
        <div class="sd-search">
            <i class="fas fa-magnifying-glass"></i>
            <input type="text" id="sdSearch" placeholder="Cari nama atau kode kursi…">
        </div>
        <div class="sd-filters">
            <button class="sd-filter active" data-filter="all">
                <i class="fas fa-list"></i> Semua <span class="count">{{ $stats['total'] }}</span>
            </button>
            <button class="sd-filter" data-filter="redeemed">
                <i class="fas fa-check"></i> Sudah Hadir <span class="count">{{ $stats['redeemed'] }}</span>
            </button>
            <button class="sd-filter" data-filter="pending">
                <i class="fas fa-hourglass-half"></i> Belum Scan <span class="count">{{ $stats['pending'] }}</span>
            </button>
        </div>
    </div>

    <!-- List -->
    @if ($attendees->isEmpty())
        <div class="sd-list">
            <div class="sd-empty">
                <div class="ico"><i class="fas fa-users-slash"></i></div>
                <div style="font-weight:700; color:#1f2533; font-size:1.05rem;">Belum ada pengunjung</div>
                <div class="small mt-1">Belum ada tiket terkonfirmasi untuk jadwal ini.</div>
            </div>
        </div>
    @else
        <div class="sd-list" id="sdList">
            @if ($redeemedList->count())
                <div class="sd-divider sd-section" data-section="redeemed">
                    <i class="fas fa-check-circle" style="color:#16a34a;"></i> Sudah Hadir <span class="line"></span> {{ $redeemedList->count() }}
                </div>
                @foreach ($redeemedList as $a)
                    @include('admin.tickets._attendee_row', ['a' => $a])
                @endforeach
            @endif

            @if ($pendingList->count())
                <div class="sd-divider sd-section" data-section="pending">
                    <i class="fas fa-hourglass-half" style="color:#d97706;"></i> Belum Scan <span class="line"></span> {{ $pendingList->count() }}
                </div>
                @foreach ($pendingList as $a)
                    @include('admin.tickets._attendee_row', ['a' => $a])
                @endforeach
            @endif
        </div>
        <div class="sd-empty d-none" id="sdNoResult">
            <div class="ico"><i class="fas fa-magnifying-glass"></i></div>
            <div style="font-weight:700; color:#1f2533;">Tidak ada hasil</div>
            <div class="small mt-1">Coba kata kunci atau filter lain.</div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
(function () {
    const search = document.getElementById('sdSearch');
    const list = document.getElementById('sdList');
    const noResult = document.getElementById('sdNoResult');
    const filters = document.querySelectorAll('.sd-filter');
    if (!list) return;

    let currentFilter = 'all';

    function apply() {
        const q = (search?.value || '').trim().toLowerCase();
        const rows = list.querySelectorAll('.sd-row');
        const sections = list.querySelectorAll('.sd-section');
        let visibleCount = 0;
        const visiblePerSection = { redeemed: 0, pending: 0 };

        rows.forEach(row => {
            const status = row.dataset.status;
            const name = (row.dataset.name || '').toLowerCase();
            const seats = (row.dataset.seats || '').toLowerCase();
            const matchFilter = currentFilter === 'all' || status === currentFilter;
            const matchSearch = !q || name.includes(q) || seats.includes(q);
            const show = matchFilter && matchSearch;
            row.style.display = show ? '' : 'none';
            if (show) {
                visibleCount++;
                visiblePerSection[status] = (visiblePerSection[status] || 0) + 1;
            }
        });

        sections.forEach(sec => {
            const key = sec.dataset.section;
            sec.style.display = (visiblePerSection[key] > 0) ? '' : 'none';
        });

        if (noResult) noResult.classList.toggle('d-none', visibleCount > 0);
    }

    search?.addEventListener('input', apply);
    filters.forEach(btn => {
        btn.addEventListener('click', () => {
            filters.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentFilter = btn.dataset.filter;
            apply();
        });
    });
})();
</script>
@endpush