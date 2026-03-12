@extends('layouts.admin')
@section('title', 'Detail Pengajuan - SIPERCUT')

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
            z-index: 1001; /* Di atas overlay */
            padding: 24px;
            display: flex; flex-direction: column;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Mobile Overlay (Backdrop) */
        .sidebar-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            z-index: 1000; /* Di bawah sidebar, di atas konten */
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .sidebar-overlay.show { display: block; opacity: 1; }

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
            text-decoration: none;
            margin-bottom: 4px;
        }

        .nav-link:hover { background-color: #FEF2F2; color: var(--primary); }

        /* Logic Active State */
        .nav-link.active {
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(158, 42, 43, 0.3);
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
            cursor: pointer; transition: 0.2s;
        }
        .glass-profile:hover { background: rgba(255, 255, 255, 0.2); }

        .dashboard-container {
            padding: 0 40px 40px 40px;
            margin-top: -80px;
        }

        .card-content {
            background: white;
            border-radius: 16px;
            border: 1px solid #F1F5F9;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            margin-bottom: 24px;
        }

        /* --- DETAIL PAGE STYLING --- */
        .detail-header {
            padding: 30px;
            border-bottom: 1px dashed #E2E8F0;
            display: flex; justify-content: space-between; align-items: flex-start;
            background: #fff;
        }
        
        .detail-body { padding: 30px; }

        .label-muted {
            font-size: 0.75rem; color: #94A3B8; text-transform: uppercase; 
            letter-spacing: 0.05em; font-weight: 700; margin-bottom: 6px;
        }
        .value-text { font-size: 1rem; font-weight: 600; color: #334155; margin-bottom: 24px; }

        .file-box {
            background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px;
            padding: 16px; display: flex; align-items: center; justify-content: space-between;
            transition: 0.2s;
        }
        .file-box:hover { border-color: var(--secondary); background: #F1F5F9; }

        /* Decision Form */
        .decision-card-container {
            background: #FAFAFA;
            border-left: 1px solid #E2E8F0;
            height: 100%;
            padding: 30px;
        }
        
        .btn-check-custom { position: absolute; clip: rect(0,0,0,0); pointer-events: none; }

        .option-tile {
            display: flex; align-items: center; gap: 15px;
            padding: 16px;
            background: white;
            border: 2px solid #E2E8F0;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 12px;
            position: relative;
        }
        
        .option-tile:hover { border-color: #CBD5E1; transform: translateY(-2px); }
        
        .tile-icon {
            width: 40px; height: 40px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; background: #F1F5F9; color: #64748B;
            transition: all 0.2s;
        }
        
        .tile-text { font-weight: 700; color: #334155; font-size: 0.95rem; }
        .tile-desc { font-size: 0.75rem; color: #94A3B8; font-weight: 500; }

        /* States */
        #opt_approve:checked + .option-tile { border-color: #10B981; background: #ECFDF5; }
        #opt_approve:checked + .option-tile .tile-icon { background: #10B981; color: white; }

        #opt_pending:checked + .option-tile { border-color: #F59E0B; background: #FFFBEB; }
        #opt_pending:checked + .option-tile .tile-icon { background: #F59E0B; color: white; }

        #opt_reject:checked + .option-tile { border-color: #EF4444; background: #FEF2F2; }
        #opt_reject:checked + .option-tile .tile-icon { background: #EF4444; color: white; }

        .badge-pill { padding: 8px 16px; border-radius: 30px; font-size: 0.8rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }
        .badge-warning-soft { background: #FFF7ED; color: #C2410C; border: 1px solid #FFEDD5; }
        .badge-success-soft { background: #F0FDF4; color: #15803D; border: 1px solid #DCFCE7; }
        .badge-danger-soft { background: #FEF2F2; color: #B91C1C; border: 1px solid #FECACA; }

        /* Mobile Responsive */
        .mobile-toggler { display: none; color: white; font-size: 1.5rem; background: none; border: none; margin-right: 15px; }
        
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .hero-banner { padding: 20px; height: auto; padding-bottom: 100px; }
            .dashboard-container { padding: 0 20px 20px; }
            .mobile-toggler { display: block; }
            .detail-header { flex-direction: column; gap: 15px; }
            .decision-card-container { border-left: none; border-top: 1px solid #E2E8F0; padding: 24px; }
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
                            Pengajuan Cuti <i class="bi bi-chevron-right mx-1" style="font-size: 0.7rem"></i> Detail
                        </div>
                        <h2 class="fw-bold m-0 text-white">Verifikasi Pengajuan</h2>
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
            
            <div class="mb-3">
                <a href="{{ route('admin.kelola_pengajuan') }}" class="text-white text-decoration-none small opacity-75 hover-opacity-100 fw-medium">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </div>

            @if(session('success'))
            <div class="alert alert-success d-flex align-items-center border-0 shadow-sm rounded-3 mb-4" role="alert" style="background-color: #F0FDF4; color: #15803D;">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="card-content">
                <div class="row g-0"> 
                    
                    <div class="col-lg-8">
                        <div class="detail-header">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold fs-4" 
                                     style="width: 60px; height: 60px; background: #F1F5F9; color: var(--secondary);">
                                     {{ substr($pengajuan->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="fw-bold text-dark m-0">{{ $pengajuan->user->name }}</h4>
                                    <div class="text-muted small mt-1">
                                        {{ $pengajuan->user->jabatan ?? 'Pegawai' }} &bull; NIP: {{ $pengajuan->user->nip ?? '-' }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                @if($pengajuan->status == 'approved' || $pengajuan->status == 'disetujui')
                                    <span class="badge-pill badge-success-soft"><i class="bi bi-check-circle-fill"></i> Disetujui</span>
                                @elseif($pengajuan->status == 'rejected' || $pengajuan->status == 'ditolak')
                                    <span class="badge-pill badge-danger-soft"><i class="bi bi-x-circle-fill"></i> Ditolak</span>
                                @else
                                    <span class="badge-pill badge-warning-soft"><i class="bi bi-hourglass-split"></i> Menunggu</span>
                                @endif
                            </div>
                        </div>

                        <div class="detail-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="label-muted">Unit Kerja</div>
                                    <div class="value-text">{{ $pengajuan->user->bidang_unit ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="label-muted">Jenis Cuti</div>
                                    <div class="value-text text-primary fw-bold">{{ $pengajuan->jenis_cuti }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="label-muted">Tanggal Mulai</div>
                                    <div class="value-text">{{ \Carbon\Carbon::parse($pengajuan->start_date)->format('d F Y') }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="label-muted">Tanggal Selesai</div>
                                    <div class="value-text">{{ \Carbon\Carbon::parse($pengajuan->end_date)->format('d F Y') }}</div>
                                </div>
                                <div class="col-12">
                                    <div class="label-muted">Total Durasi</div>
                                    <div class="value-text">{{ $pengajuan->duration ?? (\Carbon\Carbon::parse($pengajuan->start_date)->diffInDays(\Carbon\Carbon::parse($pengajuan->end_date)) + 1) }} Hari</div>
                                </div>
                                <div class="col-12">
                                    <div class="label-muted">Alasan Cuti</div>
                                    <div class="p-4 bg-light rounded-3 border border-light text-dark" style="font-size: 0.95rem; line-height: 1.6; background-color: #F8FAFC;">
                                        {{ $pengajuan->reason }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="label-muted">Alamat Selama Cuti</div>
                                    <div class="value-text">{{ $pengajuan->address ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="label-muted">Nomor Telepon / HP</div>
                                    <div class="value-text">{{ $pengajuan->phone ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="label-muted">Nomor Telepon / HP Darurat</div>
                                    <div class="value-text">{{ $pengajuan->emergency_phone ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="label-muted">Hubungan dengan yang Bersangkutan</div>
                                    <div class="value-text">{{ $pengajuan->emergency_relationship ?? '-' }}</div>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-top border-dashed">
                                <div class="label-muted mb-3">Lampiran Dokumen</div>
                                @if($pengajuan->file_path)
                                    <div class="file-box">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-danger bg-opacity-10 text-danger rounded p-2">
                                                <i class="bi bi-file-earmark-pdf-fill fs-4"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark small">Dokumen Pendukung.pdf</div>
                                                <div class="text-muted" style="font-size: 0.7rem;">Lampiran pengajuan cuti</div>
                                            </div>
                                        </div>
                                        <a href="{{ route('admin.pengajuan.lampiran', $pengajuan->id) }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-medium">
                                            Buka <i class="bi bi-box-arrow-up-right ms-1"></i>
                                        </a>
                                    </div>
                                @else
                                    <span class="text-muted small fst-italic">Tidak ada dokumen dilampirkan.</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="decision-card-container">
                            <h6 class="fw-bold text-dark mb-1">Keputusan Pejabat</h6>
                            <p class="small text-muted mb-4">Tentukan status pengajuan ini.</p>

                            <form action="{{ route('admin.pengajuan.update', $pengajuan->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <input type="radio" name="status" id="opt_approve" value="approved" class="btn-check-custom" 
                                    {{ $pengajuan->status == 'approved' ? 'checked' : '' }}>
                                <label for="opt_approve" class="option-tile">
                                    <div class="tile-icon"><i class="bi bi-check-lg"></i></div>
                                    <div>
                                        <div class="tile-text">Setujui Pengajuan</div>
                                        <div class="tile-desc">Izinkan pegawai mengambil cuti</div>
                                    </div>
                                </label>

                                <input type="radio" name="status" id="opt_pending" value="pending" class="btn-check-custom" 
                                    {{ $pengajuan->status == 'pending' ? 'checked' : '' }}>
                                <label for="opt_pending" class="option-tile">
                                    <div class="tile-icon"><i class="bi bi-hourglass-split"></i></div>
                                    <div>
                                        <div class="tile-text">Tangguhkan</div>
                                        <div class="tile-desc">Menunggu konfirmasi lanjut</div>
                                    </div>
                                </label>

                                <input type="radio" name="status" id="opt_reject" value="rejected" class="btn-check-custom" 
                                    {{ $pengajuan->status == 'rejected' ? 'checked' : '' }}>
                                <label for="opt_reject" class="option-tile">
                                    <div class="tile-icon"><i class="bi bi-x-lg"></i></div>
                                    <div>
                                        <div class="tile-text">Tolak Pengajuan</div>
                                        <div class="tile-desc">Cuti tidak dapat diberikan</div>
                                    </div>
                                </label>

                                <div class="mt-4">
                                    <label class="label-muted">Catatan (Opsional)</label>
                                    <textarea class="form-control bg-white" name="rejection_reason" rows="3" placeholder="Tulis catatan persetujuan atau alasan penolakan..." style="font-size: 0.9rem;">{{ $pengajuan->rejection_reason }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm mt-4" style="background: var(--primary); border: none;">
                                    Simpan Keputusan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 text-center">
                <p class="text-muted small opacity-50">&copy; 2026 Pemerintah Kabupaten Semarang.</p>
            </div>
        </div>
@endsection
