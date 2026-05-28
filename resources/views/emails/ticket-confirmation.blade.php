<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tiket CineTix</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2 style="color: #1A1953;">Pembayaran Berhasil — Tiket Anda Siap!</h2>

    @php
        $ticket = $booking->ticketBookings->first();
        $film = $ticket?->schedule?->film;
        $schedule = $ticket?->schedule;
        $seats = $booking->ticketBookings->map(fn ($t) => $t->seat->seat_code)->implode(', ');
    @endphp

    <p>Terima kasih telah memesan di <strong>CineTix</strong>.</p>

    <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
        <tr><td style="padding: 8px 0;"><strong>Film</strong></td><td>{{ $film?->title ?? '-' }}</td></tr>
        <tr><td style="padding: 8px 0;"><strong>Tanggal</strong></td><td>{{ $schedule?->schedule_date?->format('d M Y') ?? '-' }}</td></tr>
        <tr><td style="padding: 8px 0;"><strong>Jam</strong></td><td>{{ $schedule?->start_time?->format('H:i') ?? '-' }}</td></tr>
        <tr><td style="padding: 8px 0;"><strong>Studio</strong></td><td>{{ $schedule?->studio?->name ?? '-' }}</td></tr>
        <tr><td style="padding: 8px 0;"><strong>Kursi</strong></td><td>{{ $seats }}</td></tr>
        <tr><td style="padding: 8px 0;"><strong>ID Booking</strong></td><td style="font-family: monospace;">{{ $booking->qr_redeem }}</td></tr>
        <tr><td style="padding: 8px 0;"><strong>Total</strong></td><td>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</td></tr>
    </table>

    <p>Tunjukkan kode QR di bioskop saat masuk. Simpan email ini sebagai bukti pemesanan.</p>

    <p style="margin: 24px 0;">
        <a href="{{ $ticketUrl }}" style="background: #1A1953; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold;">
            Lihat Tiket Online
        </a>
    </p>

    <p style="font-size: 12px; color: #666;">Email ini dikirim ke {{ $booking->guest_email }}. Jika Anda tidak melakukan pemesanan, abaikan email ini.</p>
</body>
</html>
