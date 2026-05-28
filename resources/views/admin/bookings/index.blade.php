@extends('layouts.admin')

@section('title', 'Manajemen Booking')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h1 class="fw-bold text-primary">Daftar Transaksi</h1>
        <p class="text-muted">Pantau semua pesanan tiket dan status pembayaran user.</p>
    </div>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th class="py-3 px-4">ID Booking</th>
                    <th class="py-3">Customer</th>
                    <th class="py-3">Film</th>
                    <th class="py-3">Total Bayar</th>
                    <th class="py-3">Status</th>
                    <th class="py-3">Waktu Transaksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                <tr>
                    <td class="px-4 py-3 fw-bold">#{{ $booking->id }}</td>
<<<<<<< Updated upstream
                   <td>
                        @if($booking->user)
                            <div class="fw-bold">{{ $booking->user->name }} <span class="badge bg-primary" style="font-size: 0.7em;">Member</span></div>
                            <small class="text-muted">{{ $booking->user->email }}</small>
                        @else
                            <div class="fw-bold">{{ $booking->guest_name }} <span class="badge bg-secondary" style="font-size: 0.7em;">Guest</span></div>
                            <small class="text-muted">{{ $booking->guest_email }}</small>
=======
                    <td>
                        <div class="fw-bold">{{ $booking->customerName() }}</div>
                        <small class="text-muted">{{ $booking->customerEmail() ?? '-' }}</small>
                        @if($booking->isGuest())
                            <span class="badge bg-info text-dark mt-1">Guest</span>
>>>>>>> Stashed changes
                        @endif
                    </td>
                    <td>
                        {{ $booking->ticketBookings->first()?->schedule->film->title ?? 'N/A' }}
                    </td>
                    <td class="fw-bold text-primary">
                        Rp {{ number_format($booking->payments->first()?->amount ?? 0, 0, ',', '.') }}
                    </td>
                    <td>
                        @php
                            $status = $booking->payments->first()?->status ?? 'pending';
                        @endphp
                        @if($status == 'success')
                            <span class="badge bg-success px-3">Lunas</span>
                        @elseif($status == 'pending')
                            <span class="badge bg-warning text-dark px-3">Pending</span>
                        @else
                            <span class="badge bg-danger px-3">Gagal</span>
                        @endif
                    </td>
                    <td>{{ $booking->created_at->format('d M Y, H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">Belum ada transaksi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $bookings->links() }}
    </div>
</div>
<<<<<<< Updated upstream
=======

@foreach($bookings as $booking)
<div class="modal fade" id="bookingDetail{{ $booking->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold">Detail Booking #{{ $booking->id }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted mb-3">Informasi Customer</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" width="100">Tipe</td>
                                <td class="fw-bold">: {{ $booking->customerTypeLabel() }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Nama</td>
                                <td class="fw-bold">: {{ $booking->customerName() }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Email</td>
                                <td class="fw-bold">: {{ $booking->customerEmail() ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">No. HP</td>
                                <td class="fw-bold">: {{ $booking->customerPhone() ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted mb-3">Informasi Film</h6>
                        @php
                            $schedule = $booking->ticketBookings->first()?->schedule;
                        @endphp
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" width="100">Judul</td>
                                <td class="fw-bold">: {{ $schedule?->film->title ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Studio</td>
                                <td class="fw-bold">: {{ $schedule?->studio->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jadwal</td>
                                <td class="fw-bold">: {{ $schedule ? \Carbon\Carbon::parse($schedule->showtime)->format('d M Y, H:i') : '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row">
                    <div class="col-12">
                        <h6 class="fw-bold text-muted mb-3">Detail Kursi & Pembayaran</h6>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="text-muted">Kursi yang dipesan:</span>
                            @foreach($booking->ticketBookings as $ticket)
                                <span class="badge bg-secondary">{{ $ticket->seat->seat_code }}</span>
                            @endforeach
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                            <div>
                                <span class="text-muted d-block">Total Pembayaran</span>
                                <h4 class="fw-bold text-primary mb-0">Rp {{ number_format($booking->payments->first()?->amount ?? 0, 0, ',', '.') }}</h4>
                            </div>
                            <div class="text-end">
                                <span class="text-muted d-block">Status</span>
                                @php
                                    $status = $booking->payments->first()?->status ?? 'pending';
                                @endphp
                                @if($status == 'success')
                                    <span class="badge bg-success fs-6">Lunas</span>
                                @elseif($status == 'pending')
                                    <span class="badge bg-warning text-dark fs-6">Pending</span>
                                @else
                                    <span class="badge bg-danger fs-6">Gagal</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

>>>>>>> Stashed changes
@endsection
