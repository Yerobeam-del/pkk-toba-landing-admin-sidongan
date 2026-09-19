<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sidongan_outgoing_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incoming_document_id')->constrained('sidongan_documents')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('outgoing_number')->nullable()->unique();
            $table->string('recipient');
            $table->text('recipient_address')->nullable();
            $table->string('subject');
            $table->text('body');
            $table->string('nature')->default('Biasa');
            $table->string('attachment_description')->nullable();
            $table->text('cc')->nullable();
            $table->date('letter_date')->nullable();
            $table->string('signatory_name')->nullable();
            $table->string('signatory_title')->default('Ketua TP PKK Kabupaten Toba');
            $table->string('status')->default('draft');
            $table->text('revision_note')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['incoming_document_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sidongan_outgoing_letters');
    }
};
