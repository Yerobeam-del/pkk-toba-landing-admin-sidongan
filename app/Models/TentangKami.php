<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TentangKami extends Model
{
    protected $table = 'tentang_kami';
    
    protected $fillable = [
        'judul', 'subjudul', 'heading', 'deskripsi', 
        'program_list', 'maps_embed_code', 'maps_link'
    ];
    
    protected $casts = [
        'program_list' => 'array'
    ];
    
    /**
     * Get the first (and only) record
     */
    public static function getFirst()
    {
        return static::first();
    }
}