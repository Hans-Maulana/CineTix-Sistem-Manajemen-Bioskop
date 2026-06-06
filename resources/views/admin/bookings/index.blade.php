@extends('layouts.admin')

@section('title', 'Manajemen Booking')

@push('styles')
<style>
    /* ===== Bookings Modern UI ===== */
    .bk-hero {
        background: linear-gradient(120deg, #1A1953 0%, #2d2b7a 60%, #3a37a0 100%);
        border-radius: 24px;
        color: #fff;
        padding: 28px 32px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(26, 25, 83, 0.25);
    }
    .bk-hero::after {
        content: "";
        position: absolute;
        right: -60px; top: -60px;
        width: 220px; height: 220px;
        background: rgba(212, 176, 106, 0.18);
        border-radius: 50%;
    }
    .bk-hero::before {
        content: "";
        position: absolute;
        right: 90px; bottom: -80px;
        width: 180px; height: 180px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }
    .bk-hero h1 { font-size: 1.75rem; font-weight: 800; margin-bottom: 4px; }

    /* Quick stat cards (clickable as status filter) */
    .bk-stat {
        background: #fff;
        border-radius: 18px;
        padding: 18px 20px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 8px 22px rgba(26, 25, 83, 0.04);
        display: flex;
        align-items: center;
        gap: 14px;
        height: 100%;
        text-decoration: none;
        color: inherit;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        cursor: pointer;
    }
    .bk-stat:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 30px rgba(26, 25, 83, 0.10);
        color: inherit;
    }
    .bk-stat.is-active {
        border-color: #1A1953;
        box-shadow: 0 14px 30px rgba(26, 25, 83, 0.15);
    }
    .bk-stat-icon {
        width: 46px; height: 46px;
        border-radius: 14px;
        flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
    }
    .bk-stat-label {
        font-size: 0.7rem;
        letter-spacing: 0.06em;
        font-weight: 700;
        text-transform: uppercase;
        color: #8a93a6;
    }
    .bk-stat-value {
        font-size: 1.45rem;
        font-weight: 800;
        color: #1f2533;
        line-height: 1.1;
        margin-top: 2px;
    }

    /* Toolbar (search + advanced toggle + reset) */
    .bk-toolbar {
        background: #fff;
        border: 1px solid rgba(26, 25, 83, 0.06);
        border-radius: 18px;
        padding: 14px 16px;
        box-shadow: 0 8px 22px rgba(26, 25, 83, 0.03);
    }
    .bk-search-wrap {
        position: relative;
        flex: 1;
    }
    .bk-search-wrap .bi-search {
        position: absolute;
        left: 14px; top: 50%;
        transform: translateY(-50%);
        color: #8a93a6;
    }
    .bk-search-wrap .form-control {
        padding-left: 40px;
        border-radius: 12px;
        height: 44px;
        border: 1px solid #e6e8f0;
    }
    .bk-search-wrap .form-control:focus {
        border-color: #1A1953;
        box-shadow: 0 0 0 4px rgba(26, 25, 83, 0.08);
    }
    .btn-bk-soft {
        background: #f3f4fa;
        color: #1A1953;
        border: 1px solid transparent;
        border-radius: 12px;
        font-weight: 700;
        padding: 10px 16px;
        transition: all 0.2s ease;
    }
    .btn-bk-soft:hover {
        background: #e9ebf5;
        color: #1A1953;
    }
    .btn-bk-soft.is-active {
        background: #1A1953;
        color: #fff;
    }

    /* Advanced filter panel (collapsible) */
    .bk-advanced {
        background: #fff;
        border: 1px solid rgba(26, 25, 83, 0.06);
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 8px 22px rgba(26, 25, 83, 0.03);
    }
    .bk-advanced label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #8a93a6;
        margin-bottom: 4px;
    }
    .bk-advanced .form-select,
    .bk-advanced .form-control {
        padding: 10px 14px;
        font-size: 0.9rem;
        border-radius: 12px;
        border: 1px solid #e6e8f0;
        height: 42px;
    }

    /* Active filter chips */
    .active-filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid rgba(26, 25, 83, 0.12);
        border-radius: 999px;
        padding: 6px 12px;
        font-size: 0.78rem;
        font-weight: 600;
        color: #1A1953;
        background: #fff;
        text-decoration: none;
    }
    .active-filter-chip i { font-size: 0.75rem; color: #6c7689; }
    .active-filter-chip .chip-remove {
        color: #8a93a6;
        margin-left: 4px;
        text-decoration: none;
    }
    .active-filter-chip .chip-remove:hover { color: #dc3545; }

    /* Booking list (card-style table) */
    .bk-list {
        background: #fff;
        border-radius: 20px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 10px 30px rgba(26, 25, 83, 0.03);
        overflow: hidden;
    }
    .bk-list-header {
        display: grid;
        grid-template-columns: 80px 1.8fr 1.5fr 1fr 0.9fr 1.1fr 90px;
        gap: 12px;
        padding: 14px 22px;
        background: #fafbff;
        border-bottom: 1px solid #eef0f7;
        font-size: 0.7rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #8a93a6;
        font-weight: 700;
    }
    .bk-row {
        display: grid;
        grid-template-columns: 80px 1.8fr 1.5fr 1fr 0.9fr 1.1fr 90px;
        gap: 12px;
        align-items: center;
        padding: 16px 22px;
        border-bottom: 1px solid #f1f3fa;
        transition: background 0.18s ease;
    }
    .bk-row:last-child { border-bottom: 0; }
    .bk-row:hover { background: #faf9ff; }

    .bk-id {
        font-weight: 800;
        color: #1A1953;
        font-size: 0.95rem;
    }
    .bk-id-meta { font-size: 0.72rem; color: #8a93a6; font-weight: 500; }

    .bk-customer {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }
    .bk-avatar {
        width: 40px; height: 40px;
        border-radius: 50%;
        flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800;
        font-size: 0.85rem;
        color: #fff;
        text-transform: uppercase;
    }
    .bk-customer-name {
        font-weight: 700;
        color: #1f2533;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 0.92rem;
    }
    .bk-customer-meta {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        margin-top: 4px;
    }
    .bk-tag {
        display: inline-block;
        font-size: 0.62rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 1px 7px;
        border-radius: 6px;
    }
    .bk-tag-member { background: rgba(26, 25, 83, 0.08); color: #1A1953; }
    .bk-tag-guest  { background: rgba(212, 176, 106, 0.18); color: #8a6a25; }

    .bk-film {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }
    .bk-poster {
        width: 40px;
        height: 56px;
        border-radius: 8px;
        object-fit: cover;
        flex-shrink: 0;
        background: #eef0f7;
    }
    .bk-poster-fallback {
        width: 40px;
        height: 56px;
        border-radius: 8px;
        flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg, #1A1953, #3a37a0);
        color: rgba(255,255,255,0.7);
        font-size: 1.1rem;
    }
    .bk-film-title {
        font-weight: 700;
        color: #1f2533;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .bk-film-meta { font-size: 0.74rem; color: #8a93a6; }

    .bk-amount {
        font-weight: 800;
        color: #1A1953;
        font-size: 1rem;
    }
    .bk-amount-method { font-size: 0.74rem; color: #8a93a6; margin-top: 2px; }

    .bk-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.74rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 999px;
    }
    .bk-status-pill::before {
        content: "";
        width: 6px; height: 6px;
        border-radius: 50%;
        background: currentColor;
    }
    .pill-success { background: rgba(25, 167, 95, 0.12); color: #15a05c; }
    .pill-pending { background: rgba(255, 193, 7, 0.16);  color: #b58806; }
    .pill-failed  { background: rgba(220, 53, 69, 0.12);  color: #dc3545; }

    .bk-time-main { font-size: 0.86rem; color: #1f2533; font-weight: 600; }
    .bk-time-sub  { font-size: 0.72rem; color: #8a93a6; }

    .btn-bk-detail {
        background: #fff;
        border: 1px solid #e6e8f0;
        color: #1A1953;
        padding: 8px 14px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.82rem;
        transition: all 0.2s ease;
    }
    .btn-bk-detail:hover {
        background: #1A1953;
        color: #fff;
        border-color: #1A1953;
    }

    .bk-empty {
        padding: 60px 20px;
        text-align: center;
        color: #8a93a6;
    }
    .bk-empty i { font-size: 3rem; color: #c7cbdc; }

    /* Modal restyle */
    .modal-cinema .modal-content {
        border: 0;
        border-radius: 22px;
        overflow: hidden;
    }
    .modal-cinema .modal-header {
        background: linear-gradient(120deg, #1A1953, #3a37a0);
        color: #fff;
        padding: 22px 26px;
        border-bottom: 0;
        position: relative;
    }
    .modal-cinema .modal-header h5 small { opacity: 0.7; font-weight: 500; }
    .modal-cinema .info-block {
        background: #fafbff;
        border-radius: 14px;
        padding: 16px 18px;
        height: 100%;
    }
    .modal-cinema .info-title {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #8a93a6;
        margin-bottom: 12px;
    }
    .modal-cinema .info-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        font-size: 0.88rem;
        padding: 6px 0;
        border-bottom: 1px dashed #e6e8f0;
    }
    .modal-cinema .info-row:last-child { border-bottom: 0; }
    .modal-cinema .info-row .label { color: #8a93a6; }
    .modal-cinema .info-row .value { color: #1f2533; font-weight: 600; text-align: right; }

    .seat-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #1A1953;
        color: #fff;
        font-weight: 700;
        font-size: 0.78rem;
        padding: 6px 10px;
        border-radius: 8px;
    }

    .summary-box {
        background: linear-gradient(135deg, #fafbff, #f3f4fa);
        border: 1px solid #e6e8f0;
        border-radius: 16px;
        padding: 18px 20px;
    }

    /* Responsive: collapse list to cards */
    @media (max-width: 992px) {
        .bk-list-header { display: none; }
        .bk-row {
            grid-template-columns: 1fr;
            gap: 8px;
            padding: 16px;
        }
        .bk-row > div::before {
            content: attr(data-label);
            display: block;
            font-size: 0.66rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #8a93a6;
            letter-spacing: 0.06em;
            margin-bottom: 2px;
        }
    }
</style>
@endpush

@section('content')

@php
    $statusOptions = [
        ''        => ['label' => 'Semua',   'icon' => 'bi-receipt',         'class' => 'primary', 'value' => $stats['total']],
        'success' => ['label' => 'Lunas',   'icon' => 'bi-check2-circle',   'class' => 'success', 'value' => $stats['success']],
        'pending' => ['label' => 'Pending', 'icon' => 'bi-hourglass-split', 'class' => 'warning', 'value' => $stats['pending']],
        'revenue' => ['label' => 'Pendapatan', 'icon' => 'bi-cash-stack',   'class' => 'info',    'value' => $stats['revenue']],
    ];
    $activeStatus = request('status', '');

    // Generate stable color palette for avatars based on string hash
    $avatarPalette = ['#1A1953', '#3a37a0', '#d4b06a', '#15a05c', '#0d6efd', '#dc3545', '#6f42c1', '#fd7e14'];
@endphp

<!-- HERO HEADER -->
<div class="bk-hero mb-4">
    <div class="row align-items-center position-relative" style="z-index:2;">
        <div class="col-md-7">
            <span class="badge bg-light text-dark rounded-pill mb-2 px-3 py-2">
                <i class="bi bi-receipt me-1"></i> Manajemen Booking
            </span>
            <h1 class="fw-bold mb-1">Daftar Transaksi</h1>
            <p class="mb-0 text-white-50">Pantau semua pesanan tiket dan status pembayaran user.</p>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <div class="d-inline-flex flex-column flex-md-row gap-2 justify-content-md-end">
                <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                    <i class="bi bi-bag-check me-1"></i> {{ number_format($stats['total'], 0, ',', '.') }} Total
                </span>
                <span class="badge rounded-pill px-3 py-2" style="background:rgba(212,176,106,0.95); color:#1A1953;">
                    <i class="bi bi-cash-coin me-1"></i> Rp {{ number_format($stats['revenue'], 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- QUICK STATS (clickable as status filter) -->
<div class="row g-3 mb-4">
    @foreach($statusOptions as $key => $opt)
        @php
            $isRevenue = $key === 'revenue';
            $isActive = !$isRevenue && $activeStatus === $key;
            $href = $isRevenue
                ? route('admin.bookings.index', array_merge(request()->except('page'), ['status' => 'success']))
                : route('admin.bookings.index', array_merge(request()->except(['status', 'page']), $key ? ['status' => $key] : []));
        @endphp
        <div class="col-6 col-xl-3">
            <a href="{{ $href }}" class="bk-stat {{ $isActive ? 'is-active' : '' }}">
                <div class="bk-stat-icon bg-{{ $opt['class'] }} bg-opacity-10 text-{{ $opt['class'] }}">
                    <i class="bi {{ $opt['icon'] }}"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="bk-stat-label">{{ $opt['label'] }}</div>
                    <div class="bk-stat-value">
                        @if($isRevenue)
                            Rp {{ number_format($opt['value'], 0, ',', '.') }}
                        @else
                            {{ number_format($opt['value'], 0, ',', '.') }}
                        @endif
                    </div>
                </div>
                @if($isActive)
                    <i class="bi bi-funnel-fill text-primary"></i>
                @endif
            </a>
        </div>
    @endforeach
</div>

<!-- TOOLBAR: SEARCH + ADVANCED TOGGLE -->
@php
    $hasAdvanced = request()->hasAny(['booking_status', 'method', 'type', 'date_from', 'date_to', 'sort', 'per_page']);
@endphp
<form action="{{ route('admin.bookings.index') }}" method="GET" id="filterForm" class="mb-3">
    @foreach(['status'] as $persist)
        @if(request($persist))
            <input type="hidden" name="{{ $persist }}" value="{{ request($persist) }}">
        @endif
    @endforeach

    <div class="bk-toolbar mb-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <div class="bk-search-wrap flex-grow-1">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="form-control" placeholder="Cari ID booking, nama customer, email, atau judul film..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-primary fw-bold rounded-pill px-4" style="background:#1A1953; border-color:#1A1953; height:44px;">
                <i class="bi bi-search me-1"></i> Cari
            </button>
            <button type="button" class="btn btn-bk-soft {{ $hasAdvanced ? 'is-active' : '' }}" data-bs-toggle="collapse" data-bs-target="#advancedFilter" aria-expanded="{{ $hasAdvanced ? 'true' : 'false' }}">
                <i class="bi bi-sliders me-1"></i> Filter Lanjutan
            </button>
            @if(request()->hasAny(['status', 'booking_status', 'method', 'type', 'date_from', 'date_to', 'sort', 'per_page', 'search']))
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-bk-soft" title="Reset semua filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            @endif
        </div>
    </div>

    <!-- ADVANCED FILTER (collapsible) -->
    <div class="collapse {{ $hasAdvanced ? 'show' : '' }}" id="advancedFilter">
        <div class="bk-advanced mb-3">
            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <label>Status Booking</label>
                    <select name="booking_status" class="form-select">
                        <option value="">Semua Booking</option>
                        <option value="pending"   {{ request('booking_status') == 'pending'   ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('booking_status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="cancelled" {{ request('booking_status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label>Metode Bayar</label>
                    <select name="method" class="form-select">
                        <option value="">Semua Metode</option>
                        <option value="qris"            {{ request('method') == 'qris' ? 'selected' : '' }}>QRIS</option>
                        <option value="virtual_account" {{ request('method') == 'virtual_account' ? 'selected' : '' }}>Virtual Account</option>
                        <option value="transfer"        {{ request('method') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="ewallet"         {{ request('method') == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                        <option value="cash"            {{ request('method') == 'cash' ? 'selected' : '' }}>Tunai</option>
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label>Tipe Customer</label>
                    <select name="type" class="form-select">
                        <option value="">Semua</option>
                        <option value="member" {{ request('type') == 'member' ? 'selected' : '' }}>Member</option>
                        <option value="guest"  {{ request('type') == 'guest'  ? 'selected' : '' }}>Guest</option>
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label>Urutkan</label>
                    <select name="sort" class="form-select">
                        <option value="updated_desc" {{ request('sort', 'updated_desc') == 'updated_desc' ? 'selected' : '' }}>Update Terbaru</option>
                        <option value="updated_asc"  {{ request('sort') == 'updated_asc'  ? 'selected' : '' }}>Update Terlama</option>
                        <option value="amount_high"  {{ request('sort') == 'amount_high'  ? 'selected' : '' }}>Nominal Tertinggi</option>
                        <option value="amount_low"   {{ request('sort') == 'amount_low'   ? 'selected' : '' }}>Nominal Terendah</option>
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
                <div class="col-md-3 col-6">
                    <label>Per Halaman</label>
                    <select name="per_page" class="form-select">
                        <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                        <option value="15" {{ request('per_page', '15') == '15' ? 'selected' : '' }}>15</option>
                        <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                    </select>
                </div>
                <div class="col-md-3 col-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 fw-bold" style="background:#1A1953; border-color:#1A1953; height:42px; border-radius:12px;">
                        <i class="bi bi-funnel-fill me-1"></i> Terapkan Filter
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- ACTIVE FILTER CHIPS (with individual remove) -->
@if(request()->hasAny(['status', 'booking_status', 'method', 'type', 'date_from', 'date_to', 'search']))
    <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
        <span class="text-muted small fw-bold me-1">Filter aktif:</span>

        @php
            $chipReset = fn ($keys) => route('admin.bookings.index', request()->except(array_merge((array) $keys, ['page'])));
        @endphp

        @if(request('search'))
            <span class="active-filter-chip">
                <i class="bi bi-search"></i> Cari: <strong>{{ request('search') }}</strong>
                <a href="{{ $chipReset('search') }}" class="chip-remove"><i class="bi bi-x-circle-fill"></i></a>
            </span>
        @endif
        @if(request('status'))
            <span class="active-filter-chip">
                <i class="bi bi-credit-card"></i> Bayar: <strong>{{ ucfirst(request('status')) }}</strong>
                <a href="{{ $chipReset('status') }}" class="chip-remove"><i class="bi bi-x-circle-fill"></i></a>
            </span>
        @endif
        @if(request('booking_status'))
            <span class="active-filter-chip">
                <i class="bi bi-receipt-cutoff"></i> Booking: <strong>{{ ucfirst(request('booking_status')) }}</strong>
                <a href="{{ $chipReset('booking_status') }}" class="chip-remove"><i class="bi bi-x-circle-fill"></i></a>
            </span>
        @endif
        @if(request('method'))
            <span class="active-filter-chip">
                <i class="bi bi-wallet2"></i> Metode: <strong>{{ strtoupper(str_replace('_', ' ', request('method'))) }}</strong>
                <a href="{{ $chipReset('method') }}" class="chip-remove"><i class="bi bi-x-circle-fill"></i></a>
            </span>
        @endif
        @if(request('type'))
            <span class="active-filter-chip">
                <i class="bi bi-people"></i> Tipe: <strong>{{ ucfirst(request('type')) }}</strong>
                <a href="{{ $chipReset('type') }}" class="chip-remove"><i class="bi bi-x-circle-fill"></i></a>
            </span>
        @endif
        @if(request('date_from') || request('date_to'))
            <span class="active-filter-chip">
                <i class="bi bi-calendar3"></i>
                Tanggal: <strong>{{ request('date_from') ?: '...' }} → {{ request('date_to') ?: '...' }}</strong>
                <a href="{{ $chipReset(['date_from', 'date_to']) }}" class="chip-remove"><i class="bi bi-x-circle-fill"></i></a>
            </span>
        @endif
    </div>
@endif

<!-- BOOKING LIST -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <span class="text-muted small">
        Menampilkan <span class="fw-bold text-dark">{{ $bookings->count() }}</span> dari <span class="fw-bold text-dark">{{ $bookings->total() }}</span> transaksi
    </span>
    <small class="text-muted"><i class="bi bi-arrow-down-up me-1"></i>
        @switch(request('sort', 'updated_desc'))
            @case('updated_asc') Update Terlama @break
            @case('amount_high') Nominal Tertinggi @break
            @case('amount_low')  Nominal Terendah @break
            @default Update Terbaru
        @endswitch
    </small>
</div>

<div class="bk-list mb-4">
    <div class="bk-list-header">
        <div>ID</div>
        <div>Customer</div>
        <div>Film</div>
        <div>Total</div>
        <div>Status</div>
        <div>Diperbarui</div>
        <div class="text-center">Aksi</div>
    </div>

    @forelse($bookings as $booking)
        @php
            $payment = $booking->latestPayment;
            $status  = $payment?->status ?? 'pending';
            $schedule = $booking->ticketBookings->first()?->schedule;
            $film = $schedule?->film;

            $name = $booking->customerName();
            $initial = mb_substr(trim($name), 0, 1);
            $initial = $initial ?: 'U';
            $color = $avatarPalette[crc32($name) % count($avatarPalette)];
        @endphp
        <div class="bk-row">
            <div data-label="ID">
                <div class="bk-id">#{{ $booking->id }}</div>
                <div class="bk-id-meta">{{ $booking->ticketBookings->count() }} kursi</div>
            </div>

            <div data-label="Customer">
                <div class="bk-customer">
                    <div class="bk-avatar" style="background: {{ $color }};">{{ $initial }}</div>
                    <div style="min-width:0;">
                        <div class="bk-customer-name" title="{{ $name }}">{{ $name }}</div>
                        <div class="bk-customer-meta">
                            @if($booking->isGuest())
                                <span class="bk-tag bk-tag-guest">Guest</span>
                            @else
                                <span class="bk-tag bk-tag-member">Member</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div data-label="Film">
                <div class="bk-film">
                    @if($film && $film->cover_url)
                        <img src="{{ $film->cover_url }}" alt="{{ $film->title }}" class="bk-poster">
                    @else
                        <div class="bk-poster-fallback"><i class="bi bi-film"></i></div>
                    @endif
                    <div style="min-width:0;">
                        <div class="bk-film-title">{{ $film?->title ?? 'N/A' }}</div>
                        <div class="bk-film-meta">
                            @if($schedule)
                                {{ $schedule->studio?->name ?? '-' }} • {{ $schedule->schedule_date->format('d M') }}, {{ $schedule->start_time->format('H:i') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div data-label="Total">
                <div class="bk-amount">Rp {{ number_format($payment?->amount ?? 0, 0, ',', '.') }}</div>
                @if($payment?->method)
                    <div class="bk-amount-method">{{ $payment->method_label }}</div>
                @endif
            </div>

            <div data-label="Status">
                @if($status == 'success')
                    <span class="bk-status-pill pill-success">Lunas</span>
                @elseif($status == 'pending')
                    <span class="bk-status-pill pill-pending">Pending</span>
                @else
                    <span class="bk-status-pill pill-failed">Gagal</span>
                @endif
                @if($booking->status === 'cancelled')
                    <div class="bk-amount-method mt-1" style="color:#dc3545;">Booking dibatalkan</div>
                @endif
            </div>

            <div data-label="Diperbarui">
                <div class="bk-time-main">{{ $booking->updated_at->translatedFormat('d M Y') }}</div>
                <div class="bk-time-sub">{{ $booking->updated_at->format('H:i') }} • {{ $booking->updated_at->diffForHumans() }}</div>
            </div>

            <div class="text-center" data-label="Aksi">
                <button type="button" class="btn btn-bk-detail" data-bs-toggle="modal" data-bs-target="#bookingDetail{{ $booking->id }}">
                    <i class="bi bi-eye"></i> Detail
                </button>
            </div>
        </div>
    @empty
        <div class="bk-empty">
            <i class="bi bi-inbox d-block mb-2"></i>
            <h6 class="fw-bold text-dark mb-1">Tidak ada transaksi</h6>
            <p class="mb-3">Belum ada transaksi yang cocok dengan filter saat ini.</p>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-bk-soft">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
            </a>
        </div>
    @endforelse
</div>

@if($bookings->hasPages())
    <div class="d-flex justify-content-center">
        {{ $bookings->links() }}
    </div>
@endif

<!-- DETAIL MODALS -->
@foreach($bookings as $booking)
    @php
        $schedule = $booking->ticketBookings->first()?->schedule;
        $film = $schedule?->film;
        $payment = $booking->latestPayment;
        $payStatus = $payment?->status ?? 'pending';
    @endphp
    <div class="modal fade modal-cinema" id="bookingDetail{{ $booking->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title fw-bold mb-0">
                            Detail Booking #{{ $booking->id }}
                            <small class="d-block">{{ $booking->created_at->translatedFormat('l, d F Y, H:i') }}</small>
                        </h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="info-block">
                                <div class="info-title"><i class="bi bi-person-circle me-1"></i> Customer</div>
                                <div class="info-row"><span class="label">Tipe</span><span class="value">{{ $booking->customerTypeLabel() }}</span></div>
                                <div class="info-row"><span class="label">Nama</span><span class="value">{{ $booking->customerName() }}</span></div>
                                <div class="info-row"><span class="label">Email</span><span class="value">{{ $booking->customerEmail() ?? '-' }}</span></div>
                                <div class="info-row"><span class="label">No. HP</span><span class="value">{{ $booking->customerPhone() ?? '-' }}</span></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-block">
                                <div class="info-title"><i class="bi bi-film me-1"></i> Penayangan</div>
                                <div class="info-row"><span class="label">Film</span><span class="value">{{ $film?->title ?? '-' }}</span></div>
                                <div class="info-row"><span class="label">Studio</span><span class="value">{{ $schedule?->studio->name ?? '-' }}</span></div>
                                <div class="info-row"><span class="label">Tanggal</span><span class="value">{{ $schedule ? $schedule->schedule_date->translatedFormat('d M Y') : '-' }}</span></div>
                                <div class="info-row"><span class="label">Jam</span><span class="value">{{ $schedule ? $schedule->start_time->format('H:i') : '-' }}</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="info-block mb-3">
                        <div class="info-title"><i class="bi bi-grid-3x3-gap-fill me-1"></i> Kursi yang Dipesan ({{ $booking->ticketBookings->count() }})</div>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @forelse($booking->ticketBookings as $ticket)
                                <span class="seat-chip"><i class="bi bi-ticket-detailed-fill"></i> {{ $ticket->seat->seat_code }}</span>
                            @empty
                                <span class="text-muted small">Belum ada kursi.</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="summary-box d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <div class="info-title mb-1"><i class="bi bi-cash-coin me-1"></i> Pembayaran</div>
                            <h3 class="fw-bold text-primary mb-0" style="color:#1A1953 !important;">
                                Rp {{ number_format($payment?->amount ?? 0, 0, ',', '.') }}
                            </h3>
                            @if($payment)
                                <small class="text-muted">
                                    Metode: <strong>{{ $payment->method_label }}</strong>
                                    @if($payment->paid_at)
                                        • Dibayar {{ $payment->paid_at->translatedFormat('d M Y, H:i') }}
                                    @endif
                                </small>
                            @endif
                        </div>
                        <div class="text-end">
                            <div class="info-title mb-1">Status</div>
                            @if($payStatus == 'success')
                                <span class="bk-status-pill pill-success" style="font-size:0.95rem; padding:8px 14px;">Lunas</span>
                            @elseif($payStatus == 'pending')
                                <span class="bk-status-pill pill-pending" style="font-size:0.95rem; padding:8px 14px;">Pending</span>
                            @else
                                <span class="bk-status-pill pill-failed" style="font-size:0.95rem; padding:8px 14px;">Gagal</span>
                            @endif
                            <div class="text-muted small mt-1">Booking: <strong>{{ ucfirst($booking->status) }}</strong></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-bk-soft" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('filterForm');
        if (!form) return;

        form.querySelectorAll('select[name="booking_status"], select[name="method"], select[name="type"], select[name="sort"], select[name="per_page"]').forEach(function (el) {
            el.addEventListener('change', function () {
                form.submit();
            });
        });

        form.querySelectorAll('input[type="date"]').forEach(function (el) {
            el.addEventListener('change', function () {
                form.submit();
            });
        });
    });
</script>
@endpush
