@extends('layouts.app')

@push('styles')
@include('partials.customer_film_styles')
@include('partials.e_ticket_styles')
<style>
    body { background-color: #e4e8ef !important; }

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

    .cx-ticket-notes {
        background: #fff;
        border: 1px solid rgba(26, 25, 83, 0.1);
        border-radius: 14px;
        padding: 18px 20px;
        margin-top: 24px;
    }

    .cx-ticket-notes h6 {
        font-size: 0.88rem;
        font-weight: 800;
        color: #1f2533;
        margin-bottom: 12px;
    }

    .cx-ticket-notes ul {
        margin: 0;
        padding-left: 18px;
        color: #5c6478;
        font-size: 0.84rem;
        line-height: 1.65;
    }

    .cx-ticket-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 14px;
        border-radius: 12px;
        font-weight: 700;
        text-decoration: none;
        background: #fff;
        color: #1A1953;
        border: 1.5px solid rgba(26, 25, 83, 0.15);
        margin-top: 20px;
    }

    .cx-ticket-btn:hover { background: #f4f6fa; color: #1A1953; }
</style>
@endpush

@section('content')
<div class="container py-4 py-lg-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-4">
                <div class="cx-success-icon">
                    <iconify-icon icon="lucide:badge-check"></iconify-icon>
                </div>
                <h2 class="fw-bold text-dark mb-2">Pemesanan Berhasil!</h2>
                <p class="text-muted mb-0">Pembayaran telah dikonfirmasi. Tiket siap digunakan.</p>
            </div>

            @include('partials.e_ticket_card', ['booking' => $booking, 'downloadable' => true])

            <div class="cx-ticket-notes">
                <h6>Informasi Penting</h6>
                <ul>
                    <li>Datang 15 menit sebelum film dimulai untuk proses scan tiket.</li>
                    <li>Tunjukkan QR code atau ID booking kepada petugas bioskop.</li>
                    <li>Dilarang membawa makanan dan minuman dari luar bioskop.</li>
                </ul>
            </div>

            <a href="{{ route('landing-page') }}" class="cx-ticket-btn">
                <iconify-icon icon="lucide:home"></iconify-icon>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
