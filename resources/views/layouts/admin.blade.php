<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - SIPERCUT')</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    
    <style>
        /* Variables */
        :root {
            --primary: #9E2A2B;       /* Red Maroon */
            --primary-dark: #781F1F;  /* Dark Red */
            --secondary: #64748B;     /* Slate Grey */
            --bg-body: #F1F5F9;       /* Slate 100 */
            --sidebar-width: 270px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: #334155;
            overflow-x: hidden;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            background: #FFFFFF;
            border-right: 1px dashed #E2E8F0;
            z-index: 1060 !important;
            padding: 24px;
            display: flex; flex-direction: column;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-brand {
            display: flex; align-items: center; gap: 12px; padding-bottom: 30px;
            text-decoration: none; color: var(--primary);
        }
        
        .nav-label {
            font-size: 0.7rem; font-weight: 700; color: #94A3B8;
            text-transform: uppercase; letter-spacing: 0.08em;
            margin: 20px 0 10px 12px;
        }

        .nav-link {
            display: flex; align-items: center; gap: 12px; padding: 12px 16px;
            color: #64748B; border-radius: 12px; font-weight: 500;
            transition: all 0.2s; margin-bottom: 4px; text-decoration: none;
        }

        .nav-link:hover { background-color: #FEF2F2; color: var(--primary); }

        .nav-link.active {
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(158, 42, 43, 0.3);
        }
        .nav-link.active i { color: white; }

        /* Main Layout */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease-in-out;
        }

        /* Hero Banner */
        .hero-banner {
            background: linear-gradient(135deg, var(--primary) 0%, #561616 100%);
            height: 280px;
            padding: 40px;
            color: white;
            border-bottom-left-radius: 30px;
            border-bottom-right-radius: 30px;
        }

        /* Glass Profile */
        .glass-profile {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 50px;
            color: white;
            display: flex; align-items: center; gap: 12px;
            cursor: pointer; transition: 0.2s;
        }
        .glass-profile:hover { background: rgba(255, 255, 255, 0.2); }

        /* Cards & Widgets */
        .dashboard-container {
            padding: 0 40px 40px 40px;
            margin-top: -80px;
        }

        .card-stat {
            background: white; border: none; border-radius: 16px;
            padding: 24px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06);
            height: 100%; transition: transform 0.2s;
        }
        .card-stat:hover { transform: translateY(-5px); }

        .stat-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; margin-bottom: 16px;
        }

        .card-content {
            background: white; border-radius: 16px;
            border: 1px solid #F1F5F9;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
            overflow: hidden;
        }
        .card-header-custom {
            padding: 24px; border-bottom: 1px dashed #E2E8F0;
            display: flex; justify-content: space-between; align-items: center;
        }

        .list-item-custom {
            padding: 16px 24px; border-bottom: 1px solid #F8FAFC;
            transition: background 0.1s;
        }
        .list-item-custom:hover { background-color: #FDFDFD; }

        /* Mobile UX */
        .mobile-toggler { 
            display: none; color: white; font-size: 1.5rem; 
            border: none; background: none; margin-right: 15px; 
        }

        #sidebarOverlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1055 !important;
            display: none;
            backdrop-filter: blur(2px);
            pointer-events: auto;
        }

        #sidebarOverlay.show { display: block; }

        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .hero-banner { border-radius: 0; padding: 20px; height: auto; padding-bottom: 100px; }
            .dashboard-container { padding: 0 20px 20px 20px; }
            .mobile-toggler { display: block; }
        }

        /* Select2 Custom Style */
        .select2-container--bootstrap-5 .select2-selection {
            border: 1px solid #E2E8F0 !important;
            border-radius: 10px !important;
            padding: 5px 10px !important;
            background-color: #F8FAFC !important;
            min-height: 45px !important;
            display: flex;
            align-items: center;
        }
    </style>

    @stack('styles')

</head>
<body>

    <div id="sidebarOverlay"></div>

    <nav class="sidebar" id="sidebar">
        <a href="#" class="sidebar-brand">
            <img src="{{ asset('logokabupatensemarang.png') }}" alt="Logo" width="36">
            <div style="line-height: 1.1;">
                <div style="font-weight: 800; font-size: 1.1rem; letter-spacing: -0.5px;">SIPERCUT</div>
                <div style="font-size: 0.7rem; color: #94A3B8; font-weight: 500;">Kab. Semarang</div>
            </div>
        </a>

        <div style="overflow-y: auto; flex: 1;" class="custom-scrollbar">
            <div class="nav-label">Main Menu</div>
            
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i> Dashboard
            </a>

            <a href="{{ route('admin.kelola_pengajuan') }}" class="nav-link {{ (request()->routeIs('admin.kelola_pengajuan*') || request()->routeIs('admin.pengajuan.*')) ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> Pengajuan Cuti
                @if(isset($menunggu) && $menunggu > 0)
                    <span class="badge bg-danger rounded-pill ms-auto" style="font-size: 0.7rem">{{ $menunggu }}</span>
                @endif
            </a>

            <a href="{{ route('admin.kelola_pegawai') }}" class="nav-link {{ (request()->routeIs('admin.kelola_pegawai*') || request()->routeIs('admin.tambah_pegawai*') || request()->routeIs('admin.pegawai.*')) ? 'active' : '' }}">
                <i class="bi bi-people"></i> Data Pegawai
            </a>
            
            <div class="nav-label">Laporan</div>
            <a href="{{ route('admin.laporan') }}" class="nav-link {{ request()->routeIs('admin.laporan*') ? 'active' : '' }}">
                <i class="bi bi-printer"></i> Rekapitulasi
            </a>
        </div>

        <div class="mt-auto pt-4 border-top border-dashed">
             <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 border-0 d-flex align-items-center gap-2 px-3 py-2 bg-light" style="font-size: 0.9rem;">
                    <i class="bi bi-box-arrow-left"></i> Keluar Aplikasi
                </button>
            </form>
        </div>
    </nav>

    <div class="main-content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Mobile Sidebar (Logic Global Sidebar)
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            window.toggleSidebar = function toggleSidebar() {
                if (!sidebar || !overlay) return;
                const willOpen = !sidebar.classList.contains('show');
                sidebar.classList.toggle('show', willOpen);
                overlay.classList.toggle('show', willOpen);
                document.body.style.overflow = willOpen ? 'hidden' : '';
            };

            window.closeSidebar = function closeSidebar() {
                if (!sidebar || !overlay) return;
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                document.body.style.overflow = '';
            };

            document.querySelectorAll('.mobile-toggler, #btnToggleSidebar, [data-toggle-sidebar]')
                .forEach((btn) => btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    window.toggleSidebar();
                }));

            if (overlay) overlay.addEventListener('click', window.closeSidebar);

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') window.closeSidebar();
            });
        });
    </script>

    @stack('scripts')
</body>
</html>