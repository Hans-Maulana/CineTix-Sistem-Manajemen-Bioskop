<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CineTix - Reset Password</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f5fa;
            margin: 0;
            padding: 0;
            color: #1A1953;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f4f5fa;
            padding: 40px 0;
        }
        .email-content {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(26, 25, 83, 0.05);
            overflow: hidden;
        }
        .email-header {
            background-color: #1A1953;
            padding: 30px;
            text-align: center;
        }
        .email-header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            letter-spacing: 1px;
        }
        .email-body {
            padding: 40px 30px;
            line-height: 1.6;
            color: #3a4150;
        }
        .email-body h2 {
            color: #1A1953;
            font-size: 22px;
            margin-top: 0;
            margin-bottom: 20px;
        }
        .btn-reset {
            display: inline-block;
            background-color: #1A1953;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 8px;
            font-weight: bold;
            margin: 24px 0;
            font-size: 16px;
        }
        .email-footer {
            background-color: #f9f9fb;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #8a93a6;
            border-top: 1px solid #eef0f5;
        }
        .trouble-text {
            font-size: 13px;
            color: #5c6478;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px dashed #eef0f5;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-content">
            <div class="email-header">
                <h1>CineTix</h1>
            </div>
            <div class="email-body">
                <h2>Halo, {{ $user->name ?? 'Pengguna CineTix' }}!</h2>
                
                <p>Kami menerima permintaan untuk mereset password akun CineTix Anda. Jangan khawatir, Anda bisa segera mengatur ulang password dengan mengklik tombol di bawah ini:</p>
                
                <div style="text-align: center;">
                    <a href="{{ $url }}" class="btn-reset">Reset Password</a>
                </div>
                
                <p>Link pemulihan password ini hanya akan berlaku selama <strong>60 menit</strong>. Jika Anda merasa tidak pernah meminta pengaturan ulang password, Anda tidak perlu melakukan tindakan apapun dan akun Anda tetap aman.</p>
                
                <p>Terima kasih,<br><strong>Tim CineTix</strong></p>
                
                
            </div>
            <div class="email-footer">
                &copy; {{ date('Y') }} CineTix. Semua Hak Cipta Dilindungi.<br>
                Sistem Manajemen Bioskop Terpadu
            </div>
        </div>
    </div>
</body>
</html>
