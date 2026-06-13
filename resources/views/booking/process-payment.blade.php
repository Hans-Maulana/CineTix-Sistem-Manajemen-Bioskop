@extends('layouts.app')

@push('styles')
@include('partials.customer_film_styles')
<style>
    body {
        background-color: #e4e8ef !important;
    }

    .cx-process-page {
        padding-bottom: 2.5rem;
    }

    .cx-process-card {
        background: #fff;
        border: 1px solid rgba(26, 25, 83, 0.1);
        border-radius: 18px;
        box-shadow: 0 4px 16px rgba(26, 25, 83, 0.07);
        overflow: hidden;
    }

    .cx-process-head {
        padding: 18px 22px;
        background: linear-gradient(135deg, #1A1953 0%, #2d2b7a 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .cx-process-head-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .cx-process-head-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.14);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .cx-process-head h5 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 800;
    }

    .cx-process-head p {
        margin: 0;
        font-size: 0.78rem;
        color: rgba(255, 255, 255, 0.72);
    }

    .cx-process-method-badge {
        background: rgba(255, 255, 255, 0.16);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.22);
        border-radius: 999px;
        padding: 6px 14px;
        font-size: 0.78rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .cx-process-body {
        padding: 24px 22px 28px;
    }

    .cx-countdown-box {
        text-align: center;
        padding: 20px 16px 18px;
        background: #f4f6fa;
        border: 1px solid rgba(26, 25, 83, 0.08);
        border-radius: 16px;
        margin-bottom: 22px;
    }

    .cx-countdown-label {
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #8a93a6;
        margin-bottom: 8px;
    }

    #countdown {
        font-size: clamp(2.4rem, 6vw, 3.2rem);
        font-weight: 800;
        color: #1A1953;
        letter-spacing: -0.02em;
        font-variant-numeric: tabular-nums;
        line-height: 1;
        margin-bottom: 14px;
    }

    #countdown.countdown-expired {
        color: #dc3545 !important;
        animation: cxPulse 1s infinite;
    }

    @keyframes cxPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.55; }
    }

    .cx-countdown-bar {
        height: 8px;
        background: #dfe4ec;
        border-radius: 999px;
        overflow: hidden;
        max-width: 320px;
        margin: 0 auto;
    }

    .cx-countdown-bar .progress-bar {
        background: linear-gradient(90deg, #1A1953, #3a37a0);
        transition: width 1s linear;
    }

    .cx-amount-box {
        text-align: center;
        padding: 18px 16px;
        background: rgba(26, 25, 83, 0.04);
        border: 1.5px dashed rgba(26, 25, 83, 0.18);
        border-radius: 14px;
        margin-bottom: 24px;
    }

    .cx-amount-box p {
        margin: 0 0 4px;
        font-size: 0.8rem;
        color: #8a93a6;
        font-weight: 600;
    }

    .cx-amount-box h2 {
        margin: 0;
        font-size: clamp(1.6rem, 4vw, 2rem);
        font-weight: 800;
        color: #1A1953;
    }

    .cx-va-card {
        background: #fafbfc;
        border: 1px solid rgba(26, 25, 83, 0.1);
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 24px;
    }

    .cx-va-bank {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 14px;
        margin-bottom: 14px;
        border-bottom: 1px solid rgba(26, 25, 83, 0.08);
    }

    .cx-va-bank span {
        font-weight: 800;
        color: #1f2533;
    }

    .cx-va-label {
        font-size: 0.76rem;
        font-weight: 700;
        color: #8a93a6;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 10px;
    }

    .cx-va-number-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    #vaNumber {
        font-family: 'JetBrains Mono', 'Courier New', monospace;
        font-size: clamp(1.1rem, 3vw, 1.45rem);
        font-weight: 800;
        color: #1A1953;
        letter-spacing: 0.06em;
        margin: 0;
        word-break: break-all;
    }

    .cx-copy-btn {
        border: 1.5px solid rgba(26, 25, 83, 0.15);
        background: #fff;
        color: #1A1953;
        border-radius: 10px;
        padding: 8px 14px;
        font-size: 0.82rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.18s ease;
        flex-shrink: 0;
    }

    .cx-copy-btn:hover {
        background: #1A1953;
        color: #fff;
        border-color: #1A1953;
    }

    .cx-qris-wrap {
        text-align: center;
        margin-bottom: 24px;
    }

    .cx-qris-frame {
        display: inline-block;
        padding: 16px;
        background: #fff;
        border: 1px solid rgba(26, 25, 83, 0.1);
        border-radius: 16px;
        box-shadow: 0 4px 14px rgba(26, 25, 83, 0.06);
        margin-bottom: 12px;
    }

    .cx-qris-frame img {
        max-width: 260px;
        width: 100%;
        height: auto;
        display: block;
    }

    .cx-qris-hint {
        font-size: 0.84rem;
        color: #8a93a6;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin: 0;
    }

    .cx-instructions {
        background: #f8f9fc;
        border: 1px solid rgba(26, 25, 83, 0.08);
        border-radius: 14px;
        padding: 18px 18px 16px;
        margin-bottom: 24px;
    }

    .cx-instructions h6 {
        font-size: 0.88rem;
        font-weight: 800;
        color: #1f2533;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .cx-instruction-item {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        margin-bottom: 10px;
    }

    .cx-instruction-item:last-child {
        margin-bottom: 0;
    }

    .cx-instruction-num {
        width: 24px;
        height: 24px;
        border-radius: 999px;
        background: #1A1953;
        color: #fff;
        font-size: 0.72rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .cx-instruction-text {
        font-size: 0.84rem;
        color: #5c6478;
        line-height: 1.55;
        padding-top: 2px;
    }

    .cx-process-actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .cx-btn-primary-lg {
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

    .cx-btn-primary-lg:hover:not(:disabled) {
        background: #14123e;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(26, 25, 83, 0.22);
    }

    .cx-btn-primary-lg:disabled {
        background: #dc3545;
        opacity: 0.85;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .cx-btn-secondary-lg {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        border-radius: 12px;
        padding: 13px 18px;
        font-size: 0.9rem;
        font-weight: 700;
        background: #fff;
        color: #1A1953;
        border: 1.5px solid rgba(26, 25, 83, 0.15);
        text-decoration: none;
        transition: all 0.18s ease;
    }

    .cx-btn-secondary-lg:hover {
        background: #f4f6fa;
        color: #1A1953;
    }

    .cx-btn-danger-lg {
        width: 100%;
        border-radius: 12px;
        padding: 13px 18px;
        font-size: 0.9rem;
        font-weight: 700;
        background: #fff;
        color: #dc3545;
        border: 1.5px solid rgba(220, 53, 69, 0.35);
        transition: all 0.18s ease;
    }

    .cx-btn-danger-lg:hover {
        background: rgba(220, 53, 69, 0.06);
        color: #dc3545;
    }

    .cx-process-summary {
        background: #fff;
        border: 1px solid rgba(26, 25, 83, 0.1);
        border-radius: 18px;
        box-shadow: 0 4px 16px rgba(26, 25, 83, 0.07);
        overflow: hidden;
    }

    .cx-process-summary-head {
        padding: 16px 20px;
        border-bottom: 1px solid rgba(26, 25, 83, 0.08);
        font-weight: 800;
        color: #1f2533;
    }

    .cx-process-summary-body {
        padding: 20px;
    }

    .cx-summary-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        font-size: 0.86rem;
        margin-bottom: 10px;
    }

    .cx-summary-row span:first-child {
        color: #8a93a6;
    }

    .cx-summary-row span:last-child {
        font-weight: 700;
        color: #1f2533;
        text-align: right;
    }

    .cx-summary-total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 14px;
        margin-top: 8px;
        border-top: 1px solid rgba(26, 25, 83, 0.1);
    }

    .cx-summary-total-row span:last-child {
        font-size: 1.25rem;
        font-weight: 800;
        color: #1A1953;
    }

    .btn-back-custom {
        border: 2px solid #1A1953 !important;
        color: #1A1953 !important;
        font-weight: bold;
        background: transparent;
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

<div class="cx-process-page">
<div class="container py-4 py-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('landing-page') }}" class="text-primary text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('booking.payment', array_filter(['booking' => $booking, 'token' => request('token')])) }}" class="text-primary text-decoration-none">Pembayaran</a></li>
                <li class="breadcrumb-item active">Konfirmasi</li>
            </ol>
        </nav>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-lg-8">
            <div class="cx-process-card">
                <div class="cx-process-head">
                    <div class="cx-process-head-left">
                        <div class="cx-process-head-icon">
                            @if($payment->method === 'qris')
                                <iconify-icon icon="lucide:qr-code"></iconify-icon>
                            @else
                                <iconify-icon icon="lucide:landmark"></iconify-icon>
                            @endif
                        </div>
                        <div>
                            <h5>Menunggu Pembayaran</h5>
                            <p>Selesaikan sebelum waktu habis</p>
                        </div>
                    </div>
                    <span class="cx-process-method-badge">{{ $displayData['method_label'] }}</span>
                </div>

                <div class="cx-process-body">
                    {{-- Countdown --}}
                    <div class="cx-countdown-box">
                        <div class="cx-countdown-label">Selesaikan dalam waktu</div>
                        <div id="countdown">--:--</div>
                        <div class="cx-countdown-bar">
                            <div class="progress-bar" id="progressBar" role="progressbar" style="width: 100%;"></div>
                        </div>
                    </div>

                    <div class="cx-amount-box">
                        <p>Total yang harus dibayar</p>
                        <h2>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</h2>
                    </div>

                    {{-- QRIS --}}
                    @if($payment->method === 'qris')
                        <div class="cx-qris-wrap">
                            <div class="cx-qris-frame">
                                <img src="{{ $displayData['qr_url'] }}" alt="QRIS Code">
                            </div>
                            <p class="cx-qris-hint">
                                <iconify-icon icon="lucide:scan-line"></iconify-icon>
                                Scan dengan Mobile Banking atau E-Wallet
                            </p>
                        </div>
                    @endif

                    {{-- Virtual Account --}}
                    @if($payment->method === 'virtual_account')
                        <div class="cx-va-card">
                            <div class="cx-va-bank">
                                <span>{{ $displayData['bank_name'] }}</span>
                                <iconify-icon icon="lucide:building-2" style="font-size:1.4rem;color:#1A1953;"></iconify-icon>
                            </div>
                            <div class="cx-va-label">Nomor Virtual Account</div>
                            <div class="cx-va-number-row">
                                <h2 id="vaNumber">{{ $displayData['va_number'] }}</h2>
                                <button type="button" class="cx-copy-btn" onclick="copyVA(this)">
                                    <iconify-icon icon="lucide:copy"></iconify-icon>
                                    Salin
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- Instructions --}}
                    <div class="cx-instructions">
                        <h6>
                            <iconify-icon icon="lucide:list-checks" style="color:#1A1953;"></iconify-icon>
                            Cara Pembayaran
                        </h6>
                        @foreach($displayData['instructions'] as $index => $instruction)
                            <div class="cx-instruction-item">
                                <span class="cx-instruction-num">{{ $index + 1 }}</span>
                                <span class="cx-instruction-text">{{ $instruction }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Actions --}}
                    <div class="cx-process-actions">
                        <form method="POST" action="{{ route('booking.confirm-payment', array_filter(['booking' => $booking, 'payment' => $payment, 'token' => request('token')])) }}" id="confirmForm">
                            @csrf
                            <button type="submit" class="cx-btn-primary-lg" id="confirmBtn">
                                Saya Sudah Bayar
                            </button>
                        </form>

                        <a href="{{ route('booking.payment', array_filter(['booking' => $booking, 'token' => request('token')])) }}" class="cx-btn-secondary-lg">
                            <iconify-icon icon="lucide:arrow-left"></iconify-icon>
                            Ganti Metode Pembayaran
                        </a>

                        @auth
                            @if($booking->user_id === auth()->id())
                                <form method="POST" action="{{ route('booking.cancel', $booking) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');">
                                    @csrf
                                    <button type="submit" class="cx-btn-danger-lg">
                                        <iconify-icon icon="lucide:trash-2" class="me-1"></iconify-icon>
                                        Batalkan Pesanan
                                    </button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="col-lg-4">
            <div class="cx-process-summary">
                <div class="cx-process-summary-head">Detail Pesanan</div>
                <div class="cx-process-summary-body">
                    <h6 class="fw-bold text-dark mb-3">{{ $film?->title ?? '—' }}</h6>

                    <div class="cx-summary-row">
                        <span>Tanggal</span>
                        <span>{{ $schedule?->schedule_date->format('d M Y') ?? '—' }}</span>
                    </div>
                    <div class="cx-summary-row">
                        <span>Jam</span>
                        <span>{{ $schedule ? $schedule->start_time->format('H:i') . ' – ' . $schedule->end_time->format('H:i') : '—' }}</span>
                    </div>
                    <div class="cx-summary-row">
                        <span>Studio</span>
                        <span>{{ $schedule?->studio->name ?? '—' }}</span>
                    </div>
                    <div class="cx-summary-row">
                        <span>Kursi</span>
                        <span>
                            @foreach($booking->ticketBookings as $ticket)
                                {{ $ticket->seat->seat_code }}@if(!$loop->last), @endif
                            @endforeach
                        </span>
                    </div>
                    <div class="cx-summary-row">
                        <span>Metode</span>
                        <span>{{ $displayData['method_label'] }}</span>
                    </div>

                    <div class="cx-summary-total-row">
                        <span class="fw-bold">Total</span>
                        <span>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@push('scripts')
