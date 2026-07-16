<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class StrukturMember extends Model
{
    protected $table = 'struktur_members';
    
    protected $fillable = [
        'name', 'position', 'pokja_id', 'photo_path', 
        'description', 'sort_order', 'is_active'
    ];
    
    protected $casts = ['is_active' => 'boolean'];
    
    // Accessor untuk URL foto
    public function photoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->photo_path 
                ? asset('storage/' . $this->photo_path) 
                : asset('assets/landing/images/struktur/default-avatar.png'),
        );
    }
    
    public function pokja()
    {
        return $this->belongsTo(Pokja::class);
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}