<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCutiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jenis_cuti'        => 'required|string',
            'alasan'            => 'required|string|min:20',
            'lama_hari'         => 'required|integer|min:1',
            'tanggal_mulai'     => 'required|date',
            'tanggal_selesai'   => 'required|date|after_or_equal:tanggal_mulai',
            'alamat_cuti'       => 'required|string',
            'no_telepon'        => 'required|string',
            'catatan_tambahan'  => 'nullable|string',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,doc,docx,jpg,png,xls,xlsx|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
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

    // =========================================================================
    // HELPERS
    // =========================================================================

    /**
     * Cek apakah pengajuan ini adalah Cuti Tahunan.
     * Dipakai di controller untuk validasi saldo (butuh data user).
     */
    public function isCutiTahunan(): bool
    {
        return $this->jenis_cuti === \App\Models\LeaveRequest::JENIS_TAHUNAN;
    }
}