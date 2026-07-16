<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Application;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $applications = [
            [
                'name' => 'SIEDA (E-Dasawisma)',
                'short_name' => 'SIEDA',
                'description' => 'Sistem Informasi Dasawisma',
                'category' => 'aplikasi',
                'url' => '#',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'name' => 'SIDONGAN (Surat Menyurat)',
                'short_name' => 'SIDONGAN',
                'description' => 'Sistem Surat Menyurat Digital',
                'category' => 'aplikasi',
                'url' => '#',
                'sort_order' => 2,
                'is_active' => true
            ],
            [
                'name' => 'Aplikasi',
                'short_name' => 'APP',
                'description' => 'Daftar aplikasi PKK',
                'category' => 'layanan',
                'url' => '#',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'name' => 'Berita',
                'short_name' => 'NEWS',
                'description' => 'Berita dan kegiatan PKK',
                'category' => 'layanan',
                'url' => '#',
                'sort_order' => 2,
                'is_active' => true
            ],
            [
                'name' => 'Data Desa',
                'short_name' => 'DESA',
                'description' => 'Data desa di Kabupaten Toba',
                'category' => 'layanan',
                'url' => '#',
                'sort_order' => 3,
                'is_active' => true
            ],
        ];
        
        foreach ($applications as $app) {
            Application::updateOrCreate(
                ['short_name' => $app['short_name']],
                $app
            );
        }
    }
}