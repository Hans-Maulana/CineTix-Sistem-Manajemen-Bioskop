@extends('layouts.admin')

@section('title', 'Jadwal Film')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h1 class="fw-bold text-primary">Jadwal Tayang</h1>
        <p class="text-muted">Atur jadwal penayangan film di setiap studio.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="{{ route('admin.schedules.create') }}" class="btn-teal text-decoration-none">
            <i class="bi bi-calendar-plus"></i> Tambah Jadwal
        </a>
    </div>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th class="py-3 px-4">Film</th>
                    <th class="py-3">Studio</th>
                    <th class="py-3">Tanggal</th>
                    <th class="py-3">Jam</th>
                    <th class="py-3">Harga Tiket</th>
                    <th class="py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $schedule)
                <tr>
                    <td class="px-4 py-3">
                        <div class="d-flex align-items-center">
                            <img src="{{ $schedule->film->cover_url }}" class="rounded me-3" style="width: 35px; height: 50px; object-fit: cover;">
                            <span class="fw-bold">{{ $schedule->film->title }}</span>
                        </div>
                    </td>
                    <td><span class="badge bg-light text-dark border">{{ $schedule->studio->name }}</span></td>
                    <td>{{ $schedule->schedule_date->format('d M Y') }}</td>
                    <td><span class="text-primary fw-bold">{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</span></td>
                    <td class="fw-bold text-success">Rp {{ number_format($schedule->ticket_price, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="{{ route('admin.schedules.edit', $schedule) }}" class="btn btn-sm btn-outline-warning rounded-3 me-2">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-3">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">Belum ada jadwal tayang.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $schedules->links() }}
    </div>
</div>
@endsection
