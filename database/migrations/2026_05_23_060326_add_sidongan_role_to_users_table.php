<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('sidongan_role')->nullable()->after('email');
            // Role options: bupati, ketua, sekretaris, bendahara, pokja1, pokja2, pokja3, pokja4
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('sidongan_role');
        });
    }
};