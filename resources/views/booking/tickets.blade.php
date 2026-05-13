@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h2 class="mb-0">🎟️ Tiket Aktif Saya</h2>
                <a href="{{ route('landing-page') }}" class="btn btn-outline-secondary">Kembali</a>
            </div>

            @if($bookings->isNotEmpty())
                <div class="row g-4">
                    @foreach($bookings as $booking)
                        @foreach($booking->ticketBookings as $ticket)
                            <div class="col-lg-6">
                                <div class="card shadow-sm border-0 h-100 overflow-hidden ticket-card">
                                    <div class="row g-0 h-100">
                                        <div class="col-4 bg-dark d-flex align-items-center justify-content-center p-3">
                                            <div class="text-center">
                                                <div class="bg-white p-2 rounded mb-2">
                                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ $booking->qr_redeem }}" 
                                                         alt="QR Code" class="img-fluid">
                                                </div>
                                                <small class="text-white font-monospace">{{ $booking->qr_redeem }}</small>
                                            </div>
                                        </div>
                                        <div class="col-8">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h5 class="card-title mb-0 fw-bold text-primary">{{ $ticket->schedule->film->title }}</h5>
                                                    <span class="badge bg-info">{{ $ticket->schedule->studio->name ?? 'Studio' }}</span>
                                                </div>
                                                <hr class="my-2">
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">Tanggal</small>
                                                        <span class="fw-bold small">{{ $ticket->schedule->schedule_date->format('d M Y') }}</span>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">Waktu</small>
                                                        <span class="fw-bold small">{{ $ticket->schedule->start_time }}</span>
                                                    </div>
                                                    <div class="col-6 mt-2">
                                                        <small class="text-muted d-block">Kursi</small>
                                                        <span class="badge bg-warning text-dark fs-6">{{ $ticket->seat->seat_code }}</span>
                                                    </div>
                                                    <div class="col-6 mt-2 text-end align-self-end">
                                                        <a href="{{ route('booking.confirmation', $booking) }}" class="btn btn-sm btn-link p-0 text-decoration-none">Detail Tiket →</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Ticket perforation effect --}}
                                    <div class="ticket-cutout-top"></div>
                                    <div class="ticket-cutout-bottom"></div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            @else
                <div class="card shadow-sm border-0 p-5 text-center">
                    <div class="display-1 mb-4">🎬</div>
                    <h4>Belum Ada Tiket Aktif</h4>
                    <p class="text-muted">Semua tiket yang sudah dibayar dan belum tayang akan muncul di sini.</p>
                    <div class="mt-3">
                        <a href="{{ route('landing-page') }}" class="btn btn-primary px-5 py-2">Pesan Tiket Sekarang</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .ticket-card {
        position: relative;
        border-radius: 12px !important;
        transition: transform 0.2s;
    }
    .ticket-card:hover {
        transform: scale(1.02);
    }
    .ticket-cutout-top, .ticket-cutout-bottom {
        position: absolute;
        left: 33.333%;
        width: 20px;
        height: 20px;
        background-color: #f8f9fa; /* Match background color */
        border-radius: 50%;
        margin-left: -10px;
        z-index: 2;
    }
    .ticket-cutout-top {
        top: -10px;
    }
    .ticket-cutout-bottom {
        bottom: -10px;
    }
</style>
@endsection
