<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SidonganAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login via guard sidongan
        if (!Auth::guard('sidongan')->check()) {
            // Redirect ke login SIDONGAN (bukan admin)
            return redirect()->route('sidongan.login');
        }

        $user = Auth::guard('sidongan')->user();
        
        // Cek apakah user punya akses SIDONGAN
        if (empty($user->sidongan_role)) {
            Auth::guard('sidongan')->logout();
            return redirect()->route('sidongan.login')->withErrors([
                'email' => 'Akun ini tidak memiliki akses ke SIDONGAN.'
            ]);
        }

        return $next($request);
    }
}