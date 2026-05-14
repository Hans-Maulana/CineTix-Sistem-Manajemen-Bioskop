@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        {{-- Flash Messages --}}
        <div class="col-12">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <iconify-icon icon="lucide:alert-circle" class="me-2"></iconify-icon>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <iconify-icon icon="lucide:alert-triangle" class="me-2"></iconify-icon>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
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
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0 text-white">{{ $schedule->film->title }}</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-1"><strong class="text-dark">Studio:</strong> <span class="text-secondary">{{ $schedule->studio->name }}</span></p>
                            <p class="mb-1"><strong class="text-dark">Tipe:</strong> <span class="text-secondary">{{ $schedule->studio->type->name ?? 'Standard' }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong class="text-dark">Tanggal:</strong> <span class="text-secondary">{{ $schedule->schedule_date->format('d M Y') }}</span></p>
                            <p class="mb-1"><strong class="text-dark">Jam Tayang:</strong> <span class="text-secondary">{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</span></p>
                        </div>
                    </div>

                    <hr>

                    <div class="cinema-screen bg-dark text-white text-center py-2 mb-5 rounded shadow-sm">
                        <small>LAYAR BIOSKOP</small>
                    </div>

                    <div class="seat-map d-flex flex-column gap-2 align-items-center mb-4" id="seatsContainer">
                        @if($schedule->studio->seat_layout)
                            @foreach($schedule->studio->seat_layout as $rowIndex => $row)
                                <div class="d-flex gap-2 align-items-center">
                                    <div class="row-label fw-bold text-muted me-2" style="width: 20px;">{{ chr(65 + $rowIndex) }}</div>

                                    @foreach($row as $colIndex => $isSeat)
                                        @if($isSeat == 1)
                                            @php
                                                $seatCode = chr(65 + $rowIndex) . ($colIndex + 1);
                                                $seat = $seatsByCode->get($seatCode);
                                                $isBooked = $seat && in_array($seat->id, $bookedSeatIds);
                                            @endphp

                                            @if($seat)
                                                <button type="button"
                                                        class="seat-btn btn btn-sm {{ $isBooked ? 'seat-booked' : 'seat-available' }}"
                                                        data-seat-id="{{ $seat->id }}"
                                                        data-seat-code="{{ $seatCode }}"
                                                        onclick="toggleSeat({{ $seat->id }}, '{{ $seatCode }}', '{{ $isBooked ? 'booked' : 'available' }}')"
                                                        {{ $isBooked ? 'disabled' : '' }}>
                                                    {{ $colIndex + 1 }}
                                                </button>
                                            @else
                                                <div class="seat-placeholder bg-danger opacity-25 rounded" style="width: 28px; height: 28px;" title="Seat Missing from DB"></div>
                                            @endif
                                        @else
                                            <div class="empty-space" style="width: 28px;"></div>
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info">Layout studio belum diatur.</div>
                        @endif
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex flex-wrap gap-4 justify-content-center">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width: 18px; height: 18px; background-color: #28a745; border-radius: 3px;"></div>
                                <span class="small fw-bold text-secondary">Tersedia</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width: 18px; height: 18px; background-color: #dee2e6; border-radius: 3px;"></div>
                                <span class="small fw-bold text-secondary">Tidak Tersedia</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width: 18px; height: 18px; background-color: #1A1953; border-radius: 3px;"></div>
                                <span class="small fw-bold text-secondary">Pilihanmu</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden position-sticky" style="top: 100px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-dark">Ringkasan Pemesanan</h5>
                </div>
                <div class="card-body p-4">
                    <form id="bookingForm" method="POST" action="{{ route('booking.store') }}">
                        @csrf
                        <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">

                        <div class="mb-3">
                            <label class="form-label"><strong>Kursi Pilihanmu</strong></label>
                            <div id="selectedSeats" class="p-3 bg-light rounded-3 mb-2 min-vh-10" style="min-height: 50px; border: 1px dashed #ced4da;">
                                <small class="text-white text-secondary opacity-75">Belum ada kursi yang dipilih</small>
                            </div>
                            <input type="hidden" name="seat_ids" id="seatIds">
                        </div>

                        <div class="mb-3">
                            <p class="mb-1"><strong>Harga Tiket:</strong></p>
                            <p class="fs-5">Rp {{ number_format($schedule->ticket_price, 0, ',', '.') }}/kursi</p>
                        </div>

                        <div class="mb-3">
                            <p class="mb-1"><strong>Jumlah Kursi:</strong></p>
                            <p id="seatCount" class="fs-5">0</p>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <label for="promoCode" class="form-label fw-bold text-dark small">Kode Promo (Opsional)</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control border-primary-subtle" id="promoCode" name="promo_code" placeholder="Masukkan kode promo">
                                <button class="btn btn-primary fw-bold text-white px-3" type="button" id="applyPromo">Terapkan</button>
                            </div>
                            <div id="promoMessage" class="small mt-2"></div>
                        </div>

                        <hr class="my-4 opacity-10">

                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark">Total Harga:</span>
                            <span id="totalPrice" class="fs-4 fw-bold" style="color: #1A1953;">Rp 0</span>
                        </div>

                        <button type="submit" class="btn btn-primary text-white w-100 py-3 fw-bold rounded-3 shadow-sm" id="bookingBtn" disabled>
                            Lanjutkan ke Pembayaran <i class="iconify" data-icon="lucide:arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.socket.io/4.5.4/socket.io.min.js"></script>
<script>
    const scheduleId = {{ $schedule->id }};
    const ticketPrice = {{ $schedule->ticket_price }};
    let selectedSeats = [];

    // Initialize Pusher untuk Real-Time Seat Updates
    try {
        const pusherKey = '{{ config('broadcasting.connections.pusher.key') }}';
        if (pusherKey) {
            const pusher = new Pusher(pusherKey, {
                cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
                encrypted: true
            });

            const channel = pusher.subscribe(`booking.schedule.${scheduleId}`);

            // Listen untuk Seat Booked Event
            channel.bind('seat-booked', function(data) {
                console.log('Seat booked:', data);
                updateSeatStatus(data.seat_id, 'booked');
            });

            // Listen untuk Seat Available Event
            channel.bind('seat-available', function(data) {
                console.log('Seat available:', data);
                updateSeatStatus(data.seat_id, 'available');
            });
        }
    } catch (error) {
        console.error('Pusher failed to initialize:', error);
    }

    // Initialize styles
    document.addEventListener('DOMContentLoaded', function() {
        attachSeatStyles();
    });

    function attachSeatStyles() {
        const style = document.createElement('style');
        style.textContent = `
            .seat-btn {
                width: 28px;
                height: 28px;
                padding: 0;
                margin: 2px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-weight: bold;
                font-size: 0.75rem;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s ease;
            }

            .seat-available {
                background-color: #28a745;
                color: white;
            }

            .seat-available:hover:not(:disabled) {
                background-color: #218838;
                transform: scale(1.1);
            }

            .seat-booked {
                background-color: #dee2e6;
                color: #adb5bd;
                cursor: not-allowed;
            }

            .seat-selected {
                background-color: #1A1953 !important;
                color: white !important;
                border: 2px solid #0d0d2b;
                transform: scale(1.15);
                box-shadow: 0 0 10px rgba(26, 25, 83, 0.4);
            }

            .seat-row {
                display: flex;
                justify-content: center;
                flex-wrap: wrap;
            }
        `;
        document.head.appendChild(style);
    }

    function toggleSeat(seatId, seatCode, status) {
        if (status === 'booked') return;

        const index = selectedSeats.findIndex(s => s.id === seatId);
        const btn = document.querySelector(`[data-seat-id="${seatId}"]`);

        if (index > -1) {
            selectedSeats.splice(index, 1);
            btn.classList.remove('seat-selected');
        } else {
            selectedSeats.push({ id: seatId, code: seatCode });
            btn.classList.add('seat-selected');
        }

        updateSummary();
    }

    function updateSummary() {
        const seatCount = selectedSeats.length;
        const totalPrice = seatCount * ticketPrice;

        document.getElementById('seatCount').textContent = seatCount;
        document.getElementById('totalPrice').innerHTML = `Rp ${totalPrice.toLocaleString('id-ID')}`;
        document.getElementById('seatIds').value = selectedSeats.map(s => s.id).join(',');
        document.getElementById('selectedSeats').innerHTML = seatCount > 0
            ? selectedSeats.map(s => `<span class="badge px-3 py-2 rounded-pill shadow-sm me-1 mb-1" style="background: #1A1953; color: white;">${s.code}</span>`).join('')
            : '<small class="text-secondary opacity-75">Belum ada kursi yang dipilih</small>';

        document.getElementById('bookingBtn').disabled = seatCount === 0;
    }

    function updateSeatStatus(seatId, status) {
        const btn = document.querySelector(`[data-seat-id="${seatId}"]`);
        if (!btn) return;

        if (status === 'booked') {
            btn.classList.remove('seat-available', 'seat-selected');
            btn.classList.add('seat-booked');
            btn.disabled = true;
        } else if (status === 'available') {
            btn.classList.remove('seat-booked', 'seat-selected');
            btn.classList.add('seat-available');
            btn.disabled = false;
        }
    }

    // Apply promo code
    document.getElementById('applyPromo').addEventListener('click', function() {
        const code = document.getElementById('promoCode').value;
        const message = document.getElementById('promoMessage');

        if (!code) {
            message.innerHTML = '<span class="text-danger">Masukkan kode promo</span>';
            return;
        }

        // Simulate promo validation (replace with actual API call)
        message.innerHTML = '<span class="text-success">Promo berhasil diterapkan</span>';
    });

    // Form submission
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        if (selectedSeats.length === 0) {
            e.preventDefault();
            alert('Silakan pilih minimal 1 kursi');
            return;
        }
        
        // Show loading state
        const btn = document.getElementById('bookingBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Memproses...';
    });
</script>

<style>
    .seat-row {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 0.5rem;
    }
</style>
@endsection
