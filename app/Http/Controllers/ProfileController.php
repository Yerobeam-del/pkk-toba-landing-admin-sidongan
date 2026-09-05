<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
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
            $base64 = $request->input('cropped_avatar_base64');

            // Hapus prefix "data:image/jpeg;base64," (juga aman untuk PNG/WebP:
            // regex mencocokkan tipe apa pun yang dikirim Cropper)
            $image = preg_replace('#^data:image/[\w.+-]+;base64,#', '', $base64);
            $image = str_replace(' ', '+', $image);

            $decoded = base64_decode($image, true);
            if ($decoded !== false && strlen($decoded) > 0) {
                $imageName = 'avatar_' . $user->id . '_' . time() . '.jpg';

                // Hapus avatar lama (sebelumnya file lama menumpuk di storage)
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $path = 'avatars/' . $imageName;
                Storage::disk('public')->put($path, $decoded);

                $validated['avatar'] = $path;
            }
        }
        
        // Fallback: handle file upload biasa
        elseif ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $file = $request->file('avatar');

            // Hapus avatar lama (sebelumnya file lama menumpuk di storage)
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Ekstensi ditentukan dari ISI file (magic bytes), bukan nama kiriman
            // klien — dan SVG ditolak (XSS via /storage). Lihat ImageUploadSanitizer.
            $path = \App\Support\ImageUploadSanitizer::store($file, 'avatars', 'avatar_' . $user->id . '_');
            if ($path === false) {
                return back()->withErrors(['avatar' => 'File avatar bukan gambar yang didukung (JPG/PNG/WEBP/GIF).'])->withInput();
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
