<?php

namespace App\Models\Sieda;

class RefStatusPerkawinan extends BaseSiedaModel
{
    protected $table = 'ref_status_perkawinan';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $fillable = ['nama', 'active'];

    public static function moduleName(): string { return 'Referensi Status Perkawinan'; }
    public static function primaryKey(): string { return 'id'; }
}
