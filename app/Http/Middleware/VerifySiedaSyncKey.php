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
 * SIEDA memanggil endpoint Admin Panel ini (mis. sinkronisasi avatar) dengan:
 *
 *   - X-Sieda-Key       : shared secret (SIEDA_SYNC_SECRET, wajib)
 *   - X-Sieda-Timestamp : unix timestamp saat request (wajib, anti-replay ±5 menit)
 *   - X-Sieda-Signature : hash_hmac('sha256', "{timestamp}.{raw_body}", secret) (wajib)
 *
 * Ketiga header sekarang WAJIB. Signature yang opsional berarti key yang
 * bocor saja sudah cukup untuk memanggil endpoint; dengan HMAC wajib,
 * penyerang harus membaca secret di memori/proses SIEDA saat menyusun request.
 *
 * Pastikan SIEDA_SYNC_SECRET di .env pkk-toba SAMA dengan di .env SIEDA.
 */
class VerifySiedaSyncKey
{
    /** Toleransi clock skew (detik) untuk X-Sieda-Timestamp. */
    private const MAX_CLOCK_SKEW = 300;

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
        $signature   = $request->header('X-Sieda-Signature');
        $timestamp   = $request->header('X-Sieda-Timestamp');

        // Verifikasi key dengan timing-safe comparison
        if (empty($providedKey) || !hash_equals($secret, $providedKey)) {
            Log::warning('[SiedaSync] Invalid sync key attempt', [
                'ip'         => $request->ip(),
                'path'       => $request->path(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        // Timestamp + signature keduanya WAJIB (anti-replay + integritas body).
        // Sebelumnya keduanya opsional — cukup key untuk lewat, yang membuat
        // kebocoran key saja sudah setara akses penuh ke endpoint ini.
        if (empty($timestamp) || empty($signature)) {
            Log::warning('[SiedaSync] Missing timestamp/signature headers', [
                'ip'   => $request->ip(),
                'path' => $request->path(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        // Tolak request yang menyimpang lebih dari ±5 menit dari waktu server
        // (mencegah replay signature lama). Sisi SIEDA mengirim unix timestamp;
        // kadang dikirim dalam milidetik oleh salah konfigurasi — konversikan.
        $requestTime = (int) $timestamp;
        if (abs((int) (microtime(true) * 1000) - $requestTime * 1000) > self::MAX_CLOCK_SKEW * 1000
            && abs(time() - $requestTime) > self::MAX_CLOCK_SKEW) {
            Log::warning('[SiedaSync] Request timestamp outside allowed window', [
                'ip'        => $request->ip(),
                'timestamp' => $timestamp,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Request expired',
            ], 401);
        }

        // Verifikasi HMAC signature atas timestamp + raw body
        $payload           = $timestamp . '.' . $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($expectedSignature, $signature)) {
            Log::warning('[SiedaSync] Invalid HMAC signature', [
                'ip'   => $request->ip(),
                'path' => $request->path(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid signature',
            ], 401);
        }

        return $next($request);
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
