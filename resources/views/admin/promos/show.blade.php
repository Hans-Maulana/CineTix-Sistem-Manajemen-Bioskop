@extends('layouts.admin')

@section('title', 'Detail Promo')

@section('content')
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-1">{{ $promo->code }}</h1>
            <p class="text-muted">{{ $promo->description ?? 'Promo tanpa deskripsi' }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.promos.edit', $promo) }}" class="btn btn-warning me-2">Edit</a>
            <a href="{{ route('admin.promos.index') }}" class="btn btn-secondary">← Kembali</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Informasi Promo</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Kode Promo</label>
                        <p class="badge bg-primary px-3 py-2 font-monospace">{{ $promo->code }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Tipe & Nilai Diskon</label>
                        <p>
                            <strong>
                                @if($promo->discount_type === 'percentage')
                                    {{ $promo->discount_value }}%
                                @else
                                    Rp {{ number_format($promo->discount_value, 0, ',', '.') }}
                                @endif
                            </strong>
                            <span class="text-muted">({{ $promo->discount_type === 'percentage' ? 'Persentase' : 'Fixed Amount' }})</span>
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Berlaku</label>
                        <p>
                            {{ $promo->valid_from->format('d M Y') }} hingga {{ $promo->valid_until->format('d M Y') }}
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Status</label>
                        <p>
                            @if($promo->isValid())
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Tidak Aktif</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Penggunaan & Limit</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Total Penggunaan</label>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar {{ $promo->max_usage && $promo->usage_count >= $promo->max_usage ? 'bg-danger' : 'bg-success' }}"
                                role="progressbar"
                                style="width: {{ $promo->max_usage ? ($promo->usage_count / $promo->max_usage * 100) : 0 }}%"
                                aria-valuenow="{{ $promo->usage_count }}"
                                aria-valuemin="0"
                                aria-valuemax="{{ $promo->max_usage ?? 100 }}">
                                {{ $promo->usage_count }} / {{ $promo->max_usage ?? '∞' }}
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Max Usage Total</label>
                        <p>
                            <strong>{{ $promo->max_usage ?? 'Unlimited' }}</strong>
                            <small class="text-muted">(Total berapa kali promo bisa digunakan)</small>
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Max Usage Per Customer</label>
                        <p>
                            <strong>{{ $promo->max_usage_per_customer }}</strong>
                            <small class="text-muted">(Max per customer)</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($promo->usages->count() > 0)
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Riwayat Penggunaan ({{ $promo->usages->count() }} customer)</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Penggunaan</th>
                            <th>Booking ID</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($promo->usages as $usage)
                            <tr>
                                <td>{{ $usage->user?->name ?? '-' }}</td>
                                <td>{{ $usage->user?->email ?? '-' }}</td>
                                <td>{{ $usage->usage_count }}x</td>
                                <td>
                                    @if($usage->booking_id)
                                        <a href="#" class="text-primary">{{ $usage->booking_id }}</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $usage->updated_at->format('d M Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            Promo ini belum pernah digunakan oleh customer manapun.
        </div>
    @endif
</div>
@endsection
