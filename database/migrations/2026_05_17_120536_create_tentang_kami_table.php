<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tentang_kami', function (Blueprint $table) {
            $table->id();
            $table->string('judul')->default('Tentang Kami');
            $table->string('subjudul')->default('Informasi tentang PKK Kabupaten Toba');
            $table->string('heading')->default('Memberdayakan Keluarga, Mensejahterakan Masyarakat');
            $table->text('deskripsi');
            $table->json('program_list'); // Array of strings
            $table->text('maps_embed_code');
            $table->string('maps_link')->nullable();
            $table->timestamps();
        });
        
        // Insert default data
        DB::table('tentang_kami')->insert([
            'judul' => 'Tentang Kami',
            'subjudul' => 'Informasi tentang PKK Kabupaten Toba',
            'heading' => 'Memberdayakan Keluarga, Mensejahterakan Masyarakat',
            'deskripsi' => 'PKK Kabupaten Toba berkomitmen untuk terus berinovasi dalam memberikan pelayanan terbaik kepada masyarakat melalui transformasi digital dan program-program pemberdayaan keluarga.',
            'program_list' => json_encode([
                'Program ketahanan dan kesejahteraan keluarga',
                'Peningkatan peran perempuan dalam pembangunan',
                'Pemberdayaan ekonomi masyarakat desa',
                'Digitalisasi layanan masyarakat terpadu'
            ]),
            'maps_embed_code' => '<iframe src="https://www.google.com/maps/embed?pb=..." width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
            'maps_link' => 'https://goo.gl/maps/xxx'
        ]);
    }
    
    public function down(): void {
        Schema::dropIfExists('tentang_kami');
    }
};