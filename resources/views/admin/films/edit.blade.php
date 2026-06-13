@extends('layouts.admin')

@section('title', 'Edit Film')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h1 class="fw-bold text-primary">Edit Film: {{ $film->title }}</h1>
        <p class="text-muted">Perbarui informasi film yang sudah terdaftar.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="{{ route('admin.films.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card-custom">
    <form action="{{ route('admin.films.update', $film) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- Kolom Kiri -->
            <div class="col-lg-8">
                <div class="mb-4">
                    <label class="form-label fw-bold">Judul Film</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $film->title) }}" required>
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Sinopsis</label>
                    <textarea name="synopsis" rows="5" class="form-control @error('synopsis') is-invalid @enderror" required>{{ old('synopsis', $film->synopsis) }}</textarea>
                    @error('synopsis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">Sutradara</label>
                        <input type="text" name="director" class="form-control" value="{{ old('director', $film->director) }}" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">Tanggal Rilis</label>
                        <input type="date" name="release_date" class="form-control" value="{{ old('release_date', $film->release_date) }}" required>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="col-lg-4">
                <div class="mb-4">
                    <label class="form-label fw-bold">Poster Saat Ini</label>
                    <div class="mb-3 text-center">
                        <img src="{{ $film->cover_url }}" class="rounded-4 shadow-sm" style="width: 150px; height: 225px; object-fit: fill;">
                    </div>
                    <label class="form-label fw-bold">Ganti Poster (Opsional)</label>
                    <input type="file" name="cover" class="form-control" accept="image/*">
                    <small class="text-muted d-block mt-2">Format: JPG, PNG, WEBP, AVIF (Max 2MB)</small>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">Durasi (Menit)</label>
                        <input type="number" name="duration" class="form-control" value="{{ old('duration', $film->duration) }}" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="now_playing" {{ old('status', $film->status) == 'now_playing' ? 'selected' : '' }}>Sedang Tayang</option>
                            <option value="coming_soon" {{ old('status', $film->status) == 'coming_soon' ? 'selected' : '' }}>Segera Tayang</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Klasifikasi Umur</label>
                    <select name="classification" class="form-select" required>
                        <option value="SU" {{ old('classification', $film->classification) == 'SU' ? 'selected' : '' }}>SU (Semua Umur)</option>
                        <option value="13+" {{ old('classification', $film->classification) == '13+' ? 'selected' : '' }}>13+ (Remaja)</option>
                        <option value="17+" {{ old('classification', $film->classification) == '17+' ? 'selected' : '' }}>17+ (Dewasa)</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Genre</label>
                    <div class="genre-tags-container">
                        @foreach($genres as $genre)
                        <div class="genre-tag-item">
                            <input class="genre-checkbox-hidden" type="checkbox" name="genres[]" value="{{ $genre->id }}" 
                                id="genre{{ $genre->id }}" {{ in_array($genre->id, $selectedGenres) ? 'checked' : '' }}>
                            <label class="genre-tag-label" for="genre{{ $genre->id }}">
                                {{ $genre->genre_name }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div class="text-end">
            <button type="submit" class="btn-teal rounded-pill px-5">Perbarui Film</button>
        </div>
    </form>
</div>
@endsection
