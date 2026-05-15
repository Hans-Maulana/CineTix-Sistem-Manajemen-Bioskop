@extends('layouts.admin')

@section('title', 'Edit Jadwal')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h1 class="fw-bold text-primary">Edit Jadwal Tayang</h1>
        <p class="text-muted">Perbarui waktu atau harga tiket untuk penayangan ini.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="{{ route('admin.schedules.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card-custom">
    <form action="{{ route('admin.schedules.update', $schedule) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-6 mb-4">
                <label class="form-label fw-bold">Pilih Film</label>
                <select name="film_id" class="form-select" required>
                    @foreach($films as $film)
                        <option value="{{ $film->id }}" {{ $schedule->film_id == $film->id ? 'selected' : '' }}>{{ $film->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-4">
                <label class="form-label fw-bold">Pilih Studio</label>
                <select name="studio_id" class="form-select" required>
                    @foreach($studios as $studio)
                        <option value="{{ $studio->id }}" {{ $schedule->studio_id == $studio->id ? 'selected' : '' }}>{{ $studio->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-4">
                <label class="form-label fw-bold">Tanggal Tayang</label>
                <input type="date" name="schedule_date" class="form-control" value="{{ $schedule->schedule_date->format('Y-m-d') }}" required>
            </div>
            <div class="col-md-4 mb-4">
                <label class="form-label fw-bold">Jam Mulai</label>
                <input type="time" name="start_time" class="form-control" value="{{ $schedule->start_time->format('H:i') }}" required>
            </div>
            <div class="col-md-4 mb-4">
                <label class="form-label fw-bold">Jam Selesai</label>
                <input type="time" name="end_time" class="form-control" value="{{ $schedule->end_time->format('H:i') }}" required>
            </div>
            <div class="col-md-6 mb-4">
                <label class="form-label fw-bold">Harga Tiket (Rp)</label>
                <input type="number" name="ticket_price" class="form-control" value="{{ (int)$schedule->ticket_price }}" required>
            </div>
            <div class="col-md-6 mb-4">
                <label class="form-label fw-bold">Status</label>
                <select name="status" class="form-select">
                    <option value="active" {{ $schedule->status == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ $schedule->status == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
        </div>
        <div class="text-end mt-4">
            <button type="submit" class="btn-teal rounded-pill px-5">Perbarui Jadwal</button>
        </div>
    </form>
</div>
@endsection
