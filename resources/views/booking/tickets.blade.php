@extends('layouts.app')

@push('styles')
@include('partials.customer_film_styles')
@include('partials.e_ticket_styles')
<style>
    body { background-color: #e4e8ef !important; }

    .cx-tickets-page { padding-bottom: 2.5rem; }

    .cx-tickets-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }

    .cx-tickets-head h2 {
        font-size: 1.35rem;
        font-weight: 800;
        color: #1f2533;
        margin: 0;
    }

    .cx-tickets-empty {
        background: #fff;
        border: 1px solid rgba(26, 25, 83, 0.1);
        border-radius: 18px;
        padding: 3rem 2rem;
        text-align: center;
        box-shadow: 0 4px 16px rgba(26, 25, 83, 0.06);
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
<div class="cx-tickets-page">
<div class="container py-4 py-lg-5">
    <div class="cx-tickets-head">
        <h2>
            <iconify-icon icon="lucide:ticket" class="me-1" style="color:#1A1953;"></iconify-icon>
            Tiket Aktif Saya
        </h2>
        <a href="{{ route('landing-page') }}" class="btn btn-back-custom rounded-pill px-4 py-2">Beranda</a>
    </div>

    @if($bookings->isNotEmpty())
        @if(session()->has('ticket_email_sent') && session('ticket_email_sent') === false)
            <div class="alert alert-warning rounded-4 border-0 shadow-sm mb-4" role="alert">
                <iconify-icon icon="lucide:mail-warning" class="me-2"></iconify-icon>
                Email tiket gagal dikirim otomatis. Tiket tetap aktif di halaman ini.
            </div>
        @elseif(session('success'))
            <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4" role="alert">
                <iconify-icon icon="lucide:check-circle" class="me-2"></iconify-icon>
                {{ session('success') }}
            </div>
        @endif

        <div class="row g-4">
            @foreach($bookings as $booking)
                <div class="col-lg-6 d-flex justify-content-center">
                    @include('partials.e_ticket_card', [
                        'booking' => $booking,
                        'downloadable' => true,
                        'ticketDomId' => 'ticket-' . $booking->id,
                    ])
                </div>
            @endforeach
        </div>
    @else
        <div class="cx-tickets-empty">
            <iconify-icon icon="lucide:ticket-x" style="font-size:3rem;color:#c5cad6;"></iconify-icon>
            <h4 class="fw-bold text-dark mt-3 mb-2">Belum Ada Tiket Aktif</h4>
            <p class="text-muted mb-3">Tiket yang sudah dibayar dan belum tayang akan muncul di sini.</p>
            <p class="text-muted small mb-4">
                Sudah tayang? <a href="{{ route('booking.history') }}" class="text-primary fw-bold text-decoration-none">Lihat riwayat transaksi</a>
            </p>
            <a href="{{ route('landing-page') }}" class="btn rounded-pill px-4 py-2 fw-bold text-white" style="background:#1A1953;">
                Pesan Tiket Sekarang
            </a>
        </div>
    @endif
</div>
</div>

@if(session('success_booking'))
    @php $successBooking = \App\Models\Booking::find(session('success_booking')); @endphp
    @if($successBooking)
        <div class="modal fade" id="successBookingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow-lg text-center p-4">
                    <div class="modal-body">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4"
                             style="width:72px;height:72px;background:rgba(25,167,95,0.12);color:#19a75f;font-size:2rem;">
                            <iconify-icon icon="lucide:badge-check"></iconify-icon>
                        </div>
                        <h3 class="fw-bold text-dark mb-2">Pembelian Berhasil!</h3>
                        <p class="text-muted mb-4">Pembayaran terkonfirmasi. Tiket elektronik Anda sudah siap.</p>
                        <button class="btn w-100 py-2.5 rounded-3 text-white fw-bold" data-bs-dismiss="modal" style="background:#1A1953;border:none;">
                            Lihat Tiket Saya
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const successModalEl = document.getElementById('successBookingModal');
        if (successModalEl && typeof bootstrap !== 'undefined') {
            bootstrap.Modal.getOrCreateInstance(successModalEl).show();
        }

        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.download-ticket-btn');
            if (!btn) return;

            e.preventDefault();
            const bookingId = btn.getAttribute('data-booking-id');
            const ticketEl = document.getElementById('ticket-' + bookingId);
            if (!ticketEl) return;

            const original = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            btn.disabled = true;

            html2canvas(ticketEl, {
                scale: 3,
                useCORS: true,
                backgroundColor: '#e4e8ef',
                logging: false,
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'CineTix-Ticket-' + bookingId + '.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
                btn.innerHTML = original;
                btn.disabled = false;
            }).catch(() => {
                alert('Gagal mengunduh tiket. Silakan coba lagi.');
                btn.innerHTML = original;
                btn.disabled = false;
            });
        });
    });
</script>
@endpush
@endsection
