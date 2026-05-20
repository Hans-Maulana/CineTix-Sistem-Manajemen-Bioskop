@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            {{-- Payment Process Card --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header text-white py-3 px-4 border-0" style="background: #1A1953 !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white bg-opacity-20 p-2 rounded-3 hstack">
                                @if($payment->method === 'qris')
                                    <iconify-icon icon="lucide:qr-code" class="fs-6 text-dark"></iconify-icon>
                                @else
                                    <iconify-icon icon="lucide:landmark" class="fs-6 text-dark"></iconify-icon>
                                @endif
                            </div>
                            <h5 class="mb-0 fw-bold text-white">Menunggu Pembayaran</h5>
                        </div>
                        <span class="badge bg-white bg-opacity-20 text-dark rounded-pill px-3 py-2 fw-medium">
                            {{ $displayData['method_label'] }}
                        </span>
                    </div>
                </div>
                
                <div class="card-body p-4 p-md-5 text-center bg-white">

                    {{-- Countdown Timer --}}
                    <div class="mb-5">
                        <p class="text-muted small fw-medium mb-2 uppercase tracking-wider">Selesaikan dalam waktu</p>
                        <div id="countdown" class="display-3 fw-bold mb-3" style="color: #1A1953; letter-spacing: -2px;">
                            --:--
                        </div>
                        <div class="progress mx-auto rounded-pill" style="height: 8px; max-width: 300px;">
                            <div class="progress-bar" id="progressBar" role="progressbar" style="width: 100%; background: #1A1953;"></div>
                        </div>
                    </div>

                    <div class="row justify-content-center mb-5">
                        <div class="col-md-10 p-4 rounded-4" style="background: #f8f9ff; border: 1px dashed #d1d5ff;">
                            <p class="text-muted small mb-1">Total yang harus dibayar</p>
                            <h2 class="fw-bold mb-0" style="color: #1A1953;">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</h2>
                        </div>
                    </div>

                    {{-- QRIS Section --}}
                    @if($payment->method === 'qris')
                        <div class="mb-5">
                            <div class="d-inline-block p-4 bg-white rounded-4 shadow-sm border mb-3">
                                <img src="{{ $displayData['qr_url'] }}" alt="QRIS Code" 
                                     class="img-fluid" style="max-width: 280px;">
                            </div>
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <iconify-icon icon="lucide:scan" class="text-primary"></iconify-icon>
                                <p class="text-muted small mb-0">Scan QRIS menggunakan Mobile Banking atau E-Wallet</p>
                            </div>
                        </div>
                    @endif

                    {{-- Virtual Account Section --}}
                    @if($payment->method === 'virtual_account')
                        <div class="mb-5">
                            <div class="p-4 bg-white rounded-4 shadow-sm border mb-3 mx-auto" style="max-width: 450px;">
                                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                                    <span class="fw-bold text-dark">{{ $displayData['bank_name'] }}</span>
                                    <iconify-icon icon="solar:bank-bold-duotone" class="fs-4 text-primary"></iconify-icon>
                                </div>
                                <p class="text-muted small mb-2">Nomor Virtual Account</p>
                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    <h2 class="font-monospace text-dark mb-0 fw-bold" id="vaNumber" style="letter-spacing: 2px;">
                                        {{ $displayData['va_number'] }}
                                    </h2>
                                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" onclick="copyVA()">
                                        <iconify-icon icon="lucide:copy" class="me-1"></iconify-icon> Salin
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Instructions --}}
                    <div class="text-start p-4 rounded-4 border bg-light mb-5">
                        <h6 class="fw-bold text-white mb-3">
                            <iconify-icon icon="lucide:list-checks" class="me-2 text-primary "></iconify-icon>
                            Cara Pembayaran:
                        </h6>
                        <div class="d-flex flex-column gap-2">
                            @foreach($displayData['instructions'] as $index => $instruction)
                                <div class="d-flex gap-3">
                                    <span class="badge bg-white text-primary border rounded-circle hstack justify-content-center" style="width: 24px; height: 24px; flex-shrink: 0;">{{ $index + 1 }}</span>
                                    <span class="text-muted small text-white">{{ $instruction }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex flex-column gap-3">
                        <form method="POST" action="{{ route('booking.confirm-payment', ['booking' => $booking, 'payment' => $payment]) }}" id="confirmForm">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg w-100 py-3 text-white fw-bold rounded-4 shadow-sm" id="confirmBtn" style="background: #1A1953 !important; border: none;">
                                Saya Sudah Bayar
                            </button>
                        </form>

                        <form method="POST" action="{{ route('booking.cancel', $booking) }}" id="cancelForm" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');">
                            @csrf
                            @method('POST')
                            <button type="submit" class="btn btn-outline-danger btn-lg w-100 py-3 text-danger fw-bold rounded-4">
                                <iconify-icon icon="lucide:trash-2" class="me-2"></iconify-icon> Batalkan Pesanan
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Summary Detail Mini --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-sm-8 mb-3 mb-sm-0">
                            <h6 class="fw-bold text-dark mb-1">
                                @foreach($booking->ticketBookings as $ticket)
                                    {{ $ticket->schedule->film->title }}
                                    @break
                                @endforeach
                            </h6>
                            <p class="text-muted small mb-0">
                                @foreach($booking->ticketBookings as $ticket)
                                    {{ $ticket->seat->seat_code }} ({{ $booking->ticketBookings->count() }} Kursi)
                                    @break
                                @endforeach
                                • {{ $booking->ticketBookings->first()->schedule->schedule_date->format('d M Y') }}
                            </p>
                        </div>
                        <div class="col-sm-4 text-sm-end">
                            <p class="text-muted small mb-0">Total Harga</p>
                            <h5 class="fw-bold mb-0" style="color: #1A1953;">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .font-monospace {
        font-family: 'JetBrains Mono', 'Courier New', monospace !important;
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
@endpush

@push('scripts')
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
            document.getElementById('confirmBtn').classList.remove('btn-primary');
            document.getElementById('confirmBtn').classList.add('btn-danger');
            document.getElementById('progressBar').style.width = '0%';

            setTimeout(() => {
                window.location.href = "{{ route('booking.payment', $booking) }}";
            }, 3000);
            return;
        }

        const minutes = Math.floor(remaining / 60);
        const seconds = remaining % 60;
        document.getElementById('countdown').textContent = 
            String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');

        const progress = (remaining / maxSeconds) * 100;
        document.getElementById('progressBar').style.width = progress + '%';

        if (remaining < 60) {
            document.getElementById('countdown').style.color = '#dc3545';
        }

        remaining--;
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);

    function copyVA() {
        const vaNumber = document.getElementById('vaNumber')?.textContent.trim();
        if (vaNumber) {
            navigator.clipboard.writeText(vaNumber).then(() => {
                const btn = event.currentTarget;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<iconify-icon icon="lucide:check" class="me-1"></iconify-icon> Tersalin!';
                setTimeout(() => { btn.innerHTML = originalText; }, 2000);
            });
        }
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
