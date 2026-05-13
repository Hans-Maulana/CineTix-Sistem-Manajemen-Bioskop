@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-success">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">✅ Pemesanan Berhasil!</h4>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <div class="display-1">🎬</div>
                    </div>

                    <h5>Terima Kasih Telah Melakukan Pemesanan</h5>
                    <p class="text-muted mb-4">Tiket Anda telah dikonfirmasi. Silakan simpan kode QR di bawah ini.</p>

                    {{-- QR Code --}}
                    <div class="mb-4 p-4 bg-light rounded">
                        <p><strong>Kode Pemesanan:</strong></p>
                        <p class="fs-4 font-monospace text-primary">{{ $booking->qr_redeem }}</p>
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $booking->qr_redeem }}" alt="QR Code" class="img-fluid mb-3">
                        <p class="small text-muted">Tunjukkan kode ini saat check-in di bioskop</p>
                    </div>

                    {{-- Booking Details --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Detail Tiket</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 text-start">
                                    <p><strong>Film:</strong></p>
                                    <p class="text-muted">
                                        @foreach($booking->ticketBookings as $ticket)
                                            {{ $ticket->schedule->film->title }}
                                            @break
                                        @endforeach
                                    </p>
                                </div>
                                <div class="col-md-6 text-start">
                                    <p><strong>Studio:</strong></p>
                                    <p class="text-muted">
                                        @foreach($booking->ticketBookings as $ticket)
                                            {{ $ticket->schedule->studio->name ?? '-' }}
                                            @break
                                        @endforeach
                                    </p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 text-start">
                                    <p><strong>Tanggal:</strong></p>
                                    <p class="text-muted">
                                        @foreach($booking->ticketBookings as $ticket)
                                            {{ $ticket->schedule->schedule_date->format('d M Y') }}
                                            @break
                                        @endforeach
                                    </p>
                                </div>
                                <div class="col-md-6 text-start">
                                    <p><strong>Jam Tayang:</strong></p>
                                    <p class="text-muted">
                                        @foreach($booking->ticketBookings as $ticket)
                                            {{ $ticket->schedule->start_time }} - {{ $ticket->schedule->end_time }}
                                            @break
                                        @endforeach
                                    </p>
                                </div>
                            </div>

                            <hr>

                            <div class="text-start">
                                <p><strong>Kursi Anda:</strong></p>
                                <div>
                                    @foreach($booking->ticketBookings as $ticket)
                                        <span class="badge bg-primary me-1 mb-2">{{ $ticket->seat->seat_code }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Info --}}
                    @php
                        $successPayment = $booking->payments->where('status', 'success')->first();
                    @endphp
                    @if($successPayment)
                        <div class="alert alert-info text-start">
                            <strong>💳 Info Pembayaran:</strong>
                            <div class="row mt-2">
                                <div class="col-6">
                                    <small class="text-muted">Metode:</small>
                                    <p class="mb-0 fw-bold">{{ $successPayment->method_label }}</p>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Status:</small>
                                    <p class="mb-0"><span class="badge {{ $successPayment->status_badge }}">{{ $successPayment->status_label }}</span></p>
                                </div>
                                <div class="col-6 mt-2">
                                    <small class="text-muted">Jumlah:</small>
                                    <p class="mb-0 fw-bold">Rp {{ number_format($successPayment->amount, 0, ',', '.') }}</p>
                                </div>
                                <div class="col-6 mt-2">
                                    <small class="text-muted">Tanggal Bayar:</small>
                                    <p class="mb-0">{{ $successPayment->paid_at?->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="d-grid gap-2">
                        <a href="{{ route('booking.history') }}" class="btn btn-primary">
                            Lihat Histori Pemesanan
                        </a>
                        <a href="{{ route('landing-page') }}" class="btn btn-outline-secondary">
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>

            {{-- Important Info --}}
            <div class="alert alert-warning mt-4" role="alert">
                <h6 class="alert-heading">📌 Informasi Penting</h6>
                <ul class="mb-0">
                    <li>Harap datang 15 menit sebelum jam tayang dimulai</li>
                    <li>Tunjukkan kode QR atau nomor pemesanan saat check-in</li>
                    <li>Tiket tidak dapat dipindahkan ke orang lain</li>
                    <li>Jika ada pertanyaan, hubungi customer service kami</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
    .font-monospace {
        font-family: 'Courier New', monospace !important;
        letter-spacing: 2px;
    }
</style>
@endsection
