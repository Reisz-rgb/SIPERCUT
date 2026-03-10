<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: #9E2A2B; padding: 30px; text-align: center; color: white; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p { margin: 4px 0 0; font-size: 13px; opacity: 0.85; }
        .body { padding: 32px 36px; color: #333; }
        .body p { line-height: 1.6; font-size: 14px; }
        .btn { display: inline-block; margin: 24px 0; padding: 14px 32px; background: #9E2A2B; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px; }
        .note { font-size: 12px; color: #888; border-top: 1px solid #eee; padding-top: 16px; margin-top: 24px; }
        .url-text { word-break: break-all; font-size: 12px; color: #555; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SIPERCUT</h1>
            <p>Sistem Informasi Cuti Pegawai</p>
        </div>
        <div class="body">
            <p>Halo, <strong>{{ $userName }}</strong></p>
            <p>Kami menerima permintaan untuk mereset password akun SIPERCUT Anda. Klik tombol di bawah untuk melanjutkan:</p>

            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="btn">Reset Password Saya</a>
            </div>

            <p>Atau salin link berikut ke browser Anda:</p>
            <p class="url-text">{{ $resetUrl }}</p>

            <div class="note">
                <p>Link ini akan <strong>kadaluarsa dalam 1 jam</strong>.</p>
                <p>Jika Anda tidak merasa meminta reset password, abaikan email ini. Akun Anda tetap aman.</p>
            </div>
        </div>
    </div>
</body>
</html>