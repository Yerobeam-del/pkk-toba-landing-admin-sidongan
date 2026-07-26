<?php

use App\Models\ActivityReport;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Laporan kegiatan menjadi milik ROLE, bukan milik satu orang.
 *
 * Sebelumnya laporan hanya terikat pada `created_by`, sehingga bila satu role
 * diisi lebih dari satu akun, tiap orang membuat laporannya sendiri untuk surat
 * yang sama dan tidak bisa melihat laporan rekan serole-nya.
 *
 * Dengan kolom `role`:
 *   - satu surat + satu role  = satu laporan (dikerjakan bersama serole),
 *   - surat yang didisposisi ke beberapa role tetap punya laporan terpisah per role,
 *   - `created_by` DIPERTAHANKAN sebagai catatan siapa yang pertama membuat.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('activity_reports', 'role')) {
            Schema::table('activity_reports', function (Blueprint $table) {
                $table->string('role')->nullable()->after('document_id')->index();
            });
        }

        // Isi role laporan lama dari peran pembuatnya, agar data lama tetap terbaca
        // oleh aturan akses yang baru.
        ActivityReport::whereNull('role')->with('creator')->get()->each(function ($laporan) {
            $peran = optional(User::find($laporan->created_by))->sidongan_role;
            if ($peran) {
                $laporan->updateQuietly(['role' => $peran]);
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('activity_reports', 'role')) {
            Schema::table('activity_reports', function (Blueprint $table) {
                $table->dropIndex(['role']);
                $table->dropColumn('role');
            });
        }
    }
};
