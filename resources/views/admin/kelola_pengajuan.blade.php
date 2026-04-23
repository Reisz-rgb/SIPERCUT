@extends('layouts.admin')
@section('title', 'Kelola Pengajuan - SIPERCUT')

@push('styles')
<style>
    /* --- VARIABLES (SAMA DENGAN DASHBOARD) --- */
    :root {
        --primary:       #9E2A2B;
        --primary-dark:  #781F1F;
        --secondary:     #64748B;
        --bg-body:       #F1F5F9;
        --sidebar-width: 270px;
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: var(--bg-body);
        color: #334155;
        overflow-x: hidden;
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar             { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track       { background: transparent; }
    ::-webkit-scrollbar-thumb       { background: #CBD5E1; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

    /* --- SIDEBAR --- */
    .sidebar {
        width: var(--sidebar-width);
        height: 100vh;
        position: fixed;
        top: 0; left: 0;
        background: #FFFFFF;
        border-right: 1px dashed #E2E8F0;
        z-index: 1050;
        padding: 24px;
        display: flex; flex-direction: column;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .sidebar-brand {
        display: flex; align-items: center; gap: 12px;
        padding-bottom: 30px;
        text-decoration: none;
        color: var(--primary);
    }

    .nav-label {
        font-size: 0.7rem; font-weight: 700; color: #94A3B8;
        text-transform: uppercase; letter-spacing: 0.08em;
        margin: 20px 0 10px 12px;
    }

    .nav-link {
        display: flex; align-items: center; gap: 12px;
        padding: 12px 16px;
        color: #64748B;
        border-radius: 12px;
        font-weight: 500;
        transition: all 0.2s;
        margin-bottom: 4px;
        text-decoration: none;
    }

    .nav-link:hover      { background-color: #FEF2F2; color: var(--primary); }
    .nav-link.active     { background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%); color: white; box-shadow: 0 4px 12px rgba(158, 42, 43, 0.3); }
    .nav-link.active i   { color: white; }

    /* --- MAIN LAYOUT & HERO --- */
    .main-content {
        margin-left: var(--sidebar-width);
        min-height: 100vh;
        transition: margin-left 0.3s ease-in-out;
    }

    .hero-banner {
        background: linear-gradient(135deg, var(--primary) 0%, #561616 100%);
        height: 280px;
        padding: 40px;
        color: white;
        border-bottom-left-radius: 30px;
        border-bottom-right-radius: 30px;
    }

    .glass-profile {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 8px 16px;
        border-radius: 50px;
        color: white;
        display: flex; align-items: center; gap: 12px;
        cursor: pointer;
        transition: 0.2s;
    }
    .glass-profile:hover { background: rgba(255, 255, 255, 0.2); }

    /* --- CONTENT CARD --- */
    .dashboard-container {
        padding: 0 40px 40px 40px;
        margin-top: -80px; /* Efek overlap */
    }

    .card-content {
        background: white;
        border-radius: 16px;
        border: 1px solid #F1F5F9;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        min-height: 400px;
    }

    .card-header-custom {
        padding: 24px;
        border-bottom: 1px dashed #E2E8F0;
        background: #fff;
        display: flex; flex-wrap: wrap; gap: 15px; align-items: center; justify-content: space-between;
    }

    /* --- TABLE STYLING --- */
    .table-custom th {
        font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;
        color: #64748B; background-color: #F8FAFC;
        padding: 16px 24px; border-bottom: 1px solid #E2E8F0; font-weight: 700;
    }
    .table-custom td {
        padding: 16px 24px; vertical-align: middle;
        border-bottom: 1px solid #F1F5F9; color: #334155; font-size: 0.9rem;
    }
    .table-custom tr:last-child td  { border-bottom: none; }
    .table-custom tr:hover td       { background-color: #FAFAFA; }

    /* --- BADGES --- */
    .badge-pill         { padding: 6px 12px; border-radius: 30px; font-size: 0.75rem; font-weight: 600; }
    .badge-warning-soft { background: #FFF7ED; color: #C2410C; border: 1px solid #FFEDD5; }
    .badge-success-soft { background: #F0FDF4; color: #15803D; border: 1px solid #DCFCE7; }
    .badge-danger-soft  { background: #FEF2F2; color: #B91C1C; border: 1px solid #FECACA; }

    /* --- INPUTS --- */
    .search-group           { position: relative; min-width: 250px; }
    .search-group input     { padding-left: 40px; border-radius: 10px; border-color: #E2E8F0; background: #fff; }
    .search-group input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(158, 42, 43, 0.1); }
    .search-group i         { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94A3B8; }

    .form-select-custom       { border-radius: 10px; border-color: #E2E8F0; cursor: pointer; }
    .form-select-custom:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(158, 42, 43, 0.1); }

    /* Mobile Responsive */
    .mobile-toggler { display: none; color: white; font-size: 1.5rem; background: none; border: none; margin-right: 15px; }

    #sidebarOverlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.5); z-index: 1040;
        display: none; backdrop-filter: blur(2px);
    }

    @media (max-width: 992px) {
        .sidebar              { transform: translateX(-100%); }
        .sidebar.show         { transform: translateX(0); }
        .main-content         { margin-left: 0; }
        .hero-banner          { padding: 20px; height: auto; padding-bottom: 100px; }
        .dashboard-container  { padding: 0 20px 20px; }
        .mobile-toggler       { display: block; }
        .search-group         { width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="hero-banner">
    <div class="d-flex justify-content-between align-items-start">
        <div class="d-flex align-items-center">
            <button class="mobile-toggler">
                <i class="bi bi-list"></i>
            </button>
            <div>
                <h2 class="fw-bold m-0 text-white">Kelola Pengajuan</h2>
                <p class="text-white text-opacity-75 m-0 small mt-1">
                    Dashboard <i class="bi bi-chevron-right mx-1" style="font-size: 0.7rem"></i> Pengajuan Cuti
                </p>
            </div>
        </div>

        <div class="dropdown">
            <div class="glass-profile" data-bs-toggle="dropdown">
                <div class="rounded-circle bg-white text-danger fw-bold d-flex align-items-center justify-content-center"
                     style="width: 32px; height: 32px;">
                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
                <span class="d-none d-md-block small fw-medium">{{ Auth::user()->name ?? 'Admin' }}</span>
                <i class="bi bi-chevron-down small d-none d-md-block"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-2 rounded-3">
                <li><a class="dropdown-item rounded small" href="{{ route('admin.profil') }}">Profile Saya</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item rounded small text-danger">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="dashboard-container">
    <div class="card-content">

        <div class="card-header-custom">
            <div class="d-flex gap-3 align-items-center flex-grow-1 flex-wrap">
                <h5 class="m-0 fw-bold text-dark me-2">Daftar Cuti</h5>

                <form action="{{ route('admin.kelola_pengajuan') }}" method="GET" class="d-flex">
                    <input type="hidden" name="search" value="{{ request('search') }}">

                    <select name="status"
                            class="form-select form-select-sm form-select-custom fw-medium text-secondary"
                            onchange="this.form.submit()"
                            style="min-width: 150px;">
                        <option value="Semua"    {{ request('status') == 'Semua'    ? 'selected' : '' }}>Semua Status</option>
                        <option value="pending"  {{ request('status') == 'pending'  ? 'selected' : '' }}>⏳ Menunggu</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Disetujui</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Ditolak</option>
                    </select>
                </form>
            </div>

            <form action="{{ route('admin.kelola_pengajuan') }}" method="GET" class="search-group">
                <input type="hidden" name="status" value="{{ request('status', 'Semua') }}">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="form-control"
                       placeholder="Cari Pegawai atau NIP..."
                       value="{{ request('search') }}">
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-custom w-100 m-0">
                <thead>
                    <tr>
                        <th style="padding-left: 30px;">Pegawai</th>
                        <th>Jenis & Tanggal</th>
                        <th>Durasi</th>
                        <th>Tgl Pengajuan</th>
                        <th>Status</th>
                        <th class="text-end" style="padding-right: 30px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuan as $item)
                        <tr>
                            <td style="padding-left: 30px;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                         style="width: 40px; height: 40px; background: #F1F5F9; color: var(--secondary);">
                                        {{ substr(optional($item->user)->name ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ optional($item->user)->name ?? 'User Terhapus' }}</div>
                                        <div class="text-muted small" style="font-size: 0.8rem;">{{ optional($item->user)->nip ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $item->jenis_cuti }}</div>
                                <div class="small text-muted">
                                    {{ \Carbon\Carbon::parse($item->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($item->end_date)->format('d M Y') }}
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-dark">
                                    {{ \Carbon\Carbon::parse($item->start_date)->diffInDays(\Carbon\Carbon::parse($item->end_date)) + 1 }}
                                </span> Hari
                            </td>
                            <td class="text-muted">
                                {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}
                            </td>
                            <td>
                                @if($item->status == 'menunggu' || $item->status == 'pending')
                                    <span class="badge badge-pill badge-warning-soft"><i class="bi bi-hourglass-split me-1"></i> Menunggu</span>
                                @elseif($item->status == 'disetujui' || $item->status == 'approved')
                                    <span class="badge badge-pill badge-success-soft"><i class="bi bi-check-circle me-1"></i> Disetujui</span>
                                @else
                                    <span class="badge badge-pill badge-danger-soft"><i class="bi bi-x-circle me-1"></i> Ditolak</span>
                                @endif
                            </td>
                            <td class="text-end" style="padding-right: 30px;">
                                <a href="{{ route('admin.pengajuan.show', $item->id) }}"
                                   class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-medium"
                                   style="font-size: 0.85rem;">
                                    Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="py-4">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="60" class="mb-3 opacity-25" alt="Empty">
                                    <p class="text-muted fw-bold m-0">Tidak ada data pengajuan ditemukan</p>
                                    <p class="text-muted small m-0">Coba ubah filter atau kata kunci pencarian</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pengajuan->hasPages())
            <div class="p-4 border-top">
                {{ $pengajuan->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        @endif

    </div>

    <div class="mt-5 text-center">
        <p class="text-muted small opacity-50">&copy; 2026 Pemerintah Kabupaten Semarang.</p>
    </div>
</div>
@endsection