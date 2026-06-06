<!DOCTYPE html>
<html>
<head>
    <title>Kode OTP CineTix</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <div style="max-width: 500px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
        <h2 style="text-align: center; color: #1a1843;">Verifikasi Email Guest Checkout</h2>
        <p>Halo pelanggan setia CineTix,</p>
        <p>Gunakan kode OTP berikut untuk melanjutkan proses pembelian tiket Anda. Kode ini berlaku selama 5 menit.</p>

        <div style="text-align: center; margin: 30px 0;">
            <span style="font-size: 32px; font-weight: bold; letter-spacing: 5px; background: #f4f4f4; padding: 10px 20px; border-radius: 5px;">{{ $otpCode }}</span>
        </div>

        <p style="font-size: 12px; color: #777;">Jika Anda tidak merasa melakukan transaksi di CineTix, abaikan email ini.</p>
    </div>
</body>
</html>
