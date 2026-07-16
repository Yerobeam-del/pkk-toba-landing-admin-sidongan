<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $fillable = [
        'name',
        'kode_wilayah',   // Contoh: '12.12.01'
        'kabupaten_kode'  // Contoh: '12.12'
    ];
    
    public function desas()
    {
        return $this->hasMany(Desa::class)->orderBy('sort_order');
    }
    
    public function activeDesas()
    {
        return $this->hasMany(Desa::class)->where('is_active', true)->orderBy('sort_order');
    }
}