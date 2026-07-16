<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sidongan_documents', function (Blueprint $table) {
            // Ubah kolom file_path, file_name, file_type agar bisa NULL (kosong)
            $table->string('file_path')->nullable()->change();
            $table->string('file_name')->nullable()->change();
            $table->string('file_type')->nullable()->change();
            // file_size sudah nullable atau tidak masalah jika 0, tapi biarkan saja
        });
    }

    public function down(): void
    {
        Schema::table('sidongan_documents', function (Blueprint $table) {
            // Kembalikan ke semula jika rollback
            $table->string('file_path')->nullable(false)->change();
            $table->string('file_name')->nullable(false)->change();
            $table->string('file_type')->nullable(false)->change();
        });
    }
};