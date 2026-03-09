@extends('layouts.admin')
@section('title', 'Edit Pegawai - SIPERCUT')

@push('styles')
<style>
        /* --- 1. CORE VARIABLES (SAMA PERSIS DENGAN INDEX) --- */
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

        /* --- SIDEBAR --- */
        .sidebar {
            width: var(--sidebar-width); height: 100vh; position: fixed;
            /* IMPORTANT: jangan override z-index jadi lebih kecil dari overlay di layouts.admin */
            background: #FFFFFF; border-right: 1px dashed #E2E8F0; z-index: 1060 !important;
            padding: 24px; display: flex; flex-direction: column; transition: 0.3s;
        }
        .sidebar-brand {
            display: flex; align-items: center; gap: 12px; padding-bottom: 30px;
            text-decoration: none; color: var(--primary);
        }
        .nav-label {
            font-size: 0.7rem; font-weight: 700; color: #94A3B8;
            text-transform: uppercase; letter-spacing: 0.08em; margin: 20px 0 10px 12px;
        }
        .nav-link {
            display: flex; align-items: center; gap: 12px; padding: 12px 16px;
            color: #64748B; border-radius: 12px; font-weight: 500;
            transition: all 0.2s; text-decoration: none; margin-bottom: 4px;
        }
        .nav-link:hover { background-color: #FEF2F2; color: var(--primary); }
        .nav-link.active {
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white; box-shadow: 0 4px 12px rgba(158, 42, 43, 0.3);
        }
        .nav-link.active i { color: white; }

        /* --- HERO & MAIN CONTENT --- */
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

        /* Glass Profile */
        .glass-profile {
            background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2); padding: 8px 16px;
            border-radius: 50px; color: white; display: flex; align-items: center; gap: 12px;
            cursor: pointer; transition: 0.2s;
        }
        .glass-profile:hover { background: rgba(255, 255, 255, 0.2); }

        /* --- FLOATING CONTENT --- */
        .dashboard-container { padding: 0 40px 40px 40px; margin-top: -80px; }

        .card-content {
            background: white; border-radius: 16px; border: 1px solid #F1F5F9;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06);
            padding: 32px;
        }

        /* --- FORM STYLES --- */
        .form-label {
            font-size: 0.85rem; font-weight: 700; color: #64748B; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.02em;
        }
        
        .form-control, .form-select {
            padding: 12px 16px; border-radius: 10px; border: 1px solid #E2E8F0;
            font-size: 0.95rem; color: #334155; background-color: #F8FAFC;
            transition: all 0.2s;
        }
        .form-control:focus, .form-select:focus {
            background-color: #fff; border-color: var(--secondary);
            box-shadow: 0 0 0 4px rgba(100, 116, 139, 0.1); outline: none;
        }
        
        .input-group-text {
            background-color: #F1F5F9; border: 1px solid #E2E8F0;
            border-radius: 10px 0 0 10px; color: #64748B;
        }
        /* Fix border radius for inputs inside groups */
        .input-group .form-control { border-top-left-radius: 0; border-bottom-left-radius: 0; }

        /* Buttons */
        .btn-cancel {
            padding: 12px 24px; border-radius: 10px; font-weight: 600; font-size: 0.9rem;
            background: #F1F5F9; color: #64748B; border: none; transition: 0.2s;
            text-decoration: none; display: inline-block;
        }
        .btn-cancel:hover { background: #E2E8F0; color: #334155; }

        .btn-submit {
            padding: 12px 24px; border-radius: 10px; font-weight: 600; font-size: 0.9rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white; border: none; transition: 0.2s; box-shadow: 0 4px 12px rgba(158, 42, 43, 0.2);
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(158, 42, 43, 0.3); }

        /* Mobile */
        .mobile-toggler { display: none; color: white; font-size: 1.5rem; background: none; border: none; margin-right: 15px; }
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .hero-banner { padding: 20px; height: auto; padding-bottom: 100px; }
            .dashboard-container { padding: 0 20px 20px; }
            .mobile-toggler { display: block; }
        }
    </style>
@endpush

