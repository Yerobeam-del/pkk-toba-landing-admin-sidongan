<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sidongan_documents';

    // Field yang digunakan di form
    protected $fillable = [
        'title',
        'slug',
        'description',
        'sender',              // Pengirim surat
        'document_number',     // Nomor surat dari pengirim
        'agenda_number',       // Nomor agenda internal (AG/MM/YYYY/NNN)
        'document_date',       // Tanggal surat
        'subject',             // Perihal surat
        'suggestion',          // Saran sekretaris
        'status',
        'category_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'is_public',
        'metadata',
        'disposisi_data',      // Data disposisi (JSON)
        'verifikasi_data',     // Data verifikasi (JSON)
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'document_date' => 'date',
        'is_public' => 'boolean',
        'metadata' => 'array',
        'disposisi_data' => 'array',
        'verifikasi_data' => 'array',
        'file_size' => 'integer',
    ];

    // Auto-generate slug saat title diubah
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = Str::slug($value . '-' . time());
    }

    // Format ukuran file (KB, MB, GB)
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    // URL file untuk akses publik
    public function getFileUrlAttribute()
    {
        return Storage::disk('public')->url($this->file_path);
    }

    // Relasi
    public function category()
    {
        return $this->belongsTo(DocumentCategory::class, 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(DocumentTag::class, 'sidongan_document_tag', 'document_id', 'tag_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function activityReports()
    {
        return $this->hasMany(ActivityReport::class, 'document_id');
    }

    // Scope: Hanya dokumen published & public
    public function scopePublished($query)
    {
        return $query->where('status', 'published')->where('is_public', true);
    }

    // Scope: Search
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function($q) use ($keyword) {
            $q->where('title', 'LIKE', "%{$keyword}%")
            ->orWhere('description', 'LIKE', "%{$keyword}%")
            ->orWhere('document_number', 'LIKE', "%{$keyword}%")
            ->orWhere('agenda_number', 'LIKE', "%{$keyword}%")
            ->orWhere('sender', 'LIKE', "%{$keyword}%")
            ->orWhere('subject', 'LIKE', "%{$keyword}%");
        });
    }

    // AUTO-GENERATE: Slug & Agenda Number saat dokumen dibuat
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($document) {
            // Generate slug jika kosong
            if (empty($document->slug)) {
                $document->slug = Str::slug($document->title . '-' . time());
            }
            
            // Generate agenda number jika kosong
            if (empty($document->agenda_number)) {
                $document->agenda_number = self::generateAgendaNumber();
            }
        });
    }

    /**
     * Generate nomor agenda otomatis
     * Format BARU: NNN/SM/PKK-T/BULAN(ROMAWI)/TAHUN
     * Contoh: 001/SM/PKK-T/VI/2026
     * Reset nomor urut setiap ganti bulan
     */
    public static function generateAgendaNumber()
    {
        $month = now()->month; // 1-12
        $year = now()->year;   // 2026
        
        // Cari dokumen terakhir di bulan & tahun ini untuk dapat nomor urut terakhir
        $lastDocument = self::withTrashed()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->whereNotNull('agenda_number')
            ->orderBy('id', 'desc')
            ->first();
        
        // Hitung nomor urut berikutnya
        $nextSequence = 1;
        
        if ($lastDocument && $lastDocument->agenda_number) {
            // Parse format baru: 001/SM/PKK-T/VI/2026
            // Ambil bagian pertama (nomor urut)
            $parts = explode('/', $lastDocument->agenda_number);
            if (!empty($parts[0])) {
                $nextSequence = intval($parts[0]) + 1;
            }
        }
        
        // Format nomor urut dengan 3 digit
        $sequence = str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
        
        // Konversi bulan ke Romawi
        $bulanRomawi = self::toRomanMonth($month);
        
        // Format final: 001/SM/PKK-T/VI/2026
        $agendaNumber = "{$sequence}/SM/PKK-T/{$bulanRomawi}/{$year}";
        
        // Pastikan nomor agenda unik (loop sampai dapat yang belum dipakai)
        while (self::withTrashed()->where('agenda_number', $agendaNumber)->exists()) {
            $sequence = str_pad(intval($sequence) + 1, 3, '0', STR_PAD_LEFT);
            $agendaNumber = "{$sequence}/SM/PKK-T/{$bulanRomawi}/{$year}";
        }
        
        return $agendaNumber;
    }

    /**
     * Helper: Ubah angka bulan (1-12) ke Romawi
     */
    private static function toRomanMonth($month)
    {
        $roman = [
            1 => 'I',   2 => 'II',  3 => 'III', 4 => 'IV',
            5 => 'V',   6 => 'VI',  7 => 'VII', 8 => 'VIII',
            9 => 'IX',  10 => 'X',  11 => 'XI',  12 => 'XII'
        ];
        return $roman[$month] ?? 'I';
    }

    /**
     * Cek apakah semua disposisi sudah memberikan laporan
     */
    public function allDispositionsReported()
    {
        $disposisiData = $this->disposisi_data;
        
        \Log::info("=== CHECK allDispositionsReported ===");
        \Log::info("Document ID: {$this->id}");
        \Log::info("Raw disposisi_data:", ['data' => $disposisiData]);
        
        // Handle double-encoded JSON (data lama yang salah)
        if (is_array($disposisiData) && isset($disposisiData['data']) && is_string($disposisiData['data'])) {
            \Log::info("Detected double-encoded JSON, decoding...");
            $decoded = json_decode($disposisiData['data'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $disposisiData = $decoded;
                \Log::info("Decoded disposisi_data:", ['data' => $disposisiData]);
            }
        }
        
        if (!is_array($disposisiData) || !isset($disposisiData['target_roles'])) {
            \Log::info("No target_roles found, returning true");
            return true;
        }
        
        $targetRoles = $disposisiData['target_roles'];
        \Log::info("Target roles:", ['roles' => $targetRoles]);
        
        if (empty($targetRoles)) {
            \Log::info("Empty target_roles, returning true");
            return true;
        }
        
        // Ambil SEMUA user dengan role target
        $targetUsers = \App\Models\User::whereIn('sidongan_role', $targetRoles)->get();
        
        \Log::info("Target users found:", [
            'count' => $targetUsers->count(),
            'users' => $targetUsers->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'role' => $u->sidongan_role
            ])
        ]);
        
        if ($targetUsers->isEmpty()) {
            \Log::info("No users found with target roles, returning true");
            return true;
        }
        
        // Cek apakah SEMUA user target sudah lapor
        $allReported = true;
        foreach ($targetUsers as $user) {
            $hasReported = \App\Models\ActivityReport::where('document_id', $this->id)
                ->where('created_by', $user->id)
                ->exists();
            
            \Log::info("User report status:", [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->sidongan_role,
                'has_reported' => $hasReported ? 'YES' : 'NO'
            ]);
            
            if (!$hasReported) {
                $allReported = false;
                \Log::info("User {$user->name} ({$user->sidongan_role}) has NOT reported yet!");
            }
        }
        
        \Log::info("All dispositions reported: " . ($allReported ? 'YES' : 'NO'));
        return $allReported;
    }

    /**
     * Update status dokumen berdasarkan laporan dan verifikasi
     */
    public function updateStatusBasedOnReports()
    {
        return $this->updateCorrectStatus();
    }

    /**
     * Hitung jumlah user yang sudah lapor
     */
    public function getReportedCount()
    {
        $disposisiData = $this->disposisi_data;
        
        if (!is_array($disposisiData) || !isset($disposisiData['target_roles'])) {
            return 0;
        }
        
        $targetRoles = $disposisiData['target_roles'];
        $targetUsers = \App\Models\User::whereIn('sidongan_role', $targetRoles)->get();
        
        $reportedCount = 0;
        foreach ($targetUsers as $user) {
            $hasReported = \App\Models\ActivityReport::where('document_id', $this->id)
                ->where('created_by', $user->id)
                ->exists();
            
            if ($hasReported) {
                $reportedCount++;
            }
        }
        
        return $reportedCount;
    }

    /**
     * Hitung total user yang harus lapor
     */
    public function getTotalRequiredReports()
    {
        $disposisiData = $this->disposisi_data;
        
        if (!is_array($disposisiData) || !isset($disposisiData['target_roles'])) {
            return 0;
        }
        
        $targetRoles = $disposisiData['target_roles'];
        return \App\Models\User::whereIn('sidongan_role', $targetRoles)->count();
    }

    /**
     * Cek apakah semua laporan sudah diverifikasi (disetujui)
     */
    public function allReportsVerified()
    {
        $disposisiData = $this->disposisi_data;
        
        // Handle double-encoded JSON (data lama)
        if (is_array($disposisiData) && isset($disposisiData['data']) && is_string($disposisiData['data'])) {
            $decoded = json_decode($disposisiData['data'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $disposisiData = $decoded;
            }
        }
        
        if (!is_array($disposisiData) || !isset($disposisiData['target_roles'])) {
            return true;
        }
        
        $targetRoles = $disposisiData['target_roles'];
        $targetUsers = \App\Models\User::whereIn('sidongan_role', $targetRoles)->get();
        
        if ($targetUsers->isEmpty()) {
            return true;
        }
        
        foreach ($targetUsers as $user) {
            $report = \App\Models\ActivityReport::where('document_id', $this->id)
                ->where('created_by', $user->id)
                ->first();
            
            if (!$report || $report->status !== 'disetujui') {
                return false;
            }
        }
        
        return true;
    }

    public function updateCorrectStatus()
    {
        \Log::info("=== UPDATE STATUS DOCUMENT {$this->id} ===");
        
        // Cek apakah semua disposisi sudah lapor
        $allReported = $this->allDispositionsReported();
        \Log::info("All Dispositions Reported: " . ($allReported ? 'YES' : 'NO'));
        
        if (!$allReported) {
            $this->update(['status' => 'berjalan']);
            \Log::info("Status updated to: berjalan");
            return 'berjalan';
        }
        
        // Semua sudah lapor, cek status laporan
        $reports = $this->activityReports;
        $hasRejected = $reports->where('status', 'ditolak')->count() > 0;
        $hasApproved = $reports->where('status', 'disetujui')->count() > 0;
        $hasPending = $reports->where('status', 'menunggu_verifikasi')->count() > 0;
        
        \Log::info("Report status - Rejected: " . ($hasRejected ? 'YES' : 'NO') . 
                ", Approved: " . ($hasApproved ? 'YES' : 'NO') . 
                ", Pending: " . ($hasPending ? 'YES' : 'NO'));
        
        // JIKA ADA LAPORAN YANG DITOLAK → Status tetap BERJALAN (bisa lapor ulang)
        if ($hasRejected) {
            $this->update(['status' => 'berjalan']);
            \Log::info("Status updated to: berjalan (ada laporan ditolak)");
            return 'berjalan';
        }
        
        // Jika ada yang menunggu verifikasi
        if ($hasPending) {
            $this->update(['status' => 'menunggu_verifikasi']);
            \Log::info("Status updated to: menunggu_verifikasi");
            return 'menunggu_verifikasi';
        }
        
        // Semua disetujui → Selesai
        $this->update(['status' => 'selesai']);
        \Log::info("Status updated to: selesai");
        return 'selesai';
    }

    /**
     * Hitung jumlah laporan yang sudah diverifikasi
     */
    public function getVerifiedReportsCount()
    {
        return $this->activityReports()
            ->where('status', 'disetujui')
            ->count();
    }

    /**
     * Hitung jumlah laporan yang menunggu verifikasi
     */
    public function getPendingReportsCount()
    {
        return $this->activityReports()
            ->where('status', 'menunggu_verifikasi')
            ->count();
    }
}