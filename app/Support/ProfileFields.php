<?php

/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Support;

/**
 * Sumber tunggal aturan kelengkapan profil (dipakai Admin Panel / SIDONGAN).
 *
 * Field PEMBLOKIR — phone_number & personal_email — adalah satu-satunya yang
 * menentukan apakah user boleh masuk aplikasi (setelah login maupun lewat
 * deep link). Foto profil (avatar) bersifat opsional: hanya dilacak sebagai
 * langkah UI di halaman onboarding, TIDAK pernah menghalangi akses.
 *
 * Konsumen:
 *  - App\Http\Middleware\SidonganEnsureProfileComplete → proteksi deep link
 *  - App\Http\Controllers\OnboardingController           → halaman & store
 *  - App\Http\Controllers\Sidongan\AuthController        → redirect setelah login
 *
 * Catatan: aplikasi SIEDA punya padanan berkas ini dengan logika yang sama —
 * kedua aplikasi harus memakai daftar placeholder & field pemblokir yang
 * identik agar perilaku onboarding sama persis.
 */
class ProfileFields
{
    /**
     * Nilai "placeholder" yang dianggap belum diisi (sisa data lama/import).
     * Dibandingkan case-insensitive (sudah dinormalisasi huruf kecil).
     */
    public const PLACEHOLDERS = ['-', '--', '0', 'n/a', '- -', 'belum diisi', 'tidak ada'];

    /**
     * Field yang menghalangi akses aplikasi bila kosong.
     */
    public const BLOCKING_FIELDS = ['phone_number', 'personal_email'];

    /**
     * Field opsional yang hanya dilacak untuk tampilan langkah onboarding.
     */
    public const OPTIONAL_FIELDS = ['avatar'];

    /**
     * True bila nilai dianggap terisi (bukan kosong / placeholder).
     */
    public static function isFilled($value): bool
    {
        $normalized = strtolower(trim((string) $value));

        return $normalized !== '' && !in_array($normalized, self::PLACEHOLDERS, true);
    }

    /**
     * Field pemblokir yang masih kosong pada user.
     *
     * @return array<int, string>
     */
    public static function missingBlocking($user): array
    {
        $missing = [];

        foreach (self::BLOCKING_FIELDS as $field) {
            if (!self::isFilled($user->{$field} ?? null)) {
                $missing[] = $field;
            }
        }

        return $missing;
    }

    /**
     * True bila tidak ada field pemblokir yang kosong → user boleh masuk aplikasi.
     */
    public static function blockingComplete($user): bool
    {
        return self::missingBlocking($user) === [];
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
