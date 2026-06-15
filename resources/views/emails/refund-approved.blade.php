<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Refund Disetujui — CineTix</title>
<style>
  body { margin:0; padding:0; background:#f0f2f7; font-family:'Segoe UI',Arial,sans-serif; }
  .wrap { max-width:600px; margin:32px auto; background:#fff; border-radius:18px; overflow:hidden; box-shadow:0 8px 32px rgba(26,25,83,.10); border: 1px solid rgba(26,25,83,.05); }
  .header { background:linear-gradient(135deg,#1A1953 0%,#2d2b7a 100%); padding:30px 24px; text-align:center; }
  .header .brand { color:#fff; font-size:24px; font-weight:800; letter-spacing:1px; margin:0 0 16px; }
  .header .icon { width:50px; height:50px; background:rgba(255,255,255,.2); border-radius:50%; display:inline-flex; align-items:center; justify-content:center; margin-bottom:12px; font-size:1.5rem; }
  .header h1 { color:#fff; margin:0 0 8px; font-size:1.4rem; font-weight:700; }
  .header p  { color:rgba(255,255,255,.8); margin:0; font-size:.95rem; }
  .body { padding:32px; }
  .greeting { font-size:1.05rem; font-weight:700; color:#1f2533; margin-top:0; margin-bottom:12px; }
  .msg { font-size:.95rem; color:#5c6478; line-height:1.6; margin-bottom:24px; }
  .card { background:#f8f9fb; border-radius:12px; padding:20px; margin-bottom:24px; border:1px dashed #c0c5d0; }
  .card-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; padding-bottom:12px; border-bottom:1px solid #e6e8f0; }
  .card-row:last-child { margin-bottom:0; padding-bottom:0; border-bottom:none; }
  .label { font-size:.8rem; color:#8a93a6; font-weight:600; text-transform:uppercase; letter-spacing:.03em; }
  .value { font-size:.95rem; color:#1f2533; font-weight:700; text-align:right; }
  .amount-box { background:linear-gradient(135deg,#19a75f,#15864c); border-radius:12px; padding:20px; text-align:center; margin-bottom:24px; box-shadow: 0 4px 15px rgba(25,167,95,.2); }
  .amount-box .alabel { color:rgba(255,255,255,.85); font-size:.8rem; font-weight:600; text-transform:uppercase; letter-spacing:.05em; margin-bottom:6px; }
  .amount-box .aval { color:#fff; font-size:2rem; font-weight:800; }
  .note { background:#fff9ed; border-left:4px solid #f0b84a; border-radius:8px; padding:16px; font-size:.9rem; color:#7a5a10; line-height:1.5; margin-bottom:24px; }
  .footer { text-align:center; padding:24px 32px; background:#f8f9fb; font-size:.8rem; color:#a0aab8; border-top:1px solid rgba(26,25,83,.05); }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <div class="brand">CINETIX</div>
    <h1>Refund Disetujui!</h1>
    <p>Pengajuan refund Anda telah dikonfirmasi oleh tim kami</p>
  </div>
  <div class="body">
    <p class="greeting">Halo, {{ $booking->customerName() }} 👋</p>
    <p class="msg">
      Kami dengan senang hati memberitahu bahwa pengajuan refund untuk pemesanan tiket Anda telah <strong>disetujui</strong>. Dana akan segera diproses dan ditransfer kembali ke rekening Anda.
    </p>

    <div class="card">
      <div class="card-row">
        <span class="label">Booking ID</span>
        <span class="value">#{{ $booking->id }}</span>
      </div>
      @php
        $firstTicket = $booking->ticketBookings->first();
        $film = $firstTicket?->schedule?->film;
      @endphp
      @if($film)
      <div class="card-row">
        <span class="label">Film</span>
        <span class="value">{{ $film->title }}</span>
      </div>
      @endif
      @if($firstTicket?->schedule)
      <div class="card-row">
        <span class="label">Jadwal</span>
        <span class="value">{{ $firstTicket->schedule->schedule_date->translatedFormat('d M Y') }}, {{ $firstTicket->schedule->start_time->format('H:i') }}</span>
      </div>
      @endif
      <div class="card-row">
        <span class="label">Total Bayar</span>
        <span class="value">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
      </div>
      <div class="card-row">
        <span class="label">Potongan Admin ({{ \App\Models\Booking::REFUND_ADMIN_FEE_PERCENT }}%)</span>
        <span class="value" style="color:#dc3545;">- Rp {{ number_format($booking->refundAdminFee(), 0, ',', '.') }}</span>
      </div>
    </div>

    <div class="amount-box">
      <div class="alabel">Total Dana yang Dikembalikan</div>
      <div class="aval">Rp {{ number_format($booking->refund_amount, 0, ',', '.') }}</div>
    </div>

    <div class="note">
      ⏱ <strong>Estimasi proses transfer:</strong> 3–7 hari kerja. Jika dana belum diterima lebih dari 7 hari kerja, silakan hubungi tim support CineTix.
    </div>

    <p class="msg" style="margin-bottom:0;">
      Terima kasih telah menggunakan CineTix. Kami berharap dapat melayani Anda kembali di masa mendatang! 🎬
    </p>
  </div>
  <div class="footer">
    © {{ date('Y') }} CineTix — Sistem Manajemen Bioskop<br>
    Email ini dikirim otomatis, mohon jangan membalas email ini.
  </div>
</div>
</body>
</html>
