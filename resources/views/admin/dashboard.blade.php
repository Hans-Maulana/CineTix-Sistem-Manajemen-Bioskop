@extends('layouts.admin')

@section('title', 'Dashboard')

@push('styles')
<style>
    /* ===== Dashboard Modern Styles ===== */
    .dash-hero {
        background: linear-gradient(120deg, #1A1953 0%, #2d2b7a 60%, #3a37a0 100%);
        border-radius: 24px;
        color: #fff;
        padding: 28px 32px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(26, 25, 83, 0.25);
    }
    .dash-hero::after {
        content: "";
        position: absolute;
        right: -60px;
        top: -60px;
        width: 220px;
        height: 220px;
        background: rgba(212, 176, 106, 0.18);
        border-radius: 50%;
    }
    .dash-hero::before {
        content: "";
        position: absolute;
        right: 90px;
        bottom: -80px;
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }

    .stat-card {
        background: #fff;
        border-radius: 20px;
        padding: 22px 24px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 10px 30px rgba(26, 25, 83, 0.04);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        height: 100%;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 36px rgba(26, 25, 83, 0.10);
    }
    .stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .stat-label {
        font-size: 0.72rem;
        letter-spacing: 0.06em;
        font-weight: 700;
        text-transform: uppercase;
        color: #8a93a6;
    }
    .stat-value {
        font-size: 1.8rem;
        font-weight: 800;
        line-height: 1.1;
        color: #1f2533;
        margin-top: 6px;
    }
    .trend-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.74rem;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 50px;
    }
    .trend-up { background: rgba(25, 167, 95, 0.12); color: #15a05c; }
    .trend-down { background: rgba(220, 53, 69, 0.12); color: #dc3545; }
    .trend-flat { background: rgba(138, 147, 166, 0.15); color: #6c7689; }

    .mini-stat {
        background: #fff;
        border-radius: 18px;
        padding: 18px 20px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 8px 22px rgba(26, 25, 83, 0.03);
        display: flex;
        align-items: center;
        gap: 14px;
        height: 100%;
    }
    .mini-icon {
        width: 42px; height: 42px;
        border-radius: 12px;
        flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
    }

    .panel {
        background: #fff;
        border-radius: 20px;
        padding: 26px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 10px 30px rgba(26, 25, 83, 0.03);
    }
    .panel-title { font-weight: 800; color: #1f2533; font-size: 1.05rem; }

    .occ-ring {
        --val: 0;
        width: 130px; height: 130px;
        border-radius: 50%;
        background: conic-gradient(#d4b06a calc(var(--val) * 1%), #eef0f7 0);
        display: flex; align-items: center; justify-content: center;
        position: relative;
    }
    .occ-ring::after {
        content: "";
        position: absolute;
        inset: 14px;
        background: #fff;
        border-radius: 50%;
    }
    .occ-inner { position: relative; z-index: 1; text-align: center; }

    .rank-badge {
        width: 26px; height: 26px;
        border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 0.8rem;
        color: #fff;
    }
    .rank-1 { background: #d4b06a; }
    .rank-2 { background: #9aa3b8; }
    .rank-3 { background: #c08457; }
    .rank-x { background: #d8dce6; color: #5d6778; }

    .pending-item {
        border: 1px solid #eef0f7;
        border-radius: 14px;
        padding: 14px 16px;
        margin-bottom: 12px;
        transition: background 0.2s ease;
    }
    .pending-item:hover { background: #faf9ff; }
</style>
@endpush

@section('content')

<!-- HERO HEADER -->
<div class="dash-hero mb-4">
    <div class="row align-items-center position-relative" style="z-index:2;">
        <div class="col-md-7">
            <span class="badge bg-light text-dark rounded-pill mb-2 px-3 py-2">
                <i class="bi bi-calendar3 me-1"></i> {{ now()->translatedFormat('l, d F Y') }}
            </span>
            <h1 class="fw-bold mb-1">Halo, {{ auth()->user()->name }} 👋</h1>
            <p class="mb-0 text-white-50">Berikut ringkasan performa bioskop CineTix hari ini.</p>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.films.create') }}" class="btn btn-light fw-bold rounded-pill px-4 me-2 mb-2">
                <i class="bi bi-plus-lg"></i> Tambah Film
            </a>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-light fw-bold rounded-pill px-4 mb-2">
                <i class="bi bi-graph-up"></i> Lihat Laporan
            </a>
        </div>
    </div>
</div>

<!-- HERO STATS: FOKUS HARI INI -->
<div class="row g-3 mb-3">
    <!-- Pendapatan Hari Ini -->
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-cash-stack"></i></div>
                @include('admin.partials.trend', ['trend' => $revenueTrend])
            </div>
            <div class="stat-label mt-3">Pendapatan Hari Ini</div>
            <div class="stat-value">Rp {{ number_format($revenueToday, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Tiket Terjual Hari Ini -->
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-ticket-detailed"></i></div>
                @include('admin.partials.trend', ['trend' => $ticketsTrend])
            </div>
            <div class="stat-label mt-3">Tiket Terjual Hari Ini</div>
            <div class="stat-value">{{ number_format($ticketsToday, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Transaksi Hari Ini -->
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-bag-check"></i></div>
                @include('admin.partials.trend', ['trend' => $transactionsTrend])
            </div>
            <div class="stat-label mt-3">Transaksi Hari Ini</div>
            <div class="stat-value">{{ number_format($transactionsToday, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Booking Pending (Actionable) -->
    <div class="col-md-6 col-xl-3">
        <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" class="text-decoration-none">
            <div class="stat-card" style="border-color: rgba(220,53,69,0.25);">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-hourglass-split"></i></div>
                </div>
                <div class="stat-label mt-3">Booking Pending</div>
                <div class="stat-value">{{ number_format($pendingBookingsCount, 0, ',', '.') }}</div>
                <div class="text-muted small mt-2">Lihat detail <i class="bi bi-arrow-right"></i></div>
            </div>
        </a>
    </div>
</div>

<!-- BARIS WIDGET: OKUPANSI + STAT SEKUNDER -->
<div class="row g-3 mb-4">
    <!-- Okupansi Kursi Hari Ini -->
    <div class="col-xl-4">
        <div class="panel h-100">
            <div class="panel-title mb-3"><i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i>Okupansi Kursi Hari Ini</div>
            <div class="d-flex align-items-center gap-4">
                <div class="occ-ring" style="--val: {{ $occupancyRate }};">
                    <div class="occ-inner">
                        <div class="fw-bold" style="font-size:1.6rem; color:#1A1953;">{{ $occupancyRate }}%</div>
                        <div class="text-muted" style="font-size:0.7rem;">terisi</div>
                    </div>
                </div>
                <div>
                    <div class="mb-2">
                        <div class="text-muted small">Kursi terisi</div>
                        <div class="fw-bold fs-5">{{ number_format($todayOccupied, 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <div class="text-muted small">Total kapasitas</div>
                        <div class="fw-bold fs-5">{{ number_format($todayCapacity, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat sekunder -->
    <div class="col-xl-8">
        <div class="row g-3 h-100">
            <div class="col-sm-6">
                <div class="mini-stat">
                    <div class="mini-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-calendar-event"></i></div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">{{ $todaySchedulesCount }}</div>
                        <div class="text-muted small">Penayangan hari ini</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="mini-stat">
                    <div class="mini-icon bg-success bg-opacity-10 text-success"><i class="bi bi-person-plus"></i></div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">{{ $newMembersWeek }}</div>
                        <div class="text-muted small">Member baru minggu ini</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="mini-stat">
                    <div class="mini-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-film"></i></div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">{{ $totalFilms }}</div>
                        <div class="text-muted small">Total film di katalog</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="mini-stat">
                    <div class="mini-icon bg-info bg-opacity-10 text-info"><i class="bi bi-people"></i></div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">{{ number_format($totalCustomers, 0, ',', '.') }}</div>
                        <div class="text-muted small">Total member terdaftar</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- GRAFIK + BOOKING PENDING -->
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="panel" style="height: 420px;">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <div class="panel-title">Grafik Pendapatan</div>
                    <small class="text-muted">
                        @if($filter === 'yearly') 12 bulan terakhir
                        @elseif($filter === 'monthly') 30 hari terakhir
                        @else 7 hari terakhir @endif
                    </small>
                </div>
                <div class="btn-group btn-group-sm rounded-pill p-1 bg-light" role="group">
                    <a href="?filter=weekly" class="btn rounded-pill px-3 fw-bold {{ $filter === 'weekly' ? 'btn-primary text-white' : 'text-muted' }}">Mingguan</a>
                    <a href="?filter=monthly" class="btn rounded-pill px-3 fw-bold {{ $filter === 'monthly' ? 'btn-primary text-white' : 'text-muted' }}">Bulanan</a>
                    <a href="?filter=yearly" class="btn rounded-pill px-3 fw-bold {{ $filter === 'yearly' ? 'btn-primary text-white' : 'text-muted' }}">Tahunan</a>
                </div>
            </div>
            <div style="height: 320px;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="panel" style="height: 420px; display:flex; flex-direction:column;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="panel-title"><i class="bi bi-cash-coin text-success me-2"></i>Pembayaran Masuk</div>
                <a href="{{ route('admin.bookings.index', ['status' => 'success']) }}" class="text-decoration-none small fw-bold text-success">
                    Lihat Semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="flex-grow-1" style="overflow-y:auto;">
                @forelse($recentPayments as $pay)
                <a href="{{ route('admin.bookings.index', ['search' => $pay->booking_id, 'status' => 'success']) }}" class="text-decoration-none">
                    <div class="pending-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <h6 class="mb-1 fw-bold text-dark">{{ $pay->booking?->customerName() ?? '-' }}</h6>
                            <span class="badge bg-success">Lunas</span>
                        </div>
                        <div class="text-muted small mb-1">ID #{{ $pay->booking_id }} • {{ ($pay->paid_at ?? $pay->created_at)->diffForHumans() }}</div>
                        <div class="fw-bold text-success">Rp {{ number_format($pay->amount ?? 0, 0, ',', '.') }}</div>
                    </div>
                </a>
                @empty
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size:2.5rem;"></i>
                    <p class="text-muted mt-2 mb-0">Belum ada pembayaran masuk.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- TOP FILM MINGGU INI + METODE PEMBAYARAN -->
<div class="row g-3">
    <div class="col-lg-8">
        <div class="panel h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="panel-title"><i class="bi bi-fire text-danger me-2"></i>Top 5 Film Minggu Ini</div>
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                    <i class="bi bi-calendar-week me-1"></i> {{ now()->startOfWeek()->translatedFormat('d M') }} – {{ now()->endOfWeek()->translatedFormat('d M') }}
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="border-bottom">
                        <tr class="text-muted small text-uppercase">
                            <th class="py-3" style="width:50px;">#</th>
                            <th class="py-3">Film</th>
                            <th class="py-3 text-center">Tiket (Minggu Ini)</th>
                            <th class="py-3 text-end">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topFilms as $i => $tf)
                        <tr>
                            <td>
                                <span class="rank-badge {{ $i == 0 ? 'rank-1' : ($i == 1 ? 'rank-2' : ($i == 2 ? 'rank-3' : 'rank-x')) }}">{{ $i + 1 }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $tf->cover_url }}" class="rounded me-3 shadow-sm" style="width: 42px; height: 56px; object-fit: cover;">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0">{{ $tf->title }}</h6>
                                        <small class="text-muted">CineTix Original</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold">
                                    {{ number_format($tf->tickets_sold, 0, ',', '.') }} tiket
                                </span>
                            </td>
                            <td class="text-end fw-bold text-success">
                                Rp {{ number_format($tf->total_revenue ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-graph-down-arrow d-block mb-2" style="font-size:2rem;"></i>
                                Belum ada tiket terjual minggu ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="panel h-100 d-flex flex-column">
            <div class="panel-title mb-3"><i class="bi bi-wallet2 text-success me-2"></i>Metode Pembayaran</div>
            <div class="flex-grow-1 d-flex align-items-center" style="min-height: 220px; position: relative;">
                @if(count($paymentCounts) > 0)
                <canvas id="paymentChart"></canvas>
                @else
                <div class="text-center w-100 text-muted">
                    <i class="bi bi-wallet d-block mb-2" style="font-size:2rem;"></i>
                    Belum ada data pembayaran.
                </div>
                @endif
            </div>
            <small class="text-muted d-block text-center mt-3"><i class="bi bi-shield-check text-success me-1"></i> Berdasarkan transaksi sukses</small>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Sales Chart
    const ctx = document.getElementById('salesChart').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 320);
    gradient.addColorStop(0, 'rgba(26, 25, 83, 0.18)');
    gradient.addColorStop(1, 'rgba(26, 25, 83, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: {!! json_encode($chartTotals) !!},
                borderColor: '#1A1953',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: '#d4b06a',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) return 'Rp ' + (value/1000000) + 'jt';
                            if (value >= 1000) return 'Rp ' + (value/1000) + 'rb';
                            return 'Rp ' + value;
                        }
                    }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // Doughnut Chart for Payment Methods
    @if(count($paymentCounts) > 0)
    const payCtx = document.getElementById('paymentChart').getContext('2d');
    new Chart(payCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($paymentLabels) !!},
            datasets: [{
                data: {!! json_encode($paymentCounts) !!},
                backgroundColor: ['#1A1953', '#d4b06a', '#28a745', '#17a2b8', '#ffc107', '#dc3545'],
                borderWidth: 3,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, padding: 14, font: { size: 11, weight: 'bold' } }
                }
            },
            cutout: '62%'
        }
    });
    @endif
</script>
@endpush
