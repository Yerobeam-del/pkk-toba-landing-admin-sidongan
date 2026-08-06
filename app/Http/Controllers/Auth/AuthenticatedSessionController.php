<?php

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

        // ===== PERSONAL EMAIL FLOW AFTER LOGIN =====
        // 1. Belum diset → redirect ke halaman setup
        if (!$user->personal_email) {
            return redirect()->route('personal-email.setup');
        }

        // 2. Sudah diset tapi belum diverifikasi → redirect ke notice
        if (!$user->hasVerifiedPersonalEmail()) {
            return redirect()->route('personal-email.notice');
        }

        // 3. Sudah diverifikasi → lanjut ke dashboard
        return redirect()->intended(route('admin.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
