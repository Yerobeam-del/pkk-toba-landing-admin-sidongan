<?php

namespace App\Http\Controllers\Sidongan;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the SIDONGAN user's profile form.
     */
    public function edit(): View
    {
        $user = auth()->guard('sidongan')->user();

        $completionItems = [
            'name' => !empty($user->name),
            'email' => !empty($user->email),
            'avatar' => !empty($user->avatar),
            'phone_number' => !empty($user->phone_number),
            'personal_email' => !empty($user->personal_email) && !is_null($user->personal_email_verified_at),
        ];
        $completedCount = count(array_filter($completionItems));
        $completionPercentage = round(($completedCount / count($completionItems)) * 100);

        return view('sidongan.profile.edit', [
            'user' => $user,
            'completionPercentage' => $completionPercentage,
            'completionItems' => $completionItems,
        ]);
    }

    /**
     * Update the SIDONGAN user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = auth()->guard('sidongan')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'cropped_avatar_base64' => ['nullable', 'string'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'personal_email' => ['nullable', 'email', 'max:255', 'unique:users,personal_email,' . $user->id],
        ]);

        // Handle avatar from base64 (cropper)
        if ($request->filled('cropped_avatar_base64')) {
            $base64 = $request->input('cropped_avatar_base64');
            $image = str_replace('data:image/jpeg;base64,', '', $base64);
            $image = str_replace(' ', '+', $image);
            $imageName = 'avatar_' . $user->id . '_' . time() . '.jpg';

            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = 'avatars/' . $imageName;
            Storage::disk('public')->put($path, base64_decode($image));

            $validated['avatar'] = $path;
        }
        // Fallback: handle regular file upload
        elseif ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $file = $request->file('avatar');

            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $validated['avatar'] = $file->storeAs('avatars', $filename, 'public');
        }

        $user->fill($validated);

        // Reset verification if login email changed
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Reset verification if personal email changed
        if ($user->isDirty('personal_email')) {
            $user->personal_email_verified_at = null;
        }

        $user->save();

        // If profile was previously skipped, check if it's now complete
        if (session('onboarding_skipped')) {
            $hasPhone = !empty($user->phone_number);
            $hasEmail = !empty($user->personal_email);
            if ($hasPhone && $hasEmail) {
                session()->forget('onboarding_skipped');
            }
        }

        return Redirect::route('sidongan.profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Display the change password form.
     */
    public function password(): RedirectResponse
    {
        return redirect()->route('sidongan.profile.edit')->with('tab', 'keamanan');
    }

    /**
     * Update the SIDONGAN user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password:web'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = auth()->guard('sidongan')->user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return Redirect::route('sidongan.profile.password')->with('success', 'Password berhasil diubah!');
    }
}
