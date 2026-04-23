@extends('layouts.admin')
@section('title', 'Laporan & Analytics - SIPERCUT')

@push('styles')
<style>
    /*
      PENTING:
      Jangan override layout global dari layouts.admin (body, :root, .sidebar, .main-content, dll)
      Biar sidebar (logo SIPERCUT / Kab. Semarang) tetap konsisten di semua halaman.
      Semua style di halaman ini di-scope ke .laporan-page
    */

    /* --- HERO & WRAPPER KHUSUS LAPORAN --- */
    .laporan-page .hero-banner {
        background: linear-gradient(135deg, var(--primary) 0%, #561616 100%);
        padding: 40px 40px 100px 40px;
        color: white;
        border-bottom-left-radius: 30px;
        border-bottom-right-radius: 30px;
    }

    .laporan-page .glass-profile {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 8px 16px;
        border-radius: 50px;
        color: white;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: 0.2s;
    }
    .laporan-page .glass-profile:hover { background: rgba(255, 255, 255, 0.2); }

    /* --- DASHBOARD CONTAINER & CARDS --- */
    .laporan-page .dashboard-container {
        padding: 0 40px 40px 40px;
        margin-top: -60px;
    }

    .laporan-page .card-content {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06);
        padding: 24px;
        margin-bottom: 24px;
        transition: transform 0.2s;
    }

    /* --- FILTERS & INPUTS --- */
    .laporan-page .form-label-custom {
        font-size: 0.75rem;
        font-weight: 700;
        color: #94A3B8;
        margin-bottom: 6px;
        text-transform: uppercase;
    }

    .laporan-page .form-control-custom,
    .laporan-page .form-select-custom {
        border: 1px solid #E2E8F0;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 0.9rem;
        background-color: #F8FAFC;
        color: #334155;
        transition: all 0.2s;
    }
    .laporan-page .form-control-custom:focus,
    .laporan-page .form-select-custom:focus {
        background-color: white;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(158, 42, 43, 0.1);
        outline: none;
    }

    /* Buttons */
    .laporan-page .btn-export {
        padding: 10px 16px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.85rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: 0.2s;
        height: 42px;
    }
    .laporan-page .btn-pdf         { background-color: #FEF2F2; color: #991B1B; border: 1px solid #FCA5A5; }
    .laporan-page .btn-pdf:hover   { background-color: #FEE2E2; transform: translateY(-2px); }
    .laporan-page .btn-excel       { background-color: #ECFDF5; color: #065F46; border: 1px solid #6EE7B7; }
    .laporan-page .btn-excel:hover { background-color: #D1FAE5; transform: translateY(-2px); }

    .laporan-page .btn-search       { background-color: var(--primary); color: white; border: none; border-radius: 10px; padding: 0 20px; font-weight: 600; transition: 0.2s; }
    .laporan-page .btn-search:hover { background-color: var(--primary-dark); }

    /* --- STAT CARDS --- */
    .laporan-page .stat-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid #F1F5F9;
        height: 100%;
        transition: 0.2s;
    }
    .laporan-page .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .laporan-page .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 16px;
    }

    .laporan-page .stat-label {
        font-size: 0.85rem;
        color: #64748B;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .laporan-page .stat-value {
        font-size: 2rem;
        font-weight: 800;
        color: #1E293B;
        line-height: 1.2;
        margin: 5px 0;
    }

    /* --- CHART & TABLE --- */
    .laporan-page .chart-title {
        font-weight: 700;
        font-size: 1.1rem;
        color: #1E293B;
        margin-bottom: 20px;
    }

    .laporan-page .progress-custom {
        height: 10px;
        border-radius: 20px;
        background-color: #F1F5F9;
        margin-bottom: 20px;
    }
    .laporan-page .progress-bar { border-radius: 20px; }

    .laporan-page .table-custom th {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748B;
        background-color: #F8FAFC;
        border-bottom: 1px solid #E2E8F0;
        padding: 16px;
        font-weight: 600;
    }
    .laporan-page .table-custom td {
        padding: 16px;
        vertical-align: middle;
        border-bottom: 1px solid #F1F5F9;
        font-size: 0.9rem;
    }

    .laporan-page .badge-stat {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.8rem;
        min-width: 40px;
        display: inline-block;
        text-align: center;
    }

    /* Mobile: HANYA atur bagian laporan, jangan sentuh sidebar/main-content global */
    .laporan-page .mobile-toggler {
        display: none;
        color: white;
        font-size: 1.5rem;
        background: none;
        border: none;
        margin-right: 15px;
    }

    @media (max-width: 992px) {
        .laporan-page .hero-banner        { padding: 20px 20px 80px 20px; }
        .laporan-page .dashboard-container { padding: 0 20px 20px; }
        .laporan-page .mobile-toggler     { display: block; }
        .laporan-page .stat-value         { font-size: 1.75rem; }
    }
</style>
@endpush

@section('content')
<div class="laporan-page">
    <div class="hero-banner">
        <div class="d-flex justify-content-between align-items-start">
            <div class="d-flex align-items-center">
                <button type="button" class="mobile-toggler" data-toggle-sidebar>
                    <i class="bi bi-list"></i>
                </button>
                <div>
                    <div class="text-white text-opacity-75 small mb-1">
                        Administrator <i class="bi bi-chevron-right mx-1" style="font-size: 0.7rem"></i> Laporan
                    </div>
                    <h2 class="fw-bold m-0 text-white">Analytics & Statistik</h2>
                    <div class="mt-2 text-white text-opacity-75 small">
                        <i class="bi bi-calendar3 me-1"></i> Data Periode: {{ $labelWaktu }}
                    </div>
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
                    <li>
                        <a class="dropdown-item rounded small" href="{{ route('admin.profil') }}">Profile Saya</a>
                    </li>
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
            <div class="d-flex align-items-center gap-2 text-danger fw-bold small mb-3">
                <i class="bi bi-sliders"></i> PENGATURAN LAPORAN
            </div>

            <form action="{{ route('admin.laporan') }}" method="GET">
                <div class="row g-3 align-items-end">

                    <div class="col-12 col-md-3">
                        <label class="form-label-custom">Periode Waktu</label>
                        <select name="filter" class="form-select-custom w-100" onchange="this.form.submit()">
                            <option value="1_bulan"   {{ $filter == '1_bulan'   ? 'selected' : '' }}>1 Bulan Terakhir</option>
                            <option value="3_bulan"   {{ $filter == '3_bulan'   ? 'selected' : '' }}>3 Bulan Terakhir</option>
                            <option value="tahun_ini" {{ $filter == 'tahun_ini' ? 'selected' : '' }}>Tahun Ini (Jan-Des)</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label-custom">Filter Bidang / Unit</label>
                        <select name="bidang_unit" id="selectBidang" class="form-select-custom w-100">
                            <option value="">Semua Bidang</option>
                            @if(isset($listBidang))
                                @foreach($listBidang as $bidang)
                                    @if($bidang)
                                        <option value="{{ $bidang }}" {{ request('bidang_unit') == $bidang ? 'selected' : '' }}>
                                            {{ $bidang }}
                                        </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label-custom">Cari Nama Pegawai</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"
                                  style="border-radius: 10px 0 0 10px; border-color: #E2E8F0;">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" name="search"
                                   class="form-control form-control-custom border-start-0 ps-0"
                                   placeholder="Ketik nama lalu Enter..."
                                   value="{{ request('search') }}"
                                   style="border-radius: 0 10px 10px 0;">
                        </div>
                    </div>

                    <div class="col-12 col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn-search flex-grow-1" style="height: 42px;">
                                Terapkan
                            </button>

                            <div class="dropdown">
                                <button class="btn btn-light border dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown"
                                        style="height: 42px; border-radius: 10px;">
                                    <i class="bi bi-download"></i>
                                </button>
                                <ul class="dropdown-menu shadow-sm border-0">
                                    <li>
                                        <a class="dropdown-item small"
                                           href="{{ route('admin.download.pdf', request()->all()) }}">
                                            <i class="bi bi-file-pdf text-danger me-2"></i>Export PDF
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item small"
                                           href="{{ route('admin.download.excel', request()->all()) }}">
                                            <i class="bi bi-file-excel text-success me-2"></i>Export Excel
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon-wrapper bg-light text-dark">
                        <i class="bi bi-files"></i>
                    </div>
                    <div class="stat-label">Total Pengajuan</div>
                    <div class="stat-value">{{ $total }}</div>
                    <div class="small text-muted">Periode ini</div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon-wrapper" style="background-color: #ECFDF5; color: #059669;">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <div class="stat-label">Tingkat Persetujuan</div>
                    <div class="stat-value text-success">{{ $persenApproved }}%</div>
                    <div class="small text-muted">{{ $approved }} Disetujui</div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon-wrapper" style="background-color: #FFFBEB; color: #D97706;">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="stat-label">Rata-rata Proses</div>
                    <div class="stat-value text-warning">{{ $avgDays }} <span class="fs-6 fw-normal text-muted">Hari</span></div>
                    <div class="small text-muted">Durasi Peninjauan</div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon-wrapper" style="background-color: #EFF6FF; color: #2563EB;">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div class="stat-label">Sedang Menunggu</div>
                    <div class="stat-value text-primary">{{ $pending }}</div>
                    <div class="small text-muted">{{ $persenPending }}% dari total</div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-6">
                <div class="card-content h-100">
                    <h6 class="chart-title">Status Pengajuan</h6>
                    <div class="mt-4">
                        <div class="d-flex justify-content-between small fw-bold mb-1">
                            <span>Disetujui</span> <span class="text-success">{{ $approved }}</span>
                        </div>
                        <div class="progress progress-custom">
                            <div class="progress-bar bg-success" style="width: {{ $persenApproved }}%"></div>
                        </div>

                        <div class="d-flex justify-content-between small fw-bold mb-1">
                            <span>Ditolak</span> <span class="text-danger">{{ $rejected }}</span>
                        </div>
                        <div class="progress progress-custom">
                            <div class="progress-bar bg-danger" style="width: {{ $persenRejected }}%"></div>
                        </div>

                        <div class="d-flex justify-content-between small fw-bold mb-1">
                            <span>Menunggu</span> <span class="text-warning">{{ $pending }}</span>
                        </div>
                        <div class="progress progress-custom">
                            <div class="progress-bar bg-warning" style="width: {{ $persenPending }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card-content h-100">
                    <h6 class="chart-title">Distribusi Jenis Cuti</h6>
                    <div style="height: 250px; position: relative;">
                        <canvas id="jenisCutiChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-content p-0 overflow-hidden">
            <div class="p-4 border-bottom border-light bg-white">
                <h6 class="chart-title mb-0">Statistik Detail per Unit Kerja / Pegawai</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-custom mb-0 w-100">
                    <thead>
                        <tr>
                            <th class="ps-4">Unit Kerja / Nama</th>
                            <th class="text-center">Disetujui</th>
                            <th class="text-center">Ditolak</th>
                            <th class="text-center">Menunggu</th>
                            <th class="text-center">Total</th>
                            <th class="text-end pe-4">Rate Setuju</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($unitStats as $stat)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">{{ $stat['name'] }}</td>
                                <td class="text-center"><span class="badge-stat" style="background: #ECFDF5; color: #059669;">{{ $stat['approved'] }}</span></td>
                                <td class="text-center"><span class="badge-stat" style="background: #FEF2F2; color: #DC2626;">{{ $stat['rejected'] }}</span></td>
                                <td class="text-center"><span class="badge-stat" style="background: #FFFBEB; color: #D97706;">{{ $stat['pending'] }}</span></td>
                                <td class="text-center fw-bold">{{ $stat['total'] }}</td>
                                <td class="text-end pe-4 fw-bold text-success">{{ $stat['rate'] }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 opacity-25"></i>
                                    <p class="small mt-2">Tidak ada data yang sesuai dengan filter Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5 text-center text-muted small opacity-50">
            &copy; 2026 Pemerintah Kabupaten Semarang.
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(document).ready(function() {

        $('#selectBidang').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Cari atau Pilih Bidang...',
            allowClear: true,
            dropdownParent: $('#selectBidang').parent()
        });

        $('#selectBidang').on('select2:select select2:clear', function(e) {
            $(this).closest('form').submit();
        });

        const labels     = {!! json_encode($chartLabels) !!};
        const dataValues = {!! json_encode($chartValues) !!};

        const ctx = document.getElementById('jenisCutiChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels.length ? labels : ['Belum ada data'],
                datasets: [{
                    data: dataValues.length ? dataValues : [1],
                    backgroundColor: ['#10B981', '#F59E0B', '#EF4444', '#3B82F6', '#6B7280', '#8B5CF6'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 20,
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 12 }
                        }
                    },
                    tooltip: { enabled: labels.length > 0 }
                }
            }
        });
    });
</script>
@endpush