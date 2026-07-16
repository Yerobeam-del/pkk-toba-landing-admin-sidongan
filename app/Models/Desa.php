<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    protected $fillable = [
        'kecamatan_id', 'name', 'kode_wilayah', 'description', 'image', 
        'population', 'households', 'sort_order', 'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean'
    ];
    
    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }
    
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}