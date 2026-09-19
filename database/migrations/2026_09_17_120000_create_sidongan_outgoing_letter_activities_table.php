<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Audit trail Surat Keluar: mencatat setiap aktivitas (siapa melakukan
 * apa dan kapan) agar riwayat tetap utuh meski kolom status berubah,
 * dan bisa menerima jenis aktivitas baru tanpa mengubah skema.
 *
 * Data lama di-backfill dari kolom timestamp yang sudah ada.
 * Dikembangkan oleh Institut Teknologi Del.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sidongan_outgoing_letter_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outgoing_letter_id')->constrained('sidongan_outgoing_letters')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 50);
            $table->string('description', 500)->nullable();
            $table->timestamps();

            $table->index(['outgoing_letter_id', 'created_at']);
        });

        // Backfill riwayat surat yang dibuat sebelum fitur ini ada,
        // dari kolom timestamp workflow yang sudah terisi.
        $statuses = \DB::table('sidongan_outgoing_letters')
            ->select('id', 'created_by', 'approved_by', 'submitted_at', 'approved_at', 'sent_at', 'archived_at', 'outgoing_number', 'created_at')
            ->get();

        $rows = [];
        $now = now();
        foreach ($statuses as $l) {
            if ($l->created_at) {
                $rows[] = ['outgoing_letter_id' => $l->id, 'user_id' => $l->created_by, 'action' => 'created', 'description' => 'Membuat draft Surat Keluar', 'created_at' => $l->created_at, 'updated_at' => $now];
            }
            if ($l->submitted_at) {
                $rows[] = ['outgoing_letter_id' => $l->id, 'user_id' => $l->created_by, 'action' => 'submitted', 'description' => 'Mengajukan surat kepada Ketua PKK untuk persetujuan', 'created_at' => $l->submitted_at, 'updated_at' => $now];
            }
            if ($l->approved_at) {
                $rows[] = ['outgoing_letter_id' => $l->id, 'user_id' => $l->approved_by, 'action' => 'approved', 'description' => 'Menyetujui surat dan menerbitkan nomor ' . ($l->outgoing_number ?? '-'), 'created_at' => $l->approved_at, 'updated_at' => $now];
            }
            if ($l->sent_at) {
                $rows[] = ['outgoing_letter_id' => $l->id, 'user_id' => $l->created_by, 'action' => 'sent', 'description' => 'Menandai surat sebagai sudah dikirim', 'created_at' => $l->sent_at, 'updated_at' => $now];
            }
            if ($l->archived_at) {
                $rows[] = ['outgoing_letter_id' => $l->id, 'user_id' => $l->created_by, 'action' => 'archived', 'description' => 'Mengarsipkan Surat Keluar', 'created_at' => $l->archived_at, 'updated_at' => $now];
            }
        }

        if ($rows) {
            \DB::table('sidongan_outgoing_letter_activities')->insert($rows);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sidongan_outgoing_letter_activities');
    }
};
