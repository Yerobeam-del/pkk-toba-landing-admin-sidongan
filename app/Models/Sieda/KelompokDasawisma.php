<?php

namespace App\Models\Sieda;

class KelompokDasawisma extends BaseSiedaModel
{
    protected $table = 'tp_pkk_group_dasawisma';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'nama', 'kader', 'id_dusun', 'kode_desa', 'desa_id', 'config_year', 'active',
    ];

    public static function primaryKey(): string
    {
        return 'id';
    }

    public static function moduleName(): string
    {
        return 'Kelompok Dasawisma';
    }

    public function dusun()
    {
        return $this->belongsTo(RefDusun::class, 'id_dusun');
    }

    public function keluarga()
    {
        return $this->hasMany(Keluarga::class, 'id_kelompok_dasawisma', 'id');
    }
}
