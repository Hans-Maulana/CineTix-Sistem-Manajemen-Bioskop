@extends('layouts.admin')

@section('title', 'Laporan & Analitik Penjualan')

@section('content')
@php
    $queryParams = request()->only(['film_id', 'year', 'month', 'start_date', 'end_date']);
@endphp

<div class="container-fluid py-2">
    <!-- Hero Header -->
    <div class="rp-hero mb-4 d-print-none">
        <div class="row align-items-center position-relative shadow-sm" style="z-index: 2;">
            <div class="col-md-7">
                <span class="badge bg-light text-dark rounded-pill mb-2 px-3 py-2 fw-bold">
                    <i class="bi bi-bar-chart-line-fill text-primary me-1"></i> Analisis & Laporan
                </span>
                <h1 class="fw-bold mb-1">Laporan Penjualan</h1>
                <p class="mb-0 text-white-50">Pantau performa finansial, rekap penjualan tiket per film, bulanan, dan harian secara real-time.</p>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex flex-wrap justify-content-md-end gap-2">
                <button onclick="window.print()" class="btn btn-light fw-bold px-4 py-2.5 rounded-pill shadow-sm text-primary">
                    <i class="bi bi-printer-fill me-1"></i> Cetak Halaman
                </button>
            </div>
        </div>
    </div>

    <!-- Judul Khusus Cetak/Print -->
    <div class="d-none d-print-block mb-4 text-center">
        <h2 class="fw-bold text-dark mb-1">CINETIX - LAPORAN ANALISIS PENJUALAN</h2>
        <p class="text-muted">Tanggal Cetak: {{ date('d M Y, H:i') }} WIB | Diunduh oleh: {{ auth()->user()->name }}</p>
        <hr class="border-2 border-dark my-4">
    </div>

    <!-- Stats Row -->
    @if($totalBookings > 0)
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="rp-stat">
                    <div class="rp-stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div>
                        <div class="rp-stat-label">Total Pendapatan</div>
                        <div class="rp-stat-value text-success">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="rp-stat">
                    <div class="rp-stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-ticket-detailed"></i>
                    </div>
                    <div>
                        <div class="rp-stat-label">Tiket Terjual</div>
                        <div class="rp-stat-value">{{ number_format($totalTickets, 0, ',', '.') }} <span class="fs-7 text-muted fw-normal">Tiket</span></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="rp-stat">
                    <div class="rp-stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-bag-check"></i>
                    </div>
                    <div>
                        <div class="rp-stat-label">Transaksi Sukses</div>
                        <div class="rp-stat-value">{{ number_format($totalBookings, 0, ',', '.') }} <span class="fs-7 text-muted fw-normal">Booking</span></div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Filter Toolbar -->
    <div class="rp-toolbar mb-4 d-print-none">
        <form action="{{ route('admin.reports.index') }}" method="GET" class="row g-3">
            <div class="col-md-3 col-sm-6">
                <label class="form-label">Filter Film</label>
                <select name="film_id" class="form-select">
                    <option value="">Semua Film</option>
                    @foreach($films as $f)
                        <option value="{{ $f->id }}" {{ $filmId == $f->id ? 'selected' : '' }}>{{ $f->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-sm-6">
                <label class="form-label">Tahun</label>
                <select name="year" class="form-select">
                    <option value="">Semua Tahun</option>
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-sm-6">
                <label class="form-label">Bulan</label>
                <select name="month" class="form-select">
                    <option value="">Semua Bulan</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3 col-sm-6">
                <label class="form-label">Rentang Tanggal (Kustom)</label>
                <div class="input-group">
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    <span class="input-group-text bg-light border-start-0 border-end-0">-</span>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
            </div>
            <div class="col-md-2 col-sm-12 align-self-end">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-teal w-100 py-2.5">
                        <i class="bi bi-funnel-fill me-1"></i> Filter
                    </button>
                    @if($filmId || $year || $month || $startDate || $endDate)
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-light border py-2.5 px-3" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Active Filter Alert Panel -->
    @if($filmId || $year || $month || $startDate || $endDate)
        <div class="alert alert-info border-0 shadow-sm rounded-4 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3 py-3 px-4 d-print-none">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-info-circle-fill fs-5 text-primary"></i>
                <div>
                    <span class="fw-bold text-dark me-2">Menampilkan Laporan Terfilter:</span>
                    <span class="text-muted">
                        @if($filmId)
                            Film: <strong class="text-dark">{{ $films->firstWhere('id', $filmId)->title ?? 'N/A' }}</strong>;
                        @endif
                        @if($startDate && $endDate)
                            Periode: <strong class="text-dark">{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</strong>;
                        @endif
                        @if($year)
                            Tahun: <strong class="text-dark">{{ $year }}</strong>;
                            @if($month)
                                Bulan: <strong class="text-dark">{{ \Carbon\Carbon::create(null, $month, 1)->translatedFormat('F') }}</strong>;
                            @endif
                        @endif
                    </span>
                </div>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-bold shadow-sm">
                <i class="bi bi-arrow-clockwise me-1"></i> Tampilkan Semua
            </a>
        </div>
    @endif

    <!-- Main Content Area -->
    @if($totalBookings == 0)
        <!-- Empty State -->
        <div class="table-card text-center p-5 border-0 rounded-4 mt-2">
            <div class="py-5">
                <div class="mx-auto mb-4 bg-light text-muted rounded-circle d-flex align-items-center justify-content-center" style="width: 90px; height: 90px;">
                    <i class="bi bi-bar-chart-steps fs-1 text-secondary opacity-50"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">Tidak Ada Data Penjualan</h4>
                <p class="text-muted mx-auto mb-4" style="max-width: 500px;">
                    Tidak ada transaksi pembayaran lunas (confirmed) yang ditemukan untuk kriteria filter yang Anda tentukan. Silakan ubah filter Anda.
                </p>
                <div>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-teal px-4 py-2.5 rounded-pill shadow-sm">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Tabs Navigation -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 d-print-none">
            <ul class="nav rp-nav-pills" id="reportTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="daily-tab" data-bs-toggle="pill" data-bs-target="#daily-pane" type="button" role="tab" aria-controls="daily-pane" aria-selected="true">
                        <i class="bi bi-calendar3 me-1.5"></i> Laporan Harian
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="monthly-tab" data-bs-toggle="pill" data-bs-target="#monthly-pane" type="button" role="tab" aria-controls="monthly-pane" aria-selected="false">
                        <i class="bi bi-calendar-month me-1.5"></i> Laporan Bulanan & Tahunan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="film-tab" data-bs-toggle="pill" data-bs-target="#film-pane" type="button" role="tab" aria-controls="film-pane" aria-selected="false">
                        <i class="bi bi-film me-1.5"></i> Laporan per Film
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="transactions-tab" data-bs-toggle="pill" data-bs-target="#transactions-pane" type="button" role="tab" aria-controls="transactions-pane" aria-selected="false">
                        <i class="bi bi-list-columns-reverse me-1.5"></i> Detail Transaksi
                    </button>
                </li>
            </ul>
        </div>

        <!-- Tab Panes Content -->
        <div class="tab-content" id="reportTabContent">
            <!-- 1. TAB LAPORAN HARIAN -->
            <div class="tab-pane fade show active" id="daily-pane" role="tabpanel" aria-labelledby="daily-tab" tabindex="0">
                <div class="table-card">
                    <div class="table-card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h5 class="fw-bold text-primary mb-1"><i class="bi bi-calendar3 me-1 text-primary"></i> Rekap Pendapatan Harian</h5>
                            <p class="text-muted small mb-0">Daftar akumulasi transaksi sukses dan total omset per hari.</p>
                        </div>
                        <div class="d-flex gap-2 d-print-none">
                            <a href="{{ route('admin.reports.export', array_merge($queryParams, ['type' => 'daily', 'format' => 'excel'])) }}" class="btn-rp-action">
                                <i class="bi bi-file-earmark-excel-fill text-success"></i> Excel
                            </a>
                            <a href="{{ route('admin.reports.export', array_merge($queryParams, ['type' => 'daily', 'format' => 'pdf'])) }}" class="btn-rp-action">
                                <i class="bi bi-file-earmark-pdf-fill text-danger"></i> PDF
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="py-3 px-4" width="30%">Tanggal</th>
                                    <th class="py-3 text-center" width="30%">Total Transaksi</th>
                                    <th class="py-3 text-end px-4" width="40%">Total Omset Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dailyReports as $daily)
                                    <tr>
                                        <td class="px-4 py-3 fw-bold text-dark">
                                            <i class="bi bi-calendar3 me-2 text-muted"></i>
                                            {{ \Carbon\Carbon::parse($daily->booking_date)->translatedFormat('d F Y') }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill fw-bold fs-7">
                                                {{ number_format($daily->total_bookings, 0, ',', '.') }} Transaksi
                                            </span>
                                        </td>
                                        <td class="text-end px-4">
                                            <div class="d-flex align-items-center justify-content-end gap-2">
                                                <span class="fw-bold text-primary fs-6 me-2">
                                                    Rp {{ number_format($daily->total_revenue ?? 0, 0, ',', '.') }}
                                                </span>
                                                <a href="{{ route('admin.reports.index', ['start_date' => $daily->booking_date, 'end_date' => $daily->booking_date, 'tab' => 'transactions-pane']) }}" class="btn btn-sm btn-outline-primary fw-bold px-3 py-1 rounded-pill d-print-none">
                                                    <i class="bi bi-search me-1"></i> Detail Transaksi
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted">Belum ada data transaksi harian.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 2. TAB LAPORAN BULANAN & TAHUNAN -->
            <div class="tab-pane fade" id="monthly-pane" role="tabpanel" aria-labelledby="monthly-tab" tabindex="0">
                <div class="table-card">
                    <div class="table-card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h5 class="fw-bold text-primary mb-1"><i class="bi bi-calendar-month me-1 text-primary"></i> Rekap Pendapatan Bulanan</h5>
                            <p class="text-muted small mb-0">Tren pertumbuhan transaksi bisnis bioskop dan total omset per bulan.</p>
                        </div>
                        <div class="d-flex gap-2 d-print-none">
                            <a href="{{ route('admin.reports.export', array_merge($queryParams, ['type' => 'monthly', 'format' => 'excel'])) }}" class="btn-rp-action">
                                <i class="bi bi-file-earmark-excel-fill text-success"></i> Excel
                            </a>
                            <a href="{{ route('admin.reports.export', array_merge($queryParams, ['type' => 'monthly', 'format' => 'pdf'])) }}" class="btn-rp-action">
                                <i class="bi bi-file-earmark-pdf-fill text-danger"></i> PDF
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="py-3 px-4" width="30%">Bulan & Tahun</th>
                                    <th class="py-3 text-center" width="30%">Total Transaksi</th>
                                    <th class="py-3 text-end px-4" width="40%">Total Omset Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($monthlyReports as $monthly)
                                    @php
                                        $carbonDate = \Carbon\Carbon::parse($monthly->month_year . '-01');
                                        $yearVal = $carbonDate->format('Y');
                                        $monthVal = $carbonDate->format('n');
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 fw-bold text-dark">
                                            <i class="bi bi-calendar-event-fill me-2 text-primary"></i>
                                            {{ $carbonDate->translatedFormat('F Y') }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill fw-bold fs-7">
                                                {{ number_format($monthly->total_bookings, 0, ',', '.') }} Transaksi
                                            </span>
                                        </td>
                                        <td class="text-end px-4">
                                            <div class="d-flex align-items-center justify-content-end gap-2">
                                                <span class="fw-bold text-primary fs-6 me-2">
                                                    Rp {{ number_format($monthly->total_revenue ?? 0, 0, ',', '.') }}
                                                </span>
                                                <a href="{{ route('admin.reports.index', ['year' => $yearVal, 'month' => $monthVal, 'tab' => 'transactions-pane']) }}" class="btn btn-sm btn-outline-primary fw-bold px-3 py-1 rounded-pill d-print-none">
                                                    <i class="bi bi-search me-1"></i> Detail Transaksi
                                                </a>
                                            </div>
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
            </div>

            <!-- 3. TAB LAPORAN PER FILM -->
            <div class="tab-pane fade" id="film-pane" role="tabpanel" aria-labelledby="film-tab" tabindex="0">
                <div class="table-card">
                    <div class="table-card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h5 class="fw-bold text-primary mb-1"><i class="bi bi-film me-1 text-primary"></i> Rekap Penjualan per Film</h5>
                            <p class="text-muted small mb-0">Statistik jumlah tiket terjual beserta kontribusi pendapatan kotor per judul film.</p>
                        </div>
                        <div class="d-flex gap-2 d-print-none">
                            <a href="{{ route('admin.reports.export', array_merge($queryParams, ['type' => 'film', 'format' => 'excel'])) }}" class="btn-rp-action">
                                <i class="bi bi-file-earmark-excel-fill text-success"></i> Excel
                            </a>
                            <a href="{{ route('admin.reports.export', array_merge($queryParams, ['type' => 'film', 'format' => 'pdf'])) }}" class="btn-rp-action">
                                <i class="bi bi-file-earmark-pdf-fill text-danger"></i> PDF
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="py-3 px-4" width="15%">ID Film</th>
                                    <th class="py-3" width="45%">Judul Film</th>
                                    <th class="py-3 text-center" width="20%">Tiket Terjual</th>
                                    <th class="py-3 text-end px-4" width="20%">Total Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($filmReports as $film)
                                    <tr>
                                        <td class="px-4 py-3 fw-bold text-muted">#{{ $film->id }}</td>
                                        <td class="fw-bold text-dark">{{ $film->title }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold fs-7">
                                                {{ number_format($film->tickets_sold, 0, ',', '.') }} Tiket
                                            </span>
                                        </td>
                                        <td class="text-end px-4">
                                            <div class="d-flex align-items-center justify-content-end gap-2">
                                                <span class="fw-bold text-primary fs-6 me-2">
                                                    Rp {{ number_format($film->total_revenue ?? 0, 0, ',', '.') }}
                                                </span>
                                                <a href="{{ route('admin.reports.index', ['film_id' => $film->id, 'tab' => 'transactions-pane']) }}" class="btn btn-sm btn-outline-primary fw-bold px-3 py-1 rounded-pill d-print-none">
                                                    <i class="bi bi-search me-1"></i> Detail Transaksi
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">Belum ada data tiket terjual per film.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 4. TAB DETAIL TRANSAKSI -->
            <div class="tab-pane fade" id="transactions-pane" role="tabpanel" aria-labelledby="transactions-tab" tabindex="0">
                <div class="table-card">
                    <div class="table-card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h5 class="fw-bold text-primary mb-1"><i class="bi bi-list-columns-reverse me-1 text-primary"></i> Rincian Detail Transaksi</h5>
                            <p class="text-muted small mb-0">Daftar lengkap transaksi sukses (confirmed) lunas terjual.</p>
                        </div>
                        <div class="d-flex gap-2 d-print-none">
                            <a href="{{ route('admin.reports.export', array_merge($queryParams, ['type' => 'detailed', 'format' => 'excel'])) }}" class="btn-rp-action">
                                <i class="bi bi-file-earmark-excel-fill text-success"></i> Excel
                            </a>
                            <a href="{{ route('admin.reports.export', array_merge($queryParams, ['type' => 'detailed', 'format' => 'pdf'])) }}" class="btn-rp-action">
                                <i class="bi bi-file-earmark-pdf-fill text-danger"></i> PDF
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="py-3 px-4">Booking ID</th>
                                    <th class="py-3">Customer</th>
                                    <th class="py-3">Film & Penayangan</th>
                                    <th class="py-3 text-center">Kursi</th>
                                    <th class="py-3 text-end">Total Pembayaran</th>
                                    <th class="py-3">Waktu Transaksi</th>
                                    <th class="py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($detailedBookings as $booking)
                                    @php
                                        $payment = $booking->latestPayment;
                                        $payStatus = $payment?->status ?? 'pending';
                                        $schedule = $booking->ticketBookings->first()?->schedule;
                                        $film = $schedule?->film;
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 fw-bold text-dark">#{{ $booking->id }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $booking->customerName() }}</div>
                                            <div class="d-flex gap-2 mt-1">
                                                @if($booking->isGuest())
                                                    <span class="bk-tag bk-tag-guest">Guest</span>
                                                @else
                                                    <span class="bk-tag bk-tag-member">Member</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark text-truncate" style="max-width: 180px;" title="{{ $film?->title ?? '-' }}">{{ $film?->title ?? '-' }}</div>
                                            <div class="small text-muted mt-0.5">
                                                {{ $schedule?->studio->name ?? '-' }} • {{ $schedule ? $schedule->start_time->format('H:i') : '-' }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @foreach($booking->ticketBookings as $ticket)
                                                <span class="badge bg-secondary text-dark border px-1.5 py-1 text-uppercase mb-1" style="font-size: 0.75rem;">
                                                    {{ $ticket->seat->seat_code }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            <div>Rp {{ number_format($payment?->amount ?? 0, 0, ',', '.') }}</div>
                                            @if($payment?->method)
                                                <div class="small text-muted fw-normal mt-0.5">{{ $payment->method_label }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $booking->created_at->translatedFormat('d M Y') }}</div>
                                            <div class="small text-muted mt-0.5">{{ $booking->created_at->format('H:i') }} WIB</div>
                                        </td>
                                        <td class="text-center">
                                            @if($payStatus == 'success')
                                                <span class="bk-status-pill pill-success">Lunas</span>
                                            @elseif($payStatus == 'pending')
                                                <span class="bk-status-pill pill-pending">Pending</span>
                                            @else
                                                <span class="bk-status-pill pill-failed">Gagal</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">Belum ada detail transaksi yang tercatat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($detailedBookings->hasPages())
                        <div class="p-4 border-top bg-white d-print-none">
                            <div class="d-flex justify-content-center">
                                {{ $detailedBookings->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    /* ===== Custom Reports Styles ===== */
    .rp-hero {
        background: linear-gradient(120deg, #1A1953 0%, #2d2b7a 60%, #3a37a0 100%);
        border-radius: 24px;
        color: #fff;
        padding: 28px 32px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(26, 25, 83, 0.25);
    }
    .rp-hero::after {
        content: ""; position: absolute;
        right: -60px; top: -60px;
        width: 220px; height: 220px;
        background: rgba(212, 176, 106, 0.18);
        border-radius: 50%;
    }
    .rp-hero::before {
        content: "";
        position: absolute;
        right: 90px; bottom: -80px;
        width: 180px; height: 180px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }
    .rp-hero h1 { font-size: 1.75rem; font-weight: 800; margin-bottom: 4px; }
    
    .rp-stat {
        background: #fff;
        border-radius: 18px;
        padding: 18px 20px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 8px 22px rgba(26, 25, 83, 0.04);
        display: flex; align-items: center; gap: 14px;
        height: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .rp-stat:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 26px rgba(26, 25, 83, 0.08);
    }
    .rp-stat-icon {
        width: 46px; height: 46px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; flex-shrink: 0;
    }
    .rp-stat-label { font-size: 0.72rem; letter-spacing: 0.06em; font-weight: 700; text-transform: uppercase; color: #8a93a6; }
    .rp-stat-value { font-size: 1.4rem; font-weight: 800; color: #1f2533; line-height: 1.1; margin-top: 2px; }

    .rp-toolbar {
        background: #fff;
        border-radius: 18px;
        padding: 20px 24px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 6px 18px rgba(26, 25, 83, 0.04);
    }
    .rp-toolbar label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #8a93a6;
        margin-bottom: 6px;
    }
    .rp-toolbar .form-select,
    .rp-toolbar .form-control {
        border-radius: 12px;
        height: 44px;
        border: 1px solid #e6e8f0;
    }
    
    .rp-nav-pills {
        background: rgba(26, 25, 83, 0.03);
        padding: 6px;
        border-radius: 14px;
        display: inline-flex;
        border: 1px solid rgba(26, 25, 83, 0.05);
    }
    
    .rp-nav-pills .nav-link {
        color: #5d6778;
        font-weight: 700;
        border-radius: 10px;
        padding: 10px 22px;
        font-size: 0.88rem;
        transition: all 0.2s ease;
        border: 0;
        background-color: transparent;
    }
    
    .rp-nav-pills .nav-link:hover {
        color: #1A1953;
        background-color: rgba(26, 25, 83, 0.06);
    }
    
    .rp-nav-pills .nav-link.active {
        background-color: #1A1953 !important;
        color: #fff !important;
        box-shadow: 0 6px 15px rgba(26, 25, 83, 0.18) !important;
    }
    
    .table-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 10px 30px rgba(26, 25, 83, 0.03);
        overflow: hidden;
    }
    
    .table-card-header {
        padding: 20px 24px;
        background: #fff;
        border-bottom: 1px solid #eef0f7;
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

    .btn-rp-action {
        background: #f3f4fa;
        color: #1A1953;
        border: 1px solid transparent;
        border-radius: 12px;
        font-weight: 700;
        padding: 10px 18px;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-rp-action:hover {
        background: #e9ebf5;
        color: #1A1953;
    }

    @media print {
        body { background-color: white !important; color: black !important; }
        .topbar, .menu-bar, .d-print-none, .rp-nav-pills { display: none !important; }
        .table-card { box-shadow: none !important; border: 1px solid #ddd !important; margin-bottom: 30px !important; }
        .table { width: 100% !important; border-collapse: collapse !important; }
        .table th, .table td { border: 1px solid #ddd !important; padding: 10px !important; }
        .badge { border: 1px solid #000 !important; color: #000 !important; background: transparent !important; }
        .tab-pane { display: block !important; opacity: 1 !important; }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Auto activate the correct tab if pagination or specific params exist
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('page') || urlParams.has('tab')) {
            const targetPaneId = urlParams.get('tab') || 'transactions-pane';
            const tabBtn = document.querySelector(`button[data-bs-target="#${targetPaneId}"]`);
            if (tabBtn) {
                const tab = new bootstrap.Tab(tabBtn);
                tab.show();
            }
        }

        // Store active tab in pagination links dynamically
        const tabButtons = document.querySelectorAll('button[data-bs-toggle="pill"]');
        tabButtons.forEach(btn => {
            btn.addEventListener('shown.bs.tab', function(e) {
                const paneId = e.target.getAttribute('data-bs-target').replace('#', '');
                // Update pagination links with the active tab query param
                const pagLinks = document.querySelectorAll('.pagination a');
                pagLinks.forEach(link => {
                    const url = new URL(link.href);
                    url.searchParams.set('tab', paneId);
                    link.href = url.href;
                });
            });
        });
        
        // Initial setup for existing pagination links
        const activeTab = document.querySelector('button[data-bs-toggle="pill"].active');
        if (activeTab) {
            const paneId = activeTab.getAttribute('data-bs-target').replace('#', '');
            const pagLinks = document.querySelectorAll('.pagination a');
            pagLinks.forEach(link => {
                const url = new URL(link.href);
                url.searchParams.set('tab', paneId);
                link.href = url.href;
            });
        }
    });
</script>
@endpush

@endsection
