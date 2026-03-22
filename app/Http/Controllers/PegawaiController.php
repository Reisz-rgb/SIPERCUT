<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $pegawai = User::where('role', 'user')
            ->when($request->filled('search'), fn ($q) =>
                $q->where(fn ($sub) =>
                    $sub->where('name', 'LIKE', "%{$request->search}%")
                        ->orWhere('nip', 'LIKE', "%{$request->search}%")
                        ->orWhere('bidang_unit', 'LIKE', "%{$request->search}%")
                )
            )
            ->latest()
            ->paginate(10);

        return view('admin.kelola_pegawai', compact('pegawai'));
    }

    public function create()
    {
        return view('admin.tambah_pegawai');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            $this->pegawaiRules(),
            $this->pegawaiMessages()
        );

        $nip   = $this->sanitizeNumeric($request->nip);
        $phone = $this->sanitizeNumeric($request->phone);

        User::create([
            'name'               => $validated['name'],
            'nip'                => $nip,
            'phone'              => $phone,
            'email'              => $validated['email'],
            'jabatan'            => $validated['jabatan'],
            'bidang_unit'        => $validated['bidang_unit'],
            'join_date'          => $validated['join_date'],
            'status'             => $validated['status'] ?? 'aktif',
            'annual_leave_quota' => $validated['annual_leave_quota'],
            'password'           => Hash::make($nip),
            'role'               => 'user',
        ]);

        return redirect()
            ->route('admin.kelola_pegawai')
            ->with('success', 'Pegawai berhasil ditambahkan! Password default = NIP pegawai.');
    }

    public function edit($id)
    {
        $pegawai = User::findOrFail($id);
        return view('admin.edit_pegawai', compact('pegawai'));
    }

    public function update(Request $request, $id)
    {
        $pegawai = User::findOrFail($id);

        $validated = $request->validate(
            $this->pegawaiUpdateRules((int) $id),
            $this->pegawaiMessages()
        );

        $pegawai->fill([
            'name'               => $validated['name'],
            'nip'                => $this->sanitizeNumeric($request->nip),
            'phone'              => $this->sanitizeNumeric($request->phone),
            'email'              => $validated['email'] ?? null,
            'jabatan'            => $validated['jabatan'],
            'bidang_unit'        => $validated['bidang_unit'],
            'join_date'          => $validated['join_date'] ?? $pegawai->join_date,
            'status'             => $validated['status'],
            'annual_leave_quota' => $validated['annual_leave_quota'] ?? $pegawai->annual_leave_quota,
        ]);

        if ($validated['status'] === 'nonaktif') {
            $pegawai->remember_token = null;
        }

        $pegawai->save();

        return redirect()
            ->route('admin.kelola_pegawai')
            ->with('success', 'Data pegawai berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $pegawai = User::findOrFail($id);

        if ($pegawai->cuti()->count() > 0) {
            return redirect()
                ->route('admin.kelola_pegawai')
                ->with('error', 'Tidak dapat menghapus pegawai yang memiliki riwayat pengajuan cuti!');
        }

        $pegawai->delete();

        return redirect()
            ->route('admin.kelola_pegawai')
            ->with('success', 'Pegawai berhasil dihapus!');
    }

    public function resetPassword($id)
    {
        $pegawai = User::findOrFail($id);

        $pegawai->update(['password' => Hash::make($pegawai->nip)]);

        return redirect()
            ->route('admin.kelola_pegawai')
            ->with('success', 'Password pegawai berhasil direset ke NIP!');
    }

    private function pegawaiRules(?int $id = null): array
    {
        $unique = fn ($col) => 'required|string|unique:users,' . $col . ($id ? ',' . $id : '');

        return [
            'name'               => 'required|string|max:255',
            'nip'                => $unique('nip'),
            'phone'              => $unique('phone'),
            'email'              => 'nullable|email|unique:users,email' . ($id ? ',' . $id : ''),
            'jabatan'            => 'required|string|max:255',
            'bidang_unit'        => 'required|string|max:255',
            'join_date'          => 'nullable|date',
            'annual_leave_quota' => 'required|integer|min:0|max:30',
            'status'             => 'required|in:aktif,nonaktif',
        ];
    }

    private function pegawaiUpdateRules(int $id): array
    {
        return [
            'name'               => 'required|string|max:255',
            'nip'                => 'required|string|unique:users,nip,' . $id,
            'phone'              => 'required|string|unique:users,phone,' . $id,
            'email'              => 'nullable|email|unique:users,email,' . $id,
            'jabatan'            => 'required|string|max:255',
            'bidang_unit'        => 'required|string|max:255',
            'join_date'          => 'nullable|date',
            'annual_leave_quota' => 'nullable|integer|min:0|max:30',
            'status'             => 'required|in:aktif,nonaktif',
        ];
    }

    private function pegawaiMessages(): array
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
            'status.required'             => 'Status akun wajib dipilih',
            'status.in'                   => 'Status akun tidak valid',
        ];
    }

    private function sanitizeNumeric(string $value): string
    {
        return preg_replace('/[^0-9]/', '', $value);
    }
}
