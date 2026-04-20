<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - SIPERCUT</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary: #9E2A2B;
            --primary-dark: #781F1F;
            --bg-body: #F1F5F9;
            --text-secondary: #64748B;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: #334155;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Custom Scrollbar (konsisten) */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

        .auth-hero {
            background: linear-gradient(135deg, var(--primary) 0%, #561616 100%);
            min-height: 260px;
            padding: 44px 20px;
            color: #fff;
            border-bottom-left-radius: 30px;
            border-bottom-right-radius: 30px;
            position: relative;
            overflow: hidden;
        }
        .auth-hero::before {
            content: "";
            position: absolute;
            inset: -40px;
            background:
                radial-gradient(800px 260px at 20% 30%, rgba(255,255,255,0.18), transparent 60%),
                radial-gradient(600px 240px at 80% 40%, rgba(255,255,255,0.12), transparent 55%);
            pointer-events: none;
        }

        /* Brand (samakan posisi seperti Landing Page: kiri-atas, tidak ikut max-width container) */
        .landing-brand {
            position: absolute;
            top: 18px;
            left: 32px;
            z-index: 3;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .landing-brand img {
            width: 30px;
            height: 30px;
            object-fit: contain;
            flex: 0 0 auto;
        }
        .landing-brand .brand-title {
            font-weight: 800;
            font-size: 1.05rem;
            letter-spacing: -0.5px;
            line-height: 1;
        }
        .landing-brand .brand-subtitle {
            font-size: 0.68rem;
            opacity: 0.9;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            line-height: 1.1;
        }

        .auth-shell {
            max-width: 430px;
            margin: 0 auto;
            padding: 0 16px 40px 16px;
            margin-top: -90px; /* efek "ngambang" seperti dashboard-container */
            position: relative;
            z-index: 2;
        }

        .auth-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #F1F5F9;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .auth-card-header {
            padding: 22px 22px 18px 22px;
            border-bottom: 1px dashed #E2E8F0;
        }

        .auth-card-body {
            padding: 22px;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #334155;
        }

        .form-control {
            border-radius: 12px;
            border: 1px solid #E2E8F0;
            background-color: #F8FAFC;
            padding: 12px 14px;
        }

        .form-control:focus {
            border-color: rgba(158, 42, 43, 0.55);
            box-shadow: 0 0 0 4px rgba(158, 42, 43, 0.10);
            background-color: #fff;
        }

        .btn-primary-gradient {
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 14px;
            font-weight: 700;
            box-shadow: 0 10px 20px rgba(158, 42, 43, 0.18);
        }
        .btn-primary-gradient:hover {
            filter: brightness(0.98);
            box-shadow: 0 12px 22px rgba(158, 42, 43, 0.22);
        }

        .link-soft {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 600;
        }
        .link-soft:hover { color: var(--primary); }

        .alert-mini {
            font-size: 0.9rem;
            background-color: #FEF2F2;
            border: 1px solid #FECACA;
            color: #991B1B;
            border-radius: 12px;
            padding: 12px 14px;
        }

        @media (max-width: 480px) {
            .auth-hero { border-radius: 0; min-height: 220px; }
            .auth-shell { margin-top: -70px; }
            .landing-brand { top: 14px; left: 18px; }
        }
    </style>
</head>

<body>
    <header class="auth-hero">
        {{-- Brand: posisikan kiri-atas seperti Landing Page --}}
        <div class="landing-brand">
            <img src="{{ asset('logokabupatensemarang.png') }}" alt="Logo Kab Semarang">
            <div>
                <div class="brand-title">SIPERCUT</div>
                <div class="brand-subtitle">DISDIKBUDPORA KAB. SEMARANG</div>
            </div>
        </div>
    </header>

    <main class="auth-shell">
        <div class="auth-card">
            <div class="auth-card-header">
                <div class="text-center">
                    <h1 class="m-0" style="font-size: 1.35rem; font-weight: 800;">Masuk ke SIPERCUT</h1>
                    <p class="m-0 mt-1" style="color: rgba(255,255,255,0); display:none;">&nbsp;</p>
                    <p class="m-0" style="color: var(--text-secondary); font-size: 0.92rem;">Sistem Informasi Cuti Pegawai</p>
                </div>
            </div>

            <div class="auth-card-body">
             {{-- Throttle warning (khusus) --}}
            @if ($errors->has('throttle'))
                <div class="alert-mini mb-4" style="background-color:#FFF7ED; border-color:#FED7AA; color:#92400E;">
                    <div class="d-flex gap-2 align-items-start">
                        <i class="bi bi-clock-history" style="margin-top:2px; flex-shrink:0;"></i>
                        <div>{{ $errors->first('throttle') }}</div>
                    </div>
                </div>
            @endif

            {{-- Error umum — KECUALIKAN throttle --}}
            @if ($errors->hasAny(['nip', 'password', 'login']))
                <div class="alert-mini mb-4">
                    <div class="d-flex gap-2 align-items-start">
                        <i class="bi bi-exclamation-triangle-fill" style="margin-top: 2px;"></i>
                        <div>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->get('nip') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                                @foreach ($errors->get('password') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                                @foreach ($errors->get('login') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

                @if (session('success'))
                    <div class="alert border-0 bg-success bg-opacity-10 text-success rounded-4 p-3 mb-4" style="font-size: 0.9rem;">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('login.process') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Nomor Induk Pegawai</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white" style="border-radius: 12px 0 0 12px; border: 1px solid #E2E8F0; border-right: none;">
                                <i class="bi bi-person-badge"></i>
                            </span>
                            <input type="text" name="nip" class="form-control" placeholder="Masukkan NIP Anda" value="{{ old('nip') }}" required autofocus style="border-left: none; border-radius: 0 12px 12px 0;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="form-label mb-0">Password</label>
                            <a href="{{ route('password.request') }}" class="link-soft" style="font-size: 0.82rem;">Lupa password?</a>
                        </div>
                        <div class="input-group mt-2">
                            <span class="input-group-text bg-white" style="border-radius: 12px 0 0 12px; border: 1px solid #E2E8F0; border-right: none;">
                                <i class="bi bi-shield-lock"></i>
                            </span>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required style="border-left: none; border-radius: 0 12px 12px 0;">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary-gradient text-white w-100 mt-2">
                        Masuk
                    </button>
                </form>

                <div class="text-center mt-4">
                    <p class="m-0" style="font-size: 0.9rem; color: var(--text-secondary);">
                        Belum punya akun? <a href="{{ route('register') }}" class="text-decoration-none" style="color: var(--primary); font-weight: 800;">Daftar</a>
                    </p>
                </div>

                <div class="text-center mt-4" style="font-size: 0.78rem; color: #94A3B8;">
                    &copy; 2026 Disdikbudpora Kabupaten Semarang
                    <div class="mt-2">
                        <a href="{{ route('landing') }}" class="text-decoration-none" style="color: #94A3B8; font-weight: 600;">Kembali ke Beranda</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
