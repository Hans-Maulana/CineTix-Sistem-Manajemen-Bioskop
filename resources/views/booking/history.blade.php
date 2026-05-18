@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h2 class="mb-0">📜 Daftar Transaksi</h2>
                <a href="{{ route('landing-page') }}" class="btn btn-outline-secondary text-white">Kembali</a>
            </div>

            @if($bookings->isNotEmpty())
                <div class="row g-4">
                    @foreach($bookings as $booking)
                        @php
                            $firstTicket = $booking->ticketBookings->first();
                            $film = $firstTicket ? $firstTicket->schedule->film : null;
                        @endphp
                        <div class="col-lg-6">
                            <div class="card shadow-sm border-0 h-100 overflow-hidden transaction-card" 
                                 style="cursor: pointer; border-radius: 15px !important;" 
                                 onclick="window.location.href='{{ route('booking.confirmation', $booking) }}'">
                                <div class="row g-0 h-100">
                                    <div class="col-4">
                                        <img src="{{ $film ? $film->cover_url : asset('storage/cover/default-cover.svg') }}" 
                                             alt="{{ $film->title ?? 'Film' }}" 
                                             class="img-fluid h-100 object-fit-cover">
                                    </div>
                                    <div class="col-8">
                                        <div class="card-body d-flex flex-column h-100">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="fw-bold text-primary mb-0 text-truncate" style="max-width: 120px;">{{ $film->title ?? 'Transaksi' }}</h6>
                                                <span class="badge {{ $booking->status === 'confirmed' ? 'bg-success' : ($booking->status === 'pending' ? 'bg-warning' : 'bg-danger') }} small">
                                                    {{ $booking->status === 'confirmed' ? 'Selesai' : ($booking->status === 'pending' ? 'Menunggu' : 'Batal') }}
                                                </span>
                                            </div>
                                            
                                            <p class="text-muted small mb-3">
                                                <iconify-icon icon="lucide:calendar" class="me-1"></iconify-icon>
                                                {{ $booking->created_at->format('d M Y') }}
                                            </p>

                                            <div class="mt-auto">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small class="text-muted">Jumlah</small>
                                                    <small class="fw-bold">{{ $booking->ticketBookings->count() }} Kursi</small>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <small class="text-muted">Total</small>
                                                    <small class="fw-bold text-dark">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</small>
                                                </div>
                                                
                                                <div class="mt-3 text-end">
                                                    @if($booking->status === 'pending')
                                                        <a href="{{ route('booking.payment', $booking) }}" class="btn btn-sm btn-success px-3 rounded-pill">Bayar</a>
                                                    @else
                                                        <span class="text-primary small fw-bold">Detail <iconify-icon icon="lucide:chevron-right"></iconify-icon></span>
                                                    @endif
                                                </div>
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
