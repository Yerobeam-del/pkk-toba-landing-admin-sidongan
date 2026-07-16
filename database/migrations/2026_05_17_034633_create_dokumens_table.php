<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumens', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama dokumen
            $table->string('file_path'); // Path file di storage
            $table->string('file_name'); // Nama file asli
            $table->string('file_size'); // Ukuran file (formatted: 2.5 MB)
            $table->string('file_type')->nullable(); // MIME type
            $table->date('document_date'); // Tanggal dokumen
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumens');
    }
};