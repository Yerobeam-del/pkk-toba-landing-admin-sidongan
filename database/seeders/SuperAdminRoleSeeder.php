<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class SuperAdminRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Cari role administrator
        $adminRole = Role::where('name', 'administrator')->first();
        
        if (!$adminRole) {
            $this->command->error('Role administrator tidak ditemukan!');
            return;
        }
        
        // Cari user Super Admin
        $superAdmin = User::where('sidongan_role', 'super_admin')->first();
        
        if ($superAdmin) {
            $superAdmin->role_id = $adminRole->id;
            $superAdmin->save();
            
            $this->command->info("Role administrator berhasil di-set untuk user: {$superAdmin->email}");
        } else {
            $this->command->error('User Super Admin tidak ditemukan!');
        }
    }
}