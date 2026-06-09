@extends('layouts.admin')

@section('title', 'Edit Studio')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-7">
        <h1 class="fw-bold text-primary mb-1">
            <i class="bi bi-pencil-square me-1"></i> Edit Studio: {{ $studio->name }}
        </h1>
        <p class="text-muted mb-0">Perbarui informasi & tata letak kursi studio.</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <a href="{{ route('admin.studios.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<form action="{{ route('admin.studios.update', $studio) }}" method="POST" data-studio-form>
    @csrf
    @method('PUT')
    @include('admin.studios._form', [
        'studio' => $studio,
        'submitLabel' => 'Perbarui Studio',
        'hasBookings' => $hasBookings ?? false,
    ])
</form>
@endsection
