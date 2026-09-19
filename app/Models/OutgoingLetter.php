<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Support\ImageUploadSanitizer;

class OutgoingLetter extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sidongan_outgoing_letters';

    protected $fillable = [
        'incoming_document_id', 'created_by', 'approved_by', 'outgoing_number',
        'recipient', 'recipient_address', 'subject', 'body', 'nature',
        'attachment_description', 'attachment_path', 'attachment_name',
        'attachment_type', 'attachment_size', 'cc', 'letter_date',
        'signatory_name', 'signatory_title', 'status', 'revision_note',
        'submitted_at', 'approved_at', 'sent_at', 'archived_at', 'metadata',
    ];

    protected $casts = [
        'letter_date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'sent_at' => 'datetime',
        'archived_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function incomingDocument()
    {
        return $this->belongsTo(Document::class, 'incoming_document_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function activities()
    {
        return $this->hasMany(OutgoingLetterActivity::class, 'outgoing_letter_id')->orderBy('created_at')->orderBy('id');
    }

    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'perlu_revisi'], true);
    }

    public function hasAttachment(): bool
    {
        return !empty($this->attachment_path);
    }

    /**
     * Simpan file lampiran ke disk 'public'. Isi file diverifikasi benar-
     * benar PDF lewat magic bytes — nama kiriman klien tidak dipercaya.
     * Hanya PDF yang boleh dilampirkan karena lampiran digabungkan ke
     * PDF final saat cetak. Return false bila file ditolak.
     */
    public function storeAttachment(UploadedFile $file): bool
    {
        if (!function_exists('finfo_open')) {
            return false; // ekstensi fileinfo tidak ada → gagal aman (fail-closed)
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file->getRealPath());
        if ($mime !== 'application/pdf') {
            return false;
        }

        $this->deleteAttachmentFile();

        $filename = time() . '_' . ImageUploadSanitizer::safeBaseName($file->getClientOriginalName()) . '.pdf';
        $path = $file->storeAs('sidongan/outgoing-attachments', $filename, 'public');

        $this->forceFill([
            'attachment_path' => $path,
            'attachment_name' => $file->getClientOriginalName(),
            'attachment_type' => $mime,
            'attachment_size' => $file->getSize(),
        ])->save();

        return true;
    }

    public function deleteAttachmentFile(): void
    {
        if ($this->attachment_path && Storage::disk('public')->exists($this->attachment_path)) {
            Storage::disk('public')->delete($this->attachment_path);
        }
    }

    public function attachmentSizeHuman(): string
    {
        if (!$this->attachment_size) {
            return 'File lampiran';
        }

        return round($this->attachment_size / 1024, 2) . ' KB';
    }

    public static function generateNumber($date = null): string
    {
        $date = $date ? \Carbon\Carbon::parse($date) : now();
        $last = static::withTrashed()->whereYear('letter_date', $date->year)
            ->whereMonth('letter_date', $date->month)
            ->whereNotNull('outgoing_number')->latest('id')->first();
        $sequence = $last ? ((int) explode('/', $last->outgoing_number)[0] + 1) : 1;
        $roman = [1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'VI',7=>'VII',8=>'VIII',9=>'IX',10=>'X',11=>'XI',12=>'XII'][$date->month];
        do {
            $number = sprintf('%03d/SKR/PKK-T/%s/%d', $sequence++, $roman, $date->year);
        } while (static::withTrashed()->where('outgoing_number', $number)->exists());
        return $number;
    }
}
