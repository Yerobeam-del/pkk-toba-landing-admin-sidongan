<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticatedSidongan
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        // Cek apakah sudah login dengan guard sidongan
        if (Auth::guard('sidongan')->check()) {
            return redirect()->route('sidongan.dashboard');
        }

        return $next($request);
    }
}