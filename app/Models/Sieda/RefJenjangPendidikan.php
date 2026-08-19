<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Models\Sieda;

class RefJenjangPendidikan extends BaseSiedaModel
{
    protected $table = 'ref_jenjang_pendidikan';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $fillable = ['nama', 'active'];

    public static function moduleName(): string { return 'Referensi Jenjang Pendidikan'; }
    public static function primaryKey(): string { return 'id'; }
}
/* Dikembangkan oleh Institut Teknologi Del */
