@extends('layouts.admin')

@section('title', 'Tambah Jadwal')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-7">
        <h1 class="fw-bold text-primary mb-1">
            <i class="bi bi-calendar-plus me-1"></i> Tambah Jadwal
        </h1>
        <p class="text-muted mb-0">Tentukan film, studio, dan jam tayang. Jam selesai otomatis dihitung dari durasi film.</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <a href="{{ route('admin.schedules.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<form action="{{ route('admin.schedules.store') }}" method="POST">
    @csrf
    @include('admin.schedules._form', [
        'submitLabel' => 'Simpan Jadwal',
    ])
</form>
@endsection
