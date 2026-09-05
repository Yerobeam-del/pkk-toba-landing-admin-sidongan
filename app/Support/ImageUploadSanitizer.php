<?php

/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Sanitizer unggahan gambar: ekstensi file DITENTUKAN DARI ISI FILE
 * (magic bytes via finfo), bukan dari nama asli kiriman klien.
 *
 * Alasan keberadaan kelas ini:
 *
 * 1. Nama file klien tidak dipercaya. `getClientOriginalExtension()` hanya
 *    memotong teks setelah titik terakhir — file "shell.php.jpg" lolos
 *    sebagai "jpg" dari nama, dan server web yang salah konfigurasi bisa
 *    mengeksekusinya. Dengan sniffing isi, ekstensi yang tersimpan selalu
 *    sesuai isi file sebenarnya.
 *
 * 2. Aturan validasi `image` bawaan Laravel menerima image/svg+xml. SVG
 *    bisa memuat <script> — ketika file itu dilayani dari /storage (same
 *    origin), SVG berbahaya menjadi penyimpanan XSS. Kami menolaknya.
 *
 * Catatan penggunaan: pasangkan dengan validasi `image|mimes:jpg,jpeg,png,
 * webp,gif|max:...` di controller — sanitizer adalah lapis kedua yang
 * memastikan APA yang tersimpan selalu cocok dengan ISI file.
 */
class ImageUploadSanitizer
{
    /** MIME gambar yang diizinkan → ekstensi kanonis. SVG sengaja TIDAK ada. */
    private const ALLOWED_MIMES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];

    /**
     * Simpan gambar yang sudah tervalidasi ke disk 'public' dengan nama
     * aman + ekstensi hasil sniffing isi file.
     *
     * @param  UploadedFile  $file      File dari $request->file(...)
     * @param  string        $directory Tujuan di disk 'public' (mis. 'avatars')
     * @param  string        $prefix    Awalan nama file (mis. 'avatar_5_')
     * @return string|false             Path tersimpan, atau false bila isi file bukan gambar yang diizinkan.
     */
    public static function store(UploadedFile $file, string $directory, string $prefix): string|false
    {
        $ext = self::resolveExtension($file);

        if ($ext === null) {
            return false;
        }

        $filename = $prefix . time() . '.' . $ext;

        return $file->storeAs($directory, $filename, 'public');
    }

    /**
     * Tentukan ekstensi sejati dari isi file. Null bila bukan gambar
     * yang diizinkan (termasuk SVG, script, dan file tidak dikenal).
     */
    public static function resolveExtension(UploadedFile $file): ?string
    {
        if (!function_exists('finfo_open')) {
            // Ekstensi fileinfo tidak terpasang → gagal aman (fail-closed)
            return null;
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file->getRealPath());

        return self::ALLOWED_MIMES[$mime] ?? null;
    }

    /**
     * Sanitasi nama dasar dokumen/pattern yang dipakai sebagai nama file:
     * buang karakter berbahaya, batasi panjang. (Nama TIDAK dipercaya
     * sebagai ekstensi — itu tetap dari isi file.)
     */
    public static function safeBaseName(string $name, int $maxLength = 80): string
    {
        $base = pathinfo($name, PATHINFO_FILENAME);

        // Slug aman untuk filesystem; fallback bila hasilnya kosong
        $base = Str::slug($base) ?: 'file';

        return Str::limit($base, $maxLength, '');
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
