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
                   <td>
                        @if($booking->user)
                            <div class="fw-bold">{{ $booking->user->name }} <span class="badge bg-primary" style="font-size: 0.7em;">Member</span></div>
                            <small class="text-muted">{{ $booking->user->email }}</small>
                        @else
                            <div class="fw-bold">{{ $booking->guest_name }} <span class="badge bg-secondary" style="font-size: 0.7em;">Guest</span></div>
                            <small class="text-muted">{{ $booking->guest_email }}</small>
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
@endsection
