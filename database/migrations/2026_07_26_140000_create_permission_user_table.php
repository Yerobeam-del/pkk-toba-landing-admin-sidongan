<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Izin tingkat akun.
 *
 * Sebelumnya form Tambah/Edit Akun menyimpan centang "Permission Akses" ke
 * role bersama (`$user->role->permissions()->sync(...)`), sehingga mengubah
 * izin satu akun anggota diam-diam mengubah izin SEMUA akun anggota.
 *
 * Tabel ini membuat izin benar-benar per orang. Izin efektif seorang user =
 * izin dari role-nya (baseline) DITAMBAH izin pribadinya di tabel ini.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('permission_user')) {
            Schema::create('permission_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['user_id', 'permission_id']);
            });
        }

        // Pindahkan izin role 'anggota' menjadi izin pribadi tiap akunnya.
        //
        // 'anggota' dimaksudkan sebagai role dasar tanpa izin (lihat komentar di
        // RolePermissionSeeder), tapi izin sempat menempel di sana lewat form yang
        // bermasalah. Setelah dipindahkan, izin tiap akun anggota bisa diatur
        // sendiri-sendiri. Akses efektif setiap orang TIDAK berubah karena
        // izinnya disalin lebih dulu sebelum dilepas dari role.
        $anggota = Role::where('name', 'anggota')->first();

        if (!$anggota) {
            return;
        }

        $permissionIds = $anggota->permissions()->pluck('permissions.id')->all();

        if (empty($permissionIds)) {
            return;
        }

        $now = now();

        User::where('role_id', $anggota->id)->get()->each(function (User $user) use ($permissionIds, $now) {
            $rows = collect($permissionIds)->map(fn($pid) => [
                'user_id' => $user->id,
                'permission_id' => $pid,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();

            DB::table('permission_user')->insertOrIgnore($rows);
        });

        // Role 'anggota' kembali menjadi baseline kosong
        $anggota->permissions()->detach();
    }

    public function down(): void
    {
        // Kembalikan izin pribadi akun anggota ke role-nya, lalu buang tabelnya.
        $anggota = Role::where('name', 'anggota')->first();

        if ($anggota && Schema::hasTable('permission_user')) {
            $permissionIds = DB::table('permission_user')
                ->join('users', 'users.id', '=', 'permission_user.user_id')
                ->where('users.role_id', $anggota->id)
                ->distinct()
                ->pluck('permission_user.permission_id')
                ->all();

            if (!empty($permissionIds)) {
                $anggota->permissions()->syncWithoutDetaching($permissionIds);
            }
        }

        Schema::dropIfExists('permission_user');
    }
};
