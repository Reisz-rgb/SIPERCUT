<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use App\Exports\LaporanExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    // =========================================================================
    // CONSTANTS
    // =========================================================================

    private const FILTER_PERIODS = [
        '1_bulan'  => ['months' => 1,  'label' => '1 Bulan Terakhir'],
        '3_bulan'  => ['months' => 3,  'label' => '3 Bulan Terakhir'],
        'tahun_ini' => ['year_start' => true, 'label' => null], // label dinamis
    ];

    // =========================================================================
    // DOWNLOAD LAPORAN
    // =========================================================================

    public function downloadExcel(Request $request)
    {
        return Excel::download(new LaporanExport($request), 'laporan_cuti.xlsx');
    }

    public function downloadPdf(Request $request)
    {
        [$startDate, $titlePeriode] = $this->resolvePeriodFilter($request->input('filter', '1_bulan'));

        $data = LeaveRequest::with('user')
            ->where('created_at', '>=', $startDate)
            ->when($request->filled('search'), fn ($q) =>
                $q->whereHas('user', fn ($u) => $u->where('name', 'LIKE', "%{$request->search}%"))
            )
            ->when($request->filled('bidang_unit'), fn ($q) =>
                $q->whereHas('user', fn ($u) => $u->where('bidang_unit', $request->bidang_unit))
            )
            ->get();

        return Pdf::loadView('admin.laporan_pdf', compact('data', 'titlePeriode'))
            ->download('laporan_cuti.pdf');
    }

    public function downloadLampiran($id)
    {
        $pengajuan = LeaveRequest::findOrFail($id);

        if (!$pengajuan->file_path) {
            return back()->with('error', 'Tidak ada lampiran.');
        }

        abort_unless(Storage::disk('public')->exists($pengajuan->file_path), 404);

        return Storage::disk('public')->download(
            $pengajuan->file_path,
            basename($pengajuan->file_path)
        );
    }

    // =========================================================================
    // DASHBOARD
    // =========================================================================

    public function dashboard()
    {
        $totalPegawai = User::where('role', 'user')->count();
        $listPegawai  = User::where('role', 'user')->orderBy('name')->get();

        if (!Schema::hasTable('leave_requests')) {
            return $this->dashboardEmptyResponse($totalPegawai, $listPegawai);
        }

        $stats           = $this->getLeaveStats();
        $pendingRequests = $this->getLatestPendingRequests();
        $recentActivities = $this->getRecentActivities();
        [$chartLabels, $dataApproved, $dataRejected, $dataPending] = $this->getChartData();

        return view('admin.dashboard_admin', [
            'totalPengajuan'   => $stats->total,
            'disetujui'        => $stats->approved,
            'menunggu'         => $stats->pending,
            'ditolak'          => $stats->rejected,
            'totalPegawai'     => $totalPegawai,
            'pendingRequests'  => $pendingRequests,
            'recentActivities' => $recentActivities,
            'listPegawai'      => $listPegawai,
            'chartLabels'      => $chartLabels,
            'dataApproved'     => $dataApproved,
            'dataRejected'     => $dataRejected,
            'dataPending'      => $dataPending,
        ]);
    }

    // =========================================================================
    // KELOLA PENGAJUAN
    // =========================================================================

    public function kelolaPengajuan(Request $request)
    {
        $pengajuan = LeaveRequest::with('user')
            ->when($request->filled('search'), fn ($q) =>
                $q->whereHas('user', fn ($u) =>
                    $u->where('name', 'like', "%{$request->search}%")
                      ->orWhere('nip', 'like', "%{$request->search}%")
                )
            )
            ->when(
                $request->filled('status') && $request->status !== 'Semua',
                fn ($q) => $q->where('status', $request->status)
            )
            ->latest()
            ->paginate(10);

        return view('admin.kelola_pengajuan', compact('pengajuan'));
    }

    public function show($id)
    {
        $pengajuan = LeaveRequest::with('user')->findOrFail($id);
        return view('admin.detail_pengajuan', compact('pengajuan'));
    }

    public function updateStatus($id, Request $request)
    {
        $validated = $request->validate([
            'status'           => 'required|in:approved,pending,rejected',
            'rejection_reason' => 'nullable|string',
        ]);

        $pengajuan = LeaveRequest::findOrFail($id);
        $pengajuan->update([
            'status'           => $validated['status'],
            'rejection_reason' => $validated['rejection_reason'] ?? null,
        ]);

        $this->syncAnnualLeaveBalance($pengajuan);

        return back()->with('success', 'Keputusan berhasil disimpan!');
    }

    // =========================================================================
    // LAPORAN & ANALYTICS
    // =========================================================================

    public function laporan(Request $request)
    {
        $filter = $request->input('filter', '1_bulan');
        [$startDate, $labelWaktu] = $this->resolvePeriodFilter($filter);

        $listBidang = User::whereNotNull('bidang_unit')
            ->where('bidang_unit', '!=', '')
            ->distinct()
            ->pluck('bidang_unit');

        $baseQuery = LeaveRequest::query()
            ->where('created_at', '>=', $startDate)
            ->when($request->filled('search'), fn ($q) =>
                $q->whereHas('user', fn ($u) => $u->where('name', 'LIKE', "%{$request->search}%"))
            )
            ->when($request->filled('bidang_unit'), fn ($q) =>
                $q->whereHas('user', fn ($u) => $u->where('bidang_unit', $request->bidang_unit))
            );

        // Statistik kartu
        $total    = $baseQuery->count();
        $approved = (clone $baseQuery)->where('status', 'approved')->count();
        $rejected = (clone $baseQuery)->where('status', 'rejected')->count();
        $pending  = (clone $baseQuery)->where('status', 'pending')->count();

        $persenApproved = $this->percentage($approved, $total);
        $persenRejected = $this->percentage($rejected, $total);
        $persenPending  = $this->percentage($pending,  $total);

        // Rata-rata waktu proses (detik → hari)
        $avgSeconds = (clone $baseQuery)
            ->whereIn('status', ['approved', 'rejected'])
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, updated_at)) as avg_time')
            ->value('avg_time');
        $avgDays = $avgSeconds ? round($avgSeconds / 86400, 1) : 0;

        // Chart pie: jenis cuti
        $jenisCutiStats = (clone $baseQuery)
            ->select('jenis_cuti', DB::raw('count(*) as total'))
            ->groupBy('jenis_cuti')
            ->pluck('total', 'jenis_cuti');

        $chartLabels = $jenisCutiStats->keys();
        $chartValues = $jenisCutiStats->values();

        // Tabel: statistik per bidang unit
        $unitStats = (clone $baseQuery)
            ->with('user')
            ->get()
            ->groupBy(fn ($item) => $item->user->bidang_unit ?? 'Umum')
            ->map(fn ($group, $unitName) => [
                'name'     => $unitName,
                'total'    => $group->count(),
                'approved' => $group->where('status', 'approved')->count(),
                'rejected' => $group->where('status', 'rejected')->count(),
                'pending'  => $group->where('status', 'pending')->count(),
                'rate'     => $this->percentage($group->where('status', 'approved')->count(), $group->count()),
            ])
            ->sortByDesc('total');

        return view('admin.laporan', compact(
            'filter', 'labelWaktu', 'listBidang',
            'total', 'approved', 'rejected', 'pending',
            'persenApproved', 'persenRejected', 'persenPending',
            'avgDays', 'chartLabels', 'chartValues', 'unitStats'
        ));
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Resolve filter periode → [Carbon $startDate, string $label]
     */
    private function resolvePeriodFilter(string $filter): array
    {
        return match ($filter) {
            '1_bulan'  => [Carbon::now()->subMonth(),      '1 Bulan Terakhir'],
            '3_bulan'  => [Carbon::now()->subMonths(3),    '3 Bulan Terakhir'],
            default    => [Carbon::now()->startOfYear(),   'Tahun Ini (' . date('Y') . ')'],
        };
    }

    /**
     * Hitung persentase, safe dari division by zero.
     */
    private function percentage(int $part, int $total): float
    {
        return $total > 0 ? round(($part / $total) * 100, 1) : 0;
    }

    /**
     * Sinkronkan saldo cuti tahunan.
     */
    private function syncAnnualLeaveBalance(LeaveRequest $pengajuan): void
    {
        // Trim dan case-insensitive agar tidak gagal karena spasi/huruf
        $jenis = trim($pengajuan->jenis_cuti);
        $year  = Carbon::parse($pengajuan->start_date)->year;

        // Selalu recalculate untuk semua jenis cuti yang mempengaruhi kuota tahunan
        if (in_array($jenis, ['Cuti Tahunan', 'Cuti Besar'], true)) {
            LeaveBalance::recalculateAnnualBalances((int) $pengajuan->user_id, $year);
        }
    }

    /**
     * Aggregate statistik leave request (total, approved, pending, rejected).
     */
    private function getLeaveStats()
    {
        return LeaveRequest::selectRaw("
            COUNT(*) as total,
            SUM(status = 'approved') as approved,
            SUM(status = 'pending')  as pending,
            SUM(status = 'rejected') as rejected
        ")->first();
    }

    private function getLatestPendingRequests(int $limit = 5)
    {
        return LeaveRequest::with('user')
            ->where('status', 'pending')
            ->latest()
            ->limit($limit)
            ->get();
    }

    private function getRecentActivities(int $limit = 3)
    {
        return LeaveRequest::with('user')
            ->latest('updated_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Bangun data grafik 6 bulan terakhir.
     * Returns [$labels, $approved, $rejected, $pending]
     */
    private function getChartData(): array
    {
        $start = Carbon::now()->subMonths(5)->startOfMonth();

        $chartRaw = LeaveRequest::selectRaw("
                YEAR(start_date)  as year,
                MONTH(start_date) as month,
                SUM(status = 'approved') as approved,
                SUM(status = 'rejected') as rejected,
                SUM(status = 'pending')  as pending
            ")
            ->where('start_date', '>=', $start)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->keyBy(fn ($row) => "{$row->year}-{$row->month}");

        $labels   = [];
        $approved = [];
        $rejected = [];
        $pending  = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $key  = "{$date->year}-{$date->month}";

            $labels[]   = $date->translatedFormat('M');
            $approved[] = $chartRaw[$key]->approved ?? 0;
            $rejected[] = $chartRaw[$key]->rejected ?? 0;
            $pending[]  = $chartRaw[$key]->pending  ?? 0;
        }

        return [$labels, $approved, $rejected, $pending];
    }

    /**
     * Response dashboard kosong saat tabel belum ada.
     */
    private function dashboardEmptyResponse(int $totalPegawai, $listPegawai)
    {
        return view('admin.dashboard_admin', [
            'totalPengajuan'   => 0,
            'disetujui'        => 0,
            'menunggu'         => 0,
            'ditolak'          => 0,
            'totalPegawai'     => $totalPegawai,
            'pendingRequests'  => collect(),
            'recentActivities' => collect(),
            'listPegawai'      => $listPegawai,
            'chartLabels'      => [],
            'dataApproved'     => [],
            'dataRejected'     => [],
            'dataPending'      => [],
        ]);
    }
}