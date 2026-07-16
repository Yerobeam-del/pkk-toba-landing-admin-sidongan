<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kecamatans', function (Blueprint $table) {
            $table->string('kode_wilayah')->nullable()->after('id');
            $table->string('kabupaten_kode')->nullable()->after('kode_wilayah');
        });
    }

    public function down(): void
    {
        Schema::table('kecamatans', function (Blueprint $table) {
            $table->dropColumn(['kode_wilayah', 'kabupaten_kode']);
        });
    }
};