<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    protected $table = 'wilayah';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'kode';
    
    protected $fillable = ['kode', 'nama'];
    
    public function scopeProvinsi($query)
    {
        return $query->where('kode', 'like', '__')
                     ->where('kode', 'not like', '%.%');
    }
    
    public function scopeKabupaten($query, $provinceCode = null)
    {
        $query->where('kode', 'like', '__.__')
              ->where('kode', 'not like', '%.____');
        
        if ($provinceCode) {
            $query->where('kode', 'like', $provinceCode . '.%');
        }
        
        return $query;
    }
    
    public function scopeKecamatan($query, $regencyCode = null)
    {
        $query->where('kode', 'like', '__.__.__')
              ->where('kode', 'not like', '%.____');
        
        if ($regencyCode) {
            $query->where('kode', 'like', $regencyCode . '.%');
        }
        
        return $query;
    }
    
    public function scopeKelurahan($query, $districtCode = null)
    {
        $query->where('kode', 'like', '__.__.__.____');
        
        if ($districtCode) {
            $query->where('kode', 'like', $districtCode . '.%');
        }
        
        return $query;
    }
}