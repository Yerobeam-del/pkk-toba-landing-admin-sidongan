<?php

/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Http\Controllers;

use App\Http\Middleware\SidonganEnsureProfileComplete;
use App\Notifications\PersonalEmailVerificationNotification;
use App\Support\ProfileFields;
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
     * Route login yang sesuai dengan host saat ini.
     *
     * Halaman onboarding dipakai lintas aplikasi (SIDONGAN / Admin Panel).
     * Pengunjung yang belum login harus diarahkan ke halaman login aplikasi
     * yang sedang dibuka — kalau di host SIDONGAN → login SIDONGAN, selain itu
     * → login Admin Panel (route login lama selalu tersedia di host mana pun).
     */
    private function loginRoute(Request $request): string
    {
        $sidonganDomain = (string) config('app.sidongan_domain');

        if ($sidonganDomain !== '' && str_ends_with($request->getHost(), $sidonganDomain)) {
            return 'sidongan.login';
        }

        return 'login';
    }

    /**
     * Display the onboarding page.
     * Smart: detects which system the user came from and adapts branding.
     */
    public function show(Request $request): View|RedirectResponse
    {
        [$user, $systemKey] = $this->getUserAndSystem();

        // Not authenticated → redirect ke login aplikasi yang sesuai host
        if (!$user) {
            return redirect()->route($this->loginRoute($request));
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
            return redirect()->route($this->loginRoute($request));
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

        // Personal email baru didaftarkan lewat onboarding → kirim link verifikasi
        // (paritas dengan alur personal email Admin Panel). Fitur Lupa Password
        // hanya aktif setelah email pribadi diverifikasi.
        $verificationSent = false;
        if (isset($updateData['personal_email']) && ProfileFields::isFilled($updateData['personal_email'])) {
            // Route signed URL mengikuti sistem asal: SIDONGAN memakai route
            // sendiri (host sidongan), Admin Panel memakai route default.
            $verifyRoute = $systemKey === 'sidongan'
                ? 'sidongan.personal-email.verify'
                : 'personal-email.verify';

            try {
                $user->notify(new PersonalEmailVerificationNotification($updateData['personal_email'], $verifyRoute));
                $verificationSent = true;

                Log::channel('audit')->info('Link verifikasi email pribadi dikirim via onboarding', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'system' => $systemKey,
                    'personal_email' => $updateData['personal_email'],
                    'timestamp' => now()->toIso8601String(),
                ]);
            } catch (\Throwable $e) {
                Log::channel('audit')->warning('Gagal kirim link verifikasi email pribadi via onboarding', [
                    'user_id' => $user->id,
                    'system' => $systemKey,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // "Simpan & Lanjutkan" = simpan lalu LANGSUNG lanjut ke aplikasi
        // (dashboard), meskipun masih ada langkah yang belum dilengkapi — selama
        // tidak ada field PEMBLOKIR yang kosong. Field pemblokir didefinisikan
        // sekali di App\Support\ProfileFields (phone_number & personal_email) —
        // identik dengan SIEDA; avatar opsional tidak pernah mengunci user.
        // "Lewati — nanti saja" tetap tersedia untuk user yang ingin melewati
        // onboarding tanpa menyimpan apa pun.
        $freshUser = $user->fresh();
        $blockingRemaining = ProfileFields::missingBlocking($freshUser);

        if (empty($blockingRemaining)) {
            // Tidak ada field pemblokir tersisa — hapus status skip (session & DB)
            // supaya kalau suatu saat field dikosongkan lagi, onboarding muncul lagi.
            session()->forget('onboarding_skipped');
            if ($freshUser->onboarding_skipped_at) {
                $freshUser->forceFill(['onboarding_skipped_at' => null])->save();
            }

            $successMessage = 'Profil berhasil disimpan! Anda bisa melengkapi foto profil nanti melalui menu Edit Profil.';
            if ($verificationSent) {
                $successMessage .= ' Link verifikasi email pribadi telah dikirim ke <strong>' . e($updateData['personal_email']) . '</strong> — silakan cek inbox Anda.';
            }

            // Flash 'success' (bukan 'status') supaya muncul sebagai toast di dashboard SIDONGAN.
            return redirect()->route($system['dashboard_route'])
                ->with('success', $successMessage)
                ->with('status', $successMessage);
        }

        $statusMessage = 'Profil berhasil disimpan. Silakan lengkapi data yang tersisa.';
        if ($verificationSent) {
            $statusMessage .= ' Link verifikasi email pribadi telah dikirim ke <strong>' . e($updateData['personal_email']) . '</strong> — silakan cek inbox Anda.';
        }

        return redirect()->route('onboarding')
            ->with('status', $statusMessage);
    }

    /**
     * Skip onboarding.
     */
    public function skip(Request $request): RedirectResponse
    {
        [$user, $systemKey] = $this->getUserAndSystem();

        if (!$user) {
            return redirect()->route($this->loginRoute($request));
        }

        $system = $this->systems[$systemKey];

        Log::channel('audit')->info('Onboarding di-skip', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'system' => $systemKey,
            'missing_fields' => $this->getMissingFields($user, $systemKey),
            'timestamp' => now()->toIso8601String(),
        ]);

        // Simpan keputusan skip di DB (bertahan lintas login) + session flag.
        // Tanpa ini, user yang melewati onboarding akan dilempar ke onboarding
        // LAGI setiap kali login — tidak bisa "melewati" halaman onboarding.
        if (!$user->onboarding_skipped_at) {
            $user->forceFill(['onboarding_skipped_at' => now()])->save();
        }
        session(['onboarding_skipped' => true]);

        return redirect()->route($system['dashboard_route'])
            ->with('status', 'Anda bisa melengkapi profil nanti melalui menu Edit Profil.');
    }

    /**
     * Get missing fields based on system requirements.
     */
    private function getMissingFields($user, string $systemKey): array
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
        return ProfileFields::missingBlocking($user) !== [];
    }

    /**
     * Static helper: get missing fields (for middleware).
     */
    public static function getMissingFieldsStatic($user): array
    {
        $missing = [];
        if (!ProfileFields::isFilled($user->phone_number ?? null)) $missing[] = 'phone_number';
        if (!ProfileFields::isFilled($user->personal_email ?? null)) $missing[] = 'personal_email';
        if (empty($user->avatar)) $missing[] = 'avatar';
        return $missing;
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
