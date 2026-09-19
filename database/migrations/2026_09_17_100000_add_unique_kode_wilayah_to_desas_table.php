<?php

/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Satu desa hanya boleh didaftarkan sekali: unique index pada
 * kode_wilayah. Lapisan kedua setelah validasi `unique` di
 * DesaController — melindungi dari race condition dua request
 * yang lolos validasi hampir bersamaan (mis. double-submit).
 * ============================================================ */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('desas', function (Blueprint $table) {
            $table->unique('kode_wilayah', 'desas_kode_wilayah_unique');
        });
    }

    public function down(): void
    {
        Schema::table('desas', function (Blueprint $table) {
            $table->dropUnique('desas_kode_wilayah_unique');
        });
    }
};
