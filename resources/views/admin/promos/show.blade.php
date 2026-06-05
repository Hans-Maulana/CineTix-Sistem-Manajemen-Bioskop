@extends('layouts.admin')

@section('title', 'Detail Promo - ' . $promo->code)

@push('styles')
@include('admin.promos._form_styles')
<style>
    .ps-info-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 6px 18px rgba(26, 25, 83, 0.04);
        padding: 22px 24px;
    }
    .ps-info-card h5 {
        font-weight: 800; color: #1f2533;
        margin-bottom: 16px;
        display: flex; align-items: center; gap: 10px;
    }
    .ps-info-card h5 .ico {
        width: 34px; height: 34px;
        background: linear-gradient(135deg, #1A1953, #3a37a0);
        color: #fff;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.9rem;
    }
    .ps-row {
        display: flex; justify-content: space-between; gap: 16px;
        padding: 10px 0;
        border-bottom: 1px dashed rgba(26, 25, 83, 0.08);
    }
    .ps-row:last-child { border-bottom: 0; }
    .ps-row .k { color: #6b7280; font-size: 0.85rem; }
    .ps-row .v { font-weight: 700; color: #1f2533; font-size: 0.92rem; text-align: right; }

    .ps-table {
        background: #fff;
        border-radius: 18px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 6px 18px rgba(26, 25, 83, 0.04);
        overflow: hidden;
    }
    .ps-table-head {
        padding: 18px 22px;
        border-bottom: 1px solid rgba(26, 25, 83, 0.06);
        display: flex; justify-content: space-between; align-items: center;
    }
    .ps-table-head h5 {
        margin: 0; font-weight: 800; color: #1f2533;
        display: flex; align-items: center; gap: 10px;
    }
    .ps-table-head h5 .ico {
        width: 34px; height: 34px;
        background: linear-gradient(135deg, #1A1953, #3a37a0);
        color: #fff;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.9rem;
    }
    .ps-usage-row {
        display: grid;
        grid-template-columns: 50px 1fr auto auto;
        gap: 16px;
        align-items: center;
        padding: 14px 22px;
        border-bottom: 1px solid rgba(26, 25, 83, 0.05);
    }
    .ps-usage-row:last-child { border-bottom: 0; }
    .ps-usage-row:hover { background: #fafbfd; }
    .ps-avatar {
        width: 42px; height: 42px;
        border-radius: 12px;
        background: linear-gradient(135deg, #1A1953, #3a37a0);
        color: #fff; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
    }
    .ps-empty {
        text-align: center; padding: 50px 20px; color: #8a93a6;
    }
    .ps-empty .ico {
        width: 60px; height: 60px;
        margin: 0 auto 12px;
        background: rgba(26, 25, 83, 0.06); color: #1A1953;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem;
    }
</style>
@endpush

@section('content')
@php
    $now = now();
    $isUpcoming = $promo->valid_from->gt($now);
    $isExpired = $promo->valid_until->lt($now) || ($promo->max_usage && $promo->usage_count >= $promo->max_usage);
    $isActive = !$isUpcoming && !$isExpired;
    $usagePercent = $promo->max_usage
        ? min(100, ($promo->usage_count / $promo->max_usage) * 100)
        : ($promo->usage_count > 0 ? 100 : 0);
    $daysLeft = $now->diffInDays($promo->valid_until, false);
@endphp

<div class="container-fluid py-4">
    <!-- Hero -->
    <div class="pr-hero mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 position-relative" style="z-index:1;">
            <div>
                <a href="{{ route('admin.promos.index') }}" class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2 text-decoration-none"
                   style="background:rgba(255,255,255,0.12); color:rgba(255,255,255,0.9); font-size:0.78rem; font-weight:600;">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Promo
                </a>
                <h1>{{ $promo->code }}</h1>
                <p class="mb-0" style="opacity:0.85; font-size:0.95rem;">{{ $promo->description ?: 'Detail dan riwayat penggunaan promo.' }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.promos.edit', $promo) }}" class="d-inline-flex align-items-center gap-2 px-4 py-2 rounded-pill text-decoration-none"
                   style="background:rgba(255,255,255,0.18); color:#fff; font-weight:700;">
                    <i class="fas fa-pen"></i> Edit
                </a>
                <form action="{{ route('admin.promos.destroy', $promo) }}" method="POST" class="m-0" onsubmit="return confirm('Yakin ingin menghapus promo {{ $promo->code }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="d-inline-flex align-items-center gap-2 px-4 py-2 rounded-pill border-0"
                       style="background:rgba(214, 59, 59, 0.85); color:#fff; font-weight:700;">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Coupon + Info Grid -->
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <!-- Coupon preview -->
            @php
                $couponBgStyle = '';
                if ($isUpcoming) {
                    $couponBgStyle = 'background:linear-gradient(135deg,#d97706,#f59e0b);';
                } elseif ($isExpired) {
                    $couponBgStyle = 'background:linear-gradient(135deg,#6b7280,#9ca3af);';
                }
            @endphp
            <div class="pr-card">
                <div class="pr-coupon" style="{{ $couponBgStyle }}">
                    @if ($isActive)
                        <span class="pr-status-pill" style="background:rgba(34, 197, 94, 0.25);">
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
                        <div class="pr-progress">
                            <div class="label">
                                <span><i class="fas fa-fire me-1"></i>Pemakaian</span>
                                <span>{{ $promo->usage_count }} / {{ $promo->max_usage ?? '∞' }}</span>
                            </div>
                            <div class="bar">
                                <div class="fill" style="width: {{ $usagePercent }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ps-info-card h-100">
                <h5><span class="ico"><i class="fas fa-info"></i></span> Informasi Dasar</h5>
                <div class="ps-row">
                    <span class="k">Kode</span>
                    <span class="v" style="font-family:'Courier New',monospace; letter-spacing:0.04em;">{{ $promo->code }}</span>
                </div>
                <div class="ps-row">
                    <span class="k">Tipe Diskon</span>
                    <span class="v">{{ $promo->discount_type === 'percentage' ? 'Persentase' : 'Nominal Tetap' }}</span>
                </div>
                <div class="ps-row">
                    <span class="k">Nilai Diskon</span>
                    <span class="v" style="color:#16a34a;">
                        @if ($promo->discount_type === 'percentage')
                            {{ rtrim(rtrim(number_format($promo->discount_value, 2, '.', ''), '0'), '.') }}%
                        @else
                            Rp {{ number_format($promo->discount_value, 0, ',', '.') }}
                        @endif
                    </span>
                </div>
                <div class="ps-row">
                    <span class="k">Limit per Customer</span>
                    <span class="v">{{ $promo->max_usage_per_customer }}x</span>
                </div>
                <div class="ps-row">
                    <span class="k">Total Kuota</span>
                    <span class="v">{{ $promo->max_usage ? number_format($promo->max_usage) . 'x' : '∞ Unlimited' }}</span>
                </div>
                <div class="ps-row">
                    <span class="k">Status</span>
                    <span class="v">
                        @if ($isActive)
                            <span class="badge" style="background:rgba(34,197,94,0.12); color:#16a34a; padding:5px 10px; border-radius:6px;">Aktif</span>
                        @elseif ($isUpcoming)
                            <span class="badge" style="background:rgba(245,158,11,0.12); color:#d97706; padding:5px 10px; border-radius:6px;">Akan Datang</span>
                        @else
                            <span class="badge" style="background:rgba(214,59,59,0.12); color:#d63b3b; padding:5px 10px; border-radius:6px;">Berakhir</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ps-info-card h-100">
                <h5><span class="ico"><i class="fas fa-calendar-day"></i></span> Periode & Statistik</h5>
                <div class="ps-row">
                    <span class="k">Mulai Berlaku</span>
                    <span class="v">{{ $promo->valid_from->translatedFormat('d M Y') }}</span>
                </div>
                <div class="ps-row">
                    <span class="k">Berakhir</span>
                    <span class="v">{{ $promo->valid_until->translatedFormat('d M Y') }}</span>
                </div>
                <div class="ps-row">
                    <span class="k">Sisa Hari</span>
                    <span class="v" style="color: {{ $daysLeft <= 7 && $daysLeft >= 0 ? '#d63b3b' : '#1f2533' }};">
                        @if ($daysLeft >= 0)
                            {{ (int) ceil($daysLeft) }} hari
                        @else
                            <span style="color:#d63b3b;">Sudah lewat</span>
                        @endif
                    </span>
                </div>
                <div class="ps-row">
                    <span class="k">Total Pemakaian</span>
                    <span class="v" style="color:#16a34a;">{{ number_format($promo->usage_count) }} kali</span>
                </div>
                <div class="ps-row">
                    <span class="k">Customer Pengguna</span>
                    <span class="v">{{ number_format($promo->usages->count()) }} orang</span>
                </div>
                <div class="ps-row">
                    <span class="k">Dibuat</span>
                    <span class="v">{{ $promo->created_at->translatedFormat('d M Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Usage History -->
    <div class="ps-table">
        <div class="ps-table-head">
            <h5><span class="ico"><i class="fas fa-clock-rotate-left"></i></span> Riwayat Pemakaian</h5>
            <span class="text-muted small">{{ $promo->usages->count() }} customer</span>
        </div>
        @if ($promo->usages->isEmpty())
            <div class="ps-empty">
                <div class="ico"><i class="fas fa-receipt"></i></div>
                <div style="font-weight:700; color:#1f2533;">Belum ada yang memakai promo ini</div>
                <div class="small mt-1">Pemakaian akan tercatat otomatis saat customer checkout dengan kode ini.</div>
            </div>
        @else
            @foreach ($promo->usages->sortByDesc('updated_at') as $usage)
                @php
                    $userName = optional($usage->user)->name ?? 'User dihapus';
                    $cleanName = preg_replace('/^\d+\s*-\s*/', '', $userName);
                    $initial = mb_strtoupper(mb_substr(trim($cleanName), 0, 1));
                @endphp
                <div class="ps-usage-row">
                    <div class="ps-avatar">{{ $initial }}</div>
                    <div>
                        <div style="font-weight:700; color:#1f2533;">{{ $cleanName }}</div>
                        <div class="text-muted small">{{ optional($usage->user)->email ?? '-' }}</div>
                    </div>
                    <div class="text-end">
                        <div style="font-weight:800; color:#1A1953; font-size:1.1rem;">{{ $usage->usage_count }}x</div>
                        <div class="text-muted small">pemakaian</div>
                    </div>
                    <div class="text-end text-muted small" style="min-width:120px;">
                        <div>{{ optional($usage->updated_at)->translatedFormat('d M Y') }}</div>
                        <div>{{ optional($usage->updated_at)->diffForHumans() }}</div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
