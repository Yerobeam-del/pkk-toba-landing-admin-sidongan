<?php

/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

/**
 * Seed awal Pengaturan Situs: baris setting default + permission
 * manage-settings yang otomatis diberikan ke role administrator.
 *
 * firstOrCreate: aman dijalankan berulang, tidak menimpa hasil
 * edit admin di database yang sudah berjalan.
 */
class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (SiteSetting::DEFAULTS as $key => $value) {
            // Kunci tanpa default (mis. path logo) tidak perlu baris:
            // model sudah jatuh ke default saat baris tidak ada.
            if ($value === null) {
                continue;
            }

            SiteSetting::firstOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $permission = Permission::firstOrCreate(
            ['name' => 'manage-settings'],
            [
                'display_name' => 'Kelola Pengaturan Situs',
                'group' => 'settings',
            ]
        );

        $adminRole = Role::where('name', 'administrator')->first();
        if ($adminRole && ! $adminRole->permissions()->where('permissions.id', $permission->id)->exists()) {
            $adminRole->permissions()->attach($permission);
        }
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
