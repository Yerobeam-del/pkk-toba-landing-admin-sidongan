<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('hero_sliders', function (Blueprint $table) {
            // Hapus kolom yang tidak diperlukan (optional - bisa di-set nullable saja)
            $table->string('title')->nullable()->change();
            $table->text('description')->nullable()->change();
            $table->string('button_text')->nullable()->change();
            $table->string('button_url')->nullable()->change();
        });
    }
    
    public function down(): void {
        Schema::table('hero_sliders', function (Blueprint $table) {
            $table->string('title')->change();
            $table->text('description')->change();
            $table->string('button_text')->change();
            $table->string('button_url')->change();
        });
    }
};