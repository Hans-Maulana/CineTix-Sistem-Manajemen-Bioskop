@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h1 class="fw-bold text-dark mb-2">{{ $isAuthenticated ? 'Kode Promo Saya' : 'Kode Promo' }}</h1>
            <p class="text-muted mb-0">
                @if($isAuthenticated)
                    Salin kode promo lalu masukkan saat memilih kursi sebelum checkout.
                @else
                    <a href="{{ route('login') }}" class="fw-bold">Login</a> atau
                    <a href="{{ route('register') }}" class="fw-bold">daftar</a>
                    untuk memakai kode promo (misalnya <strong>WELCOME2026</strong> diskon Rp 20.000).
                @endif
            </p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ route('landing-page') }}" class="btn btn-outline-secondary rounded-pill px-4">← Beranda</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4 border-0 shadow-sm">{{ session('success') }}</div>
    @endif

    @if($promos->isNotEmpty())
        <div class="row g-4">
            @foreach($promos as $item)
                @php
                    $promo = $item['promo'];
                    $status = $item['status'];
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <span class="badge bg-{{ $status['badge'] }}">{{ $status['label'] }}</span>
                            <small class="text-muted">{{ $item['remaining'] }}x tersisa</small>
                        </div>
                        <div class="card-body p-4 d-flex flex-column">
                            <h5 class="fw-bold text-dark mb-1">{{ $promo->description ?? 'Promo Spesial' }}</h5>
                            <div class="font-monospace fs-4 fw-bold text-primary mb-3 user-select-all">{{ $promo->code }}</div>

                            <p class="fs-3 fw-bold text-success mb-2">{{ $promo->discountLabel() }}</p>
                            <p class="text-muted small mb-3">
                                Berlaku {{ $promo->valid_from->format('d M Y') }} – {{ $promo->valid_until->format('d M Y') }}
                                <br>
                                Maks. {{ $promo->max_usage_per_customer }}x per akun
                                @if($isAuthenticated && $item['user_usage'] > 0)
                                    <br>Anda sudah pakai: {{ $item['user_usage'] }}x
                                @endif
                            </p>

                            <div class="mt-auto d-grid gap-2">
                                @if($status['label'] === 'Tersedia' && $isAuthenticated)
                                    <button type="button" class="btn btn-primary text-white fw-bold rounded-3"
                                            onclick="copyPromoCode('{{ $promo->code }}')">
                                        Salin Kode
                                    </button>
                                    <a href="{{ route('films.search') }}" class="btn btn-outline-primary rounded-3 fw-bold">
                                        Pesan Tiket
                                    </a>
                                @elseif(!$isAuthenticated)
                                    <a href="{{ route('login') }}" class="btn btn-primary text-white fw-bold rounded-3">
                                        Login untuk Pakai
                                    </a>
                                @else
                                    <button type="button" class="btn btn-outline-secondary rounded-3" disabled>
                                        Tidak Tersedia
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5">
                <p class="text-muted mb-0">Belum ada kode promo aktif saat ini. Cek kembali nanti!</p>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function copyPromoCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        alert('Kode "' + code + '" disalin! Tempel saat checkout.');
    });
}
</script>
@endpush
@endsection
