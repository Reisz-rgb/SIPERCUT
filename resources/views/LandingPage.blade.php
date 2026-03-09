<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPERCUT - Disdikbudpora Kab. Semarang</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary: #9E2A2B;
            --primary-dark: #781F1F;
            --bg-body: #F1F5F9;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-body);
            color: #334155;
            overflow-x: hidden;
        }

        /* background image layer */
        .bg-hero {
            position: relative;
            min-height: 100vh;
            background-image:
                linear-gradient(135deg, rgba(158, 42, 43, 0.88) 0%, rgba(86, 22, 22, 0.88) 55%, rgba(15, 23, 42, 0.78) 100%),
                url('{{ asset('images/landing-bg.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 1100;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.08);
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #fff;
        }

        .brand-title {
            font-weight: 800;
            letter-spacing: -0.3px;
            line-height: 1.1;
        }

        .brand-sub {
            font-size: 0.75rem;
            opacity: 0.85;
            letter-spacing: 0.06em;
        }

        .btn-primary-soft {
            background: #fff;
            color: var(--primary);
            border: 1px solid rgba(255,255,255,0.45);
            font-weight: 800;
            border-radius: 14px;
            padding: 10px 16px;
            box-shadow: 0 10px 25px -10px rgba(0,0,0,0.35);
        }
        .btn-primary-soft:hover {
            background: rgba(255,255,255,0.92);
            color: var(--primary-dark);
        }

        .hero-wrap {
            padding: 56px 0 40px 0;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.18);
            color: #fff;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .hero-title {
            color: #fff;
            font-weight: 900;
            letter-spacing: -1px;
            font-size: clamp(2.2rem, 4.2vw, 3.6rem);
            margin: 14px 0 10px 0;
        }

        .hero-lead {
            color: rgba(255,255,255,0.9);
            max-width: 52ch;
            font-size: 1.05rem;
        }

        .card-glass {
            background: rgba(255,255,255,0.92);
            border: 1px solid rgba(255,255,255,0.55);
            border-radius: 18px;
            box-shadow: 0 18px 45px -20px rgba(0,0,0,0.45);
            overflow: hidden;
        }

        .card-glass .card-head {
            padding: 22px 22px 16px 22px;
            border-bottom: 1px dashed #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .pill {
            padding: 8px 12px;
            border-radius: 999px;
            background: #FEF2F2;
            color: var(--primary);
            font-weight: 800;
            font-size: 0.85rem;
        }

        .feature {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            padding: 14px 22px;
        }

        .feature + .feature {
            border-top: 1px solid #F1F5F9;
        }

        .feature-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(158, 42, 43, 0.12) 0%, rgba(158, 42, 43, 0.06) 100%);
            display: grid;
            place-items: center;
            color: var(--primary);
            flex: 0 0 auto;
        }

        .feature-title { font-weight: 900; margin: 0; }
        .feature-text { margin: 2px 0 0 0; color: #64748B; font-size: 0.92rem; }

        .cta-row {
            padding: 18px 22px 22px 22px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-gradient {
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            color: #fff;
            font-weight: 900;
            border-radius: 14px;
            padding: 12px 16px;
            box-shadow: 0 10px 25px -12px rgba(158, 42, 43, 0.55);
        }
        .btn-gradient:hover { filter: brightness(0.98); }

        .btn-outline-light-soft {
            background: transparent;
            border: 1px solid rgba(15, 23, 42, 0.15);
            color: #0F172A;
            font-weight: 800;
            border-radius: 14px;
            padding: 12px 16px;
        }
        .btn-outline-light-soft:hover { background: rgba(15, 23, 42, 0.04); }

        .footer {
            color: rgba(255,255,255,0.85);
            font-size: 0.85rem;
            padding: 22px 0 28px 0;
        }

        @media (max-width: 992px) {
            .hero-wrap { padding-top: 34px; }
            .hero-lead { font-size: 1rem; }
        }
    </style>
</head>

<body>
    <div class="bg-hero">
        <!-- Topbar (sticky, selaras feel admin/user) -->
        <header class="topbar">
            <div class="container py-3 px-3 px-md-4 d-flex align-items-center justify-content-between">
                <a class="brand" href="#">
                    <img src="{{ asset('logokabupatensemarang.png') }}" alt="Logo" width="40" height="40" style="object-fit: contain;">
                    <div>
                        <div class="brand-title">SIPERCUT</div>
                        <div class="brand-sub">DISDIKBUDPORA KAB. SEMARANG</div>
                    </div>
                </a>

                {{-- Tombol Login/Register dihapus (sesuai permintaan) --}}
            </div>
        </header>

        <!-- Hero -->
        <main class="container hero-wrap px-3 px-md-4">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <div class="hero-badge">
                        <i class="bi bi-shield-check"></i>
                        Sistem Informasi Izin & Cuti Pegawai
                    </div>

                    <h1 class="hero-title">Kelola Izin & Cuti Pegawai<br class="d-none d-lg-block"> dengan Cepat dan Rapi</h1>

                    <p class="hero-lead mb-0">
                        Satu pintu untuk pengajuan cuti, monitoring status, dan rekapitulasi.
                        Desainnya dibuat konsisten dengan tampilan Admin/User SIPERCUT.
                    </p>
                </div>

                <div class="col-lg-6">
                    <div class="card-glass">
                        <div class="card-head">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width:44px;height:44px;border-radius:14px;background:linear-gradient(135deg,var(--primary) 0%, var(--primary-dark) 100%);display:grid;place-items:center;color:white;box-shadow:0 10px 22px -12px rgba(158,42,43,0.55);">
                                    <i class="bi bi-calendar2-check" style="font-size:1.2rem"></i>
                                </div>
                                <div>
                                    <div style="font-weight:900; font-size:1.05rem;">Mulai dengan SIPERCUT</div>
                                    <div style="color:#64748B; font-size:0.9rem;">Panduan singkat cara mengajukan cuti</div>
                                </div>
                            </div>
                            <span class="pill">2026</span>
                        </div>

                        <div class="feature">
                            <div class="feature-icon"><span style="font-weight:900;">1</span></div>
                            <div>
                                <p class="feature-title">Login ke SIPERCUT</p>
                                <p class="feature-text">Masuk pakai akun kamu. Jika belum punya akun, minta dibuatkan oleh admin/kepegawaian.</p>
                            </div>
                        </div>

                        <div class="feature">
                            <div class="feature-icon"><span style="font-weight:900;">2</span></div>
                            <div>
                                <p class="feature-title">Buka menu Pengajuan Cuti</p>
                                <p class="feature-text">Pilih jenis cuti, lalu isi tanggal mulai–selesai sesuai kebutuhan.</p>
                            </div>
                        </div>

                        <div class="feature">
                            <div class="feature-icon"><span style="font-weight:900;">3</span></div>
                            <div>
                                <p class="feature-title">Lengkapi data & lampiran</p>
                                <p class="feature-text">Isi alasan cuti dan upload lampiran bila diperlukan (mis. surat dokter).</p>
                            </div>
                        </div>

                        <div class="feature">
                            <div class="feature-icon"><span style="font-weight:900;">4</span></div>
                            <div>
                                <p class="feature-title">Kirim & pantau status</p>
                                <p class="feature-text">Klik <b>Ajukan</b>, lalu pantau proses persetujuan di Monitoring/Riwayat.</p>
                            </div>
                        </div>

                        <div class="cta-row">
                            <a href="{{ url('/login') }}" class="btn btn-gradient d-inline-flex align-items-center gap-2">
                                <i class="bi bi-arrow-right-circle"></i>
                                Masuk & Ajukan Cuti
                            </a>
                            {{-- Tombol Buat Akun dihapus (sesuai permintaan) --}}
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer text-center">
                <div class="opacity-75">© 2026 Disdikbudpora Kabupaten Semarang</div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
