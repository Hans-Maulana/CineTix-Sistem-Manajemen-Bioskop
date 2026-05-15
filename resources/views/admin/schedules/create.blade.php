@extends('layouts.admin')

@section('title', 'Tambah Jadwal')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h1 class="fw-bold text-primary">Tambah Jadwal</h1>
        <p class="text-muted">Tentukan jam tayang film di studio pilihan.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="{{ route('admin.schedules.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card-custom">
    <form action="{{ route('admin.schedules.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-4">
                <label class="form-label fw-bold">Pilih Film</label>
                <select name="film_id" class="form-select" required>
                    <option value="">Pilih Film...</option>
                    @foreach($films as $film)
                        <option value="{{ $film->id }}">{{ $film->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-4">
                <label class="form-label fw-bold">Pilih Studio</label>
                <select name="studio_id" class="form-select" required>
                    <option value="">Pilih Studio...</option>
                    @foreach($studios as $studio)
                        <option value="{{ $studio->id }}">{{ $studio->name }} (Kapasitas: {{ $studio->capacity }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-4">
                <label class="form-label fw-bold">Tanggal Tayang</label>
                <input type="date" name="schedule_date" class="form-control" required>
            </div>
            <div class="col-md-4 mb-4">
                <label class="form-label fw-bold">Jam Mulai</label>
                <input type="time" name="start_time" class="form-control" required>
            </div>
            <div class="col-md-4 mb-4">
                <label class="form-label fw-bold">Jam Selesai</label>
                <input type="time" name="end_time" class="form-control" required>
            </div>
            <div class="col-md-6 mb-4">
                <label class="form-label fw-bold">Harga Tiket (Rp)</label>
                <input type="number" name="ticket_price" class="form-control" placeholder="Contoh: 50000" required>
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
            <button type="submit" class="btn-teal rounded-pill px-5">Simpan Jadwal</button>
        </div>
    </form>
</div>
@endsection
