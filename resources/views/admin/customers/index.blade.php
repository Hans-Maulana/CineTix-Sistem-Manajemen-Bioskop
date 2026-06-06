@extends('layouts.admin')

@section('title', 'Manajemen Customer')

@push('styles')
<style>
    .cs-hero {
        background: linear-gradient(120deg, #1A1953 0%, #2d2b7a 60%, #3a37a0 100%);
        border-radius: 24px;
        color: #fff;
        padding: 28px 32px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(26, 25, 83, 0.25);
    }
    .cs-hero::after {
        content: ""; position: absolute;
        right: -60px; top: -60px;
        width: 220px; height: 220px;
        background: rgba(212, 176, 106, 0.18);
        border-radius: 50%;
    }
    .cs-hero h1 { font-size: 1.75rem; font-weight: 800; margin-bottom: 4px; }

    .cs-stat {
        background: #fff;
        border-radius: 18px;
        padding: 18px 22px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 8px 22px rgba(26, 25, 83, 0.04);
        display: flex; align-items: center; gap: 16px;
        height: 100%;
    }
    .cs-stat-icon {
        font-size: 1.6rem;
        color: #1A1953;
        flex-shrink: 0;
        width: 36px;
        text-align: center;
    }
    .cs-stat-label { font-size: 0.7rem; letter-spacing: 0.06em; font-weight: 700; text-transform: uppercase; color: #8a93a6; }
    .cs-stat-value { font-size: 1.5rem; font-weight: 800; color: #1f2533; line-height: 1.1; margin-top: 2px; }

    .cs-toolbar {
        background: #fff;
        border-radius: 18px;
        padding: 18px 22px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 6px 18px rgba(26, 25, 83, 0.04);
    }
    .cs-search { position: relative; flex: 1; min-width: 240px; }
    .cs-search i {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: #9aa3b6;
    }
    .cs-search input {
        width: 100%;
        border: 1px solid rgba(26, 25, 83, 0.1);
        border-radius: 12px;
        padding: 10px 14px 10px 38px;
        font-size: 0.92rem;
    }
    .cs-search input:focus {
        outline: 0; border-color: #d4b06a;
        box-shadow: 0 0 0 4px rgba(212, 176, 106, 0.15);
    }
    .cs-toolbar select {
        border: 1px solid rgba(26, 25, 83, 0.1);
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 0.9rem;
        background: #fff;
        font-weight: 600;
        color: #1f2533;
    }
    .cs-chips { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 12px; }
    .cs-chip {
        background: rgba(26, 25, 83, 0.06);
        border-radius: 999px;
        padding: 4px 10px 4px 14px;
        font-size: 0.78rem; font-weight: 600; color: #1A1953;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .cs-chip a {
        color: #6b7280; font-size: 0.8rem;
        background: rgba(26, 25, 83, 0.08);
        width: 18px; height: 18px;
        border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        text-decoration: none;
    }
    .cs-chip a:hover { background: rgba(214, 59, 59, 0.15); color: #d63b3b; }

    /* Customer cards */
    .cs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
        gap: 16px;
    }
    .cs-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 6px 18px rgba(26, 25, 83, 0.04);
        padding: 18px 20px;
        transition: all 0.2s;
        position: relative;
        overflow: hidden;
    }
    .cs-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 30px rgba(26, 25, 83, 0.1);
        border-color: rgba(212, 176, 106, 0.4);
    }

    .cs-card-head {
        display: flex; gap: 14px; align-items: center;
        margin-bottom: 14px;
    }
    .cs-avatar {
        width: 54px; height: 54px;
        border-radius: 16px;
        background: linear-gradient(135deg, #1A1953, #3a37a0);
        color: #fff; font-weight: 800;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
        box-shadow: 0 6px 14px rgba(26, 25, 83, 0.18);
    }
    .cs-card-head .info { flex: 1; min-width: 0; }
    .cs-name {
        font-weight: 800; color: #1f2533; font-size: 1rem;
        display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .cs-since {
        font-size: 0.74rem; color: #8a93a6;
        margin-top: 2px; font-weight: 600;
    }

    .cs-contact {
        display: flex; flex-direction: column; gap: 6px;
        margin-bottom: 14px;
    }
    .cs-contact-row {
        display: flex; align-items: center; gap: 8px;
        font-size: 0.84rem;
        color: #6b7280;
        word-break: break-word;
    }
    .cs-contact-row i {
        width: 26px; height: 26px;
        background: rgba(26, 25, 83, 0.06); color: #1A1953;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.78rem;
        flex-shrink: 0;
    }
    .cs-contact-row .val { word-break: break-word; flex: 1; min-width: 0; }
    .cs-contact-row.muted .val { color: #9aa3b6; font-style: italic; }

    .cs-stats-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        padding-top: 12px;
        border-top: 1px dashed rgba(26, 25, 83, 0.08);
    }
    .cs-stat-mini {
        background: #f7f8fc;
        border-radius: 10px;
        padding: 10px 12px;
    }
    .cs-stat-mini .lbl {
        font-size: 0.65rem; color: #8a93a6;
        font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;
    }
    .cs-stat-mini .val {
        font-size: 1.05rem; font-weight: 800; color: #1f2533;
        line-height: 1.1; margin-top: 2px;
    }
    .cs-stat-mini .val small {
        font-size: 0.7rem; color: #6b7280; font-weight: 500;
    }

    .cs-empty {
        text-align: center;
        padding: 60px 24px;
        background: #fff;
        border-radius: 20px;
        border: 2px dashed rgba(26, 25, 83, 0.12);
    }
    .cs-empty .ico {
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
    <div class="cs-hero mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 position-relative" style="z-index:1;">
            <div>
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2"
                     style="background:rgba(255,255,255,0.12); font-size:0.75rem; font-weight:700; letter-spacing:0.06em; text-transform:uppercase;">
                    <i class="fas fa-user-group"></i> Manajemen Customer
                </div>
                <h1>Daftar Customer</h1>
                <p class="mb-0" style="opacity:0.85; font-size:0.95rem;">Pantau aktivitas customer terdaftar — booking, transaksi, dan pengunjung VIP.</p>
            </div>
            <div class="text-end" style="opacity:0.9;">
                <div style="font-size:0.78rem; opacity:0.78; font-weight:600;">Total Pendapatan dari Member</div>
                <div style="font-size:1.5rem; font-weight:800; font-family:Inter,sans-serif;">
                    Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="cs-stat">
                <i class="fas fa-users cs-stat-icon"></i>
                <div>
                    <div class="cs-stat-label">Total Customer</div>
                    <div class="cs-stat-value">{{ number_format($stats['total']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="cs-stat">
                <i class="fas fa-user-check cs-stat-icon"></i>
                <div>
                    <div class="cs-stat-label">Sudah Booking</div>
                    <div class="cs-stat-value">{{ number_format($stats['active']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="cs-stat">
                <i class="fas fa-user-slash cs-stat-icon"></i>
                <div>
                    <div class="cs-stat-label">Belum Booking</div>
                    <div class="cs-stat-value">{{ number_format(max(0, $stats['total'] - $stats['active'])) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toolbar -->
    <form method="GET" action="{{ route('admin.customers.index') }}" class="cs-toolbar mb-4">
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <div class="cs-search">
                <i class="fas fa-magnifying-glass"></i>
                <input type="text" name="search" placeholder="Cari nama, email, atau kontak…" value="{{ request('search') }}">
            </div>
            <select name="activity" onchange="this.form.submit()">
                <option value="">Semua Aktivitas</option>
                <option value="active" {{ request('activity') === 'active' ? 'selected' : '' }}>Sudah Booking</option>
                <option value="inactive" {{ request('activity') === 'inactive' ? 'selected' : '' }}>Belum Booking</option>
            </select>
            <select name="sort" onchange="this.form.submit()">
                <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Terbaru Bergabung</option>
                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Terlama Bergabung</option>
                <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Nama A → Z</option>
                <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Nama Z → A</option>
                <option value="most_active" {{ request('sort') === 'most_active' ? 'selected' : '' }}>Paling Aktif</option>
                <option value="top_spender" {{ request('sort') === 'top_spender' ? 'selected' : '' }}>Pengeluaran Tertinggi</option>
            </select>
            <button type="submit" class="btn" style="background:#1A1953; color:#fff; border-radius:12px; padding:10px 18px; font-weight:700;">
                <i class="fas fa-filter me-1"></i> Terapkan
            </button>
            @if (request()->hasAny(['search', 'activity']) || request('sort', 'newest') !== 'newest')
                <a href="{{ route('admin.customers.index') }}" class="btn btn-light border" style="border-radius:12px; padding:10px 16px; font-weight:600;">
                    <i class="fas fa-rotate-left me-1"></i> Reset
                </a>
            @endif
        </div>

        @php
            $activeFilters = [];
            if (request('search')) $activeFilters[] = ['k' => 'search', 'l' => 'Cari: ' . request('search')];
            if (request('activity')) {
                $actMap = ['active' => 'Sudah Booking', 'inactive' => 'Belum Booking'];
                $activeFilters[] = ['k' => 'activity', 'l' => 'Aktivitas: ' . ($actMap[request('activity')] ?? request('activity'))];
            }
        @endphp
        @if (count($activeFilters))
            <div class="cs-chips">
                @foreach ($activeFilters as $f)
                    <span class="cs-chip">
                        {{ $f['l'] }}
                        <a href="{{ route('admin.customers.index', array_merge(request()->except($f['k']), ['page' => null])) }}" title="Hapus filter">
                            <i class="fas fa-xmark"></i>
                        </a>
                    </span>
                @endforeach
            </div>
        @endif
    </form>

    <!-- Grid -->
    @if ($customers->isEmpty())
        <div class="cs-empty">
            <div class="ico"><i class="fas fa-user-slash"></i></div>
            <div style="font-weight:700; color:#1f2533; font-size:1.1rem;">Tidak ada customer</div>
            <div class="text-muted small mt-1">
                @if (request()->hasAny(['search', 'activity']))
                    Tidak ada customer yang cocok dengan filter. <a href="{{ route('admin.customers.index') }}">Reset filter</a>.
                @else
                    Belum ada user yang terdaftar sebagai customer.
                @endif
            </div>
        </div>
    @else
        <div class="cs-grid">
            @foreach ($customers as $customer)
                @php
                    $cleanName = preg_replace('/^\d+\s*-\s*/', '', $customer->name ?? 'User');
                    $initial = mb_strtoupper(mb_substr(trim($cleanName), 0, 1));
                    $bookingCount = (int) ($customer->confirmed_bookings_count ?? 0);
                    $totalSpent = (float) ($customer->total_spent ?? 0);
                @endphp
                <div class="cs-card">
                    <div class="cs-card-head">
                        <div class="cs-avatar">{{ $initial }}</div>
                        <div class="info">
                            <div class="cs-name">{{ $cleanName }}</div>
                            <div class="cs-since">
                                <i class="fas fa-calendar-day me-1"></i>
                                Bergabung {{ $customer->created_at->translatedFormat('d M Y') }}
                            </div>
                        </div>
                    </div>

                    <div class="cs-contact">
                        <div class="cs-contact-row">
                            <i class="fas fa-envelope"></i>
                            <div class="val">{{ $customer->email }}</div>
                        </div>
                        <div class="cs-contact-row {{ !$customer->contact ? 'muted' : '' }}">
                            <i class="fas fa-phone"></i>
                            <div class="val">{{ $customer->contact ?: 'Belum ada kontak' }}</div>
                        </div>
                    </div>

                    <div class="cs-stats-row">
                        <div class="cs-stat-mini">
                            <div class="lbl">Booking</div>
                            <div class="val">{{ $bookingCount }} <small>kali</small></div>
                        </div>
                        <div class="cs-stat-mini">
                            <div class="lbl">Total Belanja</div>
                            <div class="val" style="font-size:0.92rem;">
                                @if ($totalSpent >= 1000000)
                                    Rp {{ number_format($totalSpent / 1000000, 1, ',', '.') }}<small>jt</small>
                                @elseif ($totalSpent >= 1000)
                                    Rp {{ number_format($totalSpent / 1000, 0, ',', '.') }}<small>rb</small>
                                @else
                                    Rp {{ number_format($totalSpent, 0, ',', '.') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($customers->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $customers->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
