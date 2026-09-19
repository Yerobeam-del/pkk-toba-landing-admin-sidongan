<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Satu baris riwayat aktivitas Surat Keluar (audit trail).
 * Dikembangkan oleh Institut Teknologi Del.
 */
class OutgoingLetterActivity extends Model
{
    protected $table = 'sidongan_outgoing_letter_activities';

    protected $fillable = ['outgoing_letter_id', 'user_id', 'action', 'description'];

    public function letter()
    {
        return $this->belongsTo(OutgoingLetter::class, 'outgoing_letter_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Catat satu aktivitas. $context dipakai untuk menyusun
     * deskripsi yang informatif (mis. nomor yang diterbitkan).
     */
    public static function record(OutgoingLetter $letter, string $action, string $description, ?int $userId = null): self
    {
        return static::create([
            'outgoing_letter_id' => $letter->id,
            'user_id' => $userId ?? auth('sidongan')->id() ?? auth()->id(),
            'action' => $action,
            'description' => $description,
        ]);
    }

    /** Konfigurasi tampilan timeline per jenis aksi. */
    public static function display(string $action): array
    {
        return match ($action) {
            'created' => ['icon' => 'fas fa-inbox', 'color' => 'so-timeline-icon-blue', 'role' => 'Sekretaris PKK'],
            'updated' => ['icon' => 'fas fa-edit', 'color' => 'so-timeline-icon-blue', 'role' => 'Sekretaris PKK'],
            'word_imported' => ['icon' => 'fas fa-file-word', 'color' => 'so-timeline-icon-blue', 'role' => 'Sekretaris PKK'],
            'attachment_added' => ['icon' => 'fas fa-paperclip', 'color' => 'so-timeline-icon-blue', 'role' => 'Sekretaris PKK'],
            'attachment_removed' => ['icon' => 'fas fa-paperclip', 'color' => 'so-timeline-icon-slate', 'role' => 'Sekretaris PKK'],
            'submitted' => ['icon' => 'fas fa-paper-plane', 'color' => 'so-timeline-icon-cyan', 'role' => 'Sekretaris PKK'],
            'approved' => ['icon' => 'fas fa-check', 'color' => 'so-timeline-icon-green', 'role' => 'Ketua PKK'],
            'revision_requested' => ['icon' => 'fas fa-rotate-left', 'color' => 'so-timeline-icon-amber', 'role' => 'Ketua PKK'],
            'sent' => ['icon' => 'fas fa-envelope-open-text', 'color' => 'so-timeline-icon-slate', 'role' => 'Sekretaris PKK'],
            'archived' => ['icon' => 'fas fa-archive', 'color' => 'so-timeline-icon-purple', 'role' => 'Sekretaris PKK'],
            default => ['icon' => 'fas fa-history', 'color' => 'so-timeline-icon-slate', 'role' => 'Sekretaris PKK'],
        };
    }
}
