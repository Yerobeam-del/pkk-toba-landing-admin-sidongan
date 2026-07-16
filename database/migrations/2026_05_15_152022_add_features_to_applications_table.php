<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->json('features')->nullable()->after('description');
            // Menyimpan array fitur, contoh: 
            // ["Terintegrasi dengan data PKK", "Akses real-time 24/7", "Keamanan data terjamin"]
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('features');
        });
    }
};