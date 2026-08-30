<?php

/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsImage extends Model
{
    protected $table = 'news_images';

    protected $fillable = [
        'news_id',
        'image_path',
        'caption',
        'sort_order',
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
