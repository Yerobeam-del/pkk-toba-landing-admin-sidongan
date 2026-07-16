<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sidongan_documents', function (Blueprint $table) {
            // Pastikan nama tabel: sidongan_documents (bukan documents)
            
            if (!Schema::hasColumn('sidongan_documents', 'sender')) {
                $table->string('sender')->nullable()->after('title');
            }
            if (!Schema::hasColumn('sidongan_documents', 'document_number')) {
                $table->string('document_number')->nullable()->after('sender');
            }
            if (!Schema::hasColumn('sidongan_documents', 'agenda_number')) {
                $table->string('agenda_number')->unique()->nullable()->after('document_number');
            }
            if (!Schema::hasColumn('sidongan_documents', 'document_date')) {
                $table->date('document_date')->nullable()->after('agenda_number');
            }
            if (!Schema::hasColumn('sidongan_documents', 'subject')) {
                $table->string('subject')->nullable()->after('document_date');
            }
            if (!Schema::hasColumn('sidongan_documents', 'suggestion')) {
                $table->text('suggestion')->nullable()->after('subject');
            }
            if (!Schema::hasColumn('sidongan_documents', 'status')) {
                $table->enum('status', ['draft', 'menunggu_disposisi', 'berjalan', 'selesai', 'diarsipkan'])
                      ->default('menunggu_disposisi')->after('suggestion');
            }
            // ✅ Pastikan category_id nullable
            if (!Schema::hasColumn('sidongan_documents', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained('sidongan_categories')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sidongan_documents', function (Blueprint $table) {
            $table->dropColumn(['sender', 'document_number', 'agenda_number', 'document_date', 'subject', 'suggestion', 'status']);
        });
    }
};