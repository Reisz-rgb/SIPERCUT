<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ActiveUserMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && method_exists(Auth::user(), 'isActive') && ! Auth::user()->isActive()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'login' => 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.'
                ]);
        }

        return $next($request);
    }
}
