<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityReport extends Model
{
    use HasFactory;

    protected $table = 'activity_reports';

    protected $fillable = [
        'document_id',
        'role',        // pemilik laporan: satu laporan per surat per role
        'created_by',  // catatan siapa yang pertama membuat
        'kegiatan_nama',
        'kegiatan_tanggal',
        'start_time',
        'end_time',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'kelurahan',
        'alamat_lengkap',
        'deskripsi',
        'fotos',
        'status',
        'catatan_verifikasi',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'kegiatan_tanggal' => 'date',
        'fotos' => 'array',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ==========================================================
    // ATURAN AKSES LAPORAN BERSAMA
    // ==========================================================
    // Satu surat = SATU laporan, dikerjakan bersama oleh semua role tujuan
    // disposisi (bukan perorangan, bukan per role):
    //   - boleh MENGUBAH : semua role tujuan disposisi surat itu
    //   - boleh MELIHAT  : semua role tujuan disposisi surat itu, plus Ketua
    // Ditaruh di model agar aturannya satu sumber, tidak tersebar di controller.

    /** Peran tujuan disposisi pada surat induk laporan ini. */
    public function peranTujuanSurat(): array
    {
        $dokumen = $this->document;
        if (!$dokumen || !$dokumen->disposisi_data) {
            return [];
        }

        $data = is_string($dokumen->disposisi_data)
            ? json_decode($dokumen->disposisi_data, true)
            : $dokumen->disposisi_data;

        return $data['target_roles'] ?? [];
    }

    /** Boleh mengubah: semua role tujuan disposisi surat (laporan milik surat), dan Super Admin. */
    public function bolehDiubahOleh(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        // Super Admin: akses penuh (konsisten dengan bolehDilihatOleh)
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (empty($user->sidongan_role)) {
            return false;
        }

        // Laporan lama yang belum punya role: jatuhkan ke aturan lama (pembuatnya).
        if (empty($this->role)) {
            return $this->created_by === $user->id;
        }

        return in_array($user->sidongan_role, $this->peranTujuanSurat(), true);
    }

    /** Boleh melihat: pemilik role, seluruh role tujuan disposisi, dan Ketua. */
    public function bolehDilihatOleh(?User $user): bool
    {
        if (!$user || empty($user->sidongan_role)) {
            return false;
        }

        if ($user->isSuperAdmin() || $user->hasSidonganRole('ketua')) {
            return true;
        }

        if ($this->bolehDiubahOleh($user)) {
            return true;
        }

        return in_array($user->sidongan_role, $this->peranTujuanSurat(), true);
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
