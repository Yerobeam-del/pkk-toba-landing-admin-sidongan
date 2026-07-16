<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StrukturSeeder extends Seeder
{
    public function run(): void
    {
        // Pokja
        $pokja = [
            ['name' => 'Pengurus Inti', 'slug' => 'pengurus-inti', 'sort_order' => 0],
            ['name' => 'Pokja I', 'slug' => 'pokja-1', 'description' => 'Penghayatan dan Pengamalan Pancasila', 'sort_order' => 1],
            ['name' => 'Pokja II', 'slug' => 'pokja-2', 'description' => 'Pendidikan, Keterampilan & Pengembangan Usaha', 'sort_order' => 2],
            ['name' => 'Pokja III', 'slug' => 'pokja-3', 'description' => 'Pangan, Sandang, Tata Laksana Rumah Tangga', 'sort_order' => 3],
            ['name' => 'Pokja IV', 'slug' => 'pokja-4', 'description' => 'Kesehatan, Kelestarian Lingkungan & Perencanaan Sehat', 'sort_order' => 4],
        ];
        
        foreach ($pokja as $p) {
            $p['created_at'] = now();
            $p['updated_at'] = now();
            DB::table('pokja')->updateOrInsert(['slug' => $p['slug']], $p);
        }
        
        // Contoh Anggota Pengurus Inti
        $members = [
            ['name' => 'Ny. [Nama Ketua]', 'position' => 'Ketua', 'pokja_id' => 1, 'sort_order' => 1],
            ['name' => 'Ny. [Nama Wakil]', 'position' => 'Wakil Ketua', 'pokja_id' => 1, 'sort_order' => 2],
            ['name' => 'Ny. [Nama Sekretaris]', 'position' => 'Sekretaris', 'pokja_id' => 1, 'sort_order' => 3],
            ['name' => 'Ny. [Nama Bendahara]', 'position' => 'Bendahara', 'pokja_id' => 1, 'sort_order' => 4],
        ];
        
        foreach ($members as $m) {
            $m['created_at'] = now();
            $m['updated_at'] = now();
            DB::table('struktur_members')->insert($m);
        }
    }
}