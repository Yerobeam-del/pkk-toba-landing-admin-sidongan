<?php

namespace App\Http\Controllers\Sidongan;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\PersonalEmailVerificationNotification;
use App\Support\ProfileFields;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
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
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
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

            // Ekstensi dari isi file (bukan nama klien); SVG ditolak (XSS).
            $path = \App\Support\ImageUploadSanitizer::store($file, 'avatars', 'avatar_' . $user->id . '_');
            if ($path === false) {
                return back()->withErrors(['avatar' => 'File avatar bukan gambar yang didukung (JPG/PNG/WEBP/GIF).'])->withInput();
            }
            $validated['avatar'] = $path;
        }

        $user->fill($validated);

        // Reset verification if login email changed
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Personal email berubah/baru → status verifikasi direset dan link
        // verifikasi baru dikirim (paritas dengan alur personal email Admin Panel).
        $personalEmailChanged = $user->isDirty('personal_email');
        if ($personalEmailChanged) {
            $user->personal_email_verified_at = null;
        }

        $user->save();

        $verificationSent = false;
        if ($personalEmailChanged && ProfileFields::isFilled($user->personal_email)) {
            $verificationSent = $this->sendPersonalEmailVerification($user);
        }

        // Jika profil (field pemblokir) kini lengkap, bersihkan status skip
        // — baik flag session maupun preferensi tersimpan di DB — supaya lain
        // kali login tidak langsung masuk tanpa diminta melengkapi data.
        $freshUser = $user->fresh();
        if ($freshUser && ProfileFields::blockingComplete($freshUser)) {
            session()->forget('onboarding_skipped');
            if ($freshUser->onboarding_skipped_at) {
                $freshUser->forceFill(['onboarding_skipped_at' => null])->save();
            }
        }

        $message = 'Profil berhasil diperbarui!';
        if ($verificationSent) {
            $message .= ' Link verifikasi email pribadi telah dikirim ke <strong>' . e($user->personal_email) . '</strong> — silakan cek inbox Anda.';
        }

        return Redirect::route('sidongan.profile.edit')->with('success', $message);
    }

    /**
     * Kirim notifikasi verifikasi email pribadi (signed link 24 jam) dengan
     * route milik SIDONGAN, supaya link menunjuk ke host SIDONGAN dan user
     * bisa langsung klik tanpa dialihkan ke aplikasi lain.
     */
    private function sendPersonalEmailVerification(User $user): bool
    {
        try {
            $user->notify(new PersonalEmailVerificationNotification(
                $user->personal_email,
                'sidongan.personal-email.verify'
            ));

            Log::channel('audit')->info('Link verifikasi email pribadi dikirim (SIDONGAN)', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'personal_email' => $user->personal_email,
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::channel('audit')->warning('Gagal kirim link verifikasi email pribadi (SIDONGAN)', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Verifikasi email pribadi via signed link dari email.
     *
     * Tidak perlu login: link signed (24 jam) itu sendiri adalah bukti
     * kepemilikan email. Email di link harus sama dengan email yang tersimpan
     * di database — kalau user sudah mengganti email, link lama ditolak.
     */
    public function verifyPersonalEmail(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $email = trim((string) $request->query('email', ''));

        if (
            !ProfileFields::isFilled($email)
            || !ProfileFields::isFilled($user->personal_email)
            || strcasecmp($user->personal_email, $email) !== 0
        ) {
            return redirect()->route('sidongan.login')
                ->with('error', 'Link verifikasi tidak valid atau email pribadi Anda sudah diubah. Silakan daftarkan ulang lewat menu Edit Profil.');
        }

        // Idempoten: aman walau link diklik berulang kali.
        $user->markPersonalEmailAsVerified();

        // Paritas dengan alur verifikasi Admin Panel (Auth\PersonalEmailController
        // @verify): bersihkan email pending dari session setelah verifikasi sukses.
        session()->forget('pending_personal_email');

        Log::channel('audit')->info('Email pribadi berhasil diverifikasi (SIDONGAN)', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'personal_email' => $user->personal_email,
        ]);

        if (Auth::guard('sidongan')->check() && Auth::guard('sidongan')->id() === $user->id) {
            return redirect()->route('sidongan.dashboard')
                ->with('success', '🎉 Email pribadi <strong>' . e($user->personal_email) . '</strong> berhasil diverifikasi! Sekarang fitur Lupa Password sudah aktif.');
        }

        return redirect()->route('sidongan.login')
            ->with('success', 'Email pribadi berhasil diverifikasi. Silakan login untuk melanjutkan.');
    }

    /**
     * Kirim ulang link verifikasi email pribadi (dari halaman Edit Profil).
     * Rate limit: 3x per 30 menit per user.
     */
    public function resendPersonalEmailVerification(Request $request): RedirectResponse
    {
        $user = auth()->guard('sidongan')->user();

        if ($user->hasVerifiedPersonalEmail()) {
            return Redirect::route('sidongan.profile.edit')
                ->with('info', 'Email pribadi Anda sudah terverifikasi.');
        }

        if (!ProfileFields::isFilled($user->personal_email)) {
            return Redirect::route('sidongan.profile.edit')
                ->with('warning', 'Simpan dulu email pribadi Anda sebelum meminta link verifikasi.');
        }

        $throttleKey = 'sidongan-resend-personal-email:' . $user->id . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            Log::channel('audit')->warning('Rate limit — kirim ulang verifikasi email pribadi (SIDONGAN)', [
                'user_id' => $user->id,
                'cooldown' => ceil($seconds / 60) . ' menit',
            ]);

            return Redirect::route('sidongan.profile.edit')
                ->with('error', 'Terlalu banyak permintaan. Silakan coba lagi dalam ' . ceil($seconds / 60) . ' menit.');
        }

        if ($this->sendPersonalEmailVerification($user)) {
            RateLimiter::hit($throttleKey, 1800); // 30 menit cooldown

            return Redirect::route('sidongan.profile.edit')
                ->with('success', 'Link verifikasi telah dikirim ulang ke <strong>' . e($user->personal_email) . '</strong>.');
        }

        return Redirect::route('sidongan.profile.edit')
            ->with('error', 'Link verifikasi gagal dikirim. Silakan coba lagi nanti.');
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
        // Guard 'sidongan' — bukan 'web' — karena user login via guard
        // sidongan (guard web di-logout saat login SIDONGAN). Rule
        // current_password:web dulu selalu gagal walau password benar.
        $validated = $request->validate([
            'current_password' => ['required', 'current_password:sidongan'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = auth()->guard('sidongan')->user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return Redirect::route('sidongan.profile.password')->with('success', 'Password berhasil diubah!');
    }
}
