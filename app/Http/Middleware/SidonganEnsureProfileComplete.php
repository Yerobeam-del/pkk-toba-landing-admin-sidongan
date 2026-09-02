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
        'sidongan.onboarding.skip',
        'onboarding',
        'onboarding.store',
        'onboarding.skip',
    ];

    /**
     * Pages that should NOT be redirected to onboarding
     * (to avoid redirect loops).
     */
    private array $exemptPaths = [
        '/sidongan/profile',
        '/sidongan/logout',
        '/sidongan/onboarding',
        '/onboarding',
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

        // Skip if user previously chose to skip onboarding
        if (session('onboarding_skipped', false)) {
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

        // Profile incomplete → redirect to standalone onboarding
        return redirect()->route('onboarding');
    }

    /**
     * Check if user profile is complete enough for SIDONGAN.
     *
     * Required fields:
     * - phone_number: needed for contact/whatsapp notifications
     * - personal_email: needed for reset password functionality
     *
     * Optional (not blocking):
     * - avatar: cosmetic, can be set later
     * - name: always set from admin
     */
    private function isProfileComplete($user): bool
    {
        $phone = trim($user->phone_number ?? '');
        $hasPhone = !empty($phone) && !in_array($phone, ['-', '--', '0', 'n/a', '- -', 'Belum diisi', 'Tidak ada']);

        $email = trim($user->personal_email ?? '');
        $hasPersonalEmail = !empty($email) && !in_array($email, ['-', '--', 'n/a', 'Belum diisi', 'Tidak ada']);

        return $hasPhone && $hasPersonalEmail;
    }

    /**
     * Get list of missing profile fields for smart display.
     * Includes avatar as an optional field.
     */
    public static function getMissingFields($user): array
    {
        $missing = [];

        $phone = trim($user->phone_number ?? '');
        if (empty($phone) || in_array($phone, ['-', '--', '0', 'n/a', '- -', 'Belum diisi', 'Tidak ada'])) {
            $missing[] = 'phone_number';
        }

        $email = trim($user->personal_email ?? '');
        if (empty($email) || in_array($email, ['-', '--', 'n/a', 'Belum diisi', 'Tidak ada'])) {
            $missing[] = 'personal_email';
        }

        // Avatar is optional but tracked for onboarding display
        if (empty($user->avatar)) {
            $missing[] = 'avatar';
        }

        return $missing;
    }

    /**
     * Get profile completion percentage.
     * Total fields: 3 (phone, email, avatar)
     */
    public static function getCompletionPercentage($user): int
    {
        $total = 3;
        $completed = 0;

        if (!empty($user->phone_number)) $completed++;
        if (!empty($user->personal_email)) $completed++;
        if (!empty($user->avatar)) $completed++;

        return (int) round(($completed / $total) * 100);
    }

    /**
     * Check if profile is blocking (phone + email missing).
     * Avatar alone doesn't block.
     */
    public static function isProfileBlocking($user): bool
    {
        return empty($user->phone_number) || empty($user->personal_email);
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
