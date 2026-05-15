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
        <div class="card-custom">
            <div class="text-muted small fw-bold text-uppercase mb-2">Total Film</div>
            <div class="display-6 fw-bold text-primary">{{ $totalFilms }}</div>
            <div class="text-success small mt-2">↑ Tersedia di katalog</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom">
            <div class="text-muted small fw-bold text-uppercase mb-2">Total Transaksi</div>
            <div class="display-6 fw-bold text-primary">{{ $totalBookings }}</div>
            <div class="text-primary small mt-2">Total pesanan tiket</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom">
            <div class="text-muted small fw-bold text-uppercase mb-2">Total Member</div>
            <div class="display-6 fw-bold text-primary">{{ $totalCustomers }}</div>
            <div class="text-warning small mt-2">User terdaftar</div>
        </div>
    </div>
</div>

<div class="row">
    <!-- SALES CHART -->
    <div class="col-lg-8">
        <div class="card-custom" style="height: 400px;">
            <h5 class="fw-bold mb-4">Grafik Penjualan (7 Hari Terakhir)</h5>
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
                        <h6 class="mb-1 fw-bold">{{ $rb->user->name }}</h6>
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

<!-- MOVIES -->
<h3 class="fw-bold mb-4 mt-5">Now Playing</h3>
<div class="row g-4">
    @foreach($films as $film)
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100" style="background: #1a1a1a;">
            <img src="{{ $film->cover_url }}" class="w-100" style="height: 350px; object-fit: contain; background: #000;" alt="{{ $film->title }}">
            <div class="p-4 bg-white">
                <h5 class="fw-bold mb-1 text-truncate">{{ $film->title }}</h5>
                <p class="text-muted small mb-3 text-truncate">
                    {{ $film->genres->pluck('name')->implode(', ') }}
                </p>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.films.edit', $film) }}" class="btn btn-outline-warning flex-grow-1 rounded-3">Edit</a>
                    <form action="{{ route('admin.films.destroy', $film) }}" method="POST" class="flex-grow-1" onsubmit="return confirm('Yakin ingin menghapus film ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100 rounded-3">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: {!! json_encode($chartTotals) !!},
                borderColor: '#1A1953',
                backgroundColor: 'rgba(26, 25, 83, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#1A1953'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endpush
