@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="text-center mb-4">
                <h2 class="fw-bold text-dark">Tiket Anda Siap!</h2>
                <p class="text-muted mb-0">
                    Salinan tiket telah dikirim ke <strong>{{ $booking->guest_email }}</strong>
                </p>
            </div>

            @php
                $firstTicket = $booking->ticketBookings->first();
                $film = $firstTicket?->schedule?->film;
                $schedule = $firstTicket?->schedule;
                $seatCodes = $booking->ticketBookings->map(fn ($t) => $t->seat->seat_code)->implode(', ');
            @endphp

            @if($firstTicket)
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mx-auto" style="max-width: 440px;">
                <div class="p-4 text-white" style="background: #112a46;">
                    <h4 class="fw-bold mb-3" style="color: #f2bd52;">{{ $film?->title ?? 'FILM' }}</h4>
                    <div class="row small g-2">
                        <div class="col-6">
                            <div class="text-white-50">Tanggal</div>
                            <div class="fw-bold">{{ $schedule->schedule_date->format('d M Y') }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-white-50">Jam</div>
                            <div class="fw-bold">{{ $schedule->start_time->format('H:i') }}</div>
                        </div>
                        <div class="col-12">
                            <div class="text-white-50">Studio</div>
                            <div class="fw-bold">{{ $schedule->studio->name }}</div>
                        </div>
                    </div>
                </div>
                <div class="p-4 text-center" style="background: #f2bd52;">
                    <div class="mb-3">
                        <div class="small text-dark opacity-75">Kursi</div>
                        <div class="fs-4 fw-bold text-dark">{{ $seatCodes }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-dark opacity-75">ID Booking</div>
                        <span class="badge bg-white text-dark px-3 py-2 font-monospace">{{ $booking->qr_redeem }}</span>
                    </div>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($booking->qr_redeem) }}"
                         alt="QR Code" class="img-fluid rounded-3 border border-3 border-white shadow">
                    <p class="small text-dark mt-3 mb-0">Tunjukkan QR ini di pintu masuk bioskop</p>
                </div>
            </div>
            @endif

            <div class="text-center mt-4">
                <a href="{{ route('landing-page') }}" class="btn btn-primary px-5 py-2 text-white fw-bold rounded-pill" style="background: #1A1953;">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
