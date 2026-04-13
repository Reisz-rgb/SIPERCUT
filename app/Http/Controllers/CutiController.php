<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCutiRequest;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use App\Models\Supervisor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CutiController extends Controller
{
    // =========================================================================
    // USER – FORM PENGAJUAN
    // =========================================================================

    public function create()
    {
        $user         = Auth::user();
        $leaveBalance = LeaveBalance::calculateTotalAvailable($user->id, now()->year);
        [$workYears, $workMonths] = $this->resolveWorkDuration($user->join_date);

        // Ambil semua atasan aktif, dikelompokkan per unit kerja untuk dropdown
        $supervisors = Supervisor::active()
            ->orderBy('unit_kerja')
            ->orderBy('nama')
            ->get()
            ->groupBy('unit_kerja');

        return view('user.pengajuan-cuti', compact(
            'user', 'leaveBalance', 'workYears', 'workMonths', 'supervisors'
        ));
    }

    public function store(StoreCutiRequest $request)
    {
        $user = Auth::user();

        // Cek saldo Cuti Tahunan (butuh data user, tidak bisa di Form Request)
        if ($request->isCutiTahunan()) {
            $leaveBalance = LeaveBalance::calculateTotalAvailable($user->id, now()->year);

            if ($request->lama_hari > $leaveBalance['total_available']) {
                return back()
                    ->withErrors(['lama_hari' => "Saldo cuti tidak mencukupi. Tersedia: {$leaveBalance['total_available']} hari"])
                    ->withInput();
            }
        }

        $leaveRequest = LeaveRequest::create([
            'user_id'                => $user->id,
            'supervisor_id'          => $request->supervisor_id,
            'jenis_cuti'             => $request->jenis_cuti,
            'start_date'             => $request->tanggal_mulai,
            'end_date'               => $request->tanggal_selesai,
            'duration'               => $request->lama_hari,
            'reason'                 => $request->alasan,
            'address'                => $request->alamat_cuti,
            'phone'                  => $this->sanitizePhone($request->no_telepon),
            'emergency_phone'        => $this->sanitizePhone($request->no_telepon_darurat),
            'emergency_relationship' => $request->hubungan_darurat,
            'notes'                  => $request->catatan_tambahan,
            'file_path'              => $this->uploadDocument($request),
            'status'                 => LeaveRequest::STATUS_PENDING,
        ]);

        $refNumber = 'CUTI-' . now()->year . '-' . str_pad($leaveRequest->id, 4, '0', STR_PAD_LEFT);

        return view('user.PengajuanSukses', compact('refNumber'));
    }

    // =========================================================================
    // ADMIN – DETAIL & UPDATE STATUS
    // =========================================================================

    public function show($id)
    {
        $pengajuan = LeaveRequest::with(['user', 'supervisor'])->findOrFail($id);
        return view('admin.detail_pengajuan', compact('pengajuan'));
    }

    public function updateStatus(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        $validated = $request->validate([
            'keputusan'    => 'required|in:disetujui,tidak_disetujui',
            'pertimbangan' => 'nullable|string',
        ]);

        $status = $validated['keputusan'] === 'disetujui'
            ? LeaveRequest::STATUS_APPROVED
            : LeaveRequest::STATUS_REJECTED;

        $leaveRequest->update([
            'status'           => $status,
            'rejection_reason' => $validated['pertimbangan'],
        ]);

        $this->syncAnnualLeaveBalance($leaveRequest);

        return response()->json([
            'message' => 'Status berhasil diperbarui',
            'status'  => $status,
        ]);
    }

    public function downloadDocument($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        abort_unless(
            Auth::user()->isAdmin() || Auth::id() === $leaveRequest->user_id,
            403
        );

        abort_if(!$leaveRequest->file_path, 404);

        // Cek file benar-benar ada di storage
        abort_unless(Storage::disk('private')->exists($leaveRequest->file_path), 404);

        return Storage::disk('private')->download($leaveRequest->file_path);
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private function resolveWorkDuration(?string $joinDate): array
    {
        $join   = $joinDate ? Carbon::parse($joinDate) : now()->subYears(5);
        $years  = (int) floor($join->diffInYears(now()));
        $months = (int) floor($join->copy()->addYears($years)->diffInMonths(now()));

        return [$years, $months];
    }

    private function uploadDocument(Request $request): ?string
    {
        if (!$request->hasFile('dokumen_pendukung')) {
            return null;
        }

        $file = $request->file('dokumen_pendukung');

        // UUID agar nama file tidak bisa ditebak atau di-traverse
        $safeFileName = \Str::uuid() . '.' . strtolower($file->getClientOriginalExtension());

        return $file->storeAs('leave_documents', $safeFileName, 'private');
    }

    private function sanitizePhone(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }

    private function syncAnnualLeaveBalance(LeaveRequest $leaveRequest): void
    {
        if ($leaveRequest->jenis_cuti === LeaveRequest::JENIS_TAHUNAN) {
            $year = Carbon::parse($leaveRequest->start_date)->year;
            LeaveBalance::recalculateAnnualBalances((int) $leaveRequest->user_id, $year);
        }
    }
}