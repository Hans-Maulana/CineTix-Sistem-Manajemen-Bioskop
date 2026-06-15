<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Refund Ditolak — CineTix</title>
<style>
  body { margin:0; padding:0; background:#f0f2f7; font-family:'Segoe UI',Arial,sans-serif; }
  .wrap { max-width:600px; margin:32px auto; background:#fff; border-radius:18px; overflow:hidden; box-shadow:0 8px 32px rgba(26,25,83,.10); border: 1px solid rgba(26,25,83,.05); }
  .header { background:linear-gradient(135deg,#c0392b 0%,#922b21 100%); padding:30px 24px; text-align:center; }
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
  .reason-box { background:#fff4f4; border-left:4px solid #e74c3c; border-radius:8px; padding:16px; font-size:.9rem; color:#7b2020; line-height:1.6; margin-bottom:24px; }
  .reason-box strong { display:block; margin-bottom:6px; font-size:.85rem; text-transform:uppercase; letter-spacing:.04em; color:#a93226; }
  .note { background:#eef2ff; border-left:4px solid #1A1953; border-radius:8px; padding:16px; font-size:.9rem; color:#3a3875; line-height:1.5; margin-bottom:24px; }
  .footer { text-align:center; padding:24px 32px; background:#f8f9fb; font-size:.8rem; color:#a0aab8; border-top:1px solid rgba(26,25,83,.05); }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <div class="brand">CINETIX</div>
    <h1>Refund Tidak Disetujui</h1>
    <p>Pengajuan refund Anda tidak dapat kami proses saat ini</p>
  </div>
  <div class="body">
    <p class="greeting">Halo, {{ $booking->customerName() }} 👋</p>
    <p class="msg">
      Kami mohon maaf, pengajuan refund untuk pemesanan tiket Anda <strong>tidak dapat disetujui</strong> setelah dilakukan peninjauan oleh tim admin CineTix.
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
    </div>

    <div class="reason-box">
      <strong>Alasan Penolakan:</strong>
      {{ $reason }}
    </div>

    <div class="note">
      💡 Tiket Anda <strong>tetap aktif</strong> dan masih bisa digunakan sesuai jadwal yang tertera. Jika Anda ingin mengajukan pertanyaan lebih lanjut, silakan hubungi tim support CineTix.
    </div>

    <p class="msg" style="margin-bottom:0;">
      Terima kasih atas pengertian Anda. Sampai jumpa di bioskop! 🎬
    </p>
  </div>
  <div class="footer">
    © {{ date('Y') }} CineTix — Sistem Manajemen Bioskop<br>
    Email ini dikirim otomatis, mohon jangan membalas email ini.
  </div>
</div>
</body>
</html>
