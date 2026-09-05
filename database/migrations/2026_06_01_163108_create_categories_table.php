<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Guard tambahan: migrasi sebelumnya (2026_06_01_161400_...) sudah
        // membuat tabel categories dasar beserta kolom category_id di news.
        // Tanpa guard ini, migrate:fresh / database test baru akan gagal
        // "table categories already exists" karena urutan file memanggil
        // 161400 lebih dulu.
        if (Schema::hasTable('categories')) {
            // Augment skema bila kolom full belum ada (dibuat versi dasar oleh 161400)
            Schema::table('categories', function (Blueprint $table) {
                if (!Schema::hasColumn('categories', 'color')) {
                    $table->string('color')->nullable()->after('description'); // Untuk warna badge
                }
                if (!Schema::hasColumn('categories', 'sort_order')) {
                    $table->integer('sort_order')->default(0)->after('color');
                }
                if (!Schema::hasColumn('categories', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('sort_order');
                }
            });

            return;
        }

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color')->nullable(); // Untuk warna badge
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
/* Dikembangkan oleh Institut Teknologi Del */
