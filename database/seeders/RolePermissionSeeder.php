<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            // User Management
            ['name' => 'manage-users', 'display_name' => 'Kelola Pengguna', 'group' => 'users'],
            ['name' => 'view-users', 'display_name' => 'Lihat Pengguna', 'group' => 'users'],
            
            // Berita
            ['name' => 'manage-berita', 'display_name' => 'Kelola Berita', 'group' => 'berita'],
            
            // Struktur
            ['name' => 'manage-struktur', 'display_name' => 'Kelola Struktur', 'group' => 'struktur'],
            
            // Aplikasi
            ['name' => 'manage-aplikasi', 'display_name' => 'Kelola Aplikasi', 'group' => 'aplikasi'],
            
            // Desa
            ['name' => 'manage-desa', 'display_name' => 'Kelola Desa', 'group' => 'desa'],
            
            // SK & Dokumen
            ['name' => 'manage-dokumen', 'display_name' => 'Kelola SK & Dokumen', 'group' => 'dokumen'],
            
            // Template
            ['name' => 'manage-template', 'display_name' => 'Kelola Template', 'group' => 'template'],
            
            // Tentang Kami
            ['name' => 'manage-tentang', 'display_name' => 'Kelola Tentang Kami', 'group' => 'tentang'],
            
            // Hero Slider
            ['name' => 'manage-hero-slider', 'display_name' => 'Kelola Hero Slider', 'group' => 'hero-slider'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name']], // Kunci unik untuk dicek
                $perm // Data yang diisi jika belum ada
            );
        }

        // Create Roles
        $adminRole = Role::firstOrCreate(
            ['name' => 'administrator'],
            [
                'display_name' => 'Administrator',
                'description' => 'Akses penuh ke semua fitur admin panel'
            ]
        );

        $memberRole = Role::firstOrCreate(
            ['name' => 'anggota'],
            [
                'display_name' => 'Anggota',
                'description' => 'Akses terbatas sesuai permission yang diberikan'
            ]
        );

        // Administrator gets all permissions
        $adminRole->permissions()->attach(Permission::all());

        // Anggota gets no permissions by default (will be assigned individually)
    }
}