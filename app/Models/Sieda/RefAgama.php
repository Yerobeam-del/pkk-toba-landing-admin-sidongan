<?php

namespace App\Models\Sieda;

class RefAgama extends BaseSiedaModel
{
    protected $table = 'ref_agama';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $fillable = ['nama', 'active'];

    public static function moduleName(): string { return 'Referensi Agama'; }
    public static function primaryKey(): string { return 'id'; }
}
