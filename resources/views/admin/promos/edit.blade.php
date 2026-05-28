@extends('layouts.admin')

@section('title', 'Edit Promo')

@section('content')
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-1">✎ Edit Kode Promo</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.promos.index') }}" class="btn btn-secondary">← Kembali</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.promos.update', $promo) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="code" class="form-label fw-bold">Kode Promo</label>
                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" 
                           value="{{ old('code', $promo->code) }}" required>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">Deskripsi</label>
                    <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" 
                           name="description" value="{{ old('description', $promo->description) }}">
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="discount_type" class="form-label fw-bold">Tipe Diskon</label>
                            <select class="form-select @error('discount_type') is-invalid @enderror" id="discount_type" name="discount_type" required>
                                <option value="fixed" {{ old('discount_type', $promo->discount_type) === 'fixed' ? 'selected' : '' }}>Fixed (Jumlah Tetap)</option>
                                <option value="percentage" {{ old('discount_type', $promo->discount_type) === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                            </select>
                            @error('discount_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="discount_value" class="form-label fw-bold">Nilai Diskon</label>
                            <input type="number" step="0.01" class="form-control @error('discount_value') is-invalid @enderror" 
                                   id="discount_value" name="discount_value" value="{{ old('discount_value', $promo->discount_value) }}" required>
                            @error('discount_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="valid_from" class="form-label fw-bold">Berlaku Dari</label>
                            <input type="date" class="form-control @error('valid_from') is-invalid @enderror" 
                                   id="valid_from" name="valid_from" value="{{ old('valid_from', $promo->valid_from->format('Y-m-d')) }}" required>
                            @error('valid_from')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="valid_until" class="form-label fw-bold">Berlaku Hingga</label>
                            <input type="date" class="form-control @error('valid_until') is-invalid @enderror" 
                                   id="valid_until" name="valid_until" value="{{ old('valid_until', $promo->valid_until->format('Y-m-d')) }}" required>
                            @error('valid_until')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="max_usage" class="form-label fw-bold">Max Usage Total</label>
                            <input type="number" class="form-control @error('max_usage') is-invalid @enderror" 
                                   id="max_usage" name="max_usage" value="{{ old('max_usage', $promo->max_usage) }}">
                            <small class="text-muted">Kosongkan untuk unlimited</small>
                            @error('max_usage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="max_usage_per_customer" class="form-label fw-bold">Max Usage Per Customer</label>
                            <input type="number" class="form-control @error('max_usage_per_customer') is-invalid @enderror" 
                                   id="max_usage_per_customer" name="max_usage_per_customer" value="{{ old('max_usage_per_customer', $promo->max_usage_per_customer) }}" required>
                            <small class="text-muted">Berapa kali 1 customer bisa pakai promo ini</small>
                            @error('max_usage_per_customer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-3">
                    <small>
                        <strong>Status Penggunaan:</strong> {{ $promo->usage_count }} / {{ $promo->max_usage ?? '∞' }} penggunaan
                    </small>
                </div>

                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="{{ route('admin.promos.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">✓ Update Promo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
