@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h1 class="fw-bold text-primary">Overview</h1>
        <p class="text-muted">Pantau performa bioskop kamu hari ini.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="{{ route('admin.films.create') }}" class="btn-teal text-decoration-none">
            <i class="bi bi-plus-lg"></i> Tambah Film Baru
        </a>
    </div>
</div>

<!-- STATS -->
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card-custom h-100 p-4 border-start border-primary border-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text-muted small fw-bold text-uppercase">Total Film</div>
                <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-film fs-5"></i></div>
            </div>
            <div class="display-6 fw-bold text-primary">{{ $totalFilms }}</div>
            <div class="text-success small mt-2"><i class="bi bi-check-circle-fill me-1"></i> Tersedia di katalog</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom h-100 p-4 border-start border-success border-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text-muted small fw-bold text-uppercase">Total Pendapatan</div>
                <div class="p-2 bg-success bg-opacity-10 text-success rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-cash-stack fs-5"></i></div>
            </div>
            <div class="display-6 fw-bold text-success">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <div class="text-muted small mt-2"><i class="bi bi-shield-check-fill me-1"></i> Transaksi Sukses</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom h-100 p-4 border-start border-warning border-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text-muted small fw-bold text-uppercase">Tiket Terjual</div>
                <div class="p-2 bg-warning bg-opacity-10 text-warning rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-ticket-detailed fs-5"></i></div>
            </div>
            <div class="display-6 fw-bold text-warning">{{ number_format($totalTicketsSold, 0, ',', '.') }}</div>
            <div class="text-muted small mt-2"><i class="bi bi-people-fill me-1"></i> Tiket terverifikasi</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom h-100 p-4 border-start border-info border-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text-muted small fw-bold text-uppercase">Transaksi Lunas</div>
                <div class="p-2 bg-info bg-opacity-10 text-info rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-bag-check fs-5"></i></div>
            </div>
            <div class="display-6 fw-bold text-info">{{ $totalBookings }}</div>
            <div class="text-muted small mt-2"><i class="bi bi-cart-check-fill me-1"></i> Pesanan terkonfirmasi</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom h-100 p-4 border-start border-secondary border-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text-muted small fw-bold text-uppercase">Total Member</div>
                <div class="p-2 bg-secondary bg-opacity-10 text-secondary rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-person-check fs-5"></i></div>
            </div>
            <div class="display-6 fw-bold text-secondary">{{ $totalCustomers }}</div>
            <div class="text-muted small mt-2"><i class="bi bi-patch-check-fill me-1"></i> Pelanggan terdaftar</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom h-100 p-4 border-start border-danger border-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text-muted small fw-bold text-uppercase">Jadwal Aktif</div>
                <div class="p-2 bg-danger bg-opacity-10 text-danger rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-calendar-check fs-5"></i></div>
            </div>
            <div class="display-6 fw-bold text-danger">{{ $totalActiveSchedules }}</div>
            <div class="text-muted small mt-2"><i class="bi bi-clock-history me-1"></i> Penayangan mendatang</div>
        </div>
    </div>
</div>

<div class="row">
    <!-- SALES CHART -->
    <div class="col-lg-8">
        <div class="card-custom" style="height: 400px;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">
                    Grafik Penjualan 
                    @if($filter === 'yearly')
                        (12 Bulan Terakhir)
                    @elseif($filter === 'monthly')
                        (30 Hari Terakhir)
                    @else
                        (7 Hari Terakhir)
                    @endif
                </h5>
                <div class="btn-group btn-group-sm rounded-pill p-1 bg-light shadow-sm" role="group">
                    <a href="?filter=weekly" class="btn rounded-pill px-3 fw-bold {{ $filter === 'weekly' ? 'btn-primary text-white bg-primary' : 'btn-light text-muted' }}">Mingguan</a>
                    <a href="?filter=monthly" class="btn rounded-pill px-3 fw-bold {{ $filter === 'monthly' ? 'btn-primary text-white bg-primary' : 'btn-light text-muted' }}">Bulanan</a>
                    <a href="?filter=yearly" class="btn rounded-pill px-3 fw-bold {{ $filter === 'yearly' ? 'btn-primary text-white bg-primary' : 'btn-light text-muted' }}">Tahunan</a>
                </div>
            </div>
            <div style="height: 300px;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- RECENT BOOKINGS / NOTIF -->
    <div class="col-lg-4">
        <div class="card-custom" style="height: 400px; overflow-y: auto;">
            <h5 class="fw-bold mb-4">Booking Terbaru (Pending)</h5>
            <div class="list-group list-group-flush">
                @forelse($recentBookings as $rb)
                <div class="list-group-item px-0 border-0 mb-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-1 fw-bold">{{ $rb->customerName() }}</h6>
                        <span class="badge bg-warning text-dark small">Pending</span>
                    </div>
                    <p class="mb-1 text-muted small">ID: #{{ $rb->id }} • {{ $rb->created_at->diffForHumans() }}</p>
                    <small class="fw-bold text-primary">Rp {{ number_format($rb->payments->first()?->amount ?? 0, 0, ',', '.') }}</small>
                </div>
                @empty
                <div class="text-center py-4">
                    <p class="text-muted italic">Tidak ada booking baru</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- THIRD ROW: TOP FILMS & PAYMENT METHODS -->
<div class="row mt-5 g-4">
    <!-- TOP SELLING FILMS -->
    <div class="col-lg-8">
        <div class="card-custom h-100 shadow-sm p-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-fire text-danger me-2"></i> Tren Film Terpopuler (Top 5)</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light border-bottom">
                        <tr>
                            <th class="py-3 px-3">Film</th>
                            <th class="py-3 text-center">Tiket Terjual</th>
                            <th class="py-3 text-end px-3">Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topFilms as $tf)
                        <tr>
                            <td class="py-2">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $tf->cover_url }}" class="rounded me-3 shadow-sm" style="width: 45px; height: 60px; object-fit: cover;">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0">{{ $tf->title }}</h6>
                                        <small class="text-muted">CineTix Top Movies</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold">
                                    {{ number_format($tf->tickets_sold, 0, ',', '.') }} Tiket
                                </span>
                            </td>
                            <td class="text-end px-3 fw-bold text-primary">
                                Rp {{ number_format($tf->total_revenue ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">Belum ada data penjualan film.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PAYMENT METHODS CHART -->
    <div class="col-lg-4">
        <div class="card-custom h-100 shadow-sm p-4 d-flex flex-column justify-content-between">
            <div>
                <h5 class="fw-bold mb-4"><i class="bi bi-wallet2 text-success me-2"></i> Metode Pembayaran</h5>
                <div style="height: 220px; position: relative;">
                    <canvas id="paymentChart"></canvas>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-muted d-block text-center"><i class="bi bi-shield-check text-success me-1"></i> Data diperbarui secara real-time</small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Sales Chart
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: {!! json_encode($chartTotals) !!},
                borderColor: '#1A1953',
                backgroundColor: 'rgba(26, 25, 83, 0.08)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#1A1953',
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
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // Doughnut Chart for Payment Methods
    const payCtx = document.getElementById('paymentChart').getContext('2d');
    new Chart(payCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($paymentLabels) !!},
            datasets: [{
                data: {!! json_encode($paymentCounts) !!},
                backgroundColor: [
                    '#1A1953', // Deep Navy
                    '#28a745', // Success Green
                    '#ffc107'  // Warning Yellow
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        font: { size: 11, weight: 'bold' }
                    }
                }
            },
            cutout: '65%'
        }
    });
</script>
@endpush
