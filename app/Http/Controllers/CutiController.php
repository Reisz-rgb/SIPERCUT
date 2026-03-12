<?php

namespace App\Http\Controllers;

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
            'user',
            'leaveBalance',
            'workYears',
            'workMonths',
            'supervisors'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            $this->storeRules(),
            $this->storeMessages()
        );

        $user         = Auth::user();
        $leaveBalance = LeaveBalance::calculateTotalAvailable($user->id, now()->year);

        // Cek saldo khusus Cuti Tahunan
        if (
            $validated['jenis_cuti'] === 'Cuti Tahunan' &&
            $validated['lama_hari'] > $leaveBalance['total_available']
        ) {
            return back()
                ->withErrors(['lama_hari' => "Saldo cuti tidak mencukupi. Tersedia: {$leaveBalance['total_available']} hari"])
                ->withInput();
        }

        $leaveRequest = LeaveRequest::create([
            'user_id'       => $user->id,
            'supervisor_id' => $validated['supervisor_id'],
            'jenis_cuti'    => $validated['jenis_cuti'],
            'start_date'    => $validated['tanggal_mulai'],
            'end_date'      => $validated['tanggal_selesai'],
            'duration'      => $validated['lama_hari'],
            'reason'        => $validated['alasan'],
            'address'       => $validated['alamat_cuti'],
            'phone'         => $this->sanitizePhone($request->no_telepon),
            'notes'         => $validated['catatan_tambahan'],
            'file_path'     => $this->uploadDocument($request),
            'status'        => LeaveRequest::STATUS_PENDING,
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
            'keputusan'     => 'required|in:disetujui,tidak_disetujui',
            'pertimbangan'  => 'nullable|string',
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

        $file     = $request->file('dokumen_pendukung');
        $fileName = time() . '_' . $file->getClientOriginalName();

        return $file->storeAs('leave_documents', $fileName, 'public');
    }

    private function sanitizePhone(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }

    private function syncAnnualLeaveBalance(LeaveRequest $leaveRequest): void
    {
        if ($leaveRequest->jenis_cuti === 'Cuti Tahunan') {
            $year = Carbon::parse($leaveRequest->start_date)->year;
            LeaveBalance::recalculateAnnualBalances((int) $leaveRequest->user_id, $year);
        }
    }

    private function storeRules(): array
    {
        return [
            'supervisor_id'      => 'required|exists:supervisors,id',
            'jenis_cuti'         => 'required|string',
            'alasan'             => 'required|string|min:20',
            'lama_hari'          => 'required|integer|min:1',
            'tanggal_mulai'      => 'required|date',
            'tanggal_selesai'    => 'required|date|after_or_equal:tanggal_mulai',
            'alamat_cuti'        => 'required|string',
            'no_telepon'         => 'required|string',
            'catatan_tambahan'   => 'nullable|string',
            'dokumen_pendukung'  => 'nullable|file|mimes:pdf,doc,docx,jpg,png,xls,xlsx|max:5120',
        ];
    }

    private function storeMessages(): array
    {
        return [
            'supervisor_id.required'         => 'Atasan langsung wajib dipilih',
            'supervisor_id.exists'           => 'Atasan langsung yang dipilih tidak valid',
            'jenis_cuti.required'            => 'Jenis cuti wajib dipilih',
            'alasan.required'                => 'Alasan cuti wajib diisi',
            'alasan.min'                     => 'Mohon berikan alasan yang lebih mendalam (minimal 20 karakter).',
            'lama_hari.required'             => 'Lama hari cuti wajib diisi',
            'lama_hari.min'                  => 'Lama cuti minimal 1 hari',
            'tanggal_mulai.required'         => 'Tanggal mulai wajib diisi',
            'tanggal_selesai.required'       => 'Tanggal selesai wajib diisi',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
            'alamat_cuti.required'           => 'Alamat selama cuti wajib diisi',
            'no_telepon.required'            => 'Nomor telepon wajib diisi',
            'dokumen_pendukung.max'          => 'Ukuran file maksimal 5MB',
        ];
    }
}