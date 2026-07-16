<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pokja extends Model
{
    protected $table = 'pokja';
    
    protected $fillable = ['name', 'slug', 'description', 'sort_order', 'is_active'];
    
    protected $casts = ['is_active' => 'boolean'];
    
    public function members(): HasMany
    {
        return $this->hasMany(StrukturMember::class)->orderBy('sort_order');
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}