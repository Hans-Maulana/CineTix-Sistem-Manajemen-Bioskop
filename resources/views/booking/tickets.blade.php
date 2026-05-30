@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h2 class="mb-0 text-dark fw-bold">🎟️ Tiket Aktif Saya</h2>
                <a href="{{ route('landing-page') }}" class="btn btn-outline-secondary text-dark border-secondary px-4 rounded-pill">Kembali</a>
            </div>

            @if($bookings->isNotEmpty())
                <div class="row g-4">
                    @foreach($bookings as $booking)
                        @php
                            $firstTicket = $booking->ticketBookings->first();
                            $film = $firstTicket ? $firstTicket->schedule->film : null;
                            $seatCodes = $booking->ticketBookings->pluck('seat.seat_code')->implode(', ');
                        @endphp
                        @if($firstTicket)
                            <div class="col-md-6 d-flex justify-content-center">
                                <div class="ticket-card-container w-100 shadow-sm border-0" id="ticket-{{ $booking->id }}" style="max-width: 440px; font-family: 'Outfit', 'Inter', sans-serif; border-radius: 20px 20px 0 0; overflow: hidden; background: #f2bd52; transition: transform 0.2s;">
                                    <!-- Top Part (Dark Blue) -->
                                    <div class="ticket-top p-4 text-white" style="background: #112a46; border-bottom: 2px dashed rgba(242, 197, 95, 0.4); position: relative;">
                                        <div style="position: absolute; right: 15px; bottom: 15px; opacity: 0.05; font-size: 80px; pointer-events: none;">
                                            <iconify-icon icon="solar:film-strip-bold"></iconify-icon>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h4 class="fw-bold tracking-wide mb-0 text-truncate" style="color: #f2bd52; text-transform: uppercase; font-size: 1.15rem; max-width: 80%;">
                                                {{ $film->title ?? 'FILM' }}
                                            </h4>
                                            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 28px; height: 28px; background: rgba(255,255,255,0.1);">
                                                <span class="fw-bold small" style="color: #f2bd52;">C</span>
                                            </div>
                                        </div>
                                        
                                        <div class="row text-white text-opacity-95 small g-2 pt-2">
                                            <div class="col-4 border-end border-white border-opacity-10 pe-2">
                                                <div class="text-white text-opacity-50" style="font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">Hari</div>
                                                <div class="fw-bold text-white text-truncate" style="font-size: 12px;">{{ $firstTicket->schedule->schedule_date->translatedFormat('l') }}</div>
                                                <div class="fw-bold text-white" style="font-size: 13px;">{{ $firstTicket->schedule->schedule_date->translatedFormat('d M Y') }}</div>
                                            </div>
                                            <div class="col-5 border-end border-white border-opacity-10 px-2">
                                                <div class="text-white text-opacity-50" style="font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">Bioskop</div>
                                                <div class="fw-bold text-white text-truncate" style="font-size: 12px;">CineTix Bioskop</div>
                                                <div class="fw-bold text-white text-truncate" style="font-size: 11px;">{{ $firstTicket->schedule->studio->name }}</div>
                                            </div>
                                            <div class="col-3 ps-2">
                                                <div class="text-white text-opacity-50" style="font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">Jam</div>
                                                <div class="fw-bold text-white" style="font-size: 13px;">{{ $firstTicket->schedule->start_time->format('H:i') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Bottom Part (Golden Yellow) -->
                                    <div class="ticket-bottom p-4" style="background: #f2bd52; position: relative;">
                                        <div class="row align-items-center">
                                            <div class="col-8">
                                                <div class="mb-3">
                                                    <div class="text-dark text-opacity-70 small" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">ID Booking</div>
                                                    <span class="badge bg-white bg-opacity-50 text-dark fw-bold px-3 py-1.5 mt-1 rounded-pill" style="font-family: monospace; font-size: 13px; letter-spacing: 1px;">
                                                        {{ $booking->qr_redeem }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="text-dark text-opacity-70 small" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">
                                                        {{ $booking->ticketBookings->count() }} Tiket
                                                    </div>
                                                    <div class="fw-bold text-dark fs-5 mt-1">
                                                        {{ $seatCodes }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-4 text-center">
                                                <div class="p-2 bg-white rounded-3 shadow-sm d-inline-block" style="background: #112a46 !important; border: 2px solid #fff;">
                                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ $booking->qr_redeem }}" alt="QR Code" class="img-fluid" style="width: 70px; height: 70px;">
                                                    <div class="text-white fw-bold mt-1" style="font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Kode QR</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top border-dark border-opacity-10">
                                            <span class="text-dark text-opacity-80 small" style="font-size: 11px; font-weight: 500;">Download tiket ini ke ponselmu</span>
                                            <button class="btn btn-sm btn-dark px-3 rounded-pill text-white fw-bold d-flex align-items-center gap-1 download-ticket-btn" data-booking-id="{{ $booking->id }}" style="font-size: 10px; background: #112a46; border: none;">
                                                <iconify-icon icon="lucide:download"></iconify-icon> DOWNLOAD
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Scalloped bottom wavy edge -->
                                    <div class="ticket-scallop-bottom-edge" style="height: 10px; background: #f2bd52; position: relative;">
                                        <div style="position: absolute; left: 0; bottom: -10px; width: 100%; height: 10px; background-image: radial-gradient(circle at 50% 100%, transparent 6px, #f2bd52 7px); background-size: 16px 10px; background-repeat: repeat-x; z-index: 10;"></div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                
                {{-- Spacer for scalloped bottom visibility --}}
                <div style="height: 30px;"></div>
            @else
                <div class="card shadow-sm border-0 p-5 text-center rounded-4 bg-white">
                    <div class="display-1 mb-4 text-muted">🎬</div>
                    <h4 class="fw-bold text-dark">Belum Ada Tiket Aktif</h4>
                    <p class="text-muted">Semua tiket yang sudah dibayar dan belum tayang akan muncul di sini.</p>
                    <div class="mt-4 d-flex flex-column gap-2 justify-content-center">
                        <p class="text-muted small mb-0">
                            Tiket yang sudah tayang? <a href="{{ route('booking.history') }}" class="text-primary fw-bold text-decoration-none">Lihat Riwayat Transaksi</a> untuk memberikan ulasan.
                        </p>
                        <div>
                            <a href="{{ route('landing-page') }}" class="btn btn-primary px-5 py-2.5 rounded-pill fw-bold text-white shadow-sm" style="background: #1A1953 !important; border: none;">Pesan Tiket Sekarang</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Success Booking Popup Modal --}}
