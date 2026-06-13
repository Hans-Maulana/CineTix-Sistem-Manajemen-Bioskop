@extends('layouts.app')

@push('styles')
@include('partials.customer_film_styles')
@endpush

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

            @if(session('seats_restored'))
                <div class="alert alert-info alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <iconify-icon icon="lucide:armchair" class="me-2"></iconify-icon>
                    Pilihan kursi Anda ({{ session('seats_restored') }}) sudah dipulihkan setelah login.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        <div class="col-md-8">
            <div class="cx-booking-hero" data-aos="fade-up">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div>
                        <span class="badge bg-white bg-opacity-20 text-white rounded-pill mb-2 px-3 py-2">
                            <iconify-icon icon="lucide:armchair" class="me-1"></iconify-icon> Pilih Kursi
                        </span>
                        <h4 class="mb-1 text-white fw-bold">{{ $schedule->film->title }}</h4>
                        <p class="mb-0 text-white-50 small">{{ $schedule->studio->name }} · {{ $schedule->schedule_date->format('d M Y') }} · {{ $schedule->start_time->format('H:i') }}</p>
                    </div>
                    <div class="text-white text-end">
                        <div class="small text-white-50">Harga per kursi</div>
                        <div class="fs-4 fw-bold">Rp {{ number_format($schedule->ticket_price, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div class="card cx-booking-card shadow-sm">
                <div class="card-header text-white">
                    <h5 class="mb-0 text-white fw-bold">Peta Kursi Studio</h5>
                </div>
                <div class="card-body p-4">
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
            <div class="card cx-summary-card border-0 overflow-hidden position-sticky" style="top: 100px;">
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
                                <a href="{{ route('login', ['redirect' => url()->full()]) }}" class="link-primary fw-bold">Login</a> untuk pakai kode promo
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

                        <button type="button" class="cx-btn-book w-100 py-3" id="bookingBtn" onclick="submitBooking()" style="height: auto;">
                            Lanjutkan ke Pembayaran <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Email & OTP Guest --}}
<div class="modal fade" id="emailConfirmModal" tabindex="-1" aria-labelledby="emailConfirmModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered cx-checkout-modal">
        <div class="modal-content cx-modal-shell">
            <div class="cx-modal-header">
                <button type="button" class="btn-close cx-modal-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                <div class="cx-modal-icon">
                    <iconify-icon icon="lucide:ticket-check"></iconify-icon>
                </div>
                <h5 class="cx-modal-title" id="emailConfirmModalLabel">Checkout Tiket</h5>
                <p class="cx-modal-subtitle">Verifikasi email sebelum melanjutkan pembayaran</p>

                <div class="cx-modal-steps">
                    <span class="cx-modal-step is-active" data-step="1">1. Email</span>
                    <span class="cx-modal-step-divider"></span>
                    <span class="cx-modal-step" data-step="2">2. OTP</span>
                </div>
            </div>

            <div class="cx-modal-body">
                <div id="step-email-confirm">
                    <p class="cx-modal-label">Tiket digital akan dikirim ke</p>
                    <div class="cx-modal-email-box">
                        <iconify-icon icon="lucide:mail" class="cx-modal-email-icon"></iconify-icon>
                        <span id="modalEmailDisplay">—</span>
                    </div>
                    <p class="cx-modal-hint">Pastikan alamat email sudah benar. Tiket dan bukti pembayaran akan dikirim ke email ini.</p>

                    <div class="cx-modal-actions">
                        <button type="button" class="cx-modal-btn cx-modal-btn-ghost" data-bs-dismiss="modal">
                            Ubah Email
                        </button>
                        <button type="button" class="cx-modal-btn cx-modal-btn-primary" id="btnSendOtp">
                            Kirim Kode OTP
                        </button>
                    </div>
                </div>

                <div id="step-otp-input" style="display: none;">
                    <div class="cx-modal-info">
                        <iconify-icon icon="lucide:shield-check"></iconify-icon>
                        <p>Kode 6 digit telah dikirim ke email Anda. Periksa kotak masuk atau folder spam.</p>
                    </div>

                    <label class="cx-modal-label text-center d-block">Masukkan Kode OTP</label>
                    <div class="cx-otp-group" id="otpInputGroup">
                        @for ($i = 0; $i < 6; $i++)
                            <input type="text"
                                   class="cx-otp-digit"
                                   maxlength="1"
                                   inputmode="numeric"
                                   pattern="[0-9]*"
                                   autocomplete="one-time-code"
                                   aria-label="Digit OTP {{ $i + 1 }}"
                                   data-otp-index="{{ $i }}">
                        @endfor
                    </div>
                    <p class="cx-modal-error" id="otpErrorMsg" hidden></p>
                    <input type="hidden" id="guestOtpCode">

                    <div class="cx-modal-actions">
                        <button type="button" class="cx-modal-btn cx-modal-btn-ghost" id="btnBackToEmail">
                            Kembali
                        </button>
                        <button type="button" class="cx-modal-btn cx-modal-btn-primary" id="btnVerifyOtp">
                            Verifikasi & Lanjut
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .cx-checkout-modal {
        max-width: 440px;
    }

    .cx-modal-shell {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 24px 60px rgba(26, 25, 83, 0.22);
    }

    .cx-modal-header {
        position: relative;
        text-align: center;
        padding: 28px 28px 20px;
        background: linear-gradient(160deg, #1A1953 0%, #2a2880 100%);
        color: #fff;
    }

    .cx-modal-close {
        position: absolute;
        top: 16px;
        right: 16px;
        filter: invert(1);
        opacity: 0.85;
    }

    .cx-modal-icon {
        width: 52px;
        height: 52px;
        margin: 0 auto 12px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.14);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
    }

    .cx-modal-title {
        font-size: 1.25rem;
        font-weight: 800;
        margin: 0 0 4px;
        color: #fff;
    }

    .cx-modal-subtitle {
        font-size: 0.82rem;
        color: rgba(255, 255, 255, 0.72);
        margin: 0 0 18px;
    }

    .cx-modal-steps {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 999px;
        padding: 6px 14px;
    }

    .cx-modal-step {
        font-size: 0.72rem;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.55);
        transition: color 0.2s ease;
    }

    .cx-modal-step.is-active {
        color: #fff;
    }

    .cx-modal-step-divider {
        width: 20px;
        height: 2px;
        background: rgba(255, 255, 255, 0.25);
        border-radius: 999px;
    }

    .cx-modal-body {
        padding: 24px 28px 28px;
        background: #fff;
    }

    .cx-modal-label {
        font-size: 0.78rem;
        font-weight: 700;
        color: #8a93a6;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 10px;
    }

    .cx-modal-email-box {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        background: #f4f6fa;
        border: 1px solid rgba(26, 25, 83, 0.1);
        border-radius: 12px;
        margin-bottom: 12px;
    }

    .cx-modal-email-icon {
        font-size: 1.25rem;
        color: #1A1953;
        flex-shrink: 0;
    }

    .cx-modal-email-box span {
        font-size: 1rem;
        font-weight: 800;
        color: #1A1953;
        word-break: break-all;
        text-align: left;
    }

    .cx-modal-hint {
        font-size: 0.82rem;
        color: #8a93a6;
        line-height: 1.55;
        margin-bottom: 22px;
    }

    .cx-modal-info {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        padding: 14px 16px;
        background: rgba(26, 25, 83, 0.06);
        border-radius: 12px;
        margin-bottom: 22px;
    }

    .cx-modal-info iconify-icon {
        font-size: 1.2rem;
        color: #1A1953;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .cx-modal-info p {
        margin: 0;
        font-size: 0.84rem;
        color: #5c6478;
        line-height: 1.55;
        text-align: left;
    }

    .cx-otp-group {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-bottom: 24px;
    }

    .cx-otp-digit {
        width: 46px;
        height: 54px;
        border: 2px solid #dfe4ec;
        border-radius: 12px;
        text-align: center;
        font-size: 1.35rem;
        font-weight: 800;
        color: #1A1953;
        background: #fafbfc;
        transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
        padding: 0;
    }

    .cx-otp-digit:focus {
        outline: none;
        border-color: #1A1953;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(26, 25, 83, 0.12);
    }

    .cx-otp-digit.filled {
        border-color: #1A1953;
        background: #fff;
    }

    .cx-otp-digit.is-error {
        border-color: #dc3545;
        animation: cxOtpShake 0.35s ease;
    }

    .cx-modal-error {
        margin: -12px 0 18px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #dc3545;
        text-align: center;
    }

    @keyframes cxOtpShake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-4px); }
        75% { transform: translateX(4px); }
    }

    .cx-modal-actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .cx-modal-btn {
        width: 100%;
        border: none;
        border-radius: 12px;
        padding: 13px 18px;
        font-size: 0.92rem;
        font-weight: 700;
        transition: all 0.18s ease;
        white-space: nowrap;
    }

    .cx-modal-btn-primary {
        background: #1A1953;
        color: #fff;
    }

    .cx-modal-btn-primary:hover:not(:disabled) {
        background: #14123e;
        color: #fff;
    }

    .cx-modal-btn-primary:disabled {
        opacity: 0.65;
    }

    .cx-modal-btn-ghost {
        background: #f4f6fa;
        color: #1A1953;
        border: 1px solid rgba(26, 25, 83, 0.12);
    }

    .cx-modal-btn-ghost:hover {
        background: #e9edf3;
        color: #1A1953;
    }

    @media (max-width: 420px) {
        .cx-modal-body,
        .cx-modal-header {
            padding-left: 20px;
            padding-right: 20px;
        }

        .cx-otp-digit {
            width: 42px;
            height: 50px;
            font-size: 1.2rem;
        }

        .cx-otp-group {
            gap: 6px;
        }
    }
