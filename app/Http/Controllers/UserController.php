<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Models\LeaveRequest;

use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Show user dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $currentYear = (int) now()->year;

        // --- KONFIGURASI KUOTA ---
        $quotaTahunanMax = 12; // Batas maksimal cuti tahunan per tahun
        $totalAwal       = 24; // Batas maksimal total semua jenis cuti

        try {
            $approved = defined(LeaveRequest::class . '::STATUS_APPROVED')
                ? LeaveRequest::STATUS_APPROVED
                : 'approved';

            $durationExpr = DB::raw('COALESCE(duration, DATEDIFF(end_date, start_date) + 1)');

            /**
             * BIRU (Jatah Tahunan):
             * Menghitung total HARI cuti tahunan yang telah digunakan.
             * Contoh: jika sudah ambil 5 hari cuti tahunan → $usedTahunanDays = 5
             */
            $usedTahunanDays = (int) LeaveRequest::where('user_id', $user->id)
                ->where('status', $approved)
                ->where('jenis_cuti', 'Cuti Tahunan')
                ->whereYear('start_date', $currentYear)
                ->sum($durationExpr);

            /**
             * ORANGE (Telah Diambil):
             * Menghitung JUMLAH PENGAJUAN (transaksi) yang disetujui di tahun ini.
             */
            $approvedRequestCount = (int) LeaveRequest::where('user_id', $user->id)
                ->where('status', $approved)
                ->whereYear('start_date', $currentYear)
                ->count();

            // --- HITUNG PERSENTASE BAR ---

            // Biru: berapa persen hari cuti tahunan yang sudah terpakai dari kuota 12
            // Contoh: (5 / 12) * 100 = 41.67%
            $percentTahunan = ($quotaTahunanMax > 0)
                ? ($usedTahunanDays / $quotaTahunanMax) * 100
                : 0;
            $percentTahunan = min(100, max(0, $percentTahunan));

            // Orange: berapa persen jumlah pengajuan dari total awal 24
            // Contoh: (6 / 24) * 100 = 25%
            $percentOrange = ($totalAwal > 0)
                ? ($approvedRequestCount / $totalAwal) * 100
                : 0;
            $percentOrange = min(100, max(0, $percentOrange));

            // --- VARIABEL UNTUK VIEW ---
            // $totalQuota     → Angka besar BIRU  = hari cuti tahunan terpakai (misal: 5)
            // $quotaTahunanMax → Label "Kuota: 12"
            // $usedLeave      → Angka besar ORANGE = jumlah pengajuan (misal: 6)
            // $remainingLeave → Angka HIJAU = sisa dari totalAwal dikurangi pengajuan
            $totalQuota     = $usedTahunanDays;       // 5
            $usedLeave      = $approvedRequestCount;  // 6
            $remainingLeave = max(0, $totalAwal - $approvedRequestCount); // 24 - 6 = 18

        } catch (\Throwable $e) {
            Log::error('Dashboard hitung cuti error: ' . $e->getMessage());
            $totalQuota     = 0;
            $usedLeave      = 0;
            $percentTahunan = 0;
            $percentOrange  = 0;
            $remainingLeave = $totalAwal;
        }

        // Data pendukung lainnya
        $recentLeaves = LeaveRequest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        $totalSubmissions = LeaveRequest::where('user_id', $user->id)->count();
        if ($totalSubmissions > 0) {
            $pending  = defined(LeaveRequest::class . '::STATUS_PENDING')  ? LeaveRequest::STATUS_PENDING  : 'pending';
            $rejected = defined(LeaveRequest::class . '::STATUS_REJECTED') ? LeaveRequest::STATUS_REJECTED : 'rejected';

            $approvedCount = LeaveRequest::where('user_id', $user->id)->where('status', $approved)->count();
            $pendingCount  = LeaveRequest::where('user_id', $user->id)->where('status', $pending)->count();
            $rejectedCount = LeaveRequest::where('user_id', $user->id)->where('status', $rejected)->count();

            $approvedPercent = round(($approvedCount / $totalSubmissions) * 100);
            $pendingPercent  = round(($pendingCount  / $totalSubmissions) * 100);
            $rejectedPercent = round(($rejectedCount / $totalSubmissions) * 100);
        } else {
            $approvedPercent = $pendingPercent = $rejectedPercent = 0;
        }

        return view('user.UserDashboard', compact(
            'user',
            'totalQuota',        // Angka besar BIRU  → hari cuti tahunan terpakai (contoh: 5)
            'quotaTahunanMax',   // Label "Kuota: 12" di kartu biru
            'percentTahunan',    // Lebar bar BIRU    → (5/12)*100 = 41.67%
            'usedLeave',         // Angka besar ORANGE → jumlah pengajuan (contoh: 6)
            'percentOrange',     // Lebar bar ORANGE  → (6/24)*100 = 25%
            'remainingLeave',    // Angka besar HIJAU → sisa cuti tersedia (contoh: 18)
            'totalAwal',         // Label "Total awal: 24" di kartu hijau
            'recentLeaves',
            'approvedPercent',
            'pendingPercent',
            'rejectedPercent'
        ));
    }

    /**
     * Show user profile
     */
    public function profile()
    {
        $user = Auth::user();
        return view('user.ProfilPage', compact('user'));
    }

    /**
     * Show edit profile form
     */
    public function editProfile()
    {
        $user = Auth::user();
        return view('user.EditProfilPage', compact('user'));
    }

    /**
     * Update user profile
     */
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

        $phone = preg_replace('/[^0-9]/', '', $request->phone);

        $user->update([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'phone'       => $phone,
            'jabatan'     => $validated['jabatan']     ?? null,
            'bidang_unit' => $validated['bidang_unit'] ?? null,
        ]);

        return redirect()
            ->route('user.profil')
            ->with('status', 'sukses');
    }

    /**
     * Show change password form
     */
    public function showChangePassword()
    {
        return view('user.ChangePassword');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
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
            return redirect()
                ->back()
                ->withErrors(['current_password' => 'Password lama tidak sesuai. Gunakan password terakhir yang Anda set, atau gunakan NIP jika belum pernah mengubah password.'])
                ->withInput();
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()
            ->route('user.profil')
            ->with('status', 'password_updated');
    }

    /**
     * Show user leave history
     */
    public function history(Request $request)
    {
        $user   = Auth::user();
        $status = $request->get('status', 'all');

        $query = LeaveRequest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        if ($status !== 'all') {
            $statusMap = [
                'approved' => defined(LeaveRequest::class . '::STATUS_APPROVED') ? LeaveRequest::STATUS_APPROVED : 'approved',
                'pending'  => defined(LeaveRequest::class . '::STATUS_PENDING')  ? LeaveRequest::STATUS_PENDING  : 'pending',
                'rejected' => defined(LeaveRequest::class . '::STATUS_REJECTED') ? LeaveRequest::STATUS_REJECTED : 'rejected',
            ];

            if (isset($statusMap[$status])) {
                $query->where('status', $statusMap[$status]);
            }
        }

        $leaves = $query->paginate(10);

        return view('user.RiwayatPage', compact('leaves', 'user', 'status'));
    }

    /**
     * Download surat cuti (docx)
     */
    public function downloadSuratCuti($id)
    {
        while (ob_get_level()) {
            ob_end_clean();
        }

        error_reporting(0);
        ini_set('display_errors', '0');

        try {
            $leave = LeaveRequest::with('user')->findOrFail($id);
            $user  = $leave->user;

            $templatePath = storage_path('app/template/surat_cuti_template.docx');

            if (!file_exists($templatePath)) {
                Log::error('Template not found: ' . $templatePath);
                abort(404, 'Template tidak ditemukan');
            }

            $templateProcessor = new TemplateProcessor($templatePath);

            $sanitize = function ($text) {
                if ($text === null || $text === '') return '-';
                $text = (string) $text;
                $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
                $text = htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                return $text;
            };

            $templateProcessor->setValue('TANGGAL_SURAT', Carbon::now()->isoFormat('D MMMM YYYY'));
            $templateProcessor->setValue('NAMA',          $sanitize($user->name));
            $templateProcessor->setValue('NIP',           $sanitize($user->nip));
            $templateProcessor->setValue('JABATAN',       $sanitize($user->jabatan));
            $templateProcessor->setValue('MASA_KERJA',    $sanitize($user->masa_kerja));
            $templateProcessor->setValue('UNIT_KERJA',    $sanitize($user->unit_kerja));

            $jenisMap = [
                'Cuti Tahunan'                      => 'CUTI_TAHUNAN',
                'Cuti Besar'                        => 'CUTI_BESAR',
                'Cuti Sakit'                        => 'CUTI_SAKIT',
                'Cuti Melahirkan'                   => 'CUTI_MELAHIRKAN',
                'Cuti Karena Alasan Penting'        => 'CUTI_KARENA_ALASAN_PENTING',
                'Cuti di Luar Tanggungan Negara'    => 'CUTI_DI_LUAR_TANGGUNGAN_NEGARA',
            ];

            foreach ($jenisMap as $jenis => $placeholder) {
                $templateProcessor->setValue($placeholder, $leave->jenis_cuti === $jenis ? 'X' : '');
            }

            $templateProcessor->setValue('ALASAN',          $sanitize($leave->reason));
            $templateProcessor->setValue('LAMA_HARI',       ((int) $leave->duration) . ' hari');
            $templateProcessor->setValue('TANGGAL_MULAI',   Carbon::parse($leave->start_date)->isoFormat('D MMMM YYYY'));
            $templateProcessor->setValue('TANGGAL_SELESAI', Carbon::parse($leave->end_date)->isoFormat('D MMMM YYYY'));

            $templateProcessor->setValue('SISA_N-2',    $sanitize($user->sisa_cuti_n2));
            $templateProcessor->setValue('SISA_N-1',    $sanitize($user->sisa_cuti_n1));
            $templateProcessor->setValue('N',           $sanitize($user->sisa_cuti_n));
            $templateProcessor->setValue('KETERANGAN',  '-');

            $templateProcessor->setValue('ALAMAT', $sanitize($leave->address));
            $templateProcessor->setValue('TELP',   $sanitize($leave->phone));

            $approved = defined(LeaveRequest::class . '::STATUS_APPROVED') ? LeaveRequest::STATUS_APPROVED : 'approved';
            $pending  = defined(LeaveRequest::class . '::STATUS_PENDING')  ? LeaveRequest::STATUS_PENDING  : 'pending';
            $rejected = defined(LeaveRequest::class . '::STATUS_REJECTED') ? LeaveRequest::STATUS_REJECTED : 'rejected';

            $templateProcessor->setValue('DISETUJUI',      $leave->status === $approved ? 'X' : '');
            $templateProcessor->setValue('MENUNGGU',       $leave->status === $pending  ? 'X' : '');
            $templateProcessor->setValue('TIDAK_DISETUJUI',$leave->status === $rejected ? 'X' : '');

            $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $user->name);
            $fileName = 'Surat_Cuti_' . $safeName . '_' . date('Ymd_His') . '.docx';

            $tempPath = storage_path('app/temp');
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }

            $tempFile = $tempPath . '/' . $fileName;
            $templateProcessor->saveAs($tempFile);

            if (!file_exists($tempFile)) {
                throw new \Exception('File tidak berhasil dibuat');
            }

            while (ob_get_level()) {
                ob_end_clean();
            }

            return response()->download($tempFile, $fileName, [
                'Content-Type'        => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Cache-Control'       => 'no-cache, no-store, must-revalidate',
                'Pragma'              => 'no-cache',
                'Expires'             => '0',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Exception in downloadSuratCuti: ' . $e->getMessage());

            if (isset($tempFile) && file_exists($tempFile)) {
                unlink($tempFile);
            }

            return back()->with('error', 'Gagal membuat surat cuti: ' . $e->getMessage());
        }
    }
}