@extends('layouts.admin')

@section('title', 'Manajemen Customer')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h1 class="fw-bold text-primary">Daftar Customer</h1>
        <p class="text-muted">Lihat data user yang terdaftar sebagai customer di aplikasi kamu.</p>
    </div>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th class="py-3 px-4">Nama Lengkap</th>
                    <th class="py-3">Email</th>
                    <th class="py-3">Kontak</th>
                    <th class="py-3">Bergabung Sejak</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td class="px-4 py-3 fw-bold text-dark">{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->contact ?? '-' }}</td>
                    <td>{{ $customer->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-5 text-muted">Belum ada customer yang terdaftar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $customers->links() }}
    </div>
</div>
@endsection
