<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePegawaiRequest;
use App\Http\Requests\UpdatePegawaiRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PegawaiController extends Controller
{
    // =========================================================================
    // INDEX & CRUD
    // =========================================================================

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

    public function store(StorePegawaiRequest $request)
    {
        $nip   = $this->sanitizeNumeric($request->nip);
        $phone = $this->sanitizeNumeric($request->phone);

        User::create([
            'name'               => $request->name,
            'nip'                => $nip,
            'phone'              => $phone,
            'email'              => $request->email,
            'jabatan'            => $request->jabatan,
            'bidang_unit'        => $request->bidang_unit,
            'join_date'          => $request->join_date,
            'status'             => $request->status,
            'annual_leave_quota' => $request->annual_leave_quota,
            'password'           => Hash::make($nip), // default password = NIP
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

    public function update(Request $request, User $pegawai)
    {
        $request->validate([
            'name'               => 'required|string|max:255',
            'nip'                => 'required|numeric|unique:users,nip,' . $pegawai->id,
            'email'              => 'nullable|email|unique:users,email,' . $pegawai->id,
            'phone'              => 'nullable|string|max:20',
            'jabatan'            => 'required|string|max:255',
            'bidang_unit'        => 'required|string|max:255',
            'status'             => 'required|in:aktif,nonaktif',
            'join_date'          => 'nullable|date',
            'annual_leave_quota' => 'nullable|integer|min:0',
        ]);

        $pegawai->update($request->only([
            'name', 'nip', 'email', 'phone',
            'jabatan', 'bidang_unit', 'status',
            'join_date', 'annual_leave_quota',
        ]));

        if ($request->filled('new_password')) {
            $request->validate([
                'new_password' => 'string|min:6|confirmed',
            ], [
                'new_password.confirmed' => 'Konfirmasi password baru tidak cocok',
                'new_password.min'       => 'Password minimal 6 karakter',
            ]);

            $pegawai->update([
                'password' => Hash::make($request->new_password),
            ]);
        }

        return redirect()->route('admin.kelola_pegawai')
            ->with('success', "Data {$pegawai->name} berhasil diperbarui.");
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

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private function sanitizeNumeric(string $value): string
    {
        return preg_replace('/[^0-9]/', '', $value);
    }
}