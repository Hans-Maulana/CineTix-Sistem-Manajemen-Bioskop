@extends('layouts.admin')

@section('title', 'Kelola Promo')

@section('content')
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-1">📋 Kelola Kode Promo</h1>
            <p class="text-muted">Buat, edit, dan kelola kode promo untuk pelanggan</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.promos.create') }}" class="btn btn-primary">
                ➕ Buat Promo Baru
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Kode Promo</th>
                    <th>Deskripsi</th>
                    <th>Tipe & Nilai</th>
                    <th>Tanggal Valid</th>
                    <th>Penggunaan</th>
                    <th>Limit / Customer</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($promos as $promo)
                    <tr>
                        <td>
                            <span class="badge bg-primary px-3 py-2 font-monospace">{{ $promo->code }}</span>
                        </td>
                        <td>
                            {{ $promo->description ?? '-' }}
                        </td>
                        <td>
                            <strong>{{ $promo->discount_type === 'percentage' ? $promo->discount_value . '%' : 'Rp ' . number_format($promo->discount_value, 0, ',', '.') }}</strong>
                            <small class="text-muted d-block">
                                {{ $promo->discount_type === 'percentage' ? 'Persentase' : 'Fixed' }}
                            </small>
                        </td>
                        <td>
                            <small>
                                {{ $promo->valid_from->format('d M Y') }} s/d {{ $promo->valid_until->format('d M Y') }}
                            </small>
                        </td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar 
                                    {{ $promo->max_usage && $promo->usage_count >= $promo->max_usage ? 'bg-danger' : 'bg-success' }}"
                                    role="progressbar"
                                    style="width: {{ $promo->max_usage ? ($promo->usage_count / $promo->max_usage * 100) : 0 }}%"
                                    aria-valuenow="{{ $promo->usage_count }}"
                                    aria-valuemin="0"
                                    aria-valuemax="{{ $promo->max_usage ?? 100 }}">
                                </div>
                            </div>
                            <small>{{ $promo->usage_count }} / {{ $promo->max_usage ?? '∞' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $promo->max_usage_per_customer }}x</span>
                        </td>
                        <td>
                            @if($promo->isValid())
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Tidak Aktif</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.promos.show', $promo) }}" class="btn btn-sm btn-info">Lihat</a>
                            <a href="{{ route('admin.promos.edit', $promo) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('admin.promos.destroy', $promo) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <p class="text-muted mb-0">Belum ada kode promo. <a href="{{ route('admin.promos.create') }}">Buat sekarang</a></p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $promos->links() }}
    </div>
</div>
@endsection
