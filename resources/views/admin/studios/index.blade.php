@extends('layouts.admin')

@section('title', 'Manajemen Studio')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h1 class="fw-bold text-primary">Daftar Studio</h1>
        <p class="text-muted">Kelola kapasitas dan tipe studio bioskop kamu.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="{{ route('admin.studios.create') }}" class="btn-teal text-decoration-none">
            <i class="bi bi-plus-lg"></i> Tambah Studio
        </a>
    </div>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th class="py-3 px-4">Nama Studio</th>
                    <th class="py-3">Tipe</th>
                    <th class="py-3">Kapasitas</th>
                    <th class="py-3">Status</th>
                    <th class="py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($studios as $studio)
                <tr>
                    <td class="px-4 py-3 fw-bold text-dark">{{ $studio->name }}</td>
                    <td><span class="badge bg-light text-primary border px-3">{{ $studio->type->name ?? 'N/A' }}</span></td>
                    <td>{{ $studio->capacity }} Kursi</td>
                    <td>
                        @if($studio->status == 'active')
                            <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i> Aktif</span>
                        @else
                            <span class="text-danger"><i class="bi bi-x-circle-fill me-1"></i> Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="{{ route('admin.studios.edit', $studio) }}" class="btn btn-sm btn-outline-warning rounded-3 me-2">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('admin.studios.destroy', $studio) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus studio ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-3">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">Belum ada studio.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
