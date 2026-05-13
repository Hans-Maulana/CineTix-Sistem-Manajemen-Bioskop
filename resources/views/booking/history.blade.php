@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h2 class="mb-0">📜 Daftar Transaksi</h2>
                <a href="{{ route('landing-page') }}" class="btn btn-outline-secondary">Kembali</a>
            </div>

            @if($bookings->isNotEmpty())
                <div class="d-flex flex-column gap-4">
                    @foreach($bookings as $booking)
                        @php
                            $firstTicket = $booking->ticketBookings->first();
                            $film = $firstTicket ? $firstTicket->schedule->film : null;
                        @endphp
                        <div class="card shadow-sm border-0 overflow-hidden transaction-card" 
                             style="cursor: pointer;" 
                             onclick="window.location.href='{{ route('booking.confirmation', $booking) }}'">
                            <div class="row g-0">
                                <div class="col-md-2">
                                    <img src="{{ $film ? $film->cover_url : asset('storage/cover/default-cover.svg') }}" 
                                         alt="{{ $film->title ?? 'Film' }}" 
                                         class="img-fluid h-100 object-fit-cover" 
                                         style="min-height: 150px; aspect-ratio: 2/3;">
                                </div>
                                <div class="col-md-10">
                                    <div class="card-body d-flex flex-column h-100">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h5 class="card-title mb-1 text-primary fw-bold">{{ $film->title ?? 'Transaksi CineTix' }}</h5>
                                                <p class="text-muted small mb-0">
                                                    <iconify-icon icon="lucide:calendar" class="me-1"></iconify-icon>
                                                    {{ $booking->created_at->format('d M Y, H:i') }}
                                                </p>
                                            </div>
                                            <span class="badge {{ $booking->status === 'confirmed' ? 'bg-success' : ($booking->status === 'pending' ? 'bg-warning' : 'bg-danger') }} px-3 py-2">
                                                {{ $booking->status === 'confirmed' ? 'Selesai' : ($booking->status === 'pending' ? 'Menunggu' : 'Dibatalkan') }}
                                            </span>
                                        </div>

                                        <div class="row mt-auto">
                                            <div class="col-sm-3">
                                                <small class="text-muted d-block">Tipe</small>
                                                <span class="fw-bold">{{ ucfirst($booking->booking_type) }}</span>
                                            </div>
                                            <div class="col-sm-3">
                                                <small class="text-muted d-block">Jumlah Kursi</small>
                                                <span class="fw-bold">{{ $booking->ticketBookings->count() }} Kursi</span>
                                            </div>
                                            <div class="col-sm-3">
                                                <small class="text-muted d-block">Total Bayar</small>
                                                <span class="fw-bold text-dark">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="col-sm-3 text-end">
                                                @if($booking->status === 'pending')
                                                    <a href="{{ route('booking.payment', $booking) }}" class="btn btn-sm btn-success px-4">Bayar</a>
                                                @else
                                                    <span class="text-primary small fw-bold">Lihat Detail <iconify-icon icon="lucide:chevron-right"></iconify-icon></span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-5">
                    {{ $bookings->links() }}
                </div>
            @else
                <div class="card shadow-sm border-0 p-5 text-center">
                    <div class="display-1 mb-4">🛒</div>
                    <h4>Belum Ada Transaksi</h4>
                    <p class="text-muted">Sepertinya Anda belum pernah melakukan pemesanan.</p>
                    <div class="mt-3">
                        <a href="{{ route('landing-page') }}" class="btn btn-primary px-5 py-2">Mulai Pesan Sekarang</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .transaction-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .transaction-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
</style>
@endsection
