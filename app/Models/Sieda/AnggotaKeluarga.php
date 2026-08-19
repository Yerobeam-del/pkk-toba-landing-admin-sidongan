<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Models\Sieda;

class AnggotaKeluarga extends BaseSiedaModel
{
    protected $table = 'tp_pkk_anggota_keluarga';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'no_kk', 'nik', 'active', 'created_by', 'updated_by',
    ];

    public static function primaryKey(): string
    {
        return 'id';
    }

    public static function moduleName(): string
    {
        return 'Anggota Keluarga';
    }

    public function warga()
    {
        return $this->belongsTo(Warga::class, 'nik', 'nik');
    }

    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class, 'no_kk', 'no_kk');
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
