<?php

/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Http\Controllers\Sidongan;

use App\Http\Controllers\Controller;
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
     * Display the onboarding page with smart field detection.
     * Shows only the fields that are missing.
     */
    public function show(): View
    {
        $user = Auth::guard('sidongan')->user();
        $missingFields = SidonganEnsureProfileComplete::getMissingFields($user);
        $completionPercentage = SidonganEnsureProfileComplete::getCompletionPercentage($user);

        // If profile is already complete, redirect to dashboard
        if (empty($missingFields)) {
            return redirect()->route('sidongan.dashboard');
        }

        return view('sidongan-auth.onboarding', [
            'user' => $user,
            'missingFields' => $missingFields,
            'completionPercentage' => $completionPercentage,
            'hasAvatar' => !empty($user->avatar),
            'hasPhone' => !empty($user->phone_number),
            'hasPersonalEmail' => !empty($user->personal_email),
        ]);
    }

    /**
     * Handle onboarding form submission.
     * Smart: only validates fields that were shown (missing fields).
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::guard('sidongan')->user();
        $missingFields = SidonganEnsureProfileComplete::getMissingFields($user);

        // Build dynamic validation rules based on missing fields
        $rules = [];

        // Avatar is always optional (can upload anytime)
        $rules['avatar'] = ['nullable', 'image', 'max:2048'];

        if (in_array('phone_number', $missingFields)) {
            $rules['phone_number'] = [
                'required',
                'string',
                'min:10',
                'max:15',
                'regex:/^[0-9+\-\s()]+$/',
            ];
        }

        if (in_array('personal_email', $missingFields)) {
            $rules['personal_email'] = [
                'required',
                'email',
                'max:255',
                'unique:users,personal_email,' . $user->id,
            ];
        }

        // Only validate if there are rules (shouldn't happen if redirected correctly)
        if (empty($rules)) {
            return redirect()->route('sidongan.dashboard');
        }

        $validated = $request->validate($rules);

        // Update user profile
        $updateData = [];

        // Handle avatar upload
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $file = $request->file('avatar');

            // Delete old avatar
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

            Log::channel('audit')->info('Profil SIDONGAN dilengkapi via onboarding', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'fields_updated' => array_keys($updateData),
                'timestamp' => now()->toIso8601String(),
            ]);
        }

        // Check if profile is now complete
        $remainingMissing = SidonganEnsureProfileComplete::getMissingFields($user->fresh());

        if (empty($remainingMissing)) {
            return redirect()->route('sidongan.dashboard')
                ->with('status', 'Profil Anda berhasil dilengkapi! Selamat datang di SIDONGAN.');
        }

        // Still have missing fields (user submitted partial)
        return redirect()->route('sidongan.onboarding')
            ->with('status', 'Profil berhasil disimpan. Silakan lengkapi data yang tersisa.');
    }

    /**
     * Skip onboarding (user chooses to complete later).
     */
    public function skip(): RedirectResponse
    {
        $user = Auth::guard('sidongan')->user();

        Log::channel('audit')->info('Onboarding SIDONGAN di-skip', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'missing_fields' => SidonganEnsureProfileComplete::getMissingFields($user),
            'timestamp' => now()->toIso8601String(),
        ]);

        return redirect()->route('sidongan.dashboard')
            ->with('status', 'Anda bisa melengkapi profil nanti melalui menu Edit Profil.');
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
