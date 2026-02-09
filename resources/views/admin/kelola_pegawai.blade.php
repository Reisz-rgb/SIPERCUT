@extends('layouts.admin')
@section('title', 'Kelola Pegawai - SIIPUL')

@push('styles')
<style>
        /* --- 1. CORE VARIABLES & LAYOUT --- */
        :root {
            --primary: #9E2A2B;         
            --primary-dark: #781F1F;    
            --secondary: #64748B;
            --bg-body: #F1F5F9;         
            --sidebar-width: 270px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: #334155;
            overflow-x: hidden;
        }

        /* --- SIDEBAR & OVERLAY --- */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            background: #FFFFFF;
            border-right: 1px dashed #E2E8F0;
            z-index: 1001;
            padding: 24px;
            display: flex; flex-direction: column;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            z-index: 1000;
            display: none; opacity: 0; transition: opacity 0.3s ease;
        }
        .sidebar-overlay.show { display: block; opacity: 1; }

        .sidebar-brand {
            display: flex; align-items: center; gap: 12px;
            padding-bottom: 30px; text-decoration: none; color: var(--primary);
        }
        
        .nav-label {
            font-size: 0.7rem; font-weight: 700; color: #94A3B8;
            text-transform: uppercase; letter-spacing: 0.08em;
            margin: 20px 0 10px 12px;
        }

        .nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 16px; color: #64748B;
            border-radius: 12px; font-weight: 500;
            transition: all 0.2s; text-decoration: none; margin-bottom: 4px;
        }

        .nav-link:hover { background-color: #FEF2F2; color: var(--primary); }

        .nav-link.active {
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white; box-shadow: 0 4px 12px rgba(158, 42, 43, 0.3);
        }
        .nav-link.active i { color: white; }

        /* --- MAIN CONTENT --- */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease-in-out;
        }

        .hero-banner {
            background: linear-gradient(135deg, var(--primary) 0%, #561616 100%);
            height: 280px; padding: 40px; color: white;
            border-bottom-left-radius: 30px; border-bottom-right-radius: 30px;
        }

        .glass-profile {
            background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2); padding: 8px 16px;
            border-radius: 50px; color: white; display: flex; align-items: center; gap: 12px;
            cursor: pointer; transition: 0.2s;
        }
        .glass-profile:hover { background: rgba(255, 255, 255, 0.2); }

        .dashboard-container { padding: 0 40px 40px 40px; margin-top: -80px; }

        .card-content {
            background: white; border-radius: 16px; border: 1px solid #F1F5F9;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06);
            overflow: hidden; margin-bottom: 24px; padding: 0;
        }

        /* --- TABLE & SEARCH STYLING --- */
        .table-custom { width: 100%; border-collapse: separate; border-spacing: 0; }
        
        .table-custom thead th {
            background: #F8FAFC; color: #64748B; padding: 16px 24px;
            font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
            border-bottom: 1px solid #E2E8F0;
        }
        
        .table-custom tbody td {
            padding: 16px 24px; vertical-align: middle;
            border-bottom: 1px solid #F1F5F9; color: #334155; font-size: 0.95rem;
        }
        
        .table-custom tbody tr:hover { background-color: #FAFAFA; }
        
        /* Action Buttons */
        .btn-action {
            width: 34px; height: 34px; border-radius: 8px; 
            display: flex; align-items: center; justify-content: center;
            border: 1px solid #E2E8F0; background: white; color: #64748B;
            transition: 0.2s; text-decoration: none;
        }
        .btn-action:hover { border-color: var(--primary); color: var(--primary); background: #FEF2F2; }
        .btn-action.delete:hover { border-color: #EF4444; color: #EF4444; background: #FEF2F2; }

        /* Badges */
        .status-badge { padding: 6px 12px; border-radius: 30px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }
        .status-active { background: #F0FDF4; color: #15803D; border: 1px solid #DCFCE7; }
        .status-inactive { background: #F1F5F9; color: #64748B; border: 1px solid #E2E8F0; }

        /* Search Box */
        .search-box { position: relative; }
        .search-box input {
            padding-left: 42px; border-radius: 50px; height: 45px;
            border: 1px solid #E2E8F0; background: #F8FAFC;
            transition: 0.2s; font-size: 0.9rem;
        }
        .search-box input:focus { border-color: var(--secondary); background: white; box-shadow: 0 0 0 4px rgba(100, 116, 139, 0.1); outline: none; }
        .search-box i {
            position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94A3B8;
        }

        /* Mobile */
        .mobile-toggler { display: none; color: white; font-size: 1.5rem; background: none; border: none; margin-right: 15px; }
        
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .hero-banner { padding: 20px; height: auto; padding-bottom: 100px; }
            .dashboard-container { padding: 0 20px 20px; }
            .mobile-toggler { display: block; }
            .table-responsive { overflow-x: auto; }
        }
    </style>
@endpush

@section('content')
<div class="hero-banner">
            <div class="d-flex justify-content-between align-items-start">
                <div class="d-flex align-items-center">
                    <button class="mobile-toggler" id="btnToggleSidebar">
                        <i class="bi bi-list"></i>
                    </button>
                    <div>
                        <div class="text-white text-opacity-75 small mb-1 fw-medium">
                            Administrator <i class="bi bi-chevron-right mx-1" style="font-size: 0.7rem"></i> Master Data
                        </div>
                        <h2 class="fw-bold m-0 text-white">Kelola Pegawai</h2>
                    </div>
                </div>
                
                <div class="dropdown">
                    <div class="glass-profile" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-white text-danger fw-bold d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <span class="d-none d-md-block small fw-medium">{{ Auth::user()->name }}</span>
                        <i class="bi bi-chevron-down small d-none d-md-block"></i>
                    </div>
                     <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-2 rounded-3">
                        <li><a class="dropdown-item rounded small" href="{{ route('admin.profil') }}">Profile Saya</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item rounded small text-danger">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="dashboard-container">
            
            @if(session('success'))
            <div class="alert alert-success d-flex align-items-center border-0 shadow-sm rounded-3 mb-4" role="alert" style="background-color: #F0FDF4; color: #15803D;">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="card-content">
                
                <div class="p-4 border-bottom border-light bg-white">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <form action="{{ route('admin.kelola_pegawai') }}" method="GET">
                                <div class="search-box">
                                    <i class="bi bi-search"></i>
                                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau NIP..." value="{{ request('search') }}">
                                </div>
                            </form>
                        </div>
                        <div class="col-md-8 text-md-end">
                            <a href="{{ route('admin.tambah_pegawai') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm" style="background: var(--primary); border:none;">
                                <i class="bi bi-plus-lg me-2"></i>Tambah Pegawai
                            </a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th class="ps-4">Nama Pegawai</th>
                                <th>NIP</th>
                                <th>Jabatan</th>
                                <th>Unit Kerja</th>
                                <th>Sisa Cuti</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pegawai as $p)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" 
                                             style="width: 40px; height: 40px; background: #F1F5F9; color: var(--secondary);">
                                            {{ substr($p->name, 0, 1) }}
                                        </div>
                                        
                                        <div>
                                            <div class="fw-bold text-dark">{{ $p->name }}</div>
                                            <div class="text-muted small" style="font-size: 0.75rem;">{{ $p->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-secondary fw-medium font-monospace">{{ $p->nip }}</td>
                                <td>{{ $p->jabatan ?? '-' }}</td>
                                <td>{{ $p->bidang_unit ?? '-' }}</td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $p->annual_leave_quota }}</span>
                                    <span class="text-muted small">/ 12</span>
                                </td>
                                <td>
                                    @if($p->status == 'nonaktif')
                                        <span class="status-badge status-inactive">
                                            <i class="bi bi-circle-fill" style="font-size: 6px;"></i> Non-Aktif
                                        </span>
                                    @else
                                        <span class="status-badge status-active">
                                            <i class="bi bi-circle-fill" style="font-size: 6px;"></i> Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.pegawai.edit', $p->id) }}" class="btn-action" title="Edit Data">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        
                                        <form id="delete-form-{{ $p->id }}" action="{{ route('admin.pegawai.destroy', $p->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="konfirmasiHapus({{ $p->id }})" class="btn-action delete" title="Hapus Pegawai">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted opacity-25 mb-3"><i class="bi bi-people fs-1"></i></div>
                                    <p class="text-muted small m-0">Tidak ada data pegawai ditemukan.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($pegawai->hasPages())
                <div class="p-4 border-top border-light d-flex justify-content-between align-items-center bg-white">
                    <small class="text-muted fw-medium">
                        Menampilkan {{ $pegawai->firstItem() }}-{{ $pegawai->lastItem() }} dari {{ $pegawai->total() }} pegawai
                    </small>
                    <div>
                        {{ $pegawai->links('pagination::bootstrap-5') }}
                    </div>
                </div>
                @endif
                
            </div>

            <div class="mt-5 text-center">
                <p class="text-muted small opacity-50">&copy; 2026 Pemerintah Kabupaten Semarang.</p>
            </div>
        </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// --- 2. SCRIPT DELETE CONFIRMATION ---
        function konfirmasiHapus(id) {
            Swal.fire({
                title: 'Hapus Data Pegawai?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#9E2A2B', 
                cancelButtonColor: '#64748B',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
</script>
@endpush
