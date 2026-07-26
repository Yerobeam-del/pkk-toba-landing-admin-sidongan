<?php

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
    // Laporan dimiliki ROLE, bukan perorangan:
    //   - boleh MENGUBAH : sesama anggota role pemilik laporan
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

    /** Boleh mengubah: hanya anggota role pemilik laporan. */
    public function bolehDiubahOleh(?User $user): bool
    {
        if (!$user || empty($user->sidongan_role)) {
            return false;
        }

        // Laporan lama yang belum punya role: jatuhkan ke aturan lama (pembuatnya).
        if (empty($this->role)) {
            return $this->created_by === $user->id;
        }

        return $this->role === $user->sidongan_role;
    }

    /** Boleh melihat: pemilik role, seluruh role tujuan disposisi, dan Ketua. */
    public function bolehDilihatOleh(?User $user): bool
    {
        if (!$user || empty($user->sidongan_role)) {
            return false;
        }

        if (in_array($user->sidongan_role, ['ketua', 'super_admin'], true)) {
            return true;
        }

        if ($this->bolehDiubahOleh($user)) {
            return true;
        }

        return in_array($user->sidongan_role, $this->peranTujuanSurat(), true);
    }
}