<?php

/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SidonganEnsureProfileComplete
{
    /**
     * Routes that should NOT be redirected to onboarding.
     * User should be able to access profile and logout even if profile is incomplete.
     */
    private array $exemptRoutes = [
        'sidongan.profile.edit',
        'sidongan.profile.update',
        'sidongan.profile.password',
        'sidongan.profile.password.update',
        'sidongan.logout',
        'sidongan.onboarding',
        'sidongan.onboarding.store',
    ];

    /**
     * Pages that should NOT be redirected to onboarding
     * (to avoid redirect loops).
     */
    private array $exemptPaths = [
        '/sidongan/profile',
        '/sidongan/logout',
        '/sidongan/onboarding',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('sidongan')->check()) {
            return redirect()->route('sidongan.login');
        }

        $user = Auth::guard('sidongan')->user();

        // Skip if profile is already complete
        if ($this->isProfileComplete($user)) {
            return $next($request);
        }

        // Skip if accessing exempt routes/paths
        $routeName = $request->route()?->getName();
        $path = $request->path();

        if (in_array($routeName, $this->exemptRoutes)) {
            return $next($request);
        }

        foreach ($this->exemptPaths as $exemptPath) {
            if (str_starts_with($path, ltrim($exemptPath, '/'))) {
                return $next($request);
            }
        }

        // Profile incomplete → redirect to onboarding
        return redirect()->route('sidongan.onboarding');
    }

    /**
     * Check if user profile is complete enough for SIDONGAN.
     *
     * Required fields:
     * - phone_number: needed for contact/whatsapp notifications
     * - personal_email: needed for reset password functionality
     *
     * Optional (not blocking):
     * - avatar: cosmetic
     * - name: always set from admin
     */
    private function isProfileComplete($user): bool
    {
        // Check phone number
        $hasPhone = !empty($user->phone_number);

        // Check personal email (not required to be verified, just set)
        $hasPersonalEmail = !empty($user->personal_email);

        // Profile is complete if BOTH phone and personal email are set
        return $hasPhone && $hasPersonalEmail;
    }

    /**
     * Get list of missing profile fields for smart display.
     */
    public static function getMissingFields($user): array
    {
        $missing = [];

        if (empty($user->phone_number)) {
            $missing[] = 'phone_number';
        }

        if (empty($user->personal_email)) {
            $missing[] = 'personal_email';
        }

        return $missing;
    }

    /**
     * Get profile completion percentage.
     */
    public static function getCompletionPercentage($user): int
    {
        $total = 2; // phone_number, personal_email
        $completed = 0;

        if (!empty($user->phone_number)) $completed++;
        if (!empty($user->personal_email)) $completed++;

        return (int) round(($completed / $total) * 100);
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
