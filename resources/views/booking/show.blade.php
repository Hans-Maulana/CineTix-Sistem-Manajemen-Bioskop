@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Breadcrumb & Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-down">
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('landing-page') }}"
                        class="text-primary text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('films.detail', $schedule->film) }}"
                        class="text-primary text-decoration-none">{{ $schedule->film->title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pilih Kursi</li>
            </ol>
        </nav>
        <a href="{{ route('films.detail', $schedule->film) }}" class="btn btn-back-custom rounded-pill px-4 py-2 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-outline" class="fs-5"></iconify-icon>
            <span>Kembali</span>
        </a>
    </div>

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

                    <div class="cinema-screen mb-5">
                        LAYAR BIOSKOP
                    </div>

                    <div class="seat-map d-flex flex-column gap-2 align-items-center mb-4" id="seatsContainer">
                        @if($schedule->studio->seat_layout)
                            @foreach(array_reverse($schedule->studio->seat_layout, true) as $rowIndex => $row)
                                @php
                                    $seatCounter = 1;
                                @endphp
                                <div class="d-flex gap-2 align-items-center">
                                    <div class="row-label fw-bold text-muted me-2" style="width: 20px;">{{ chr(65 + $rowIndex) }}</div>

                                    @foreach($row as $colIndex => $isSeat)
                                        @if($isSeat == 1)
                                            @php
                                                $seatCode = chr(65 + $rowIndex) . $seatCounter;
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
                                                    {{ $seatCounter }}
                                                </button>
                                            @else
                                                <div class="seat-placeholder bg-danger opacity-25 rounded" style="width: 28px; height: 28px;" title="Seat Missing from DB"></div>
                                            @endif
                                            @php
                                                $seatCounter++;
                                            @endphp
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
                            <div id="seatIdsContainer"></div>
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

                        @if($isAuthenticated)
                        <div class="mb-4" id="promoSection">
                            <label for="promoCode" class="form-label fw-bold text-dark small">Kode Promo (Opsional)</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control border-primary-subtle" id="promoCode" name="promo_code" placeholder="Masukkan kode promo" autocomplete="off">
                                <button class="btn btn-primary fw-bold text-white px-3" type="button" id="applyPromo">Terapkan</button>
                            </div>
                            <div id="promoMessage" class="small mt-2"></div>
                            <div id="discountRow" class="d-none justify-content-between text-success small mt-2">
                                <span>Diskon promo:</span>
                                <span id="discountAmount">- Rp 0</span>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-info border-0 mb-3 py-3">
                            <p class="mb-0 small">
                                <a href="{{ route('login') }}" class="link-primary fw-bold">Login</a> untuk pakai kode promo
                                <strong>WELCOME2026</strong> (diskon Rp 20.000).
                            </p>
                        </div>
                        @endif

                        @if($isAuthenticated)
                            <input type="hidden" id="guestEmail" name="guest_email" value="{{ $user->email }}">
                        @else
                            <div class="mb-4">
                                <label for="guestEmail" class="form-label fw-bold text-dark small">Email untuk kirim tiket <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="guestEmail" name="guest_email"
                                       placeholder="contoh@email.com" required autocomplete="email"
                                       value="{{ old('guest_email') }}">
                                <div class="form-text">Tiket digital akan dikirim ke email ini setelah pembayaran.</div>
                            </div>
                        @endif

                        <hr class="my-4 opacity-10">

                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark">Total Harga:</span>
                            <span id="totalPrice" class="fs-4 fw-bold" style="color: #1A1953;">Rp 0</span>
                        </div>

                        <button type="button" class="btn btn-primary text-white w-100 py-3 fw-bold rounded-3 shadow-sm" id="bookingBtn" onclick="submitBooking()">
                            Lanjutkan ke Pembayaran <i class="iconify" data-icon="lucide:arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Email & OTP Guest --}}
<div class="modal fade" id="emailConfirmModal" tabindex="-1" aria-labelledby="emailConfirmModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="emailConfirmModalLabel">Checkout Tiket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body pt-2 text-center">

                <div id="step-email-confirm">
                    <p class="mb-2 text-muted">Tiket digital Anda akan dikirim ke:</p>
                    <p class="fs-4 fw-bold text-primary mb-3" id="modalEmailDisplay">—</p>
                    <p class="text-muted small mb-4">Pastikan alamat email sudah benar karena tiket akan dikirimkan ke email ini.</p>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light border w-50 py-2 fw-bold" data-bs-dismiss="modal">
                            Ubah Email
                        </button>
                        <button type="button" class="btn text-white w-50 py-2 fw-bold" id="btnSendOtp" style="background: #1A1953;">
                            Kirim OTP Validasi
                        </button>
                    </div>
                </div>

                <div id="step-otp-input" style="display: none;">
                    <div class="alert alert-info border-0 rounded-3 small mb-4 text-start">
                        <iconify-icon icon="lucide:info" class="me-1"></iconify-icon>
                        6-digit kode OTP telah dikirim ke email Anda. Silakan periksa kotak masuk atau folder spam.
                    </div>
                    <div class="mb-4 text-start">
                        <label class="form-label fw-bold text-dark small">Masukkan Kode OTP <span class="text-danger">*</span></label>
                        <input type="text" id="guestOtpCode" class="form-control py-3 text-center fs-3 fw-bold tracking-widest" placeholder="• • • • • •" maxlength="6" autocomplete="off">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light border w-50 py-2 fw-bold" id="btnBackToEmail">
                            Kembali
                        </button>
                        <button type="button" class="btn text-white w-50 py-2 fw-bold" id="btnVerifyOtp" style="background: #1A1953;">
                            Verifikasi & Lanjut
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    .tracking-widest { letter-spacing: 0.5em; }
</style>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.socket.io/4.5.4/socket.io.min.js"></script>
<script>
    const scheduleId = {{ $schedule->id }};
    const ticketPrice = {{ $schedule->ticket_price }};
    const isAuthenticated = {{ $isAuthenticated ? 'true' : 'false' }};
    let selectedSeats = [];
    let appliedDiscount = 0;
    let promoApplied = false;

    // Fungsi utama submit booking - dipanggil langsung dari onclick tombol
    function submitBooking() {
        if (selectedSeats.length === 0) {
            alert('Silakan pilih minimal 1 kursi terlebih dahulu.');
            return;
        }

        if (!isAuthenticated) {
            // Guest: tampilkan modal OTP
            const guestEmail = document.getElementById('guestEmail');
            const email = guestEmail ? guestEmail.value.trim() : '';
            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                if (guestEmail) { guestEmail.classList.add('is-invalid'); guestEmail.focus(); }
                return;
            }
            if (guestEmail) guestEmail.classList.remove('is-invalid');
            document.getElementById('modalEmailDisplay').textContent = email;
            document.getElementById('step-otp-input').style.display = 'none';
            document.getElementById('step-email-confirm').style.display = 'block';
            document.getElementById('guestOtpCode').value = '';
            const emailModalEl = document.getElementById('emailConfirmModal');
            if (emailModalEl && typeof bootstrap !== 'undefined') {
                new bootstrap.Modal(emailModalEl).show();
            }
            return;
        }

        // User sudah login: isi seat_ids[] dan submit form
        const container = document.getElementById('seatIdsContainer');
        if (container) {
            container.innerHTML = selectedSeats.map(s => '<input type="hidden" name="seat_ids[]" value="' + s.id + '">').join('');
        }

        const btn = document.getElementById('bookingBtn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Memproses...';
        }

        const form = document.getElementById('bookingForm');
        if (form) form.submit();
    }

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
        restoreSelectedSeats();
        if (isAuthenticated) {
            initPromoHandler();
        }
        initGuestCheckout();
        initFormSubmit();
    });

    function restoreSelectedSeats() {
        const saved = sessionStorage.getItem('selected_seats_' + scheduleId);
        if (saved) {
            try {
                const parsedSeats = JSON.parse(saved);
                selectedSeats = [];
                parsedSeats.forEach(seat => {
                    const btn = document.querySelector(`[data-seat-id="${seat.id}"]`);
                    if (btn && !btn.disabled) {
                        btn.classList.add('seat-selected');
                        selectedSeats.push(seat);
                    }
                });
                sessionStorage.setItem('selected_seats_' + scheduleId, JSON.stringify(selectedSeats));
                updateSummary();
            } catch (e) {
                console.error('Failed to restore selected seats:', e);
            }
        }
    }

    function initGuestCheckout() {
        const guestEmail = document.getElementById('guestEmail');
        const bookingBtn = document.getElementById('bookingBtn');
        const bookingForm = document.getElementById('bookingForm');
        const emailModalEl = document.getElementById('emailConfirmModal');
        if (!guestEmail || !bookingBtn || !bookingForm) return;

        guestEmail.addEventListener('input', updateSummary);

        if (isAuthenticated) {
            return;
        }

        let emailModal = null;
        if (emailModalEl && typeof bootstrap !== 'undefined') {
            emailModal = new bootstrap.Modal(emailModalEl);
        }


        document.getElementById('btnSendOtp')?.addEventListener('click', function() {
            const btn = this;
            const email = guestEmail.value.trim();

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Mengirim...';

            fetch('{{ route("guest.send-otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = 'Kirim OTP Validasi';

                if (data.success) {
                    // Pindah ke Step Input OTP
                    document.getElementById('step-email-confirm').style.display = 'none';
                    document.getElementById('step-otp-input').style.display = 'block';
                    setTimeout(() => document.getElementById('guestOtpCode').focus(), 300);
                } else {
                    alert(data.message || 'Gagal mengirim OTP');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.disabled = false;
                btn.innerHTML = 'Kirim OTP Validasi';
                alert('Terjadi kesalahan sistem saat mengirim OTP.');
            });
        });

        document.getElementById('btnBackToEmail')?.addEventListener('click', function() {
            document.getElementById('step-otp-input').style.display = 'none';
            document.getElementById('step-email-confirm').style.display = 'block';
        });

        document.getElementById('btnVerifyOtp')?.addEventListener('click', function() {
            const email = guestEmail.value.trim();
            const otp = document.getElementById('guestOtpCode').value.trim();
            const btn = this;

            if (!otp || otp.length !== 6) {
                alert('Masukkan 6-digit kode OTP dengan benar.');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Memverifikasi...';

            fetch('{{ route("guest.verify-otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email: email, otp: otp })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (emailModal) emailModal.hide();

                    const mainBtn = document.getElementById('bookingBtn');
                    if (mainBtn) {
                        mainBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Mengunci Kursi...';
                        setTimeout(function() {
                            mainBtn.disabled = true;
                        }, 50);
                    }

                    bookingForm.submit();
                } else {
                    btn.disabled = false;
                    btn.innerHTML = 'Verifikasi & Lanjut';
                    alert(data.message || 'Kode OTP Salah!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.disabled = false;
                btn.innerHTML = 'Verifikasi & Lanjut';
                alert('Terjadi kesalahan saat memverifikasi OTP.');
            });
        });
    }

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

        // Save to sessionStorage
        sessionStorage.setItem('selected_seats_' + scheduleId, JSON.stringify(selectedSeats));

        updateSummary();
    }

    function updateSummary() {
        const seatCount = selectedSeats.length;
        const subtotal = seatCount * ticketPrice;
        let discount = promoApplied ? appliedDiscount : 0;

        if (promoApplied && seatCount > 0) {
            discount = calculateDiscount(subtotal);
            appliedDiscount = discount;
        }

        const totalPrice = Math.max(0, subtotal - discount);

        document.getElementById('seatCount').textContent = seatCount;
        document.getElementById('totalPrice').innerHTML = `Rp ${totalPrice.toLocaleString('id-ID')}`;
        // Perbarui hidden inputs seat_ids[] agar benar-benar dikirim sebagai array
        const container = document.getElementById('seatIdsContainer');
        if (container) {
            container.innerHTML = selectedSeats.map(s => `<input type="hidden" name="seat_ids[]" value="${s.id}">`).join('');
        }
        document.getElementById('selectedSeats').innerHTML = seatCount > 0
            ? selectedSeats.map(s => `<span class="badge px-3 py-2 rounded-pill shadow-sm me-1 mb-1" style="background: #1A1953; color: white;">${s.code}</span>`).join('')
            : '<small class="text-secondary opacity-75">Belum ada kursi yang dipilih</small>';

        const discountRow = document.getElementById('discountRow');
        const discountAmount = document.getElementById('discountAmount');
        if (discountRow && discountAmount) {
            if (discount > 0) {
                discountRow.classList.remove('d-none');
                discountRow.classList.add('d-flex');
                discountAmount.textContent = '- Rp ' + discount.toLocaleString('id-ID');
            } else {
                discountRow.classList.add('d-none');
                discountRow.classList.remove('d-flex');
            }
        }

        const bookingBtn = document.getElementById('bookingBtn');
        if (bookingBtn) {
            if (!isAuthenticated) {
                const email = document.getElementById('guestEmail')?.value.trim() || '';
                const validEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
                bookingBtn.disabled = seatCount === 0 || !validEmail;
            } else {
                bookingBtn.disabled = seatCount === 0;
            }
        }
    }

    function calculateDiscount(subtotal) {
        if (!window.activePromo) return 0;
        if (window.activePromo.discount_type === 'percentage') {
            return Math.floor(subtotal * window.activePromo.discount_value / 100);
        }
        return Math.min(window.activePromo.discount_value, subtotal);
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

    function initPromoHandler() {
        const applyBtn = document.getElementById('applyPromo');
        if (!applyBtn) return;

        applyBtn.addEventListener('click', function() {
            const code = document.getElementById('promoCode').value.trim();
            const message = document.getElementById('promoMessage');

            if (!code) {
                message.innerHTML = '<span class="text-danger">Masukkan kode promo</span>';
                return;
            }

            fetch('{{ route('promo.validate') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ code: code })
            })
            .then(response => response.json())
            .then(data => {
                if (data.valid) {
                    window.activePromo = {
                        code: data.code,
                        discount_type: data.discount_type,
                        discount_value: parseFloat(data.discount_value),
                    };
                    promoApplied = true;
                    document.getElementById('promoCode').readOnly = true;
                    applyBtn.disabled = true;
                    message.innerHTML = `<span class="text-success">✓ ${data.message}</span>`;
                    updateSummary();
                } else {
                    window.activePromo = null;
                    promoApplied = false;
                    appliedDiscount = 0;
                    message.innerHTML = `<span class="text-danger">✗ ${data.message}</span>`;
                    updateSummary();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                message.innerHTML = '<span class="text-danger">Terjadi kesalahan saat validasi promo</span>';
            });
        });
    }

    function initFormSubmit() {
        const bookingForm = document.getElementById('bookingForm');
        if (!bookingForm) {
            console.error('bookingForm tidak ditemukan!');
            return;
        }

        if (isAuthenticated) {
            // User sudah login: langsung submit
            bookingForm.addEventListener('submit', function(e) {
                if (selectedSeats.length === 0) {
                    e.preventDefault();
                    alert('Silakan pilih minimal 1 kursi terlebih dahulu.');
                    return;
                }
                // Isi ulang seat_ids[] dari selectedSeats array sebelum submit
                const container = document.getElementById('seatIdsContainer');
                if (container) {
                    container.innerHTML = selectedSeats.map(s => `<input type="hidden" name="seat_ids[]" value="${s.id}">`).join('');
                }
                const btn = document.getElementById('bookingBtn');
                if (btn) {
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Memproses...';
                    setTimeout(function() { btn.disabled = true; }, 100);
                }
            });
        } else {
            // User belum login: butuh OTP
            bookingForm.addEventListener('submit', function(e) {
                e.preventDefault();
                if (selectedSeats.length === 0) {
                    alert('Silakan pilih minimal 1 kursi terlebih dahulu.');
                    return;
                }
                const guestEmail = document.getElementById('guestEmail');
                const email = guestEmail ? guestEmail.value.trim() : '';
                if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    if (guestEmail) {
                        guestEmail.classList.add('is-invalid');
                        guestEmail.focus();
                    }
                    return;
                }
                if (guestEmail) guestEmail.classList.remove('is-invalid');
                document.getElementById('modalEmailDisplay').textContent = email;
                document.getElementById('step-otp-input').style.display = 'none';
                document.getElementById('step-email-confirm').style.display = 'block';
                document.getElementById('guestOtpCode').value = '';
                const emailModalEl = document.getElementById('emailConfirmModal');
                if (emailModalEl && typeof bootstrap !== 'undefined') {
                    new bootstrap.Modal(emailModalEl).show();
                }
            });
        }
    }
