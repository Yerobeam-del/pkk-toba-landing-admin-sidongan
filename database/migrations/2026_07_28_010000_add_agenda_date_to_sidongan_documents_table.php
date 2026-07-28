<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sidongan_documents', function (Blueprint $table) {
            $table->date('agenda_date')->nullable()->after('agenda_number');
        });
    }

    public function down(): void
    {
        Schema::table('sidongan_documents', function (Blueprint $table) {
            $table->dropColumn('agenda_date');
        });
    }
};
