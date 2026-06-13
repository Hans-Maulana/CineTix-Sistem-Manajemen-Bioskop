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
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4 mx-auto" style="max-width:720px;" role="alert">
            <iconify-icon icon="lucide:check-circle" class="me-2"></iconify-icon>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
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
