<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Http\Controllers\Sidongan;

use App\Http\Controllers\Controller;
use App\Support\ProfileFields;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Tampilkan form login SIDONGAN
     */
    public function showLoginForm()
    {
        // Jika sudah login sebagai sidongan, redirect ke dashboard
        if (Auth::guard('sidongan')->check()) {
            return redirect()->route('sidongan.dashboard');
        }
        
        // Logout dari guard lain untuk mencegah konflik
        Auth::guard('web')->logout();
        
        return view('sidongan-auth.login');
    }

    /**
     * Handle login SIDONGAN
     */
    public function login(Request $request)
    {
        // Logout dari guard lain untuk mencegah konflik session
        Auth::guard('web')->logout();
        
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password harus diisi',
        ]);

        // Attempt login dengan guard 'sidongan'
        if (Auth::guard('sidongan')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // Clear any old session data
            $request->session()->forget('url.intended');

            $user = Auth::guard('sidongan')->user();

            // ===== POST-LOGIN FLOW (paritas dengan Admin Panel) =====
            // 1. Email pribadi pending (terisi tapi belum terverifikasi) →
            //    onboarding, panel OTP "Cek Email" — SATU PINTU untuk semua
            //    sistem: verifikasi terjadi di onboarding, bukan lagi via link
            //    di halaman profil. Email pending diadopsi ke session supaya
            //    panel dan tombol "Kirim kode" bekerja dari titik masuk mana pun.
            //
            // 2. Field pemblokir kosong → onboarding juga — paritas dengan
            //    SIEDA (LoginController) yang memeriksa kelengkapan saat login.
            //    Field pemblokir didefinisikan sekali di App\Support\ProfileFields.
            //
            // PENGECUALIAN (untuk kasus 2): user yang sebelumnya memilih
            // "Lewati — nanti saja" (tersimpan di DB users.onboarding_skipped_at
            // / session) TIDAK dilempar ke onboarding lagi di setiap login —
            // tanpa ini user yang belum lengkap akan terjebak loop onboarding
            // setiap kali login. Email pending TETAP didahulukan di atas skip
            // (konsisten dengan Admin Panel) — panel punya tautan
            // "Verifikasi nanti & masuk dashboard" sehingga tidak pernah mengurung.
            $pendingEmail = session('pending_personal_email')
                ?? $user->personal_email;
            $hasPendingEmail = $pendingEmail && !$user->hasVerifiedPersonalEmail();

            if ($hasPendingEmail
                || (!ProfileFields::blockingComplete($user)
                    && !$user->onboarding_skipped_at
                    && !session('onboarding_skipped'))) {
                if ($hasPendingEmail && !session('pending_personal_email')) {
                    session(['pending_personal_email' => $pendingEmail]);
                }

                return redirect()->route('onboarding');
            }

            return redirect()->intended(route('sidongan.dashboard'));
        }

        // Jika gagal, logout dan throw validation error
        Auth::guard('sidongan')->logout();
        
        throw ValidationException::withMessages([
            'email' => ['Email atau password yang Anda masukkan salah.'],
        ]);
    }

    /**
     * Logout SIDONGAN
     */
    public function logout(Request $request)
    {
        Auth::guard('sidongan')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('sidongan.login');
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
