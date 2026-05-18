@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h2 class="mb-0">🎟️ Tiket Aktif Saya</h2>
                <a href="{{ route('landing-page') }}" class="btn btn-outline-secondary text-white">Kembali</a>
            </div>

            @if($bookings->isNotEmpty())
                <div class="row g-4">
                    @foreach($bookings as $booking)
                        @php
                            $firstTicket = $booking->ticketBookings->first();
                            $film = $firstTicket ? $firstTicket->schedule->film : null;
                            $seatCodes = $booking->ticketBookings->pluck('seat.seat_code')->implode(', ');
                        @endphp
                        <div class="col-lg-6">
                            <div class="card shadow-sm border-0 h-100 overflow-hidden ticket-card" style="border-radius: 15px !important;">
                                <div class="row g-0 h-100">
                                    <div class="col-4">
                                        <img src="{{ $film ? $film->cover_url : asset('storage/cover/default-cover.svg') }}" 
                                             alt="{{ $film->title ?? 'Film' }}" 
                                             class="img-fluid h-100 object-fit-cover"
                                             style="min-height: 160px;">
                                    </div>
                                    <div class="col-8">
                                        <div class="card-body d-flex flex-column h-100">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="fw-bold text-primary mb-0 text-truncate" style="max-width: 150px;">{{ $film->title ?? 'Film' }}</h6>
                                                <span class="badge bg-info small">{{ $firstTicket->schedule->studio->name ?? 'Studio' }}</span>
                                            </div>
                                            
                                            <div class="row g-1 mb-2">
                                                <div class="col-6">
                                                    <small class="text-muted d-block" style="font-size: 10px;">Tanggal</small>
                                                    <span class="fw-bold small" style="font-size: 11px;">{{ $firstTicket->schedule->schedule_date->format('d M Y') }}</span>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted d-block" style="font-size: 10px;">Kursi</small>
                                                    <span class="fw-bold text-warning small" style="font-size: 11px;">{{ $seatCodes }}</span>
                                                </div>
                                            </div>

                                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                                <div class="bg-light p-1 rounded border">
                                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data={{ $booking->qr_redeem }}" 
                                                         alt="QR" class="img-fluid" style="width: 40px;">
                                                </div>
                                                <a href="{{ route('booking.confirmation', $booking) }}" class="btn btn-sm btn-primary px-3 rounded-pill text-white text-decoration-none shadow-sm">Detail Tiket</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Ticket perforation effect --}}
                                <div class="ticket-cutout-top" style="left: 33.333%;"></div>
                                <div class="ticket-cutout-bottom" style="left: 33.333%;"></div>
                            </div>
                        </div>
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
