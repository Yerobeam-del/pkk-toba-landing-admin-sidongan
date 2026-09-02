<?php

/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Http\Controllers;

use App\Http\Middleware\SidonganEnsureProfileComplete;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    /**
     * Available systems that can trigger onboarding.
     * Each system has its own guard, branding, and redirect URLs.
     */
    private array $systems = [
        'sidongan' => [
            'guard' => 'sidongan',
            'name' => 'SIDONGAN',
            'full_name' => 'Sistem Informasi Dokumen Organisasi Agenda dan Naskah',
            'org' => 'PKK Kabupaten Toba',
            'logo' => 'assets/sidongan/images/Logo-SIDONGAN-white.svg',
            'color_start' => '#0d9486',
            'color_mid' => '#14b8a6',
            'color_end' => '#0ea5e9',
            'dashboard_route' => 'sidongan.dashboard',
            'login_route' => 'sidongan.login',
            'profile_edit_route' => 'sidongan.profile.edit',
        ],
        'admin' => [
            'guard' => 'web',
            'name' => 'Admin Panel',
            'full_name' => 'Panel Administrasi PKK Kabupaten Toba',
            'org' => 'PKK Kabupaten Toba',
            'logo' => 'assets/admin/images/Logo-PKK-Toba-White.png',
            'color_start' => '#6d28d9',
            'color_mid' => '#7c3aed',
            'color_end' => '#a855f7',
            'dashboard_route' => 'admin.dashboard',
            'login_route' => 'login',
            'profile_edit_route' => null,
        ],
    ];

    /**
     * Detect which system the user logged in from.
     * Returns system key or null if not authenticated.
     */
    private function detectSystem(): ?string
    {
        // Check SIDONGAN guard first
        if (Auth::guard('sidongan')->check()) {
            return 'sidongan';
        }

        // Check web (admin) guard
        if (Auth::guard('web')->check()) {
            return 'admin';
        }

        return null;
    }

    /**
     * Get the current authenticated user and their system.
     */
    private function getUserAndSystem(): array
    {
        $systemKey = $this->detectSystem();

        if (!$systemKey || !isset($this->systems[$systemKey])) {
            return [null, null];
        }

        $system = $this->systems[$systemKey];
        $user = Auth::guard($system['guard'])->user();

        return [$user, $systemKey];
    }

    /**
     * Display the onboarding page.
     * Smart: detects which system the user came from and adapts branding.
     */
    public function show(): View|RedirectResponse
    {
        [$user, $systemKey] = $this->getUserAndSystem();

        // Not authenticated → redirect to login
        if (!$user) {
            return redirect()->route('sidongan.login');
        }

        $system = $this->systems[$systemKey];

        // Check if profile is already complete
        $missingFields = $this->getMissingFields($user, $systemKey);
        if (empty($missingFields)) {
            return redirect()->route($system['dashboard_route']);
        }

        $completionPercentage = $this->getCompletionPercentage($user, $systemKey);

        return view('onboarding.index', [
            'user' => $user,
            'system' => $system,
            'systemKey' => $systemKey,
            'missingFields' => $missingFields,
            'completionPercentage' => $completionPercentage,
        ]);
    }

    /**
     * Handle onboarding form submission.
     */
    public function store(Request $request): RedirectResponse
    {
        [$user, $systemKey] = $this->getUserAndSystem();

        if (!$user) {
            return redirect()->route('sidongan.login');
        }

        $system = $this->systems[$systemKey];
        $missingFields = $this->getMissingFields($user, $systemKey);

        // Build dynamic validation rules
        $rules = [];

        // Avatar is always optional
        $rules['avatar'] = ['nullable', 'image', 'max:2048'];

        if (in_array('phone_number', $missingFields)) {
            $rules['phone_number'] = [
                'required', 'string', 'min:10', 'max:15',
                'regex:/^[0-9+\-\s()]+$/',
            ];
        }

        if (in_array('personal_email', $missingFields)) {
            $rules['personal_email'] = [
                'required', 'email', 'max:255',
                'unique:users,personal_email,' . $user->id,
            ];
        }

        if (empty($rules)) {
            return redirect()->route($system['dashboard_route']);
        }

        $validated = $request->validate($rules);

        $updateData = [];

        // Handle avatar upload
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $file = $request->file('avatar');
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $imageName = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $imageName, 'public');
            $updateData['avatar'] = $path;
        }

        if (isset($validated['phone_number'])) {
            $updateData['phone_number'] = $validated['phone_number'];
        }

        if (isset($validated['personal_email'])) {
            $updateData['personal_email'] = $validated['personal_email'];
        }

        if (!empty($updateData)) {
            $user->update($updateData);

            Log::channel('audit')->info('Profil dilengkapi via onboarding', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'system' => $systemKey,
                'fields_updated' => array_keys($updateData),
                'timestamp' => now()->toIso8601String(),
            ]);
        }

        // Check remaining
        $remaining = $this->getMissingFields($user->fresh(), $systemKey);

        if (empty($remaining)) {
            return redirect()->route($system['dashboard_route'])
                ->with('status', 'Profil Anda berhasil dilengkapi! Selamat datang.');
        }

        return redirect()->route('onboarding')
            ->with('status', 'Profil berhasil disimpan. Silakan lengkapi data yang tersisa.');
    }

    /**
     * Skip onboarding.
     */
    public function skip(): RedirectResponse
    {
        [$user, $systemKey] = $this->getUserAndSystem();

        if (!$user) {
            return redirect()->route('sidongan.login');
        }

        $system = $this->systems[$systemKey];

        Log::channel('audit')->info('Onboarding di-skip', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'system' => $systemKey,
            'missing_fields' => $this->getMissingFields($user, $systemKey),
            'timestamp' => now()->toIso8601String(),
        ]);

        return redirect()->route($system['dashboard_route'])
            ->with('status', 'Anda bisa melengkapi profil nanti melalui menu Edit Profil.');
    }

    /**
     * Get missing fields based on system requirements.
     */
    private function getMissingFields($user, string $systemKey): array
    {
        $missing = [];

        if (empty($user->phone_number)) {
            $missing[] = 'phone_number';
        }

        if (empty($user->personal_email)) {
            $missing[] = 'personal_email';
        }

        if (empty($user->avatar)) {
            $missing[] = 'avatar';
        }

        return $missing;
    }

    /**
     * Get completion percentage.
     */
    private function getCompletionPercentage($user, string $systemKey): int
    {
        $total = 3;
        $completed = 0;

        if (!empty($user->phone_number)) $completed++;
        if (!empty($user->personal_email)) $completed++;
        if (!empty($user->avatar)) $completed++;

        return (int) round(($completed / $total) * 100);
    }

    /**
     * Static helper: check if profile is blocking (for middleware).
     */
    public static function isProfileBlocking($user): bool
    {
        return empty($user->phone_number) || empty($user->personal_email);
    }

    /**
     * Static helper: get missing fields (for middleware).
     */
    public static function getMissingFieldsStatic($user): array
    {
        $missing = [];
        if (empty($user->phone_number)) $missing[] = 'phone_number';
        if (empty($user->personal_email)) $missing[] = 'personal_email';
        if (empty($user->avatar)) $missing[] = 'avatar';
        return $missing;
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
