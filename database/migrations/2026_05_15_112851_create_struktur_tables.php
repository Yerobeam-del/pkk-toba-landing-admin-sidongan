<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Pokja (Kelompok Kerja)
        Schema::create('pokja', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "Pokja I", "Pokja II", dll
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabel Anggota Struktur
        Schema::create('struktur_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position'); // "Ketua", "Anggota", dll
            $table->foreignId('pokja_id')->nullable()->constrained('pokja')->onDelete('cascade');
            $table->string('photo_path')->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('struktur_members');
        Schema::dropIfExists('pokja');
    }
};