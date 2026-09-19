<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 *
 * Ruang kerja terakhir user (launcher "Pilih Ruang Kerja").
 *
 * Setelah login, user dengan akses lebih dari satu aplikasi melihat
 * launcher berisi kartu aplikasi yang bisa diaksesnya. Pilihannya
 * disimpan di kolom ini dan dijadikan tujuan redirect default pada
 * login berikutnya (tetap bisa diganti lewat menu "Ganti Ruang Kerja").
 *
 * Nilai: null (belum pernah memilih — launcher tampil), 'pkk'
 * (Admin Panel PKK), 'sieda' (SIEDA via SSO), 'sidongan' (SIDONGAN).
 * ============================================================ */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('workspace', 20)->nullable()->after('onboarding_skipped_at')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['workspace']);
            $table->dropColumn('workspace');
        });
    }
};
/* Dikembangkan oleh Institut Teknologi Del */
