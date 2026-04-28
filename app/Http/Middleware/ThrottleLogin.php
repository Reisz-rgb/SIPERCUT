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
        $nip = strtolower(trim($request->input('nip', '')));

        $ipKey      = 'login.ip.' . sha1($request->ip());
        $accountKey = 'login.account.' . sha1($nip);

        if (
            $this->limiter->tooManyAttempts($ipKey, 20) ||
            $this->limiter->tooManyAttempts($accountKey, 5)
        ) {
            $seconds = max(
                $this->limiter->availableIn($ipKey),
                $this->limiter->availableIn($accountKey)
            );

            return back()
                ->withErrors([
                    'throttle' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
                ])
                ->withInput($request->only('nip'));
        }

        return $next($request);
    }
}