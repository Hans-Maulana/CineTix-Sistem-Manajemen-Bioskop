@php
    $firstTicket = $booking->ticketBookings->first();
    $schedule = $firstTicket?->schedule;
    $film = $schedule?->film;
    $seatCodes = $booking->ticketBookings->map(fn ($t) => $t->seat->seat_code)->implode(', ');
    $downloadable = $downloadable ?? false;
    $ticketDomId = $ticketDomId ?? ('ticket-' . $booking->id);
@endphp

@if($firstTicket)
<div class="cx-eticket-wrap" id="{{ $ticketDomId }}">
    <div class="cx-eticket">
        <div class="cx-eticket-notch left"></div>
        <div class="cx-eticket-notch right"></div>

        <div class="cx-eticket-top">
            <div class="cx-eticket-brand">
                <iconify-icon icon="lucide:ticket"></iconify-icon>
                CineTix E-Ticket
            </div>
            <div class="cx-eticket-meta-top">
                <span class="cx-eticket-status {{ in_array($booking->status, ['refunded', 'cancelled']) ? 'bg-danger text-white' : '' }}">
                    {{ $booking->status === 'refunded' ? 'Refund Selesai' : ($booking->status === 'cancelled' ? 'Dibatalkan' : 'Terkonfirmasi') }}
                </span>
                <div class="cx-eticket-booking-code">
                    <span class="cx-eticket-booking-label">Kode Booking</span>
                    <span class="cx-eticket-booking-value">{{ $booking->qr_redeem }}</span>
                </div>
            </div>
        </div>

        <div class="cx-eticket-body">
            <div class="cx-eticket-poster">
                @if($film?->cover_url)
                    <img src="{{ $film->cover_url }}" alt="{{ $film->title }}" style="{{ in_array($booking->status, ['refunded', 'cancelled']) ? 'filter: grayscale(100%); opacity: 0.8;' : '' }}">
                @else
                    <div class="cx-eticket-poster-placeholder">
                        <iconify-icon icon="lucide:film"></iconify-icon>
                    </div>
                @endif
            </div>

            <div class="cx-eticket-info">
                <h3 class="cx-eticket-title">{{ $film?->title ?? 'Film' }}</h3>
                <div class="cx-eticket-grid">
                    <div class="cx-eticket-field">
                        <label>Tanggal</label>
                        <span>{{ $schedule->schedule_date->format('d M Y') }}</span>
                    </div>
                    <div class="cx-eticket-field">
                        <label>Jam</label>
                        <span>{{ $schedule->start_time->format('H:i') }} – {{ $schedule->end_time->format('H:i') }}</span>
                    </div>
                    <div class="cx-eticket-field">
                        <label>Studio</label>
                        <span>{{ $schedule->studio->name }}</span>
                    </div>
                    <div class="cx-eticket-field">
                        <label>Kursi</label>
                        <span class="cx-eticket-seats">{{ $seatCodes }}</span>
                    </div>
                    <div class="cx-eticket-field cx-eticket-field--full">
                        <label>Kode Booking</label>
                        <span class="cx-eticket-booking-inline">{{ $booking->qr_redeem }}</span>
                    </div>
                </div>
            </div>

            <div class="cx-eticket-qr">
                <div class="cx-eticket-qr-frame" style="background: white; padding: 10px; border-radius: 8px; display: inline-block;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&margin=4&data={{ urlencode($booking->qr_redeem) }}"
                         alt="QR Code Tiket" style="display: block;">
                </div>
                <p class="cx-eticket-qr-hint mt-2">Scan di pintu masuk bioskop</p>
            </div>
        </div>

        @if(in_array($booking->status, ['refunded', 'cancelled']))
            <div class="cx-eticket-footer d-flex justify-content-center" style="background-color: #fdf5f6; border-top: 1px dashed #f5c6cb;">
                <p class="text-danger fw-bold mb-0 d-flex align-items-center gap-2">
                    <iconify-icon icon="lucide:info" style="font-size: 1.2rem;"></iconify-icon>
                    Tiket ini telah {{ $booking->status === 'refunded' ? 'direfund' : 'dibatalkan' }} dan tidak dapat digunakan.
                </p>
            </div>
        @elseif($downloadable)
            <div class="cx-eticket-footer">
                <p class="mb-0">Simpan tiket di ponsel untuk check-in lebih cepat</p>
                <div class="d-flex align-items-center gap-2">
                    @php $booking->loadMissing('ticketBookings.schedule'); @endphp
                    @if($booking->canRequestRefund())
                        <a href="{{ route('booking.refund.request', $booking) }}" class="cx-eticket-refund">
                            <iconify-icon icon="lucide:rotate-ccw"></iconify-icon>
                            Ajukan Refund
                        </a>
                    @endif
                    <button type="button" class="cx-eticket-download download-ticket-btn" data-booking-id="{{ $booking->id }}">
                        <iconify-icon icon="lucide:download"></iconify-icon>
                        Unduh Tiket
                    </button>
                </div>
            </div>
        @else
            <div class="cx-eticket-footer">
                <p>Tunjukkan QR code ini kepada petugas bioskop saat masuk</p>
            </div>
        @endif
    </div>
</div>
@endif
