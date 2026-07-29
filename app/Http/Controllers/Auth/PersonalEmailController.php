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
    public function showSetupForm(): View|RedirectResponse
    {
        $user = Auth::user();

        // Kalau sudah diverifikasi di DB, redirect ke dashboard
        if ($user->hasVerifiedPersonalEmail()) {
            return redirect()->intended(route('admin.dashboard'))
                ->with('info', 'Email pribadi <strong>' . e($user->personal_email) . '</strong> sudah diverifikasi.');
        }

        // Cek apakah ada email yang sedang menunggu verifikasi di session
        $pendingEmail = session('pending_personal_email');

        if ($pendingEmail) {
            return view('auth.setup-personal-email', [
                'existing_email' => $pendingEmail,
                'needs_verification' => true,
            ]);
        }

        return view('auth.setup-personal-email', [
            'existing_email' => null,
            'needs_verification' => false,
        ]);
    }

    /**
     * Validasi email, simpan di SESSION (bukan DB), lalu kirim link verifikasi.
     * Email akan disimpan ke DB hanya setelah user mengklik link verifikasi.
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

        $email = $request->personal_email;

        // Simpan email di SESSION — BELUM masuk database
        session(['pending_personal_email' => $email]);

        // Kirim notifikasi verifikasi ke email tersebut
        try {
            $user->notify(new PersonalEmailVerificationNotification($email));
        } catch (\Throwable $e) {
            // Gagal kirim → hapus session
            session()->forget('pending_personal_email');

            Log::channel('audit')->warning('Gagal kirim email verifikasi personal email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('personal-email.notice')
                ->with('error', 'Email verifikasi gagal dikirim ke <strong>' . e($email) . '</strong>. Silakan klik "Kirim Ulang" untuk mencoba lagi.');
        }

        Log::channel('audit')->info('Personal email menunggu verifikasi (session)', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'personal_email' => $email,
            'login_email' => $user->email,
        ]);

        return redirect()->route('personal-email.notice')
            ->with('success', 'Email verifikasi telah dikirim ke <strong>' . e($email) . '</strong>. Silakan cek inbox email Anda.');
    }

    /**
     * Tampilkan halaman "Cek Email Anda".
     */
    public function showNotice(): View|RedirectResponse
    {
        $user = Auth::user();

        // Kalau sudah diverifikasi, langsung redirect ke dashboard
        if ($user->hasVerifiedPersonalEmail()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        // Ambil email dari session
        $pendingEmail = session('pending_personal_email');

        // Kalau tidak ada pending email, redirect ke setup
        if (!$pendingEmail) {
            return redirect()->route('personal-email.setup');
        }

        return view('auth.personal-email-notice', [
            'personal_email' => $pendingEmail,
        ]);
    }

    /**
     * Verifikasi personal email via signed URL.
     * Email BARU disimpan ke DATABASE di sini, setelah user klik link.
     */
    public function verify(Request $request, $id): RedirectResponse
    {
        $user = Auth::user();

        // Pastikan user yang login adalah pemilik link
        if ((int) $user->id !== (int) $id) {
            abort(403, 'Link verifikasi tidak sesuai dengan akun Anda.');
        }

        // Ambil email dari signed URL
        $email = $request->query('email');

        if (!$email) {
            return redirect()->route('personal-email.setup')
                ->with('error', 'Link verifikasi tidak valid. Silakan daftarkan email pribadi Anda kembali.');
        }

        // Jika sudah diverifikasi sebelumnya, lewati
        if ($user->hasVerifiedPersonalEmail()) {
            return redirect()->intended(route('admin.dashboard'))
                ->with('info', 'Email pribadi sudah diverifikasi sebelumnya.');
        }

        // Simpan ke DATABASE — BARU SEKARANG!
        $user->personal_email = $email;
        $user->personal_email_verified_at = now();
        $user->save();

        // Bersihkan session
        session()->forget('pending_personal_email');

        Log::channel('audit')->info('Personal email berhasil diverifikasi & disimpan', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'personal_email' => $email,
        ]);

        return redirect()->intended(route('admin.dashboard'))
            ->with('success', '🎉 Email pribadi <strong>' . e($email) . '</strong> berhasil diverifikasi! Sekarang Anda bisa menggunakan fitur Lupa Password.');
    }

    /**
     * Kirim ulang link verifikasi (email diambil dari session).
     * Rate limit: 3x per 30 menit per user.
     */
    public function resend(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Kalau sudah diverifikasi, redirect ke dashboard
        if ($user->hasVerifiedPersonalEmail()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        // Ambil email dari session
        $email = session('pending_personal_email');

        // Kalau tidak ada pending email, redirect ke setup
        if (!$email) {
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
                'personal_email' => $email,
                'cooldown_remaining' => $seconds . ' detik',
            ]);

            return back()->with('error', 'Terlalu banyak permintaan. Silakan coba lagi dalam ' . $minutes . ' menit.');
        }

        // Kirim ulang notifikasi verifikasi
        try {
            $user->notify(new PersonalEmailVerificationNotification($email));
            RateLimiter::hit($throttleKey, 1800); // 30 menit cooldown

            Log::channel('audit')->info('Resend verifikasi personal email', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'personal_email' => $email,
            ]);

            return back()->with('success', 'Email verifikasi telah dikirim ulang ke <strong>' . e($email) . '</strong>.');
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
        // Hapus pending email dari session jika ada
        session()->forget('pending_personal_email');

        return redirect()->intended(route('admin.dashboard'))
            ->with('info', 'Anda bisa setup email pribadi nanti melalui menu Profil.');
    }
}
