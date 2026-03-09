@extends('layouts.admin')
@section('title', 'Profil Saya - SIPERCUT')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
        :root {
            --primary-red: #9E2A2B; /* Merah yang konsisten dengan halaman sebelumnya */
            --primary-hover: #7F1D1D;
            --bg-body: #F3F4F6;
            --text-main: #111827;
            --text-muted: #6B7280;
        }

        body { 
            background-color: var(--bg-body); 
            font-family: 'Inter', sans-serif; 
            color: var(--text-main);
            padding-bottom: 40px;
        }

        /* Header Navigation */
        .top-header {
            background-color: var(--primary-red);
            color: white;
            padding: 16px 24px;
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .btn-back { 
            color: white; text-decoration: none; font-size: 1.25rem; 
            display: flex; align-items: center; transition: transform 0.2s;
        }
        .btn-back:hover { color: #e0e0e0; transform: translateX(-3px); }

        /* Container Limit */
        .profile-container {
            max-width: 800px;
            margin: 24px auto;
            padding: 0 16px;
        }

        /* Kartu Merah Profil */
        .profile-banner-card {
            background: linear-gradient(135deg, var(--primary-red) 0%, #7F1D1D 100%);
            color: white;
            border-radius: 16px;
            padding: 32px;
            display: flex;
            align-items: center;
            gap: 24px;
            margin-bottom: 24px;
            box-shadow: 0 10px 25px -5px rgba(158, 42, 43, 0.4);
        }
        .profile-avatar-large {
            width: 90px; height: 90px;
            background-color: white;
            color: var(--primary-red);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.2rem; font-weight: 700;
            flex-shrink: 0;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        /* Form Styling */
        .card-clean {
            background: white; border: 1px solid #E5E7EB;
            border-radius: 16px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            padding: 32px; margin-bottom: 24px;
        }
        
        .form-label-custom {
            font-size: 0.8rem; color: var(--text-muted); font-weight: 600;
            margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;
            display: flex; align-items: center; gap: 8px;
        }
        
        .form-control-custom {
            background-color: #F9FAFB; border: 1px solid #E5E7EB;
            padding: 12px 16px; border-radius: 8px; color: var(--text-main);
            transition: all 0.2s;
        }
        .form-control-custom:focus {
            background-color: white; border-color: var(--primary-red);
            box-shadow: 0 0 0 3px rgba(158, 42, 43, 0.1); outline: none;
        }
        /* Style input file */
        input[type="file"]::file-selector-button {
            background-color: #E5E7EB; border: none; padding: 8px 12px; margin-right: 12px;
            border-radius: 6px; cursor: pointer; color: var(--text-main); font-weight: 500;
        }

        /* Info Box */
        .info-note {
            background-color: #EFF6FF; border: 1px solid #DBEAFE;
            color: #1E40AF; padding: 20px; border-radius: 12px;
            font-size: 0.9rem; line-height: 1.6; display: flex; gap: 12px;
        }

        /* Buttons Area */
        .action-buttons {
            display: flex; justify-content: flex-end; gap: 12px;
        }
        
        .btn-custom {
            padding: 12px 24px; border-radius: 8px; font-weight: 600; font-size: 0.95rem;
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            border: none; transition: 0.2s; text-decoration: none;
        }
        
        .btn-save { background-color: #10B981; color: white; }
        .btn-save:hover { background-color: #059669; transform: translateY(-1px); }
        
        .btn-logout { background-color: white; border: 1px solid #FECACA; color: #DC2626; }
        .btn-logout:hover { background-color: #FEF2F2; border-color: #DC2626; }

        /* Alert Animasi */
        .success-alert {
            position: fixed; top: 30px; left: 50%; transform: translate(-50%, -150%);
            background-color: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0;
            padding: 12px 24px; border-radius: 50px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            z-index: 1050; opacity: 0; transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            display: flex; align-items: center; gap: 10px; font-weight: 600;
        }
        .success-alert.show { transform: translate(-50%, 0); opacity: 1; }

        /* --- RESPONSIVE MEDIA QUERIES --- */
        @media (max-width: 576px) {
            .profile-banner-card {
                flex-direction: column; text-align: center;
                padding: 24px 20px;
            }
            .profile-avatar-large { margin-bottom: 8px; }
            
            .card-clean { padding: 20px; }
            
            .action-buttons {
                flex-direction: column; /* Tombol menumpuk */
            }
            .btn-custom { width: 100%; } /* Tombol full width */
            
            .btn-logout { order: 2; } /* Logout di paling bawah */
            .btn-save { order: 1; }
        }
    </style>
@endpush

@section('content')
<div class="top-header">
        <a href="{{ route('admin.dashboard') }}" class="btn-back"><i class="bi bi-arrow-left"></i></a>
        <span>Profil Saya</span>
    </div>

    <div id="successAlert" class="success-alert">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <span>Profil berhasil diperbarui!</span>
    </div>

    <div class="profile-container">
        
        <div class="profile-banner-card">
            <div class="profile-avatar-large">{{ substr(Auth::user()->name ?? 'A', 0, 2) }}</div>
            <div>
                <h4 class="mb-1 fw-bold">{{ Auth::user()->name ?? 'Nama Pengguna' }}</h4>
                <div style="opacity: 0.9;">{{ ucfirst(Auth::user()->role) }} - Kabupaten Semarang</div>
            </div>
        </div>

        <div class="card-clean">
            <form id="profileForm">
                <div class="mb-4">
                    <label class="form-label-custom">
                        <i class="bi bi-person text-danger"></i> Nama Lengkap
                    </label>
                    <input type="text" class="form-control form-control-custom" value="{{ Auth::user()->name }}">
                </div>

                <div class="mb-4">
                    <label class="form-label-custom">
                        <i class="bi bi-envelope text-danger"></i> Alamat Email
                    </label>
                    <input type="email" class="form-control form-control-custom" value="{{ Auth::user()->email }}">
                </div>

                <div class="mb-2">
                    <label class="form-label-custom">
                        <i class="bi bi-image text-danger"></i> Foto Profil
                    </label>
                    <input type="file" class="form-control form-control-custom w-100">
                    <div class="form-text text-muted small mt-2">Format: JPG, PNG. Maksimal 2MB.</div>
                </div>
            </form>
        </div>

        <div class="info-note mb-4">
            <i class="bi bi-info-circle-fill fs-5 mt-1"></i>
            <div>
                <strong>Catatan Penting</strong><br>
                Perubahan pada NIP atau Unit Kerja hanya dapat dilakukan oleh Administrator Utama. Hubungi admin jika terdapat kesalahan data kepegawaian.
            </div>
        </div>

        <div class="action-buttons">
            <form action="{{ route('logout') }}" method="POST" class="d-grid d-sm-block w-100 w-sm-auto">
                @csrf
                <button type="submit" class="btn-custom btn-logout w-100">
                    <i class="bi bi-box-arrow-right"></i> Log Out
                </button>
            </form>
            
            <button type="button" class="btn-custom btn-save w-100 w-sm-auto" onclick="simpanProfil()">
                <i class="bi bi-check-lg"></i> Simpan Perubahan
            </button>
        </div>

    </div>
@endsection

@push('scripts')
<script>
function simpanProfil() {
            const alertBox = document.getElementById('successAlert');
            
            // Animasi Loading Simpel pada button (opsional)
            const btn = document.querySelector('.btn-save');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
            btn.disabled = true;

            // Simulasi proses simpan (2 detik)
            setTimeout(() => {
                // Kembalikan tombol
                btn.innerHTML = originalText;
                btn.disabled = false;

                // Munculkan Alert
                alertBox.classList.add('show');

                // Hilangkan Alert
                setTimeout(() => {
                    alertBox.classList.remove('show');
                }, 3000);
            }, 800);
        }
</script>
@endpush
