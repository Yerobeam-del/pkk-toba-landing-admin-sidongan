<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Login normal dari Admin Panel → bukan dari SIEDA, jadi tombol
        // "Kembali ke SIEDA" di halaman profil tidak boleh muncul.
        $request->session()->forget('sso_from_sieda');

        $user = Auth::user();

        // ===== POST-LOGIN FLOW (onboarding-style, paritas dengan SIEDA) =====
        // 1. Field pemblokir kosong (phone_number / personal_email) → onboarding
        //    dengan branding Admin Panel. SEMUA kasus verifikasi email — termasuk
        //    email pending yang sudah dikirim — ditangani DI ONBOARDING (panel
        //    OTP), bukan lagi di halaman "Cek Email" terpisah, supaya tidak ada
        //    dua alur berbeda untuk hal yang sama.
        $pendingEmail = session('pending_personal_email')
            ?? $user->personal_email;
        $hasPendingEmail = $pendingEmail && !$user->hasVerifiedPersonalEmail();

        if ($hasPendingEmail || (!\App\Support\ProfileFields::blockingComplete($user)
            && !session('onboarding_skipped'))) {
            return redirect()->route('onboarding');
        }

        // 2. Lengkap / di-skip → lanjut ke ruang kerja.
        //    Belum pernah memilih → launcher "Pilih Ruang Kerja" (kartu sesuai
        //    akses); sudah pernah → langsung ke ruang kerja tersimpan.
        //    intended() tetap dipakai supaya deep-link (login ditengah jalan)
        //    tetap kembali ke halaman yang dituju semula.
        if (empty($user->workspace)) {
            return redirect()->intended(route('workspace.pilih'));
        }

        return redirect()->intended(
            \App\Http\Controllers\WorkspaceController::url($user->workspace, $user)
        );
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Kembali ke halaman login Admin Panel (bukan landing publik) —
        // konsisten dengan alur login yang selalu berakhir di dashboard /
        // launcher ruang kerja.
        return redirect()->route('login');
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
