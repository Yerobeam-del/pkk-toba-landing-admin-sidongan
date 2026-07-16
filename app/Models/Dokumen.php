<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Dokumen extends Model
{
    protected $fillable = [
        'name',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'document_date',
        'status',
        'sort_order'
    ];

    protected $casts = [
        'document_date' => 'date',
        'status' => 'string'
    ];

    // Scope untuk hanya ambil yang published
    public function scopePublished($query)
    {
        return $query->where('status', 'published')->orderBy('document_date', 'desc');
    }

    // Accessor untuk URL file
    public function fileUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->file_path ? asset('storage/' . $this->file_path) : null,
        );
    }

    // Format tanggal untuk display
    public function getFormattedDateAttribute()
    {
        return $this->document_date?->format('d F Y');
    }
}