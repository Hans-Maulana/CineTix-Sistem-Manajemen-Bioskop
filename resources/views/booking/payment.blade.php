@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            {{-- Flash Messages --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Booking Summary Card --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning">
                    <h4 class="mb-0 text-white">💳 Pembayaran</h4>
                </div>
                <div class="card-body">
                    <h5>Detail Pemesanan</h5>
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td><strong>Film</strong></td>
                                    <td>
                                        @foreach($booking->ticketBookings as $ticket)
                                            {{ $ticket->schedule->film->title }}
                                            @break
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal & Jam</strong></td>
                                    <td>
                                        @foreach($booking->ticketBookings as $ticket)
                                            {{ $ticket->schedule->schedule_date->format('d M Y') }} | 
                                            {{ $ticket->schedule->start_time }} - {{ $ticket->schedule->end_time }}
                                            @break
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Kursi</strong></td>
                                    <td>
                                        @foreach($booking->ticketBookings as $ticket)
                                            <span class="badge bg-primary">{{ $ticket->seat->seat_code }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr class="border-top">
                                    <td><strong>Total Harga</strong></td>
                                    <td>
                                        <h5 class="text-primary mb-0">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</h5>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if($booking->promo)
                        <div class="alert alert-success mb-0">
                            <strong>🎉 Promo Diterapkan</strong>
                            <p class="mb-0">Kode: <strong>{{ $booking->promo->code }}</strong> - Diskon: Rp {{ number_format($booking->promo->disc_amount, 0, ',', '.') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Payment History (jika ada percobaan sebelumnya) --}}
            @if($booking->payments->where('status', 'failed')->count() > 0)
                <div class="alert alert-warning mb-4">
                    <strong>⚠️ Percobaan Pembayaran Sebelumnya:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($booking->payments->where('status', 'failed') as $failedPayment)
                            <li>
                                {{ $failedPayment->method_label }} - 
                                <span class="badge bg-danger">Gagal</span> 
                                ({{ $failedPayment->created_at->format('d M Y H:i') }})
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Payment Method Selection --}}
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Pilih Metode Pembayaran</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('booking.initiate-payment', $booking) }}">
                        @csrf

                        <div class="row g-3">
                            @foreach($paymentMethods as $method)
                                <div class="col-md-6">
                                    <div class="card border payment-method-card h-100" style="cursor: pointer;" onclick="selectMethod('{{ $method['key'] }}')">
                                        <div class="card-body text-center p-4">
                                            <input class="form-check-input visually-hidden" type="radio" 
                                                   name="payment_method" value="{{ $method['key'] }}" 
                                                   id="method_{{ $method['key'] }}" required>
                                            <div class="fs-1 mb-3">{{ $method['icon'] }}</div>
                                            <h5 class="mb-2">{{ $method['label'] }}</h5>
                                            <p class="text-muted small mb-0">{{ $method['description'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-success btn-lg" id="payBtn" disabled>
                                Lanjutkan Pembayaran - Rp {{ number_format($booking->total_amount, 0, ',', '.') }}
                            </button>
                            <a href="{{ route('booking.show', $booking->ticketBookings->first()->schedule) }}" class="btn btn-outline-secondary">
                                Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .payment-method-card {
        transition: all 0.3s ease;
        border: 2px solid transparent !important;
    }
    .payment-method-card:hover {
        border-color: #0d6efd !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(13, 110, 253, 0.15);
    }
    .payment-method-card.selected {
        border-color: #0d6efd !important;
        background-color: #f0f7ff;
        box-shadow: 0 4px 15px rgba(13, 110, 253, 0.2);
    }
    .payment-method-card.selected::after {
        content: '✓';
        position: absolute;
        top: 10px;
        right: 10px;
        background: #0d6efd;
        color: white;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
</style>

<script>
    function selectMethod(method) {
        // Unselect all
        document.querySelectorAll('.payment-method-card').forEach(card => {
            card.classList.remove('selected');
        });

        // Select clicked
        const radio = document.getElementById('method_' + method);
        radio.checked = true;
        radio.closest('.payment-method-card').classList.add('selected');

        // Enable button
        document.getElementById('payBtn').disabled = false;
    }
</script>
@endsection
