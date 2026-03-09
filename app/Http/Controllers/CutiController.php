<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CutiController extends Controller
{
    /**
     * Show leave application form
     */
    public function create()
    {
        $user = Auth::user();
        $currentYear = now()->year;
        
        $leaveBalance = LeaveBalance::calculateTotalAvailable($user->id, $currentYear);
        
        $joinDate = $user->join_date ? Carbon::parse($user->join_date) : now()->subYears(5);
        // Gunakan floor untuk memastikan angka bulat
        $workYears = floor($joinDate->diffInYears(now()));
        $workMonths = floor($joinDate->copy()->addYears($workYears)->diffInMonths(now()));
        
        return view('user.pengajuan-cuti', compact('user', 'leaveBalance', 'workYears', 'workMonths'));
    }
    
    /**
     * Store leave request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_cuti' => 'required|string',
            'alasan' => 'required|string|min:20',
            'lama_hari' => 'required|integer|min:1',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alamat_cuti' => 'required|string',
            'no_telepon' => 'required|string',
            'catatan_tambahan' => 'nullable|string',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,doc,docx,jpg,png,xls,xlsx|max:5120', // 5MB
        ], [
            'jenis_cuti.required' => 'Jenis cuti wajib dipilih',
            'alasan.required' => 'Alasan cuti wajib diisi',
            'alasan.min' => 'Mohon berikan alasan yang lebih mendalam (minimal 20 karakter).',
            'lama_hari.required' => 'Lama hari cuti wajib diisi',
            'lama_hari.min' => 'Lama cuti minimal 1 hari',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
            'alamat_cuti.required' => 'Alamat selama cuti wajib diisi',
            'no_telepon.required' => 'Nomor telepon wajib diisi',
            'dokumen_pendukung.max' => 'Ukuran file maksimal 5MB',
        ]);
        
        $user = Auth::user();
        $currentYear = now()->year;
        
        // Cek saldo cuti (hanya untuk Cuti Tahunan)
        $leaveBalance = LeaveBalance::calculateTotalAvailable($user->id, $currentYear);
        
        if ($validated['jenis_cuti'] === 'Cuti Tahunan' && $validated['lama_hari'] > $leaveBalance['total_available']) {
            return redirect()
                ->back()
                ->withErrors(['lama_hari' => "Saldo cuti tidak mencukupi. Tersedia: {$leaveBalance['total_available']} hari"])
                ->withInput();
        }
        
        // Handle file upload
        $filePath = null;
        if ($request->hasFile('dokumen_pendukung')) {
            $file = $request->file('dokumen_pendukung');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('leave_documents', $fileName, 'public');
        }
        
        // Bersihkan nomor telepon
        $phone = preg_replace('/[^0-9]/', '', $request->no_telepon);
        
        // Simpan leave request
        $leaveRequest = LeaveRequest::create([
            'user_id' => $user->id,
            'jenis_cuti' => $validated['jenis_cuti'],
            'start_date' => $validated['tanggal_mulai'],
            'end_date' => $validated['tanggal_selesai'],
            'duration' => $validated['lama_hari'],
            'reason' => $validated['alasan'],
            'address' => $validated['alamat_cuti'],
            'phone' => $phone,
            'notes' => $validated['catatan_tambahan'],
            'file_path' => $filePath,
            'status' => LeaveRequest::STATUS_PENDING,
        ]);
        
        // Generate reference number
        $refNumber = 'CUTI-' . now()->year . '-' . str_pad($leaveRequest->id, 4, '0', STR_PAD_LEFT);
        
        return view('user.PengajuanSukses', compact('refNumber'));
    }
    
    /**
     * Show leave request detail (for admin)
     */
    public function show($id)
    {
        $pengajuan = LeaveRequest::with('user')->findOrFail($id);
        return view('admin.detail_pengajuan', compact('pengajuan'));
    }
    
    /**
     * Update leave request status (for admin)
     */
    public function updateStatus(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        
        $validated = $request->validate([
            'keputusan' => 'required|in:disetujui,tidak_disetujui',
            'pertimbangan' => 'nullable|string',
        ]);
        
        $status = $validated['keputusan'] === 'disetujui' 
            ? LeaveRequest::STATUS_APPROVED 
            : LeaveRequest::STATUS_REJECTED;
        
        $leaveRequest->update([
            'status' => $status,
            'rejection_reason' => $validated['pertimbangan'],
        ]);
        
        // Jika diubah statusnya, sinkronkan saldo cuti tahunan (agar konsisten walau approve/reject berubah)
        if ($leaveRequest->jenis_cuti === 'Cuti Tahunan') {
            $year = \Carbon\Carbon::parse($leaveRequest->start_date)->year;
            LeaveBalance::recalculateAnnualBalances((int)$leaveRequest->user_id, (int)$year);
        }
        
        return response()->json([
            'message' => 'Status berhasil diperbarui',
            'status' => $status,
        ]);
    }
    
    /**
     * Deduct leave balance when approved
     */
    private function deductLeaveBalance(LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->jenis_cuti !== 'Cuti Tahunan') {
            return;
        }

        $userId = $leaveRequest->user_id;
        $currentYear = now()->year;
        $duration = $leaveRequest->duration;
        
        // Get balances
        $n = LeaveBalance::getOrCreateBalance($userId, $currentYear);
        $n1 = LeaveBalance::getOrCreateBalance($userId, $currentYear - 1);
        $n2 = LeaveBalance::getOrCreateBalance($userId, $currentYear - 2);
        
        // Deduct from current year first
        if ($n->remaining >= $duration) {
            $n->used += $duration;
            $n->remaining -= $duration;
            $n->save();
        } else {
            // Use bonus from N-1 and N-2
            $remaining = $duration;
            
            // Use from N
            if ($n->remaining > 0) {
                $useFromN = min($n->remaining, $remaining);
                $n->used += $useFromN;
                $n->remaining -= $useFromN;
                $n->save();
                $remaining -= $useFromN;
            }
            
            // Use from N-1 bonus
            $bonusN1 = floor($n1->remaining / 2);
            if ($remaining > 0 && $bonusN1 > 0) {
                $useFromN1 = min($bonusN1, $remaining);
                $n1->used += ($useFromN1 * 2); // Karena bonus setengah
                $n1->remaining -= ($useFromN1 * 2);
                $n1->save();
                $remaining -= $useFromN1;
            }
            
            // Use from N-2 bonus
            $bonusN2 = floor($n2->remaining / 2);
            if ($remaining > 0 && $bonusN2 > 0) {
                $useFromN2 = min($bonusN2, $remaining);
                $n2->used += ($useFromN2 * 2);
                $n2->remaining -= ($useFromN2 * 2);
                $n2->save();
            }
        }
    }
}