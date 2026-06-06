@extends('layouts.admin')

@section('title', 'Tambah Promo')

@push('styles')
@include('admin.promos._form_styles')
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Hero -->
    <div class="pr-hero mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 position-relative" style="z-index:1;">
            <div>
                <a href="{{ route('admin.promos.index') }}" class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2 text-decoration-none"
                   style="background:rgba(255,255,255,0.12); color:rgba(255,255,255,0.9); font-size:0.78rem; font-weight:600;">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Promo
                </a>
                <h1>Buat Promo Baru</h1>
                <p class="mb-0" style="opacity:0.85; font-size:0.95rem;">Tentukan kode, diskon, periode, dan kuota pemakaian.</p>
            </div>
        </div>
    </div>

    @include('admin.promos._form')
</div>
@endsection

@push('scripts')
@include('admin.promos._form_scripts')
@endpush
