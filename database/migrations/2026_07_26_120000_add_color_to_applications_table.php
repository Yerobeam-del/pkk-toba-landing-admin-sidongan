<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (!Schema::hasColumn('applications', 'color')) {
                // Hex warna utama kartu di landing page, contoh: #0f6b63.
                // Dibiarkan null untuk aplikasi lama; landing page memakai
                // warna default PKK selama kolom ini kosong.
                $table->string('color', 7)->nullable()->after('icon');
            }
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'color')) {
                $table->dropColumn('color');
            }
        });
    }
};
