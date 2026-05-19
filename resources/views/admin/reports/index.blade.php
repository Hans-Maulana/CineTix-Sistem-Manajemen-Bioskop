@extends('layouts.admin')

@section('title', 'Laporan & Analitik Penjualan')

@section('content')
<div class="row mb-4 align-items-center d-print-none">
    <div class="col-md-5">
        <h1 class="fw-bold text-primary mb-1">Laporan & Analitik Penjualan</h1>
        <p class="text-muted mb-0">Pantau performa finansial, rekap penjualan tiket per film, dan tren bulanan.</p>
    </div>
    <div class="col-md-7 text-md-end mt-3 mt-md-0 d-flex flex-wrap justify-content-md-end gap-2">
        <button onclick="window.print()" class="btn btn-outline-primary px-4 py-2 shadow-sm fw-bold">
            <i class="bi bi-printer-fill me-2"></i> Cetak Laporan
        </button>
        <div class="btn-group shadow-sm">
            <button type="button" class="btn btn-success px-4 py-2 fw-bold dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-file-earmark-excel-fill me-2"></i> Export Excel
            </button>
            <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                <li><a class="dropdown-item py-2 fw-medium" href="{{ route('admin.reports.export', ['type' => 'film', 'format' => 'excel']) }}"><i class="bi bi-film me-2 text-success"></i> Laporan per Film (Excel)</a></li>
                <li><a class="dropdown-item py-2 fw-medium" href="{{ route('admin.reports.export', ['type' => 'monthly', 'format' => 'excel']) }}"><i class="bi bi-calendar-month me-2 text-success"></i> Laporan Bulanan (Excel)</a></li>
            </ul>
        </div>
        <div class="btn-group shadow-sm">
            <button type="button" class="btn btn-danger px-4 py-2 fw-bold dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-file-earmark-pdf-fill me-2"></i> Export PDF
            </button>
            <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                <li><a class="dropdown-item py-2 fw-medium" href="{{ route('admin.reports.export', ['type' => 'film', 'format' => 'pdf']) }}"><i class="bi bi-film me-2 text-danger"></i> Laporan per Film (PDF)</a></li>
                <li><a class="dropdown-item py-2 fw-medium" href="{{ route('admin.reports.export', ['type' => 'monthly', 'format' => 'pdf']) }}"><i class="bi bi-calendar-month me-2 text-danger"></i> Laporan Bulanan (PDF)</a></li>
            </ul>
        </div>
    </div>
</div>

{{-- Judul Khusus Print --}}
<div class="d-none d-print-block mb-4 text-center">
    <h2 class="fw-bold text-dark mb-1">CINETIX - LAPORAN PENJUALAN TIKET</h2>
    <p class="text-muted">Tanggal Cetak: {{ date('d M Y, H:i') }} WIB</p>
    <hr class="border-2 border-dark my-4">
</div>

{{-- Summary Cards --}}
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card-custom bg-primary text-white p-4 rounded-4 shadow-sm h-100 d-flex flex-column justify-content-between position-relative overflow-hidden">
            <div>
                <span class="text-white text-opacity-75 small fw-bold text-uppercase tracking-wider">Total Pendapatan Bersih</span>
                <h2 class="fw-bold mb-0 mt-2 display-6">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h2>
            </div>
            <div class="mt-4 pt-3 border-top border-white border-opacity-25 d-flex justify-content-between align-items-center">
                <span class="small text-white text-opacity-75"><i class="bi bi-shield-check me-1"></i> Terverifikasi & Lunas</span>
                <i class="bi bi-wallet2 fs-1 text-white text-opacity-25 position-absolute bottom-0 end-0 me-3 mb-3"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom bg-white p-4 rounded-4 shadow-sm h-100 d-flex flex-column justify-content-between position-relative overflow-hidden border border-light">
            <div>
                <span class="text-muted small fw-bold text-uppercase tracking-wider">Total Tiket Terjual</span>
                <h2 class="fw-bold text-dark mb-0 mt-2 display-6">{{ number_format($totalTickets, 0, ',', '.') }} <span class="fs-5 text-muted fw-normal">Tiket</span></h2>
            </div>
            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                <span class="small text-success fw-bold"><i class="bi bi-graph-up-arrow me-1"></i> Performa Aktif</span>
                <i class="bi bi-ticket-detailed fs-1 text-primary opacity-10 position-absolute bottom-0 end-0 me-3 mb-3"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom bg-white p-4 rounded-4 shadow-sm h-100 d-flex flex-column justify-content-between position-relative overflow-hidden border border-light">
            <div>
                <span class="text-muted small fw-bold text-uppercase tracking-wider">Total Transaksi Sukses</span>
                <h2 class="fw-bold text-dark mb-0 mt-2 display-6">{{ number_format($totalBookings, 0, ',', '.') }} <span class="fs-5 text-muted fw-normal">Booking</span></h2>
            </div>
            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                <span class="small text-primary fw-bold"><i class="bi bi-cart-check-fill me-1"></i> Pembayaran Berhasil</span>
                <i class="bi bi-bag-check fs-1 text-primary opacity-10 position-absolute bottom-0 end-0 me-3 mb-3"></i>
            </div>
        </div>
    </div>
