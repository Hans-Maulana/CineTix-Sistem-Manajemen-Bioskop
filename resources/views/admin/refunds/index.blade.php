@extends('layouts.admin')

@section('title', 'Manajemen Refund')

@push('styles')
<style>
  .rfnd-admin-hero {
    background: linear-gradient(135deg, #1A1953 0%, #2d2b7a 100%);
    border-radius: 20px;
    padding: 24px 28px;
    color: #fff;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
  }
  .rfnd-admin-hero::after {
    content: '';
    position: absolute;
    right: -40px; top: -40px;
    width: 160px; height: 160px;
    background: rgba(255,255,255,.05);
    border-radius: 50%;
  }

  .rfnd-tab-nav {
    display: flex;
    gap: 8px;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
  }
  .rfnd-tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 18px;
    border-radius: 50px;
    font-size: .84rem;
    font-weight: 700;
    text-decoration: none;
    transition: all .2s ease;
    border: 2px solid transparent;
  }
  .rfnd-tab-btn.active-requested {
    background: #fff3cd; color: #856404; border-color: #ffc107;
  }
  .rfnd-tab-btn.inactive-requested {
    background: #f8f9fb; color: #5c6478; border-color: rgba(26,25,83,.08);
  }
  .rfnd-tab-btn.active-approved {
    background: #d1f0e2; color: #155e35; border-color: #28a745;
  }
  .rfnd-tab-btn.inactive-approved {
    background: #f8f9fb; color: #5c6478; border-color: rgba(26,25,83,.08);
  }
  .rfnd-tab-btn.active-rejected {
    background: #fde8e8; color: #7b2020; border-color: #dc3545;
  }
  .rfnd-tab-btn.inactive-rejected {
    background: #f8f9fb; color: #5c6478; border-color: rgba(26,25,83,.08);
  }

  .rfnd-panel {
    background: #fff;
    border-radius: 18px;
    border: 1px solid rgba(26,25,83,.07);
    box-shadow: 0 8px 24px rgba(26,25,83,.06);
    overflow: hidden;
  }

  .rfnd-table th {
    background: #f8f9fb;
    font-size: .72rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: #8a93a6;
    padding: 14px 16px;
    border-bottom: 1px solid rgba(26,25,83,.06);
    white-space: nowrap;
  }
  .rfnd-table td {
    padding: 16px;
    vertical-align: middle;
    border-bottom: 1px solid rgba(26,25,83,.04);
    font-size: .88rem;
  }
  .rfnd-table tr:last-child td { border-bottom: none; }
  .rfnd-table tr:hover td { background: #faf9ff; }

  .rfnd-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    border-radius: 50px;
    font-size: .74rem;
    font-weight: 800;
  }
  .rfnd-badge.requested { background: #fff3cd; color: #856404; }
  .rfnd-badge.approved  { background: #d1f0e2; color: #155e35; }
  .rfnd-badge.rejected  { background: #fde8e8; color: #7b2020; }

  .btn-approve {
    background: #19a75f; color: #fff; border: none;
    border-radius: 8px; padding: 6px 14px;
    font-size: .8rem; font-weight: 700;
    transition: all .18s ease;
    cursor: pointer;
  }
  .btn-approve:hover { background: #15864c; transform: translateY(-1px); color: #fff; }

  .btn-reject {
    background: #fff; color: #dc3545;
    border: 1.5px solid #dc3545;
    border-radius: 8px; padding: 6px 14px;
    font-size: .8rem; font-weight: 700;
    transition: all .18s ease;
    cursor: pointer;
  }
  .btn-reject:hover { background: #fde8e8; }

  .modal-rfnd .modal-content {
    border: none;
    border-radius: 18px;
    box-shadow: 0 20px 60px rgba(26,25,83,.2);
    overflow: hidden;
  }
  .modal-rfnd .modal-header {
    background: linear-gradient(135deg, #c0392b, #922b21);
    color: #fff; border: none; padding: 22px 24px;
  }
  .modal-rfnd .modal-header .btn-close { filter: invert(1); }
  .modal-rfnd .modal-body { padding: 24px; }

  .reason-preview {
    background: #f8f9fb;
    border: 1px solid rgba(26,25,83,.08);
    border-radius: 10px;
    padding: 14px 16px;
    font-size: .87rem;
    color: #3d4459;
    line-height: 1.6;
    margin-bottom: 16px;
    font-style: italic;
  }
</style>
@endpush

@section('content')

{{-- Flash messages are now handled globally via SweetAlert in layouts.admin --}}

{{-- Hero --}}
<div class="rfnd-admin-hero">
  <div style="position:relative;z-index:2;">
    <h4 class="fw-bold text-white mb-1">
      <i class="bi bi-arrow-counterclockwise me-2"></i>Manajemen Refund
    </h4>
    <p class="text-white-50 mb-0 small">
      Tinjau dan proses pengajuan refund dari customer.
    </p>
  </div>
</div>

{{-- Tab Nav --}}
<div class="rfnd-tab-nav">
  <a href="{{ route('admin.refunds.index', ['status' => 'requested']) }}"
     class="rfnd-tab-btn {{ $filterStatus === 'requested' ? 'active-requested' : 'inactive-requested' }}">
    <i class="bi bi-hourglass-split"></i>
    Menunggu Review
    @if($counts['requested'] > 0)
      <span class="badge bg-warning text-dark rounded-pill">{{ $counts['requested'] }}</span>
    @endif
  </a>
  <a href="{{ route('admin.refunds.index', ['status' => 'approved']) }}"
     class="rfnd-tab-btn {{ $filterStatus === 'approved' ? 'active-approved' : 'inactive-approved' }}">
    <i class="bi bi-check-circle"></i>
    Disetujui
    <span class="badge bg-success rounded-pill">{{ $counts['approved'] }}</span>
  </a>
  <a href="{{ route('admin.refunds.index', ['status' => 'rejected']) }}"
     class="rfnd-tab-btn {{ $filterStatus === 'rejected' ? 'active-rejected' : 'inactive-rejected' }}">
    <i class="bi bi-x-circle"></i>
    Ditolak
    <span class="badge bg-danger rounded-pill">{{ $counts['rejected'] }}</span>
  </a>
</div>

{{-- Table --}}
<div class="rfnd-panel">
  @if($refunds->isEmpty())
    <div class="text-center py-5 text-muted">
      <i class="bi bi-inbox d-block mb-3" style="font-size:2.5rem;opacity:.4;"></i>
      <p class="mb-0 fw-semibold">Tidak ada pengajuan refund
        @if($filterStatus === 'requested') yang menunggu review
        @elseif($filterStatus === 'approved') yang disetujui
        @else yang ditolak
        @endif
      .</p>
    </div>
  @else
    <div class="table-responsive">
      <table class="table rfnd-table mb-0">
        <thead>
          <tr>
            <th>Booking</th>
            <th>Customer</th>
            <th>Film & Jadwal</th>
            <th>Total Bayar</th>
            <th>Dana Kembali</th>
            <th>Alasan</th>
            <th>Diajukan</th>
            @if($filterStatus === 'requested')
              <th>Aksi</th>
            @else
              <th>Diproses</th>
            @endif
          </tr>
        </thead>
        <tbody>
          @foreach($refunds as $booking)
            @php
              $firstTicket = $booking->ticketBookings->first();
              $film = $firstTicket?->schedule?->film;
            @endphp
            <tr>
              {{-- Booking --}}
              <td>
                <div class="fw-bold text-dark">#{{ $booking->id }}</div>
                <div class="rfnd-badge {{ $booking->refund_status }} mt-1">
                  @if($booking->refund_status === 'requested')
                    <i class="bi bi-hourglass-split"></i> Menunggu
                  @elseif($booking->refund_status === 'approved')
                    <i class="bi bi-check-circle"></i> Disetujui
                  @else
                    <i class="bi bi-x-circle"></i> Ditolak
                  @endif
                </div>
              </td>

              {{-- Customer --}}
              <td>
                <div class="fw-bold text-dark small">{{ $booking->customerName() }}</div>
                <div class="text-muted" style="font-size:.75rem;">{{ $booking->customerEmail() }}</div>
              </td>

              {{-- Film --}}
              <td>
                <div class="fw-bold text-dark small">{{ $film?->title ?? '-' }}</div>
                @if($firstTicket?->schedule)
                  <div class="text-muted" style="font-size:.75rem;">
                    {{ $firstTicket->schedule->schedule_date->translatedFormat('d M Y') }},
                    {{ $firstTicket->schedule->start_time->format('H:i') }}
                  </div>
                @endif
              </td>

              {{-- Total --}}
              <td>
                <span class="fw-bold">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
              </td>

              {{-- Refund Amount --}}
              <td>
                <span class="fw-bold text-success">Rp {{ number_format($booking->refund_amount, 0, ',', '.') }}</span>
                <div class="text-muted" style="font-size:.72rem;">-{{ \App\Models\Booking::REFUND_ADMIN_FEE_PERCENT }}% admin fee</div>
              </td>

              {{-- Alasan --}}
              <td>
                <button type="button"
                        class="btn btn-sm btn-outline-secondary rounded-pill"
                        style="font-size:.75rem;"
                        data-bs-toggle="tooltip"
                        title="{{ $booking->refund_reason }}">
                  Lihat Alasan
                </button>
              </td>

              {{-- Diajukan --}}
              <td>
                <div class="small text-muted">{{ $booking->refund_requested_at?->translatedFormat('d M Y') }}</div>
                <div class="text-muted" style="font-size:.72rem;">{{ $booking->refund_requested_at?->diffForHumans() }}</div>
              </td>

              {{-- Aksi --}}
              @if($filterStatus === 'requested')
                <td>
                  <div class="d-flex gap-2">
                    <button type="button"
                            class="btn-approve"
                            onclick="confirmApprove({{ $booking->id }}, '{{ addslashes($booking->customerName()) }}', 'Rp {{ number_format($booking->refund_amount, 0, ',', '.') }}')">
                      <i class="bi bi-check-lg me-1"></i> Setujui
                    </button>
                    <button type="button"
                            class="btn-reject"
                            onclick="openRejectModal({{ $booking->id }}, '{{ addslashes($booking->customerName()) }}', '{{ addslashes($booking->refund_reason) }}')">
                      <i class="bi bi-x-lg me-1"></i> Tolak
                    </button>
                  </div>

                  {{-- Hidden Approve Form --}}
                  <form id="approve-form-{{ $booking->id }}"
                        method="POST"
                        action="{{ route('admin.refunds.approve', $booking) }}"
                        style="display:none;">
                    @csrf
                  </form>
                </td>
              @else
                <td>
                  <div class="small text-muted">
                    {{ $booking->refund_processed_at?->translatedFormat('d M Y H:i') ?? '-' }}
                  </div>
                  @if($booking->refund_status === 'rejected' && $booking->refund_rejection_reason)
                    <div class="text-danger" style="font-size:.72rem; max-width:180px; white-space:normal;">
                      <i class="bi bi-info-circle me-1"></i>{{ Str::limit($booking->refund_rejection_reason, 60) }}
                    </div>
                  @endif
                </td>
              @endif
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    @if($refunds->hasPages())
      <div class="p-4 border-top d-flex justify-content-center">
        {{ $refunds->links() }}
      </div>
    @endif
  @endif
</div>

{{-- Modal Reject --}}
<div class="modal fade modal-rfnd" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:460px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold text-white" id="rejectModalLabel">
          <i class="bi bi-x-circle me-2"></i>Tolak Pengajuan Refund
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted small mb-3">
          Menolak refund untuk: <strong id="rejectCustomerName"></strong>
        </p>
        <p class="mb-2 small text-muted fw-bold text-uppercase" style="letter-spacing:.04em;">Alasan Customer:</p>
        <div class="reason-preview" id="rejectCustomerReason">—</div>

        <form id="rejectForm" method="POST">
          @csrf
          <div class="mb-3">
            <label for="rejectionReason" class="form-label small fw-bold text-dark">
              Alasan Penolakan <span class="text-danger">*</span>
            </label>
            <textarea class="form-control" id="rejectionReason" name="rejection_reason"
                      rows="4" required minlength="5" maxlength="500"
                      placeholder="Berikan alasan mengapa refund ini tidak dapat disetujui..."></textarea>
            <div class="form-text text-end"><span id="rejectCharCount">0</span>/500</div>
          </div>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary flex-fill rounded-3" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger flex-fill rounded-3 fw-bold text-white" id="rejectSubmitBtn">
              <i class="bi bi-x-circle me-1"></i> Konfirmasi Tolak
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // Initialize tooltips
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
    new bootstrap.Tooltip(el, { placement: 'left', trigger: 'click' });
  });

  function confirmApprove(bookingId, customerName, refundAmount) {
    Swal.fire({
      title: 'Setujui Refund?',
      text: `Anda akan menyetujui refund sebesar ${refundAmount} untuk ${customerName}. Kursi akan dikembalikan menjadi tersedia dan email notifikasi akan dikirim.`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#19a75f',
      cancelButtonColor: '#e4e8ef',
      confirmButtonText: 'Ya, Setujui',
      cancelButtonText: '<span style="color:#5c6478">Batal</span>',
      reverseButtons: true,
      customClass: {
        popup: 'rounded-4'
      }
    }).then((result) => {
      if (result.isConfirmed) {
        document.getElementById('approve-form-' + bookingId).submit();
      }
    });
  }

  function openRejectModal(bookingId, customerName, customerReason) {
    document.getElementById('rejectCustomerName').textContent = customerName;
    document.getElementById('rejectCustomerReason').textContent = customerReason || '—';
    document.getElementById('rejectionReason').value = '';
    document.getElementById('rejectCharCount').textContent = '0';
    document.getElementById('rejectForm').action = '/admin/refunds/' + bookingId + '/reject';
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
  }

  document.getElementById('rejectionReason').addEventListener('input', function() {
    document.getElementById('rejectCharCount').textContent = this.value.length;
  });

  document.getElementById('rejectForm').addEventListener('submit', function() {
    const btn = document.getElementById('rejectSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Memproses...';
  });
</script>
@endpush

@endsection
