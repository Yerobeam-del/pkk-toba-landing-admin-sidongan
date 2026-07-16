<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class HeroSlider extends Model
{
    protected $fillable = [
        'title', 'description', 'button_text', 'button_url',
        'image_path', 'image_alt', 'sort_order', 'is_active', 'display_duration'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'display_duration' => 'integer'
    ];
    
    public function imageUrl(): Attribute {
        return Attribute::make(
            get: fn () => $this->image_path ? asset('storage/' . $this->image_path) : null,
        );
    }
    
    public function scopeActive($query) {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}