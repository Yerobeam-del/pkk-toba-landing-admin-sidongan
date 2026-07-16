<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus user lama (opsional)
        User::truncate();

        // Sekretaris
        User::create([
            'name' => 'Sekretaris PKK',
            'email' => 'sekretaris@pkk-toba.id',
            'password' => bcrypt('password'),
            'sidongan_role' => 'sekretaris'
        ]);

        // Ketua PKK
        User::create([
            'name' => 'Ketua PKK',
            'email' => 'ketua@pkk-toba.id',
            'password' => bcrypt('password'),
            'sidongan_role' => 'ketua'
        ]);

        // Bendahara
        User::create([
            'name' => 'Bendahara PKK',
            'email' => 'bendahara@pkk-toba.id',
            'password' => bcrypt('password'),
            'sidongan_role' => 'bendahara'
        ]);

        // Ketua POKJA 1-4
        foreach (['1', '2', '3', '4'] as $num) {
            User::create([
                'name' => "Ketua POKJA $num",
                'email' => "pokja{$num}@pkk-toba.id",
                'password' => bcrypt('password'),
                'sidongan_role' => "pokja{$num}"
            ]);
        }
    }
}