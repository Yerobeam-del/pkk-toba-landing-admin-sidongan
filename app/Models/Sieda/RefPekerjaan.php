<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Models\Sieda;

class RefPekerjaan extends BaseSiedaModel
{
    protected $table = 'ref_pekerjaan';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $fillable = ['nama', 'active'];

    public static function moduleName(): string { return 'Referensi Pekerjaan'; }
    public static function primaryKey(): string { return 'id'; }
}
/* Dikembangkan oleh Institut Teknologi Del */
