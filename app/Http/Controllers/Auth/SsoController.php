<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SsoTokenService;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

/**
 * SSO masuk dari SIEDA.
 *
 * /sso/login   — user SIEDA dikirim ke sini dengan token HMAC; setelah
 *                diverifikasi, user langsung login di Admin Panel tanpa
 *                mengetik kredensial. Token sekali pakai + whitelist tujuan.
 *                Percobaan gagal dibatasi rate limit (anti brute-force).
 * /sso/back    — balik ke SIEDA: buat token baru dan lempar user ke
 *                callback SIEDA, sesi SIEDA otomatis pulih.
 */
class SsoController extends Controller
{
    /** Halaman Admin Panel yang boleh menjadi tujuan SSO. */
    private const RETURN_WHITELIST = [
        '/admin/profile',
        '/admin/profile/password',
        '/personal-email',
        '/personal-email/notice',
        '/admin',
        '/',
    ];

    // ==========================================
    // RATE LIMITING /sso/login (anti brute-force HMAC)
    // ==========================================
    // Hanya percobaan GAGAL yang dihitung (token tidak valid, akun tak
    // dikenal, token replay) supaya user sah di balik NAT kantor tidak
    // ikut terkunci. Dua lapis: per-IP dan GLOBAL — lapis global menutup
    // celah rotasi header X-Forwarded-For (trustProxies: '*').
    private const MAX_FAILED_ATTEMPTS_PER_IP = 10;
    private const MAX_FAILED_ATTEMPTS_GLOBAL = 50;
    private const THROTTLE_DECAY_SECONDS = 300;

    public function login(Request $request, SsoTokenService $sso)
    {
        // Sudah login di Admin Panel → langsung lanjut ke tujuan.
        if (Auth::guard('web')->check()) {
            // Catat kalau user datang lewat SSO SIEDA (token sah), supaya
            // tombol "Kembali ke SIEDA" muncul di halaman profil.
            if ($sso->verify((string) $request->query('token', ''))) {
                $request->session()->put('sso_from_sieda', true);
            }
            return redirect()->to($this->normalizeReturn($request->query('return', '/admin/profile')));
        }

        // Rate limit hanya jalur autentikasi token — user yang sudah login
        // tidak mengonsumsi kuota (tidak ada permukaan brute-force).
        $this->assertNotThrottled($request);

        $token = (string) $request->query('token', '');
        $data = $sso->verify($token);

        if (!$data) {
            $this->recordFailedAttempt($request);
            Log::warning('SSO login ditolak: token tidak valid/kedaluwarsa.', ['ip' => $request->ip()]);
            return redirect()->route('login')->withErrors([
                'email' => 'Tautan SSO tidak valid atau kedaluwarsa. Silakan login ke Admin Panel terlebih dahulu.',
            ]);
        }

        $email = mb_strtolower(trim($data['email']));
        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (!$user) {
            $this->recordFailedAttempt($request);
            Log::warning("SSO login ditolak: akun {$email} tidak ditemukan di Admin Panel.");
            return redirect()->route('login')->withErrors([
                'email' => 'Akun tidak ditemukan di Admin Panel.',
            ]);
        }

        // Token sekali pakai — cegah replay dalam masa berlaku 5 menit.
        $cacheKey = 'sso_token_used_' . hash('sha256', $token);
        if (Cache::has($cacheKey)) {
            $this->recordFailedAttempt($request);
            Log::warning("SSO login ditolak: token sudah dipakai ({$email}).");
            return redirect()->route('login')->withErrors([
                'email' => 'Tautan SSO sudah digunakan. Silakan coba lagi dari aplikasi SIEDA.',
            ]);
        }
        Cache::put($cacheKey, true, now()->addSeconds(300));

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        // Login sah → reset kuota gagal per-IP supaya IP kantor/NAT dengan
        // sesekali kesalahan tidak menumpuk (lapis global tetap berjalan).
        RateLimiter::clear('sso-login:ip:' . sha1((string) $request->ip()));

        // Tandai sesi ini berasal dari SIEDA — tombol "Kembali ke SIEDA"
        // di halaman profil hanya muncul dalam kondisi ini.
        $request->session()->put('sso_from_sieda', true);

        return redirect()->to($this->normalizeReturn($data['return'] ?? '/admin/profile'));
    }

    public function back(Request $request, SsoTokenService $sso)
    {
        if (!Auth::guard('web')->check()) {
            return redirect()->route('login');
        }

        return redirect()->to($sso->buildCallbackUrl());
    }

    /**
     * Lempar 429 bila kuota percobaan gagal /sso/login terlampaui.
     * Dua lapis: per-IP dan global (anti rotasi X-Forwarded-For).
     */
    private function assertNotThrottled(Request $request): void
    {
        $checks = [
            'per-ip' => ['sso-login:ip:' . sha1((string) $request->ip()), self::MAX_FAILED_ATTEMPTS_PER_IP],
            'global' => ['sso-login:global', self::MAX_FAILED_ATTEMPTS_GLOBAL],
        ];

        foreach ($checks as $scope => [$key, $maxAttempts]) {
            if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                $seconds = max(RateLimiter::availableIn($key), 1);

                Log::warning("SSO login dibatasi rate limit ({$scope}).", [
                    'ip' => $request->ip(),
                    'retry_after' => $seconds . ' detik',
                ]);

                throw new ThrottleRequestsException(
                    'Terlalu banyak percobaan login SSO. Silakan coba lagi dalam ' . ceil($seconds / 60) . ' menit.',
                    headers: ['Retry-After' => $seconds]
                );
            }
        }
    }

    /**
     * Catat satu percobaan gagal (token tidak valid / akun tak dikenal /
     * token replay) ke penghitung per-IP dan global.
     */
    private function recordFailedAttempt(Request $request): void
    {
        RateLimiter::hit('sso-login:ip:' . sha1((string) $request->ip()), self::THROTTLE_DECAY_SECONDS);
        RateLimiter::hit('sso-login:global', self::THROTTLE_DECAY_SECONDS);
    }

    /**
     * Pastikan tujuan hanya halaman yang dikenal, supaya token SSO
     * tidak bisa dipakai untuk melempar user ke URL sebarang.
     */
    private function normalizeReturn(string $path): string
    {
        // Token tanpa tujuan (return kosong, mis. dari buildCallbackUrl())
        // diarahkan ke halaman default — bukan ke '/' (landing publik).
        $path = trim($path);
        if ($path === '') {
            return '/admin/profile';
        }

        $clean = '/' . ltrim(parse_url($path, PHP_URL_PATH) ?: '/', '/');

        foreach (self::RETURN_WHITELIST as $allowed) {
            if ($clean === $allowed || str_starts_with($clean, $allowed . '/')) {
                return $clean;
            }
        }

        return '/admin/profile';
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
