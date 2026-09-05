<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
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
        
        // Tambahkan foreign key jika belum ada.
        // Catatan Laravel 11: API Doctrine (getDoctrineSchemaManager) sudah
        // dihapus — cek FK dilakukan manual per driver agar migrasi tetap
        // bisa dijalankan dari nol (fresh install, test database :memory:).
        $connection = Schema::getConnection();
        $hasForeignKey = false;

        if ($connection->getDriverName() === 'sqlite') {
            // Baris PRAGMA foreign_key_list(news): kolom 'from' = kolom lokal FK
            $hasForeignKey = collect($connection->select('PRAGMA foreign_key_list(news)'))
                ->contains(fn ($fk) => ($fk->from ?? null) === 'category_id');
        } else {
            // MySQL/MariaDB via information_schema
            $rows = $connection->select(
                "SELECT COUNT(*) AS c FROM information_schema.key_column_usage
                 WHERE table_schema = DATABASE()
                   AND table_name = 'news'
                   AND column_name = 'category_id'
                   AND referenced_table_name = 'categories'"
            );
            $hasForeignKey = ((int) ($rows[0]->c ?? 0)) > 0;
        }

        if (!$hasForeignKey) {
            Schema::table('news', function (Blueprint $table) {
                $table->foreign('category_id')
                      ->references('id')
                      ->on('categories')
                      ->nullOnDelete();
            });
        }
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
/* Dikembangkan oleh Institut Teknologi Del */
