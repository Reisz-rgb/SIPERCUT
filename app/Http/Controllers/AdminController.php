<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Export
use App\Exports\LaporanExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    /* =====================================================
     * 1. DOWNLOAD LAPORAN
     * ===================================================== */

    public function downloadExcel(Request $request)
    {
        return Excel::download(
            new LaporanExport($request),
            'laporan_cuti.xlsx'
        );
    }

    public function downloadPdf(Request $request)
    {
        $query = LeaveRequest::with('user');

        $filter = $request->input('filter', '1_bulan');

        if ($filter == '1_bulan') {
            $startDate = Carbon::now()->subMonth();
            $titlePeriode = "1 Bulan Terakhir";
        } elseif ($filter == '3_bulan') {
            $startDate = Carbon::now()->subMonths(3);
            $titlePeriode = "3 Bulan Terakhir";
        } else {
            $startDate = Carbon::now()->startOfYear();
            $titlePeriode = "Tahun Ini (" . date('Y') . ")";
        }

        $query->where('created_at', '>=', $startDate);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }
        
        if ($request->filled('bidang_unit')) {
            $bidang = $request->bidang_unit;
            $query->whereHas('user', function($q) use ($bidang) {
                $q->where('bidang_unit', $bidang);
            });
        }

        $data = $query->get();

        return Pdf::loadView('admin.laporan_pdf', compact('data', 'titlePeriode'))
            ->download('laporan_cuti.pdf');
    }

    /* =====================================================
     * 2. DASHBOARD ADMIN (SAFE + OPTIMIZED)
     * ===================================================== */

    public function dashboard()
    {
        $totalPegawai = User::where('role', 'user')->count();
        $listPegawai  = User::where('role', 'user')->orderBy('name')->get();

        /**
         * SAFETY NET
         * Kalau migration belum dijalankan
         */
        if (!Schema::hasTable('leave_requests')) {
            return view('admin.dashboard_admin', [
                'totalPengajuan' => 0,
                'disetujui' => 0,
                'menunggu' => 0,
                'ditolak' => 0,
                'totalPegawai' => $totalPegawai,
                'pendingRequests' => collect(),
                'recentActivities' => collect(),
                'listPegawai' => $listPegawai,
                'chartLabels' => [],
                'dataApproved' => [],
                'dataRejected' => [],
                'dataPending' => [],
            ]);
        }

        /* =====================
         * A. STATISTIK KARTU
         * ===================== */
        $stats = LeaveRequest::selectRaw("
                COUNT(*) as total,
                SUM(status = 'approved') as approved,
                SUM(status = 'pending') as pending,
                SUM(status = 'rejected') as rejected
            ")->first();

        /* =====================
         * B. PENDING TERBARU
         * ===================== */
        $pendingRequests = LeaveRequest::with('user')
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        /* =====================
         * C. AKTIVITAS TERAKHIR
         * ===================== */
        $recentActivities = LeaveRequest::with('user')
            ->latest('updated_at')
            ->limit(3)
            ->get();

        /* =====================
         * D. GRAFIK 6 BULAN (1 QUERY)
         * ===================== */
        $start = Carbon::now()->subMonths(5)->startOfMonth();

        $chartRaw = LeaveRequest::selectRaw("
                YEAR(start_date) as year,
                MONTH(start_date) as month,
                SUM(status = 'approved') as approved,
                SUM(status = 'rejected') as rejected,
                SUM(status = 'pending') as pending
            ")
            ->where('start_date', '>=', $start)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->keyBy(fn ($row) => "{$row->year}-{$row->month}");

        $chartLabels = [];
        $dataApproved = [];
        $dataRejected = [];
        $dataPending  = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $key  = $date->year . '-' . $date->month;

            $chartLabels[] = $date->translatedFormat('M');

            $dataApproved[] = $chartRaw[$key]->approved ?? 0;
            $dataRejected[] = $chartRaw[$key]->rejected ?? 0;
            $dataPending[]  = $chartRaw[$key]->pending ?? 0;
        }

        return view('admin.dashboard_admin', [
            'totalPengajuan' => $stats->total,
            'disetujui' => $stats->approved,
            'menunggu' => $stats->pending,
            'ditolak' => $stats->rejected,
            'totalPegawai' => $totalPegawai,
            'pendingRequests' => $pendingRequests,
            'recentActivities' => $recentActivities,
            'listPegawai' => $listPegawai,
            'chartLabels' => $chartLabels,
            'dataApproved' => $dataApproved,
            'dataRejected' => $dataRejected,
            'dataPending' => $dataPending,
        ]);
    }

    /* =====================================================
     * 3. DETAIL & UPDATE STATUS
     * ===================================================== */

    public function show($id)
    {
        $pengajuan = LeaveRequest::with('user')->findOrFail($id);
        return view('admin.detail_pengajuan', compact('pengajuan'));
    }

    // INI YANG SUDAH DIPERBAIKI (MENGGUNAKAN 'status', BUKAN 'keputusan')
    public function updateStatus($id, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,pending,rejected',
            'rejection_reason' => 'nullable|string'
        ]);

        $pengajuan = LeaveRequest::findOrFail($id);

        $pengajuan->status = $validated['status'];
        $pengajuan->rejection_reason =
            $validated['status'] === 'rejected'
                ? $validated['rejection_reason']
                : null;

        $pengajuan->save();

        return back()->with('success', 'Keputusan berhasil disimpan!');
    }

    public function kelolaPengajuan(Request $request)
    {
        // 1. Siapkan Query
        $query = LeaveRequest::with('user');

        // 2. Logika Pencarian (Nama atau NIP)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        // 3. Logika Filter Status
        if ($request->filled('status') && $request->status !== 'Semua') {
            $query->where('status', $request->status);
        }

        // 4. Ambil data dengan Pagination (10 data per halaman)
        $pengajuan = $query->latest()->paginate(10); 

        return view('admin.kelola_pengajuan', compact('pengajuan'));
    }

    /* =====================================================
     * 4. HALAMAN LAPORAN & ANALYTICS (FIXED: bidang_unit)
     * ===================================================== */
    public function laporan(Request $request)
    {
        $listBidang = User::whereNotNull('bidang_unit')
                        ->where('bidang_unit', '!=', '') // Pastikan tidak kosong
                        ->distinct()
                        ->pluck('bidang_unit');

        // 1. Filter Rentang Waktu
        $filter = $request->input('filter', '1_bulan'); // Default 1 bulan
        $query = LeaveRequest::query();

        if ($filter == '1_bulan') {
            $startDate = Carbon::now()->subMonth();
            $labelWaktu = "1 Bulan Terakhir";
        } elseif ($filter == '3_bulan') {
            $startDate = Carbon::now()->subMonths(3);
            $labelWaktu = "3 Bulan Terakhir";
        } else {
            $startDate = Carbon::now()->startOfYear();
            $labelWaktu = "Tahun Ini (" . date('Y') . ")";
        }

        // Terapkan filter tanggal ke query dasar
        $query->where('created_at', '>=', $startDate);

        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('bidang_unit')) {
            $bidang = $request->bidang_unit;
            $query->whereHas('user', function($q) use ($bidang) {
                $q->where('bidang_unit', $bidang);
            });
        }
        // --------------------------------------

        // 2. Hitung Statistik Card (Top) - Berdasarkan data yang SUDAH difilter
        $total = $query->count();
        
        $approved = (clone $query)->where('status', 'approved')->count();
        $rejected = (clone $query)->where('status', 'rejected')->count();
        $pending  = (clone $query)->where('status', 'pending')->count();

        // Persentase (cegah error division by zero)
        $persenApproved = $total > 0 ? round(($approved / $total) * 100, 1) : 0;
        $persenRejected = $total > 0 ? round(($rejected / $total) * 100, 1) : 0;
        $persenPending  = $total > 0 ? round(($pending / $total) * 100, 1) : 0;

        // Rata-rata Proses
        $avgSeconds = (clone $query)
            ->whereIn('status', ['approved', 'rejected'])
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, updated_at)) as avg_time')
            ->value('avg_time');
        
        $avgDays = $avgSeconds ? round($avgSeconds / 86400, 1) : 0; // Konversi detik ke hari

        // 3. Data Chart Pie (Jenis Cuti)
        $jenisCutiStats = (clone $query)
            ->select('jenis_cuti', DB::raw('count(*) as total'))
            ->groupBy('jenis_cuti')
            ->pluck('total', 'jenis_cuti');
            
        $chartLabels = $jenisCutiStats->keys();
        $chartValues = $jenisCutiStats->values();

        // 4. Data Tabel (Statistik per Bidang Unit)
        $rawRequests = (clone $query)->with('user')->get();

        $unitStats = $rawRequests->groupBy(function($item) {
            // Fix: Grouping berdasarkan 'bidang_unit'
            return $item->user->bidang_unit ?? 'Umum';
        })->map(function($group, $unitName) {
            $totalUnit = $group->count();
            $appUnit   = $group->where('status', 'approved')->count();
            $rejUnit   = $group->where('status', 'rejected')->count();
            $pendUnit  = $group->where('status', 'pending')->count();
            
            return [
                'name' => $unitName,
                'total' => $totalUnit,
                'approved' => $appUnit,
                'rejected' => $rejUnit,
                'pending' => $pendUnit,
                'rate' => $totalUnit > 0 ? round(($appUnit / $totalUnit) * 100, 1) : 0
            ];
        })->sortByDesc('total'); // Urutkan dari yang paling banyak mengajukan

        return view('admin.laporan', compact(
            'filter', 'labelWaktu',
            'listBidang', 
            'total', 'approved', 'rejected', 'pending',
            'persenApproved', 'persenRejected', 'persenPending',
            'avgDays',
            'chartLabels', 'chartValues',
            'unitStats'
        ));
    }
}