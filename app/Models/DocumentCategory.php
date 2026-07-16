<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DocumentCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sidongan_categories'; // Pastikan nama tabel sesuai migration
    protected $fillable = ['name', 'slug', 'description', 'color', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'category_id');
    }
}