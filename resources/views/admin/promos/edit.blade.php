@extends('layouts.admin')

@section('title', 'Edit Promo')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h1 class="fw-bold text-primary">Edit Kode Promo</h1>
        <p class="text-muted">Ubah detail data promo aktif Anda di sini.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="{{ route('admin.promos.index') }}" class="btn btn-outline-secondary text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="card-custom p-4">
    <form method="POST" action="{{ route('admin.promos.update', $promo) }}">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="code" class="form-label fw-bold">Kode Promo <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code"
                   value="{{ old('code', $promo->code) }}" required style="text-transform: uppercase;">
            @error('code')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="description" class="form-label fw-bold">Deskripsi Promo</label>
            <input type="text" class="form-control @error('description') is-invalid @enderror" id="description"
                   name="description" value="{{ old('description', $promo->description) }}">
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row mb-4">
            <div class="col-md-6 mb-3 mb-md-0">
                <label for="discount_type" class="form-label fw-bold">Tipe Potongan <span class="text-danger">*</span></label>
                <select class="form-select @error('discount_type') is-invalid @enderror" id="discount_type" name="discount_type" required>
                    <option value="fixed" {{ old('discount_type', $promo->discount_type) === 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rupiah)</option>
                    <option value="percentage" {{ old('discount_type', $promo->discount_type) === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                </select>
                @error('discount_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="discount_value" class="form-label fw-bold">Nilai Diskon <span class="text-danger">*</span></label>
                <input type="number" step="0.01" class="form-control @error('discount_value') is-invalid @enderror"
                       id="discount_value" name="discount_value" value="{{ old('discount_value', $promo->discount_value) }}" required>
                @error('discount_value')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6 mb-3 mb-md-0">
                <label for="valid_from" class="form-label fw-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                <input type="date" class="form-control @error('valid_from') is-invalid @enderror"
                       id="valid_from" name="valid_from" value="{{ old('valid_from', $promo->valid_from->format('Y-m-d')) }}" required>
                @error('valid_from')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="valid_until" class="form-label fw-bold">Tanggal Berakhir <span class="text-danger">*</span></label>
                <input type="date" class="form-control @error('valid_until') is-invalid @enderror"
                       id="valid_until" name="valid_until" value="{{ old('valid_until', $promo->valid_until->format('Y-m-d')) }}" required>
                @error('valid_until')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6 mb-3 mb-md-0">
                <label for="max_usage" class="form-label fw-bold">Total Kuota Penggunaan</label>
                <input type="number" class="form-control @error('max_usage') is-invalid @enderror"
                       id="max_usage" name="max_usage" value="{{ old('max_usage', $promo->max_usage) }}">
                @error('max_usage')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="max_usage_per_customer" class="form-label fw-bold">Limit per Customer <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('max_usage_per_customer') is-invalid @enderror"
                       id="max_usage_per_customer" name="max_usage_per_customer" value="{{ old('max_usage_per_customer', $promo->max_usage_per_customer) }}" required>
                @error('max_usage_per_customer')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="alert alert-info border-0 d-flex align-items-center mb-4">
            <i class="bi bi-info-circle-fill me-2 fs-5"></i>
            <div>
                Promo ini sudah digunakan sebanyak <strong>{{ $promo->usage_count }} kali</strong> dari total kuota.
            </div>
        </div>

        <hr class="my-4">

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.promos.index') }}" class="btn btn-light border">Batal</a>
            <button type="submit" class="btn-teal border-0">
                <i class="bi bi-save me-1"></i> Perbarui Promo
            </button>
        </div>
    </form>
</div>
@endsection
