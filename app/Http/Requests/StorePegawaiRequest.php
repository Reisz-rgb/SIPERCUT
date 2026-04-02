<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePegawaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'               => 'required|string|max:255',
            'nip'                => 'required|string|unique:users,nip',
            'phone'              => 'required|string|unique:users,phone',
            'email'              => 'nullable|email|unique:users,email',
            'jabatan'            => 'required|string|max:255',
            'bidang_unit'        => 'required|string|max:255',
            'join_date'          => 'nullable|date',
            'annual_leave_quota' => 'required|integer|min:0|max:30',
            'status'             => 'required|in:aktif,nonaktif',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'               => 'Nama wajib diisi',
            'nip.required'                => 'NIP wajib diisi',
            'nip.unique'                  => 'NIP sudah terdaftar',
            'phone.required'              => 'Nomor telepon wajib diisi',
            'phone.unique'                => 'Nomor telepon sudah terdaftar',
            'email.unique'                => 'Email sudah terdaftar',
            'jabatan.required'            => 'Jabatan wajib diisi',
            'bidang_unit.required'        => 'Unit kerja wajib diisi',
            'annual_leave_quota.required' => 'Kuota cuti wajib diisi',
            'annual_leave_quota.integer'  => 'Kuota cuti harus berupa angka',
        ];
    }
}