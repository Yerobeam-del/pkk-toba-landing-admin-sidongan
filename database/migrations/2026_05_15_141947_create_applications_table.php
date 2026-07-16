<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama aplikasi
            $table->string('short_name')->unique(); // Singkatan (SIEDA, SIDONGAN, dll)
            $table->string('description')->nullable(); // Deskripsi singkat
            $table->string('url')->nullable(); // Link aplikasi
            $table->string('icon')->nullable(); // Icon/path gambar
            $table->string('category')->default('layanan'); // kategori: layanan/aplikasi
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};