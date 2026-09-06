<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Support\ImageUploadSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Hitung persentase kelengkapan profil
        $completionItems = [
            'name' => !empty($user->name),
            'email' => !empty($user->email),
            'avatar' => !empty($user->avatar),
            'phone_number' => !empty($user->phone_number),
            'personal_email' => !empty($user->personal_email) && !is_null($user->personal_email_verified_at),
        ];
        $completedCount = count(array_filter($completionItems));
        $completionPercentage = round(($completedCount / count($completionItems)) * 100);

        // Izin efektif user
        $effectivePermissions = $user->effectivePermissionNames();

        return view('admin.profile.edit', [
            'user' => $user,
            'completionPercentage' => $completionPercentage,
            'completionItems' => $completionItems,
            'effectivePermissions' => $effectivePermissions,
            'linkedApplications' => $user->applications,
            'sidonganRoleName' => $user->sidongan_role_name,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // HANDLE AVATAR DARI BASE64 (Prioritas)
        if ($request->filled('cropped_avatar_base64')) {
            $path = ImageUploadSanitizer::storeBase64(
                $request->input('cropped_avatar_base64'),
                'avatars',
                'avatar_' . $user->id . '_'
            );

            if ($path === false) {
                return back()
                    ->withErrors(['cropped_avatar_base64' => 'Foto hasil crop bukan gambar yang valid.'])
                    ->withInput();
            }

            // Hapus avatar lama hanya setelah foto baru lolos validasi.
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $validated['avatar'] = $path;
        }
        // Fallback: handle file upload biasa
        elseif ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $path = ImageUploadSanitizer::store(
                $request->file('avatar'),
                'avatars',
                'avatar_' . $user->id . '_'
            );

            if ($path === false) {
                return back()
                    ->withErrors(['avatar' => 'File avatar bukan gambar yang didukung (JPG/PNG/WEBP/GIF).'])
                    ->withInput();
            }

            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $validated['avatar'] = $path;
        }

        // Update user
        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('admin.profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update the user's password (form lives in the "Keamanan" tab of Edit Profil).
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        // Kolom 'password' di-cast 'hashed' (lihat app/Models/User.php) —
        // memakai Hash::make di sini akan meng-hash dua kali dan merusak login.
        $request->user()->forceFill(['password' => $validated['password']])->save();

        return Redirect::route('admin.profile.edit')
            ->with('success', 'Password berhasil diubah!')
            ->with('tab', 'keamanan');
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
