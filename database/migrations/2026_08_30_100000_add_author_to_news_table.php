<?php

/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->string('author')->nullable()->after('category');
            $table->enum('author_type', ['account', 'manual'])->default('account')->after('author');
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn(['author', 'author_type']);
        });
    }
};
/* Dikembangkan oleh Institut Teknologi Del */
