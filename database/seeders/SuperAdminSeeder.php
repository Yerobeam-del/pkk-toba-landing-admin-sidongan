<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        // Cek apakah email sudah ada agar tidak error duplikat
        if (DB::table('users')->where('email', 'super.admin@pkk-toba.id')->doesntExist()) {
            
            DB::table('users')->insert([
                'name' => 'Super Admin',
                'email' => 'super.admin@pkk-toba.id',
                'password' => Hash::make('PassKeyPKKTobaDel2026!'),
                // Kita set role menjadi 'super_admin'
                // Pastikan Controller Anda mengizinkan role ini akses ke semua fungsi
                'sidongan_role' => 'super_admin', 
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info('Super Admin berhasil dibuat!');
            $this->command->info(' Email: super.admin@pkk-toba.id');
            $this->command->info('Password: PassKeyPKKTobaDel2026!');
        } else {
            $this->command->warn('Akun Super Admin sudah ada.');
        }
    }
}