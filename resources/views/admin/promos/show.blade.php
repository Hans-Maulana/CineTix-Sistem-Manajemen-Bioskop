@extends('layouts.admin')

@section('title', 'Detail Promo')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h1 class="fw-bold text-primary">Kupon: {{ $promo->code }}</h1>
        <p class="text-muted">{{ $promo->description ?? 'Detail dan statistik penggunaan kode promo.' }}</p>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="{{ route('admin.promos.edit', $promo) }}" class="btn btn-warning fw-bold text-decoration-none me-2">
            <i class="bi bi-pencil-square me-1"></i> Edit Data
        </a>
        <a href="{{ route('admin.promos.index') }}" class="btn btn-outline-secondary text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card-custom h-100 p-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-ticket-detailed me-2 text-primary"></i>Informasi Dasar</h5>

            <table class="table table-borderless mb-0">
                <tr>
                    <td class="text-muted px-0 py-2 w-50">Kode Kupon</td>
                    <td class="fw-bold px-0 py-2">{{ $promo->code }}</td>
                </tr>
                <tr>
                    <td class="text-muted px-0 py-2">Tipe Potongan</td>
                    <td class="fw-bold px-0 py-2">{{ $promo->discount_type === 'percentage' ? 'Persentase' : 'Nominal Tetap (Rupiah)' }}</td>
                </tr>
                <tr>
                    <td class="text-muted px-0 py-2">Besaran Diskon</td>
                    <td class="fw-bold px-0 py-2 text-success">
                        {{ $promo->discount_type === 'percentage' ? $promo->discount_value . '%' : 'Rp ' . number_format($promo->discount_value, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td class="text-muted px-0 py-2">Periode Valid</td>
                    <td class="fw-bold px-0 py-2">{{ $promo->valid_from->format('d M Y') }} s/d {{ $promo->valid_until->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td class="text-muted px-0 py-2 border-bottom-0">Status Aktif</td>
                    <td class="px-0 py-2 border-bottom-0">
                        @if($promo->isValid())
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Tidak Aktif</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card-custom h-100 p-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Kuota & Limit</h5>

            <div class="mb-4">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Total Penggunaan Sistem</span>
                    <span class="fw-bold">{{ $promo->usage_count }} / {{ $promo->max_usage ?? '∞' }}</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar {{ $promo->max_usage && $promo->usage_count >= $promo->max_usage ? 'bg-danger' : 'bg-primary' }}"
                         style="width: {{ $promo->max_usage ? ($promo->usage_count / $promo->max_usage * 100) : 0 }}%">
                    </div>
                </div>
            </div>

            <table class="table table-borderless mb-0">
                <tr>
                    <td class="text-muted px-0 py-2 w-75">Batas Pakai Per Akun User</td>
                    <td class="fw-bold px-0 py-2 text-end">{{ $promo->max_usage_per_customer }}x</td>
                </tr>
                <tr>
                    <td class="text-muted px-0 py-2 w-75">Maksimal Total Penggunaan</td>
                    <td class="fw-bold px-0 py-2 text-end">{{ $promo->max_usage ?? 'Tidak Terbatas' }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection
