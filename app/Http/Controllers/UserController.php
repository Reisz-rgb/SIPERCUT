<?php

namespace App\Http\Controllers;

use App\Helpers\MergeFieldReplacer;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\TemplateProcessor;

class UserController extends Controller
{
    // =========================================================================
    // DASHBOARD
    // =========================================================================

    public function dashboard()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $balanceData     = $this->resolveLeaveBalance($user->id);
        $recentLeaves    = $this->getRecentLeaves($user->id);
        $submissionStats = $this->getSubmissionStats($user->id);

        return view('user.UserDashboard', array_merge(
            compact('user', 'recentLeaves'),
            $balanceData,
            $submissionStats
        ));
    }

    // =========================================================================
    // PROFIL
    // =========================================================================

    public function profile()
    {
        return view('user.ProfilPage', ['user' => Auth::user()]);
    }

    public function editProfile()
    {
        return view('user.EditProfilPage', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'nullable|email|unique:users,email,' . $user->id,
            'phone'       => 'required|string|unique:users,phone,' . $user->id,
            'jabatan'     => 'nullable|string|max:255',
            'bidang_unit' => 'nullable|string|max:255',
        ], [
            'name.required'  => 'Nama wajib diisi',
            'email.unique'   => 'Email sudah digunakan',
            'phone.required' => 'Nomor HP wajib diisi',
            'phone.unique'   => 'Nomor HP sudah digunakan',
        ]);

        $user->update(array_merge($validated, [
            'phone' => $this->sanitizePhone($request->phone),
        ]));

        return redirect()->route('user.profil')->with('status', 'sukses');
    }

    // =========================================================================
    // PASSWORD
    // =========================================================================

    public function showChangePassword()
    {
        return view('user.ChangePassword');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Password lama wajib diisi',
            'password.required'         => 'Password baru wajib diisi',
            'password.min'              => 'Password minimal 6 karakter',
            'password.confirmed'        => 'Konfirmasi password tidak cocok',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Password lama tidak sesuai.'])
                ->withInput();
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('user.profil')->with('status', 'password_updated');
    }

    // =========================================================================
    // RIWAYAT CUTI
    // =========================================================================

    public function history(Request $request)
    {
        $user   = Auth::user();
        $status = $request->get('status', 'all');

        $statusMap = [
            'approved' => LeaveRequest::STATUS_APPROVED,
            'pending'  => LeaveRequest::STATUS_PENDING,
            'rejected' => LeaveRequest::STATUS_REJECTED,
        ];

        $leaves = LeaveRequest::where('user_id', $user->id)
            ->when(
                $status !== 'all' && isset($statusMap[$status]),
                fn ($q) => $q->where('status', $statusMap[$status])
            )
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.RiwayatPage', compact('leaves', 'user', 'status'));
    }

    // =========================================================================
    // DOWNLOAD SURAT CUTI
    // =========================================================================

    public function downloadSuratCuti($id)
    {
        $this->clearOutputBuffers();

        try {
            // Eager-load user dan supervisor sekaligus
            $leave      = LeaveRequest::with(['user', 'supervisor'])->findOrFail($id);
            $user       = $leave->user;
            $supervisor = $leave->supervisor; // bisa null bila data lama

            $templatePath = $this->findTemplatePath();
            abort_unless($templatePath, 404, 'Template tidak ditemukan');

            // ── Tahap 1: isi placeholder ${...} via PhpWord TemplateProcessor ──
            $processor = $this->fillTemplate($templatePath, $leave, $user);

            $fileName = 'Surat_Cuti_'
                      . preg_replace('/[^A-Za-z0-9_\-]/', '_', $user->name)
                      . '_' . date('Ymd_His') . '.docx';

            $tempFile = $this->saveTempFile($processor, $fileName);

            // ── Tahap 2: isi MERGEFIELD atasan via MergeFieldReplacer ──
            $tempFile = $this->fillSupervisorMergeFields($tempFile, $supervisor);

            $this->clearOutputBuffers();

            return response()
                ->download($tempFile, $fileName, $this->downloadHeaders())
                ->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('downloadSuratCuti error: ' . $e->getMessage());

            if (isset($tempFile) && file_exists($tempFile)) {
                @unlink($tempFile);
            }

            return back()->with('error', 'Gagal membuat surat cuti: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Isi MERGEFIELD atasan langsung (Nama_Bidang, Atasan_Langsung,
     * NIP_Atasan_langsung) menggunakan MergeFieldReplacer.
     * Menggunakan saveInPlace() — timpa file temp langsung tanpa copy.
     */
    private function fillSupervisorMergeFields(string $filePath, $supervisor): string
    {
        // Pastikan file benar-benar ada sebelum diproses
        if (!file_exists($filePath)) {
            throw new \RuntimeException(
                "File temp tidak ditemukan sebelum MergeField: {$filePath}"
            );
        }

        $namaAtasan    = $supervisor?->nama ?? '-';
        $nipAtasan     = $supervisor?->nip ?? '-';
        $jabatanAtasan = $supervisor?->jabatan ?? $supervisor?->unit_kerja ?? '-';
        $nipFormatted  = $this->formatNip($nipAtasan);

        $replacer = new MergeFieldReplacer($filePath);
        $replacer
            ->setValue('Atasan_Langsung',     $namaAtasan)
            ->setValue('NIP_Atasan_langsung', $nipFormatted)
            ->setValue('Nama_Bidang',         $jabatanAtasan);

        // saveInPlace: timpa file yang sudah ada, tidak butuh copy
        return $replacer->saveInPlace();
    }

    /**
     * Format NIP 18 digit menjadi "XXXXXXXX XXXXXX X XXX".
     * Bila format tidak dikenali, kembalikan apa adanya.
     */
    private function formatNip(string $nip): string
    {
        $digits = preg_replace('/\D/', '', $nip);

        if (strlen($digits) === 18) {
            return substr($digits, 0, 8) . ' '
                 . substr($digits, 8, 6)  . ' '
                 . substr($digits, 14, 1) . ' '
                 . substr($digits, 15, 3);
        }

        return $nip;
    }

    private function resolveLeaveBalance(int $userId): array
    {
        try {
            $balance = LeaveBalance::calculateTotalAvailable($userId, now()->year);

            return [
                'totalQuota'     => $balance['n']['remaining'],
                'usedLeave'      => $balance['n']['used'],
                'remainingLeave' => $balance['total_available'],
                'annualQuota'    => $balance['n']['quota'],
                'maxAvailable'   => $balance['n']['quota']
                                  + ($balance['n1']['bonus'] ?? 0)
                                  + ($balance['n2']['bonus'] ?? 0),
            ];
        } catch (\Throwable $e) {
            Log::error('LeaveBalance error: ' . $e->getMessage());

            return [
                'totalQuota'     => 12,
                'usedLeave'      => 0,
                'remainingLeave' => 12,
                'annualQuota'    => 12,
                'maxAvailable'   => 12,
            ];
        }
    }

    private function getRecentLeaves(int $userId)
    {
        return LeaveRequest::where('user_id', $userId)
            ->latest()
            ->limit(3)
            ->get();
    }

    private function getSubmissionStats(int $userId): array
    {
        $stats = LeaveRequest::where('user_id', $userId)
            ->selectRaw("
                COUNT(*) as total,
                SUM(status = 'approved') as approved,
                SUM(status = 'pending')  as pending,
                SUM(status = 'rejected') as rejected
            ")
            ->first();

        $total = (int) $stats->total;

        if ($total === 0) {
            return ['approvedPercent' => 0, 'pendingPercent' => 0, 'rejectedPercent' => 0];
        }

        return [
            'approvedPercent' => round(($stats->approved / $total) * 100),
            'pendingPercent'  => round(($stats->pending  / $total) * 100),
            'rejectedPercent' => round(($stats->rejected / $total) * 100),
        ];
    }

    private function sanitizePhone(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }

    private function sanitizeDocxValue(?string $text): string
    {
        if (empty($text)) {
            return '-';
        }

        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', (string) $text);
        return htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function findTemplatePath(): ?string
    {
        $candidates = [
            storage_path('app/template/surat_cuti_template.docx'),
            storage_path('app/template/template/surat_cuti_template.docx'),
            base_path('storage/app/template/surat_cuti_template.docx'),
            base_path('storage/app/template/template/surat_cuti_template.docx'),
        ];

        foreach ($candidates as $path) {
            if (is_file($path) && is_readable($path)) {
                return $path;
            }
        }

        Log::error('Template not found. Tried: ' . implode(' | ', $candidates));
        return null;
    }

    private function fillTemplate(string $templatePath, $leave, $user): TemplateProcessor
    {
        $s  = fn ($v) => $this->sanitizeDocxValue($v);
        $tp = new TemplateProcessor($templatePath);

        $tp->setValue('TANGGAL_SURAT', Carbon::now()->locale('id')->translatedFormat('j F Y'));
        $tp->setValue('NAMA',          $s($user->name));
        $tp->setValue('NIP', $this->formatNip($user->nip ?? ''));
        $tp->setValue('JABATAN',       $s($user->jabatan));
        $masaKerja = '-';
        $joinDate = $user->join_date;
        if (!$joinDate && $user->nip) {
            $digits = preg_replace('/\D/', '', $user->nip);
            if (strlen($digits) === 18) {
                $tmt = substr($digits, 8, 6); // YYYYMM
                try {
                    $joinDate = \Carbon\Carbon::createFromFormat('Ym', $tmt)->startOfMonth();
                } catch (\Throwable $e) {
                    $joinDate = null;
                }
            }
        }

        if ($joinDate) {
            $diff = \Carbon\Carbon::parse($joinDate)->diff(\Carbon\Carbon::now());
            $masaKerja = $diff->y . ' Tahun ' . $diff->m . ' Bulan';
        }

        $tp->setValue('MASA_KERJA', $masaKerja);
        $tp->setValue('MASA_KERJA', $masaKerja);
        $tp->setValue('UNIT_KERJA', $s($user->bidang_unit));

        // Normalize nama jenis cuti dari DB
       $jenisMap = [
        'Cuti Tahunan'                   => 'CUTI_TAHUNAN',
        'Cuti Besar'                     => 'CUTI_BESAR',
        'Cuti Sakit'                     => 'CUTI_SAKIT',
        'Cuti Melahirkan'                => 'CUTI_MELAHIRKAN',
        'Cuti Karena Alasan Penting'     => 'CUTI_KARENA_ALASAN_PENTING',   // ← fix
        'Cuti Alasan Penting'            => 'CUTI_KARENA_ALASAN_PENTING',   // ← fallback lama
        'Cuti di luar tanggungan Negara' => 'CUTI_DI_LUAR_TANGGUNGAN_NEGARA', // ← fix
        'Cuti Luar Tanggungan'           => 'CUTI_DI_LUAR_TANGGUNGAN_NEGARA', // ← fallback lama
        ];

        foreach ($jenisMap as $jenis => $placeholder) {
            $tp->setValue($placeholder, trim($leave->jenis_cuti) === $jenis ? 'X' : ' ');
        }

        $tp->setValue('ALASAN',          $s($leave->reason));
        $tp->setValue('LAMA_HARI',       $leave->duration . ' hari');
        $tp->setValue('TANGGAL_MULAI',   Carbon::parse($leave->start_date)->locale('id')->translatedFormat('j F Y'));
        $tp->setValue('TANGGAL_SELESAI', Carbon::parse($leave->end_date)->locale('id')->translatedFormat('j F Y'));
        try {
            $leaveYear = \Carbon\Carbon::parse($leave->start_date)->year;
            
            // Selalu recalculate dulu (hanya Cuti Tahunan yang dipotong)
            $balance = \App\Models\LeaveBalance::recalculateAnnualBalances($user->id, $leaveYear);

            $sisaN2  = (int)($balance['n2']['remaining'] ?? 0);
            $sisaN1  = (int)($balance['n1']['remaining'] ?? 0);
            $sisaN   = (int)($balance['n']['remaining']  ?? 0);
            $bonusN2 = (int) floor($sisaN2 / 2);
            $bonusN1 = (int) floor($sisaN1 / 2);
            $totalN  = $sisaN + $bonusN1 + $bonusN2; // dengan filter fix: 12+6+6=24

            $tp->setValue('SISA_N-2', (string) $sisaN2);
            $tp->setValue('SISA_N-1', (string) $sisaN1);
            $tp->setValue('N',        (string) $totalN);
            $tp->setValue('KET_N2',   $bonusN2 > 0 ? "Bonus: {$bonusN2} hr" : '-');
            $tp->setValue('KET_N1',   $bonusN1 > 0 ? "Bonus: {$bonusN1} hr" : '-');
            $tp->setValue('KET_N',    $totalN !== $sisaN ? "Termasuk bonus " . ($bonusN1 + $bonusN2) . " hr" : '-');

        } catch (\Throwable $e) {
            \Log::error('Balance fillTemplate: ' . $e->getMessage());
            $tp->setValue('SISA_N-2', '-');
            $tp->setValue('SISA_N-1', '-');
            $tp->setValue('N',        '-');
            $tp->setValue('KET_N2',   '-');
            $tp->setValue('KET_N1',   '-');
            $tp->setValue('KET_N',    '-');
        }
        $tp->setValue('ALAMAT',          $s($leave->address));
        $tp->setValue('TELP',            $s($leave->phone));

        $tp->setValue('DISETUJUI',       $leave->status === LeaveRequest::STATUS_APPROVED ? 'X' : ' ');
        $tp->setValue('MENUNGGU',        $leave->status === LeaveRequest::STATUS_PENDING  ? 'X' : ' ');
        $tp->setValue('TIDAK_DISETUJUI', $leave->status === LeaveRequest::STATUS_REJECTED ? 'X' : ' ');

        return $tp;
    }

    private function saveTempFile(TemplateProcessor $processor, string $fileName): string
    {
        $tempDir = storage_path('app/temp');

        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $path = $tempDir . '/' . $fileName;
        $processor->saveAs($path);

        if (!file_exists($path)) {
            throw new \RuntimeException('File tidak berhasil dibuat');
        }

        return $path;
    }

    private function downloadHeaders(): array
    {
        return [
            'Content-Type'  => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma'        => 'no-cache',
            'Expires'       => '0',
        ];
    }

    private function clearOutputBuffers(): void
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
    }
}