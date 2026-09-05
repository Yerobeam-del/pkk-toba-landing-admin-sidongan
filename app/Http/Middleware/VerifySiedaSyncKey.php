<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk memverifikasi request server-to-server dari aplikasi SIEDA.
 *
 * SIEDA kini memanggil endpoint Admin Panel ini (mis. sinkronisasi avatar) dengan
 * pola yang sama seperti yang dipakai Admin Panel saat memanggil SIEDA:
 *
 *   - X-Sieda-Key       : shared secret (SIEDA_SYNC_SECRET, wajib)
 *   - X-Sieda-Timestamp : unix timestamp saat request (untuk HMAC)
 *   - X-Sieda-Signature : hash_hmac('sha256', "{timestamp}.{raw_body}", secret)
 *
 * Pastikan SIEDA_SYNC_SECRET di .env pkk-toba SAMA dengan di .env SIEDA.
 */
class VerifySiedaSyncKey
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('services.sieda.sync_secret');

        // Jika secret belum dikonfigurasi, tolak semua request (fail-closed)
        if (empty($secret)) {
            Log::warning('[SiedaSync] SIEDA_SYNC_SECRET belum dikonfigurasi');
            return response()->json([
                'success' => false,
                'message' => 'Endpoint tidak tersedia',
            ], 503);
        }

        $providedKey = $request->header('X-Sieda-Key');

        // Verifikasi key dengan timing-safe comparison
        if (empty($providedKey) || !hash_equals($secret, $providedKey)) {
            Log::warning('[SiedaSync] Invalid sync key attempt', [
                'ip' => $request->ip(),
                'path' => $request->path(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        // Verifikasi HMAC signature dengan timestamp (anti-replay)
        $signature = $request->header('X-Sieda-Signature');
        $timestamp = $request->header('X-Sieda-Timestamp');

        if ($signature && $timestamp) {
            // Tolak request yang lebih lama dari 5 menit
            $requestTime = (int) $timestamp;
            $now = time();
            if (abs($now - $requestTime) > 300) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request expired',
                ], 401);
            }

            $payload = $timestamp . '.' . $request->getContent();
            $expectedSignature = hash_hmac('sha256', $payload, $secret);

            if (!hash_equals($expectedSignature, $signature)) {
                Log::warning('[SiedaSync] Invalid HMAC signature', [
                    'ip' => $request->ip(),
                    'path' => $request->path(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid signature',
                ], 401);
            }
        }

        return $next($request);
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
