@php
    /** @var array $defaultLayout - layout default 5x8 (semua seat) untuk create */
    $defaultLayout = $defaultLayout ?? array_fill(0, 5, array_fill(0, 8, 1));
    $initialLayout = old('seat_layout_data') ?? json_encode($studio->seat_layout ?? $defaultLayout);
    $hasBookings = $hasBookings ?? false;
@endphp

@push('styles')
<style>
    .st-form-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 22px;
    }
    @media (max-width: 1100px) {
        .st-form-grid { grid-template-columns: 1fr; }
    }

    .st-panel {
        background: #fff;
        border-radius: 20px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 10px 30px rgba(26, 25, 83, 0.04);
        padding: 24px;
    }
    .st-panel-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: #1f2533;
        margin-bottom: 4px;
    }
    .st-panel-subtitle {
        font-size: 0.82rem;
        color: #8a93a6;
        margin-bottom: 20px;
    }

    .st-form-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #8a93a6;
        margin-bottom: 4px;
    }
    .st-form-control {
        padding: 10px 14px;
        font-size: 0.92rem;
        border-radius: 12px;
        border: 1px solid #e6e8f0;
        height: 44px;
        width: 100%;
        background: #fff;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .st-form-control:focus {
        border-color: #1A1953;
        box-shadow: 0 0 0 4px rgba(26, 25, 83, 0.08);
        outline: 0;
    }

    /* === Seat Builder === */
    .seat-builder-wrap {
        background: linear-gradient(180deg, #fafbff 0%, #f3f4fa 100%);
        border-radius: 18px;
        padding: 22px;
        border: 1px solid #e6e8f0;
    }
    .seat-screen {
        text-align: center;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.18em;
        color: #8a93a6;
        margin-bottom: 14px;
    }
    .seat-screen .screen-bar {
        height: 8px;
        max-width: 75%;
        margin: 0 auto 8px;
        background: linear-gradient(90deg, transparent, #d4b06a, transparent);
        border-radius: 4px;
        box-shadow: 0 8px 20px rgba(212, 176, 106, 0.4);
    }

    .seat-grid {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        overflow-x: auto;
        padding: 6px 4px 14px;
    }
    .seat-row {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .seat-row-label {
        width: 28px;
        text-align: center;
        font-size: 0.78rem;
        font-weight: 800;
        color: #1A1953;
    }
    .seat-cells {
        display: flex;
        gap: 6px;
    }

    .seat-cell {
        width: 32px; height: 32px;
        border-radius: 8px 8px 4px 4px;
        cursor: pointer;
        position: relative;
        transition: transform 0.12s ease, background 0.12s ease, border 0.12s ease;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.62rem;
        font-weight: 700;
        user-select: none;
    }
    .seat-cell.is-seat {
        background: #1A1953;
        border: 1px solid #14123e;
        color: #fff;
        box-shadow: inset 0 -3px 0 rgba(0,0,0,0.2);
    }
    .seat-cell.is-aisle {
        background: transparent;
        border: 1px dashed #c7cbdc;
        color: #c7cbdc;
    }
    .seat-cell:hover { transform: translateY(-2px); }
    .seat-cell.is-seat:hover { background: #2d2b7a; }
    .seat-cell.is-aisle:hover { background: rgba(26, 25, 83, 0.05); border-color: #1A1953; }

    .seat-row-actions {
        display: flex;
        gap: 4px;
        margin-left: 6px;
    }
    .seat-row-btn {
        width: 24px; height: 24px;
        border-radius: 6px;
        border: 1px solid #e6e8f0;
        background: #fff;
        color: #8a93a6;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.72rem;
        transition: all 0.15s ease;
    }
    .seat-row-btn:hover { background: #1A1953; color: #fff; border-color: #1A1953; }
    .seat-row-btn.danger:hover { background: #dc3545; border-color: #dc3545; }

    .seat-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 14px;
        padding-bottom: 14px;
        border-bottom: 1px dashed #d8dce6;
    }
    .seat-tool-btn {
        height: 36px;
        padding: 0 14px;
        border-radius: 10px;
        border: 1px solid #e6e8f0;
        background: #fff;
        color: #1A1953;
        font-weight: 700;
        font-size: 0.82rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.18s ease;
    }
    .seat-tool-btn:hover { background: #1A1953; color: #fff; border-color: #1A1953; }
    .seat-tool-btn.danger { color: #dc3545; }
    .seat-tool-btn.danger:hover { background: #dc3545; border-color: #dc3545; color: #fff; }

    .seat-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        font-size: 0.78rem;
        color: #6c7689;
        margin-top: 12px;
        padding-top: 14px;
        border-top: 1px dashed #d8dce6;
        align-items: center;
    }
    .seat-legend-item { display: inline-flex; align-items: center; gap: 6px; }
    .legend-cell {
        width: 16px; height: 16px;
        border-radius: 4px;
    }
    .legend-cell.seat  { background: #1A1953; }
    .legend-cell.aisle { background: transparent; border: 1px dashed #c7cbdc; }

    .seat-counter {
        background: rgba(26, 25, 83, 0.08);
        color: #1A1953;
        padding: 6px 12px;
        border-radius: 10px;
        font-weight: 800;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-left: auto;
    }

    .seat-warning {
        background: rgba(255, 193, 7, 0.12);
        color: #856404;
        border: 1px solid rgba(255, 193, 7, 0.3);
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 0.85rem;
        margin-bottom: 16px;
    }

    .btn-st-primary {
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
    .btn-st-primary:hover {
        background: #14123e;
        color: #fff;
        transform: translateY(-1px);
    }
    .btn-st-secondary {
        background: #f3f4fa;
        color: #1A1953;
        border: 0;
        border-radius: 12px;
        font-weight: 700;
        padding: 12px 22px;
        height: 46px;
        transition: all 0.2s ease;
    }
    .btn-st-secondary:hover { background: #e9ebf5; color: #1A1953; }
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

@if($hasBookings)
    <div class="seat-warning">
        <i class="bi bi-info-circle-fill me-1"></i>
        Studio ini sudah memiliki transaksi tiket. Detail studio bisa diperbarui, namun <strong>kursi tidak akan diregenerasi</strong> meskipun layout diubah, untuk menjaga riwayat booking.
    </div>
@endif

<div class="st-form-grid">
    <!-- KIRI: Seat Layout Builder -->
    <div class="st-panel">
        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
            <div>
                <div class="st-panel-title">Tata Letak Kursi</div>
                <div class="st-panel-subtitle">Klik kotak untuk toggle <strong>kursi</strong> ↔ <strong>lorong</strong>. Tambah/hapus baris & kolom sesuai kebutuhan.</div>
            </div>
            <div class="seat-counter">
                <i class="bi bi-grid-3x3-gap-fill"></i>
                <span id="seatCount">0</span> kursi
            </div>
        </div>

        <div class="seat-builder-wrap">
            <div class="seat-toolbar">
                <button type="button" class="seat-tool-btn" data-action="add-row">
                    <i class="bi bi-plus-square"></i> Tambah Baris
                </button>
                <button type="button" class="seat-tool-btn danger" data-action="remove-row">
                    <i class="bi bi-dash-square"></i> Hapus Baris
                </button>
                <button type="button" class="seat-tool-btn" data-action="add-col">
                    <i class="bi bi-plus-square-dotted"></i> Tambah Kolom
                </button>
                <button type="button" class="seat-tool-btn danger" data-action="remove-col">
                    <i class="bi bi-dash-square-dotted"></i> Hapus Kolom
                </button>
                <button type="button" class="seat-tool-btn" data-action="fill">
                    <i class="bi bi-check2-all"></i> Isi Semua Kursi
                </button>
            </div>

            <div class="seat-screen">
                <div class="screen-bar"></div>
                Layar / Screen
            </div>

            <div class="seat-grid" id="seatGrid"></div>

            <div class="seat-legend">
                <div class="seat-legend-item">
                    <span class="legend-cell seat"></span> Kursi
                </div>
                <div class="seat-legend-item">
                    <span class="legend-cell aisle"></span> Lorong / kosong
                </div>
                <div class="seat-legend-item ms-auto">
                    <span><strong id="rowCount">0</strong> baris × <strong id="colCount">0</strong> kolom</span>
                </div>
            </div>
        </div>
    </div>

    <!-- KANAN: Detail Studio -->
    <div class="st-panel">
        <div class="st-panel-title">Detail Studio</div>
        <div class="st-panel-subtitle">Informasi dasar studio.</div>

        <div class="mb-3">
            <label class="st-form-label">Nama Studio</label>
            <input type="text" name="name" class="st-form-control"
                value="{{ old('name', $studio->name ?? '') }}"
                placeholder="Contoh: Studio A" required>
        </div>

        <div class="mb-3">
            <label class="st-form-label">Tipe Studio</label>
            <select name="type_id" class="st-form-control" required>
                <option value="">Pilih Tipe...</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" {{ old('type_id', $studio->type_id ?? '') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="st-form-label">Status</label>
            <select name="status" class="st-form-control" required>
                <option value="active"   {{ old('status', $studio->status ?? 'active') == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ old('status', $studio->status ?? '') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="st-form-label">Kapasitas (otomatis)</label>
            <input type="text" id="capacityDisplay" class="st-form-control" value="0 kursi" readonly style="background:#fafbff; color:#1A1953; font-weight:700;">
        </div>

        <input type="hidden" name="seat_layout" id="seatLayoutInput" value='{{ $initialLayout }}'>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('admin.studios.index') }}" class="btn-st-secondary text-decoration-none d-inline-flex align-items-center">
                <i class="bi bi-arrow-left me-1"></i> Batal
            </a>
            <button type="submit" class="btn-st-primary">
                <i class="bi bi-check-lg me-1"></i> {{ $submitLabel ?? 'Simpan Studio' }}
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const MIN_ROWS = 1;
    const MAX_ROWS = 12;
    const MIN_COLS = 1;
    const MAX_COLS = 20;

    const initialJson = document.getElementById('seatLayoutInput').value || '[]';
    let layout;
    try {
        layout = JSON.parse(initialJson);
        if (!Array.isArray(layout) || layout.length === 0) {
            layout = Array.from({ length: 5 }, () => Array(8).fill(1));
        }
    } catch (e) {
        layout = Array.from({ length: 5 }, () => Array(8).fill(1));
    }

    const gridEl = document.getElementById('seatGrid');
    const inputEl = document.getElementById('seatLayoutInput');
    const seatCountEl = document.getElementById('seatCount');
    const rowCountEl = document.getElementById('rowCount');
    const colCountEl = document.getElementById('colCount');
    const capacityDisplay = document.getElementById('capacityDisplay');

    const rowLabel = (idx) => String.fromCharCode(65 + idx);

    function maxCols() {
        return layout.reduce((m, r) => Math.max(m, r.length), 0);
    }

    function normalizeLayout() {
        const max = Math.max(maxCols(), 1);
        layout = layout.map(r => {
            const copy = r.slice(0, max);
            while (copy.length < max) copy.push(1);
            return copy.map(v => (v === 1 ? 1 : 0));
        });
    }

    function render() {
        normalizeLayout();
        gridEl.innerHTML = '';
        let totalSeats = 0;

        layout.forEach((row, rIdx) => {
            const rowEl = document.createElement('div');
            rowEl.className = 'seat-row';

            const labelEl = document.createElement('div');
            labelEl.className = 'seat-row-label';
            labelEl.textContent = rowLabel(rIdx);
            rowEl.appendChild(labelEl);

            const cellsEl = document.createElement('div');
            cellsEl.className = 'seat-cells';

            let seatCounter = 0;
            row.forEach((cell, cIdx) => {
                const cellEl = document.createElement('div');
                cellEl.className = 'seat-cell ' + (cell === 1 ? 'is-seat' : 'is-aisle');
                cellEl.dataset.row = rIdx;
                cellEl.dataset.col = cIdx;
                if (cell === 1) {
                    seatCounter++;
                    totalSeats++;
                    cellEl.textContent = seatCounter;
                    cellEl.title = rowLabel(rIdx) + seatCounter;
                } else {
                    cellEl.textContent = '';
                    cellEl.title = 'Lorong (klik untuk jadi kursi)';
                }
                cellEl.addEventListener('click', () => toggleCell(rIdx, cIdx));
                cellsEl.appendChild(cellEl);
            });

            rowEl.appendChild(cellsEl);
            gridEl.appendChild(rowEl);
        });

        seatCountEl.textContent = totalSeats;
        rowCountEl.textContent = layout.length;
        colCountEl.textContent = maxCols();
        capacityDisplay.value = totalSeats + ' kursi';
        inputEl.value = JSON.stringify(layout);
    }

    function toggleCell(r, c) {
        layout[r][c] = layout[r][c] === 1 ? 0 : 1;
        render();
    }

    function addRow() {
        if (layout.length >= MAX_ROWS) return;
        const cols = Math.max(maxCols(), 8);
        layout.push(Array(cols).fill(1));
        render();
    }

    function removeRow() {
        if (layout.length <= MIN_ROWS) return;
        layout.pop();
        render();
    }

    function addCol() {
        const cols = maxCols();
        if (cols >= MAX_COLS) return;
        layout = layout.map(r => { r.push(1); return r; });
        render();
    }

    function removeCol() {
        const cols = maxCols();
        if (cols <= MIN_COLS) return;
        layout = layout.map(r => { r.pop(); return r; });
        render();
    }

    function fillAll() {
        layout = layout.map(r => r.map(() => 1));
        render();
    }

    document.querySelectorAll('[data-action]').forEach(btn => {
        btn.addEventListener('click', () => {
            switch (btn.dataset.action) {
                case 'add-row':    addRow();    break;
                case 'remove-row': removeRow(); break;
                case 'add-col':    addCol();    break;
                case 'remove-col': removeCol(); break;
                case 'fill':       fillAll();   break;
            }
        });
    });

    // Submit guard
    const form = document.querySelector('form[data-studio-form]');
    if (form) {
        form.addEventListener('submit', function (e) {
            const total = layout.flat().reduce((a, b) => a + (b === 1 ? 1 : 0), 0);
            if (total < 1) {
                e.preventDefault();
                alert('Layout harus memiliki minimal 1 kursi.');
            } else {
                inputEl.value = JSON.stringify(layout);
            }
        });
    }

    render();
})();
</script>
@endpush
