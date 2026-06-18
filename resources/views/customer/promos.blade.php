@extends('layouts.app')

@push('styles')
<style>
    body {
        background-color: #f8f9fa; /* Soft off-white background */
    }
    
    .promo-header-title {
        font-size: clamp(2.5rem, 6vw, 4rem);
        font-weight: 800;
        background: linear-gradient(135deg, #1A1953 0%, #d4b06a 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        line-height: 1.1;
        letter-spacing: -0.03em;
    }

    .promo-card {
        background: #ffffff;
        border-radius: 24px;
        border: 1px solid rgba(26, 25, 83, 0.05);
        box-shadow: 0 12px 32px rgba(26, 25, 83, 0.06);
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .promo-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 24px 48px rgba(26, 25, 83, 0.12);
    }

    /* Decorative top accent */
    .promo-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, #1A1953, #3a37a0, #d4b06a);
    }

    .promo-status-badge {
        position: absolute;
        top: 24px;
        right: 24px;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 6px;
        z-index: 2;
    }
    
    .status-available {
        background: rgba(25, 167, 95, 0.1);
        color: #19a75f;
    }
    
    .status-used {
        background: rgba(26, 25, 83, 0.08);
        color: #8a93a6;
    }

    .promo-icon-wrapper {
        width: 64px;
        height: 64px;
        background: rgba(212, 176, 106, 0.15);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: #d4b06a;
        margin-bottom: 24px;
        transform: rotate(-10deg);
        transition: transform 0.4s;
    }

    .promo-card:hover .promo-icon-wrapper {
        transform: rotate(0deg) scale(1.05);
    }

    .promo-title {
        font-size: 1.25rem;
        font-weight: 800;
        color: #1f2533;
        margin-bottom: 8px;
        line-height: 1.4;
    }

    .promo-discount {
        font-size: 2.2rem;
        font-weight: 900;
        color: #1A1953;
        margin-bottom: 24px;
        display: flex;
        align-items: baseline;
        gap: 6px;
    }

    .promo-discount span {
        font-size: 0.9rem;
        font-weight: 700;
        color: #8a93a6;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Coupon Code Box */
    .promo-code-box {
        background: rgba(26, 25, 83, 0.03);
        border: 2px dashed rgba(26, 25, 83, 0.2);
        border-radius: 16px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        transition: all 0.3s;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .promo-code-box:hover {
        background: rgba(26, 25, 83, 0.08);
        border-color: #1A1953;
    }

    .promo-code-box::before, .promo-code-box::after {
        content: '';
        position: absolute;
        top: 50%;
        width: 16px;
        height: 16px;
        background: #ffffff;
        border-radius: 50%;
        border: 1px solid rgba(26, 25, 83, 0.08);
        transform: translateY(-50%);
    }

    .promo-code-box::before {
        left: -8px;
        border-left-color: transparent;
        border-top-color: transparent;
        border-bottom-color: transparent;
    }

    .promo-code-box::after {
        right: -8px;
        border-right-color: transparent;
        border-top-color: transparent;
        border-bottom-color: transparent;
    }

    .promo-code-text {
        font-family: 'Courier New', Courier, monospace;
        font-size: 1.4rem;
        font-weight: 900;
        color: #1A1953;
        letter-spacing: 3px;
        margin: 0;
    }

    .promo-copy-icon {
        color: #1A1953;
        font-size: 1.35rem;
        opacity: 0.6;
        transition: opacity 0.3s, transform 0.3s;
    }

    .promo-code-box:hover .promo-copy-icon {
        opacity: 1;
        transform: scale(1.1);
    }

    .promo-details-list {
        list-style: none;
        padding: 0;
        margin: 0 0 28px 0;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .promo-details-list li {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 0.9rem;
        color: #5c6478;
        line-height: 1.5;
    }

    .promo-details-list iconify-icon {
        color: #d4b06a;
        font-size: 1.15rem;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .promo-action-btn {
        margin-top: auto;
        border-radius: 14px;
        padding: 14px 24px;
        font-weight: 700;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.3s;
        width: 100%;
        text-decoration: none;
        border: none;
    }

    .btn-primary-custom {
        background: #1A1953;
        color: #ffffff;
    }

    .btn-primary-custom:hover {
        background: #14123e;
        color: #ffffff;
        box-shadow: 0 8px 24px rgba(26, 25, 83, 0.25);
        transform: translateY(-2px);
    }
    
    .btn-outline-custom {
        background: transparent;
        color: #1A1953;
        border: 2px solid #1A1953;
    }

    .btn-outline-custom:hover {
        background: #1A1953;
        color: #ffffff;
    }

    .btn-disabled-custom {
        background: #f4f5fa;
        color: #a0a6b5;
        border: 2px solid #e4e8ef;
        cursor: not-allowed;
    }

    /* Abstract shapes for header */
    .header-shapes {
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 350px;
        overflow: hidden;
        z-index: -1;
        pointer-events: none;
    }
    
    .shape-1 {
        position: absolute;
        top: -100px;
        right: -50px;
        width: 400px;
        height: 400px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(212, 176, 106, 0.12) 0%, rgba(212, 176, 106, 0) 70%);
    }

    .shape-2 {
        position: absolute;
        top: 50px;
        right: 250px;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(26, 25, 83, 0.08) 0%, rgba(26, 25, 83, 0) 70%);
    }

    /* Toast animation */
    .toast-container {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 1055;
    }
</style>
@endpush

@section('content')
<div class="header-shapes">
    <div class="shape-1"></div>
    <div class="shape-2"></div>
</div>

<div class="container py-5">
    <div class="row mb-5 align-items-center" data-aos="fade-down">
        <div class="col-lg-8 col-md-12">
            <h1 class="promo-header-title mb-3">Kode Promo Saya</h1>
            <p class="text-secondary fs-6 w-100 w-lg-75 lh-lg mb-0" style="font-weight: 500;">
                @if($isAuthenticated)
                    Salin kode promo pilihan Anda dan masukkan saat memilih kursi sebelum checkout untuk menikmati potongan harga menarik!
                @else
                    Nikmati diskon eksklusif untuk film favorit Anda. <a href="{{ route('login') }}" class="fw-bold text-primary text-decoration-none" style="color: #1A1953!important;">Masuk</a> atau <a href="{{ route('register') }}" class="fw-bold text-primary text-decoration-none" style="color: #1A1953!important;">Daftar</a> sekarang untuk menggunakan kode promo.
                @endif
            </p>
        </div>
        <div class="col-lg-4 col-md-12 text-lg-end mt-4 mt-lg-0">
            <a href="{{ route('landing-page') }}" class="btn btn-outline-custom rounded-pill px-4 py-2.5 fw-bold d-inline-flex align-items-center gap-2">
                <iconify-icon icon="lucide:arrow-left"></iconify-icon>
                Kembali ke Beranda
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4 border-0 shadow-sm mb-5 d-flex align-items-center gap-3" data-aos="fade-in">
            <iconify-icon icon="lucide:check-circle-2" style="font-size: 1.5rem; color: #19a75f;"></iconify-icon>
            <div class="fw-medium text-dark">{{ session('success') }}</div>
        </div>
    @endif

    @if($promos->isNotEmpty())
        <div class="row g-4 mb-5">
            @foreach($promos as $index => $item)
                @php
                    $promo = $item['promo'];
                    $status = $item['status'];
                    $isAvailable = $status['label'] === 'Tersedia' && $isAuthenticated;
                    $delay = $index * 100;
                @endphp

                <div class="col-xl-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $delay }}">
                    <div class="promo-card p-4 p-xl-5">
                        
                        <div class="promo-status-badge {{ $status['label'] === 'Tersedia' ? 'status-available' : 'status-used' }}">
                            <iconify-icon icon="{{ $status['label'] === 'Tersedia' ? 'lucide:check-circle' : 'lucide:x-circle' }}"></iconify-icon>
                            {{ $status['label'] }}
                        </div>

                        <div class="promo-icon-wrapper">
                            <iconify-icon icon="lucide:ticket-percent"></iconify-icon>
                        </div>

                        <h3 class="promo-title">{{ $promo->description ?? 'Promo Spesial CineTix' }}</h3>
                        
                        <div class="promo-discount">
                            {{ $promo->discount_type === 'percentage' ? $promo->discount_value . '%' : 'Rp' . number_format($promo->discount_value, 0, ',', '.') }}
                            <span>Diskon</span>
                        </div>

                        <div class="promo-code-box" onclick="copyPromoCode('{{ $promo->code }}')" title="Klik untuk menyalin">
                            <span class="promo-code-text">{{ $promo->code }}</span>
                            <iconify-icon icon="lucide:copy" class="promo-copy-icon"></iconify-icon>
                        </div>

                        <ul class="promo-details-list">
                            <li>
                                <iconify-icon icon="lucide:calendar-clock"></iconify-icon>
                                <span>Berlaku hingga <strong>{{ $promo->valid_until->format('d M Y') }}</strong></span>
                            </li>
                            <li>
                                <iconify-icon icon="lucide:user-check"></iconify-icon>
                                <span>Maks. penggunaan <strong>{{ $promo->max_usage_per_customer }}x</strong> per akun</span>
                            </li>

                        </ul>

                        <div class="mt-auto pt-3">
                            @if($isAvailable)
                                <a href="{{ route('films.search') }}" class="promo-action-btn btn-primary-custom">
                                    <iconify-icon icon="lucide:shopping-bag"></iconify-icon>
                                    Pesan Tiket Sekarang
                                </a>
                            @elseif(!$isAuthenticated)
                                <a href="{{ route('login') }}" class="promo-action-btn btn-primary-custom">
                                    <iconify-icon icon="lucide:log-in"></iconify-icon>
                                    Login untuk Pakai
                                </a>
                            @else
                                <button type="button" class="promo-action-btn btn-disabled-custom" disabled>
                                    <iconify-icon icon="lucide:ban"></iconify-icon>
                                    Sudah Terpakai
                                </button>
                            @endif
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5 my-5" data-aos="fade-up">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4" style="width: 100px; height: 100px; background: rgba(26, 25, 83, 0.05); color: #c5cad6;">
                <iconify-icon icon="lucide:ticket-x" style="font-size: 3.5rem;"></iconify-icon>
            </div>
            <h3 class="fw-bolder text-dark mb-3">Belum Ada Promo Saat Ini</h3>
            <p class="text-secondary fs-6 mb-4 w-75 mx-auto">Kami sedang menyiapkan penawaran menarik khusus untuk Anda. Cek kembali secara berkala ya!</p>
            <a href="{{ route('landing-page') }}" class="btn btn-outline-custom rounded-pill px-4 py-2.5 fw-bold d-inline-flex align-items-center gap-2">
                <iconify-icon icon="lucide:film"></iconify-icon>
                Lihat Film Tayang
            </a>
        </div>
    @endif
</div>

<!-- Toast for Copy Notification -->
<div class="toast-container">
    <div id="copyToast" class="toast align-items-center border-0 rounded-4 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000" style="background-color: #1A1953; color: #fff;">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-3 py-3 px-4 fw-medium fs-6">
                <iconify-icon icon="lucide:clipboard-check" style="font-size: 1.5rem; color: #19a75f;"></iconify-icon>
                <span id="copyToastMessage">Kode promo berhasil disalin!</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-3 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyPromoCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        const toastEl = document.getElementById('copyToast');
        const toastMessage = document.getElementById('copyToastMessage');
        toastMessage.innerHTML = 'Kode <strong>' + code + '</strong> berhasil disalin!';
        
        if (typeof bootstrap !== 'undefined') {
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        } else {
            alert('Kode promo "' + code + '" berhasil disalin!');
        }
    }).catch(err => {
        console.error('Gagal menyalin teks: ', err);
    });
}
</script>
@endpush
@endsection
