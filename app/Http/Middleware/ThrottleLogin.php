<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;

class ThrottleLogin
{
    public function __construct(protected RateLimiter $limiter) {}

    public function handle(Request $request, Closure $next)
    {
        $key = 'login.' . sha1($request->input('nip', '') . '|' . $request->ip());

        if ($this->limiter->tooManyAttempts($key, 5)) {
            $seconds = $this->limiter->availableIn($key);

            return back()
                ->withErrors([
                    'throttle' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
                ])
                ->withInput($request->only('nip'));
        }

        $response = $next($request);

        // Hanya increment jika credentials memang salah
        if (session('login_failed')) {
            $this->limiter->hit($key, 60);
            session()->forget('login_failed');
        }

        return $response;
    }
}