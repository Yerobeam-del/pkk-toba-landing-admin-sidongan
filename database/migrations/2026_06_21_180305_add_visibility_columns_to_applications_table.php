<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->boolean('show_in_floating')->default(true)->after('is_active');
            $table->boolean('show_in_footer')->default(true)->after('show_in_floating');
            $table->boolean('show_in_quick_access')->default(true)->after('show_in_footer');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['show_in_floating', 'show_in_footer', 'show_in_quick_access']);
        });
    }
};