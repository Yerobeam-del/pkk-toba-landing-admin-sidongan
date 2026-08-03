<?php

namespace App\Models\Sieda;

class RefStatusKeluarga extends BaseSiedaModel
{
    protected $table = 'ref_status_keluarga';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $fillable = ['nama', 'active'];

    public static function moduleName(): string { return 'Referensi Status Keluarga'; }
    public static function primaryKey(): string { return 'id'; }
}
