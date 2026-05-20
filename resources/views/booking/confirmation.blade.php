@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            {{-- Success Animation/Header --}}
            <div class="text-center mb-5" data-aos="fade-up">
                <div class="d-inline-block p-4 rounded-circle bg-success bg-opacity-10 mb-4">
                    <iconify-icon icon="solar:check-circle-bold-duotone" class="text-success display-1"></iconify-icon>
                </div>
                <h2 class="fw-bold text-dark">Pemesanan Berhasil!</h2>
                <p class="text-muted">Pembayaran Anda telah dikonfirmasi dan tiket sudah siap digunakan.</p>
            </div>

            {{-- Ticket Cards List --}}
            <div class="d-flex flex-column gap-4 mb-5">
                @if($booking->ticketBookings->isNotEmpty())
                    @php
                        $firstTicket = $booking->ticketBookings->first();
                        $seatCodes = $booking->ticketBookings->map(function($tb) {
                            return $tb->seat->seat_code;
                        })->implode(', ');
                    @endphp
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden ticket-item" data-aos="zoom-in">
                        <div class="card-header border-0 py-2 px-4" style="background: #1A1953 !important;">
                            <div class="d-flex justify-content-between align-items-center text-white">
                                <div class="d-flex align-items-center gap-2">
                                    <iconify-icon icon="solar:ticket-bold-duotone" class="fs-5"></iconify-icon>
                                    <span class="fw-bold tracking-wider small">CINETIX E-TICKET</span>
                                </div>
                                <span class="small opacity-75">ID: #{{ $booking->qr_redeem }}</span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="row g-0">
                                {{-- Left Side: Film Poster --}}
                                <div class="col-md-3">
                                    <img src="{{ $firstTicket->schedule->film->cover_url }}" 
                                         alt="{{ $firstTicket->schedule->film->title }}" 
                                         class="img-fluid h-100 object-fit-fill" 
                                         style="min-height: 180px;">
                                </div>

                                {{-- Middle Side: Details --}}
                                <div class="col-md-6 p-4 bg-white border-end border-dashed">
                                    <h5 class="fw-bold text-dark mb-1">{{ $firstTicket->schedule->film->title }}</h5>
                                    <div class="mb-3">
                                        <span class="badge bg-secondary rounded-pill px-3">{{ $firstTicket->schedule->studio->name }}</span>
                                        <span class="badge bg-warning text-dark rounded-pill px-3">Kursi: {{ $seatCodes }}</span>
                                    </div>
                                    <div class="d-flex flex-column gap-1 text-muted small">
                                        <span><iconify-icon icon="lucide:calendar" class="me-1"></iconify-icon> {{ $firstTicket->schedule->schedule_date->format('d M Y') }}</span>
                                        <span><iconify-icon icon="lucide:clock" class="me-1"></iconify-icon> {{ $firstTicket->schedule->start_time->format('H:i') }} - {{ $firstTicket->schedule->end_time->format('H:i') }}</span>
                                    </div>
                                </div>

                                {{-- Right Side: QR Code --}}
                                <div class="col-md-3 bg-light p-3 d-flex flex-column align-items-center justify-content-center">
                                    <div class="p-2 bg-white rounded-3 shadow-sm mb-2">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ $booking->qr_redeem }}" alt="QR Code" class="img-fluid" style="max-width: 80px;">
                                    </div>
                                    <p class="font-monospace fw-bold text-dark mb-0 small" style="letter-spacing: 1px; font-size: 10px;">{{ $booking->qr_redeem }}</p>
                                    <span class="badge bg-success bg-opacity-10 text-success mt-2 small" style="font-size: 9px;">LUNAS</span>
                                </div>
                            </div>
                        </div>
                        {{-- Decorative circles --}}
                        <div class="ticket-cutout left" style="left: 25%;"></div>
                        <div class="ticket-cutout right" style="right: 25%;"></div>
                    </div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="d-flex flex-column gap-3 mb-5">
                <a href="{{ route('landing-page') }}" class="btn btn-outline-secondary btn-lg w-100 py-3 text-white fw-bold rounded-4">
                    Kembali ke Beranda
                </a>
            </div>

            {{-- Notice --}}
            <div class="p-4 rounded-4 border bg-light">
                <h6 class="fw-bold text-white mb-3">📌 Informasi Penting:</h6>
                <ul class="text-muted text-white small mb-0 ps-3">
                    <li class="mb-2">Harap datang 15 menit sebelum film dimulai untuk proses scan tiket.</li>
                    <li class="mb-2">Tunjukkan Kode QR atau Nomor Pemesanan di atas kepada petugas bioskop.</li>
                    <li class="mb-0">Dilarang membawa makanan dan minuman dari luar bioskop.</li>
                </ul>
            </div>
        </div>
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
    .ticket-cutout {
        position: absolute;
        width: 30px;
        height: 30px;
        background-color: #f8f9fa; /* matches body bg */
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
    }
    .ticket-cutout.left {
        left: -15px;
    }
    .ticket-cutout.right {
        right: -15px;
    }
    @media (max-width: 768px) {
        .ticket-cutout { display: none; }
        .col-md-4 { border-bottom: 2px dashed #eee !important; border-end: none !important; }
    }
</style>
@endpush
@endsection
