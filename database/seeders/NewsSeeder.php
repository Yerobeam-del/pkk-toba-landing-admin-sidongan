<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $news = [
            [
                'title' => 'Pembinaan Pokja II TP PKK kab.Toba ke Desa binaan UP2K PKK Desa Sigaol Timur',
                'slug' => Str::slug('Pembinaan Pokja II TP PKK kab.Toba ke Desa binaan UP2K PKK Desa Sigaol Timur'),
                'category' => 'UP2K',
                'excerpt' => 'Kegiatan pembinaan ke Desa binaan UP2K TP PKK Sigaol Timur dilaksanakan untuk meningkatkan kemampuan TP PKK Desa Sigaol Timur dalam memodifikasi tenun ulos menjadi berbagai produk fashion seperti outer, tas dan dompet dari bahan ulos sebagai produk unggulan Desa Sigaol Timur.',
                'content' => 'Kegiatan pembinaan ke Desa binaan UP2K TP PKK Sigaol Timur dilaksanakan untuk meningkatkan kemampuan TP PKK Desa Sigaol Timur dalam memodifikasi tenun ulos menjadi berbagai produk fashion seperti outer, tas dan dompet dari bahan ulos sebagai produk unggulan Desa Sigaol Timur.',
                'image_path' => 'news/berita-1.jpg',
                'published_at' => '2026-04-02 00:00:00',
                'is_published' => true,
            ],
            [
                'title' => 'Pembinaan Desa Binaan Kabupaten Toba Tahun 2026',
                'slug' => Str::slug('Pembinaan Desa Binaan Kabupaten Toba Tahun 2026'),
                'category' => 'Aku Hatinya PKK',
                'excerpt' => 'Melaksanakan kegiatan Pembinaan kategori Aku Hatinya PKK TP PKK Kabupaten Toba ke Desa Tangga Batu Barat, Kecamatan Tampahan',
                'content' => 'Melaksanakan kegiatan Pembinaan kategori Aku Hatinya PKK TP PKK Kabupaten Toba ke Desa Tangga Batu Barat, Kecamatan Tampahan',
                'image_path' => 'news/berita-2.jpg',
                'published_at' => '2026-03-26 00:00:00',
                'is_published' => true,
            ],
            [
                'title' => 'Rapat Konsultasi Pokja III',
                'slug' => Str::slug('Rapat Konsultasi Pokja III'),
                'category' => 'Rapat Konsultasi',
                'excerpt' => 'Mengikuti Pelaksanaan Rapat Konsultasi Pokja III yang membahsa ketahanan pangan dan seluruh program pokja III',
                'content' => 'Mengikuti Pelaksanaan Rapat Konsultasi Pokja III yang membahsa ketahanan pangan dan seluruh program pokja III',
                'image_path' => 'news/berita-3.jpg',
                'published_at' => '2026-03-12 00:00:00',
                'is_published' => true,
            ],
            [
                'title' => 'Kunjungan TP. PKK Kab. Toba Ke Tempat Pemilahan Sampah',
                'slug' => Str::slug('Kunjungan TP. PKK Kab. Toba Ke Tempat Pemilahan Sampah'),
                'category' => 'Kunjungan',
                'excerpt' => 'Anggota TP. PKK Kab. Toba melaksanakan kunjungan ke tempat pemilahan sampah d Desa Pintu Pohan Kecamatan Pintu Pohan Meranti',
                'content' => 'Anggota TP. PKK Kab. Toba melaksanakan kunjungan ke tempat pemilahan sampah d Desa Pintu Pohan Kecamatan Pintu Pohan Meranti',
                'image_path' => 'news/berita-4.jpg',
                'published_at' => '2026-03-06 00:00:00',
                'is_published' => true,
            ],
            [
                'title' => 'Pembinaan Awal Desa Binaan Kategori Pola Asuh Anak dan Remaja (PAAR) tahun 2026',
                'slug' => Str::slug('Pembinaan Awal Desa Binaan Kategori Pola Asuh Anak dan Remaja PAAR tahun 2026'),
                'category' => 'Pola Asuh Anak & Remaja (PAAR)',
                'excerpt' => 'Melaksanakan pembinaan awal Desa Binaan kategori Pola Asuh Anak dan Remaja (PAAR) tahun 2026 di desa Jonggi Manulus, Kec. Parmaksian, Kab. Toba.',
                'content' => 'Melaksanakan pembinaan awal Desa Binaan kategori Pola Asuh Anak dan Remaja (PAAR) tahun 2026 di desa Jonggi Manulus, Kec. Parmaksian, Kab. Toba. Kegiatan ini dihadiri oleh Ibu Staf Ahli TP PKK Toba, Ketua I dan Ketua Pokja I TP PKK Toba, dan Camat Parmaksian serta Ketua TP PKK Kecamatan dan juga Ketua TP PKK Desa. Fokus Pembinaan tahun 2026 secara khusus dititikberatkan pada program PAAREDI (Pola Asuh Anak dan Remaja di Era Digital), yang bertujuan membentengi keluarga dari dampak negatif teknologi seperti judi online, perundungan siber, dan konten tidak layak. Selain literasi digital, pencegahan stunting melalui gerakan pencegahan perkawinan anak (CEPAK) menjadi pilar utama dalam arahan yang disampaikan oleh TP PKK Toba.',
                'image_path' => 'news/berita-5.jpg',
                'published_at' => '2026-03-05 00:00:00',
                'is_published' => true,
            ],
        ];

        foreach ($news as $item) {
            $item['created_at'] = now();
            $item['updated_at'] = now();
            DB::table('news')->updateOrInsert(
                ['slug' => $item['slug']],
                $item
            );
        }
    }
}