@if(session('success_booking'))
    @php
        $successBooking = \App\Models\Booking::find(session('success_booking'));
    @endphp
    @if($successBooking)
        <div class="modal fade" id="successBookingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow-lg text-center p-4" style="background: #ffffff;">
                    <div class="modal-body">
                        <div class="d-inline-block p-4 rounded-circle bg-success bg-opacity-10 mb-4 text-success">
                            <iconify-icon icon="solar:check-circle-bold-duotone" class="display-3"></iconify-icon>
                        </div>
                        <h3 class="fw-bold text-dark mb-2">Pembelian Berhasil!</h3>
                        <p class="text-muted mb-4">Pembayaran Anda telah terkonfirmasi. Tiket elektronik Anda sudah siap digunakan!</p>
                        
                        <div class="d-flex flex-column gap-2">
                            <button class="btn btn-primary py-2.5 rounded-pill text-white fw-bold shadow-sm" data-bs-dismiss="modal" style="background: #1A1953 !important; border: none;">
                                Lihat Tiket Saya
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif

@push('styles')
<style>
    .ticket-card-container {
        position: relative;
    }
    .ticket-card-container:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.12) !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Automatically show success booking modal if present
        var successModalEl = document.getElementById('successBookingModal');
        if (successModalEl) {
            var successModal = new bootstrap.Modal(successModalEl);
            successModal.show();
        }

        // Handle Download Ticket Click
        document.addEventListener('click', function(e) {
            let btn = e.target.closest('.download-ticket-btn');
            if (btn) {
                e.preventDefault();
                e.stopPropagation();
                let bookingId = btn.getAttribute('data-booking-id');
                let ticketEl = document.getElementById('ticket-' + bookingId);
                
                if (ticketEl) {
                    let originalText = btn.innerHTML;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>...';
                    btn.disabled = true;

                    // html2canvas config
                    html2canvas(ticketEl, {
                        scale: 3,
                        useCORS: true,
                        backgroundColor: null,
                        logging: false
                    }).then(canvas => {
                        let link = document.createElement('a');
                        link.download = 'Cinetix-Ticket-' + bookingId + '.png';
                        link.href = canvas.toDataURL('image/png');
                        link.click();
                        
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }).catch(err => {
                        console.error('Error downloading ticket:', err);
                        alert('Gagal mengunduh tiket. Silakan coba lagi.');
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    });
                }
            }
        });
    });
</script>
@endpush
@endsection
