<?php

/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * Unified Forgot Password — detects system from email
 * ============================================================ */
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class UnifiedForgotPasswordController extends Controller
{
    /**
     * Display the unified forgot password page.
     */
    public function create(): View
    {
        return view('auth.forgot-password-unified');
    }

    /**
     * Smart forgot password handler.
     *
     * Detects whether the email belongs to SIDONGAN, Admin, or both.
     * Then applies the appropriate reset flow.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = $request->email;
        $ip = $request->ip();

        // Rate limiting: 3 requests per 30 minutes
        $throttleKey = 'unified-reset-password:' . $email . '|' . $ip;

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            Log::channel('audit')->warning('Rate limit — Unified Reset Password', [
                'email' => $email,
                'ip' => $ip,
                'cooldown' => ceil($seconds / 60) . ' menit',
            ]);

            return back()->withErrors([
                'email' => 'Terlalu banyak permintaan. Silakan coba lagi dalam ' . ceil($seconds / 60) . ' menit.',
            ])->withInput(['email' => $email]);
        }

        // Find the user
        $user = User::where('email', $email)->first();

        // CASE 1: Account not found → generic message (don't reveal existence)
        if (!$user) {
            RateLimiter::hit($throttleKey, 1800);

            Log::channel('audit')->info('Reset password — email not found', [
                'email' => $email,
                'ip' => $ip,
            ]);

            return back()->withErrors([
                'email' => 'Jika akun dengan email ini terdaftar, link reset password akan dikirim.',
            ])->withInput(['email' => $email]);
        }

        // Detect which system(s) this user has access to
        $hasPersonalEmail = !empty($user->personal_email);
        $hasVerifiedPersonalEmail = $user->hasVerifiedPersonalEmail();

        // CASE 2: User with verified personal email → send to personal email
        // (Works for BOTH SIDONGAN and Admin users)
        if ($hasPersonalEmail && $hasVerifiedPersonalEmail) {
            $token = Password::createToken($user);
            $user->sendSidonganPasswordResetNotification($token);

            RateLimiter::hit($throttleKey, 1800);

            Log::channel('audit')->info('Reset password sent to personal email', [
                'email' => $email,
                'personal_email' => $user->personal_email,
                'ip' => $ip,
                'user_id' => $user->id,
            ]);

            return back()->with('status', 'success')->with('status_message',
                'Link reset password telah dikirim ke email pribadi Anda (' . $this->maskEmail($user->personal_email) . ').'
            );
        }

        // CASE 3: User with unverified personal email
        if ($hasPersonalEmail && !$hasVerifiedPersonalEmail) {
            RateLimiter::hit($throttleKey, 1800);

            return back()->with('status', 'need_verification')->with('status_message',
                'Email pribadi Anda (' . $this->maskEmail($user->personal_email) . ') belum diverifikasi. Silakan hubungi administrator.'
            );
        }

        // CASE 4: User without personal email
        if (!$hasPersonalEmail) {
            RateLimiter::hit($throttleKey, 1800);

            return back()->with('status', 'no_personal_email')->with('status_message',
                'Akun Anda belum memiliki email pribadi. Untuk mereset password, silakan hubungi administrator.'
            );
        }

        // CASE 6: User exists but no system access → generic message
        RateLimiter::hit($throttleKey, 1800);

        return back()->withErrors([
            'email' => 'Jika akun dengan email ini terdaftar, link reset password akan dikirim.',
        ])->withInput(['email' => $email]);
    }

    /**
     * Mask email for privacy: a***@gmail.com
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
