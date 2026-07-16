<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DocumentTag extends Model
{
    use HasFactory;

    protected $table = 'sidongan_tags';
    protected $fillable = ['name', 'slug'];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function documents()
    {
        return $this->belongsToMany(Document::class, 'sidongan_document_tag', 'tag_id', 'document_id');
    }
}