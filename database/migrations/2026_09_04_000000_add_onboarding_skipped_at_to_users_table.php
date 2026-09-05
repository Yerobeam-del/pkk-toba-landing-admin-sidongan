<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rekam kapan user memilih "Lewati — nanti saja" di halaman onboarding.
     *
     * Sebelumnya keputusan skip hanya disimpan di session, sehingga hilang saat
     * logout/login — user yang profilnya belum lengkap dilempar ke onboarding
     * LAGI di setiap login (loop). Kolom ini membuat keputusan skip bertahan
     * lintas sesi; field pemblokir (phone_number & personal_email) tetap wajib
     * diisi lewat Edit Profil, tetapi user tidak lagi terjebak di onboarding.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('onboarding_skipped_at')->nullable()->after('personal_email_verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('onboarding_skipped_at');
        });
    }
};
