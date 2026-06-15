@extends('layouts.admin')

@section('title', 'Tambah Film Baru')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h1 class="fw-bold text-primary">Tambah Film</h1>
        <p class="text-muted">Masukkan detail film baru untuk ditampilkan di bioskop.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="{{ route('admin.films.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card-custom">
    <form action="{{ route('admin.films.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!-- Kolom Kiri -->
            <div class="col-lg-8">
                <div class="mb-4">
                    <label class="form-label fw-bold">Judul Film</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" placeholder="Contoh: Avengers: Endgame" value="{{ old('title') }}" required>
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Sinopsis</label>
                    <textarea name="synopsis" rows="5" class="form-control @error('synopsis') is-invalid @enderror" placeholder="Tulis jalan cerita film..." required>{{ old('synopsis') }}</textarea>
                    @error('synopsis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">Sutradara</label>
                        <input type="text" name="director" class="form-control" placeholder="Nama Sutradara" value="{{ old('director') }}" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">Tanggal Rilis</label>
                        <input type="date" name="release_date" class="form-control" value="{{ old('release_date') }}" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Link Trailer YouTube</label>
                    <input type="url" name="trailer_url" class="form-control" placeholder="Contoh: https://www.youtube.com/watch?v=..." value="{{ old('trailer_url') }}">
                    <div class="form-text small text-muted"><i class="bi bi-info-circle me-1"></i> Opsional. Masukkan link YouTube lengkap.</div>
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="col-lg-4">
                <div class="mb-4">
                    <label class="form-label fw-bold">Poster Film</label>
                    <div class="border rounded-4 p-3 text-center bg-light" style="border-style: dashed !important; border-width: 2px !important;">
                        <i class="bi bi-cloud-arrow-up display-4 text-muted"></i>
                        <input type="file" name="cover" class="form-control mt-2" accept="image/*">
                        <small class="text-muted d-block mt-2">Format: JPG, PNG, WEBP, AVIF (Max 2MB)</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">Durasi (Menit)</label>
                        <input type="number" name="duration" class="form-control" placeholder="120" value="{{ old('duration') }}" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="now_playing" {{ old('status') == 'now_playing' ? 'selected' : '' }}>Sedang Tayang</option>
                            <option value="coming_soon" {{ old('status') == 'coming_soon' ? 'selected' : '' }}>Segera Tayang</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Klasifikasi Umur</label>
                    <select name="classification" class="form-select" required>
                        <option value="SU" {{ old('classification') == 'SU' ? 'selected' : '' }}>SU (Semua Umur)</option>
                        <option value="13+" {{ old('classification') == '13+' ? 'selected' : '' }}>13+ (Remaja)</option>
                        <option value="17+" {{ old('classification') == '17+' ? 'selected' : '' }}>17+ (Dewasa)</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Genre</label>
                    <div class="genre-tags-container">
                        @foreach($genres as $genre)
                        <div class="genre-tag-item">
                            <input class="genre-checkbox-hidden" type="checkbox" name="genres[]" value="{{ $genre->id }}" id="genre{{ $genre->id }}">
                            <label class="genre-tag-label" for="genre{{ $genre->id }}">
                                {{ $genre->genre_name }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @error('genres') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div class="text-end">
            <button type="reset" class="btn btn-light rounded-pill px-5 me-2">Reset</button>
            <button type="submit" class="btn-teal rounded-pill px-5">Simpan Film</button>
        </div>
    </form>
</div>
@endsection
