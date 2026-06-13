@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h2 class="mb-0 text-dark fw-bold">📜 Daftar Transaksi</h2>
                <a href="{{ route('landing-page') }}" class="btn btn-outline-secondary text-dark border-secondary px-4 rounded-pill">Kembali</a>
            </div>

            {{-- Flash Messages --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <iconify-icon icon="lucide:alert-circle" class="me-2"></iconify-icon>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <iconify-icon icon="lucide:check-circle" class="me-2"></iconify-icon>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($bookings->isNotEmpty())
                <div class="row g-4">
                    @foreach($bookings as $booking)
                        @php
                            $firstTicket = $booking->ticketBookings->first();
                            $film = $firstTicket ? $firstTicket->schedule->film : null;
                            $seatCodes = $booking->ticketBookings->map(function($tb) {
                                return $tb->seat->seat_code;
                            })->implode(', ');
                            
                            // Cek apakah ada pending payment
                            $pendingPayment = $booking->payments()->where('status', 'pending')->first();
                            
                            // Tampilkan review jika transaksi Berhasil dan jadwal tayang sudah lewat (based on date, not status)
                            $showReviewForm = $booking->status === 'confirmed' && $firstTicket && 
                                             $firstTicket->schedule && 
                                             $firstTicket->schedule->schedule_date->toDateString() <= now()->toDateString();
                            $existingReview = null;
                            if ($showReviewForm && $film) {
                                $existingReview = \App\Models\Review::where('user_id', auth()->id())->where('film_id', $film->id)->first();
                            }
                        @endphp
                        <div class="col-lg-6">
                            <div class="card shadow-sm border-0 h-100 overflow-hidden transaction-card" 
                                 style="border-radius: 15px !important;">
                                <div class="row g-0 h-100">
                                    <div class="col-4">
                                        <img src="{{ $film ? $film->cover_url : asset('storage/cover/default-cover.svg') }}" 
                                             alt="{{ $film->title ?? 'Film' }}" 
                                             class="img-fluid h-100 object-fit-fill"
                                             style="min-height: 180px;">
                                    </div>
                                    <div class="col-8">
                                        <div class="card-body d-flex flex-column h-100 p-4">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div>
                                                    <h5 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 180px;">{{ $film->title ?? 'Transaksi' }}</h5>
                                                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                                        @if($booking->status === 'confirmed')
                                                            <iconify-icon icon="lucide:check-circle" class="me-1 text-success align-middle"></iconify-icon>✓ Pembayaran Berhasil
                                                        @elseif($booking->status === 'pending' && $pendingPayment)
                                                            <iconify-icon icon="lucide:clock" class="me-1 text-info align-middle"></iconify-icon>⏱ Menunggu Pembayaran (Sisa {{ $pendingPayment->remaining_seconds < 3600 ? floor($pendingPayment->remaining_seconds / 60) . ' menit' : 'waktu terbatas' }})
                                                        @elseif($booking->status === 'pending')
                                                            <iconify-icon icon="lucide:hourglass" class="me-1 text-warning align-middle"></iconify-icon>⏳ Belum Ada Pembayaran
                                                        @else
                                                            <iconify-icon icon="lucide:x-circle" class="me-1 text-danger align-middle"></iconify-icon>✗ Pembayaran Dibatalkan
                                                        @endif
                                                    </small>
                                                </div>
                                                <span class="badge {{ $booking->status === 'confirmed' ? 'bg-success bg-opacity-10 text-success' : ($booking->status === 'pending' && $pendingPayment ? 'bg-info bg-opacity-10 text-info' : ($booking->status === 'pending' ? 'bg-warning bg-opacity-10 text-warning' : 'bg-danger bg-opacity-10 text-danger')) }} small px-3 py-1.5 rounded-pill fw-bold">
                                                    @if($booking->status === 'confirmed')
                                                        ✓ Berhasil
                                                    @elseif($booking->status === 'pending' && $pendingPayment)
                                                        ⏱ Tertunda
                                                    @elseif($booking->status === 'pending')
                                                        ⏳ Menunggu
                                                    @else
                                                        ✗ Batal
                                                    @endif
                                                </span>
                                            </div>
                                            
                                            @if($firstTicket)
                                                <div class="mb-2">
                                                    <span class="badge bg-light text-white border px-2 py-1 rounded small" style="font-size: 0.75rem;">
                                                        <iconify-icon icon="solar:video-library-bold" class="me-1 align-middle text-white"></iconify-icon>
                                                        {{ $firstTicket->schedule->studio->name }} ({{ $firstTicket->schedule->studio->type->name ?? '2D' }})
                                                    </span>
                                                </div>
                                                <div class="d-flex flex-wrap gap-x-3 gap-y-1 text-muted small mb-2" style="font-size: 0.8rem;">
                                                    <div class="me-2">
                                                        <iconify-icon icon="lucide:calendar" class="me-1 align-middle"></iconify-icon>
                                                        {{ $firstTicket->schedule->schedule_date->translatedFormat('d M Y') }}
                                                    </div>
                                                    <div>
                                                        <iconify-icon icon="lucide:clock" class="me-1 align-middle"></iconify-icon>
                                                        {{ $firstTicket->schedule->start_time->format('H:i') }}
                                                    </div>
                                                </div>
                                                <div class="mb-2 small" style="font-size: 0.8rem;">
                                                    <span class="text-muted">Kursi:</span>
                                                    <span class="fw-bold text-dark">{{ $seatCodes }}</span>
                                                </div>
                                            @else
                                                <p class="text-muted small mb-3">
                                                    <iconify-icon icon="lucide:calendar" class="me-1"></iconify-icon>
                                                    {{ $booking->created_at->format('d M Y') }}
                                                </p>
                                            @endif

                                            <div class="mt-auto pt-2 border-top">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <small class="text-muted d-block" style="font-size: 0.7rem;">Total Pembayaran</small>
                                                        <span class="fw-bold text-primary fs-6">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                                                    </div>
                                                    
                                                    <div class="text-end d-flex flex-column align-items-end gap-1">
                                                        @if($booking->status === 'pending' && $pendingPayment)
                                                            <a href="{{ route('booking.process-payment', ['booking' => $booking, 'payment' => $pendingPayment]) }}" class="btn btn-sm btn-info px-4 py-1.5 rounded-pill text-white fw-bold">Lanjutkan Pembayaran</a>
                                                        @elseif($booking->status === 'pending')
                                                            <a href="{{ route('booking.payment', $booking) }}" class="btn btn-sm btn-success px-4 py-1.5 rounded-pill text-white fw-bold">Bayar</a>
                                                        @elseif($booking->status === 'confirmed' && $firstTicket && $firstTicket->schedule->status !== 'complete')
                                                            <a href="{{ route('booking.tickets') }}" class="text-primary small fw-bold text-decoration-none" style="font-size: 0.8rem;">Lihat Tiket <iconify-icon icon="lucide:chevron-right"></iconify-icon></a>
                                                        @elseif($booking->status === 'refunded')
                                                            <span class="badge bg-success bg-opacity-10 text-success small px-2 py-1 rounded-pill fw-bold">
                                                                <iconify-icon icon="lucide:check-circle" class="me-1"></iconify-icon>Refund Selesai
                                                            </span>
                                                        @endif

                                                        {{-- Refund Status Badge --}}
                                                        @if($booking->refund_status === 'requested')
                                                            <span class="badge small px-2 py-1 rounded-pill fw-bold" style="background:#fff3cd;color:#856404;font-size:.7rem;">
                                                                <iconify-icon icon="lucide:hourglass" class="me-1"></iconify-icon>Refund Diajukan
                                                            </span>
                                                        @elseif($booking->refund_status === 'approved')
                                                            <span class="badge small px-2 py-1 rounded-pill fw-bold" style="background:#d1f0e2;color:#155e35;font-size:.7rem;">
                                                                <iconify-icon icon="lucide:check-circle" class="me-1"></iconify-icon>Refund Disetujui
                                                            </span>
                                                        @elseif($booking->refund_status === 'rejected')
                                                            <span class="badge small px-2 py-1 rounded-pill fw-bold" style="background:#fde8e8;color:#7b2020;font-size:.7rem;">
                                                                <iconify-icon icon="lucide:x-circle" class="me-1"></iconify-icon>Refund Ditolak
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Review Section --}}
                                            @if($showReviewForm && $film)
                                                <div class="review-section mt-3 pt-3 border-top" onclick="event.stopPropagation()">
                                                    @if($existingReview)
                                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                                            <span class="small fw-bold text-secondary">Ulasan Anda</span>
                                                            <div class="text-warning small">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <iconify-icon icon="solar:star-{{ $i <= $existingReview->rating ? 'bold' : 'linear' }}"></iconify-icon>
                                                                @endfor
                                                            </div>
                                                        </div>
                                                        @if($existingReview->comment)
                                                            <p class="text-muted small mb-0 italic">"{{ $existingReview->comment }}"</p>
                                                        @else
                                                            <p class="text-muted small mb-0 italic text-opacity-50">Tidak ada komentar.</p>
                                                        @endif
                                                    @else
                                                        <!-- Button to trigger review form -->
                                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-bold w-100 mb-2 toggle-review-btn text-white" 
                                                                onclick="toggleReviewForm({{ $booking->id }})">
                                                            <iconify-icon icon="solar:pen-bold" class="me-1 align-middle"></iconify-icon> Tulis Ulasan
                                                        </button>

                                                        <!-- Review Form (hidden by default) -->
                                                        <form id="review-form-{{ $booking->id }}" action="{{ route('booking.store-review') }}" method="POST" style="display: none;">
                                                            @csrf
                                                            <input type="hidden" name="film_id" value="{{ $film->id }}">
                                                            
                                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                                <span class="small fw-bold text-dark">Beri Rating:</span>
                                                                
                                                                <!-- Interactive Stars -->
                                                                <div class="rating-stars d-flex gap-1">
                                                                    @for($i = 5; $i >= 1; $i--)
                                                                        <input type="radio" id="star-{{ $booking->id }}-{{ $i }}" name="rating" value="{{ $i }}" class="btn-check" required>
                                                                        <label for="star-{{ $booking->id }}-{{ $i }}" class="star-label cursor-pointer" title="{{ $i }} Bintang">
                                                                            <iconify-icon icon="solar:star-bold" class="fs-5"></iconify-icon>
                                                                        </label>
                                                                    @endfor
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="input-group input-group-sm">
                                                                <input type="text" name="comment" class="form-control border-secondary-subtle" placeholder="Komentar (opsional)...">
                                                                <button type="submit" class="btn btn-primary btn-sm text-white px-3 fw-bold">Kirim</button>
                                                            </div>
                                                        </form>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-5">
                    {{ $bookings->links() }}
                </div>
            @else
                <div class="card shadow-sm border-0 p-5 text-center rounded-4 bg-white">
                    <div class="display-1 mb-4 text-muted">🛒</div>
                    <h4 class="fw-bold text-dark">Belum Ada Transaksi</h4>
                    <p class="text-muted">Sepertinya Anda belum pernah melakukan pemesanan.</p>
                    <div class="mt-3">
                        <a href="{{ route('landing-page') }}" class="btn btn-primary px-5 py-2.5 rounded-pill fw-bold text-white shadow-sm" style="background: #1A1953 !important; border: none;">Mulai Pesan Sekarang</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .transaction-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .transaction-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
    .rating-stars {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
    }
    .rating-stars input {
        display: none;
    }
    .rating-stars label {
        cursor: pointer;
        color: #cbd5e1;
        transition: color 0.15s ease;
    }
    .rating-stars label:hover,
    .rating-stars label:hover ~ label,
    .rating-stars input:checked ~ label {
        color: #ffc107 !important;
    }
    .italic {
        font-style: italic;
    }
</style>
@endpush
@push('scripts')
<script>
    function toggleReviewForm(bookingId) {
        const form = document.getElementById('review-form-' + bookingId);
        if (form) {
            if (form.style.display === 'none') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
    }
</script>
@endpush
@endsection
