<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticatedSidongan
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        // Cek apakah sudah login dengan guard sidongan
        if (Auth::guard('sidongan')->check()) {
            $user = Auth::guard('sidongan')->user();

            // Email pribadi pending (terisi tapi belum terverifikasi) →
            // onboarding dengan panel "Cek Email" — satu pintu verifikasi,
            // paritas dengan alur pasca-login AuthController@login. Tanpa ini
            // user pending yang membuka /sidongan-login bisa lolos langsung ke
            // dashboard dan melewati verifikasi.
            $pendingEmail = session('pending_personal_email') ?? $user->personal_email;

            if ($pendingEmail && !$user->hasVerifiedPersonalEmail()) {
                session(['pending_personal_email' => $pendingEmail]);

                return redirect()->route('onboarding');
            }

            return redirect()->route('sidongan.dashboard');
        }

        return $next($request);
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
