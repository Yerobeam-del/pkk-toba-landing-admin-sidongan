<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Kategori
        Schema::create('sidongan_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color')->default('#14b8a6');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabel Dokumen
        Schema::create('sidongan_documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            
            // Data Surat Masuk
            $table->string('sender')->nullable();
            $table->string('document_number')->nullable();
            $table->string('agenda_number')->unique()->nullable();
            $table->date('document_date')->nullable();
            $table->string('subject')->nullable();
            $table->text('suggestion')->nullable();
            
            // File
            $table->foreignId('category_id')->nullable()->constrained('sidongan_categories')->onDelete('set null');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type');
            $table->unsignedBigInteger('file_size');
            
            // Status dengan enum lengkap untuk alur SIDONGAN
            $table->enum('status', [
                'draft',
                'menunggu_disposisi',
                'berjalan',
                'menunggu_verifikasi',
                'selesai',
                'diarsipkan',
                'published',
                'archived',
                'ditolak'
            ])->default('menunggu_disposisi');
            
            $table->boolean('is_public')->default(false);
            $table->json('metadata')->nullable();
            
            // Disposisi & Verifikasi Data
            $table->json('disposisi_data')->nullable();
            $table->json('verifikasi_data')->nullable();
            
            // User Relations
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index
            $table->index(['slug', 'status', 'is_public']);
            $table->index(['category_id', 'document_date']);
            $table->index(['agenda_number']);
            $table->index(['sender', 'status']);
        });

        // Tabel Tag
        Schema::create('sidongan_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // Tabel Pivot Dokumen - Tag
        Schema::create('sidongan_document_tag', function (Blueprint $table) {
            $table->foreignId('document_id')->constrained('sidongan_documents')->onDelete('cascade');
            $table->foreignId('tag_id')->constrained('sidongan_tags')->onDelete('cascade');
            $table->primary(['document_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sidongan_document_tag');
        Schema::dropIfExists('sidongan_tags');
        Schema::dropIfExists('sidongan_documents');
        Schema::dropIfExists('sidongan_categories');
    }
};