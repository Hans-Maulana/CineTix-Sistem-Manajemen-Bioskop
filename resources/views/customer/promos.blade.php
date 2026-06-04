@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row mb-5 align-items-center">
        <div class="col-md-9">
            <h1 class="fw-bolder text-dark lh-sm" style="font-size: 5rem; letter-spacing: -2px;">
                Kode<br>Promo Saya
            </h1>
            <p class="text-secondary mt-3 fs-6 w-75">
                @if($isAuthenticated)
                    Salin kode promo lalu masukkan saat memilih kursi sebelum checkout untuk mendapatkan potongan harga.
                @else
                    <a href="{{ route('login') }}" class="fw-bold text-dark text-decoration-none">Login</a> atau
                    <a href="{{ route('register') }}" class="fw-bold text-dark text-decoration-none">daftar</a>
                    untuk memakai kode promo (misalnya <strong>WELCOME2026</strong> diskon Rp 20.000).
                @endif
            </p>
        </div>
        <div class="col-md-3 text-md-end mt-4 mt-md-0">
            <a href="{{ route('landing-page') }}" class="btn text-white rounded-pill px-5 py-3 fw-medium" style="background-color: #1a1843;">
                Beranda
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($promos->isNotEmpty())
        <div class="row g-4">
            @foreach($promos as $item)
                @php
                    $promo = $item['promo'];
                    $status = $item['status'];
                    // Logic untuk mengecek apakah promo bisa dipakai
                    $isAvailable = $status['label'] === 'Tersedia' && $isAuthenticated;
                @endphp

                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" style="background-color: #ffffff;">

                        <div class="px-4 py-3" style="background-color: #1f2937;">
                            <span class="text-white fw-medium small">{{ $status['label'] }}</span>
                        </div>

                        <div class="card-body p-4 d-flex flex-column">
                            <h5 class="fw-bold text-dark mb-4">{{ $promo->description ?? 'Promo Spesial' }}</h5>

                            <div class="text-center rounded-pill py-3 mb-4" style="background-color: #212529;">
                                <span class="font-monospace fs-5 fw-bold text-white user-select-all tracking-wider">
                                    {{ $promo->code }}
                                </span>
                            </div>

                            <p class="text-secondary fw-semibold mb-2">
                                {{ $promo->discount_type === 'percentage' ? $promo->discount_value . '%' : 'Rp ' . number_format($promo->discount_value, 0, ',', '.') }}
                            </p>

                            <div class="text-secondary small mb-4">
                                <div class="mb-1">Berlaku {{ $promo->valid_from->format('d M') }} – {{ $promo->valid_until->format('d M Y') }}</div>
                                <div>Maks. {{ $promo->max_usage_per_customer }}x per akun</div>

                                @if($isAuthenticated && $item['user_usage'] > 0)
                                    <div class="mt-1">Anda sudah pakai: {{ $item['user_usage'] }}x</div>
                                @endif
                            </div>

                            <div class="mt-auto d-flex flex-column gap-3">
                                @if($isAvailable)
                                    <button type="button" class="btn text-white rounded-pill py-2 fw-semibold"
                                            onclick="copyPromoCode('{{ $promo->code }}')">
                                        Salin Kode
                                    </button>
                                    <a href="{{ route('films.search') }}" class="btn text-white rounded-pill py-2 fw-semibold"
                                       style="background-color: #1a1843;">
                                        Pesan Tiket
                                    </a>
                                @elseif(!$isAuthenticated)
                                    <a href="{{ route('login') }}" class="btn text-white rounded-pill py-2 fw-semibold"
                                       style="background-color: #1a1843;">
                                        Login untuk Pakai
                                    </a>
                                @else
                                    <button type="button" class="btn btn-light border rounded-pill py-2 fw-semibold text-muted" disabled>
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
        <div class="text-center py-5 mt-5">
            <h4 class="fw-bold text-dark">Belum Ada Promo</h4>
            <p class="text-secondary">Belum ada kode promo aktif saat ini. Cek kembali nanti ya!</p>
        </div>
    @endif
</div>

@push('scripts')
<style>
    .tracking-wider {
        letter-spacing: 0.1em;
    }
</style>
<script>
function copyPromoCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        alert('Kode promo "' + code + '" berhasil disalin!');
    }).catch(err => {
        console.error('Gagal menyalin teks: ', err);
    });
}
</script>
@endpush
@endsection
