@extends('layouts.admin')

@section('title', 'Manajemen Film')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h1 class="fw-bold text-primary">Daftar Film</h1>
        <p class="text-muted">Kelola semua film yang tayang di bioskop kamu.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="{{ route('admin.films.create') }}" class="btn-teal text-decoration-none">
            <i class="bi bi-plus-lg"></i> Tambah Film
        </a>
    </div>
</div>

<div class="card-custom">
    <!-- Search Bar -->
    <div class="row mb-4">
        <div class="col-md-4 ms-auto">
            <form action="{{ route('admin.films.index') }}" method="GET">
                <div class="input-group shadow-sm rounded-3 overflow-hidden border">
                    <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-0 px-2" placeholder="Cari judul, sutradara, atau genre..." value="{{ request('search') }}">
                    @if(request('search'))
                        <a href="{{ route('admin.films.index') }}" class="btn btn-light border-0 d-flex align-items-center"><i class="bi bi-x-lg text-muted"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th class="py-3 px-4" style="border-radius: 12px 0 0 12px;">Poster</th>
                    <th class="py-3">Judul Film</th>
                    <th class="py-3">Genre</th>
                    <th class="py-3">Rating</th>
                    <th class="py-3">Durasi</th>
                    <th class="py-3 text-center" style="border-radius: 0 12px 12px 0;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($films as $film)
                <tr>
                    <td class="px-4 py-3">
                        <img src="{{ $film->cover_url }}" alt="Cover" class="rounded-3 shadow-sm" style="width: 120px; height: 80px; object-fit: cover;">
                    </td>
                    <td class="fw-bold text-dark">{{ $film->title }}</td>
                    <td>
                        @foreach($film->genres as $genre)
                            <span class="badge bg-light text-primary border me-1">{{ $genre->genre_name }}</span>
                        @endforeach
                    </td>
                    <td><span class="text-warning fw-bold">⭐ {{ number_format($film->reviews()->avg('rating') ?: 0, 1) }}</span></td>
                    <td><i class="bi bi-clock me-1"></i> {{ $film->duration }} mnt</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="{{ route('admin.films.edit', $film) }}" class="btn btn-sm btn-outline-warning rounded-3 me-2">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('admin.films.destroy', $film) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus film ini?')">
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
                    <td colspan="6" class="text-center py-5">
                        <img src="{{ asset('assets/images/logos/empty.svg') }}" alt="Empty" class="mb-3" style="width: 150px; opacity: 0.5;">
                        <p class="text-muted">Belum ada film yang ditambahkan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $films->links() }}
    </div>
</div>
@endsection
