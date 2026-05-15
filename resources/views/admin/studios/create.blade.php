@extends('layouts.admin')

@section('title', 'Tambah Studio')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h1 class="fw-bold text-primary">Tambah Studio</h1>
        <p class="text-muted">Daftarkan studio baru untuk bioskop kamu.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="{{ route('admin.studios.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card-custom">
    <form action="{{ route('admin.studios.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-4">
                <label class="form-label fw-bold">Nama Studio</label>
                <input type="text" name="name" class="form-control" placeholder="Contoh: Studio 1 atau Premiere" required>
            </div>
            <div class="col-md-6 mb-4">
                <label class="form-label fw-bold">Tipe Studio</label>
                <select name="type_id" class="form-select" required>
                    <option value="">Pilih Tipe...</option>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-4">
                <label class="form-label fw-bold">Kapasitas Kursi</label>
                <input type="number" name="capacity" class="form-control" placeholder="Contoh: 50" required>
            </div>
            <div class="col-md-6 mb-4">
                <label class="form-label fw-bold">Status</label>
                <select name="status" class="form-select">
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
            </div>
        </div>
        <div class="text-end mt-4">
            <button type="submit" class="btn-teal rounded-pill px-5">Simpan Studio</button>
        </div>
    </form>
</div>
@endsection
