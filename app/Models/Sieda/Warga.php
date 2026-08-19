<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Models\Sieda;

class Warga extends BaseSiedaModel
{
    protected $table = 'tp_pkk_warga';
    protected $primaryKey = 'nik';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nik', 'no_registrasi', 'nama', 'tempat_lahir', 'tanggal_lahir',
        'jenis_kelamin', 'id_agama', 'id_jenjang_pendidikan', 'id_pekerjaan',
        'id_status_perkawinan', 'no_hp', 'id_peran_keluarga', 'id_status_keluarga',
        'alamat', 'status_akseptor', 'status_posyandu', 'status_pbkb',
        'status_tabungan', 'status_kelompok_belajar', 'status_paud',
        'status_kegiatan_koperasi', 'status_kebutuhan_khusus', 'id_jabatan_tp_pkk',
    ];

    public static function primaryKey(): string
    {
        return 'nik';
    }

    public static function moduleName(): string
    {
        return 'Data Warga / Penduduk';
    }

    public function agama()
    {
        return $this->belongsTo(RefAgama::class, 'id_agama');
    }

    public function pendidikan()
    {
        return $this->belongsTo(RefJenjangPendidikan::class, 'id_jenjang_pendidikan');
    }

    public function pekerjaan()
    {
        return $this->belongsTo(RefPekerjaan::class, 'id_pekerjaan');
    }

    public function statusKeluarga()
    {
        return $this->belongsTo(RefStatusKeluarga::class, 'id_status_keluarga');
    }

    public function statusPerkawinan()
    {
        return $this->belongsTo(RefStatusPerkawinan::class, 'id_status_perkawinan');
    }

    public function anggotaKeluarga()
    {
        return $this->hasMany(AnggotaKeluarga::class, 'nik', 'nik');
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
