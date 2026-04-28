<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

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
        // 1. Definisikan Key Limiter di paling atas!
        $nip = $this->sanitizeNumeric($request->nip ?? '');
        $ipKey      = 'login.ip.' . sha1($request->ip());
        $accountKey = 'login.account.' . sha1($nip);

        // 2. Cek apakah sudah kena throttle SEBELUM validasi/attempt
        if (RateLimiter::tooManyAttempts($ipKey, 5) || RateLimiter::tooManyAttempts($accountKey, 5)) {
            return back()
                ->withErrors(['throttle' => 'Terlalu banyak percobaan login.']) // Key 'throttle' agar Test Lolos
                ->withInput();
        }

        $validator = Validator::make($request->all(), [
            'nip'      => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            RateLimiter::hit($ipKey, 60);
            return back()->withErrors($validator)->withInput();
        }

        $credentials = ['nip' => $nip, 'password' => $request->password];

        if (!Auth::attempt($credentials, $request->filled('remember'))) {
            RateLimiter::hit($ipKey, 60);
            RateLimiter::hit($accountKey, 60);

            return back()
                ->withErrors(['login' => 'NIP atau password salah'])
                ->withInput();
        }

        // Jika sukses
        RateLimiter::clear($ipKey);
        RateLimiter::clear($accountKey);
        $request->session()->regenerate();

        return $this->redirectBasedOnRole();
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
        $nip = $this->sanitizeNumeric($request->nip);
        $ipKey  = 'register.ip.' . sha1($request->ip());
        $nipKey = 'register.nip.' . sha1($nip);

        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            RateLimiter::hit($ipKey, 300);
            RateLimiter::hit($nipKey, 300);

            return back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        try {
            User::create([
                'name'        => $request->name,
                'nip'         => $nip,
                'phone'       => $this->sanitizeNumeric($request->phone),
                'email'       => $request->email,
                'bidang_unit' => $request->bidang_unit,
                'jabatan'     => $request->jabatan,
                'password'    => Hash::make($request->password),
                'role'        => 'user',
                'status'      => 'nonaktif',
            ]);

            RateLimiter::clear($ipKey);
            RateLimiter::clear($nipKey);

            return redirect()->route('register.success');

        } catch (\Throwable $e) {
            $attempts = max(
                RateLimiter::attempts($ipKey),
                RateLimiter::attempts($nipKey)
            );

            $decay = match (true) {
                $attempts >= 20 => 3600,
                $attempts >= 10 => 600,
                default => 300,
            };

            RateLimiter::hit($ipKey, $decay);
            RateLimiter::hit($nipKey, $decay);

            return back()
                ->withErrors(['register' => 'Pendaftaran gagal.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }
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