</script>

<style>
    .btn-back-custom {
        border: 2px solid #1A1953 !important;
        color: #1A1953 !important;
        font-weight: bold;
        background: transparent;
        transition: all 0.3s ease;
    }
    .btn-back-custom:hover {
        background-color: #1A1953 !important;
        color: #ffffff !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(26, 25, 83, 0.2) !important;
    }
    .breadcrumb-item+.breadcrumb-item::before {
        content: "›" !important;
        font-size: 1.5rem;
        line-height: 1;
        vertical-align: middle;
    }

    .cinema-screen {
        position: relative;
        height: 48px;
        background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
        border-bottom: 5px solid #3b82f6;
        border-radius: 0 0 50% 50% / 0 0 24px 24px;
        box-shadow: 0 14px 28px rgba(59, 130, 246, 0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #e2e8f0;
        font-weight: 800;
        font-size: 0.85rem;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        overflow: hidden;
    }
    .cinema-screen::after {
        content: '';
        position: absolute;
        bottom: -25px;
        left: 15%;
        right: 15%;
        height: 25px;
        background: radial-gradient(ellipse at center, rgba(59, 130, 246, 0.25) 0%, rgba(59, 130, 246, 0) 70%);
        pointer-events: none;
    }
    .seat-row {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 0.5rem;
    }
</style>

@endsection
