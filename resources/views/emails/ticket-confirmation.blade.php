<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tiket CineTix</title>
</head>
<body style="font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background: #f0f2f7;">
    <div style="background:#fff;border-radius:18px;overflow:hidden;box-shadow:0 8px 32px rgba(26,25,83,.10); border: 1px solid rgba(26,25,83,.05);">
        <!-- Header -->
        <div style="background:linear-gradient(135deg,#1A1953 0%,#2d2b7a 100%); padding:30px 24px; text-align:center; color:#fff;">
            <h2 style="margin:0 0 10px; font-size: 24px; font-weight:800; letter-spacing:1px;">CINETIX</h2>
            <p style="margin:0; font-size:14px; opacity: 0.8;">Pembayaran Berhasil — Tiket Anda Siap!</p>
        </div>

        @php
            $ticket = $booking->ticketBookings->first();
            $film = $ticket?->schedule?->film;
            $schedule = $ticket?->schedule;
            $seats = $booking->ticketBookings
                ->map(fn ($t) => $t->seat?->seat_code)
                ->filter()
                ->implode(', ');
        @endphp

        <!-- Ticket Body -->
        <div style="padding: 30px;">
            <p style="margin-top:0; font-size: 16px; color: #1f2533;">Terima kasih telah memesan tiket di <strong>CineTix</strong>. Berikut adalah detail e-tiket Anda:</p>
            
            <!-- Ticket Card -->
            <div style="background:#f8f9fb; border-radius:12px; border:1px dashed #c0c5d0; padding:20px; margin: 24px 0;">
                <div style="text-align:center; margin-bottom: 20px;">
                    <h3 style="margin:0; font-size: 20px; color:#1A1953;">{{ $film?->title ?? '-' }}</h3>
                    <p style="margin:5px 0 0; font-size: 14px; color:#666;">Studio {{ $schedule?->studio?->name ?? '-' }}</p>
                </div>
                
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <tr>
                        <td style="padding: 10px 0; border-bottom: 1px solid #e6e8f0; width:50%;">
                            <span style="display:block; font-size:12px; color:#8a93a6; text-transform:uppercase; font-weight:bold;">Tanggal</span>
                            <strong style="font-size: 15px; color:#1f2533;">{{ $schedule?->schedule_date?->translatedFormat('d M Y') ?? '-' }}</strong>
                        </td>
                        <td style="padding: 10px 0; border-bottom: 1px solid #e6e8f0; width:50%; text-align:right;">
                            <span style="display:block; font-size:12px; color:#8a93a6; text-transform:uppercase; font-weight:bold;">Jam Tayang</span>
                            <strong style="font-size: 15px; color:#1f2533;">{{ $schedule?->start_time?->format('H:i') ?? '-' }} WIB</strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; border-bottom: 1px solid #e6e8f0;">
                            <span style="display:block; font-size:12px; color:#8a93a6; text-transform:uppercase; font-weight:bold;">Kursi</span>
                            <strong style="font-size: 15px; color:#1A1953;">{{ $seats ?: '-' }}</strong>
                        </td>
                        <td style="padding: 10px 0; border-bottom: 1px solid #e6e8f0; text-align:right;">
                            <span style="display:block; font-size:12px; color:#8a93a6; text-transform:uppercase; font-weight:bold;">Total Bayar</span>
                            <strong style="font-size: 15px; color:#1f2533;">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</strong>
                        </td>
                    </tr>
                </table>

                <div style="text-align:center; padding-top:10px;">
                    <span style="display:block; font-size:12px; color:#8a93a6; text-transform:uppercase; font-weight:bold; margin-bottom: 10px;">Booking ID: {{ $booking->qr_redeem }}</span>
                    <div style="background: #fff; padding: 10px; display: inline-block; border-radius: 8px; border: 1px solid #e6e8f0;">
                        <img src="{{ $qrUrl }}" alt="QR Code Tiket" width="160" height="160" style="display:block;">
                    </div>
                    <p style="font-size:13px; color:#5c6478; margin:15px 0 0;">
                        Tunjukkan QR code ini ke petugas bioskop untuk ditukar dengan tiket fisik.
                    </p>
                </div>
            </div>

            <!-- Action Button -->
            <div style="text-align:center; margin: 30px 0 10px;">
                <a href="{{ $ticketUrl }}" style="background: linear-gradient(135deg,#1A1953 0%,#2d2b7a 100%); color: #fff; padding: 14px 28px; text-decoration: none; border-radius: 50px; font-weight: 600; display:inline-block; font-size: 15px; box-shadow: 0 4px 15px rgba(26,25,83,0.3);">
                    🎟️ Lihat Tiket Online
                </a>
            </div>
            
            <p style="font-size: 13px; color: #8a93a6; text-align:center; margin-bottom:0; margin-top:20px;">
                Email ini dikirim otomatis ke <strong>{{ $recipientEmail }}</strong>.<br>
                Jika Anda tidak merasa memesan tiket ini, mohon abaikan email ini.
            </p>
        </div>
    </div>
</body>
</html>
