<?php

/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Http\Controllers\Sidongan;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    /**
     * Display the password reset link request view for SIDONGAN.
     */
    public function create(): View
    {
        return view('sidongan-auth.forgot-password');
    }

    /**
     * Smart forgot password handler.
     *
     * Flow:
     * 1. User enters email → system checks if account exists
     * 2. If account exists + has verified personal email → send reset link to personal email
     * 3. If account exists + personal email not verified → show warning, suggest admin
     * 4. If account exists + no personal email → show "contact admin" message
     * 5. If account not found → generic security message (don't reveal existence)
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = $request->email;
        $ip = $request->ip();

        // ==========================================
        // RATE LIMITING: 3 request per 30 menit
        // ==========================================
        $throttleKey = 'sidongan-reset-password:' . $email . '|' . $ip;

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            Log::channel('audit')->warning('Rate limit tercapai — Reset Password (SIDONGAN)', [
                'email' => $email,
                'ip' => $ip,
                'user_agent' => $request->userAgent(),
                'cooldown_remaining' => $seconds . ' detik',
            ]);

            return back()->withErrors([
                'email' => 'Terlalu banyak permintaan. Silakan coba lagi dalam ' . ceil($seconds / 60) . ' menit.',
            ])->withInput(['email' => $email]);
        }

        // ==========================================
        // CARI AKUN
        // ==========================================
        $user = User::where('email', $email)->first();

        // CASE 1: Akun tidak ditemukan atau tidak punya akses SIDONGAN
        // → Pesan generik (jangan ungkap apakah email terdaftar)
        if (!$user || !$user->hasSidonganAccess()) {
            Log::channel('audit')->info('Reset password diminta — email tidak valid/tanpa akses SIDONGAN', [
                'email' => $email,
                'ip' => $ip,
                'user_agent' => $request->userAgent(),
                'user_found' => $user ? 'yes' : 'no',
                'has_sidongan_access' => $user ? ($user->hasSidonganAccess() ? 'yes' : 'no') : 'n/a',
                'timestamp' => now()->toIso8601String(),
            ]);

            RateLimiter::hit($throttleKey, 1800);

            // Pesan generik — jangan ungkap apakah email terdaftar
            return back()->withErrors([
                'email' => 'Jika akun dengan email ini terdaftar di SIDONGAN, link reset password akan dikirim.',
            ])->withInput(['email' => $email]);
        }

        // CASE 2: Akun ada + punya akses SIDONGAN + personal email TERVERIFIKASI
        // → Kirim reset password ke personal email
        if ($user->personal_email && $user->hasVerifiedPersonalEmail()) {
            $token = Password::createToken($user);
            $user->sendSidonganPasswordResetNotification($token);

            Log::channel('audit')->info('Reset password berhasil dikirim ke personal email (SIDONGAN)', [
                'email' => $email,
                'personal_email' => $user->personal_email,
                'ip' => $ip,
                'user_agent' => $request->userAgent(),
                'user_id' => $user->id,
                'user_name' => $user->name,
                'sidongan_role' => $user->sidongan_role,
                'timestamp' => now()->toIso8601String(),
            ]);

            RateLimiter::hit($throttleKey, 1800);

            return back()->with('status', 'success')->with('status_message',
                'Link reset password telah dikirim ke email pribadi Anda (' . $this->maskEmail($user->personal_email) . ').'
            );
        }

        // CASE 3: Akun ada + ada personal email tapi BELUM TERVERIFIKASI
        if ($user->personal_email && !$user->hasVerifiedPersonalEmail()) {
            Log::channel('audit')->warning('Reset password diminta — personal email belum diverifikasi (SIDONGAN)', [
                'email' => $email,
                'personal_email' => $user->personal_email,
                'ip' => $ip,
                'user_agent' => $request->userAgent(),
                'user_id' => $user->id,
                'timestamp' => now()->toIso8601String(),
            ]);

            RateLimiter::hit($throttleKey, 1800);

            return back()->with('status', 'need_verification')->with('status_message',
                'Email pribadi Anda (' . $this->maskEmail($user->personal_email) . ') belum diverifikasi. Silakan hubungi administrator untuk mereset password.'
            );
        }

        // CASE 4: Akun ada + TIDAK ADA personal email
        // → Suruh hubungi admin
        Log::channel('audit')->warning('Reset password diminta — tidak ada personal email (SIDONGAN)', [
            'email' => $email,
            'ip' => $ip,
            'user_agent' => $request->userAgent(),
            'user_id' => $user->id,
            'user_name' => $user->name,
            'timestamp' => now()->toIso8601String(),
        ]);

        RateLimiter::hit($throttleKey, 1800);

        return back()->with('status', 'no_personal_email')->with('status_message',
            'Akun Anda belum memiliki email pribadi. Untuk mereset password, silakan hubungi administrator.'
        );
    }

    /**
     * Mask email address for privacy display.
     * Example: a***@gmail.com
     */
    private function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) return $email;

        $name = $parts[0];
        $domain = $parts[1];

        if (strlen($name) <= 2) {
            $masked = $name[0] . '***';
        } else {
            $masked = $name[0] . str_repeat('*', min(strlen($name) - 1, 3)) . substr($name, -1);
        }

        return $masked . '@' . $domain;
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
