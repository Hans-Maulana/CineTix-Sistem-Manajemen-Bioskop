@extends('layouts.admin')

@section('title', 'Manajemen Tiket & Scanner')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <h1 class="fw-bold text-primary">Manajemen Tiket & Scanner</h1>
        <p class="text-muted">Verifikasi keaslian e-tiket via Kode QR dan pantau status penggunaan tiket penonton.</p>
    </div>
</div>

{{-- Bagian Scanner / Verifikasi --}}
<div class="row mb-5">
    <div class="col-md-12">
        <div class="card-custom bg-primary bg-opacity-10 border-2 border-primary border-dashed p-4 rounded-4 shadow-sm">
            <div class="row align-items-center g-4">
                <div class="col-md-4 text-center text-md-start">
                    <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-3">
                        <div class="p-3 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="bi bi-qr-code-scan fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-primary mb-1">Verifikasi E-Tiket</h5>
                            <span class="text-muted small">Scan QR atau masukkan kode manual</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <form action="{{ route('admin.tickets.scan') }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-white border-0 text-primary"><i class="bi bi-upc-scan"></i></span>
                            <input type="text" name="qr_code" class="form-control border-0 px-3 py-3 font-monospace" placeholder="Masukkan / Scan Kode QR Tiket (Contoh: a1b2c3d4-...)" required autofocus>
                            <button type="submit" class="btn btn-primary px-4 fw-bold"><i class="bi bi-check-circle-fill me-2"></i> Verifikasi Tiket</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Bagian Filter & Cari --}}
<div class="card-custom mb-4 p-4">
    <form action="{{ route('admin.tickets.index') }}" method="GET" class="row g-3 align-items-center">
        <div class="col-md-4">
            <label class="form-label fw-bold text-muted small mb-1">Filter Film</label>
            <select name="film_id" class="form-select shadow-sm" onchange="this.form.submit()">
                <option value="">Semua Film</option>
                @foreach($films as $film)
                    <option value="{{ $film->id }}" {{ request('film_id') == $film->id ? 'selected' : '' }}>{{ $film->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold text-muted small mb-1">Cari Tiket</label>
            <div class="input-group shadow-sm rounded-3 overflow-hidden">
                <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-0" placeholder="Cari nama customer, email, atau Kode QR..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-2 align-self-end">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm py-2">Terapkan</button>
                @if(request('film_id') || request('search'))
                    <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary py-2"><i class="bi bi-arrow-clockwise"></i></a>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- Tabel Daftar Tiket --}}
<div class="card-custom p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light border-bottom">
                <tr>
                    <th class="py-3 px-4">Kode QR / Booking ID</th>
                    <th class="py-3">Customer</th>
                    <th class="py-3">Film & Studio</th>
                    <th class="py-3">Jadwal Tayang</th>
                    <th class="py-3">Kursi</th>
                    <th class="py-3">Status Tiket</th>
                    <th class="py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    @php
                        $schedule = $booking->ticketBookings->first()?->schedule;
                    @endphp
                    <tr>
                        <td class="px-4 py-3 font-monospace small fw-bold text-primary">
                            {{ $booking->qr_redeem }}
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $booking->customerName() }}</div>
                            <small class="text-muted">{{ $booking->customerEmail() ?? '-' }}</small>
                            @if($booking->isGuest())
                                <span class="badge bg-info text-dark mt-1">Guest</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $schedule?->film->title ?? 'N/A' }}</div>
                            <span class="badge bg-secondary small">{{ $schedule?->studio->name ?? '-' }}</span>
                        </td>
                        <td>
                            @if($schedule)
                                <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($schedule->showtime)->format('d M Y') }}</div>
                                <small class="text-muted"><i class="bi bi-clock me-1"></i> {{ \Carbon\Carbon::parse($schedule->showtime)->format('H:i') }} WIB</small>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($booking->ticketBookings as $ticket)
                                    <span class="badge bg-dark">{{ $ticket->seat->seat_code }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td>
                            @if($booking->status_redeem === 'redeemed')
                                <span class="badge bg-success px-3 py-2 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i> Telah Digunakan</span>
                            @else
                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i class="bi bi-ticket-perforated-fill me-1"></i> Belum Digunakan</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($booking->status_redeem === 'unredeemed')
                                <form action="{{ route('admin.tickets.scan') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="qr_code" value="{{ $booking->qr_redeem }}">
                                    <button type="submit" class="btn btn-sm btn-outline-success fw-bold px-3 rounded-pill shadow-sm" onclick="return confirm('Tandai tiket ini sebagai Telah Digunakan?')">
                                        <i class="bi bi-qr-code-scan me-1"></i> Check-In
                                    </button>
                                </form>
                            @else
                                <span class="text-muted small"><i class="bi bi-check-all fs-5 text-success"></i> Selesai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Belum ada tiket yang terbit atau sesuai filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-top bg-white">
        {{ $bookings->links() }}
    </div>
</div>

@push('styles')
<style>
    .font-monospace {
        font-family: 'JetBrains Mono', 'Courier New', monospace !important;
    }
    .border-dashed {
        border-style: dashed !important;
    }
</style>
@endpush
@endsection
