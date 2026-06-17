@extends('layouts.app')

@push('styles')
@include('partials.customer_film_styles')
<style>
    body {
        background-color: #e4e8ef !important;
    }

    .cx-payment-page {
        padding-bottom: 2.5rem;
    }

    .cx-payment-hero {
        background: linear-gradient(135deg, #1A1953 0%, #2d2b7a 100%);
        border-radius: 18px;
        padding: 22px 24px;
        color: #fff;
        margin-bottom: 1.5rem;
        box-shadow: 0 8px 24px rgba(26, 25, 83, 0.15);
    }

    .cx-payment-hero h4 {
        font-weight: 800;
        margin-bottom: 4px;
    }

    .cx-payment-card {
        background: #fff;
        border: 1px solid rgba(26, 25, 83, 0.1);
        border-radius: 18px;
        box-shadow: 0 4px 16px rgba(26, 25, 83, 0.07);
        overflow: hidden;
    }

    .cx-payment-card-head {
        padding: 16px 20px;
        background: #1A1953;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .cx-payment-card-head iconify-icon {
        font-size: 1.25rem;
        opacity: 0.9;
    }

    .cx-payment-card-head h5 {
        margin: 0;
        font-size: 1rem;
        font-weight: 800;
    }

    .cx-payment-card-body {
        padding: 20px;
    }

    .cx-pay-method {
        cursor: pointer;
        display: block;
    }

    .cx-pay-method input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .cx-pay-method-box {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 18px;
        border: 1.5px solid #dfe4ec;
        border-radius: 14px;
        background: #fafbfc;
        transition: all 0.18s ease;
    }

    .cx-pay-method-box:hover {
        border-color: rgba(26, 25, 83, 0.35);
        background: #fff;
    }

    .cx-pay-method input:checked + .cx-pay-method-box {
        border-color: #1A1953;
        background: rgba(26, 25, 83, 0.04);
        box-shadow: 0 4px 14px rgba(26, 25, 83, 0.1);
    }

    .cx-pay-method-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        background: #fff;
        border: 1px solid rgba(26, 25, 83, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        flex-shrink: 0;
    }

    .cx-pay-method-text h6 {
        margin: 0 0 2px;
        font-size: 0.95rem;
        font-weight: 800;
        color: #1f2533;
    }

    .cx-pay-method-text p {
        margin: 0;
        font-size: 0.8rem;
        color: #8a93a6;
    }

    .cx-pay-method-check {
        margin-left: auto;
        width: 22px;
        height: 22px;
        border-radius: 999px;
        border: 2px solid #dfe4ec;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.18s ease;
    }

    .cx-pay-method input:checked + .cx-pay-method-box .cx-pay-method-check {
        background: #1A1953;
        border-color: #1A1953;
        color: #fff;
    }

    .cx-pay-method-check iconify-icon {
        font-size: 0.85rem;
        opacity: 0;
        transition: opacity 0.15s ease;
    }

    .cx-pay-method input:checked + .cx-pay-method-box .cx-pay-method-check iconify-icon {
        opacity: 1;
    }

    .cx-pay-submit {
        width: 100%;
        border: none;
        border-radius: 12px;
        padding: 14px 18px;
        font-size: 0.95rem;
        font-weight: 800;
        background: #1A1953;
        color: #fff;
        transition: all 0.18s ease;
    }

    .cx-pay-submit:hover:not(:disabled) {
        background: #14123e;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(26, 25, 83, 0.22);
    }

    .cx-pay-submit:disabled {
        background: #c5cad6;
        color: #fff;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .cx-pay-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: 100%;
        padding: 12px;
        border-radius: 12px;
        font-size: 0.88rem;
        font-weight: 700;
        color: #1A1953;
        text-decoration: none;
        border: 1.5px solid rgba(26, 25, 83, 0.15);
        background: #fff;
        transition: all 0.18s ease;
    }

    .cx-pay-back:hover {
        background: #f4f6fa;
        color: #1A1953;
    }

    .cx-payment-summary {
        background: #fff;
        border: 1px solid rgba(26, 25, 83, 0.1);
        border-radius: 18px;
        box-shadow: 0 4px 16px rgba(26, 25, 83, 0.07);
        overflow: hidden;
    }

    @media (min-width: 992px) {
        .cx-payment-summary {
            position: sticky;
            top: 108px;
            z-index: 5;
        }
    }

    .cx-summary-head {
        padding: 16px 20px;
        border-bottom: 1px solid rgba(26, 25, 83, 0.08);
        font-size: 1rem;
        font-weight: 800;
        color: #1f2533;
    }

    .cx-summary-body {
        padding: 20px;
    }

    .cx-summary-film {
        display: flex;
        gap: 14px;
        padding-bottom: 16px;
        margin-bottom: 16px;
        border-bottom: 1px dashed rgba(26, 25, 83, 0.12);
    }

    .cx-summary-poster {
        width: 160px;
        height: 90px;
        border-radius: 10px;
        background: linear-gradient(135deg, #1A1953, #3a37a0);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .cx-summary-poster iconify-icon {
        font-size: 1.8rem;
        color: rgba(255, 255, 255, 0.5);
    }

    .cx-summary-meta {
        font-size: 0.82rem;
        color: #8a93a6;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .cx-seat-badge {
        display: inline-flex;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 800;
        background: rgba(26, 25, 83, 0.08);
        color: #1A1953;
        margin-left: 4px;
    }

    .cx-summary-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 14px;
        margin-top: 8px;
        border-top: 1px solid rgba(26, 25, 83, 0.1);
    }

    .cx-summary-total span:last-child {
        font-size: 1.35rem;
        font-weight: 800;
        color: #1A1953;
    }

    .cx-summary-note {
        display: flex;
        gap: 10px;
        padding: 12px 14px;
        background: rgba(251, 140, 0, 0.08);
        border: 1px solid rgba(251, 140, 0, 0.2);
        border-radius: 12px;
        font-size: 0.8rem;
        color: #5c6478;
        line-height: 1.5;
    }

    .cx-summary-note iconify-icon {
        color: #fb8c00;
        font-size: 1.1rem;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .btn-back-custom {
        border: 2px solid #1A1953 !important;
        color: #1A1953 !important;
        font-weight: bold;
        background: transparent;
        transition: all 0.3s ease;
    }
    .btn-back-custom:hover {
        background-color: #1A1953 !important;
        color: #ffffff !important;
    }
</style>
@endpush

@section('content')
@php
    $firstTicket = $booking->ticketBookings->first();
    $schedule = $firstTicket?->schedule;
    $film = $schedule?->film;
@endphp

<div class="cx-payment-page">
<div class="container py-4 py-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('landing-page') }}" class="text-primary text-decoration-none">Beranda</a></li>
                @if($film)
                    <li class="breadcrumb-item"><a href="{{ route('films.detail', $film) }}" class="text-primary text-decoration-none">{{ $film->title }}</a></li>
                @endif
                <li class="breadcrumb-item active">Pembayaran</li>
            </ol>
        </nav>
        @if($schedule)
            <a href="{{ route('booking.show', $schedule) }}" class="btn btn-back-custom rounded-pill px-4 py-2 d-none d-md-flex align-items-center gap-2">
                <iconify-icon icon="lucide:arrow-left"></iconify-icon>
                <span>Kembali</span>
            </a>
        @endif
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <iconify-icon icon="lucide:alert-circle" class="me-2"></iconify-icon>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(!empty($activePendingPayment))
        <div class="alert alert-info alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4 d-flex flex-wrap align-items-center justify-content-between gap-2" role="alert">
            <div>
                <iconify-icon icon="lucide:clock" class="me-2"></iconify-icon>
                Anda masih punya pembayaran {{ $activePendingPayment->method_label }} yang belum selesai.
            </div>
            <a href="{{ route('booking.process-payment', array_filter(['booking' => $booking, 'payment' => $activePendingPayment, 'token' => request('token')])) }}"
               class="btn btn-sm btn-primary rounded-pill px-3">
                Lanjutkan Pembayaran
            </a>
        </div>
    @endif

    <div class="cx-payment-hero">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <span class="badge bg-white rounded-pill mb-2 px-3 py-2" style="color: var(--primary-color) !important;">
                    <iconify-icon icon="lucide:credit-card" class="me-1 text-dark"></iconify-icon> <h10 class="text-dark">Pembayaran</h10>
                </span>
                <h4 class="text-white mb-1">Selesaikan Pesanan Anda</h4>
                <p class="text-white-50 small mb-0">Pilih metode pembayaran lalu konfirmasi untuk melanjutkan</p>
            </div>
            <div class="text-white text-end">
                <div class="small text-white-50">Total tagihan</div>
                <div class="fs-3 fw-bold">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Metode Pembayaran --}}
        <div class="col-lg-7 order-lg-1 order-2">
            <div class="cx-payment-card">
                <div class="cx-payment-card-head">
                    <iconify-icon icon="lucide:wallet"></iconify-icon>
                    <h5 class="text-white">Pilih Metode Pembayaran</h5>
                </div>
                <div class="cx-payment-card-body">
                    <form method="POST" action="{{ route('booking.initiate-payment', array_filter(['booking' => $booking, 'token' => request('token')])) }}" id="paymentForm">
                        @csrf

                        <div class="d-flex flex-column gap-3 mb-4">
                            @foreach($paymentMethods as $method)
                                <label class="cx-pay-method" for="method_{{ $method['key'] }}">
                                    <input type="radio"
                                           name="payment_method"
                                           value="{{ $method['key'] }}"
                                           id="method_{{ $method['key'] }}"
                                           required>
                                    <span class="cx-pay-method-box">
                                        <span class="cx-pay-method-icon">{{ $method['icon'] }}</span>
                                        <span class="cx-pay-method-text flex-grow-1">
                                            <h6>{{ $method['label'] }}</h6>
                                            <p>{{ $method['description'] }}</p>
                                        </span>
                                        <span class="cx-pay-method-check">
                                            <iconify-icon icon="lucide:check"></iconify-icon>
                                        </span>
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        <button type="submit" class="cx-pay-submit mb-3" id="payBtn" disabled>
                            Konfirmasi & Bayar Sekarang
                        </button>

                        @if($schedule)
                            <a href="{{ route('booking.show', $schedule) }}" class="cx-pay-back">
                                <iconify-icon icon="lucide:arrow-left"></iconify-icon>
                                Kembali ke Pemilihan Kursi
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="col-lg-5 order-lg-2 order-1">
            <div class="cx-payment-summary">
                <div class="cx-summary-head">Ringkasan Pesanan</div>
                <div class="cx-summary-body">
                    @if(!empty($isGuest))
                        <div class="cx-summary-note mb-3" style="background: rgba(26,25,83,0.06); border-color: rgba(26,25,83,0.12);">
                            <iconify-icon icon="lucide:mail" style="color:#1A1953;"></iconify-icon>
                            <div>
                                <div class="small text-muted mb-1">Tiket akan dikirim ke</div>
                                <strong class="text-dark">{{ $booking->guest_email }}</strong>
                            </div>
                        </div>
                    @endif

                    <div class="cx-summary-film">
                        <div class="cx-summary-poster" style="padding: 0; overflow: hidden; background: #e4e8ef;">
                            @if($film && $film->cover)
                                <img src="{{ asset('storage/cover/' . $film->cover) }}" alt="{{ $film->title }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px; image-rendering: high-quality;">
                            @else
                                <iconify-icon icon="lucide:film"></iconify-icon>
                            @endif
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-2">{{ $film?->title ?? '—' }}</h6>
                            @if($schedule)
                                <p class="cx-summary-meta mb-1">
                                    <iconify-icon icon="lucide:calendar"></iconify-icon>
                                    {{ $schedule->schedule_date->format('d M Y') }}
                                </p>
                                <p class="cx-summary-meta mb-1">
                                    <iconify-icon icon="lucide:clock"></iconify-icon>
                                    {{ $schedule->start_time->format('H:i') }} – {{ $schedule->end_time->format('H:i') }}
                                </p>
                                <p class="cx-summary-meta mb-0">
                                    <iconify-icon icon="lucide:building-2"></iconify-icon>
                                    {{ $schedule->studio->name }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <span class="text-muted small fw-semibold">Kursi ({{ $booking->ticketBookings->count() }})</span>
                        </div>
                        <div>
                            @foreach($booking->ticketBookings as $ticket)
                                <span class="cx-seat-badge">{{ $ticket->seat->seat_code }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2 small">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Subtotal tiket</span>
                            <span class="fw-semibold">Rp {{ number_format($booking->ticketBookings->sum('price_at_sale'), 0, ',', '.') }}</span>
                        </div>

                        @if($booking->promo)
                            @php
                                $ticketSubtotal = $booking->ticketBookings->sum('price_at_sale');
                                $promoDiscount = $booking->promo->calculateDiscount($ticketSubtotal);
                            @endphp
                            <div class="d-flex justify-content-between text-success">
                                <span>Promo ({{ $booking->promo->code }})</span>
                                <span class="fw-bold">- Rp {{ number_format($promoDiscount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="cx-summary-total">
                        <span class="fw-bold">Total Pembayaran</span>
                        <span>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                    </div>

                    <div class="cx-summary-note mt-4 mb-0">
                        <iconify-icon icon="lucide:timer"></iconify-icon>
                        <span>Kursi dikunci selama 5 menit. Selesaikan pembayaran sebelum waktu habis.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@push('scripts')
<script>
    // sessionStorage.removeItem('selected_seats_' + {{ $booking->schedule_id ?? 0 }}); // Dihapus agar pilihan kursi tetap ada saat user kembali

    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', () => {
            document.getElementById('payBtn').disabled = false;
        });
    });

    document.getElementById('paymentForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('payBtn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Memproses...';
        }
    });
</script>
@endpush
@endsection
