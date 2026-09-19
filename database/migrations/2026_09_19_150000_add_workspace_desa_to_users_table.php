<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 *
 * Desa terpilih pada ruang kerja "Admin Panel Desa" — dipakai
 * launcher "Pilih Ruang Kerja" untuk pengelola LINTAS desa
 * (super admin / operator kecamatan) yang memilih desa lewat
 * dropdown di kartu. User yang di-assign satu desa tidak memakai
 * kolom ini (desanya tetap mengikuti assignment di Manajemen Akun).
 *
 * Format nilai: kode wilayah desa, mis. 12.12.03.2028.
 * ============================================================ */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('workspace_desa', 20)->nullable()->after('workspace');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('workspace_desa');
        });
    }
};
/* Dikembangkan oleh Institut Teknologi Del */
