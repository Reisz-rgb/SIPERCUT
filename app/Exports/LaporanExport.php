<?php

namespace App\Exports;

use App\Models\LeaveRequest;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = LeaveRequest::query()->with('user');

        // --- 1. FILTER WAKTU (Sama dengan Controller) ---
        $filter = $this->request->input('filter', '1_bulan');

        if ($filter == '1_bulan') {
            $startDate = Carbon::now()->subMonth();
        } elseif ($filter == '3_bulan') {
            $startDate = Carbon::now()->subMonths(3);
        } else {
            $startDate = Carbon::now()->startOfYear();
        }
        
        $query->where('created_at', '>=', $startDate);

        // --- 2. FILTER SEARCH NAMA ---
        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }

        // --- 3. FILTER BIDANG UNIT ---
        if ($this->request->filled('bidang_unit')) {
            $bidang = $this->request->bidang_unit;
            $query->whereHas('user', function($q) use ($bidang) {
                $q->where('bidang_unit', $bidang);
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Nama Pegawai',
            'NIP',
            'Bidang / Unit',
            'Jenis Cuti',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Alasan',
            'Status',
            'Tanggal Pengajuan',
        ];
    }

    public function map($leaveRequest): array
    {
        return [
            $leaveRequest->user->name ?? '-',
            $leaveRequest->user->nip ?? '-',
            $leaveRequest->user->bidang_unit ?? '-', // Pastikan kolom ini benar
            $leaveRequest->jenis_cuti,
            $leaveRequest->start_date, // Bisa diformat: Carbon::parse($leaveRequest->start_date)->format('d-m-Y')
            $leaveRequest->end_date,
            $leaveRequest->reason,
            ucfirst($leaveRequest->status),
            $leaveRequest->created_at->format('d-m-Y H:i'),
        ];
    }
}