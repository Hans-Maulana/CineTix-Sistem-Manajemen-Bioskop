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
<div class="row g-4 mb-5">
    {{-- Card 1: Scan Kamera --}}
    <div class="col-md-6">
        <div class="card-custom h-100 p-4 border-0 shadow-sm text-center d-flex flex-column justify-content-between position-relative overflow-hidden" style="background: linear-gradient(135deg, #1A1953 0%, #2b2a7c 100%); color: white; border-radius: 20px;">
            <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10" style="background-image: radial-gradient(circle, #ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
            
            <div class="position-relative z-1 py-2">
                <div class="mx-auto mb-3 bg-white bg-opacity-20 text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; box-shadow: 0 8px 20px rgba(0,0,0,0.15);">
                    <i class="bi bi-camera-fill fs-3 text-white"></i>
                </div>
                <h4 class="fw-bold mb-2 text-white">Scan Tiket via Kamera</h4>
                
                {{-- Container Scanner --}}
                <div id="scanner-container" class="d-none mb-3">
                    <div id="reader" style="width: 100%; max-width: 320px; margin: 0 auto; border-radius: 12px; overflow: hidden; border: 2px solid rgba(255,255,255,0.2);" class="bg-dark shadow-sm"></div>
                </div>
            </div>
            
            <div class="position-relative z-1 mt-auto pt-3">
                <button type="button" id="btn-toggle-scanner" class="btn btn-light w-100 py-3 fw-bold text-primary rounded-pill shadow-sm" onclick="startScanner()" style="border-radius: 15px;">
                    <i class="bi bi-qr-code-scan me-2"></i> Mulai Scanner Kamera
                </button>
            </div>
        </div>
    </div>

    {{-- Card 2: Verifikasi Manual --}}
    <div class="col-md-6">
        <div class="card-custom h-100 p-4 border-0 shadow-sm bg-white d-flex flex-column justify-content-between" style="border-radius: 20px;">
            <div class="py-2">
                <div class="mx-auto mb-3 bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bi bi-keyboard-fill fs-3"></i>
                </div>
                <h4 class="fw-bold text-dark text-center mb-2">Input Kode Manual</h4>
                
                <form id="scan-form" action="{{ route('admin.tickets.scan') }}" method="POST" class="px-2">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small mb-2">Kode Tiket / Booking ID</label>
                        <div class="input-group input-group-lg border rounded-3 overflow-hidden" style="border-color: #dee2e6 !important;">
                            <span class="input-group-text bg-light border-0 text-muted"><i class="bi bi-upc"></i></span>
                            <input type="text" name="qr_code" class="form-control border-0 px-3 py-3 font-monospace fs-6" placeholder="Contoh: a1b2c3d4-..." required>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="mt-auto pt-3">
                <button type="submit" form="scan-form" class="btn btn-primary w-100 py-3 fw-bold rounded-pill text-white shadow-sm" style="border-radius: 15px; background: #1A1953; border: none;">
                    <i class="bi bi-check-circle-fill me-2"></i> Verifikasi Tiket
                </button>
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
                                <div class="fw-bold text-dark">{{ $schedule->schedule_date->format('d M Y') }}</div>
                                <small class="text-muted"><i class="bi bi-clock me-1"></i> {{ $schedule->start_time->format('H:i') }} WIB</small>
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
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada tiket yang terbit atau sesuai filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-top bg-white">
        {{ $bookings->links() }}
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    let html5QrcodeScanner = null;

    function startScanner() {
        const container = document.getElementById('scanner-container');
        const btn = document.getElementById('btn-toggle-scanner');
        
        container.classList.remove('d-none');
        btn.innerHTML = '<i class="bi bi-stop-circle-fill me-2"></i> Hentikan Scanner';
        btn.onclick = stopScanner;
        btn.classList.remove('btn-light', 'text-primary');
        btn.classList.add('btn-danger', 'text-white');

        html5QrcodeScanner = new Html5QrcodeScanner(
            "reader",
            { 
                fps: 10, 
                qrbox: { width: 200, height: 200 },
                rememberLastUsedCamera: true
            },
            /* verbose= */ false
        );
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    }

    function stopScanner() {
        const container = document.getElementById('scanner-container');
        const btn = document.getElementById('btn-toggle-scanner');

        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear().then(() => {
                html5QrcodeScanner = null;
                container.classList.add('d-none');
                btn.innerHTML = '<i class="bi bi-qr-code-scan me-2"></i> Mulai Scanner Kamera';
                btn.onclick = startScanner;
                btn.classList.remove('btn-danger', 'text-white');
                btn.classList.add('btn-light', 'text-primary');
            }).catch(err => {
                console.error("Gagal menghentikan scanner: ", err);
            });
        } else {
            container.classList.add('d-none');
            btn.innerHTML = '<i class="bi bi-qr-code-scan me-2"></i> Mulai Scanner Kamera';
            btn.onclick = startScanner;
            btn.classList.remove('btn-danger', 'text-white');
            btn.classList.add('btn-light', 'text-primary');
        }
    }

    function onScanSuccess(decodedText, decodedResult) {
        document.querySelector('input[name="qr_code"]').value = decodedText;
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear().then(() => {
                html5QrcodeScanner = null;
                document.getElementById('scan-form').submit();
            }).catch(err => {
                console.error("Gagal menghentikan scanner: ", err);
                document.getElementById('scan-form').submit();
            });
        } else {
            document.getElementById('scan-form').submit();
        }
    }

    function onScanFailure(error) {
        // Silently ignore scan failures
    }
</script>
@endpush

@push('styles')
<style>
    .font-monospace {
        font-family: 'JetBrains Mono', 'Courier New', monospace !important;
    }
    .border-dashed {
        border-style: dashed !important;
    }
    .card-custom {
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        background: #fff;
    }
    #reader__scan_region video {
        border-radius: 8px !important;
    }
</style>
@endpush
@endsection