</div>

{{-- Section 1: Rekap per Film --}}
<div class="card-custom p-0 overflow-hidden mb-5 shadow-sm">
    <div class="p-4 bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap g-2">
        <div>
            <h5 class="fw-bold text-primary mb-1">Rekap Penjualan per Film</h5>
            <p class="text-muted small mb-0">Rincian performa jumlah tiket terjual dan kontribusi pendapatan dari masing-masing judul film.</p>
        </div>
        <div class="d-flex gap-2 d-print-none">
            <a href="{{ route('admin.reports.export', ['type' => 'film', 'format' => 'excel']) }}" class="btn btn-sm btn-outline-success fw-bold px-3 py-2 rounded-pill shadow-sm">
                <i class="bi bi-file-earmark-excel-fill me-1"></i> Excel
            </a>
            <a href="{{ route('admin.reports.export', ['type' => 'film', 'format' => 'pdf']) }}" class="btn btn-sm btn-outline-danger fw-bold px-3 py-2 rounded-pill shadow-sm">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> PDF
            </a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light border-bottom">
                <tr>
                    <th class="py-3 px-4" width="80">ID</th>
                    <th class="py-3">Judul Film</th>
                    <th class="py-3 text-center" width="200">Tiket Terjual</th>
                    <th class="py-3 text-end px-4" width="250">Total Pendapatan (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($filmReports as $film)
                    <tr>
                        <td class="px-4 py-3 fw-bold text-muted">#{{ $film->id }}</td>
                        <td class="fw-bold text-dark">{{ $film->title }}</td>
                        <td class="text-center">
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold fs-6">
                                {{ number_format($film->tickets_sold, 0, ',', '.') }} Tiket
                            </span>
                        </td>
                        <td class="text-end px-4 fw-bold text-primary fs-6">
                            Rp {{ number_format($film->total_revenue ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">Belum ada data penjualan film.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Section 2: Rekap per Bulan & Tahun --}}
<div class="card-custom p-0 overflow-hidden shadow-sm mb-4">
    <div class="p-4 bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap g-2">
        <div>
            <h5 class="fw-bold text-primary mb-1">Rekap Penjualan Bulanan & Tahunan</h5>
            <p class="text-muted small mb-0">Rangkuman tren total transaksi dan pertumbuhan omset per bulan.</p>
        </div>
        <div class="d-flex gap-2 d-print-none">
            <a href="{{ route('admin.reports.export', ['type' => 'monthly', 'format' => 'excel']) }}" class="btn btn-sm btn-outline-success fw-bold px-3 py-2 rounded-pill shadow-sm">
                <i class="bi bi-file-earmark-excel-fill me-1"></i> Excel
            </a>
            <a href="{{ route('admin.reports.export', ['type' => 'monthly', 'format' => 'pdf']) }}" class="btn btn-sm btn-outline-danger fw-bold px-3 py-2 rounded-pill shadow-sm">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> PDF
            </a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light border-bottom">
                <tr>
                    <th class="py-3 px-4" width="250">Bulan & Tahun</th>
                    <th class="py-3 text-center" width="250">Total Transaksi (Booking)</th>
                    <th class="py-3 text-end px-4">Total Omset Pendapatan (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($monthlyReports as $monthly)
                    <tr>
                        <td class="px-4 py-3 fw-bold text-dark">
                            <i class="bi bi-calendar-event me-2 text-primary"></i> {{ \Carbon\Carbon::parse($monthly->month_year . '-01')->translatedFormat('F Y') }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary px-3 py-2 rounded-pill fw-bold fs-6">
                                {{ number_format($monthly->total_bookings, 0, ',', '.') }} Transaksi
                            </span>
                        </td>
                        <td class="text-end px-4 fw-bold text-primary fs-6">
                            Rp {{ number_format($monthly->total_revenue ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-5 text-muted">Belum ada data transaksi bulanan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('styles')
<style>
    @media print {
        body { background-color: white !important; color: black !important; }
        .topbar, .menu-bar, .d-print-none { display: none !important; }
        .card-custom { box-shadow: none !important; border: 1px solid #ddd !important; margin-bottom: 30px !important; }
        .table { width: 100% !important; border-collapse: collapse !important; }
        .table th, .table td { border: 1px solid #ddd !important; padding: 10px !important; }
        .badge { border: 1px solid #000 !important; color: #000 !important; background: transparent !important; }
    }
</style>
@endpush
@endsection
