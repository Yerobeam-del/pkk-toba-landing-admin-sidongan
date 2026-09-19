<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Dikembangkan oleh Institut Teknologi Del
 *
 * Multi-assignment desa per akun: satu akun (kader/operator) kini bisa
 * mengelola LEBIH DARI SATU desa landing page. Kolom sieda_desas menyimpan
 * daftar kode wilayah desa (JSON array); sieda_kelurahan tetap dipakai
 * sebagai DESA UTAMA (kompatibel dengan sinkronisasi SIEDA — baris pertama
 * daftar menjadi utama).
 *
 * Launcher "Pilih Ruang Kerja" memakai kolom ini:
 *   - tepat 1 desa  → kartu langsung masuk tanpa pilihan,
 *   - lebih dari 1  → klik kartu memunculkan langkah "Pilih Desa".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('sieda_desas')->nullable()->after('sieda_kelurahan');
        });

        // Backfill: akun yang sudah punya desa utama memulai dengan satu
        // assignment — perilaku lama tetap berjalan tanpa perubahan.
        $rows = DB::table('users')->whereNotNull('sieda_kelurahan')->get(['id', 'sieda_kelurahan']);
        foreach ($rows as $row) {
            DB::table('users')->where('id', $row->id)->update([
                'sieda_desas' => json_encode([(string) $row->sieda_kelurahan]),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('sieda_desas');
        });
    }
};
