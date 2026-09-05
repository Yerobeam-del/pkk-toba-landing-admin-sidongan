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

            // Redirect langsung ke onboarding bila field pemblokir belum lengkap
            // — paritas dengan SIEDA (LoginController) yang memeriksa kelengkapan
            // saat login, bukan lewat bounce middleware dashboard. Field pemblokir
            // didefinisikan sekali di App\Support\ProfileFields.
            //
            // PENGECUALIAN: user yang sebelumnya memilih "Lewati — nanti saja"
            // (tersimpan di DB users.onboarding_skipped_at / session) TIDAK
            // dilempar ke onboarding lagi di setiap login — tanpa ini user yang
            // belum lengkap akan terjebak loop onboarding setiap kali login.
            if (!ProfileFields::blockingComplete($user)
                && !$user->onboarding_skipped_at
                && !session('onboarding_skipped')) {
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
