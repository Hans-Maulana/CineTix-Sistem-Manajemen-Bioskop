@extends('layouts.app')

@push('styles')
@include('partials.customer_film_styles')
@include('partials.e_ticket_styles')
<style>
    body { background-color: #e4e8ef !important; }

    .cx-ticket-success-page { padding-bottom: 2.5rem; }

    .cx-success-hero {
        text-align: center;
        margin-bottom: 28px;
    }

    .cx-success-icon {
        width: 72px;
        height: 72px;
        margin: 0 auto 16px;
        border-radius: 999px;
        background: rgba(25, 167, 95, 0.12);
        color: #19a75f;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
    }

    .cx-success-hero h2 {
        font-size: clamp(1.4rem, 4vw, 1.85rem);
        font-weight: 800;
        color: #1f2533;
        margin-bottom: 8px;
    }

    .cx-success-hero p {
        color: #8a93a6;
        margin: 0;
        font-size: 0.92rem;
    }

    .cx-success-hero strong {
        color: #1A1953;
    }

    .cx-ticket-notes {
        max-width: 720px;
        margin: 24px auto 0;
        background: #fff;
        border: 1px solid rgba(26, 25, 83, 0.1);
        border-radius: 14px;
        padding: 18px 20px;
        box-shadow: 0 2px 10px rgba(26, 25, 83, 0.05);
    }

    .cx-ticket-notes h6 {
        font-size: 0.88rem;
        font-weight: 800;
        color: #1f2533;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .cx-ticket-notes ul {
        margin: 0;
        padding-left: 18px;
        color: #5c6478;
        font-size: 0.84rem;
        line-height: 1.65;
    }

    .cx-ticket-notes li + li { margin-top: 6px; }

    .cx-ticket-actions {
        max-width: 720px;
        margin: 20px auto 0;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .cx-ticket-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 14px 18px;
        border-radius: 12px;
        font-size: 0.92rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.18s ease;
    }

    .cx-ticket-btn-primary {
        background: #1A1953;
        color: #fff;
        border: none;
    }

    .cx-ticket-btn-primary:hover {
        background: #14123e;
        color: #fff;
    }

    .cx-ticket-btn-outline {
        background: #fff;
        color: #1A1953;
        border: 1.5px solid #1A1953;
    }

    .cx-ticket-btn-outline:hover {
        background: #f4f5fa;
        color: #1A1953;
    }
</style>
@endpush

@section('content')
<div class="cx-ticket-success-page">
<div class="container py-4 py-lg-5">
    @if(session('success'))
        <!-- Modal Success Resend -->
        <div class="modal fade" id="successResendModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
                <div class="modal-content" style="border: none; border-radius: 24px; text-align: center; padding: 32px 24px; box-shadow: 0 24px 60px rgba(26,25,83,0.15);">
                    <div style="width: 72px; height: 72px; background: rgba(25,167,95,0.12); color: #19a75f; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; margin: 0 auto 20px;">
                        <iconify-icon icon="lucide:check-circle-2"></iconify-icon>
                    </div>
                    <h5 style="font-weight: 800; color: #1f2533; margin-bottom: 12px; font-size: 1.25rem;">Berhasil!</h5>
                    <p style="color: #5c6478; font-size: 0.95rem; line-height: 1.6; margin-bottom: 24px;">
                        {{ session('success') }}
                    </p>
                    <button type="button" class="cx-ticket-btn cx-ticket-btn-primary w-100" data-bs-dismiss="modal">
                        Oke, Selesai
                    </button>
                </div>
            </div>
        </div>
        
        @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                if (typeof bootstrap !== 'undefined') {
                    var successModal = new bootstrap.Modal(document.getElementById('successResendModal'));
                    successModal.show();
                }
            });
        </script>
        @endpush
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4 mx-auto" style="max-width:720px;" role="alert">
            <iconify-icon icon="lucide:alert-circle" class="me-2"></iconify-icon>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session()->has('ticket_email_sent') && session('ticket_email_sent') === false)
        <div class="alert alert-warning alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4 mx-auto" style="max-width:720px;" role="alert">
            <iconify-icon icon="lucide:mail-warning" class="me-2"></iconify-icon>
            Email tiket gagal dikirim otomatis. Gunakan tombol kirim ulang di bawah atau simpan tiket di halaman ini.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php $recipientEmail = $booking->customerEmail(); @endphp

    <div class="cx-success-hero">
        <div class="cx-success-icon">
            <iconify-icon icon="lucide:badge-check"></iconify-icon>
        </div>
        <h2>Tiket Anda Siap!</h2>
        @if(session('ticket_email_sent') === true)
            <p>Salinan tiket telah dikirim ke <strong>{{ $recipientEmail }}</strong>. Periksa inbox atau folder spam/promosi.</p>
        @elseif(session('ticket_email_sent') === false)
            <p>Tiket aktif untuk <strong>{{ $recipientEmail }}</strong>. Email belum terkirim — gunakan tombol kirim ulang di bawah.</p>
        @else
            <p>Tiket aktif untuk <strong>{{ $recipientEmail }}</strong>. Jika email belum masuk, cek folder spam atau kirim ulang di bawah.</p>
        @endif
    </div>

    @include('partials.e_ticket_card', ['booking' => $booking, 'downloadable' => false])

    <div class="cx-ticket-notes">
        <h6>
            <iconify-icon icon="lucide:info" style="color:#1A1953;"></iconify-icon>
            Informasi Penting
        </h6>
        <ul>
            <li>Datang minimal 15 menit sebelum jam tayang untuk scan tiket.</li>
            <li>Tunjukkan QR code atau ID booking kepada petugas bioskop.</li>
            <li>Simpan screenshot tiket ini sebagai cadangan.</li>
        </ul>
    </div>

    <div class="cx-ticket-actions">
        @if($booking->canGuestRequestRefund())
            <a href="{{ route('booking.guest-refund.request', ['booking' => $booking, 'token' => request('token')]) }}" class="cx-ticket-btn" style="background: #fff; color: #dc3545; border: 1px solid #dc3545; transition: all 0.2s;" onmouseover="this.style.background='#dc3545'; this.style.color='#fff';" onmouseout="this.style.background='#fff'; this.style.color='#dc3545';">
                <iconify-icon icon="lucide:rotate-ccw"></iconify-icon>
                Ajukan Refund Tiket
            </a>
        @elseif($booking->status === 'refunded')
            <div class="alert alert-info py-2">
                <iconify-icon icon="lucide:check-circle" class="me-1"></iconify-icon> Status: <strong>Refund Selesai (Auto Refund)</strong>
            </div>
        @endif

        @if($recipientEmail)
            <form method="POST" action="{{ route('booking.resend-ticket', array_filter(['booking' => $booking, 'token' => request('token')])) }}">
                @csrf
                <button type="submit" class="cx-ticket-btn cx-ticket-btn-outline">
                    <iconify-icon icon="lucide:mail"></iconify-icon>
                    Kirim Ulang Email Tiket
                </button>
            </form>
        @endif
        <a href="{{ route('landing-page') }}" class="cx-ticket-btn cx-ticket-btn-primary">
            <iconify-icon icon="lucide:home"></iconify-icon>
            Kembali ke Beranda
        </a>
    </div>
</div>
</div>
@endsection
