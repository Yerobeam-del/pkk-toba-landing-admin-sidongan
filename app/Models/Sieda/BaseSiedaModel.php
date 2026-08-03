<?php

namespace App\Models\Sieda;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk membaca data dari database SIEDA (aplikasi terpisah).
 *
 * Semua model di namespace ini menggunakan koneksi 'sieda' yang
 * diatur di config/database.php (env DB_SIEDA_*).
 *
 * PENTING: Soft-delete di sisi SIEDA menggunakan kolom `active` (0/1),
 * BUKAN Laravel's `deleted_at`. Untuk keperluan Manajemen Data:
 *   - `active = 1` → data tampil di SIEDA web & mobile
 *   - `active = 0` → data di-nonaktifkan (soft-delete)
 *   - `->forceDelete()`   → hard-delete permanen (dipakai di Admin Panel ini)
 *
 * JANGAN tambahkan `use SoftDeletes;` — trait tersebut memakai kolom
 * `deleted_at` yang tidak ada di skema SIEDA.
 */
abstract class BaseSiedaModel extends Model
{
    protected $connection = 'sieda';

    /**
     * Disable Laravel's automatic timestamp handling (SIEDA menggunakan
     * created_at/updated_at sendiri tanpa casts Eloquent).
     */
    public $timestamps = true;

    /**
     * SIEDA tidak menggunakan Eloquent casts — ambil mentah dari DB.
     */
    protected $casts = [
        'active' => 'integer',
        'config_year' => 'integer',
    ];

    /**
     * Cari record berdasarkan primary key — override di child class.
     */
    abstract public static function primaryKey(): string;

    /**
     * Nama modul untuk tampilan di UI Admin.
     */
    abstract public static function moduleName(): string;
}
