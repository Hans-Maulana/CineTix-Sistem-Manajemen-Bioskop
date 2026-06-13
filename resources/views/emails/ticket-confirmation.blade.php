<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tiket CineTix</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background: #f4f6fa;">
    <div style="background:#fff;border-radius:12px;padding:24px;border:1px solid #e6e8f0;">
        <h2 style="color: #1A1953; margin-top:0;">Pembayaran Berhasil — Tiket Anda Siap!</h2>

        @php
            $ticket = $booking->ticketBookings->first();
            $film = $ticket?->schedule?->film;
            $schedule = $ticket?->schedule;
            $seats = $booking->ticketBookings
                ->map(fn ($t) => $t->seat?->seat_code)
                ->filter()
                ->implode(', ');
        @endphp

        <p>Terima kasih telah memesan di <strong>CineTix</strong>.</p>

        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr><td style="padding: 8px 0; color:#666;">Film</td><td style="padding: 8px 0;"><strong>{{ $film?->title ?? '-' }}</strong></td></tr>
            <tr><td style="padding: 8px 0; color:#666;">Tanggal</td><td style="padding: 8px 0;">{{ $schedule?->schedule_date?->format('d M Y') ?? '-' }}</td></tr>
            <tr><td style="padding: 8px 0; color:#666;">Jam</td><td style="padding: 8px 0;">{{ $schedule?->start_time?->format('H:i') ?? '-' }}</td></tr>
            <tr><td style="padding: 8px 0; color:#666;">Studio</td><td style="padding: 8px 0;">{{ $schedule?->studio?->name ?? '-' }}</td></tr>
            <tr><td style="padding: 8px 0; color:#666;">Kursi</td><td style="padding: 8px 0;"><strong>{{ $seats ?: '-' }}</strong></td></tr>
            <tr><td style="padding: 8px 0; color:#666;">ID Booking</td><td style="padding: 8px 0; font-family: monospace;">{{ $booking->qr_redeem }}</td></tr>
            <tr><td style="padding: 8px 0; color:#666;">Total</td><td style="padding: 8px 0;">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</td></tr>
        </table>

        <div style="text-align:center;margin:24px 0;">
            <img src="{{ $qrUrl }}" alt="QR Code Tiket" width="160" height="160" style="border:1px solid #e6e8f0;border-radius:8px;">
            <p style="font-size:12px;color:#666;margin:8px 0 0;">Tunjukkan QR code ini di pintu masuk bioskop</p>
        </div>

        <p style="margin: 24px 0;">
            <a href="{{ $ticketUrl }}" style="background: #1A1953; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold; display:inline-block;">
                Lihat Tiket Online
            </a>
        </p>

        <p style="font-size: 12px; color: #666; margin-bottom:0;">
            Email ini dikirim ke <strong>{{ $recipientEmail }}</strong>.
            Jika tidak ada di inbox, periksa folder spam/promosi.
        </p>
    </div>
</body>
</html>
