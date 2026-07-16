<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('activity_reports', function (Blueprint $table) {
            $table->time('start_time')->nullable()->after('kegiatan_tanggal');
            $table->time('end_time')->nullable()->after('start_time');
            $table->string('provinsi')->nullable()->after('end_time');
            $table->string('kabupaten')->nullable()->after('provinsi');
            $table->string('kecamatan')->nullable()->after('kabupaten');
            $table->string('kelurahan')->nullable()->after('kecamatan');
            $table->text('alamat_lengkap')->nullable()->after('kelurahan');
        });
    }

    public function down()
    {
        Schema::table('activity_reports', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time', 'provinsi', 'kabupaten', 'kecamatan', 'kelurahan', 'alamat_lengkap']);
        });
    }
};
