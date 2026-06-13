<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Refund Disetujui — CineTix</title>
<style>
  body { margin:0; padding:0; background:#f0f2f7; font-family:'Segoe UI',Arial,sans-serif; }
  .wrap { max-width:560px; margin:32px auto; background:#fff; border-radius:18px; overflow:hidden; box-shadow:0 8px 32px rgba(26,25,83,.10); }
  .header { background:linear-gradient(135deg,#1A1953 0%,#2d2b7a 100%); padding:36px 32px; text-align:center; }
  .header .icon { width:60px; height:60px; background:rgba(255,255,255,.15); border-radius:50%; display:inline-flex; align-items:center; justify-content:center; margin-bottom:14px; font-size:1.8rem; }
  .header h1 { color:#fff; margin:0 0 6px; font-size:1.5rem; font-weight:800; }
  .header p  { color:rgba(255,255,255,.75); margin:0; font-size:.9rem; }
  .body { padding:32px; }
  .greeting { font-size:1rem; font-weight:700; color:#1f2533; margin-bottom:8px; }
  .msg { font-size:.9rem; color:#5c6478; line-height:1.6; margin-bottom:24px; }
  .card { background:#f8f9fb; border-radius:12px; padding:20px 24px; margin-bottom:20px; border:1px solid rgba(26,25,83,.07); }
  .card-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; }
  .card-row:last-child { margin-bottom:0; }
  .label { font-size:.78rem; color:#8a93a6; font-weight:600; text-transform:uppercase; letter-spacing:.04em; }
  .value { font-size:.92rem; color:#1f2533; font-weight:700; text-align:right; }
  .amount-box { background:linear-gradient(135deg,#19a75f,#15864c); border-radius:12px; padding:18px 24px; text-align:center; margin-bottom:20px; }
  .amount-box .alabel { color:rgba(255,255,255,.8); font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; margin-bottom:4px; }
  .amount-box .aval { color:#fff; font-size:1.8rem; font-weight:800; }
  .note { background:#fff9ed; border-left:4px solid #f0b84a; border-radius:8px; padding:14px 16px; font-size:.82rem; color:#7a5a10; line-height:1.55; margin-bottom:24px; }
  .footer { text-align:center; padding:20px 32px; background:#f8f9fb; font-size:.78rem; color:#a0aab8; border-top:1px solid rgba(26,25,83,.05); }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <div class="icon">✅</div>
    <h1>Refund Disetujui!</h1>
    <p>Pengajuan refund Anda telah dikonfirmasi oleh admin CineTix</p>
  </div>
  <div class="body">
    <p class="greeting">Halo, {{ $booking->customerName() }} 👋</p>
    <p class="msg">
      Kami dengan senang hati memberitahu bahwa pengajuan refund untuk booking tiket Anda telah
      <strong>disetujui</strong>. Dana akan segera diproses dan ditransfer ke rekening Anda.
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
      <div class="alabel">Dana yang Dikembalikan</div>
      <div class="aval">Rp {{ number_format($booking->refund_amount, 0, ',', '.') }}</div>
    </div>

    <div class="note">
      ⏱ <strong>Estimasi transfer:</strong> 3–7 hari kerja. Jika belum diterima lebih dari 7 hari kerja,
      silakan hubungi tim support CineTix.
    </div>

    <p class="msg" style="margin-bottom:0;">
      Terima kasih telah menggunakan CineTix. Semoga sampai jumpa lagi di pertunjukan berikutnya! 🎬
    </p>
  </div>
  <div class="footer">
    © {{ date('Y') }} CineTix — Sistem Manajemen Bioskop<br>
    Email ini dikirim otomatis, mohon jangan membalas email ini.
  </div>
</div>
</body>
</html>
