<?php

/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Http\Middleware;

use App\Support\ProfileFields;
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

        // Skip if user previously chose to skip onboarding — baik lewat session
        // (sesi aktif) maupun preferensi tersimpan di DB (users.onboarding_skipped_at,
        // bertahan lintas login) supaya user tidak terjebak loop onboarding.
        if (session('onboarding_skipped', false) || $user->onboarding_skipped_at) {
            return $next($request);
        }

        // Skip if accessing exempt routes/paths
        $routeName = $request->route()?->getName();
        $path = $request->path();

        if (in_array($routeName, $this->exemptRoutes)) {
            return $next($request);
        }

        foreach ($this->exemptPaths as $exemptPath) {
            $exemptPath = ltrim($exemptPath, '/');
            if ($path === $exemptPath || str_starts_with($path, $exemptPath . '/')) {
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
        // Satu sumber kebenaran: field pemblokir phone_number & personal_email
        // (App\Support\ProfileFields) — identik dengan SIEDA.
        return ProfileFields::blockingComplete($user);
    }

    /**
     * Get list of missing profile fields for smart display.
     * Includes avatar as an optional field.
     */
    public static function getMissingFields($user): array
    {
        $missing = [];

        // Field pemblokir & placeholder memakai aturan terpusat di
        // App\Support\ProfileFields (case-insensitive) — identik dengan SIEDA.
        if (!ProfileFields::isFilled($user->phone_number ?? null)) {
            $missing[] = 'phone_number';
        }

        if (!ProfileFields::isFilled($user->personal_email ?? null)) {
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
        return ProfileFields::missingBlocking($user) !== [];
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
