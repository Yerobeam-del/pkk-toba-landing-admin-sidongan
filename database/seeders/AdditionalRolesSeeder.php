<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

/**
 * Role tambahan untuk Admin Panel.
 *
 * Satu akun hanya punya SATU role (users.role_id), sehingga izin tidak bisa
 * digabung antar-role. Karena itu role di sini berupa paket izin yang utuh:
 * enam role per bidang, dan empat role gabungan untuk pengurus yang memegang
 * lebih dari satu bidang.
 *
 * Aman dijalankan berulang: role dicari berdasarkan `name`, dan izinnya
 * di-sync (bukan attach) sehingga tidak pernah menghasilkan data ganda.
 * Menjalankan ulang juga mengembalikan izin ke keadaan yang didefinisikan di sini.
 *
 * Jalankan dengan:
 *   php artisan db:seed --class=AdditionalRolesSeeder
 *
 * Role bawaan `administrator` dan `anggota` sengaja TIDAK disentuh.
 */
class AdditionalRolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            // ---------- Per bidang ----------
            [
                'name' => 'pengelola-berita',
                'display_name' => 'Pengelola Berita',
                'description' => 'Hanya mengelola berita',
                'permissions' => ['manage-berita'],
            ],
            [
                'name' => 'pengelola-aplikasi',
                'display_name' => 'Pengelola Aplikasi',
                'description' => 'Hanya mengelola aplikasi & layanan',
                'permissions' => ['manage-aplikasi'],
            ],
            [
                'name' => 'pengelola-struktur',
                'display_name' => 'Pengelola Struktur',
                'description' => 'Hanya mengelola struktur organisasi',
                'permissions' => ['manage-struktur'],
            ],
            [
                'name' => 'pengelola-dokumen',
                'display_name' => 'Pengelola SK & Dokumen',
                'description' => 'Hanya mengelola SK & dokumen',
                'permissions' => ['manage-dokumen'],
            ],
            [
                'name' => 'pengelola-template',
                'display_name' => 'Pengelola Template',
                'description' => 'Hanya mengelola template dokumen',
                'permissions' => ['manage-template'],
            ],
            // Catatan: role "Pengelola Desa" sengaja tidak dibuat karena modul
            // Desa sedang dinonaktifkan (menu sidebar dikomentari). Lihat juga
            // HIDDEN_PERMISSION_GROUPS di UserManagementController.

            // ---------- Gabungan ----------
            [
                'name' => 'pengelola-berita-aplikasi',
                'display_name' => 'Pengelola Berita & Aplikasi',
                'description' => 'Mengelola berita serta aplikasi & layanan',
                'permissions' => ['manage-berita', 'manage-aplikasi'],
            ],
            [
                'name' => 'pengelola-konten',
                'display_name' => 'Pengelola Konten',
                'description' => 'Mengelola berita, tampilan beranda, dan tentang kami',
                'permissions' => ['manage-berita', 'manage-hero-slider', 'manage-tentang'],
            ],
            [
                'name' => 'pengelola-dokumen-template',
                'display_name' => 'Pengelola Dokumen & Template',
                'description' => 'Mengelola SK & dokumen serta template',
                'permissions' => ['manage-dokumen', 'manage-template'],
            ],
            [
                'name' => 'pengelola-organisasi',
                'display_name' => 'Pengelola Organisasi',
                'description' => 'Mengelola struktur organisasi serta aplikasi & layanan',
                'permissions' => ['manage-struktur', 'manage-aplikasi'],
            ],
        ];

        foreach ($roles as $data) {
            $permissionIds = Permission::whereIn('name', $data['permissions'])->pluck('id');

            // Berhenti dengan pesan jelas kalau ada nama izin yang salah ketik,
            // daripada diam-diam membuat role tanpa izin.
            if ($permissionIds->count() !== count($data['permissions'])) {
                $ditemukan = Permission::whereIn('name', $data['permissions'])->pluck('name')->all();
                $hilang = array_diff($data['permissions'], $ditemukan);
                throw new \RuntimeException(
                    "Permission tidak ditemukan untuk role {$data['name']}: " . implode(', ', $hilang)
                    . '. Jalankan RolePermissionSeeder terlebih dahulu.'
                );
            }

            $role = Role::firstOrCreate(
                ['name' => $data['name']],
                [
                    'display_name' => $data['display_name'],
                    'description' => $data['description'],
                ]
            );

            // Pastikan label ikut ter-update kalau seeder direvisi
            $role->update([
                'display_name' => $data['display_name'],
                'description' => $data['description'],
            ]);

            $role->permissions()->sync($permissionIds);

            $this->command->info("  {$data['display_name']} -> " . implode(', ', $data['permissions']));
        }

        // Bersihkan role yang sudah tidak dipakai lagi (mis. dibuat oleh versi
        // seeder sebelumnya). Hanya dihapus kalau belum ada akun yang memakainya,
        // supaya tidak ada akun yang tiba-tiba kehilangan role.
        $usang = ['pengelola-desa'];
        foreach ($usang as $nama) {
            $role = Role::where('name', $nama)->withCount('users')->first();

            if (!$role) {
                continue;
            }

            if ($role->users_count > 0) {
                $this->command->warn(
                    "  Role '{$nama}' sudah usang tapi masih dipakai {$role->users_count} akun. "
                    . 'Pindahkan akun tersebut dulu, lalu jalankan seeder ini lagi.'
                );
                continue;
            }

            $role->permissions()->detach();
            $role->delete();
            $this->command->info("  Role usang dihapus: {$nama}");
        }

        $this->command->info('Selesai. Role administrator & anggota tidak diubah.');
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
