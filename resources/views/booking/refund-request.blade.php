@extends('layouts.app')

@push('styles')
@include('partials.customer_film_styles')
<style>
  body { background-color: #e4e8ef !important; }

  .rfnd-hero {
    background: linear-gradient(135deg, #1A1953 0%, #2d2b7a 100%);
    border-radius: 20px;
    padding: 28px 32px;
    color: #fff;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
  }
  .rfnd-hero::after {
    content: '';
    position: absolute;
    right: -40px; top: -40px;
    width: 180px; height: 180px;
    background: rgba(255,255,255,.06);
    border-radius: 50%;
  }

  .rfnd-card {
    background: #fff;
    border-radius: 18px;
    border: 1px solid rgba(26,25,83,.08);
    box-shadow: 0 8px 28px rgba(26,25,83,.07);
    overflow: hidden;
  }

  .rfnd-card-header {
    background: #f8f9fb;
    border-bottom: 1px solid rgba(26,25,83,.06);
    padding: 18px 24px;
    font-weight: 800;
    color: #1f2533;
    font-size: 1rem;
  }

  .rfnd-card-body { padding: 24px; }

  .rfnd-info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid rgba(26,25,83,.05);
    font-size: .9rem;
  }
  .rfnd-info-row:last-child { border-bottom: none; padding-bottom: 0; }
  .rfnd-info-label { color: #8a93a6; font-weight: 600; font-size: .78rem; text-transform: uppercase; letter-spacing: .04em; }
  .rfnd-info-value { color: #1f2533; font-weight: 700; text-align: right; }

  .rfnd-policy-box {
    background: #fff9ed;
    border: 1px solid rgba(240,184,74,.35);
    border-left: 4px solid #f0b84a;
    border-radius: 12px;
    padding: 16px 20px;
  }
  .rfnd-policy-box .ptitle {
    font-size: .82rem;
    font-weight: 800;
    color: #8a5800;
    text-transform: uppercase;
    letter-spacing: .05em;
    margin-bottom: 10px;
  }
  .rfnd-policy-box ul {
    margin: 0; padding-left: 1.1rem;
    font-size: .85rem; color: #6b4d00; line-height: 1.7;
  }

  .rfnd-amount-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f0f9f4;
    border-radius: 10px;
    padding: 14px 18px;
    margin-top: 16px;
  }
  .rfnd-amount-label { font-size: .82rem; font-weight: 700; color: #15864c; }
  .rfnd-amount-value { font-size: 1.25rem; font-weight: 800; color: #15864c; }

  .rfnd-textarea {
    border: 1.5px solid rgba(26,25,83,.15);
    border-radius: 12px;
    padding: 14px 16px;
    font-size: .9rem;
    resize: vertical;
    transition: border-color .18s ease, box-shadow .18s ease;
    background: #fafbfc;
    width: 100%;
  }
  .rfnd-textarea:focus {
    outline: none;
    border-color: #1A1953;
    box-shadow: 0 0 0 3px rgba(26,25,83,.1);
    background: #fff;
  }

  .rfnd-char-count { font-size: .75rem; color: #a0aab8; text-align: right; margin-top: 4px; }

  .btn-rfnd-submit {
    background: linear-gradient(135deg, #1A1953 0%, #2d2b7a 100%);
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: 14px 28px;
    font-size: .95rem;
    font-weight: 800;
    width: 100%;
    transition: all .2s ease;
    cursor: pointer;
  }
  .btn-rfnd-submit:hover:not(:disabled) {
    background: linear-gradient(135deg, #14123e 0%, #1A1953 100%);
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(26,25,83,.3);
    color: #fff;
  }
  .btn-rfnd-submit:disabled { opacity: .65; cursor: not-allowed; }

  .btn-rfnd-cancel {
    background: #f4f6fa;
    color: #5c6478;
    border: 1px solid rgba(26,25,83,.12);
    border-radius: 12px;
    padding: 14px 28px;
    font-size: .95rem;
    font-weight: 700;
    width: 100%;
    transition: all .2s ease;
  }
  .btn-rfnd-cancel:hover {
    background: #e9edf3;
    color: #1f2533;
  }
</style>
@endpush

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">

      {{-- Breadcrumb --}}
      <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('landing-page') }}" class="text-primary text-decoration-none">Beranda</a></li>
          <li class="breadcrumb-item"><a href="{{ route('booking.history') }}" class="text-primary text-decoration-none">Riwayat Transaksi</a></li>
          <li class="breadcrumb-item active">Ajukan Refund</li>
        </ol>
      </nav>

      {{-- Hero --}}
      <div class="rfnd-hero mb-4" data-aos="fade-down">
        <div style="position:relative;z-index:2;">
          <span class="badge bg-warning text-dark rounded-pill mb-2 px-3 py-2" style="font-size:.75rem;">
            <iconify-icon icon="lucide:rotate-ccw" class="me-1"></iconify-icon> Pengajuan Refund
          </span>
          <h4 class="text-white fw-bold mb-1">Batalkan & Refund Tiket</h4>
          <p class="text-white-50 mb-0 small">
            Tinjau kebijakan refund sebelum melanjutkan. Pastikan pengajuan Anda sudah benar.
          </p>
        </div>
      </div>

      {{-- Flash errors --}}
      @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4">
          <iconify-icon icon="lucide:alert-triangle" class="me-2"></iconify-icon>
          <ul class="mb-0">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <div class="row g-4" data-aos="fade-up">
        {{-- Kiri: Info Booking --}}
        <div class="col-md-6">
          <div class="rfnd-card">
            <div class="rfnd-card-header">
              <iconify-icon icon="lucide:ticket" class="me-2 text-primary"></iconify-icon>
              Detail Booking
            </div>
            <div class="rfnd-card-body">
              @php
                $firstTicket = $booking->ticketBookings->first();
                $film = $firstTicket?->schedule?->film;
              @endphp

              @if($film)
              <div class="rfnd-info-row">
                <span class="rfnd-info-label">Film</span>
                <span class="rfnd-info-value">{{ $film->title }}</span>
              </div>
              @endif

              @if($firstTicket?->schedule)
              <div class="rfnd-info-row">
                <span class="rfnd-info-label">Tanggal Tayang</span>
                <span class="rfnd-info-value">{{ $firstTicket->schedule->schedule_date->translatedFormat('d M Y') }}</span>
              </div>
              <div class="rfnd-info-row">
                <span class="rfnd-info-label">Jam Tayang</span>
                <span class="rfnd-info-value">{{ $firstTicket->schedule->start_time->format('H:i') }}</span>
              </div>
              <div class="rfnd-info-row">
                <span class="rfnd-info-label">Studio</span>
                <span class="rfnd-info-value">{{ $firstTicket->schedule->studio->name ?? '-' }}</span>
              </div>
              @endif

              <div class="rfnd-info-row">
                <span class="rfnd-info-label">Kursi</span>
                <span class="rfnd-info-value">
                  {{ $booking->ticketBookings->map(fn($tb) => $tb->seat->seat_code ?? '-')->implode(', ') }}
                </span>
              </div>

              <div class="rfnd-info-row">
                <span class="rfnd-info-label">Total Bayar</span>
                <span class="rfnd-info-value">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
              </div>

              <div class="rfnd-info-row">
                <span class="rfnd-info-label">Biaya Admin ({{ \App\Models\Booking::REFUND_ADMIN_FEE_PERCENT }}%)</span>
                <span class="rfnd-info-value text-danger">- Rp {{ number_format($booking->refundAdminFee(), 0, ',', '.') }}</span>
              </div>

              <div class="rfnd-amount-row">
                <span class="rfnd-amount-label">
                  <iconify-icon icon="lucide:wallet" class="me-1"></iconify-icon>
                  Dana Dikembalikan
                </span>
                <span class="rfnd-amount-value">Rp {{ number_format($booking->refundNetAmount(), 0, ',', '.') }}</span>
              </div>
            </div>
          </div>
        </div>

        {{-- Kanan: Kebijakan + Form --}}
        <div class="col-md-6 d-flex flex-column gap-4">
          {{-- Kebijakan Refund --}}
          <div class="rfnd-policy-box">
            <div class="ptitle">
              <iconify-icon icon="lucide:info" class="me-1"></iconify-icon>
              Kebijakan Refund
            </div>
            <ul>
              <li>Refund hanya berlaku <strong>{{ \App\Models\Booking::REFUND_MIN_HOURS_BEFORE }} jam sebelum</strong> jadwal tayang.</li>
              <li>Dikenakan biaya administrasi <strong>{{ \App\Models\Booking::REFUND_ADMIN_FEE_PERCENT }}%</strong> dari total bayar.</li>
              <li>Pengajuan akan ditinjau admin dalam <strong>1×24 jam kerja</strong>.</li>
              <li>Dana ditransfer ke rekening Anda dalam <strong>3–7 hari kerja</strong> setelah disetujui.</li>
              <li>Setiap booking hanya dapat diajukan refund <strong>1 kali</strong>.</li>
            </ul>
          </div>

          {{-- Form Pengajuan --}}
          <div class="rfnd-card flex-grow-1">
            <div class="rfnd-card-header">
              <iconify-icon icon="lucide:file-text" class="me-2 text-primary"></iconify-icon>
              Alasan Pengajuan Refund
            </div>
            <div class="rfnd-card-body">
              <form method="POST" action="{{ route('booking.refund.store', $booking) }}" id="refundForm">
                @csrf
                <div class="mb-3">
                  <label for="refundReason" class="form-label small fw-bold text-dark">
                    Jelaskan alasan Anda mengajukan refund <span class="text-danger">*</span>
                  </label>
                  <textarea
                    class="rfnd-textarea @error('refund_reason') is-invalid @enderror"
                    id="refundReason"
                    name="refund_reason"
                    rows="5"
                    maxlength="1000"
                    placeholder="Contoh: Tidak bisa hadir karena ada keperluan mendadak yang tidak bisa ditinggalkan..."
                    required
                  >{{ old('refund_reason') }}</textarea>
                  <div class="rfnd-char-count">
                    <span id="charCount">0</span>/1000 karakter
                  </div>
                  @error('refund_reason')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                {{-- Konfirmasi --}}
                <div class="form-check mb-4">
                  <input class="form-check-input" type="checkbox" id="agreePolicy" required>
                  <label class="form-check-label small text-muted" for="agreePolicy">
                    Saya memahami dan menyetujui kebijakan refund di atas, termasuk potongan biaya administrasi
                    {{ \App\Models\Booking::REFUND_ADMIN_FEE_PERCENT }}%.
                  </label>
                </div>

                <div class="d-flex flex-column gap-2">
                  <button type="submit" class="btn-rfnd-submit" id="submitBtn">
                    <iconify-icon icon="lucide:send" class="me-2"></iconify-icon>
                    Ajukan Refund Sekarang
                  </button>
                  <a href="{{ route('booking.history') }}" class="btn-rfnd-cancel text-center text-decoration-none">
                    Batalkan, Kembali ke Riwayat
                  </a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    const textarea = document.getElementById('refundReason');
    const charCount = document.getElementById('charCount');
    const agreePolicy = document.getElementById('agreePolicy');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('refundForm');

    function updateCharCount() {
      const len = textarea.value.length;
      charCount.textContent = len;
      charCount.style.color = len >= 900 ? '#dc3545' : '#a0aab8';
    }

    textarea.addEventListener('input', updateCharCount);
    updateCharCount();

    // Custom SweetAlert2 confirm before submit
    form.addEventListener('submit', function(e) {
      e.preventDefault();

      Swal.fire({
        title: 'Konfirmasi Pengajuan',
        text: 'Apakah Anda yakin ingin mengajukan refund? Tindakan ini tidak dapat dibatalkan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1A1953',
        cancelButtonColor: '#e4e8ef',
        confirmButtonText: 'Ya, Ajukan Refund',
        cancelButtonText: '<span style="color:#5c6478">Batal</span>',
        reverseButtons: true,
        customClass: {
          popup: 'rounded-4'
        }
      }).then((result) => {
        if (result.isConfirmed) {
          submitBtn.disabled = true;
          submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Mengirim...';
          form.submit();
        }
      });
    });
  </script>
@endpush
@endsection
