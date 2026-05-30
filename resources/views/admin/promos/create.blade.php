@extends('layouts.admin')

@section('title', 'Tambah Promo Baru')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h1 class="fw-bold text-primary">Tambah Promo Baru</h1>
        <p class="text-muted">Isi formulir di bawah untuk membuat kode penawaran baru.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="{{ route('admin.promos.index') }}" class="btn btn-outline-secondary text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="card-custom p-4">
    <form method="POST" action="{{ route('admin.promos.store') }}">
        @csrf

        <div class="mb-4">
            <label for="code" class="form-label fw-bold">Kode Promo <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code"
                   value="{{ old('code') }}" placeholder="Contoh: WEEKEND50" required style="text-transform: uppercase;">
            @error('code')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="description" class="form-label fw-bold">Deskripsi Promo</label>
            <input type="text" class="form-control @error('description') is-invalid @enderror" id="description"
                   name="description" value="{{ old('description') }}" placeholder="Contoh: Diskon khusus akhir pekan">
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row mb-4">
            <div class="col-md-6 mb-3 mb-md-0">
                <label for="discount_type" class="form-label fw-bold">Tipe Potongan <span class="text-danger">*</span></label>
                <select class="form-select @error('discount_type') is-invalid @enderror" id="discount_type" name="discount_type" required>
                    <option value="">-- Pilih Tipe Potongan --</option>
                    <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rupiah)</option>
                    <option value="percentage" {{ old('discount_type') === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                </select>
                @error('discount_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="discount_value" class="form-label fw-bold">Nilai Diskon <span class="text-danger">*</span></label>
                <input type="number" step="0.01" class="form-control @error('discount_value') is-invalid @enderror"
                       id="discount_value" name="discount_value" value="{{ old('discount_value') }}"
                       placeholder="Contoh: 15000 atau 10" required>
                @error('discount_value')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6 mb-3 mb-md-0">
                <label for="valid_from" class="form-label fw-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                <input type="date" class="form-control @error('valid_from') is-invalid @enderror"
                       id="valid_from" name="valid_from" value="{{ old('valid_from') }}" required>
                @error('valid_from')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="valid_until" class="form-label fw-bold">Tanggal Berakhir <span class="text-danger">*</span></label>
                <input type="date" class="form-control @error('valid_until') is-invalid @enderror"
                       id="valid_until" name="valid_until" value="{{ old('valid_until') }}" required>
                @error('valid_until')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6 mb-3 mb-md-0">
                <label for="max_usage" class="form-label fw-bold">Total Kuota Penggunaan</label>
                <input type="number" class="form-control @error('max_usage') is-invalid @enderror"
                       id="max_usage" name="max_usage" value="{{ old('max_usage') }}"
                       placeholder="Kosongkan jika tidak terbatas (∞)">
                @error('max_usage')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="max_usage_per_customer" class="form-label fw-bold">Limit per Customer <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('max_usage_per_customer') is-invalid @enderror"
                       id="max_usage_per_customer" name="max_usage_per_customer" value="{{ old('max_usage_per_customer', 1) }}" required>
                @error('max_usage_per_customer')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <hr class="my-4">

        <div class="d-flex justify-content-end gap-2">
            <button type="reset" class="btn btn-light border">Reset</button>
            <button type="submit" class="btn-teal border-0">
                <i class="bi bi-save me-1"></i> Simpan Promo
            </button>
        </div>
    </form>
</div>
@endsection