</style>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.socket.io/4.5.4/socket.io.min.js"></script>
<script>
    const scheduleId = {{ $schedule->id }};
    const ticketPrice = {{ $schedule->ticket_price }};
    const isAuthenticated = {{ $isAuthenticated ? 'true' : 'false' }};
    const rememberSeatsUrl = @json(route('booking.remember-seats', $schedule));
    const serverRestoredSeats = @json($restoredSeats ?? []);
    let selectedSeats = [];
    let appliedDiscount = 0;
    let promoApplied = false;
    let skipOtpCheck = false;

    function setModalStep(step) {
        document.querySelectorAll('.cx-modal-step').forEach(el => {
            el.classList.toggle('is-active', el.dataset.step === String(step));
        });
    }

    function getOtpDigits() {
        return Array.from(document.querySelectorAll('.cx-otp-digit'));
    }

    function getOtpValue() {
        return getOtpDigits().map(input => input.value.trim()).join('');
    }

    function syncHiddenOtp() {
        const hidden = document.getElementById('guestOtpCode');
        if (hidden) hidden.value = getOtpValue();
    }

    function resetOtpInputs() {
        getOtpDigits().forEach(input => {
            input.value = '';
            input.classList.remove('filled', 'is-error');
        });
        syncHiddenOtp();
        const err = document.getElementById('otpErrorMsg');
        if (err) {
            err.textContent = '';
            err.hidden = true;
        }
    }

    function showOtpError(message) {
        const err = document.getElementById('otpErrorMsg');
        if (err) {
            err.textContent = message;
            err.hidden = !message;
        }
        getOtpDigits().forEach(input => input.classList.add('is-error'));
    }

    function focusOtpDigit(index) {
        const digits = getOtpDigits();
        if (digits[index]) digits[index].focus();
    }

    function initOtpInputs() {
        const digits = getOtpDigits();
        if (!digits.length) return;

        digits.forEach((input, index) => {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(-1);
                this.classList.toggle('filled', this.value !== '');
                this.classList.remove('is-error');
                syncHiddenOtp();

                if (this.value && index < digits.length - 1) {
                    focusOtpDigit(index + 1);
                }
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value && index > 0) {
                    focusOtpDigit(index - 1);
                }
            });

            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pasted = (e.clipboardData.getData('text') || '').replace(/\D/g, '').slice(0, 6);
                pasted.split('').forEach((char, i) => {
                    if (digits[i]) {
                        digits[i].value = char;
                        digits[i].classList.toggle('filled', char !== '');
                    }
                });
                syncHiddenOtp();
                focusOtpDigit(Math.min(pasted.length, digits.length - 1));
            });
        });
    }

    function populateSeatIds() {
        const container = document.getElementById('seatIdsContainer');
        if (!container) return;
        container.innerHTML = selectedSeats.map(s =>
            `<input type="hidden" name="seat_ids[]" value="${s.id}">`
        ).join('');
    }

    function setSubmitLoading(isLoading, label) {
        const btn = document.getElementById('bookingBtn');
        if (!btn) return;
        if (isLoading) {
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> ${label || 'Memproses...'}`;
        }
    }

    function showGuestOtpModal() {
        const guestEmail = document.getElementById('guestEmail');
        const email = guestEmail ? guestEmail.value.trim() : '';
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            if (guestEmail) { guestEmail.classList.add('is-invalid'); guestEmail.focus(); }
            return false;
        }
        if (guestEmail) guestEmail.classList.remove('is-invalid');
        document.getElementById('modalEmailDisplay').textContent = email;
        document.getElementById('step-otp-input').style.display = 'none';
        document.getElementById('step-email-confirm').style.display = 'block';
        setModalStep(1);
        resetOtpInputs();
        const emailModalEl = document.getElementById('emailConfirmModal');
        if (emailModalEl && typeof bootstrap !== 'undefined') {
            bootstrap.Modal.getOrCreateInstance(emailModalEl).show();
        }
        return true;
    }

    function performFormSubmit() {
        if (selectedSeats.length === 0) {
            alert('Silakan pilih minimal 1 kursi terlebih dahulu.');
            return;
        }
        populateSeatIds();
        skipOtpCheck = true;
        setSubmitLoading(true);
        document.getElementById('bookingForm')?.requestSubmit();
    }

    // Fungsi utama submit booking - dipanggil langsung dari onclick tombol
    function submitBooking() {
        if (selectedSeats.length === 0) {
            alert('Silakan pilih minimal 1 kursi terlebih dahulu.');
            return;
        }

        if (!isAuthenticated) {
            showGuestOtpModal();
            return;
        }

        performFormSubmit();
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
        initOtpInputs();
        initFormSubmit();
        initAuthRedirectLinks();
    });

    function initAuthRedirectLinks() {
        document.querySelectorAll('a[href*="/login"], a[href*="/register"]').forEach(link => {
            link.addEventListener('click', function(e) {
                if (selectedSeats.length === 0) {
                    return;
                }

                e.preventDefault();
                const targetUrl = this.href;
                persistSeatSelection().finally(() => {
                    window.location.href = targetUrl;
                });
            });
        });
    }

    function persistSeatSelection() {
        persistSeatSelectionToStorage();

        if (selectedSeats.length === 0) {
            return Promise.resolve();
        }

        return fetch(rememberSeatsUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                seat_ids: selectedSeats.map(seat => Number(seat.id)),
            }),
        }).catch(error => {
            console.error('Failed to persist seats on server:', error);
        });
    }

    function persistSeatSelectionToStorage() {
        sessionStorage.setItem('selected_seats_' + scheduleId, JSON.stringify(selectedSeats));
    }

    function restoreSelectedSeats() {
        let seatsToRestore = [];

        if (serverRestoredSeats.length > 0) {
            seatsToRestore = serverRestoredSeats;
        } else {
            const saved = sessionStorage.getItem('selected_seats_' + scheduleId);
            if (saved) {
                try {
                    seatsToRestore = JSON.parse(saved);
                } catch (e) {
                    console.error('Failed to parse saved seats:', e);
                }
            }
        }

        if (!seatsToRestore || seatsToRestore.length === 0) {
            return;
        }

        selectedSeats = [];
        seatsToRestore.forEach(seat => {
            const seatId = Number(seat.id);
            const btn = document.querySelector(`[data-seat-id="${seatId}"]`);
            if (btn && !btn.disabled) {
                btn.classList.add('seat-selected');
                selectedSeats.push({
                    id: seatId,
                    code: seat.code || btn.getAttribute('data-seat-code') || '',
                });
            }
        });

        persistSeatSelectionToStorage();
        updateSummary();
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
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Gagal mengirim OTP');
                }
                return data;
            })
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = 'Kirim Kode OTP';

                if (data.success) {
                    document.getElementById('step-email-confirm').style.display = 'none';
                    document.getElementById('step-otp-input').style.display = 'block';
                    setModalStep(2);
                    resetOtpInputs();
                    setTimeout(() => focusOtpDigit(0), 300);
                } else {
                    alert(data.message || 'Gagal mengirim OTP');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.disabled = false;
                btn.innerHTML = 'Kirim Kode OTP';
                alert('Terjadi kesalahan sistem saat mengirim OTP.');
            });
        });

        document.getElementById('btnBackToEmail')?.addEventListener('click', function() {
            document.getElementById('step-otp-input').style.display = 'none';
            document.getElementById('step-email-confirm').style.display = 'block';
            setModalStep(1);
            resetOtpInputs();
        });

        document.getElementById('btnVerifyOtp')?.addEventListener('click', function() {
            const email = guestEmail.value.trim();
            const otp = getOtpValue();
            const btn = this;

            if (!otp || otp.length !== 6) {
                showOtpError('Masukkan 6 digit kode OTP.');
                focusOtpDigit(0);
                return;
            }

            const err = document.getElementById('otpErrorMsg');
            if (err) { err.textContent = ''; err.hidden = true; }
            getOtpDigits().forEach(input => input.classList.remove('is-error'));

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
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Verifikasi OTP gagal.');
                }
                return data;
            })
            .then(data => {
                if (data.success) {
                    if (emailModal) emailModal.hide();
                    setSubmitLoading(true, 'Mengunci Kursi...');
                    performFormSubmit();
                } else {
                    btn.disabled = false;
                    btn.innerHTML = 'Verifikasi & Lanjut';
                    showOtpError(data.message || 'Kode OTP salah. Coba lagi.');
                    focusOtpDigit(0);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.disabled = false;
                btn.innerHTML = 'Verifikasi & Lanjut';
                showOtpError(error.message || 'Terjadi kesalahan saat memverifikasi OTP.');
                focusOtpDigit(0);
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
            selectedSeats.push({ id: Number(seatId), code: seatCode });
            btn.classList.add('seat-selected');
        }

        persistSeatSelectionToStorage();
        persistSeatSelection();

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

        bookingForm.addEventListener('submit', function(e) {
            if (selectedSeats.length === 0) {
                e.preventDefault();
                alert('Silakan pilih minimal 1 kursi terlebih dahulu.');
                return;
            }

            populateSeatIds();

            if (!isAuthenticated && !skipOtpCheck) {
                e.preventDefault();
                showGuestOtpModal();
                return;
            }

            skipOtpCheck = false;
            setSubmitLoading(true);
        });
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
