<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        return Excel::download(new LaporanExport($request), 'laporan_cuti.xlsx');
    }

    public function downloadPdf(Request $request)
    {
        $query = LeaveRequest::with('user');

        $filter = $request->input('filter', '1_bulan');

        if ($filter === '1_bulan') {
            $startDate = Carbon::now()->subMonth();
            $titlePeriode = "1 Bulan Terakhir";
        } elseif ($filter === '3_bulan') {
            $startDate = Carbon::now()->subMonths(3);
            $titlePeriode = "3 Bulan Terakhir";
        } else {
            $startDate = Carbon::now()->startOfYear();
            $titlePeriode = "Tahun Ini (" . date('Y') . ")";
        }

        $query->where('created_at', '>=', $startDate);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('bidang_unit')) {
            $bidang = $request->bidang_unit;
            $query->whereHas('user', function ($q) use ($bidang) {
                $q->where('bidang_unit', $bidang);
            });
        }

        $data = $query->get();

        return Pdf::loadView('admin.laporan_pdf', compact('data', 'titlePeriode'))
            ->download('laporan_cuti.pdf');
    }

    /* =====================================================
     * 2. DASHBOARD ADMIN
     * ===================================================== */

    public function dashboard()
    {
        $totalPegawai = User::where('role', 'user')->count();
        $listPegawai  = User::where('role', 'user')->orderBy('name')->get();

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

        $stats = LeaveRequest::selectRaw("
                COUNT(*) as total,
                SUM(status = 'approved' OR status = 'disetujui') as approved,
                SUM(status = 'pending' OR status = 'diproses' OR status = 'tertunda') as pending,
                SUM(status = 'rejected' OR status = 'ditolak') as rejected
            ")->first();

        $pendingRequests = LeaveRequest::with('user')
            ->whereIn('status', ['pending', 'diproses', 'tertunda'])
            ->latest()
            ->limit(5)
            ->get();

        $recentActivities = LeaveRequest::with('user')
            ->latest('updated_at')
            ->limit(3)
            ->get();

        $start = Carbon::now()->subMonths(5)->startOfMonth();

        $chartRaw = LeaveRequest::selectRaw("
                YEAR(start_date) as year,
                MONTH(start_date) as month,
                SUM(status = 'approved' OR status = 'disetujui') as approved,
                SUM(status = 'rejected' OR status = 'ditolak') as rejected,
                SUM(status = 'pending' OR status = 'diproses' OR status = 'tertunda') as pending
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
            'totalPengajuan' => (int) ($stats->total ?? 0),
            'disetujui' => (int) ($stats->approved ?? 0),
            'menunggu' => (int) ($stats->pending ?? 0),
            'ditolak' => (int) ($stats->rejected ?? 0),
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

    /**
     * PENTING:
     * Setelah status diubah, kita SYNC saldo dari riwayat approved,
     * jadi DITOLAK tidak akan ikut terhitung.
     */
    public function updateStatus($id, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,pending,rejected,disetujui,diproses,ditolak,tertunda',
            'rejection_reason' => 'nullable|string',
        ]);

        $pengajuan = LeaveRequest::findOrFail($id);

        $oldStatus = (string) $pengajuan->status;
        $newStatus = (string) $validated['status'];

        try {
            DB::transaction(function () use ($pengajuan, $oldStatus, $newStatus, $validated) {

                $pengajuan->status = $newStatus;
                $pengajuan->rejection_reason = in_array($newStatus, ['rejected', 'ditolak'], true)
                    ? ($validated['rejection_reason'] ?? null)
                    : null;
                $pengajuan->save();

                // Tahun hitung saldo berdasarkan start_date
                $year = $this->getLeaveYear($pengajuan);

                // Setelah apapun perubahan status => sync ulang dari approved (fix utama)
                LeaveBalance::syncYearFromRequests((int) $pengajuan->user_id, (int) $year);
            });

            return back()->with('success', 'Keputusan berhasil disimpan!');
        } catch (\Throwable $e) {
            Log::error('Gagal update status cuti', [
                'leave_request_id' => $pengajuan->id ?? null,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Gagal menyimpan keputusan: ' . $e->getMessage());
        }
    }

    private function getLeaveYear(LeaveRequest $leaveRequest): int
    {
        try {
            if (!empty($leaveRequest->start_date)) {
                return Carbon::parse($leaveRequest->start_date)->year;
            }
        } catch (\Throwable $e) {
            // ignore
        }
        return (int) now()->year;
    }

    /* =====================================================
     * 4. KELOLA PENGAJUAN
     * ===================================================== */

    public function kelolaPengajuan(Request $request)
    {
        $query = LeaveRequest::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'Semua') {
            $query->where('status', $request->status);
        }

        $pengajuan = $query->latest()->paginate(10);

        return view('admin.kelola_pengajuan', compact('pengajuan'));
    }

    /* =====================================================
     * 5. LAPORAN & ANALYTICS
     * ===================================================== */

    public function laporan(Request $request)
    {
        $listBidang = User::whereNotNull('bidang_unit')
            ->where('bidang_unit', '!=', '')
            ->distinct()
            ->pluck('bidang_unit');

        $filter = $request->input('filter', '1_bulan');
        $query = LeaveRequest::query();

        if ($filter === '1_bulan') {
            $startDate = Carbon::now()->subMonth();
            $labelWaktu = "1 Bulan Terakhir";
        } elseif ($filter === '3_bulan') {
            $startDate = Carbon::now()->subMonths(3);
            $labelWaktu = "3 Bulan Terakhir";
        } else {
            $startDate = Carbon::now()->startOfYear();
            $labelWaktu = "Tahun Ini (" . date('Y') . ")";
        }

        $query->where('created_at', '>=', $startDate);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('bidang_unit')) {
            $bidang = $request->bidang_unit;
            $query->whereHas('user', function ($q) use ($bidang) {
                $q->where('bidang_unit', $bidang);
            });
        }

        $total = (clone $query)->count();
        $approved = (clone $query)->whereIn('status', ['approved', 'disetujui'])->count();
        $rejected = (clone $query)->whereIn('status', ['rejected', 'ditolak'])->count();
        $pending  = (clone $query)->whereIn('status', ['pending', 'diproses', 'tertunda'])->count();

        $persenApproved = $total > 0 ? round(($approved / $total) * 100, 1) : 0;
        $persenRejected = $total > 0 ? round(($rejected / $total) * 100, 1) : 0;
        $persenPending  = $total > 0 ? round(($pending / $total) * 100, 1) : 0;

        $avgSeconds = (clone $query)
            ->whereIn('status', ['approved', 'rejected', 'disetujui', 'ditolak'])
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, updated_at)) as avg_time')
            ->value('avg_time');

        $avgDays = $avgSeconds ? round($avgSeconds / 86400, 1) : 0;

        $jenisCutiStats = (clone $query)
            ->select('jenis_cuti', DB::raw('count(*) as total'))
            ->groupBy('jenis_cuti')
            ->pluck('total', 'jenis_cuti');

        $chartLabels = $jenisCutiStats->keys();
        $chartValues = $jenisCutiStats->values();

        $rawRequests = (clone $query)->with('user')->get();

        $unitStats = $rawRequests->groupBy(function ($item) {
            return $item->user->bidang_unit ?? 'Umum';
        })->map(function ($group, $unitName) {
            $totalUnit = $group->count();
            $appUnit   = $group->whereIn('status', ['approved', 'disetujui'])->count();
            $rejUnit   = $group->whereIn('status', ['rejected', 'ditolak'])->count();
            $pendUnit  = $group->whereIn('status', ['pending', 'diproses', 'tertunda'])->count();

            return [
                'name' => $unitName,
                'total' => $totalUnit,
                'approved' => $appUnit,
                'rejected' => $rejUnit,
                'pending' => $pendUnit,
                'rate' => $totalUnit > 0 ? round(($appUnit / $totalUnit) * 100, 1) : 0,
            ];
        })->sortByDesc('total');

        return view('admin.laporan', compact(
            'filter',
            'labelWaktu',
            'listBidang',
            'total',
            'approved',
            'rejected',
            'pending',
            'persenApproved',
            'persenRejected',
            'persenPending',
            'avgDays',
            'chartLabels',
            'chartValues',
            'unitStats'
        ));
    }
}