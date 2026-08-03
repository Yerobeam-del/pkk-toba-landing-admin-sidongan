<?php

namespace App\Models\Sieda;

class RefDusun extends BaseSiedaModel
{
    protected $table = 'ref_dusun';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = ['nama', 'active'];

    public static function moduleName(): string
    {
        return 'Referensi Dusun';
    }

    public static function primaryKey(): string
    {
        return 'id';
    }
}
