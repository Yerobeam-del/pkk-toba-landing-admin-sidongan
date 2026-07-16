<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Tambahkan kolom status jika belum ada
            if (!Schema::hasColumn('applications', 'status')) {
                $table->string('status')->default('active')->after('category');
            }
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};