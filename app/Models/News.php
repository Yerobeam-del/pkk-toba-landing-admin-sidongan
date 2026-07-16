<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends Model
{
    use SoftDeletes;

    protected $table = 'news';
    
    protected $fillable = [
        'title', 
        'slug', 
        'category',      // ← TAMBAHKAN INI (string dari form)
        'category_id',   // ← Biarkan juga untuk migrasi nanti
        'excerpt', 
        'content', 
        'image_path', 
        'published_at', 
        'is_published'
    ];
    
    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->where(function ($q) {
                        $q->whereNull('published_at')
                        ->orWhere('published_at', '<=', now());
                    })
                    ->orderByDesc('published_at');
    }

    // TAMBAHKAN RELASI INI
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}