<script>
    const totalSeconds = {{ $payment->remaining_seconds }};
    const maxSeconds = {{ $displayData['countdown_seconds'] }};
    let remaining = totalSeconds;

    function updateCountdown() {
        const countdownEl = document.getElementById('countdown');
        const confirmBtn = document.getElementById('confirmBtn');
        const progressBar = document.getElementById('progressBar');

        if (remaining <= 0) {
            countdownEl.textContent = '00:00';
            countdownEl.classList.add('countdown-expired');
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Waktu Habis';
            progressBar.style.width = '0%';

            setTimeout(() => {
                window.location.href = "{{ route('booking.payment', array_filter(['booking' => $booking, 'token' => request('token')])) }}";
            }, 3000);
            return;
        }

        const minutes = Math.floor(remaining / 60);
        const seconds = remaining % 60;
        countdownEl.textContent =
            String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');

        progressBar.style.width = ((remaining / maxSeconds) * 100) + '%';

        if (remaining < 60) {
            countdownEl.style.color = '#dc3545';
        }

        remaining--;
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);

    function copyVA(btn) {
        const vaNumber = document.getElementById('vaNumber')?.textContent.trim();
        if (!vaNumber) return;

        navigator.clipboard.writeText(vaNumber).then(() => {
            const original = btn.innerHTML;
            btn.innerHTML = '<iconify-icon icon="lucide:check"></iconify-icon> Tersalin!';
            setTimeout(() => { btn.innerHTML = original; }, 2000);
        });
    }

    document.getElementById('confirmForm').addEventListener('submit', function(e) {
        const btn = document.getElementById('confirmBtn');
        if (btn.disabled) {
            e.preventDefault();
            return;
        }
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Memproses...';
    });
</script>
@endpush
@endsection
