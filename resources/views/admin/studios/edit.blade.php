@extends('layouts.admin')

@section('title', 'Edit Studio')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h1 class="fw-bold text-primary">Edit Studio: {{ $studio->name }}</h1>
        <p class="text-muted">Perbarui informasi kapasitas atau tipe studio.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="{{ route('admin.studios.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card-custom">
    <form action="{{ route('admin.studios.update', $studio) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-6 mb-4">
                <label class="form-label fw-bold">Nama Studio</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $studio->name) }}" required>
            </div>
            <div class="col-md-6 mb-4">
                <label class="form-label fw-bold">Tipe Studio</label>
                <select name="type_id" class="form-select" required>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}" {{ $studio->type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-4">
                <label class="form-label fw-bold">Kapasitas Kursi</label>
                <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $studio->capacity) }}" required>
            </div>
            <div class="col-md-6 mb-4">
                <label class="form-label fw-bold">Status</label>
                <select name="status" class="form-select">
                    <option value="active" {{ $studio->status == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ $studio->status == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
        </div>
        <div class="text-end mt-4">
            <button type="submit" class="btn-teal rounded-pill px-5">Perbarui Studio</button>
        </div>
    </form>
</div>
@endsection
