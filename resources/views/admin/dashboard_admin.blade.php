@extends('layouts.admin')
@section('title', 'Dashboard Admin - SIPERCUT')

@section('content')
<div class="hero-banner">
            <div class="d-flex justify-content-between align-items-start">
                <div class="d-flex align-items-center">
                    <button class="mobile-toggler">
                        <i class="bi bi-list"></i>
                    </button>
                    <div>
                        <h2 class="fw-bold m-0 text-white">Dashboard Overview</h2>
                        <p class="text-white text-opacity-75 m-0 small mt-1">Pantau aktivitas cuti pegawai secara real-time.</p>
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
            <div class="row g-4 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card-stat">
                        <div class="stat-icon" style="background: #EFF6FF; color: #3B82F6;">
                            <i class="bi bi-files"></i>
                        </div>
                        <div class="text-muted small fw-bold text-uppercase">Total Pengajuan</div>
                        <div class="fs-2 fw-bold text-dark mt-1">{{ $totalPengajuan ?? 0 }}</div>
                        <div class="small text-muted mt-2"><i class="bi bi-calendar me-1"></i> Sepanjang waktu</div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card-stat">
                        <div class="stat-icon" style="background: #F0FDF4; color: #16A34A;">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <div class="text-muted small fw-bold text-uppercase">Disetujui</div>
                        <div class="fs-2 fw-bold text-dark mt-1">{{ $disetujui ?? 0 }}</div>
                        <div class="small text-success mt-2"><i class="bi bi-graph-up-arrow me-1"></i> Sukses</div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card-stat">
                        <div class="stat-icon" style="background: #FFF7ED; color: #EA580C;">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="text-muted small fw-bold text-uppercase">Perlu Review</div>
                        <div class="fs-2 fw-bold text-dark mt-1">{{ $menunggu ?? 0 }}</div>
                        <div class="small text-warning mt-2"><i class="bi bi-exclamation-circle me-1"></i> Menunggu</div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card-stat">
                        <div class="stat-icon" style="background: #FEF2F2; color: #DC2626;">
                            <i class="bi bi-x-octagon"></i>
                        </div>
                        <div class="text-muted small fw-bold text-uppercase">Ditolak</div>
                        <div class="fs-2 fw-bold text-dark mt-1">{{ $ditolak ?? 0 }}</div>
                        <div class="small text-danger mt-2">Tidak disetujui</div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card-stat">
                        <div class="stat-icon" style="background: #FFF7ED; color: #D97706;">
                            <i class="bi bi-person-check"></i>
                        </div>
                        <div class="text-muted small fw-bold text-uppercase">Menunggu Aktivasi</div>
                        <div class="fs-2 fw-bold text-dark mt-1">{{ $pendingActivation->count() }}</div>
                        <div class="small mt-2" style="color: #D97706;">
                            <i class="bi bi-clock me-1"></i> Perlu diaktifkan
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card-content mb-4 h-100">
                        <div class="card-header-custom bg-white">
                            <h6 class="m-0 fw-bold text-dark">Statistik Bulanan</h6>
                        </div>
                        <div class="p-4" style="height: 320px;">
                            <canvas id="statsChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card-content h-100">
                        <div class="card-header-custom bg-white">
                            <h6 class="m-0 fw-bold text-dark">
                                Menunggu Persetujuan
                                @if(isset($menunggu) && $menunggu > 0)
                                    <span class="badge bg-danger rounded-pill ms-1">{{ $menunggu }}</span>
                                @endif
                            </h6>
                            <a href="{{ route('admin.kelola_pengajuan') }}" class="small text-decoration-none fw-bold" style="color: var(--primary);">View All</a>
                        </div>
                        
                        <div class="list-group list-group-flush">
                            @forelse($pendingRequests ?? [] as $request)
                                <div class="list-item-custom d-flex gap-3 align-items-center">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; color: var(--secondary); font-weight: 700;">
                                        {{ substr(optional($request->user)->name ?? '?', 0, 1) }}
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <div class="fw-bold text-dark text-truncate">{{ optional($request->user)->name ?? 'User Tidak Dikenal' }}</div>
                                        <div class="small text-muted">
                                            {{ $request->jenis_cuti }} &bull; {{ $request->duration }} Hari
                                        </div>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.pengajuan.show', $request->id) }}" class="btn btn-sm btn-light border text-secondary fw-medium rounded-pill px-3">Review</a>
                                    </div>
                                </div>
                            @empty
                                <div class="p-5 text-center">
                                    <i class="bi bi-clipboard-check fs-1 text-muted opacity-25"></i>
                                    <p class="text-muted small m-0 mt-2">Tidak ada pengajuan baru.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Panel Aktivasi Akun --}}
            @if($pendingActivation->count() > 0)
            <div class="row g-4 mt-0">
                <div class="col-12">
                    <div class="card-content">
                        <div class="card-header-custom bg-white d-flex justify-content-between align-items-center">
                            <h6 class="m-0 fw-bold text-dark">
                                <i class="bi bi-person-check me-2" style="color: #D97706;"></i>
                                Akun Menunggu Aktivasi
                                <span class="badge rounded-pill ms-1" style="background:#FFF7ED; color:#D97706; border: 1px solid #FED7AA;">
                                    {{ $pendingActivation->count() }}
                                </span>
                            </h6>
                            <a href="{{ route('admin.kelola_pegawai') }}" class="small text-decoration-none fw-bold" style="color: var(--primary);">
                                Kelola Pegawai
                            </a>
                        </div>

                        <div class="list-group list-group-flush">
                            @foreach($pendingActivation as $u)
                            <div class="list-item-custom d-flex gap-3 align-items-center">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center flex-shrink-0 fw-bold"
                                    style="width: 42px; height: 42px; color: #64748B;">
                                    {{ substr($u->name, 0, 1) }}
                                </div>

                                <div style="flex: 1; min-width: 0;">
                                    <div class="fw-bold text-dark text-truncate">{{ $u->name }}</div>
                                    <div class="small text-muted">
                                        {{ $u->nip }}
                                        @if($u->bidang_unit)
                                            &bull; {{ $u->bidang_unit }}
                                        @endif
                                    </div>
                                    <div class="small text-muted">
                                        Mendaftar: {{ $u->created_at->diffForHumans() }}
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    {{-- Tombol Aktifkan --}}
                                    <form action="{{ route('admin.pegawai.activate', $u->id) }}" method="POST">
                                        @csrf
                                        <button type="button"
                                            onclick="konfirmasiAktivasi(this.form, '{{ addslashes($u->name) }}')"
                                            class="btn btn-sm fw-bold px-3 rounded-pill"
                                            style="background:#F0FDF4; color:#15803D; border: 1px solid #DCFCE7;">
                                            <i class="bi bi-check-lg me-1"></i>Aktifkan
                                        </button>
                                    </form>

                                    {{-- Tombol Edit (jika perlu cek data dulu) --}}
                                    <a href="{{ route('admin.pegawai.edit', $u->id) }}"
                                    class="btn btn-sm fw-bold px-3 rounded-pill"
                                    style="background:#F8FAFC; color:#64748B; border: 1px solid #E2E8F0;">
                                        <i class="bi bi-pencil-square me-1"></i>Edit
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="mt-5 text-center">
                <p class="text-muted small opacity-50">&copy; 2026 Pemerintah Kabupaten Semarang. Hak Cipta Dilindungi.</p>
            </div>
        </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
        // Chart Configuration
        const ctx = document.getElementById('statsChart').getContext('2d');
        const labels = @json($chartLabels ?? ['Jan', 'Feb', 'Mar']);
        const approvedData = @json($dataApproved ?? [0, 0, 0]);
        const pendingData = @json($dataPending ?? [0, 0, 0]);
        const rejectedData = @json($dataRejected ?? [0, 0, 0]);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Disetujui', data: approvedData, backgroundColor: '#16A34A', borderRadius: 6, barPercentage: 0.6 },
                    { label: 'Menunggu', data: pendingData, backgroundColor: '#EA580C', borderRadius: 6, barPercentage: 0.6 },
                    { label: 'Ditolak', data: rejectedData, backgroundColor: '#DC2626', borderRadius: 6, barPercentage: 0.6 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20, boxWidth: 8 } },
                    tooltip: { backgroundColor: '#1e293b', padding: 12, cornerRadius: 8 }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        border: { display: false }, 
                        grid: { borderDash: [4, 4], color: '#f1f5f9' },
                        ticks: { font: { size: 11 } }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });

        function konfirmasiAktivasi(form, nama) {
            Swal.fire({
                title: 'Aktifkan Akun?',
                html: `Akun <strong>${nama}</strong> akan diaktifkan dan dapat login ke sistem.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#15803D',
                cancelButtonColor: '#64748B',
                confirmButtonText: 'Ya, Aktifkan',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        }
</script>
@endpush