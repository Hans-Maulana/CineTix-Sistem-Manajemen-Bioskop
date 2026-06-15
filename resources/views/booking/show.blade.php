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


        </div>

        <div class="col-md-8">
            <div class="cx-booking-hero" data-aos="fade-up">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div>
                        <span class="badge bg-white text-dark rounded-pill mb-2 px-3 py-2">
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
                            <label class="form-label fw-bold text-dark small">Kode Promo (Opsional)</label>

                            {{-- Selected promo chip (hidden until promo applied) --}}
                            <div id="selectedPromoChip" class="d-none mb-2">
                                <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3" style="background:rgba(26,25,83,.07);border:1.5px solid #1A1953;">
                                    <iconify-icon icon="lucide:tag" style="color:#1A1953;font-size:1rem;"></iconify-icon>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold text-dark small" id="selectedPromoCode">—</div>
                                        <div class="text-muted" style="font-size:.72rem;" id="selectedPromoDesc"></div>
                                    </div>
                                    <button type="button" class="btn-close" style="font-size:.6rem;" id="removePromoBtn" title="Hapus promo"></button>
                                </div>
                            </div>

                            {{-- Picker button --}}
                            <button type="button" class="w-100 d-flex align-items-center justify-content-between px-3 py-2 rounded-3 border fw-semibold" id="openPromoModal"
                                    style="background:#fafbfc;border-color:rgba(26,25,83,.15) !important;color:#5c6478;font-size:.88rem;transition:all .18s ease;">
                                <span>
                                    <iconify-icon icon="lucide:ticket-percent" class="me-2" style="color:#1A1953;"></iconify-icon>
                                    <span id="promoPickerLabel">Pilih Kode Promo</span>
                                </span>
                                <iconify-icon icon="lucide:chevron-right" style="color:#aaa;"></iconify-icon>
                            </button>

                            {{-- hidden input untuk submit form --}}
                            <input type="hidden" id="promoCode" name="promo_code">

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

    /* ===== PROMO PICKER CARD ===== */
    .promo-card {
        border: 1.5px dashed rgba(26,25,83,.18);
        border-radius: 14px;
        padding: 14px 16px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all .18s ease;
        background: #fafbfc;
        position: relative;
        overflow: hidden;
    }
    .promo-card::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 4px;
        background: linear-gradient(180deg,#1A1953,#2d2b7a);
        border-radius: 14px 0 0 14px;
        opacity: 0;
        transition: opacity .18s ease;
    }
    .promo-card:hover { border-color: #1A1953; background: #fff; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(26,25,83,.1); }
    .promo-card:hover::before { opacity: 1; }
    .promo-card--selected { border-color: #1A1953 !important; background: #f0f1ff !important; }
    .promo-card--selected::before { opacity: 1; }
    .promo-icon {
        width: 30px; height: 30px;
        background: rgba(26,25,83,.08);
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        color: #1A1953; font-size: .95rem;
        flex-shrink: 0;
    }
    .promo-code-text { font-weight: 800; font-size: .95rem; color: #1A1953; letter-spacing: .02em; }
    .promo-discount-badge {
        background: linear-gradient(135deg,#1A1953,#2d2b7a);
        color: #fff; border-radius: 50px;
        padding: 3px 10px; font-size: .72rem; font-weight: 800;
        white-space: nowrap;
    }
    .promo-desc { font-size: .8rem; color: #6c7489; margin: 6px 0 0; line-height: 1.45; }
    .promo-savings { font-size: .8rem; color: #15864c; }
    .promo-expiry { font-size: .72rem; color: #a0aab8; }
    .promo-selected-check {
        display: inline-flex; align-items: center; gap: 5px;
        margin-top: 8px; font-size: .78rem; font-weight: 700; color: #1A1953;
    }
    #openPromoModal:hover { background: #f0f1ff !important; border-color: #1A1953 !important; color: #1A1953 !important; }
</style>

{{-- ===== PROMO PICKER MODAL ===== --}}
<div class="modal fade" id="promoPickerModal" tabindex="-1" aria-labelledby="promoPickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width:480px;">
        <div class="modal-content" style="border:none;border-radius:20px;overflow:hidden;box-shadow:0 24px 60px rgba(26,25,83,.22);">
            {{-- Header --}}
            <div style="background:linear-gradient(160deg,#1A1953 0%,#2a2880 100%);padding:24px 24px 18px;color:#fff;position:relative;">
                <button type="button" class="btn-close position-absolute" style="top:14px;right:14px;filter:invert(1);opacity:.85;" data-bs-dismiss="modal"></button>
                <div style="width:48px;height:48px;background:rgba(255,255,255,.14);border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin-bottom:10px;">
                    <iconify-icon icon="lucide:ticket-percent"></iconify-icon>
                </div>
                <h5 style="margin:0 0 4px;font-weight:800;font-size:1.15rem;">Pilih Kode Promo</h5>
                <p style="font-size:.8rem;color:rgba(255,255,255,.7);margin:0;">Klik promo untuk langsung menerapkan diskon</p>
            </div>

            {{-- Search --}}
            <div style="padding:14px 20px 0;background:#fff;">
                <div style="position:relative;">
                    <iconify-icon icon="lucide:search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#aaa;font-size:.95rem;"></iconify-icon>
                    <input type="text" id="promoSearchInput" placeholder="Cari kode promo..."
                           style="width:100%;padding:10px 12px 10px 36px;border:1.5px solid rgba(26,25,83,.12);border-radius:10px;font-size:.85rem;outline:none;background:#fafbfc;"
                           oninput="filterPromoList(this.value)">
                </div>
            </div>

            {{-- Body / List --}}
            <div class="modal-body" style="padding:14px 20px 20px;background:#fff;max-height:360px;overflow-y:auto;">
                {{-- Loading state --}}
                <div id="promoLoadingState" class="text-center py-4">
                    <div class="spinner-border text-primary spinner-border-sm mb-2"></div>
                    <div class="text-muted small">Memuat promo...</div>
                </div>

                {{-- Empty state --}}
                <div id="promoEmptyState" class="text-center py-4 d-none">
                    <iconify-icon icon="lucide:tag-off" style="font-size:2.5rem;color:#c5cad6;"></iconify-icon>
                    <p class="text-muted small mb-0 mt-2">Tidak ada promo tersedia saat ini.</p>
                </div>

                {{-- Promo list --}}
                <div id="promoListContainer"></div>
            </div>
        </div>
    </div>
</div>

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

    // ─── PROMO PICKER MODAL ───────────────────────────────────────────────────

    let allPromos = [];        // cache promo list
    let promoModalInstance = null;

    function initPromoHandler() {
        const openBtn = document.getElementById('openPromoModal');
        if (!openBtn) return;

        const modalEl = document.getElementById('promoPickerModal');
        if (modalEl && typeof bootstrap !== 'undefined') {
            promoModalInstance = new bootstrap.Modal(modalEl);
        }

        // Open modal + fetch promos
        openBtn.addEventListener('click', () => {
            if (promoModalInstance) promoModalInstance.show();
            fetchAvailablePromos();
        });

        // Remove promo button
        document.getElementById('removePromoBtn')?.addEventListener('click', () => {
            clearPromo();
        });
    }

    function fetchAvailablePromos() {
        showPromoLoading(true);
        const url = new URL('{{ route('promo.available') }}', location.origin);
        url.searchParams.set('ticket_price', ticketPrice);
        url.searchParams.set('seat_count', Math.max(1, selectedSeats.length));

        fetch(url, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(r => r.json())
        .then(data => {
            allPromos = data.promos || [];
            showPromoLoading(false);
            renderPromoList(allPromos);
        })
        .catch(() => {
            showPromoLoading(false);
            renderPromoList([]);
        });
    }

    function showPromoLoading(loading) {
        document.getElementById('promoLoadingState').classList.toggle('d-none', !loading);
        document.getElementById('promoListContainer').classList.toggle('d-none', loading);
    }

    function renderPromoList(promos) {
        const container = document.getElementById('promoListContainer');
        const emptyState = document.getElementById('promoEmptyState');

        if (!promos.length) {
            emptyState.classList.remove('d-none');
            container.innerHTML = '';
            return;
        }
        emptyState.classList.add('d-none');

        const activeCode = document.getElementById('promoCode').value;

        container.innerHTML = promos.map(p => {
            const isSelected = p.code === activeCode;
            const savingsText = p.savings > 0
                ? `Hemat <strong>Rp ${p.savings.toLocaleString('id-ID')}</strong>`
                : `Diskon <strong>${p.discount_label}</strong>`;

            return `
            <div class="promo-card${isSelected ? ' promo-card--selected' : ''}"
                 onclick="selectPromo(${JSON.stringify(p).replace(/"/g, '&quot;')})"
                 data-code="${p.code}">

                <div class="d-flex align-items-center justify-content-between mb-1">
                    <div class="d-flex align-items-center gap-2">
                        <div class="promo-icon">
                            <iconify-icon icon="lucide:tag"></iconify-icon>
                        </div>
                        <span class="promo-code-text">${p.code}</span>
                    </div>
                    <div class="promo-discount-badge">${p.discount_label} OFF</div>
                </div>

                ${p.description ? `<p class="promo-desc">${p.description}</p>` : ''}

                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="promo-savings">${savingsText}</span>
                    <span class="promo-expiry">Berlaku hingga ${p.valid_until}</span>
                </div>

                ${isSelected ? '<div class="promo-selected-check"><iconify-icon icon="lucide:check-circle-2"></iconify-icon> Diterapkan</div>' : ''}
            </div>`;
        }).join('');
    }

    function filterPromoList(query) {
        const q = query.toLowerCase();
        const filtered = q
            ? allPromos.filter(p =>
                p.code.toLowerCase().includes(q) ||
                (p.description || '').toLowerCase().includes(q)
              )
            : allPromos;
        renderPromoList(filtered);
    }

    function selectPromo(promo) {
        // Apply promo
        window.activePromo = {
            code: promo.code,
            discount_type: promo.discount_type,
            discount_value: promo.discount_value,
        };
        promoApplied = true;

        document.getElementById('promoCode').value = promo.code;
        document.getElementById('promoPickerLabel').textContent = promo.code;

        // Show selected chip
        const chip = document.getElementById('selectedPromoChip');
        chip.classList.remove('d-none');
        document.getElementById('selectedPromoCode').textContent = promo.code + ' — ' + promo.discount_label + ' OFF';
        document.getElementById('selectedPromoDesc').textContent = promo.description || '';

        // Success message
        const savings = calculateDiscount(ticketPrice * Math.max(1, selectedSeats.length));
        document.getElementById('promoMessage').innerHTML =
            `<span class="text-success fw-semibold">✓ Promo ${promo.code} diterapkan${savings > 0 ? '. Hemat Rp ' + savings.toLocaleString('id-ID') : ''}!</span>`;

        updateSummary();

        // Close modal
        if (promoModalInstance) promoModalInstance.hide();
    }

    function clearPromo() {
        window.activePromo = null;
        promoApplied = false;
        appliedDiscount = 0;

        document.getElementById('promoCode').value = '';
        document.getElementById('promoPickerLabel').textContent = 'Pilih Kode Promo';
        document.getElementById('selectedPromoChip').classList.add('d-none');
        document.getElementById('promoMessage').innerHTML = '';

        updateSummary();
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
