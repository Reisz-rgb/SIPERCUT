<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = Auth::id();

        return [
            'name'        => 'required|string|max:255',
            'email'       => 'nullable|email|unique:users,email,' . $userId,
            'phone'       => 'required|string|unique:users,phone,' . $userId,
            'jabatan'     => 'nullable|string|max:255',
            'bidang_unit' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Nama wajib diisi',
            'email.unique'   => 'Email sudah digunakan',
            'phone.required' => 'Nomor HP wajib diisi',
            'phone.unique'   => 'Nomor HP sudah digunakan',
        ];
    }
}