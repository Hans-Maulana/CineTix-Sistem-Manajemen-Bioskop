@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4" data-aos="fade-down">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('landing-page') }}"
                        class="text-primary text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('films.search') }}"
                        class="text-primary text-decoration-none">Film</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $film->title }}</li>
            </ol>
        </nav>

        <div class="row g-5">
            <!-- Poster & Basic Info -->
            <div class="col-lg-4" data-aos="fade-right">
                <div class="card border-0 shadow-lg overflow-hidden rounded-4">
                    <img src="{{ $film->cover_url }}" alt="{{ $film->title }}" class="img-fluid w-100">
                    <div class="card-body bg-dark text-white p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-primary px-3 py-2 fs-6">{{ $film->classification }}</span>
                            @if($film->status !== 'coming_soon')
                                <div class="text-warning fs-5">
                                    <iconify-icon icon="solar:star-bold" class="me-1"></iconify-icon>
                                    <strong>{{ number_format($avgRating, 1) }}</strong><span
                                        class="text-white text-opacity-50 fs-7">/5</span>
                                </div>
                            @endif
                        </div>

                        @if($film->status === 'coming_soon')
                            <h5 class="fw-bold mb-1">Tanggal Rilis</h5>
                            <p class="text-white text-opacity-70 mb-3">
                                <iconify-icon icon="lucide:calendar" class="me-2 text-warning"></iconify-icon>
                                {{ \Carbon\Carbon::parse($film->release_date)->translatedFormat('d F Y') }}
                            </p>
                        @endif

                        <h5 class="fw-bold mb-1">Durasi</h5>
                        <p class="text-white text-opacity-70 mb-3"><iconify-icon icon="lucide:clock"
                                class="me-2"></iconify-icon>{{ $film->duration }} Menit</p>

                        <h5 class="fw-bold mb-1">Genre</h5>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($film->genres as $genre)
                                <span
                                    class="badge border border-white border-opacity-25 fw-normal">{{ $genre->genre_name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details & Schedules -->
            <div class="col-lg-8" data-aos="fade-up">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <h1 class="display-5 fw-bold mb-0">{{ $film->title }}</h1>
                    @if($film->status === 'coming_soon')
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">COMING SOON</span>
                    @endif
                </div>

                <!-- Synopsis Section -->
                @if($film->synopsis)
                    <div class="mb-5 mt-4">
                        <h5 class="fw-bold border-bottom pb-2 mb-3 text-start">Sinopsis</h5>
                        <p class="fs-5 text-secondary lh-lg" style="text-align: justify;">{{ $film->synopsis }}</p>
                    </div>
                @endif

                <div class="row mb-5 g-4">
                    <div class="col-md-6">
                        <div class="d-flex gap-3 align-items-center">
                            <div
                                class="round-45 bg-light-primary text-primary hstack justify-content-center rounded-circle">
                                <iconify-icon icon="lucide:user" class="fs-5"></iconify-icon>
                            </div>
                            <div>
                                <small class="text-muted d-block">Sutradara</small>
                                <span class="fw-bold">{{ $film->director }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-3 align-items-center">
                            <div
                                class="round-45 bg-light-primary text-primary hstack justify-content-center rounded-circle">
                                <iconify-icon icon="lucide:building" class="fs-5"></iconify-icon>
                            </div>
                            <div>
                                <small class="text-muted d-block">Produksi</small>
                                <span class="fw-bold">{{ $film->production }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex gap-3 align-items-start">
                            <div
                                class="round-45 bg-light-primary text-primary hstack justify-content-center rounded-circle flex-shrink-0">
                                <iconify-icon icon="lucide:users" class="fs-5"></iconify-icon>
                            </div>
                            <div>
                                <small class="text-muted d-block">Aktor</small>
                                <span class="fw-bold">{{ $film->actors }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-5 opacity-10">

                <!-- Jadwal Tayang / Status Rilis -->
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="round-12 bg-primary rounded-pill" style="width: 10px; height: 30px;"></div>
                    <h3 class="mb-0 fw-bold">{{ $film->status === 'coming_soon' ? 'Informasi Rilis' : 'Jadwal Tayang' }}</h3>
                </div>

                @if($film->status === 'coming_soon')
                    <div class="card border-0 shadow-sm p-4 p-md-5 text-center bg-gradient-coming-soon text-white position-relative overflow-hidden">
                        <div class="position-absolute top-0 end-0 opacity-10" style="transform: translate(20%, -20%);">
                            <iconify-icon icon="solar:videocamera-bold" style="font-size: 15rem;"></iconify-icon>
                        </div>
                        <div class="position-relative z-1">
                            <iconify-icon icon="solar:calendar-date-bold" class="display-3 text-warning mb-3"></iconify-icon>
                            <h3 class="fw-bold mb-2 text-white">Film ini Akan Segera Tayang!</h3>
                            <p class="text-white-50 mb-4 mx-auto" style="max-width: 500px;">
                                Film <strong>{{ $film->title }}</strong> dijadwalkan untuk rilis pada tanggal 
                                <span class="text-warning fw-bold">{{ \Carbon\Carbon::parse($film->release_date)->translatedFormat('d F Y') }}</span>. 
                                Dapatkan pengalaman menonton terbaik hanya di bioskop CineTix.
                            </p>
                            
                            <hr class="border-light opacity-20 my-4">
                            
                            <h5 class="fw-bold mb-3 text-white">Ingin Menjadi Yang Pertama Menonton?</h5>
                            <form action="#" class="d-flex flex-column flex-sm-row gap-2 justify-content-center align-items-center mx-auto" style="max-width: 450px;" onsubmit="event.preventDefault(); alert('Terima kasih! Kami akan mengirimkan notifikasi ke email Anda ketika penjualan tiket dibuka.'); this.reset();">
                                <input type="email" class="form-control rounded-pill px-4 py-2 border-0 bg-white" placeholder="Masukkan email Anda..." required>
                                <button type="submit" class="btn btn-warning rounded-pill px-4 py-2 fw-bold text-white d-flex align-items-center gap-2 flex-shrink-0 transition-all hover-scale">
                                    <iconify-icon icon="lucide:bell" class="fs-5"></iconify-icon>
                                    <span>Ingatkan Saya</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @elseif($film->schedules->isNotEmpty())
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                            <thead style="background-color: #f1f3f5 !important;">
                                <tr>
                                    <th class="ps-4 py-3 border-0 text-dark fw-bold" style="color: #1a1a1a !important;">Tanggal</th>
                                    <th class="py-3 border-0 text-dark fw-bold" style="color: #1a1a1a !important;">Studio</th>
                                    <th class="py-3 border-0 text-center text-dark fw-bold" style="color: #1a1a1a !important;">Status</th>
                                    <th class="py-3 border-0 text-center text-dark fw-bold" style="color: #1a1a1a !important;">Waktu</th>
                                    <th class="py-3 border-0 text-center text-dark fw-bold" style="color: #1a1a1a !important;">Sisa Kursi</th>
                                    <th class="py-3 border-0 text-dark fw-bold" style="color: #1a1a1a !important;">Harga</th>
                                    <th class="pe-4 py-3 border-0 text-end text-dark fw-bold" style="color: #1a1a1a !important;">Aksi</th>
                                </tr>
                            </thead>
                                <tbody>
                                    @foreach($film->schedules as $schedule)
                                                                <tr>
                                                                    <td class="ps-4 fw-medium" style="color: #1a1a1a !important;">{{ $schedule->schedule_date->format('d M Y') }}</td>
                                                                    <td>
                                                                        <span class="fw-bold text-dark" style="color: #1a1a1a !important;">{{ $schedule->studio->name }}</span>
                                                                        <small
                                                                            class="d-block text-muted">{{ $schedule->studio->type->name ?? '2D' }}</small>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        @php
                                                                            $statusClass = match ($schedule->status) {
                                                                                'on schedule' => 'bg-info',
                                                                                'complete' => 'bg-success',
                                                                                'canceled' => 'bg-danger',
                                                                                default => 'bg-secondary'
                                                                            };
                                                                        @endphp
                                         <span
                                                                            class="badge {{ $statusClass }} px-2 py-1 text-dark small">{{ strtoupper($schedule->status) }}</span>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <span
                                                                            class="badge bg-white text-dark border px-3 py-2 fw-bold" style="color: #1a1a1a !important;">{{ $schedule->start_time->format('H:i') }}</span>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <div class="d-flex flex-column align-items-center">
                                                                            <div class="progress w-75 mb-1" style="height: 6px;">
                                                                                @php $percent = ($schedule->available_seats / $schedule->studio->capacity) * 100; @endphp
                                                                                <div class="progress-bar {{ $percent > 30 ? 'bg-success' : 'bg-danger' }}"
                                                                                    role="progressbar" style="width: {{ $percent }}%"></div>
                                                                            </div>
                                                                            <small
                                                                                class="fw-bold {{ $schedule->available_seats > 0 ? 'text-success' : 'text-danger' }}">
                                                                                {{ $schedule->available_seats }} / {{ $schedule->studio->capacity }}
                                                                            </small>
                                                                        </div>
                                                                    </td>
                                                                    <td class="fw-bold text-primary" style="color: var(--bs-primary) !important;">Rp
                                                                        {{ number_format($schedule->ticket_price, 0, ',', '.') }}</td>
                                                                    <td class="pe-4 text-end">
                                                                        @if($schedule->available_seats > 0 && $schedule->status === 'on schedule')
                                                                            <a href="{{ route('booking.show', $schedule) }}"
                                                                                class="btn btn-primary rounded-pill px-7 text-white fw-bold shadow-sm">
                                                                                Pilih Kursi
                                                                            </a>
                                                                        @else
                                                                            <button class="btn btn-secondary rounded-pill px-4" disabled>
                                                                                {{ $schedule->status === 'canceled' ? 'Batal' : ($schedule->status === 'complete' ? 'Selesai' : 'Habis') }}
                                                                            </button>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5 bg-light-custom rounded-4">
                        <iconify-icon icon="solar:calendar-broken" class="display-1 text-muted opacity-25 mb-3"></iconify-icon>
                        <h5 class="text-muted">Maaf, belum ada jadwal tayang tersedia.</h5>
                    </div>
                @endif

                @if($film->status !== 'coming_soon')
                    <!-- Reviews -->
                    <div class="d-flex align-items-center justify-content-between mt-10 mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="round-12 bg-primary rounded-pill" style="width: 10px; height: 30px;"></div>
                            <h3 class="mb-0 fw-bold">Ulasan Penonton</h3>
                        </div>
                        <span class="badge bg-light-custom text-dark px-3 py-2">{{ $film->reviews->count() }} Ulasan</span>
                    </div>

                    @if($film->reviews->isNotEmpty())
                        <div class="row g-4">
                            @foreach($film->reviews as $review)
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm h-100 rounded-4">
                                        <div class="card-body p-4">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div class="hstack gap-2">
                                                    <div
                                                        class="round-32 bg-primary text-dark rounded-circle hstack justify-content-center small fw-bold">
                                                        {{ substr($review->user->name, 0, 1) }}
                                                    </div>
                                                    <h6 class="mb-0 fw-bold">{{ $review->user->name }}</h6>
                                                </div>
                                                <div class="text-warning">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <iconify-icon
                                                            icon="solar:star-{{ $i <= $review->rating ? 'bold' : 'linear' }}"></iconify-icon>
                                                    @endfor
                                                </div>
                                            </div>
                                            <p class="text-muted mb-0">{{ $review->comment }}</p>
                                            <hr class="my-3 opacity-5">
                                            <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 bg-light-custom rounded-4">
                            <p class="text-muted mb-0">Belum ada ulasan untuk film ini.</p>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <style>
        .bg-light-primary {
            background-color: rgba(var(--bs-primary-rgb), 0.1);
        }

        .bg-gradient-coming-soon {
            background: linear-gradient(135deg, #1A1953 0%, #162E93 100%) !important;
            border-radius: 20px;
        }

        .hover-scale {
            transition: all 0.3s ease;
        }
        .hover-scale:hover {
            transform: scale(1.03);
            box-shadow: 0 8px 25px rgba(255, 193, 7, 0.3) !important;
        }

        .round-45 {
            width: 45px;
            height: 45px;
        }

        .round-32 {
            width: 32px;
            height: 32px;
        }

        .mt-10 {
            margin-top: 5rem;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            content: "›";
            font-size: 1.5rem;
            line-height: 1;
            vertical-align: middle;
        }
    </style>
@endsection