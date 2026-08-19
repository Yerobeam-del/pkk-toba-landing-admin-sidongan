<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SsoTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * SSO masuk dari SIEDA.
 *
 * /sso/login   — user SIEDA dikirim ke sini dengan token HMAC; setelah
 *                diverifikasi, user langsung login di Admin Panel tanpa
 *                mengetik kredensial. Token sekali pakai + whitelist tujuan.
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

        $token = (string) $request->query('token', '');
        $data = $sso->verify($token);

        if (!$data) {
            Log::warning('SSO login ditolak: token tidak valid/kedaluwarsa.', ['ip' => $request->ip()]);
            return redirect()->route('login')->withErrors([
                'email' => 'Tautan SSO tidak valid atau kedaluwarsa. Silakan login ke Admin Panel terlebih dahulu.',
            ]);
        }

        $email = mb_strtolower(trim($data['email']));
        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (!$user) {
            Log::warning("SSO login ditolak: akun {$email} tidak ditemukan di Admin Panel.");
            return redirect()->route('login')->withErrors([
                'email' => 'Akun tidak ditemukan di Admin Panel.',
            ]);
        }

        // Token sekali pakai — cegah replay dalam masa berlaku 5 menit.
        $cacheKey = 'sso_token_used_' . hash('sha256', $token);
        if (Cache::has($cacheKey)) {
            Log::warning("SSO login ditolak: token sudah dipakai ({$email}).");
            return redirect()->route('login')->withErrors([
                'email' => 'Tautan SSO sudah digunakan. Silakan coba lagi dari aplikasi SIEDA.',
            ]);
        }
        Cache::put($cacheKey, true, now()->addSeconds(300));

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

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
     * Pastikan tujuan hanya halaman yang dikenal, supaya token SSO
     * tidak bisa dipakai untuk melempar user ke URL sebarang.
     */
    private function normalizeReturn(string $path): string
    {
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
