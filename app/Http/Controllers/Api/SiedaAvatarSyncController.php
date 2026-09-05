<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Endpoint yang dipanggil aplikasi SIEDA (server-to-server, shared secret)
 * untuk menyimpan foto profil user yang diunggah lewat Edit Profil SIEDA
 * ke akun Admin Panel PKK (pkk_toba_local.users.avatar).
 *
 * Dengan begitu foto konsisten di kedua aplikasi: SIEDA mengunggah foto,
 * Admin Panel (termasuk SIDONGAN, yang membaca kolom users.avatar yang sama)
 * langsung ikut menampilkannya — tanpa admin harus mengunggah ulang.
 *
 * File disimpan ke storage Admin Panel sendiri (disk public, path avatars/…)
 * dengan pola nama yang sama seperti unggahan langsung di Admin Panel
 * (avatar_{user_id}_{timestamp}.ext) dan kolom users.avatar diperbarui.
 */
class SiedaAvatarSyncController extends Controller
{
    /** Ukuran maksimal foto (bytes) — sama dengan aturan upload 2MB. */
    private const MAX_BYTES = 2 * 1024 * 1024;

    /**
     * Terima foto profil dari SIEDA dan simpan ke akun Admin Panel.
     *
     * Request body (JSON):
     *   - email          (string, required) — email login akun (sama di SIEDA & pkk)
     *   - avatar_base64  (string, required) — isi file foto (base64)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'avatar_base64' => ['required', 'string'],
        ]);

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [mb_strtolower(trim($validated['email']))])
            ->first();

        if (!$user) {
            Log::warning('[SiedaSync] Avatar sync — user tidak ditemukan di Admin Panel', [
                'email' => $validated['email'],
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan di Admin Panel',
            ], 404);
        }

        // Bersihkan prefix data URI (data:image/jpeg;base64, dst.) bila ada
        $base64 = preg_replace('/^data:[^;]*;base64,/', '', $validated['avatar_base64']);
        $base64 = str_replace(' ', '+', $base64);

        $bytes = base64_decode($base64, true);

        if ($bytes === false || $bytes === '') {
            return response()->json([
                'success' => false,
                'message' => 'avatar_base64 tidak valid',
            ], 422);
        }

        if (strlen($bytes) > self::MAX_BYTES) {
            return response()->json([
                'success' => false,
                'message' => 'Ukuran foto melebihi 2MB',
            ], 422);
        }

        // Validasi benar-benar gambar + tentukan ekstensi dari isi file,
        // bukan dari input user (anti path traversal / eksekusi file).
        $ext = $this->deteksiEkstensi($bytes);

        if (!$ext) {
            return response()->json([
                'success' => false,
                'message' => 'File bukan gambar yang didukung (JPG/PNG/WEBP/GIF)',
            ], 422);
        }

        // Hapus foto lama di storage Admin Panel sebelum menulis yang baru
        if (!empty($user->avatar) && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $filename = 'avatar_' . $user->id . '_' . time() . '.' . $ext;
        $path = 'avatars/' . $filename;

        Storage::disk('public')->put($path, $bytes);

        $user->forceFill(['avatar' => $path])->save();

        Log::info('[SiedaSync] Avatar user berhasil disinkronkan dari SIEDA', [
            'email' => $user->email,
            'avatar' => $path,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Avatar berhasil disinkronkan',
            'data' => [
                'email' => $user->email,
                'avatar' => $path,
            ],
        ]);
    }

    /**
     * Deteksi ekstensi file dari isinya (melalui finfo). Kembalikan null
     * bila bukan gambar yang didukung.
     */
    private function deteksiEkstensi(string $bytes): ?string
    {
        // Cegah finfo gagal saat ekstensi fileinfo tidak terpasang
        if (!function_exists('finfo_open')) {
            return null;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($bytes);

        return match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => null,
        };
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
