<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && ! Auth::user()->isActive()) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return view('auth.LoginPage')
                ->withErrors([
                    'login' => 'Akun Anda tidak aktif. Silakan hubungi administrator.'
                ]);
        }

        return Auth::check()
            ? $this->redirectBasedOnRole()
            : view('auth.LoginPage');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nip'      => 'required|string',
            'password' => 'required|string',
        ], [
            'nip.required'      => 'NIP wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        $credentials = [
            'nip'      => $this->sanitizeNumeric($request->nip),
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            if (! Auth::user()->isActive()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()
                    ->withErrors([
                        'login' => 'Akun Anda tidak aktif. Silakan hubungi administrator.'
                    ])
                    ->withInput();
            }

            return $this->redirectBasedOnRole();
        }

        return back()
            ->withErrors(['login' => 'NIP atau password salah'])
            ->withInput();
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }

        $unitKerjaOptions = User::query()
            ->whereNotNull('bidang_unit')
            ->where('bidang_unit', '!=', '')
            ->select('bidang_unit')
            ->distinct()
            ->orderBy('bidang_unit')
            ->pluck('bidang_unit');

        $jabatanOptions = User::query()
            ->whereNotNull('jabatan')
            ->where('jabatan', '!=', '')
            ->select('jabatan')
            ->distinct()
            ->orderBy('jabatan')
            ->pluck('jabatan');

        return view('auth.RegisterPage', compact('unitKerjaOptions', 'jabatanOptions'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'nip'         => 'required|string|unique:users,nip',
            'phone'       => 'required|string|unique:users,phone',
            'email'       => 'required|email|unique:users,email',
            'bidang_unit' => 'required|string|max:255',
            'jabatan'     => 'required|string|max:255',
            'password'    => 'required|string|min:6|confirmed',
        ], [
            'name.required'        => 'Nama wajib diisi',
            'nip.required'         => 'NIP wajib diisi',
            'nip.unique'           => 'NIP sudah terdaftar',
            'phone.required'       => 'Nomor HP wajib diisi',
            'phone.unique'         => 'Nomor HP sudah terdaftar',
            'email.required'       => 'Email wajib diisi',
            'email.email'          => 'Format email tidak valid',
            'email.unique'         => 'Email sudah terdaftar',
            'bidang_unit.required' => 'Unit kerja wajib diisi',
            'jabatan.required'     => 'Jabatan wajib diisi',
            'password.required'    => 'Password wajib diisi',
            'password.min'         => 'Password minimal 6 karakter',
            'password.confirmed'   => 'Konfirmasi password tidak cocok',
        ]);

        $user = User::create([
            'name'        => $request->name,
            'nip'         => $this->sanitizeNumeric($request->nip),
            'phone'       => $this->sanitizeNumeric($request->phone),
            'email'       => $request->email,
            'bidang_unit' => $request->bidang_unit,
            'jabatan'     => $request->jabatan,
            'password'    => Hash::make($request->password),
            'role'        => 'user',
            'status'      => 'aktif',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('register.success');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }

    private function redirectBasedOnRole()
    {
        return Auth::user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('user.dashboard');
    }

    private function sanitizeNumeric(string $value): string
    {
        return preg_replace('/[^0-9]/', '', $value);
    }
}
