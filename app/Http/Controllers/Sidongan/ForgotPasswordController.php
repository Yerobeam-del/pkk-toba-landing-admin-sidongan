<?php

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
     * Handle an incoming password reset link request for SIDONGAN.
     *
     * @throws ValidationException
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

            return back()->with('status', 'Terlalu banyak permintaan. Silakan coba lagi dalam ' . ceil($seconds / 60) . ' menit.');
        }

        // ==========================================
        // AUDIT LOG & PROSES
        // ==========================================
        $user = User::where('email', $email)->first();

        if (!$user || !$user->hasSidonganAccess()) {
            // Jangan ungkap apakah email terdaftar (keamanan)
            Log::channel('audit')->info('Reset password diminta — email tidak valid/tanpa akses SIDONGAN', [
                'email' => $email,
                'ip' => $ip,
                'user_agent' => $request->userAgent(),
                'user_found' => $user ? 'yes' : 'no',
                'has_sidongan_access' => $user ? ($user->hasSidonganAccess() ? 'yes' : 'no') : 'n/a',
                'timestamp' => now()->toIso8601String(),
            ]);

            RateLimiter::hit($throttleKey, 1800); // 30 menit cooldown

            return back()->with('status', 'Link reset password telah dikirim ke email Anda.');
        }

        // Generate token dan kirim notifikasi SIDONGAN
        $token = Password::createToken($user);
        $user->sendSidonganPasswordResetNotification($token);

        Log::channel('audit')->info('Reset password berhasil dikirim (SIDONGAN)', [
            'email' => $email,
            'ip' => $ip,
            'user_agent' => $request->userAgent(),
            'user_id' => $user->id,
            'user_name' => $user->name,
            'sidongan_role' => $user->sidongan_role,
            'timestamp' => now()->toIso8601String(),
        ]);

        RateLimiter::hit($throttleKey, 1800); // 30 menit cooldown

        return back()->with('status', 'Link reset password telah dikirim ke email Anda.');
    }
}
