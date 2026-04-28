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
        $nip = strtolower(trim($request->input('nip', '')));

        $ipKey  = 'register.ip.' . sha1($request->ip());
        $nipKey = 'register.nip.' . sha1($nip);

        if (
            $this->limiter->tooManyAttempts($ipKey, 10) ||
            $this->limiter->tooManyAttempts($nipKey, 5)
        ) {
            $seconds = max(
                $this->limiter->availableIn($ipKey),
                $this->limiter->availableIn($nipKey)
            );

            return back()
                ->withErrors([
                    'throttle' => "Terlalu banyak percobaan pendaftaran. Coba lagi dalam {$seconds} detik.",
                ])
                ->withInput($request->except('password', 'password_confirmation'));
        }

        return $next($request);
    }
}