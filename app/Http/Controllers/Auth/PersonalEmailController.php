<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\PersonalEmailVerificationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class PersonalEmailController extends Controller
{
    /**
     * Tampilkan halaman setup personal email.
     */
    public function showSetupForm(): View
    {
        $user = Auth::user();

        // Jika sudah punya personal_email tapi belum diverifikasi,
        // kasih opsi untuk ganti atau kirim ulang verifikasi
        if ($user->personal_email && !$user->hasVerifiedPersonalEmail()) {
            return view('auth.setup-personal-email', [
                'existing_email' => $user->personal_email,
                'needs_verification' => true,
            ]);
        }

        return view('auth.setup-personal-email', [
            'existing_email' => null,
            'needs_verification' => false,
        ]);
    }

    /**
     * Simpan personal email dan kirim link verifikasi ke email tersebut.
     * Email TIDAK langsung diverifikasi — user harus klik link di email.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'personal_email' => [
                'required',
                'email',
                'max:255',
                'different:email', // Pastikan berbeda dari login email @pkk-toba.id
                'unique:users,personal_email,' . $user->id,
            ],
        ]);

        // Simpan personal email — BELUM diverifikasi
        $user->personal_email = $request->personal_email;
        $user->personal_email_verified_at = null;
        $user->save();

        // Kirim notifikasi verifikasi ke personal email
        // Bungkus try-catch agar kegagalan email TIDAK menyebabkan error 500
        try {
            $user->notify(new PersonalEmailVerificationNotification());
        } catch (\Throwable $e) {
            Log::channel('audit')->warning('Gagal kirim email verifikasi personal email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('personal-email.notice')
                ->with('error', 'Email verifikasi gagal dikirim ke <strong>' . e($request->personal_email) . '</strong>. Silakan klik "Kirim Ulang" untuk mencoba lagi.');
        }

        Log::channel('audit')->info('Personal email disimpan, menunggu verifikasi', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'personal_email' => $user->personal_email,
            'login_email' => $user->email,
        ]);

        return redirect()->route('personal-email.notice')
            ->with('success', 'Email verifikasi telah dikirim ke <strong>' . e($request->personal_email) . '</strong>. Silakan cek inbox email Anda.');
    }

    /**
     * Tampilkan halaman "Cek Email Anda" — memberi tahu user untuk cek inbox.
     */
    public function showNotice(): View|RedirectResponse
    {
        $user = Auth::user();

        // Kalau sudah diverifikasi, langsung redirect ke dashboard
        if ($user->hasVerifiedPersonalEmail()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        // Kalau personal_email belum diset, redirect ke setup
        if (!$user->personal_email) {
            return redirect()->route('personal-email.setup');
        }

        return view('auth.personal-email-notice', [
            'personal_email' => $user->personal_email,
        ]);
    }

    /**
     * Verifikasi personal email via signed URL.
     * Route sudah menggunakan middleware 'signed' — URL otomatis divalidasi.
     */
    public function verify(Request $request, $id): RedirectResponse
    {
        $user = Auth::user();

        // Pastikan user yang login adalah pemilik link
        if ((int) $user->id !== (int) $id) {
            abort(403, 'Link verifikasi tidak sesuai dengan akun Anda.');
        }

        // Cek apakah email pribadi sudah terisi
        if (!$user->personal_email) {
            return redirect()->route('personal-email.setup')
                ->with('error', 'Silakan daftarkan email pribadi Anda terlebih dahulu.');
        }

        // Jika sudah diverifikasi sebelumnya, lewati
        if ($user->hasVerifiedPersonalEmail()) {
            return redirect()->intended(route('admin.dashboard'))
                ->with('info', 'Email pribadi sudah diverifikasi sebelumnya.');
        }

        // Tandai sebagai terverifikasi
        $user->personal_email_verified_at = now();
        $user->save();

        Log::channel('audit')->info('Personal email berhasil diverifikasi', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'personal_email' => $user->personal_email,
        ]);

        return redirect()->intended(route('admin.dashboard'))
            ->with('success', '🎉 Email pribadi <strong>' . e($user->personal_email) . '</strong> berhasil diverifikasi! Sekarang Anda bisa menggunakan fitur Lupa Password.');
    }

    /**
     * Kirim ulang link verifikasi ke personal email.
     * Rate limit: 3x per 30 menit per user.
     */
    public function resend(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Kalau sudah diverifikasi, redirect ke dashboard
        if ($user->hasVerifiedPersonalEmail()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        // Kalau personal_email belum diset, redirect ke setup
        if (!$user->personal_email) {
            return redirect()->route('personal-email.setup');
        }

        // ===== RATE LIMIT: 3x per 30 menit per user =====
        $throttleKey = 'resend-verification:' . $user->id . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = ceil($seconds / 60);

            Log::channel('audit')->warning('Rate limit tercapai — Resend verifikasi personal email', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'personal_email' => $user->personal_email,
                'cooldown_remaining' => $seconds . ' detik',
            ]);

            return back()->with('error', 'Terlalu banyak permintaan. Silakan coba lagi dalam ' . $minutes . ' menit.');
        }

        // Kirim ulang notifikasi verifikasi
        try {
            $user->notify(new PersonalEmailVerificationNotification());
            RateLimiter::hit($throttleKey, 1800); // 30 menit cooldown

            Log::channel('audit')->info('Resend verifikasi personal email', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'personal_email' => $user->personal_email,
            ]);

            return back()->with('success', 'Email verifikasi telah dikirim ulang ke <strong>' . e($user->personal_email) . '</strong>.');
        } catch (\Throwable $e) {
            Log::channel('audit')->warning('Gagal kirim ulang email verifikasi personal email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Email verifikasi gagal dikirim ulang. Silakan coba lagi nanti.');
        }
    }

    /**
     * Lewati setup personal email — lanjut ke dashboard.
     */
    public function skip(Request $request): RedirectResponse
    {
        return redirect()->intended(route('admin.dashboard'))
            ->with('info', 'Anda bisa setup email pribadi nanti melalui menu Profil.');
    }
}
