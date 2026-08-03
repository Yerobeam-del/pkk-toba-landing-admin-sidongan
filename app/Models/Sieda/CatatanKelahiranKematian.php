<?php

namespace App\Models\Sieda;

class CatatanKelahiranKematian extends BaseSiedaModel
{
    protected $table = 'catatan_kelahiran_kematian';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'id_warga_ibu', 'id_warga_suami', 'no_kk', 'id_group_dasawisma',
        'status_ibu', 'bulan_hamil', 'tanggal_hamil', 'tanggal_melahirkan',
        'tanggal_nifas_selesai', 'nama_bayi', 'jenis_kelamin_bayi',
        'tanggal_lahir_bayi', 'akte_kelahiran', 'no_akte_kelahiran',
        'status_kematian', 'nama_meninggal', 'jenis_kelamin_meninggal',
        'tanggal_meninggal', 'sebab_meninggal', 'keterangan', 'config_year', 'active',
    ];

    public static function primaryKey(): string
    {
        return 'id';
    }

    public static function moduleName(): string
    {
        return 'Catatan Ibu & Anak (Kelahiran / Kematian)';
    }

    public function ibu()
    {
        return $this->belongsTo(Warga::class, 'id_warga_ibu', 'nik');
    }

    public function suami()
    {
        return $this->belongsTo(Warga::class, 'id_warga_suami', 'nik');
    }

    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class, 'no_kk', 'no_kk');
    }

    public function kelompokDasawisma()
    {
        return $this->belongsTo(KelompokDasawisma::class, 'id_group_dasawisma');
    }
}
