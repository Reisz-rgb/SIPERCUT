{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - SIPERCUT</title>

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- CSS Libraries --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">

    <style>
        /* ── Design Tokens ── */
        :root {
            --primary:       #9E2A2B;
            --primary-dark:  #781F1F;
            --secondary:     #64748B;
            --bg-body:       #F1F5F9;
            --sidebar-width: 270px;
            --radius-card:   16px;
            --shadow-card:   0 10px 25px -5px rgba(0,0,0,.06);
        }

        /* ── Base ── */
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: #334155;
            overflow-x: hidden;
        }

        ::-webkit-scrollbar             { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track       { background: transparent; }
        ::-webkit-scrollbar-thumb       { background: #CBD5E1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            background: #fff;
            border-right: 1px dashed #E2E8F0;
            z-index: 1060;
            padding: 24px;
            display: flex;
            flex-direction: column;
            transition: transform .3s cubic-bezier(.4,0,.2,1);
        }

        .sidebar-brand {
            display: flex; align-items: center; gap: 12px;
            padding-bottom: 30px;
            text-decoration: none; color: var(--primary);
        }

        .nav-label {
            font-size: .7rem; font-weight: 700; color: #94A3B8;
            text-transform: uppercase; letter-spacing: .08em;
            margin: 20px 0 10px 12px;
        }

        .nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 16px; color: #64748B;
            border-radius: 12px; font-weight: 500;
            transition: all .2s; margin-bottom: 4px;
            text-decoration: none;
        }
        .nav-link:hover      { background: #FEF2F2; color: var(--primary); }
        .nav-link.active     { background: linear-gradient(90deg, var(--primary), var(--primary-dark)); color: #fff; box-shadow: 0 4px 12px rgba(158,42,43,.3); }
        .nav-link.active i   { color: #fff; }

        /* ── Layout ── */
        .main-content { margin-left: var(--sidebar-width); min-height: 100vh; }

        /* ── Hero Banner ── */
        .hero-banner {
            background: linear-gradient(135deg, var(--primary) 0%, #561616 100%);
            height: 280px; padding: 40px; color: #fff;
            border-bottom-left-radius: 30px;
            border-bottom-right-radius: 30px;
        }

        /* ── Glass Profile Pill ── */
        .glass-profile {
            background: rgba(255,255,255,.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,.2);
            padding: 8px 16px; border-radius: 50px; color: #fff;
            display: flex; align-items: center; gap: 12px;
            cursor: pointer; transition: background .2s;
        }
        .glass-profile:hover { background: rgba(255,255,255,.2); }

        /* ── Cards ── */
        .dashboard-container { padding: 0 40px 40px; margin-top: -80px; }

        .card-stat {
            background: #fff; border: none;
            border-radius: var(--radius-card);
            padding: 24px;
            box-shadow: var(--shadow-card);
            height: 100%; transition: transform .2s;
        }
        .card-stat:hover { transform: translateY(-5px); }

        .stat-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; margin-bottom: 16px;
        }

        .card-content {
            background: #fff; border-radius: var(--radius-card);
            border: 1px solid #F1F5F9;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,.02);
            overflow: hidden;
        }

        .card-header-custom {
            padding: 24px; border-bottom: 1px dashed #E2E8F0;
            display: flex; justify-content: space-between; align-items: center;
        }

        .list-item-custom {
            padding: 16px 24px; border-bottom: 1px solid #F8FAFC;
            transition: background .1s;
        }
        .list-item-custom:hover { background: #FDFDFD; }

        /* ── Sidebar Overlay (mobile) ── */
        #sidebarOverlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,.5);
            backdrop-filter: blur(2px);
            z-index: 1055;
            display: none;
        }
        #sidebarOverlay.show { display: block; }

        .mobile-toggler {
            display: none; color: #fff; font-size: 1.5rem;
            border: none; background: none; margin-right: 15px;
        }

        /* ── Select2 ── */
        .select2-container--bootstrap-5 .select2-selection {
            border: 1px solid #E2E8F0 !important;
            border-radius: 10px !important;
            padding: 5px 10px !important;
            background-color: #F8FAFC !important;
            min-height: 45px !important;
            display: flex; align-items: center;
        }

        /* ── Responsive ── */
        @media (max-width: 992px) {
            .sidebar              { transform: translateX(-100%); }
            .sidebar.show         { transform: translateX(0); }
            .main-content         { margin-left: 0; }
            .hero-banner          { border-radius: 0; padding: 20px; height: auto; padding-bottom: 100px; }
            .dashboard-container  { padding: 0 20px 20px; }
            .mobile-toggler       { display: block; }
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- Mobile overlay --}}
<div id="sidebarOverlay"></div>

{{-- Sidebar --}}
<nav class="sidebar" id="sidebar">

    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
        <img src="{{ asset('logokabupatensemarang.png') }}" alt="Logo Kabupaten Semarang" width="36" height="36">
        <div style="line-height:1.1">
            <div style="font-weight:800;font-size:1.1rem;letter-spacing:-.5px">SIPERCUT</div>
            <div style="font-size:.7rem;color:#94A3B8;font-weight:500">Kab. Semarang</div>
        </div>
    </a>

    <div style="overflow-y:auto;flex:1">
        <div class="nav-label">Main Menu</div>

        <a href="{{ route('admin.dashboard') }}"
           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-fill"></i> Dashboard
        </a>

        <a href="{{ route('admin.kelola_pengajuan') }}"
           class="nav-link {{ request()->routeIs('admin.kelola_pengajuan*', 'admin.pengajuan.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text"></i> Pengajuan Cuti
            @if(isset($menunggu) && $menunggu > 0)
                <span class="badge bg-danger rounded-pill ms-auto" style="font-size:.7rem">{{ $menunggu }}</span>
            @endif
        </a>

        <a href="{{ route('admin.kelola_pegawai') }}"
           class="nav-link {{ request()->routeIs('admin.kelola_pegawai*', 'admin.tambah_pegawai*', 'admin.pegawai.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Data Pegawai
        </a>

        <div class="nav-label">Laporan</div>

        <a href="{{ route('admin.laporan') }}"
           class="nav-link {{ request()->routeIs('admin.laporan*') ? 'active' : '' }}">
            <i class="bi bi-printer"></i> Rekapitulasi
        </a>
    </div>

    <div class="mt-auto pt-4 border-top border-dashed">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                    class="btn btn-outline-danger w-100 border-0 d-flex align-items-center gap-2 px-3 py-2 bg-light"
                    style="font-size:.9rem">
                <i class="bi bi-box-arrow-left"></i> Keluar Aplikasi
            </button>
        </form>
    </div>

</nav>

{{-- Main Content --}}
<div class="main-content">
    @yield('content')
</div>

{{-- JS Libraries --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    /* ── Mobile Sidebar ── */
    (function () {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        function openSidebar() {
            sidebar.classList.add('show');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        // Expose globally so inline onclick attrs in child views still work
        window.openSidebar   = openSidebar;
        window.closeSidebar  = closeSidebar;
        window.toggleSidebar = () => sidebar.classList.contains('show') ? closeSidebar() : openSidebar();

        // Bind all toggle triggers
        document.querySelectorAll('.mobile-toggler, [data-toggle-sidebar]')
            .forEach(btn => btn.addEventListener('click', e => { e.preventDefault(); toggleSidebar(); }));

        overlay.addEventListener('click', closeSidebar);
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });
    })();
</script>

@stack('scripts')
</body>
</html>