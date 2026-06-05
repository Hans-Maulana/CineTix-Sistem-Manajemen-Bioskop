@php
    $schedule = $schedule ?? null;
@endphp

@push('styles')
<style>
    .scf-grid {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 22px;
    }
    @media (max-width: 1100px) {
        .scf-grid { grid-template-columns: 1fr; }
    }

    .scf-panel {
        background: #fff;
        border-radius: 20px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 10px 30px rgba(26, 25, 83, 0.04);
        padding: 24px;
    }
    .scf-panel-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: #1f2533;
        margin-bottom: 4px;
    }
    .scf-panel-subtitle {
        font-size: 0.82rem;
        color: #8a93a6;
        margin-bottom: 20px;
    }

    .scf-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #8a93a6;
        margin-bottom: 4px;
    }
    .scf-control {
        padding: 10px 14px;
        font-size: 0.92rem;
        border-radius: 12px;
        border: 1px solid #e6e8f0;
        height: 44px;
        width: 100%;
        background: #fff;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .scf-control:focus {
        border-color: #1A1953;
        box-shadow: 0 0 0 4px rgba(26, 25, 83, 0.08);
        outline: 0;
    }
    .scf-readonly {
        background: #fafbff !important;
        color: #1A1953;
        font-weight: 700;
    }

    /* Film preview card */
    .scf-film-preview {
        background: linear-gradient(180deg, #fafbff 0%, #f3f4fa 100%);
        border: 1px solid #e6e8f0;
        border-radius: 16px;
        padding: 18px;
        display: flex;
        gap: 14px;
        align-items: flex-start;
        min-height: 130px;
    }
    .scf-preview-poster {
        width: 78px; height: 110px;
        border-radius: 10px;
        object-fit: cover;
        flex-shrink: 0;
        background: #eef0f7;
        box-shadow: 0 6px 14px rgba(0,0,0,0.1);
    }
    .scf-preview-fallback {
        width: 78px; height: 110px;
        border-radius: 10px;
        flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg, #1A1953, #3a37a0);
        color: rgba(255,255,255,0.6);
        font-size: 1.5rem;
    }
    .scf-preview-info { flex: 1; min-width: 0; }
    .scf-preview-title {
        font-weight: 800;
        font-size: 1rem;
        color: #1f2533;
        margin-bottom: 4px;
        line-height: 1.3;
    }
    .scf-preview-meta {
        font-size: 0.8rem;
        color: #6c7689;
        display: flex;
        flex-wrap: wrap;
        gap: 4px 10px;
    }
    .scf-preview-meta i { color: #8a93a6; margin-right: 3px; }
    .scf-empty-preview {
        color: #8a93a6;
        font-size: 0.85rem;
        align-self: center;
        text-align: center;
        width: 100%;
    }

    /* Time preview */
    .scf-time-preview {
        background: linear-gradient(135deg, #1A1953, #3a37a0);
        color: #fff;
        border-radius: 16px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 8px;
    }
    .scf-time-preview .time-side { text-align: center; flex: 1; }
    .scf-time-preview .time-label {
        font-size: 0.62rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: rgba(255, 255, 255, 0.65);
    }
    .scf-time-preview .time-value {
        font-size: 1.45rem;
        font-weight: 800;
        line-height: 1;
        margin-top: 4px;
        font-feature-settings: 'tnum';
    }
    .scf-time-preview .time-arrow { color: rgba(255, 255, 255, 0.55); }

    .scf-status-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }
    .scf-status-option {
        position: relative;
        cursor: pointer;
    }
    .scf-status-option input { position: absolute; opacity: 0; pointer-events: none; }
    .scf-status-option .opt-box {
        border: 1px solid #e6e8f0;
        border-radius: 12px;
        padding: 12px;
        text-align: center;
        font-weight: 700;
        font-size: 0.82rem;
        color: #6c7689;
        transition: all 0.18s ease;
        background: #fff;
    }
    .scf-status-option .opt-box i { display: block; font-size: 1.3rem; margin-bottom: 5px; }
    .scf-status-option:hover .opt-box { border-color: #1A1953; color: #1A1953; }
    .scf-status-option input:checked + .opt-box {
        border-color: #1A1953;
        background: #1A1953;
        color: #fff;
        box-shadow: 0 6px 18px rgba(26, 25, 83, 0.25);
    }

    .scf-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 24px;
    }
    .btn-scf-primary {
        background: #1A1953;
        color: #fff;
        border: 0;
        border-radius: 12px;
        font-weight: 700;
        padding: 12px 28px;
        height: 46px;
        transition: all 0.2s ease;
        box-shadow: 0 8px 20px rgba(26, 25, 83, 0.25);
    }
    .btn-scf-primary:hover { background: #14123e; color: #fff; transform: translateY(-1px); }
    .btn-scf-secondary {
        background: #f3f4fa; color: #1A1953;
        border: 0; border-radius: 12px;
        font-weight: 700; padding: 12px 22px;
        height: 46px;
        text-decoration: none;
        display: inline-flex; align-items: center;
        transition: all 0.2s ease;
    }
    .btn-scf-secondary:hover { background: #e9ebf5; color: #1A1953; }

    .scf-help {
        background: rgba(13, 110, 253, 0.06);
        border-left: 3px solid #0d6efd;
        color: #084298;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 0.82rem;
        margin-top: 12px;
    }
</style>
@endpush

@if ($errors->any())
    <div class="alert alert-danger border-0 rounded-3 mb-3">
        <strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Ada kesalahan:</strong>
        <ul class="mb-0 mt-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger border-0 rounded-3 mb-3">
        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
    </div>
@endif

@php
    $filmsData = $films->map(function ($f) {
        return [
            'id'        => $f->id,
            'title'     => $f->title,
            'duration'  => (int) ($f->duration ?? 0),
            'cover'     => $f->cover_url ?? null,
            'director'  => $f->director ?? null,
        ];
    })->keyBy('id')->toArray();
    $studiosData = $studios->map(function ($s) {
        return [
            'id'       => $s->id,
            'name'     => $s->name,
            'capacity' => (int) ($s->capacity ?? 0),
            'type'     => $s->type->name ?? '-',
        ];
    })->keyBy('id')->toArray();

    $valFilmId    = old('film_id', $schedule->film_id ?? '');
    $valStudioId  = old('studio_id', $schedule->studio_id ?? '');
    $valDate      = old('schedule_date', $schedule?->schedule_date?->format('Y-m-d') ?? '');
    $valStart     = old('start_time', $schedule?->start_time?->format('H:i') ?? '');
    $valPrice     = old('ticket_price', isset($schedule) ? (int) $schedule->ticket_price : '');
    $valStatus    = old('status', $schedule->status ?? 'on schedule');
@endphp

<div class="scf-grid">
    <!-- KIRI: Detail Jadwal -->
    <div class="scf-panel">
        <div class="scf-panel-title">Detail Jadwal</div>
        <div class="scf-panel-subtitle">Tentukan film, studio, tanggal, dan jam tayang.</div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="scf-label">Pilih Film</label>
                <select name="film_id" id="filmSelect" class="scf-control" required>
                    <option value="">-- Pilih Film --</option>
                    @foreach($films as $film)
                        <option value="{{ $film->id }}" data-duration="{{ $film->duration }}" {{ $valFilmId == $film->id ? 'selected' : '' }}>
                            {{ $film->title }} ({{ $film->duration }} mnt)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="scf-label">Pilih Studio</label>
                <select name="studio_id" id="studioSelect" class="scf-control" required>
                    <option value="">-- Pilih Studio --</option>
                    @foreach($studios as $studio)
                        <option value="{{ $studio->id }}" {{ $valStudioId == $studio->id ? 'selected' : '' }}>
                            {{ $studio->name }} ({{ $studio->type->name ?? 'N/A' }} · {{ $studio->capacity }} kursi)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="scf-label">Tanggal Tayang</label>
                <input type="date" name="schedule_date" id="dateInput" class="scf-control" value="{{ $valDate }}" required>
            </div>

            <div class="col-md-4">
                <label class="scf-label">Jam Mulai</label>
                <input type="time" name="start_time" id="startInput" class="scf-control" value="{{ $valStart }}" required>
            </div>

            <div class="col-md-4">
                <label class="scf-label">Jam Selesai (otomatis)</label>
                <input type="text" id="endDisplay" class="scf-control scf-readonly" value="--:--" readonly>
            </div>

            <div class="col-12">
                <label class="scf-label">Harga Tiket (Rp)</label>
                <input type="number" name="ticket_price" class="scf-control" placeholder="Contoh: 50000" min="0" step="1000" value="{{ $valPrice }}" required>
            </div>

            <div class="col-12">
                <label class="scf-label">Status</label>
                <div class="scf-status-grid">
                    @foreach([
                        'on schedule' => ['label' => 'On Schedule', 'icon' => 'bi-calendar-check'],
                        'now playing' => ['label' => 'Now Playing', 'icon' => 'bi-play-circle-fill'],
                        'complete'    => ['label' => 'Selesai',     'icon' => 'bi-check2-all'],
                        'canceled'    => ['label' => 'Dibatalkan',  'icon' => 'bi-x-octagon'],
                    ] as $key => $opt)
                        <label class="scf-status-option">
                            <input type="radio" name="status" value="{{ $key }}" {{ $valStatus === $key ? 'checked' : '' }}>
                            <div class="opt-box">
                                <i class="bi {{ $opt['icon'] }}"></i>
                                {{ $opt['label'] }}
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="scf-help">
            <i class="bi bi-info-circle-fill me-1"></i>
            <strong>Catatan:</strong> Jam selesai dihitung otomatis berdasarkan durasi film yang dipilih.
            Sistem akan menolak jadwal yang bentrok di studio yang sama.
        </div>
    </div>

    <!-- KANAN: Preview -->
    <div class="scf-panel">
        <div class="scf-panel-title">Preview</div>
        <div class="scf-panel-subtitle">Tampilan jadwal saat dipublikasikan.</div>

        <div class="scf-film-preview" id="filmPreview">
            <div class="scf-empty-preview">
                <i class="bi bi-film d-block mb-1" style="font-size:1.5rem;"></i>
                Pilih film untuk melihat preview
            </div>
        </div>

        <div class="scf-time-preview">
            <div class="time-side">
                <div class="time-label">Mulai</div>
                <div class="time-value" id="previewStart">--:--</div>
            </div>
            <div class="time-arrow"><i class="bi bi-arrow-right"></i></div>
            <div class="time-side">
                <div class="time-label">Selesai</div>
                <div class="time-value" id="previewEnd">--:--</div>
            </div>
        </div>

        <div class="mt-3" id="studioPreview" style="display:none;">
            <div class="scf-label mb-1">Studio</div>
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-building text-primary"></i>
                <span class="fw-bold" id="studioPreviewName">-</span>
                <span class="badge bg-light text-dark border" id="studioPreviewType">-</span>
                <span class="text-muted small ms-auto"><i class="bi bi-grid-3x3-gap-fill me-1"></i><span id="studioPreviewCapacity">0</span> kursi</span>
            </div>
        </div>

        <div class="scf-actions">
            <a href="{{ route('admin.schedules.index') }}" class="btn-scf-secondary">
                <i class="bi bi-arrow-left me-1"></i> Batal
            </a>
            <button type="submit" class="btn-scf-primary">
                <i class="bi bi-check-lg me-1"></i> {{ $submitLabel ?? 'Simpan Jadwal' }}
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const FILMS = @json($filmsData);
    const STUDIOS = @json($studiosData);

    const filmSelect = document.getElementById('filmSelect');
    const studioSelect = document.getElementById('studioSelect');
    const startInput = document.getElementById('startInput');
    const endDisplay = document.getElementById('endDisplay');
    const previewStart = document.getElementById('previewStart');
    const previewEnd = document.getElementById('previewEnd');
    const filmPreview = document.getElementById('filmPreview');
    const studioPreview = document.getElementById('studioPreview');
    const studioPreviewName = document.getElementById('studioPreviewName');
    const studioPreviewType = document.getElementById('studioPreviewType');
    const studioPreviewCapacity = document.getElementById('studioPreviewCapacity');

    function pad(n) { return String(n).padStart(2, '0'); }

    function calcEndTime(start, durationMinutes) {
        if (!start || !durationMinutes) return '--:--';
        const [h, m] = start.split(':').map(Number);
        if (isNaN(h) || isNaN(m)) return '--:--';
        const totalMin = h * 60 + m + durationMinutes;
        const eh = Math.floor((totalMin / 60) % 24);
        const em = totalMin % 60;
        return pad(eh) + ':' + pad(em);
    }

    function updateFilmPreview() {
        const filmId = filmSelect.value;
        const film = FILMS[filmId];
        if (!film) {
            filmPreview.innerHTML = '<div class="scf-empty-preview"><i class="bi bi-film d-block mb-1" style="font-size:1.5rem;"></i>Pilih film untuk melihat preview</div>';
            return;
        }
        const posterHtml = film.cover
            ? '<img src="' + film.cover + '" alt="' + film.title + '" class="scf-preview-poster">'
            : '<div class="scf-preview-fallback"><i class="bi bi-film"></i></div>';
        filmPreview.innerHTML = `
            ${posterHtml}
            <div class="scf-preview-info">
                <div class="scf-preview-title">${film.title}</div>
                <div class="scf-preview-meta">
                    <span><i class="bi bi-clock"></i>${film.duration} menit</span>
                    ${film.director ? '<span><i class="bi bi-camera-reels"></i>' + film.director + '</span>' : ''}
                </div>
            </div>
        `;
    }

    function updateStudioPreview() {
        const studioId = studioSelect.value;
        const studio = STUDIOS[studioId];
        if (!studio) {
            studioPreview.style.display = 'none';
            return;
        }
        studioPreview.style.display = 'block';
        studioPreviewName.textContent = studio.name;
        studioPreviewType.textContent = studio.type;
        studioPreviewCapacity.textContent = studio.capacity;
    }

    function updateTime() {
        const filmId = filmSelect.value;
        const film = FILMS[filmId];
        const start = startInput.value;
        const duration = film ? film.duration : 0;
        const end = calcEndTime(start, duration);
        endDisplay.value = end;
        previewStart.textContent = start || '--:--';
        previewEnd.textContent = end;
    }

    filmSelect.addEventListener('change', () => { updateFilmPreview(); updateTime(); });
    studioSelect.addEventListener('change', updateStudioPreview);
    startInput.addEventListener('change', updateTime);
    startInput.addEventListener('input', updateTime);

    updateFilmPreview();
    updateStudioPreview();
    updateTime();
})();
</script>
@endpush
