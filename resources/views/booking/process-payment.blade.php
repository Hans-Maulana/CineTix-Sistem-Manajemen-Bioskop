@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            {{-- Payment Process Card --}}
            <div class="card shadow-sm">
                <div class="card-header {{ $payment->method === 'qris' ? 'bg-info' : 'bg-primary' }} text-white">
                    <h4 class="mb-0">
                        {{ $payment->method === 'qris' ? '📱' : '🏦' }} 
                        Pembayaran via {{ $displayData['method_label'] }}
                    </h4>
                </div>
                <div class="card-body text-center">

                    {{-- Countdown Timer --}}
                    <div class="mb-4">
                        <p class="text-muted mb-2">Selesaikan pembayaran dalam:</p>
                        <div id="countdown" class="display-4 text-danger fw-bold">
                            --:--
                        </div>
                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar bg-danger" id="progressBar" role="progressbar" style="width: 100%"></div>
                        </div>
                    </div>

                    <hr>

                    {{-- Amount --}}
                    <div class="mb-4">
                        <p class="text-muted mb-1">Total Pembayaran</p>
                        <h2 class="text-primary fw-bold">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</h2>
                    </div>

                    {{-- QRIS Section --}}
                    @if($payment->method === 'qris')
                        <div class="mb-4 p-4 bg-light rounded">
                            <p class="fw-bold mb-3">Scan QR Code di bawah ini:</p>
                            <div class="d-inline-block p-3 bg-white rounded shadow-sm">
                                <img src="{{ $displayData['qr_url'] }}" alt="QRIS Code" 
                                     class="img-fluid" style="max-width: 250px;">
                            </div>
                            <p class="small text-muted mt-3 mb-0">
                                ID Transaksi: <code>{{ $displayData['qr_data'] }}</code>
                            </p>
                        </div>
                    @endif

                    {{-- Virtual Account Section --}}
                    @if($payment->method === 'virtual_account')
                        <div class="mb-4 p-4 bg-light rounded">
                            <p class="fw-bold mb-2">{{ $displayData['bank_name'] }}</p>
                            <p class="text-muted mb-3">Nomor Virtual Account:</p>
                            <div class="d-flex align-items-center justify-content-center gap-3 mb-3">
                                <h2 class="font-monospace text-dark mb-0 tracking-wide" id="vaNumber">
                                    {{ $displayData['va_number'] }}
                                </h2>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="copyVA()">
                                    📋 Salin
                                </button>
                            </div>
                            <p class="small text-muted mb-0">
                                Pastikan nominal transfer sesuai dengan total pembayaran
                            </p>
                        </div>
                    @endif

                    {{-- Instructions --}}
                    <div class="mb-4 text-start">
                        <h6 class="fw-bold">Cara Pembayaran:</h6>
                        <ol class="text-muted">
                            @foreach($displayData['instructions'] as $instruction)
                                <li class="mb-1">{{ $instruction }}</li>
                            @endforeach
                        </ol>
                    </div>

                    <hr>

                    {{-- Action Buttons --}}
                    <div class="d-grid gap-2">
                        <form method="POST" action="{{ route('booking.confirm-payment', ['booking' => $booking, 'payment' => $payment]) }}" id="confirmForm">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg w-100" id="confirmBtn">
                                ✅ Selesaikan Pembayaran
                            </button>
                        </form>

                        <a href="{{ route('booking.payment', $booking) }}" class="btn btn-outline-secondary">
                            ← Ganti Metode Pembayaran
                        </a>
                    </div>
                </div>
            </div>

            {{-- Booking Info --}}
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Detail Pemesanan</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Film:</strong></p>
                            <p class="text-muted">
                                @foreach($booking->ticketBookings as $ticket)
                                    {{ $ticket->schedule->film->title }}
                                    @break
                                @endforeach
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Jadwal:</strong></p>
                            <p class="text-muted">
                                @foreach($booking->ticketBookings as $ticket)
                                    {{ $ticket->schedule->schedule_date->format('d M Y') }} | 
                                    {{ $ticket->schedule->start_time }} - {{ $ticket->schedule->end_time }}
                                    @break
                                @endforeach
                            </p>
                        </div>
                    </div>
                    <div>
                        <p class="mb-1"><strong>Kursi:</strong></p>
                        <div>
                            @foreach($booking->ticketBookings as $ticket)
                                <span class="badge bg-primary me-1">{{ $ticket->seat->seat_code }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .font-monospace {
        font-family: 'Courier New', monospace !important;
        letter-spacing: 3px;
    }
    .tracking-wide {
        letter-spacing: 4px;
    }
    #countdown {
        font-variant-numeric: tabular-nums;
    }
    .countdown-expired {
        color: #dc3545 !important;
        animation: pulse 1s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
</style>

<script>
    // Countdown Timer
    const totalSeconds = {{ $payment->remaining_seconds }};
    const maxSeconds = {{ $displayData['countdown_seconds'] }};
    let remaining = totalSeconds;

    function updateCountdown() {
        if (remaining <= 0) {
            document.getElementById('countdown').textContent = '00:00';
            document.getElementById('countdown').classList.add('countdown-expired');
            document.getElementById('confirmBtn').disabled = true;
            document.getElementById('confirmBtn').textContent = '⏰ Waktu Habis';
            document.getElementById('confirmBtn').classList.remove('btn-success');
            document.getElementById('confirmBtn').classList.add('btn-danger');
            document.getElementById('progressBar').style.width = '0%';

            // Redirect setelah 3 detik
            setTimeout(() => {
                window.location.href = "{{ route('booking.payment', $booking) }}";
            }, 3000);
            return;
        }

        const minutes = Math.floor(remaining / 60);
        const seconds = remaining % 60;
        document.getElementById('countdown').textContent = 
            String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');

        // Update progress bar
        const progress = (remaining / maxSeconds) * 100;
        document.getElementById('progressBar').style.width = progress + '%';

        // Change color when < 1 minute
        if (remaining < 60) {
            document.getElementById('countdown').style.color = '#dc3545';
        }

        remaining--;
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);

    // Copy VA Number
    function copyVA() {
        const vaNumber = document.getElementById('vaNumber')?.textContent.trim();
        if (vaNumber) {
            navigator.clipboard.writeText(vaNumber).then(() => {
                alert('Nomor VA berhasil disalin!');
            });
        }
    }

    // Confirm form protection
    document.getElementById('confirmForm').addEventListener('submit', function(e) {
        const btn = document.getElementById('confirmBtn');
        if (btn.disabled) {
            e.preventDefault();
            return;
        }
        btn.disabled = true;
        btn.textContent = '⏳ Memproses...';
    });
</script>
@endsection
