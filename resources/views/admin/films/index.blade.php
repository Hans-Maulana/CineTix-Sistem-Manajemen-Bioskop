@extends('layouts.admin')

@section('title', 'Manajemen Film')

@push('styles')
<style>
    /* ===== Films Modern UI ===== */
    .fm-hero {
        background: linear-gradient(120deg, #1A1953 0%, #2d2b7a 60%, #3a37a0 100%);
        border-radius: 24px;
        color: #fff;
        padding: 28px 32px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(26, 25, 83, 0.25);
    }
    .fm-hero::after {
        content: "";
        position: absolute;
        right: -60px; top: -60px;
        width: 220px; height: 220px;
        background: rgba(212, 176, 106, 0.18);
        border-radius: 50%;
    }
    .fm-hero::before {
        content: "";
        position: absolute;
        right: 90px; bottom: -80px;
        width: 180px; height: 180px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }
    .fm-hero h1 { font-size: 1.75rem; font-weight: 800; margin-bottom: 4px; }

    /* Stat cards */
    .fm-stat {
        background: #fff;
        border-radius: 18px;
        padding: 18px 20px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 8px 22px rgba(26, 25, 83, 0.04);
        display: flex; align-items: center; gap: 14px;
        height: 100%;
    }
    .fm-stat-icon {
        width: 46px; height: 46px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .fm-stat-label {
        font-size: 0.7rem; letter-spacing: 0.06em;
        font-weight: 700; text-transform: uppercase; color: #8a93a6;
    }
    .fm-stat-value {
        font-size: 1.45rem; font-weight: 800; color: #1f2533; line-height: 1.1; margin-top: 2px;
    }

    /* Toolbar */
    .fm-toolbar {
        background: #fff;
        border: 1px solid rgba(26, 25, 83, 0.06);
        border-radius: 18px;
        padding: 14px 16px;
        box-shadow: 0 8px 22px rgba(26, 25, 83, 0.03);
    }
    .fm-search-wrap { position: relative; flex: 1; }
    .fm-search-wrap .bi-search {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #8a93a6;
    }
    .fm-search-wrap .form-control {
        padding-left: 40px; border-radius: 12px; height: 44px; border: 1px solid #e6e8f0;
    }
    .fm-search-wrap .form-control:focus {
        border-color: #1A1953; box-shadow: 0 0 0 4px rgba(26, 25, 83, 0.08);
    }

    .btn-fm-soft {
        background: #f3f4fa; color: #1A1953;
        border: 1px solid transparent; border-radius: 12px;
        font-weight: 700; padding: 10px 16px;
        transition: all 0.2s ease;
    }
    .btn-fm-soft:hover { background: #e9ebf5; color: #1A1953; }
    .btn-fm-soft.is-active { background: #1A1953; color: #fff; }

    .btn-fm-primary {
        background: #1A1953; color: #fff;
        border: 0; border-radius: 12px;
        font-weight: 700; padding: 10px 18px;
        height: 44px;
        transition: all 0.2s ease;
        box-shadow: 0 6px 18px rgba(26, 25, 83, 0.25);
    }
    .btn-fm-primary:hover { background: #14123e; color: #fff; transform: translateY(-1px); }

    /* Advanced filter */
    .fm-advanced {
        background: #fff;
        border: 1px solid rgba(26, 25, 83, 0.06);
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 8px 22px rgba(26, 25, 83, 0.03);
    }
    .fm-advanced label {
        font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.04em; color: #8a93a6; margin-bottom: 4px;
    }
    .fm-advanced .form-select, .fm-advanced .form-control {
        padding: 10px 14px; font-size: 0.9rem; border-radius: 12px;
        border: 1px solid #e6e8f0; height: 42px;
    }

    /* Active chips */
    .fm-chip {
        display: inline-flex; align-items: center; gap: 6px;
        border: 1px solid rgba(26, 25, 83, 0.12);
        border-radius: 999px; padding: 6px 12px;
        font-size: 0.78rem; font-weight: 600; color: #1A1953;
        background: #fff; text-decoration: none;
    }
    .fm-chip i { font-size: 0.75rem; color: #6c7689; }
    .fm-chip .chip-remove { color: #8a93a6; margin-left: 4px; text-decoration: none; }
    .fm-chip .chip-remove:hover { color: #dc3545; }

    /* Film grid */
    .fm-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 22px;
    }
    .fm-card {
        background: #fff;
        border-radius: 18px;
        overflow: hidden;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 10px 28px rgba(26, 25, 83, 0.05);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        display: flex;
        flex-direction: column;
    }
    .fm-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 40px rgba(26, 25, 83, 0.12);
    }
    .fm-card-poster {
        position: relative;
        aspect-ratio: 16 / 9;
        overflow: hidden;
        background: linear-gradient(135deg, #1A1953, #3a37a0);
    }
    .fm-card-poster img {
        width: 100%; height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .fm-card:hover .fm-card-poster img { transform: scale(1.05); }

    .fm-poster-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.0) 50%);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 12px;
    }
    .fm-status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        align-self: flex-start;
        font-size: 0.66rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 4px 10px;
        border-radius: 999px;
        backdrop-filter: blur(8px);
    }
    .fm-status-now { background: rgba(25, 167, 95, 0.92); color: #fff; }
    .fm-status-coming { background: rgba(212, 176, 106, 0.92); color: #1A1953; }
    .fm-classification {
        position: absolute;
        top: 12px; right: 12px;
        background: rgba(0, 0, 0, 0.55);
        color: #fff;
        backdrop-filter: blur(6px);
        font-size: 0.66rem;
        font-weight: 800;
        padding: 4px 8px;
        border-radius: 6px;
        letter-spacing: 0.04em;
    }
    .fm-rating {
        align-self: flex-start;
        background: rgba(0, 0, 0, 0.65);
        color: #ffd54f;
        font-weight: 700;
        font-size: 0.82rem;
        padding: 4px 10px;
        border-radius: 8px;
        backdrop-filter: blur(6px);
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .fm-card-body {
        padding: 14px 16px 12px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        gap: 6px;
    }
    .fm-card-title {
        font-weight: 800;
        color: #1f2533;
        font-size: 0.98rem;
        line-height: 1.25;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.45em;
    }
    .fm-card-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.78rem;
        color: #8a93a6;
    }
    .fm-card-meta i { margin-right: 3px; }
    .fm-card-genres {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-top: 4px;
        min-height: 22px;
    }
    .fm-genre-tag {
        font-size: 0.66rem;
        font-weight: 700;
        padding: 2px 8px;
        background: rgba(26, 25, 83, 0.07);
        color: #1A1953;
        border-radius: 6px;
    }
    .fm-card-actions {
        margin-top: auto;
        padding-top: 12px;
        border-top: 1px dashed #e6e8f0;
        display: flex;
        gap: 8px;
    }
    .fm-action-btn {
        flex: 1;
        height: 36px;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        border: 1px solid #e6e8f0;
        background: #fff;
        transition: all 0.2s ease;
    }
    .fm-action-btn.edit {
        color: #1A1953;
    }
    .fm-action-btn.edit:hover {
        background: #1A1953; color: #fff; border-color: #1A1953;
    }
    .fm-action-btn.delete {
        color: #dc3545; flex: 0 0 44px;
    }
    .fm-action-btn.delete:hover {
        background: #dc3545; color: #fff; border-color: #dc3545;
    }

    /* Empty */
    .fm-empty {
        background: #fff;
        border-radius: 20px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 10px 30px rgba(26, 25, 83, 0.03);
        padding: 60px 20px;
        text-align: center;
    }
    .fm-empty i { font-size: 3rem; color: #c7cbdc; }
</style>
@endpush

@section('content')

@php
    $classificationLabel = function ($c) {
        return strtoupper((string) $c);
    };
    $statusLabel = function ($s) {
        return match ($s) {
            'now_playing' => 'Sedang Tayang',
            'coming_soon' => 'Akan Tayang',
            default => ucfirst(str_replace('_', ' ', (string) $s)),
        };
    };
@endphp

<!-- HERO -->
<div class="fm-hero mb-4">
    <div class="row align-items-center position-relative" style="z-index:2;">
        <div class="col-md-7">
            <span class="badge bg-light text-dark rounded-pill mb-2 px-3 py-2">
                <i class="bi bi-collection-play me-1"></i> Manajemen Film
            </span>
            <h1 class="fw-bold mb-1">Daftar Film</h1>
            <p class="mb-0 text-white-50">Kelola katalog film yang tayang dan akan tayang di CineTix.</p>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.films.create') }}" class="btn btn-light fw-bold rounded-pill px-4">
                <i class="bi bi-plus-lg"></i> Tambah Film
            </a>
        </div>
    </div>
</div>

<!-- STATS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="fm-stat">
            <div class="fm-stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-film"></i></div>
            <div>
                <div class="fm-stat-label">Total Film</div>
                <div class="fm-stat-value">{{ number_format($stats['total'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="fm-stat">
            <div class="fm-stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-play-circle"></i></div>
            <div>
                <div class="fm-stat-label">Sedang Tayang</div>
                <div class="fm-stat-value">{{ number_format($stats['now_playing'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="fm-stat">
            <div class="fm-stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-calendar-event"></i></div>
            <div>
                <div class="fm-stat-label">Akan Tayang</div>
                <div class="fm-stat-value">{{ number_format($stats['coming_soon'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="fm-stat">
            <div class="fm-stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-tags"></i></div>
            <div>
                <div class="fm-stat-label">Total Genre</div>
                <div class="fm-stat-value">{{ number_format($stats['genres_count'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success border-0 rounded-3 d-flex align-items-center mb-3" role="alert" style="background: rgba(25, 167, 95, 0.1); color: #15a05c;">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    </div>
@endif

<!-- TOOLBAR -->
@php
    $hasAdvanced = request()->hasAny(['genre', 'status', 'classification', 'sort']);
@endphp
<form action="{{ route('admin.films.index') }}" method="GET" id="filmFilterForm" class="mb-3">
    <div class="fm-toolbar mb-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <div class="fm-search-wrap flex-grow-1">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="form-control" placeholder="Cari judul, sutradara, aktor, atau genre..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-fm-primary">
                <i class="bi bi-search me-1"></i> Cari
            </button>
            <button type="button" class="btn btn-fm-soft {{ $hasAdvanced ? 'is-active' : '' }}" data-bs-toggle="collapse" data-bs-target="#advancedFmFilter" aria-expanded="{{ $hasAdvanced ? 'true' : 'false' }}">
                <i class="bi bi-sliders me-1"></i> Filter
                @if($hasAdvanced)
                    <span class="badge bg-light text-dark ms-1">{{ collect(['genre', 'status', 'classification'])->filter(fn ($f) => request()->filled($f))->count() }}</span>
                @endif
            </button>
            @if(request()->hasAny(['search', 'genre', 'status', 'classification', 'sort']))
                <a href="{{ route('admin.films.index') }}" class="btn btn-fm-soft" title="Reset filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            @endif
        </div>
    </div>

    <div class="collapse {{ $hasAdvanced ? 'show' : '' }}" id="advancedFmFilter">
        <div class="fm-advanced mb-3">
            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <label>Status Tayang</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="now_playing" {{ request('status') == 'now_playing' ? 'selected' : '' }}>Sedang Tayang</option>
                        <option value="coming_soon" {{ request('status') == 'coming_soon' ? 'selected' : '' }}>Akan Tayang</option>
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label>Genre</label>
                    <select name="genre" class="form-select">
                        <option value="">Semua Genre</option>
                        @foreach($genres as $g)
                            <option value="{{ $g->id }}" {{ request('genre') == $g->id ? 'selected' : '' }}>{{ $g->genre_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label>Klasifikasi</label>
                    <select name="classification" class="form-select">
                        <option value="">Semua Klasifikasi</option>
                        @foreach($classifications as $c)
                            <option value="{{ $c }}" {{ request('classification') == $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label>Urutkan</label>
                    <select name="sort" class="form-select">
                        <option value="latest"      {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Terbaru Ditambahkan</option>
                        <option value="oldest"      {{ request('sort') == 'oldest'      ? 'selected' : '' }}>Terlama Ditambahkan</option>
                        <option value="title_asc"   {{ request('sort') == 'title_asc'   ? 'selected' : '' }}>Judul (A → Z)</option>
                        <option value="title_desc"  {{ request('sort') == 'title_desc'  ? 'selected' : '' }}>Judul (Z → A)</option>
                        <option value="rating_high" {{ request('sort') == 'rating_high' ? 'selected' : '' }}>Rating Tertinggi</option>
                        <option value="release_new" {{ request('sort') == 'release_new' ? 'selected' : '' }}>Tanggal Rilis Terbaru</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</form>

@if(request()->hasAny(['search', 'genre', 'status', 'classification']))
    @php
        $chipReset = fn ($keys) => route('admin.films.index', request()->except(array_merge((array) $keys, ['page'])));
    @endphp
    <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
        <span class="text-muted small fw-bold me-1">Filter aktif:</span>
        @if(request('search'))
            <span class="fm-chip"><i class="bi bi-search"></i> Cari: <strong>{{ request('search') }}</strong>
                <a href="{{ $chipReset('search') }}" class="chip-remove"><i class="bi bi-x-circle-fill"></i></a>
            </span>
        @endif
        @if(request('status'))
            <span class="fm-chip"><i class="bi bi-play-circle"></i> Status: <strong>{{ $statusLabel(request('status')) }}</strong>
                <a href="{{ $chipReset('status') }}" class="chip-remove"><i class="bi bi-x-circle-fill"></i></a>
            </span>
        @endif
        @if(request('genre'))
            @php $gName = optional($genres->firstWhere('id', request('genre')))->genre_name; @endphp
            <span class="fm-chip"><i class="bi bi-tag"></i> Genre: <strong>{{ $gName }}</strong>
                <a href="{{ $chipReset('genre') }}" class="chip-remove"><i class="bi bi-x-circle-fill"></i></a>
            </span>
        @endif
        @if(request('classification'))
            <span class="fm-chip"><i class="bi bi-shield-check"></i> Klasifikasi: <strong>{{ request('classification') }}</strong>
                <a href="{{ $chipReset('classification') }}" class="chip-remove"><i class="bi bi-x-circle-fill"></i></a>
            </span>
        @endif
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <span class="text-muted small">
        Menampilkan <span class="fw-bold text-dark">{{ $films->count() }}</span> dari <span class="fw-bold text-dark">{{ $films->total() }}</span> film
    </span>
    <small class="text-muted"><i class="bi bi-arrow-down-up me-1"></i>
        @switch(request('sort', 'latest'))
            @case('oldest')      Terlama Ditambahkan @break
            @case('title_asc')   Judul (A → Z) @break
            @case('title_desc')  Judul (Z → A) @break
            @case('rating_high') Rating Tertinggi @break
            @case('release_new') Tanggal Rilis Terbaru @break
            @default Terbaru Ditambahkan
        @endswitch
    </small>
</div>

<!-- FILM GRID -->
@if($films->count() > 0)
    <div class="fm-grid mb-4">
        @foreach($films as $film)
            @php
                $rating = round($film->reviews_avg_rating ?? 0, 1);
            @endphp
            <div class="fm-card">
                <div class="fm-card-poster">
                    <img src="{{ $film->cover_url }}" alt="{{ $film->title }}" loading="lazy">
                    @if($film->classification)
                        <span class="fm-classification">{{ $classificationLabel($film->classification) }}</span>
                    @endif
                    <div class="fm-poster-overlay">
                        @if($film->status)
                            <span class="fm-status-badge {{ $film->status === 'now_playing' ? 'fm-status-now' : 'fm-status-coming' }}">
                                <i class="bi {{ $film->status === 'now_playing' ? 'bi-play-fill' : 'bi-clock' }}"></i>
                                {{ $statusLabel($film->status) }}
                            </span>
                        @endif
                        <span class="fm-rating">
                            <i class="bi bi-star-fill"></i> {{ number_format($rating, 1) }}
                        </span>
                    </div>
                </div>
                <div class="fm-card-body">
                    <div class="fm-card-title" title="{{ $film->title }}">{{ $film->title }}</div>
                    <div class="fm-card-meta">
                        <span><i class="bi bi-clock"></i>{{ $film->duration }} mnt</span>
                        @if($film->schedules_count !== null)
                            <span><i class="bi bi-calendar3"></i>{{ $film->schedules_count }} jadwal</span>
                        @endif
                    </div>
                    @if($film->director)
                        <div class="fm-card-meta">
                            <span class="text-truncate" title="{{ $film->director }}">
                                <i class="bi bi-camera-reels"></i>{{ $film->director }}
                            </span>
                        </div>
                    @endif
                    <div class="fm-card-genres">
                        @foreach($film->genres->take(3) as $genre)
                            <span class="fm-genre-tag">{{ $genre->genre_name }}</span>
                        @endforeach
                        @if($film->genres->count() > 3)
                            <span class="fm-genre-tag">+{{ $film->genres->count() - 3 }}</span>
                        @endif
                    </div>
                    <div class="fm-card-actions">
                        <a href="{{ route('admin.films.edit', $film) }}" class="fm-action-btn edit">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <form action="{{ route('admin.films.destroy', $film) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus film &quot;{{ addslashes($film->title) }}&quot;?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="fm-action-btn delete" title="Hapus film">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($films->hasPages())
        <div class="d-flex justify-content-center">
            {{ $films->links() }}
        </div>
    @endif
@else
    <div class="fm-empty">
        <i class="bi bi-film d-block mb-2"></i>
        <h5 class="fw-bold text-dark mb-1">Tidak ada film ditemukan</h5>
        <p class="text-muted mb-3">
            @if(request()->hasAny(['search', 'genre', 'status', 'classification']))
                Tidak ada film yang cocok dengan filter saat ini.
            @else
                Belum ada film yang ditambahkan ke katalog.
            @endif
        </p>
        @if(request()->hasAny(['search', 'genre', 'status', 'classification']))
            <a href="{{ route('admin.films.index') }}" class="btn btn-fm-soft me-2">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
            </a>
        @endif
        <a href="{{ route('admin.films.create') }}" class="btn btn-fm-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Film
        </a>
    </div>
@endif

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('filmFilterForm');
        if (!form) return;
        form.querySelectorAll('select[name="status"], select[name="genre"], select[name="classification"], select[name="sort"]').forEach(function (el) {
            el.addEventListener('change', function () { form.submit(); });
        });
    });
</script>
@endpush
