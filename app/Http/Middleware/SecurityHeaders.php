<?php

/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Header keamanan dasar untuk semua response.
 *
 * Catatan desain:
 * - X-Frame-Options DENY: halaman ini tidak punya alasan di-embed iframe
 *   oleh pihak lain (mencegah clickjacking). Google Maps masih jalan karena
 *   embed-nya iframe KE luar, bukan halaman ini yang di-embed.
 * - CSP sengaja TIDAK diset di sini: aplikasi memakai inline script/style
 *   dan CDN (Cropper, TinyMCE, dsb.) — CSP yang keliru lebih berbahaya
 *   daripada tanpa CSP. Set nonce-based CSP saat memangkas inline script.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('X-XSS-Protection', '0');

        return $response;
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
