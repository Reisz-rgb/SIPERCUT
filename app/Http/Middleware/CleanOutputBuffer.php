<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CleanOutputBuffer
{
    public function handle(Request $request, Closure $next)
    {
        // Bersihkan output buffer sebelum request
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        return $next($request);
    }
}