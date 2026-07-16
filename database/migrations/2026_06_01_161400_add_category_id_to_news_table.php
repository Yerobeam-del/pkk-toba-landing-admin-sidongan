<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Cek apakah kolom category_id sudah ada
        if (!Schema::hasColumn('news', 'category_id')) {
            Schema::table('news', function (Blueprint $table) {
                $table->foreignId('category_id')->nullable()->after('id');
            });
        }
        
        // Cek apakah tabel categories sudah ada
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
        
        // Tambahkan foreign key jika belum ada
        Schema::table('news', function (Blueprint $table) {
            // Cek apakah foreign key sudah ada
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $foreignKeys = $sm->listTableForeignKeys('news');
            $hasForeignKey = false;
            
            foreach ($foreignKeys as $foreignKey) {
                if (in_array('category_id', $foreignKey->getLocalColumns())) {
                    $hasForeignKey = true;
                    break;
                }
            }
            
            if (!$hasForeignKey) {
                $table->foreign('category_id')
                      ->references('id')
                      ->on('categories')
                      ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
        
        Schema::dropIfExists('categories');
    }
};