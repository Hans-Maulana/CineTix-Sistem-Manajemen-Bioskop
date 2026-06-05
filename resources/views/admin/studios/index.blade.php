@extends('layouts.admin')

@section('title', 'Manajemen Studio')

@push('styles')
<style>
    .st-hero {
        background: linear-gradient(120deg, #1A1953 0%, #2d2b7a 60%, #3a37a0 100%);
        border-radius: 24px;
        color: #fff;
        padding: 28px 32px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(26, 25, 83, 0.25);
    }
    .st-hero::after {
        content: ""; position: absolute;
        right: -60px; top: -60px;
        width: 220px; height: 220px;
        background: rgba(212, 176, 106, 0.18);
        border-radius: 50%;
    }
    .st-hero::before {
        content: ""; position: absolute;
        right: 90px; bottom: -80px;
        width: 180px; height: 180px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }
    .st-hero h1 { font-size: 1.75rem; font-weight: 800; margin-bottom: 4px; }

    .st-stat {
        background: #fff;
        border-radius: 18px;
        padding: 18px 20px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 8px 22px rgba(26, 25, 83, 0.04);
        display: flex; align-items: center; gap: 14px;
        height: 100%;
    }
    .st-stat-icon {
        width: 46px; height: 46px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .st-stat-label {
        font-size: 0.7rem; letter-spacing: 0.06em;
        font-weight: 700; text-transform: uppercase; color: #8a93a6;
    }
    .st-stat-value {
        font-size: 1.45rem; font-weight: 800; color: #1f2533; line-height: 1.1; margin-top: 2px;
    }

    .st-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 22px;
    }
    .st-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 10px 28px rgba(26, 25, 83, 0.04);
        padding: 22px;
        display: flex;
        flex-direction: column;
        gap: 14px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .st-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 40px rgba(26, 25, 83, 0.10);
    }
    .st-card-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
    }
    .st-card-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #1f2533;
        margin: 0;
        line-height: 1.2;
    }
    .st-card-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 6px;
    }
    .st-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 3px 9px;
        border-radius: 999px;
    }
    .st-tag-type    { background: rgba(26, 25, 83, 0.08);   color: #1A1953; }
    .st-tag-active  { background: rgba(25, 167, 95, 0.12);  color: #15a05c; }
    .st-tag-inactive{ background: rgba(220, 53, 69, 0.12);  color: #dc3545; }

    .st-screen {
        text-align: center;
        font-size: 0.66rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.18em;
        color: #8a93a6;
        position: relative;
    }
    .st-screen::before {
        content: "";
        display: block;
        height: 3px;
        background: linear-gradient(90deg, transparent, rgba(212, 176, 106, 0.65), transparent);
        margin-bottom: 6px;
        border-radius: 2px;
    }

    .st-mini {
        display: flex;
        flex-direction: column;
        gap: 3px;
        align-items: center;
        justify-content: center;
        padding: 10px;
        background: #fafbff;
        border-radius: 12px;
    }
    .st-mini-row { display: flex; gap: 3px; }
    .st-mini-cell {
        width: 9px; height: 9px;
        border-radius: 2px;
    }
    .st-mini-seat  { background: #1A1953; }
    .st-mini-aisle { background: transparent; }

    .st-card-stats {
        display: flex;
        justify-content: space-around;
        text-align: center;
        padding: 10px 0;
        border-top: 1px dashed #e6e8f0;
        border-bottom: 1px dashed #e6e8f0;
    }
    .st-card-stats .item-label { font-size: 0.66rem; color: #8a93a6; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; }
    .st-card-stats .item-value { font-size: 1.05rem; font-weight: 800; color: #1f2533; }

    .st-card-actions { display: flex; gap: 8px; }
    .st-btn {
        flex: 1;
        height: 36px;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        border: 1px solid #e6e8f0;
        background: #fff;
        transition: all 0.2s ease;
        text-decoration: none;
        color: #1A1953;
    }
    .st-btn:hover { background: #1A1953; color: #fff; border-color: #1A1953; }
    .st-btn.delete { color: #dc3545; flex: 0 0 44px; }
    .st-btn.delete:hover { background: #dc3545; color: #fff; border-color: #dc3545; }

    .st-empty {
        background: #fff;
        border-radius: 20px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 10px 30px rgba(26, 25, 83, 0.03);
        padding: 60px 20px;
        text-align: center;
    }
    .st-empty i { font-size: 3rem; color: #c7cbdc; }
</style>
@endpush

@section('content')

<div class="st-hero mb-4">
    <div class="row align-items-center position-relative" style="z-index:2;">
        <div class="col-md-7">
            <span class="badge bg-light text-dark rounded-pill mb-2 px-3 py-2">
                <i class="bi bi-building me-1"></i> Manajemen Studio
            </span>
            <h1 class="fw-bold mb-1">Daftar Studio</h1>
            <p class="mb-0 text-white-50">Kelola studio bioskop dan rancang tata letak kursinya secara visual.</p>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.studios.create') }}" class="btn btn-light fw-bold rounded-pill px-4">
                <i class="bi bi-plus-lg"></i> Tambah Studio
            </a>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="st-stat">
            <div class="st-stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-building"></i></div>
            <div>
                <div class="st-stat-label">Total Studio</div>
                <div class="st-stat-value">{{ number_format($stats['total'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="st-stat">
            <div class="st-stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-check2-circle"></i></div>
            <div>
                <div class="st-stat-label">Studio Aktif</div>
                <div class="st-stat-value">{{ number_format($stats['active'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="st-stat">
            <div class="st-stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-grid-3x3-gap-fill"></i></div>
            <div>
                <div class="st-stat-label">Total Kapasitas</div>
                <div class="st-stat-value">{{ number_format($stats['capacity'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="st-stat">
            <div class="st-stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-tags"></i></div>
            <div>
                <div class="st-stat-label">Tipe Studio</div>
                <div class="st-stat-value">{{ number_format($stats['types'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success border-0 rounded-3 d-flex align-items-center mb-3" role="alert" style="background: rgba(25, 167, 95, 0.1); color: #15a05c;">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    </div>
@endif

@if($studios->count() > 0)
    <div class="st-grid mb-4">
        @foreach($studios as $studio)
            <div class="st-card">
                <div class="st-card-head">
                    <div>
                        <h5 class="st-card-title">{{ $studio->name }}</h5>
                        <div class="st-card-meta">
                            <span class="st-tag st-tag-type">
                                <i class="bi bi-tag-fill"></i> {{ $studio->type->name ?? 'N/A' }}
                            </span>
                            @if($studio->status === 'active')
                                <span class="st-tag st-tag-active"><i class="bi bi-circle-fill" style="font-size:0.5rem;"></i> Aktif</span>
                            @else
                                <span class="st-tag st-tag-inactive"><i class="bi bi-circle-fill" style="font-size:0.5rem;"></i> Nonaktif</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="st-screen">
                    <div>Layar</div>
                </div>

                <div class="st-mini">
                    @php $layout = $studio->seat_layout ?? []; @endphp
                    @if(!empty($layout))
                        @foreach($layout as $row)
                            <div class="st-mini-row">
                                @foreach($row as $cell)
                                    <div class="st-mini-cell {{ ((int) $cell === 1) ? 'st-mini-seat' : 'st-mini-aisle' }}"></div>
                                @endforeach
                            </div>
                        @endforeach
                    @else
                        <div class="text-muted small">Belum ada layout kursi</div>
                    @endif
                </div>

                <div class="st-card-stats">
                    <div>
                        <div class="item-label">Kapasitas</div>
                        <div class="item-value">{{ $studio->capacity }}</div>
                    </div>
                    <div>
                        <div class="item-label">Kursi (DB)</div>
                        <div class="item-value">{{ $studio->seats_count ?? 0 }}</div>
                    </div>
                    <div>
                        <div class="item-label">Baris</div>
                        <div class="item-value">{{ count($layout) }}</div>
                    </div>
                </div>

                <div class="st-card-actions">
                    <a href="{{ route('admin.studios.edit', $studio) }}" class="st-btn">
                        <i class="bi bi-pencil-square"></i> Edit Layout
                    </a>
                    <form action="{{ route('admin.studios.destroy', $studio) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus studio &quot;{{ addslashes($studio->name) }}&quot;? Semua kursi & jadwal terkait akan ikut terhapus.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="st-btn delete" title="Hapus studio">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="st-empty">
        <i class="bi bi-building d-block mb-2"></i>
        <h5 class="fw-bold text-dark mb-1">Belum ada studio</h5>
        <p class="text-muted mb-3">Tambahkan studio pertama dan rancang tata letak kursinya.</p>
        <a href="{{ route('admin.studios.create') }}" class="btn btn-primary fw-bold rounded-pill px-4" style="background:#1A1953; border-color:#1A1953;">
            <i class="bi bi-plus-lg me-1"></i> Tambah Studio
        </a>
    </div>
@endif

@endsection
