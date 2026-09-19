<?php

/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom OTP verifikasi email pribadi untuk onboarding Admin Panel:
     * kode 6 digit dikirim ke email, diverifikasi langsung di halaman
     * onboarding tanpa keluar (link signed di email tetap ada sebagai
     * fallback).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('personal_email_otp_hash')->nullable()->after('personal_email_verified_at');
            $table->string('personal_email_otp_email')->nullable()->after('personal_email_otp_hash');
            $table->timestamp('personal_email_otp_expires_at')->nullable()->after('personal_email_otp_email');
            $table->unsignedTinyInteger('personal_email_otp_attempts')->default(0)->after('personal_email_otp_expires_at');
            $table->timestamp('personal_email_otp_requested_at')->nullable()->after('personal_email_otp_attempts');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'personal_email_otp_hash',
                'personal_email_otp_email',
                'personal_email_otp_expires_at',
                'personal_email_otp_attempts',
                'personal_email_otp_requested_at',
            ]);
        });
    }
};
/* Dikembangkan oleh Institut Teknologi Del */
