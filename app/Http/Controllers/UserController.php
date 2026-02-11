<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Cuti;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
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
        $currentYear = now()->year;
        
        // Hitung saldo cuti dengan LeaveBalance
        $leaveBalance = LeaveBalance::calculateTotalAvailable($user->id, $currentYear);
        $totalQuota = $leaveBalance['n']['quota'];
        $usedLeave = $leaveBalance['n']['used'];
        $remainingLeave = $leaveBalance['total_available'];
        
        // Ambil riwayat pengajuan terbaru (3 terakhir) dari LeaveRequest
        $recentLeaves = LeaveRequest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        
        // Hitung statistik status untuk donut chart
        $totalSubmissions = LeaveRequest::where('user_id', $user->id)->count();
        
        if ($totalSubmissions > 0) {
            $approvedCount = LeaveRequest::where('user_id', $user->id)
                ->where('status', LeaveRequest::STATUS_APPROVED)
                ->count();
            $pendingCount = LeaveRequest::where('user_id', $user->id)
                ->where('status', LeaveRequest::STATUS_PENDING)
                ->count();
            $rejectedCount = LeaveRequest::where('user_id', $user->id)
                ->where('status', LeaveRequest::STATUS_REJECTED)
                ->count();
            
            $approvedPercent = round(($approvedCount / $totalSubmissions) * 100);
            $pendingPercent = round(($pendingCount / $totalSubmissions) * 100);
            $rejectedPercent = round(($rejectedCount / $totalSubmissions) * 100);
        } else {
            $approvedPercent = $pendingPercent = $rejectedPercent = 0;
        }
        
        return view('user.UserDashboard', compact(
            'user',
            'totalQuota',
            'usedLeave',
            'remainingLeave',
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
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|unique:users,phone,' . $user->id,
            'jabatan' => 'nullable|string|max:255',
            'bidang_unit' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.unique' => 'Email sudah digunakan',
            'phone.required' => 'Nomor HP wajib diisi',
            'phone.unique' => 'Nomor HP sudah digunakan',
        ]);
        
        // Bersihkan phone
        $phone = preg_replace('/[^0-9]/', '', $request->phone);
        
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $phone,
            'jabatan' => $validated['jabatan'],
            'bidang_unit' => $validated['bidang_unit'],
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
            'password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Password lama wajib diisi',
            'password.required' => 'Password baru wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);
        
        $user = Auth::user();
        
        // Debug: Cek password hash
        \Log::info('Current Password Input: ' . $request->current_password);
        \Log::info('Stored Password Hash: ' . $user->password);
        \Log::info('Check Result: ' . (Hash::check($request->current_password, $user->password) ? 'TRUE' : 'FALSE'));
        
        // Cek password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()
                ->back()
                ->withErrors(['current_password' => 'Password lama tidak sesuai. Gunakan password terakhir yang Anda set, atau gunakan NIP jika belum pernah mengubah password.'])
                ->withInput();
        }
        
        // Update password
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
        $user = Auth::user();
        
        // Filter berdasarkan status
        $status = $request->get('status', 'all');
        
        $query = LeaveRequest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc');
        
        // Apply filter
        if ($status !== 'all') {
            $statusMap = [
                'approved' => LeaveRequest::STATUS_APPROVED,
                'pending' => LeaveRequest::STATUS_PENDING,
                'rejected' => LeaveRequest::STATUS_REJECTED,
            ];
            
            if (isset($statusMap[$status])) {
                $query->where('status', $statusMap[$status]);
            }
        }
        
        $leaves = $query->paginate(10);
        
        return view('user.RiwayatPage', compact('leaves', 'user', 'status'));
    }
    
    public function downloadSuratCuti($id)
    {
        // Bersihkan output buffer
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        error_reporting(0);
        ini_set('display_errors', '0');
        
        try {
            $leave = LeaveRequest::with('user')->findOrFail($id);
            $user = $leave->user;
            
            $templatePath = storage_path('app/template/surat_cuti_template.docx');
            
            if (!file_exists($templatePath)) {
                \Log::error('Template not found: ' . $templatePath);
                abort(404, 'Template tidak ditemukan');
            }
            
            $templateProcessor = new TemplateProcessor($templatePath);
            
            // Helper function untuk sanitize text
            $sanitize = function($text) {
                if (empty($text) || is_null($text)) return '-';
                // Konversi ke string terlebih dahulu
                $text = (string) $text;
                // Hapus karakter control characters yang bisa corrupt XML
                $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
                // Escape XML entities
                $text = htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                return $text;
            };
            
            // Set data dengan sanitization
            $templateProcessor->setValue('TANGGAL_SURAT', Carbon::now()->isoFormat('D MMMM YYYY'));
            $templateProcessor->setValue('NAMA', $sanitize($user->name));
            $templateProcessor->setValue('NIP', $sanitize($user->nip));
            $templateProcessor->setValue('JABATAN', $sanitize($user->jabatan));
            $templateProcessor->setValue('MASA_KERJA', $sanitize($user->masa_kerja));
            $templateProcessor->setValue('UNIT_KERJA', $sanitize($user->unit_kerja));
            
            // Set jenis cuti checkboxes
            $jenisMap = [
                'Cuti Tahunan' => 'CUTI_TAHUNAN',
                'Cuti Besar' => 'CUTI_BESAR',
                'Cuti Sakit' => 'CUTI_SAKIT',
                'Cuti Melahirkan' => 'CUTI_MELAHIRKAN',
                'Cuti Karena Alasan Penting' => 'CUTI_KARENA_ALASAN_PENTING',
                'Cuti di Luar Tanggungan Negara' => 'CUTI_DI_LUAR_TANGGUNGAN_NEGARA',
            ];
            
            foreach ($jenisMap as $jenis => $placeholder) {
                $templateProcessor->setValue($placeholder, 
                    $leave->jenis_cuti === $jenis ? 'X' : ''
                );
            }
            
            $templateProcessor->setValue('ALASAN', $sanitize($leave->reason));
            $templateProcessor->setValue('LAMA_HARI', $leave->duration . ' hari');
            $templateProcessor->setValue('TANGGAL_MULAI', 
                Carbon::parse($leave->start_date)->isoFormat('D MMMM YYYY')
            );
            $templateProcessor->setValue('TANGGAL_SELESAI', 
                Carbon::parse($leave->end_date)->isoFormat('D MMMM YYYY')
            );
            
            $templateProcessor->setValue('SISA_N-2', $sanitize($user->sisa_cuti_n2));
            $templateProcessor->setValue('SISA_N-1', $sanitize($user->sisa_cuti_n1));
            $templateProcessor->setValue('N', $sanitize($user->sisa_cuti_n));
            $templateProcessor->setValue('KETERANGAN', '-');
            
            $templateProcessor->setValue('ALAMAT', $sanitize($leave->address));
            $templateProcessor->setValue('TELP', $sanitize($leave->phone));
            
            $templateProcessor->setValue('DISETUJUI', $leave->status === LeaveRequest::STATUS_APPROVED ? 'X' : '');
            $templateProcessor->setValue('MENUNGGU', $leave->status === LeaveRequest::STATUS_PENDING ? 'X' : '');
            $templateProcessor->setValue('TIDAK_DISETUJUI', $leave->status === LeaveRequest::STATUS_REJECTED ? 'X' : '');
            
            // Generate filename yang aman
            $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $user->name);
            $fileName = 'Surat_Cuti_' . $safeName . '_' . date('Ymd_His') . '.docx';
            
            $tempPath = storage_path('app/temp');
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }
            
            $tempFile = $tempPath . '/' . $fileName;
            
            // Save file
            $templateProcessor->saveAs($tempFile);
            
            if (!file_exists($tempFile)) {
                throw new \Exception('File tidak berhasil dibuat');
            }
            
            $fileSize = filesize($tempFile);
            \Log::info('Generated file size: ' . $fileSize . ' bytes');
            
            // Validasi ukuran
            if ($fileSize < 10000) {
                \Log::warning('File size suspiciously small: ' . $fileSize . ' bytes');
            }
            
            // Clear buffer sekali lagi sebelum download
            while (ob_get_level()) {
                ob_end_clean();
            }
            
            // Return file dengan header yang tepat
            return response()->download($tempFile, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            \Log::error('Exception in downloadSuratCuti: ' . $e->getMessage());
            
            if (isset($tempFile) && file_exists($tempFile)) {
                unlink($tempFile);
            }
            
            return back()->with('error', 'Gagal membuat surat cuti: ' . $e->getMessage());
        }
    }
}