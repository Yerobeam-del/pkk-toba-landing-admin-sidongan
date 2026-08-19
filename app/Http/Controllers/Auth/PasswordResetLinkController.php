<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
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
        $throttleKey = 'reset-password:' . $email . '|' . $ip;

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            Log::channel('audit')->warning('Rate limit tercapai — Reset Password (Admin)', [
                'email' => $email,
                'ip' => $ip,
                'user_agent' => $request->userAgent(),
                'cooldown_remaining' => $seconds . ' detik',
            ]);

            throw ValidationException::withMessages([
                'email' => ['Terlalu banyak permintaan. Silakan coba lagi dalam ' . ceil($seconds / 60) . ' menit.'],
            ]);
        }

        // ==========================================
        // KIRIM RESET LINK
        // ==========================================
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // ==========================================
        // AUDIT LOG
        // ==========================================
        if ($status == Password::RESET_LINK_SENT) {
            $user = User::where('email', $email)->first();

            Log::channel('audit')->info('Reset password diminta (Admin)', [
                'email' => $email,
                'ip' => $ip,
                'user_agent' => $request->userAgent(),
                'user_id' => $user?->id,
                'user_name' => $user?->name,
                'has_admin_access' => $user?->isAdmin() ?? false,
                'timestamp' => now()->toIso8601String(),
            ]);

            RateLimiter::hit($throttleKey, 1800); // 30 menit cooldown

            return back()->with('status', __($status));
        }

        // Jika email tidak terdaftar
        Log::channel('audit')->info('Reset password gagal — email tidak ditemukan (Admin)', [
            'email' => $email,
            'ip' => $ip,
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);

        RateLimiter::hit($throttleKey, 1800);

        return back()->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
