<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    // =========================================================================
    // KONSTANTA RATE LIMITER
    // =========================================================================
    private const LOGIN_MAX_ATTEMPTS    = 5;
    private const LOGIN_DECAY_SECONDS   = 60;
    private const REGISTER_MAX_ATTEMPTS = 10;
    private const REGISTER_DECAY_BASE   = 300;

    // =========================================================================
    // SHOW LOGIN
    // =========================================================================
    public function showLogin()
    {
        // Jika user sudah login tapi statusnya nonaktif, paksa logout langsung.
        if (Auth::check() && ! Auth::user()->isActive()) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return view('auth.LoginPage')
                ->withErrors([
                    'login' => 'Akun Anda tidak aktif. Silakan hubungi administrator.',
                ]);
        }

        return Auth::check()
            ? $this->redirectBasedOnRole()
            : view('auth.LoginPage');
    }

    // =========================================================================
    // LOGIN
    // =========================================================================
    public function login(Request $request)
    {
        $nip        = $this->sanitizeNumeric($request->nip ?? '');
        $ipKey      = 'login.ip.'      . sha1($request->ip());
        $accountKey = 'login.account.' . sha1($nip);

        if (
            RateLimiter::tooManyAttempts($ipKey, self::LOGIN_MAX_ATTEMPTS) ||
            RateLimiter::tooManyAttempts($accountKey, self::LOGIN_MAX_ATTEMPTS)
        ) {
            Log::warning('Login throttled', [
                'nip' => $nip,
                'ip'  => $request->ip(),
            ]);

            return back()
                ->withErrors(['throttle' => 'Terlalu banyak percobaan login. Coba lagi nanti.'])
                ->withInput();
        }

        $validator = Validator::make($request->all(), [
            'nip'      => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            RateLimiter::hit($ipKey, self::LOGIN_DECAY_SECONDS);

            return back()->withErrors($validator)->withInput();
        }

        $credentials = ['nip' => $nip, 'password' => $request->password];

        if (! Auth::attempt($credentials, $request->filled('remember'))) {
            RateLimiter::hit($ipKey, self::LOGIN_DECAY_SECONDS);
            RateLimiter::hit($accountKey, self::LOGIN_DECAY_SECONDS);

            Log::warning('Login gagal', [
                'nip' => $nip,
                'ip'  => $request->ip(),
            ]);

            return back()
                ->withErrors(['login' => 'NIP atau password salah.'])
                ->withInput();
        }

        RateLimiter::clear($ipKey);
        RateLimiter::clear($accountKey);
        $request->session()->regenerate();

        Log::info('Login berhasil', [
            'nip' => $nip,
            'ip'  => $request->ip(),
        ]);

        return $this->redirectBasedOnRole();
    }

    // =========================================================================
    // SHOW REGISTER
    // =========================================================================
    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }

        [$unitKerjaOptions, $jabatanOptions] = $this->fetchDropdownOptions();

        return view('auth.RegisterPage', compact('unitKerjaOptions', 'jabatanOptions'));
    }

    // =========================================================================
    // REGISTER
    // =========================================================================
    public function register(Request $request)
    {
        $nip    = $this->sanitizeNumeric($request->nip ?? '');
        $ipKey  = 'register.ip.'  . sha1($request->ip());
        $nipKey = 'register.nip.' . sha1($nip);

        if (
            RateLimiter::tooManyAttempts($ipKey, self::REGISTER_MAX_ATTEMPTS) ||
            RateLimiter::tooManyAttempts($nipKey, self::REGISTER_MAX_ATTEMPTS)
        ) {
            Log::warning('Register throttled', [
                'nip' => $nip,
                'ip'  => $request->ip(),
            ]);

            return back()
                ->withErrors(['register' => 'Terlalu banyak percobaan pendaftaran. Coba lagi nanti.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }

        [$unitKerjaOptions, $jabatanOptions] = $this->fetchDropdownOptions();

        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'nip'      => 'required|string|unique:users,nip',
            'phone'    => 'required|string|unique:users,phone',
            'email'    => 'required|email:rfc,dns|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',

            'bidang_unit' => [
                'required',
                'string',
                'max:255',
                Rule::in($unitKerjaOptions->toArray()),
            ],
            'jabatan' => [
                'required',
                'string',
                'max:255',
                Rule::in($jabatanOptions->toArray()),
            ],
        ], [
            'name.required'        => 'Nama wajib diisi.',
            'nip.required'         => 'NIP wajib diisi.',
            'nip.unique'           => 'NIP sudah terdaftar.',
            'phone.required'       => 'Nomor HP wajib diisi.',
            'phone.unique'         => 'Nomor HP sudah terdaftar.',
            'email.required'       => 'Email wajib diisi.',
            'email.email'          => 'Format email tidak valid.',
            'email.unique'         => 'Email sudah terdaftar.',
            'bidang_unit.required' => 'Unit kerja wajib diisi.',
            'bidang_unit.in'       => 'Unit kerja yang dipilih tidak valid.',
            'jabatan.required'     => 'Jabatan wajib diisi.',
            'jabatan.in'           => 'Jabatan yang dipilih tidak valid.',
            'password.required'    => 'Password wajib diisi.',
            'password.min'         => 'Password minimal 8 karakter.',
            'password.confirmed'   => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            RateLimiter::hit($ipKey, self::REGISTER_DECAY_BASE);
            RateLimiter::hit($nipKey, self::REGISTER_DECAY_BASE);

            return back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        try {
            User::create([
                'name'        => $this->sanitizeString($request->name),
                'nip'         => $nip,
                'phone'       => $this->sanitizeNumeric($request->phone),
                'email'       => $this->sanitizeEmail($request->email),
                'bidang_unit' => $this->sanitizeString($request->bidang_unit),
                'jabatan'     => $this->sanitizeString($request->jabatan),
                'password'    => Hash::make($request->password),
                'role'        => 'user',     
                'status'      => 'nonaktif',  
            ]);

            RateLimiter::clear($ipKey);
            RateLimiter::clear($nipKey);

            Log::info('Registrasi berhasil', [
                'nip' => $nip,
                'ip'  => $request->ip(),
            ]);

            return redirect()->route('register.success');

        } catch (\Throwable $e) {
            // Adaptive decay: semakin banyak percobaan, semakin lama lockout.
            $attempts = max(
                RateLimiter::attempts($ipKey),
                RateLimiter::attempts($nipKey)
            );

            $decay = match (true) {
                $attempts >= 20 => 3600,
                $attempts >= 10 => 600,
                default         => self::REGISTER_DECAY_BASE,
            };

            RateLimiter::hit($ipKey, $decay);
            RateLimiter::hit($nipKey, $decay);

            Log::error('Registrasi gagal (exception)', [
                'nip'   => $nip,
                'ip'    => $request->ip(),
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['register' => 'Pendaftaran gagal. Silakan coba lagi.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }

    // =========================================================================
    // LOGOUT
    // =========================================================================
    public function logout(Request $request)
    {
        $nip = Auth::user()?->nip;

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('Logout', [
            'nip' => $nip,
            'ip'  => $request->ip(),
        ]);

        return redirect()->route('landing');
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Redirect berdasarkan role user yang sedang login.
     */
    private function redirectBasedOnRole()
    {
        return Auth::user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('user.dashboard');
    }

    /**
     * Ambil opsi dropdown bidang_unit dan jabatan dari database.
     * Dipanggil di showRegister() dan register() agar data whitelist konsisten.
     *
     * @return array{0: \Illuminate\Support\Collection, 1: \Illuminate\Support\Collection}
     */
    private function fetchDropdownOptions(): array
    {
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

        return [$unitKerjaOptions, $jabatanOptions];
    }

    private function sanitizeNumeric(string $value): string
    {
        return preg_replace('/[^0-9]/', '', $value);
    }

    private function sanitizeString(string $value): string
    {
        return strip_tags(trim($value));
    }

    private function sanitizeEmail(string $value): string
    {
        return filter_var(trim($value), FILTER_SANITIZE_EMAIL);
    }
}