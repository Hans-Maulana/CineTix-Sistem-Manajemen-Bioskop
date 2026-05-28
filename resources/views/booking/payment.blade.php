@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-9">

            {{-- Flash Messages --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
                    <iconify-icon icon="lucide:alert-circle" class="me-2"></iconify-icon>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row g-4">
                {{-- Left Side: Payment Selection --}}
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div class="card-header py-3 px-4 border-0" style="background: #1A1953 !important;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-white bg-opacity-20 p-2 rounded-3 hstack">
                                    <iconify-icon icon="solar:card-2-bold-duotone" class="fs-6 text-white"></iconify-icon>
                                </div>
                                <h5 class="mb-0 fw-bold text-white">Pilih Metode Pembayaran</h5>
                            </div>
                        </div>
                        <div class="card-body p-4 bg-white">
                            <form method="POST" action="{{ route('booking.initiate-payment', array_filter(['booking' => $booking, 'token' => request('token')])) }}" id="paymentForm">
                                @csrf

                                <div class="d-flex flex-column gap-3">
                                    @foreach($paymentMethods as $method)
                                        <div class="payment-option">
                                            <input class="form-check-input visually-hidden" type="radio" 
                                                   name="payment_method" value="{{ $method['key'] }}" 
                                                   id="method_{{ $method['key'] }}" required>
                                            <label class="payment-method-card d-flex align-items-center p-3 rounded-4 border w-100" for="method_{{ $method['key'] }}" onclick="selectMethod('{{ $method['key'] }}')">
                                                <div class="method-icon bg-light rounded-3 p-3 me-3 hstack justify-content-center" style="width: 60px; height: 60px;">
                                                    <span class="fs-2">{{ $method['icon'] }}</span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fw-bold text-dark">{{ $method['label'] }}</h6>
                                                    <p class="text-muted small mb-0">{{ $method['description'] }}</p>
                                                </div>
                                                <div class="selection-indicator bg-light rounded-circle hstack justify-content-center" style="width: 24px; height: 24px;">
                                                    <iconify-icon icon="lucide:check" class="text-white d-none"></iconify-icon>
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-5">
                                    <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold rounded-4 shadow-sm mb-3 pay-btn-custom" id="payBtn" disabled>
                                        Konfirmasi & Bayar Sekarang
                                    </button>
                                    <a href="{{ route('booking.show', $booking->ticketBookings->first()->schedule) }}" class="btn btn-link text-primary text-white w-100 text-decoration-none fw-bold">
                                        <iconify-icon icon="lucide:arrow-left" class="me-1"></iconify-icon> Kembali ke Pemilihan Kursi
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Right Side: Summary --}}
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden sticky-top" style="top: 110px;">
                        <div class="card-header bg-white py-3 px-4 border-bottom">
                            <h5 class="mb-0 fw-bold text-dark">Ringkasan Pesanan</h5>
                        </div>
                        <div class="card-body p-4">
                            @if(!empty($isGuest))
                            <div class="alert alert-info border-0 rounded-3 mb-4 py-3">
                                <div class="d-flex gap-2 align-items-start">
                                    <iconify-icon icon="lucide:mail" class="fs-5 mt-1"></iconify-icon>
                                    <div>
                                        <div class="small text-muted mb-1">Tiket akan dikirim ke</div>
                                        <strong class="text-dark">{{ $booking->guest_email }}</strong>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Film Info --}}
                            <div class="d-flex gap-3 mb-4 pb-4 border-bottom">
                                <div class="bg-light rounded-3 hstack justify-content-center" style="width: 80px; height: 110px; flex-shrink: 0;">
                                    <iconify-icon icon="lucide:film" class="fs-8 text-secondary opacity-50"></iconify-icon>
                                </div>
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="fw-bold text-dark mb-1">
                                        @foreach($booking->ticketBookings as $ticket)
                                            {{ $ticket->schedule->film->title }}
                                            @break
                                        @endforeach
                                    </h6>
                                    <p class="text-muted small mb-1">
                                        <iconify-icon icon="lucide:calendar" class="me-1"></iconify-icon>
                                        @foreach($booking->ticketBookings as $ticket)
                                            {{ $ticket->schedule->schedule_date->format('d M Y') }}
                                            @break
                                        @endforeach
                                    </p>
                                    <p class="text-muted small mb-0">
                                        <iconify-icon icon="lucide:clock" class="me-1"></iconify-icon>
                                        @foreach($booking->ticketBookings as $ticket)
                                            {{ $ticket->schedule->start_time->format('H:i') }} - {{ $ticket->schedule->end_time->format('H:i') }}
                                            @break
                                        @endforeach
                                    </p>
                                </div>
                            </div>

                            {{-- Seat Detail --}}
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small fw-medium">Kursi Pilihan ({{ $booking->ticketBookings->count() }})</span>
                                    <span class="fw-bold text-dark">
                                        @foreach($booking->ticketBookings as $ticket)
                                            <span class="badge rounded-pill px-3 py-2 ms-1" style="background: #f0f1ff; color: #1A1953; border: 1px solid #d1d5ff;">{{ $ticket->seat->seat_code }}</span>
                                        @endforeach
                                    </span>
                                </div>
                            </div>

                            <hr class="opacity-10 my-4">

                            {{-- Price Detail --}}
                            <div class="d-flex flex-column gap-2 mb-4">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Total Harga Tiket</span>
                                    <span class="text-dark fw-medium">Rp {{ number_format($booking->ticketBookings->sum('price_at_sale'), 0, ',', '.') }}</span>
                                </div>
                                
                                @if($booking->promo)
                                    @php
                                        $ticketSubtotal = $booking->ticketBookings->sum('price_at_sale');
                                        $promoDiscount = $booking->promo->calculateDiscount($ticketSubtotal);
                                    @endphp
                                    <div class="d-flex justify-content-between text-success">
                                        <span class="small fw-medium">
                                            <iconify-icon icon="lucide:ticket" class="me-1"></iconify-icon>
                                            Promo ({{ $booking->promo->code }})
                                        </span>
                                        <span class="small fw-bold">- Rp {{ number_format($promoDiscount, 0, ',', '.') }}</span>
                                    </div>
                                @endif

                                <div class="d-flex justify-content-between mt-2 pt-3 border-top">
                                    <span class="fw-bold text-dark">Total Pembayaran</span>
                                    <span class="fs-4 fw-bold" style="color: #1A1953;">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            {{-- Warning --}}
                            <div class="p-3 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-3 mb-0">
                                <div class="d-flex gap-2">
                                    <iconify-icon icon="lucide:info" class="text-warning fs-5 mt-1"></iconify-icon>
                                    <p class="small text-dark mb-0">Tiket akan di-lock selama 5 menit untuk proses pembayaran ini.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .payment-option input:checked + .payment-method-card {
        border-color: #1A1953 !important;
        background-color: #f8f9ff !important;
        box-shadow: 0 4px 15px rgba(26, 25, 83, 0.08);
    }
    .payment-option input:checked + .payment-method-card .selection-indicator {
        background-color: #1A1953 !important;
    }
    .payment-option input:checked + .payment-method-card .selection-indicator iconify-icon {
        display: block !important;
    }
    .pay-btn-custom {
        background: #1A1953 !important;
        border: none !important;
        color: white !important;
        transition: all 0.3s ease;
    }
    .pay-btn-custom:hover:not(:disabled) {
        background: #2a297a !important;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(26, 25, 83, 0.3) !important;
    }
    .pay-btn-custom:disabled {
        background: #ccc !important;
        color: #666 !important;
        cursor: not-allowed;
    }
    .payment-method-card {
        cursor: pointer;
        transition: all 0.2s ease;
        background: #fff;
        border: 1px solid #eee !important;
    }
    .payment-method-card:hover {
        border-color: #1A1953 !important;
        transform: translateX(5px);
    }
    .method-icon {
        transition: all 0.2s ease;
    }
    .payment-method-card:hover .method-icon {
        background-color: #f0f1ff !important;
    }
</style>
@endpush

@push('scripts')
<script>
    function selectMethod(method) {
        // Method selection handled by CSS + HTML radio
        document.getElementById('payBtn').disabled = false;
    }
</script>
@endpush
@endsection
