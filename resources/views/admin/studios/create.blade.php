@extends('layouts.admin')

@section('title', 'Tambah Studio')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-7">
        <h1 class="fw-bold text-primary mb-1">
            <i class="bi bi-plus-circle-dotted me-1"></i> Tambah Studio
        </h1>
        <p class="text-muted mb-0">Buat studio baru dan rancang tata letak kursinya secara visual.</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <a href="{{ route('admin.studios.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<form action="{{ route('admin.studios.store') }}" method="POST" data-studio-form>
    @csrf
    @php
        $studio = (object) ['name' => '', 'type_id' => '', 'status' => 'active', 'seat_layout' => null];
    @endphp
    @include('admin.studios._form', [
        'studio' => $studio,
        'submitLabel' => 'Simpan Studio',
        'hasBookings' => false,
    ])
</form>
@endsection
