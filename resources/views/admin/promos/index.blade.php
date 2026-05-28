@extends('layouts.admin')

@section('title', 'Daftar Promo')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="text-primary fw-bold display-6 mb-1">Daftar Promo</h1>
            <p class="text-muted mb-0">Kelola semua kode promo yang tersedia untuk pelanggan kamu.</p>
        </div>
        <a href="{{ route('admin.promos.create') }}" class="btn text-white px-4 py-2 fw-semibold rounded-3 shadow-sm" style="background-color: #1a1843;">
            <i class="bi bi-plus-lg me-2"></i>Tambah Promo
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm my-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="fw-bold" style="border-bottom: 2px solid #f0f0f0;">
                            <th class="pb-3 border-0">Kode Promo</th>
                            <th class="pb-3 border-0">Deskripsi</th>
                            <th class="pb-3 border-0">Tipe & Nilai</th>
                            <th class="pb-3 border-0">Tanggal Valid</th>
                            <th class="pb-3 border-0">Penggunaan</th>
                            <th class="pb-3 border-0 text-center">Limit / Cust</th>
                            <th class="pb-3 border-0 text-center">Status</th>
                            <th class="pb-3 border-0 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($promos as $promo)
                            <tr style="border-bottom: 1px solid #f8f9fa;">
                                <td class="py-3">
                                    <span class="fw-bold text-dark">{{ $promo->code }}</span>
                                </td>
                                <td class="text-secondary">{{ $promo->description ?? '-' }}</td>
                                <td>
                                    <span class="fw-bold text-dark d-block">
                                        {{ $promo->discount_type === 'percentage' ? $promo->discount_value . '%' : 'Rp ' . number_format($promo->discount_value, 0, ',', '.') }}
                                    </span>
                                    <small class="text-muted">
                                        {{ $promo->discount_type === 'percentage' ? 'Persentase' : 'Nominal Tetap' }}
                                    </small>
                                </td>
                                <td class="text-secondary small">
                                    {{ $promo->valid_from->format('d M Y') }} - {{ $promo->valid_until->format('d M Y') }}
                                </td>
                                <td style="min-width: 150px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar bg-secondary opacity-50"
                                                role="progressbar"
                                                style="width: {{ $promo->max_usage ? ($promo->usage_count / $promo->max_usage * 100) : 0 }}%">
                                            </div>
                                        </div>
                                        <small class="text-muted fw-medium">{{ $promo->usage_count }}/{{ $promo->max_usage ?? '∞' }}</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-2 py-1 rounded-2">{{ $promo->max_usage_per_customer }}x</span>
                                </td>
                                <td class="text-center">
                                    @if($promo->isValid())
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-3">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 rounded-3">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('admin.promos.show', $promo) }}" class="btn btn-sm btn-outline-info rounded-3" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.promos.edit', $promo) }}" class="btn btn-sm btn-outline-warning rounded-3" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('admin.promos.destroy', $promo) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" onclick="return confirm('Yakin ingin menghapus promo ini?')" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    Belum ada kode promo yang dibuat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($promos->hasPages())
                <div class="d-flex justify-content-end mt-4">
                    {{ $promos->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
