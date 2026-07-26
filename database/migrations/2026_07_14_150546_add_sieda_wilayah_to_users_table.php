<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kolom-kolom ini sudah terlanjur ada di sebagian database, sehingga
        // migrasi tanpa penjagaan akan gagal dan memblokir migrasi berikutnya.
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'sieda_role')) {
                $table->string('sieda_role')->nullable()->after('sidongan_role');
            }
            if (!Schema::hasColumn('users', 'sieda_kecamatan')) {
                $table->string('sieda_kecamatan')->nullable()->after('sieda_role'); // Kode kecamatan
            }
            if (!Schema::hasColumn('users', 'sieda_kelurahan')) {
                $table->string('sieda_kelurahan')->nullable()->after('sieda_kecamatan'); // Kode kelurahan
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['sieda_role', 'sieda_kecamatan', 'sieda_kelurahan']);
        });
    }
};