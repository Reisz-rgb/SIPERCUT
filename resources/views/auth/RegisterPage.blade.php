<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru - SIPERCUT</title>

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

        /* Brand*/
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
            max-width: 560px;
            margin: 0 auto;
            padding: 0 16px 40px 16px;
            margin-top: -90px;
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

        .auth-card-body { padding: 22px; }

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
            font-weight: 700;
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
                    <h1 class="m-0" style="font-size: 1.35rem; font-weight: 800;">Daftar Akun Baru</h1>
                    <p class="m-0 mt-1" style="color: var(--text-secondary); font-size: 0.92rem;">Lengkapi data diri Anda untuk mendaftar</p>
                </div>
            </div>

            <div class="auth-card-body">
                @if ($errors->any())
                    <div class="alert-mini mb-4">
                        <div class="d-flex gap-2 align-items-start">
                            <i class="bi bi-exclamation-triangle-fill" style="margin-top: 2px;"></i>
                            <div>
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <div class="alert border-0 rounded-4 p-3 mb-4" style="background: #E7F1FF; color: #0C5460; border: 1px solid #B3D7FF;">
                    <div class="d-flex gap-2">
                        <i class="bi bi-info-circle-fill" style="margin-top: 2px;"></i>
                        <div style="font-size: 0.88rem; line-height: 1.55;">
                            <div style="font-weight: 800;">Penting:</div>
                            <div>1. Masukkan <strong>Nama</strong> dan <strong>NIP</strong> sesuai data pegawai yang terdaftar.</div>
                            <div>2. Jika pertama kali login, gunakan <strong>NIP sebagai password</strong>.</div>
                            <div>3. Gunakan form ini untuk <strong>mengubah password</strong> dan <strong>mendaftarkan nomor HP</strong> Anda.</div>
                        </div>
                    </div>
                </div>

                {{-- DILARANG ubah name/action/logic. Hanya styling. --}}
                <form action="{{ route('register.process') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white" style="border-radius: 12px 0 0 12px; border: 1px solid #E2E8F0; border-right: none;">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" name="name" class="form-control" placeholder="Nama lengkap sesuai SK" value="{{ old('name') }}" required style="border-left: none; border-radius: 0 12px 12px 0;">
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">NIP</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white" style="border-radius: 12px 0 0 12px; border: 1px solid #E2E8F0; border-right: none;">
                                    <i class="bi bi-person-badge"></i>
                                </span>
                                <input type="text" name="nip" class="form-control" placeholder="19xxxxxxxxxxx" value="{{ old('nip') }}" required style="border-left: none; border-radius: 0 12px 12px 0;">
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">Nomor HP / WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white" style="border-radius: 12px 0 0 12px; border: 1px solid #E2E8F0; border-right: none;">
                                    <i class="bi bi-telephone"></i>
                                </span>
                                <input type="text" name="phone" class="form-control" placeholder="0812xxxx" value="{{ old('phone') }}" required style="border-left: none; border-radius: 0 12px 12px 0;">
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white" style="border-radius: 12px 0 0 12px; border: 1px solid #E2E8F0; border-right: none;">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" name="email" class="form-control" placeholder="nama@email.com" value="{{ old('email') }}" required style="border-left: none; border-radius: 0 12px 12px 0;">
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">Unit Kerja</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white" style="border-radius: 12px 0 0 12px; border: 1px solid #E2E8F0; border-right: none;">
                                    <i class="bi bi-building"></i>
                                </span>
                                <input type="text" name="bidang_unit" list="unitKerjaOptions" class="form-control" placeholder="Ketik atau pilih unit kerja" value="{{ old('bidang_unit') }}" autocomplete="off" required style="border-left: none; border-radius: 0 12px 12px 0;">
                                <datalist id="unitKerjaOptions">
                                    @foreach (($unitKerjaOptions ?? []) as $unitKerja)
                                        <option value="{{ $unitKerja }}"></option>
                                    @endforeach
                                </datalist>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">Jabatan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white" style="border-radius: 12px 0 0 12px; border: 1px solid #E2E8F0; border-right: none;">
                                    <i class="bi bi-briefcase"></i>
                                </span>
                                <input type="text" name="jabatan" list="jabatanOptions" class="form-control" placeholder="Ketik atau pilih jabatan" value="{{ old('jabatan') }}" autocomplete="off" required style="border-left: none; border-radius: 0 12px 12px 0;">
                                <datalist id="jabatanOptions">
                                    @foreach (($jabatanOptions ?? []) as $jabatanOption)
                                        <option value="{{ $jabatanOption }}"></option>
                                    @endforeach
                                </datalist>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white" style="border-radius: 12px 0 0 12px; border: 1px solid #E2E8F0; border-right: none;">
                                    <i class="bi bi-shield-lock"></i>
                                </span>
                                <input type="password" name="password" class="form-control" placeholder="••••••••" required style="border-left: none; border-radius: 0 12px 12px 0;">
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">Konfirmasi Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white" style="border-radius: 12px 0 0 12px; border: 1px solid #E2E8F0; border-right: none;">
                                    <i class="bi bi-shield-check"></i>
                                </span>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required style="border-left: none; border-radius: 0 12px 12px 0;">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary-gradient text-white w-100 mt-4">
                        Daftar
                    </button>

                    <div class="text-center mt-4">
                        <span style="font-size: 0.9rem; color: var(--text-secondary);">Sudah punya akun?</span>
                        <a href="{{ route('login') }}" class="text-decoration-none" style="color: var(--primary); font-weight: 800;">Masuk</a>
                    </div>

                    <div class="text-center mt-4" style="font-size: 0.78rem; color: #94A3B8;">
                        &copy; 2026 Disdikbudpora Kabupaten Semarang
                        <div class="mt-2">
                            <a href="{{ url('/') }}" class="text-decoration-none" style="color: #94A3B8; font-weight: 600;">Kembali ke Halaman Utama</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
