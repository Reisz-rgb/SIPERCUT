<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;

class ThrottleRegister
{
    public function __construct(protected RateLimiter $limiter) {}

    public function handle(Request $request, Closure $next)
    {
        // Key berdasarkan IP — NIP belum tentu valid saat register
        $key = 'register.' . sha1($request->ip());

        if ($this->limiter->tooManyAttempts($key, 5)) {
            $seconds = $this->limiter->availableIn($key);

            return back()
                ->withErrors([
                    'throttle' => "Terlalu banyak percobaan pendaftaran. Coba lagi dalam {$seconds} detik.",
                ])
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $response = $next($request);

        // Hanya increment saat register GAGAL (validasi error / redirect back)
        // Register sukses = redirect ke register.success (bukan back())
        if (session('register_failed')) {
            $this->limiter->hit($key, 300); // window 5 menit
            session()->forget('register_failed');
        }

        return $response;
    }
}