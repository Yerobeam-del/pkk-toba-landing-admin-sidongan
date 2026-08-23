<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action');          // create, update, delete, toggle-status, reset-password, etc.
            $table->string('subject_type');     // Model class (User, News, etc.)
            $table->unsignedBigInteger('subject_id'); // ID of the affected model
            $table->text('description');        // Human-readable description
            $table->json('properties')->nullable(); // Extra data (old/new values, etc.)
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['subject_type', 'subject_id']);
            $table->index('action');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_activity_logs');
    }
};
