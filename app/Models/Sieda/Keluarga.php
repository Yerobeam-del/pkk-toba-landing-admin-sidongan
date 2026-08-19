<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Models\Sieda;

class Keluarga extends BaseSiedaModel
{
    protected $table = 'tp_pkk_keluarga';
    protected $primaryKey = 'no_kk';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_kk', 'id_kepala_keluarga', 'id_kelompok_dasawisma',
        'desa_id', 'config_year', 'no_registrasi_keluarga', 'kode_desa',
    ];

    public static function primaryKey(): string
    {
        return 'no_kk';
    }

    public static function moduleName(): string
    {
        return 'Data Keluarga';
    }

    public function kepalaKeluarga()
    {
        return $this->belongsTo(Warga::class, 'id_kepala_keluarga', 'nik');
    }

    public function kelompokDasawisma()
    {
        return $this->belongsTo(KelompokDasawisma::class, 'id_kelompok_dasawisma');
    }

    public function anggota()
    {
        return $this->hasMany(AnggotaKeluarga::class, 'no_kk', 'no_kk');
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