@section('content')
<div class="hero-banner">
            <div class="d-flex justify-content-between align-items-start">
                <div class="d-flex align-items-center">
                    <button type="button" class="mobile-toggler" data-toggle-sidebar>
                        <i class="bi bi-list"></i>
                    </button>
                    <div>
                        <div class="text-white text-opacity-75 small mb-1 fw-medium">
                            Data Pegawai <i class="bi bi-chevron-right mx-1" style="font-size: 0.7rem"></i> Edit
                        </div>
                        <h2 class="fw-bold m-0 text-white">Edit Data Pegawai</h2>
                    </div>
                </div>
                
                <div class="dropdown">
                    <div class="glass-profile" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-white text-danger fw-bold d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                        </div>
                        <span class="d-none d-md-block small fw-medium">{{ Auth::user()->name ?? 'Admin' }}</span>
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
            <div class="card-content">
                
                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom border-light">
                    <a href="{{ route('admin.kelola_pegawai') }}" class="btn btn-light rounded-circle p-2 text-secondary" style="width: 40px; height: 40px; display: grid; place-items: center; transition: 0.2s;" onmouseover="this.style.background='#E2E8F0'" onmouseout="this.style.background='#F8FAFC'">
                        <i class="bi bi-arrow-left fs-5"></i>
                    </a>
                    <div>
                        <h5 class="fw-bold m-0 text-dark">Formulir Perubahan Data</h5>
                        <div class="text-muted small">Silakan perbarui informasi pegawai dengan teliti.</div>
                    </div>
                </div>

                <form id="formEditPegawai" action="{{ route('admin.pegawai.update', $pegawai->id) }}" method="POST">
                    @csrf
                    @method('PUT') 

                    <div class="row g-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $pegawai->name) }}" placeholder="Nama Lengkap" required>
                            </div>
                            @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label">NIP (Nomor Induk Pegawai)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                <input type="number" name="nip" class="form-control @error('nip') is-invalid @enderror" 
                                       value="{{ old('nip', $pegawai->nip) }}" placeholder="19xxxxxxxxxx" required>
                            </div>
                            @error('nip') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label">Alamat Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $pegawai->email) }}" placeholder="contoh@semarangkab.go.id" required>
                            </div>
                            @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label">Nomor WhatsApp / Telepon</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone', $pegawai->phone) }}" placeholder="08xxxxxxxxxx" required>
                            </div>
                            @error('phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label">Posisi / Jabatan</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-briefcase"></i></span>
                                <input type="text" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror" 
                                       value="{{ old('jabatan', $pegawai->jabatan) }}" placeholder="Contoh: Staf Teknis" required>
                            </div>
                            @error('jabatan') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label">Bidang / Unit Kerja</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-building"></i></span>
                                <input type="text" name="bidang_unit" class="form-control @error('bidang_unit') is-invalid @enderror" 
                                       value="{{ old('bidang_unit', $pegawai->bidang_unit) }}" placeholder="Contoh: Dinas Kominfo" required>
                            </div>
                            @error('bidang_unit') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="col-12">
                            <div class="p-3 rounded-3 bg-light border border-secondary-subtle">
                                <label class="form-label mb-2">Status Akun Sistem</label>
                                <select name="status" class="form-select w-auto">
                                    <option value="aktif" {{ old('status', $pegawai->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="nonaktif" {{ old('status', $pegawai->status) == 'nonaktif' ? 'selected' : '' }}>Non-aktif</option>
                                </select>
                                <div class="d-flex align-items-start gap-2 mt-2 text-muted small">
                                    <i class="bi bi-info-circle-fill mt-1 text-primary"></i>
                                    <span style="line-height: 1.4;">Akun dengan status <strong>Non-aktif</strong> tidak akan bisa login ke dalam sistem aplikasi SIPERCUT. Pastikan status benar sebelum menyimpan.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-5 pt-3 border-top border-light">
                        <a href="{{ route('admin.kelola_pegawai') }}" class="btn-cancel">Batal</a>
                        <button type="button" onclick="simpanPerubahan()" class="btn-submit">
                            <i class="bi bi-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
            
            <div class="mt-5 text-center">
                <p class="text-muted small opacity-50">&copy; 2026 Pemerintah Kabupaten Semarang.</p>
            </div>

        </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function simpanPerubahan() {
            const form = document.getElementById('formEditPegawai');
            
            // Validasi HTML5 dasar
            if(!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            Swal.fire({
                title: 'Simpan Perubahan?',
                text: "Pastikan data pegawai yang Anda masukkan sudah benar.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#9E2A2B', 
                cancelButtonColor: '#64748B',
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Loading state
                    Swal.fire({
                        title: 'Menyimpan...',
                        html: 'Mohon tunggu sebentar.',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading() }
                    });
                    form.submit();
                }
            })
        }
</script>
@endpush
