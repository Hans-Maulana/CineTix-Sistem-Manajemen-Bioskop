@extends('layouts.admin')

@section('title', 'Manajemen Promo')

@push('styles')
<style>
    .pr-hero {
        background: linear-gradient(120deg, #1A1953 0%, #2d2b7a 60%, #3a37a0 100%);
        border-radius: 24px;
        color: #fff;
        padding: 28px 32px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(26, 25, 83, 0.25);
    }
    .pr-hero::after {
        content: ""; position: absolute;
        right: -60px; top: -60px;
        width: 220px; height: 220px;
        background: rgba(212, 176, 106, 0.18);
        border-radius: 50%;
    }
    .pr-hero h1 { font-size: 1.75rem; font-weight: 800; margin-bottom: 4px; }
    .pr-btn-add {
        background: linear-gradient(120deg, #d4b06a, #e7c585);
        color: #1A1953; font-weight: 700;
        border: 0; padding: 11px 20px;
        border-radius: 12px;
        display: inline-flex; align-items: center; gap: 8px;
        transition: all 0.2s;
        box-shadow: 0 8px 20px rgba(212, 176, 106, 0.35);
    }
    .pr-btn-add:hover { transform: translateY(-2px); box-shadow: 0 12px 26px rgba(212, 176, 106, 0.5); color: #1A1953; }

    .pr-stat {
        background: #fff;
        border-radius: 18px;
        padding: 18px 20px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 8px 22px rgba(26, 25, 83, 0.04);
        display: flex; align-items: center; gap: 14px;
        height: 100%;
    }
    .pr-stat-icon {
        width: 46px; height: 46px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem; flex-shrink: 0;
    }
    .pr-stat-label { font-size: 0.7rem; letter-spacing: 0.06em; font-weight: 700; text-transform: uppercase; color: #8a93a6; }
    .pr-stat-value { font-size: 1.45rem; font-weight: 800; color: #1f2533; line-height: 1.1; margin-top: 2px; }

    .pr-toolbar {
        background: #fff;
        border-radius: 18px;
        padding: 18px 22px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 6px 18px rgba(26, 25, 83, 0.04);
    }
    .pr-search { position: relative; flex: 1; min-width: 240px; }
    .pr-search i {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: #9aa3b6;
    }
    .pr-search input {
        width: 100%;
        border: 1px solid rgba(26, 25, 83, 0.1);
        border-radius: 12px;
        padding: 10px 14px 10px 38px;
        font-size: 0.92rem;
    }
    .pr-search input:focus {
        outline: 0; border-color: #d4b06a;
        box-shadow: 0 0 0 4px rgba(212, 176, 106, 0.15);
    }
    .pr-toolbar select {
        border: 1px solid rgba(26, 25, 83, 0.1);
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 0.9rem;
        background: #fff;
        font-weight: 600;
        color: #1f2533;
    }
    .pr-chips { display: flex; flex-wrap: wrap; gap: 6px; }
    .pr-chip {
        background: rgba(26, 25, 83, 0.06);
        border-radius: 999px;
        padding: 4px 10px 4px 14px;
        font-size: 0.78rem; font-weight: 600; color: #1A1953;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .pr-chip a {
        color: #6b7280; font-size: 0.8rem;
        background: rgba(26, 25, 83, 0.08);
        width: 18px; height: 18px;
        border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        text-decoration: none;
        transition: all 0.15s;
    }
    .pr-chip a:hover { background: rgba(214, 59, 59, 0.15); color: #d63b3b; }

    /* Coupon card */
    .pr-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
        gap: 18px;
    }
    .pr-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 6px 18px rgba(26, 25, 83, 0.05);
        position: relative;
        overflow: hidden;
        transition: all 0.25s;
        display: flex; flex-direction: column;
    }
    .pr-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 32px rgba(26, 25, 83, 0.12);
        border-color: rgba(212, 176, 106, 0.4);
    }
    .pr-card.expired { opacity: 0.78; }

    .pr-coupon {
        position: relative;
        background: linear-gradient(135deg, #1A1953 0%, #3a37a0 100%);
        color: #fff;
        padding: 22px 22px 28px;
    }
    .pr-card.upcoming .pr-coupon {
        background: linear-gradient(135deg, #d97706, #f59e0b);
    }
    .pr-card.expired .pr-coupon {
        background: linear-gradient(135deg, #6b7280, #9ca3af);
    }
    .pr-coupon::before, .pr-coupon::after {
        content: "";
        position: absolute;
        bottom: -12px;
        width: 24px; height: 24px;
        background: #f7f8fc;
        border-radius: 50%;
    }
    .pr-coupon::before { left: -12px; }
    .pr-coupon::after { right: -12px; }

    .pr-status-pill {
        position: absolute; top: 14px; right: 14px;
        padding: 4px 10px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(6px);
        font-size: 0.68rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.06em;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .pr-coupon-discount {
        font-size: 2.4rem; font-weight: 800; line-height: 1;
        font-family: 'Inter', sans-serif;
    }
    .pr-coupon-discount small { font-size: 1.2rem; opacity: 0.85; font-weight: 700; }
    .pr-coupon-type {
        font-size: 0.72rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.08em;
        opacity: 0.9; margin-top: 6px;
    }
    .pr-coupon-code {
        margin-top: 14px;
        padding: 8px 12px;
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(6px);
        border: 1px dashed rgba(255, 255, 255, 0.4);
        border-radius: 10px;
        font-family: 'Courier New', monospace;
        font-weight: 800; font-size: 1rem;
        letter-spacing: 0.08em;
        text-align: center;
    }

    .pr-body {
        padding: 18px 20px 16px;
        flex: 1;
        display: flex; flex-direction: column;
    }
    .pr-desc {
        font-size: 0.88rem;
        color: #6b7280;
        line-height: 1.45;
        margin-bottom: 12px;
        min-height: 38px;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .pr-desc.empty { font-style: italic; color: #9aa3b6; }

    .pr-meta-row {
        display: flex; justify-content: space-between; gap: 10px;
        font-size: 0.78rem; color: #6b7280;
        padding-top: 12px;
        border-top: 1px dashed rgba(26, 25, 83, 0.08);
    }
    .pr-meta-row .lbl { color: #8a93a6; font-weight: 600; }
    .pr-meta-row .val { font-weight: 700; color: #1f2533; margin-top: 2px; }

    .pr-progress { margin-top: 12px; }
    .pr-progress .label {
        display: flex; justify-content: space-between;
        font-size: 0.74rem; font-weight: 600; color: #6b7280;
        margin-bottom: 4px;
    }
    .pr-progress .bar {
        height: 6px; background: #f1f3f9;
        border-radius: 999px; overflow: hidden;
    }
    .pr-progress .fill {
        height: 100%;
        background: linear-gradient(90deg, #1A1953, #d4b06a);
        border-radius: 999px;
    }

    .pr-actions {
        display: flex; gap: 6px; padding: 12px 16px;
        border-top: 1px solid rgba(26, 25, 83, 0.05);
        background: #fafbfd;
    }
    .pr-actions a, .pr-actions button {
        flex: 1;
        padding: 8px 10px;
        border-radius: 10px;
        font-size: 0.82rem; font-weight: 700;
        text-align: center; text-decoration: none;
        border: 0;
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        transition: all 0.15s;
    }
    .btn-detail {
        background: rgba(26, 25, 83, 0.06); color: #1A1953;
    }
    .btn-detail:hover { background: rgba(26, 25, 83, 0.12); color: #1A1953; }
    .btn-edit {
        background: rgba(245, 158, 11, 0.12); color: #d97706;
    }
    .btn-edit:hover { background: rgba(245, 158, 11, 0.2); color: #d97706; }
    .btn-del {
        background: rgba(214, 59, 59, 0.1); color: #d63b3b;
    }
    .btn-del:hover { background: rgba(214, 59, 59, 0.2); color: #d63b3b; }

    .pr-empty {
        text-align: center;
        padding: 60px 24px;
        background: #fff;
        border-radius: 20px;
        border: 2px dashed rgba(26, 25, 83, 0.12);
    }
    .pr-empty .ico {
        width: 80px; height: 80px;
        margin: 0 auto 14px;
        background: rgba(26, 25, 83, 0.06); color: #1A1953;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.8rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Hero -->
    <div class="pr-hero mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 position-relative" style="z-index:1;">
            <div>
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2"
                     style="background:rgba(255,255,255,0.12); font-size:0.75rem; font-weight:700; letter-spacing:0.06em; text-transform:uppercase;">
                    <i class="fas fa-tags"></i> Manajemen Promo
                </div>
                <h1>Kode Promo</h1>
                <p class="mb-0" style="opacity:0.85; font-size:0.95rem;">Kelola kupon diskon untuk pelanggan: aktifkan, pantau penggunaan, dan atur batasan.</p>
            </div>
            <a href="{{ route('admin.promos.create') }}" class="pr-btn-add">
                <i class="fas fa-plus"></i> Tambah Promo
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="pr-stat">
                <div class="pr-stat-icon" style="background:rgba(26, 25, 83, 0.1); color:#1A1953;"><i class="fas fa-tag"></i></div>
                <div>
                    <div class="pr-stat-label">Total Promo</div>
                    <div class="pr-stat-value">{{ number_format($stats['total']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="pr-stat">
                <div class="pr-stat-icon" style="background:rgba(34, 197, 94, 0.12); color:#16a34a;"><i class="fas fa-bolt"></i></div>
                <div>
                    <div class="pr-stat-label">Aktif</div>
                    <div class="pr-stat-value">{{ number_format($stats['active']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="pr-stat">
                <div class="pr-stat-icon" style="background:rgba(214, 59, 59, 0.12); color:#d63b3b;"><i class="fas fa-circle-xmark"></i></div>
                <div>
                    <div class="pr-stat-label">Expired</div>
                    <div class="pr-stat-value">{{ number_format($stats['expired']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="pr-stat">
                <div class="pr-stat-icon" style="background:rgba(212, 176, 106, 0.18); color:#b18a3f;"><i class="fas fa-fire"></i></div>
                <div>
                    <div class="pr-stat-label">Total Pemakaian</div>
                    <div class="pr-stat-value">{{ number_format($stats['redemptions']) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toolbar -->
    <form method="GET" action="{{ route('admin.promos.index') }}" id="filterForm" class="pr-toolbar mb-4">
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <div class="pr-search">
                <i class="fas fa-magnifying-glass"></i>
                <input type="text" name="search" placeholder="Cari kode atau deskripsi…" value="{{ request('search') }}">
            </div>
            <select name="status" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>Akan Datang</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
            </select>
            <select name="type" onchange="this.form.submit()">
                <option value="">Semua Tipe</option>
                <option value="percentage" {{ request('type') === 'percentage' ? 'selected' : '' }}>Persentase</option>
                <option value="fixed" {{ request('type') === 'fixed' ? 'selected' : '' }}>Nominal Tetap</option>
            </select>
            <select name="sort" onchange="this.form.submit()">
                <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Terbaru</option>
                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Terlama</option>
                <option value="most_used" {{ request('sort') === 'most_used' ? 'selected' : '' }}>Paling Banyak Dipakai</option>
                <option value="expires_soon" {{ request('sort') === 'expires_soon' ? 'selected' : '' }}>Segera Berakhir</option>
                <option value="code_asc" {{ request('sort') === 'code_asc' ? 'selected' : '' }}>Kode A → Z</option>
            </select>
            <button type="submit" class="btn" style="background:#1A1953; color:#fff; border-radius:12px; padding:10px 18px; font-weight:700;">
                <i class="fas fa-filter me-1"></i> Terapkan
            </button>
            @if (request()->hasAny(['search', 'status', 'type']) || request('sort', 'newest') !== 'newest')
                <a href="{{ route('admin.promos.index') }}" class="btn btn-light border" style="border-radius:12px; padding:10px 16px; font-weight:600;">
                    <i class="fas fa-rotate-left me-1"></i> Reset
                </a>
            @endif
        </div>

        @php
            $activeFilters = [];
            if (request('search')) $activeFilters[] = ['k' => 'search', 'l' => 'Cari: ' . request('search')];
            if (request('status')) $activeFilters[] = ['k' => 'status', 'l' => 'Status: ' . ucfirst(request('status'))];
            if (request('type')) $activeFilters[] = ['k' => 'type', 'l' => 'Tipe: ' . (request('type') === 'percentage' ? 'Persentase' : 'Nominal')];
        @endphp
        @if (count($activeFilters))
            <div class="pr-chips mt-3">
                @foreach ($activeFilters as $f)
                    <span class="pr-chip">
                        {{ $f['l'] }}
                        <a href="{{ route('admin.promos.index', array_merge(request()->except($f['k']), ['page' => null])) }}" title="Hapus filter">
                            <i class="fas fa-xmark"></i>
                        </a>
                    </span>
                @endforeach
            </div>
        @endif
    </form>

    <!-- Grid -->
    @if ($promos->isEmpty())
        <div class="pr-empty">
            <div class="ico"><i class="fas fa-ticket"></i></div>
            <div style="font-weight:700; color:#1f2533; font-size:1.1rem;">Belum ada promo</div>
            <div class="text-muted small mt-1">
                @if (request()->hasAny(['search', 'status', 'type']))
                    Tidak ada promo yang cocok dengan filter. <a href="{{ route('admin.promos.index') }}">Hapus filter</a>.
                @else
                    Klik tombol <strong>Tambah Promo</strong> di kanan atas untuk membuat kode pertama.
                @endif
            </div>
        </div>
    @else
        <div class="pr-grid">
            @foreach ($promos as $promo)
                @php
                    $now = now();
                    $isUpcoming = $promo->valid_from->gt($now);
                    $isExpired = $promo->valid_until->lt($now) || ($promo->max_usage && $promo->usage_count >= $promo->max_usage);
                    $isActive = !$isUpcoming && !$isExpired;
                    $cardClass = $isExpired ? 'expired' : ($isUpcoming ? 'upcoming' : '');

                    $usagePercent = $promo->max_usage
                        ? min(100, ($promo->usage_count / $promo->max_usage) * 100)
                        : null;

                    $daysLeft = $now->diffInDays($promo->valid_until, false);
                @endphp
                <div class="pr-card {{ $cardClass }}">
                    <div class="pr-coupon">
                        @if ($isActive)
                            <span class="pr-status-pill" style="background:rgba(34, 197, 94, 0.25); color:#fff;">
                                <span style="width:6px; height:6px; background:#4ade80; border-radius:50%;"></span> Aktif
                            </span>
                        @elseif ($isUpcoming)
                            <span class="pr-status-pill"><i class="fas fa-clock"></i> Akan Datang</span>
                        @else
                            <span class="pr-status-pill" style="background:rgba(0,0,0,0.25);"><i class="fas fa-circle-xmark"></i> Berakhir</span>
                        @endif

                        <div class="pr-coupon-discount">
                            @if ($promo->discount_type === 'percentage')
                                {{ rtrim(rtrim(number_format($promo->discount_value, 2, '.', ''), '0'), '.') }}<small>%</small>
                            @else
                                <small>Rp</small> {{ number_format($promo->discount_value, 0, ',', '.') }}
                            @endif
                        </div>
                        <div class="pr-coupon-type">
                            {{ $promo->discount_type === 'percentage' ? 'Diskon Persentase' : 'Potongan Tetap' }}
                        </div>
                        <div class="pr-coupon-code">{{ $promo->code }}</div>
                    </div>

                    <div class="pr-body">
                        <div class="pr-desc {{ !$promo->description ? 'empty' : '' }}">
                            {{ $promo->description ?: 'Belum ada deskripsi' }}
                        </div>

                        <div class="mt-auto">
                            <div class="pr-meta-row">
                                <div>
                                    <div class="lbl"><i class="fas fa-calendar-day me-1"></i>Berlaku sampai</div>
                                    <div class="val">{{ $promo->valid_until->translatedFormat('d M Y') }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="lbl"><i class="fas fa-user me-1"></i>Per Customer</div>
                                    <div class="val">{{ $promo->max_usage_per_customer }}x</div>
                                </div>
                            </div>

                            <div class="pr-progress">
                                <div class="label">
                                    <span><i class="fas fa-fire me-1"></i>Pemakaian</span>
                                    <span>{{ $promo->usage_count }} / {{ $promo->max_usage ?? '∞' }}</span>
                                </div>
                                <div class="bar">
                                    <div class="fill" style="width: {{ $usagePercent !== null ? $usagePercent : ($promo->usage_count > 0 ? 100 : 0) }}%"></div>
                                </div>
                            </div>

                            @if ($isActive)
                                <div class="mt-2 small" style="color: {{ $daysLeft <= 7 ? '#d63b3b' : '#16a34a' }}; font-weight:600;">
                                    <i class="fas fa-hourglass-half me-1"></i>
                                    {{ (int) max(0, ceil($daysLeft)) }} hari lagi berakhir
                                </div>
                            @elseif ($isUpcoming)
                                <div class="mt-2 small" style="color:#d97706; font-weight:600;">
                                    <i class="fas fa-clock me-1"></i>
                                    Mulai {{ $promo->valid_from->translatedFormat('d M Y') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="pr-actions">
                        <a href="{{ route('admin.promos.show', $promo) }}" class="btn-detail">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                        <a href="{{ route('admin.promos.edit', $promo) }}" class="btn-edit">
                            <i class="fas fa-pen"></i> Edit
                        </a>
                        <form action="{{ route('admin.promos.destroy', $promo) }}" method="POST" class="m-0 d-flex" style="flex:1;" onsubmit="return confirm('Yakin ingin menghapus promo {{ $promo->code }}? Riwayat penggunaan akan ikut terhapus.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-del" style="width:100%;"><i class="fas fa-trash"></i> Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($promos->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $promos->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
