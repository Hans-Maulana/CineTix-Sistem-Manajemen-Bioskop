@extends('layouts.admin')

@section('title', 'Jadwal Tayang')

@push('styles')
<style>
    .sc-hero {
        background: linear-gradient(120deg, #1A1953 0%, #2d2b7a 60%, #3a37a0 100%);
        border-radius: 24px;
        color: #fff;
        padding: 28px 32px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(26, 25, 83, 0.25);
    }
    .sc-hero::after {
        content: ""; position: absolute;
        right: -60px; top: -60px;
        width: 220px; height: 220px;
        background: rgba(212, 176, 106, 0.18);
        border-radius: 50%;
    }
    .sc-hero::before {
        content: ""; position: absolute;
        right: 90px; bottom: -80px;
        width: 180px; height: 180px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }
    .sc-hero h1 { font-size: 1.75rem; font-weight: 800; margin-bottom: 4px; }

    .sc-stat {
        background: #fff;
        border-radius: 18px;
        padding: 18px 20px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 8px 22px rgba(26, 25, 83, 0.04);
        display: flex; align-items: center; gap: 14px;
        height: 100%;
        text-decoration: none; color: inherit;
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
    }
    .sc-stat:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 30px rgba(26, 25, 83, 0.10);
        color: inherit;
    }
    .sc-stat.is-active { border-color: #1A1953; box-shadow: 0 14px 30px rgba(26, 25, 83, 0.15); }
    .sc-stat-icon {
        width: 46px; height: 46px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .sc-stat-label { font-size: 0.7rem; letter-spacing: 0.06em; font-weight: 700; text-transform: uppercase; color: #8a93a6; }
    .sc-stat-value { font-size: 1.45rem; font-weight: 800; color: #1f2533; line-height: 1.1; margin-top: 2px; }

    .sc-toolbar {
        background: #fff;
        border: 1px solid rgba(26, 25, 83, 0.06);
        border-radius: 18px;
        padding: 14px 16px;
        box-shadow: 0 8px 22px rgba(26, 25, 83, 0.03);
    }
    .sc-search-wrap { position: relative; flex: 1; }
    .sc-search-wrap .bi-search {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #8a93a6;
    }
    .sc-search-wrap .form-control {
        padding-left: 40px; border-radius: 12px; height: 44px; border: 1px solid #e6e8f0;
    }
    .sc-search-wrap .form-control:focus {
        border-color: #1A1953; box-shadow: 0 0 0 4px rgba(26, 25, 83, 0.08);
    }

    .btn-sc-primary {
        background: #1A1953; color: #fff;
        border: 0; border-radius: 12px;
        font-weight: 700; padding: 10px 18px;
        height: 44px;
        transition: all 0.2s ease;
        box-shadow: 0 6px 18px rgba(26, 25, 83, 0.25);
    }
    .btn-sc-primary:hover { background: #14123e; color: #fff; transform: translateY(-1px); }
    .btn-sc-soft {
        background: #f3f4fa; color: #1A1953;
        border: 1px solid transparent; border-radius: 12px;
        font-weight: 700; padding: 10px 16px;
        transition: all 0.2s ease;
    }
    .btn-sc-soft:hover { background: #e9ebf5; color: #1A1953; }
    .btn-sc-soft.is-active { background: #1A1953; color: #fff; }

    .sc-tab-bar {
        display: flex; flex-wrap: wrap; gap: 8px;
        margin-bottom: 14px;
    }
    .sc-tab {
        padding: 8px 16px;
        border-radius: 999px;
        background: #fff;
        border: 1px solid #e6e8f0;
        color: #6c7689;
        font-weight: 700;
        font-size: 0.85rem;
        text-decoration: none;
        transition: all 0.18s ease;
    }
    .sc-tab:hover { border-color: #1A1953; color: #1A1953; }
    .sc-tab.is-active { background: #1A1953; color: #fff; border-color: #1A1953; }

    .sc-advanced {
        background: #fff;
        border: 1px solid rgba(26, 25, 83, 0.06);
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 8px 22px rgba(26, 25, 83, 0.03);
    }
    .sc-advanced label {
        font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.04em; color: #8a93a6; margin-bottom: 4px;
    }
    .sc-advanced .form-select, .sc-advanced .form-control {
        padding: 10px 14px; font-size: 0.9rem; border-radius: 12px;
        border: 1px solid #e6e8f0; height: 42px;
    }

    /* Card grid */
    .sc-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
    }
    .sc-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 10px 28px rgba(26, 25, 83, 0.04);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
    }
    .sc-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 40px rgba(26, 25, 83, 0.10);
    }

    .sc-card-head {
        display: flex;
        gap: 14px;
        padding: 16px;
        align-items: flex-start;
    }
    .sc-poster {
        width: 70px;
        height: 100px;
        border-radius: 10px;
        object-fit: cover;
        flex-shrink: 0;
        background: #eef0f7;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .sc-poster-fallback {
        width: 70px; height: 100px;
        border-radius: 10px;
        flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg, #1A1953, #3a37a0);
        color: rgba(255,255,255,0.7);
        font-size: 1.4rem;
    }
    .sc-card-info { flex: 1; min-width: 0; }
    .sc-film-title {
        font-size: 0.98rem; font-weight: 800; color: #1f2533;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: 4px;
    }
    .sc-film-meta {
        font-size: 0.78rem; color: #8a93a6;
        display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
    }

    .sc-status-pill {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 0.66rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.04em;
        padding: 3px 9px; border-radius: 999px;
        white-space: nowrap;
    }
    .sc-status-pill::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .pill-onschedule { background: rgba(13, 110, 253, 0.12); color: #0d6efd; }
    .pill-nowplaying { background: rgba(25, 167, 95, 0.14);  color: #15a05c; }
    .pill-complete   { background: rgba(108, 117, 125, 0.14); color: #6c7689; }
    .pill-canceled   { background: rgba(220, 53, 69, 0.12);  color: #dc3545; }

    .sc-time-block {
        background: linear-gradient(135deg, #fafbff, #f3f4fa);
        margin: 0 16px;
        padding: 14px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        border: 1px dashed #d8dce6;
    }
    .sc-time-side {
        text-align: center;
        flex: 1;
    }
    .sc-time-side .time-label { font-size: 0.62rem; color: #8a93a6; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; }
    .sc-time-side .time-value {
        font-size: 1.3rem; font-weight: 800; color: #1A1953; line-height: 1;
        margin-top: 2px;
        font-feature-settings: 'tnum';
    }
    .sc-time-arrow {
        color: #8a93a6;
        font-size: 1rem;
    }

    .sc-card-body {
        padding: 14px 16px;
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }
    .sc-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.74rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 8px;
    }
    .sc-tag-date { background: rgba(26, 25, 83, 0.07); color: #1A1953; }
    .sc-tag-studio { background: rgba(212, 176, 106, 0.18); color: #8a6a25; }
    .sc-tag-price { background: rgba(25, 167, 95, 0.12); color: #15a05c; margin-left: auto; }

    /* Stats block */
    .sc-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin: 0 16px;
        padding: 12px;
        background: #fafbff;
        border-radius: 12px;
        border: 1px solid #eef0f7;
    }
    .sc-stats.is-two { grid-template-columns: repeat(2, 1fr); }
    .sc-stats-item { text-align: center; }
    .sc-stats-item .si-label {
        font-size: 0.6rem;
        font-weight: 700;
        color: #8a93a6;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        display: block;
    }
    .sc-stats-item .si-value {
        font-size: 0.95rem;
        font-weight: 800;
        color: #1f2533;
        line-height: 1.1;
        margin-top: 2px;
        font-feature-settings: 'tnum';
    }
    .sc-stats-item .si-value.muted { color: #8a93a6; }
    .sc-stats-item .si-value.ok    { color: #15a05c; }
    .sc-stats-item .si-value.warn  { color: #b58806; }

    .sc-occupancy {
        margin: 8px 16px 0;
        font-size: 0.7rem;
        color: #6c7689;
    }
    .sc-occupancy .label-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 4px;
    }
    .sc-occupancy .label-row strong { color: #1A1953; font-weight: 800; }
    .sc-occ-bar {
        height: 6px;
        background: #eef0f7;
        border-radius: 999px;
        overflow: hidden;
    }
    .sc-occ-fill {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #1A1953, #3a37a0);
        transition: width 0.4s ease;
    }
    .sc-occ-fill.ok    { background: linear-gradient(90deg, #15a05c, #2dbf76); }
    .sc-occ-fill.warn  { background: linear-gradient(90deg, #d4b06a, #e0c489); }
    .sc-occ-fill.full  { background: linear-gradient(90deg, #dc3545, #f06272); }

    .sc-card-actions {
        display: flex;
        gap: 8px;
        padding: 12px 16px 16px;
        border-top: 1px dashed #e6e8f0;
        margin-top: 12px;
    }
    .sc-action-btn {
        flex: 1;
        height: 36px;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        border: 1px solid #e6e8f0;
        background: #fff;
        color: #1A1953;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .sc-action-btn:hover { background: #1A1953; color: #fff; border-color: #1A1953; }
    .sc-action-btn.delete { color: #dc3545; flex: 0 0 44px; }
    .sc-action-btn.delete:hover { background: #dc3545; color: #fff; border-color: #dc3545; }

    .sc-empty {
        background: #fff;
        border-radius: 20px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 10px 30px rgba(26, 25, 83, 0.03);
        padding: 60px 20px;
        text-align: center;
    }
    .sc-empty i { font-size: 3rem; color: #c7cbdc; }

    .sc-chip {
        display: inline-flex; align-items: center; gap: 6px;
        border: 1px solid rgba(26, 25, 83, 0.12);
        border-radius: 999px; padding: 6px 12px;
        font-size: 0.78rem; font-weight: 600; color: #1A1953;
        background: #fff; text-decoration: none;
    }
    .sc-chip i { font-size: 0.75rem; color: #6c7689; }
    .sc-chip .chip-remove { color: #8a93a6; margin-left: 4px; text-decoration: none; }
    .sc-chip .chip-remove:hover { color: #dc3545; }
</style>
@endpush

@section('content')

@php
    $statusMeta = [
        'on schedule' => ['label' => 'On Schedule', 'class' => 'onschedule', 'icon' => 'bi-calendar-check'],
        'now playing' => ['label' => 'Now Playing', 'class' => 'nowplaying', 'icon' => 'bi-play-circle-fill'],
        'complete'    => ['label' => 'Selesai',     'class' => 'complete',   'icon' => 'bi-check2-all'],
        'canceled'    => ['label' => 'Dibatalkan',  'class' => 'canceled',   'icon' => 'bi-x-octagon'],
    ];
    $activeRange = request('range', '');
    $tabs = [
        ''         => ['label' => 'Semua',       'icon' => 'bi-collection'],
        'today'    => ['label' => 'Hari Ini',    'icon' => 'bi-calendar-day'],
        'upcoming' => ['label' => 'Akan Tayang', 'icon' => 'bi-calendar-event'],
        'this_week'=> ['label' => 'Minggu Ini',  'icon' => 'bi-calendar-week'],
        'past'     => ['label' => 'Selesai',     'icon' => 'bi-archive'],
    ];
@endphp

<!-- HERO -->
<div class="sc-hero mb-4">
    <div class="row align-items-center position-relative" style="z-index:2;">
        <div class="col-md-7">
            <span class="badge bg-light text-dark rounded-pill mb-2 px-3 py-2">
                <i class="bi bi-calendar3 me-1"></i> Manajemen Jadwal
            </span>
            <h1 class="fw-bold mb-1">Jadwal Tayang</h1>
            <p class="mb-0 text-white-50">Atur jadwal penayangan film di setiap studio.</p>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.schedules.create') }}" class="btn btn-light fw-bold rounded-pill px-4">
                <i class="bi bi-calendar-plus"></i> Tambah Jadwal
            </a>
        </div>
    </div>
</div>

<!-- STATS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <a href="{{ route('admin.schedules.index') }}" class="sc-stat {{ !request()->hasAny(['range', 'status', 'film_id', 'studio_id']) ? 'is-active' : '' }}">
            <div class="sc-stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-calendar3"></i></div>
            <div>
                <div class="sc-stat-label">Total Jadwal</div>
                <div class="sc-stat-value">{{ number_format($stats['total'], 0, ',', '.') }}</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('admin.schedules.index', ['range' => 'today']) }}" class="sc-stat {{ request('range') === 'today' ? 'is-active' : '' }}">
            <div class="sc-stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-calendar-day"></i></div>
            <div>
                <div class="sc-stat-label">Hari Ini</div>
                <div class="sc-stat-value">{{ number_format($stats['today'], 0, ',', '.') }}</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('admin.schedules.index', ['range' => 'upcoming']) }}" class="sc-stat {{ request('range') === 'upcoming' ? 'is-active' : '' }}">
            <div class="sc-stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-calendar-event"></i></div>
            <div>
                <div class="sc-stat-label">Akan Tayang</div>
                <div class="sc-stat-value">{{ number_format($stats['upcoming'], 0, ',', '.') }}</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <div class="sc-stat" style="cursor: default;">
            <div class="sc-stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-cash-stack"></i></div>
            <div>
                <div class="sc-stat-label">Pendapatan Aktual</div>
                <div class="sc-stat-value">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success border-0 rounded-3 d-flex align-items-center mb-3" role="alert" style="background: rgba(25, 167, 95, 0.1); color: #15a05c;">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger border-0 rounded-3 d-flex align-items-center mb-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
    </div>
@endif

<!-- TAB BAR (range filter) -->
<div class="sc-tab-bar">
    @foreach($tabs as $key => $tab)
        @php
            $url = route('admin.schedules.index', array_merge(request()->except(['range', 'page']), $key ? ['range' => $key] : []));
            $isActive = $activeRange === $key;
        @endphp
        <a href="{{ $url }}" class="sc-tab {{ $isActive ? 'is-active' : '' }}">
            <i class="bi {{ $tab['icon'] }} me-1"></i> {{ $tab['label'] }}
        </a>
    @endforeach
</div>

<!-- TOOLBAR -->
@php
    $hasAdvanced = request()->hasAny(['film_id', 'studio_id', 'status', 'date_from', 'date_to', 'sort']);
@endphp
<form action="{{ route('admin.schedules.index') }}" method="GET" id="scFilterForm" class="mb-3">
    @if(request('range'))
        <input type="hidden" name="range" value="{{ request('range') }}">
    @endif

    <div class="sc-toolbar mb-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <div class="sc-search-wrap flex-grow-1">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="form-control" placeholder="Cari judul film atau nama studio..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-sc-primary">
                <i class="bi bi-search me-1"></i> Cari
            </button>
            <button type="button" class="btn btn-sc-soft {{ $hasAdvanced ? 'is-active' : '' }}" data-bs-toggle="collapse" data-bs-target="#advancedScFilter" aria-expanded="{{ $hasAdvanced ? 'true' : 'false' }}">
                <i class="bi bi-sliders me-1"></i> Filter
                @if($hasAdvanced)
                    <span class="badge bg-light text-dark ms-1">{{ collect(['film_id', 'studio_id', 'status', 'date_from', 'date_to'])->filter(fn ($f) => request()->filled($f))->count() }}</span>
                @endif
            </button>
            @if(request()->hasAny(['search', 'range', 'film_id', 'studio_id', 'status', 'date_from', 'date_to', 'sort']))
                <a href="{{ route('admin.schedules.index') }}" class="btn btn-sc-soft" title="Reset filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            @endif
        </div>
    </div>

    <div class="collapse {{ $hasAdvanced ? 'show' : '' }}" id="advancedScFilter">
        <div class="sc-advanced mb-3">
            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <label>Film</label>
                    <select name="film_id" class="form-select">
                        <option value="">Semua Film</option>
                        @foreach($films as $f)
                            <option value="{{ $f->id }}" {{ request('film_id') == $f->id ? 'selected' : '' }}>{{ $f->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label>Studio</label>
                    <select name="studio_id" class="form-select">
                        <option value="">Semua Studio</option>
                        @foreach($studios as $s)
                            <option value="{{ $s->id }}" {{ request('studio_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label>Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach($statusMeta as $k => $meta)
                            <option value="{{ $k }}" {{ request('status') == $k ? 'selected' : '' }}>{{ $meta['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label>Urutkan</label>
                    <select name="sort" class="form-select">
                        <option value="date_desc"  {{ request('sort', 'date_desc') == 'date_desc' ? 'selected' : '' }}>Tanggal Terbaru (Paling Baru)</option>
                        <option value="date_asc"   {{ request('sort') == 'date_asc'   ? 'selected' : '' }}>Tanggal Lama</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                        <option value="price_low"  {{ request('sort') == 'price_low'  ? 'selected' : '' }}>Harga Terendah</option>
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label>Dari Tanggal</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3 col-6">
                    <label>Sampai Tanggal</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
            </div>
        </div>
    </div>
</form>

@if(request()->hasAny(['search', 'film_id', 'studio_id', 'status', 'date_from', 'date_to']))
    @php
        $chipReset = fn ($keys) => route('admin.schedules.index', request()->except(array_merge((array) $keys, ['page'])));
    @endphp
    <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
        <span class="text-muted small fw-bold me-1">Filter aktif:</span>
        @if(request('search'))
            <span class="sc-chip"><i class="bi bi-search"></i> Cari: <strong>{{ request('search') }}</strong>
                <a href="{{ $chipReset('search') }}" class="chip-remove"><i class="bi bi-x-circle-fill"></i></a>
            </span>
        @endif
        @if(request('film_id'))
            @php $fName = optional($films->firstWhere('id', request('film_id')))->title; @endphp
            <span class="sc-chip"><i class="bi bi-film"></i> Film: <strong>{{ $fName }}</strong>
                <a href="{{ $chipReset('film_id') }}" class="chip-remove"><i class="bi bi-x-circle-fill"></i></a>
            </span>
        @endif
        @if(request('studio_id'))
            @php $sName = optional($studios->firstWhere('id', request('studio_id')))->name; @endphp
            <span class="sc-chip"><i class="bi bi-building"></i> Studio: <strong>{{ $sName }}</strong>
                <a href="{{ $chipReset('studio_id') }}" class="chip-remove"><i class="bi bi-x-circle-fill"></i></a>
            </span>
        @endif
        @if(request('status'))
            <span class="sc-chip"><i class="bi bi-flag"></i> Status: <strong>{{ $statusMeta[request('status')]['label'] ?? request('status') }}</strong>
                <a href="{{ $chipReset('status') }}" class="chip-remove"><i class="bi bi-x-circle-fill"></i></a>
            </span>
        @endif
        @if(request('date_from') || request('date_to'))
            <span class="sc-chip">
                <i class="bi bi-calendar3"></i>
                Tanggal: <strong>{{ request('date_from') ?: '...' }} → {{ request('date_to') ?: '...' }}</strong>
                <a href="{{ $chipReset(['date_from', 'date_to']) }}" class="chip-remove"><i class="bi bi-x-circle-fill"></i></a>
            </span>
        @endif
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <span class="text-muted small">
        Menampilkan <span class="fw-bold text-dark">{{ $schedules->count() }}</span> dari <span class="fw-bold text-dark">{{ $schedules->total() }}</span> jadwal
    </span>
    <small class="text-muted"><i class="bi bi-arrow-down-up me-1"></i>
        @switch(request('sort', 'date_desc'))
            @case('date_desc')  Tanggal Terbaru @break
            @case('date_asc')   Tanggal Lama @break
            @case('price_high') Harga Tertinggi @break
            @case('price_low')  Harga Terendah @break
            @default Tanggal Terbaru
        @endswitch
    </small>
</div>

<!-- GRID -->
@if($schedules->count() > 0)
    <div class="sc-grid mb-4">
        @foreach($schedules as $schedule)
            @php
                $sm = $statusMeta[$schedule->status] ?? ['label' => ucfirst($schedule->status), 'class' => 'complete', 'icon' => 'bi-flag'];
            @endphp
            <div class="sc-card">
                <div class="sc-card-head">
                    @if($schedule->film?->cover_url)
                        <img src="{{ $schedule->film->cover_url }}" alt="{{ $schedule->film->title }}" class="sc-poster">
                    @else
                        <div class="sc-poster-fallback"><i class="bi bi-film"></i></div>
                    @endif
                    <div class="sc-card-info">
                        <div class="sc-film-title" title="{{ $schedule->film->title ?? 'N/A' }}">{{ $schedule->film->title ?? 'N/A' }}</div>
                        <div class="sc-film-meta">
                            <span><i class="bi bi-clock me-1"></i>{{ $schedule->film->duration ?? 0 }} mnt</span>
                            <span class="sc-status-pill pill-{{ $sm['class'] }}">{{ $sm['label'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="sc-time-block">
                    <div class="sc-time-side">
                        <div class="time-label">Mulai</div>
                        <div class="time-value">{{ $schedule->start_time?->format('H:i') ?? '--:--' }}</div>
                    </div>
                    <div class="sc-time-arrow"><i class="bi bi-arrow-right"></i></div>
                    <div class="sc-time-side">
                        <div class="time-label">Selesai</div>
                        <div class="time-value">{{ $schedule->end_time?->format('H:i') ?? '--:--' }}</div>
                    </div>
                </div>

                <div class="sc-card-body">
                    <span class="sc-tag sc-tag-date">
                        <i class="bi bi-calendar3"></i>{{ $schedule->schedule_date->translatedFormat('d M Y') }}
                    </span>
                    <span class="sc-tag sc-tag-studio">
                        <i class="bi bi-building"></i>{{ $schedule->studio->name ?? '-' }}
                        @if($schedule->studio?->type?->name) · {{ $schedule->studio->type->name }} @endif
                    </span>
                    <span class="sc-tag sc-tag-price">
                        <i class="bi bi-cash"></i>Rp {{ number_format($schedule->ticket_price, 0, ',', '.') }}
                    </span>
                </div>

                @php
                    $sold       = (int) ($schedule->tickets_sold ?? 0);
                    $redeemed   = (int) ($schedule->tickets_redeemed ?? 0);
                    $revenue    = (float) ($schedule->revenue ?? 0);
                    $capacity   = (int) ($schedule->studio?->capacity ?? 0);
                    $potential  = $capacity * (int) $schedule->ticket_price;
                    $occupancy  = $capacity > 0 ? min(100, round(($sold / $capacity) * 100)) : 0;
                    $attendRate = $sold > 0 ? round(($redeemed / $sold) * 100) : 0;

                    // Tampilkan "Hadir" hanya kalau jadwal sudah/sedang jalan
                    $showAttendance = in_array($schedule->status, ['now playing', 'complete', 'canceled'], true);

                    $occClass = $occupancy >= 90 ? 'full' : ($occupancy >= 60 ? 'ok' : ($occupancy >= 30 ? 'warn' : ''));
                @endphp

                <div class="sc-stats {{ $showAttendance ? '' : 'is-two' }}">
                    <div class="sc-stats-item">
                        <span class="si-label"><i class="bi bi-ticket-detailed me-1"></i>Terjual</span>
                        <span class="si-value">
                            {{ number_format($sold, 0, ',', '.') }}
                            @if($capacity > 0)
                                <span class="text-muted" style="font-size:0.72rem;font-weight:600;">/ {{ $capacity }}</span>
                            @endif
                        </span>
                    </div>

                    @if($showAttendance)
                        <div class="sc-stats-item">
                            <span class="si-label"><i class="bi bi-person-check me-1"></i>Hadir</span>
                            <span class="si-value {{ $attendRate >= 70 ? 'ok' : ($attendRate >= 40 ? 'warn' : 'muted') }}">
                                {{ number_format($redeemed, 0, ',', '.') }}
                                @if($sold > 0)
                                    <span class="text-muted" style="font-size:0.72rem;font-weight:600;">({{ $attendRate }}%)</span>
                                @endif
                            </span>
                        </div>
                    @endif

                    <div class="sc-stats-item">
                        <span class="si-label">
                            <i class="bi bi-cash-coin me-1"></i>{{ $showAttendance ? 'Pendapatan' : 'Estimasi' }}
                        </span>
                        <span class="si-value ok">
                            Rp {{ number_format($showAttendance ? $revenue : $potential, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                @if($capacity > 0)
                    <div class="sc-occupancy">
                        <div class="label-row">
                            <span><i class="bi bi-grid-3x3-gap-fill me-1"></i>Okupansi</span>
                            <span><strong>{{ $occupancy }}%</strong> · sisa {{ max(0, $capacity - $sold) }} kursi</span>
                        </div>
                        <div class="sc-occ-bar">
                            <div class="sc-occ-fill {{ $occClass }}" style="width: {{ $occupancy }}%;"></div>
                        </div>
                    </div>
                @endif

                @php
                    $isFinished = $schedule->status === 'complete';
                    $hasAnyBookings = ($schedule->ticket_bookings_count ?? 0) > 0;
                    $canDelete = $schedule->status === 'on schedule' && !$hasAnyBookings;

                    if (!$canDelete) {
                        if ($isFinished) {
                            $deleteReason = 'Jadwal sudah selesai';
                        } elseif ($schedule->status === 'now playing') {
                            $deleteReason = 'Jadwal sedang tayang';
                        } elseif ($schedule->status === 'canceled') {
                            $deleteReason = 'Jadwal dibatalkan';
                        } elseif ($hasAnyBookings) {
                            $deleteReason = 'Sudah ada transaksi tiket';
                        } else {
                            $deleteReason = 'Tidak dapat dihapus';
                        }
                    }
                @endphp
                <div class="sc-card-actions">
                    @if(!$isFinished)
                        <a href="{{ route('admin.schedules.edit', $schedule) }}" class="sc-action-btn">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                    @else
                        <span class="sc-action-btn" style="cursor: default; color: #8a93a6; background: #fafbff;" title="Jadwal yang sudah selesai tidak bisa di-edit">
                            <i class="bi bi-lock-fill"></i> Terkunci
                        </span>
                    @endif

                    @if($canDelete)
                        <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="sc-action-btn delete" title="Hapus jadwal">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    @else
                        <span class="sc-action-btn delete" style="opacity: 0.45; cursor: not-allowed; background: #fafbff;" title="{{ $deleteReason }} — tidak dapat dihapus">
                            <i class="bi bi-trash"></i>
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if($schedules->hasPages())
        <div class="d-flex justify-content-center">
            {{ $schedules->links() }}
        </div>
    @endif
@else
    <div class="sc-empty">
        <i class="bi bi-calendar-x d-block mb-2"></i>
        <h5 class="fw-bold text-dark mb-1">Tidak ada jadwal</h5>
        <p class="text-muted mb-3">
            @if(request()->hasAny(['search', 'film_id', 'studio_id', 'status', 'date_from', 'date_to', 'range']))
                Tidak ada jadwal yang cocok dengan filter saat ini.
            @else
                Belum ada jadwal tayang yang ditambahkan.
            @endif
        </p>
        @if(request()->hasAny(['search', 'film_id', 'studio_id', 'status', 'date_from', 'date_to', 'range']))
            <a href="{{ route('admin.schedules.index') }}" class="btn btn-sc-soft me-2">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
            </a>
        @endif
        <a href="{{ route('admin.schedules.create') }}" class="btn btn-sc-primary">
            <i class="bi bi-calendar-plus me-1"></i> Tambah Jadwal
        </a>
    </div>
@endif

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('scFilterForm');
        if (!form) return;
        form.querySelectorAll('select[name="film_id"], select[name="studio_id"], select[name="status"], select[name="sort"]').forEach(function (el) {
            el.addEventListener('change', function () { form.submit(); });
        });
    });
</script>
@endpush
