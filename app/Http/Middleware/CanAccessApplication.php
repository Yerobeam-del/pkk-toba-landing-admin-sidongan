<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanAccessApplication
{
    public function handle(Request $request, Closure $next, $applicationId): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Super Admin bisa akses semua
        if ($user->sidongan_role === 'super_admin') {
            return $next($request);
        }

        // Admin lama (email based) bisa akses semua
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check akses aplikasi untuk user biasa
        if ($user->canAccessApplication($applicationId)) {
            return $next($request);
        }

        abort(403, 'Anda tidak memiliki akses ke aplikasi ini.');
    }
}