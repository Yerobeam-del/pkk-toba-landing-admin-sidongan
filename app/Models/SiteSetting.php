<?php

/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * SiteSetting: penyimpanan key-value untuk konten situs yang
 * diatur dari Admin Panel (alamat footer, sosial media, judul
 * footer, teks copyright, dll).
 *
 * Semua nilai ter-cache (CACHE_DRIVER=file); cache di-flush setiap
 * kali setting disimpan sehingga perubahan langsung terlihat.
 */
class SiteSetting extends Model
{
    protected $table = 'site_settings';

    protected $fillable = ['key', 'value'];

    /** Kunci yang dikenal + nilai defaultnya (fallback jika baris belum di-seed). */
    public const DEFAULTS = [
        'footer_title'        => 'PKK Kabupaten Toba',
        'footer_address'      => "Jl. D. I. Panjaitan, No. 1, Balige,\nKabupaten Toba,\nSumatera Utara 22311",
        'instagram_url'       => 'https://www.instagram.com/tppkktoba_/',
        'instagram_handle'    => '@tppkktoba_',
        'footer_copyright'    => 'TP-PKK Kabupaten Toba',
        // Path logo di disk public (mis. site/logo-toba_abc123.png).
        // Kosong/null = footer memakai file bawaan di assets/landing/images.
        'footer_logo_toba'    => null,
        'footer_logo_pkk'     => null,
    ];

    /** Cache key prefix + durasi (detik). */
    private const CACHE_PREFIX = 'site_setting_';
    private const CACHE_TTL = 86400;

    public static function get(string $key, ?string $fallback = null): string
    {
        $default = self::DEFAULTS[$key] ?? $fallback ?? '';

        return Cache::remember(self::CACHE_PREFIX . $key, self::CACHE_TTL, function () use ($key, $default) {
            $row = static::query()->where('key', $key)->first();

            // value nullable: baris tanpa nilai jatuh ke default
            return ($row && $row->value !== null && $row->value !== '') ? $row->value : $default;
        });
    }

    /** Simpan satu atau banyak setting sekaligus (flush cache otomatis). */
    public static function set(array $values): void
    {
        foreach ($values as $key => $value) {
            static::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
            Cache::forget(self::CACHE_PREFIX . $key);
        }
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
