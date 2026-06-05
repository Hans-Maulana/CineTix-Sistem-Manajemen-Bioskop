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
    <!-- Search Bar -->
    <div class="row mb-4">
        <div class="col-md-4 ms-auto">
            <form action="{{ route('admin.customers.index') }}" method="GET">
                <div class="input-group shadow-sm rounded-3 overflow-hidden border">
                    <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-0 px-2" placeholder="Cari nama, email, atau kontak..." value="{{ request('search') }}">
                    @if(request('search'))
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-light border-0 d-flex align-items-center"><i class="bi bi-x-lg text-muted"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

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
