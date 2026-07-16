<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Template extends Model
{
    protected $fillable = ['name', 'file_path', 'file_name', 'file_size', 'file_type', 'upload_date', 'status', 'sort_order'];
    protected $casts = ['upload_date' => 'date', 'status' => 'string'];

    public function scopePublished($query) { return $query->where('status', 'published'); }

    public function fileUrl(): Attribute {
        return Attribute::make(get: fn () => $this->file_path ? asset('storage/' . $this->file_path) : null);
    }

    /**
    * Get preview URL
    */
    public function getPreviewUrlAttribute()
    {
        if (!$this->file_path) return null;
        
        // Cek apakah preview sudah ada
        $previewPath = 'previews/' . pathinfo($this->file_path, PATHINFO_FILENAME) . '_thumb.jpg';
        $previewFullPath = storage_path('app/public/' . $previewPath);
        
        if (file_exists($previewFullPath)) {
            return asset('storage/' . $previewPath);
        }
        
        // Generate preview jika belum ada
        $service = new \App\Services\DocumentPreviewService();
        $generatedPath = $service->generatePreview(
            $this->file_path,
            $this->file_type,
            $this->file_name
        );
        
        if ($generatedPath) {
            return asset('storage/' . $generatedPath);
        }
        
        // Fallback ke placeholder
        return $this->getDefaultPlaceholderUrl();
    }

    /**
     * Get default placeholder URL
     */
    private function getDefaultPlaceholderUrl()
    {
        $ext = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
        
        $placeholders = [
            'pdf' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&h=400&fit=crop',
            'doc' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=600&h=400&fit=crop',
            'docx' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=600&h=400&fit=crop',
            'xls' => 'https://images.unsplash.com/photo-1460518451285-97b6aa326961?w=600&h=400&fit=crop',
            'xlsx' => 'https://images.unsplash.com/photo-1460518451285-97b6aa326961?w=600&h=400&fit=crop',
            'ppt' => 'https://images.unsplash.com/photo-1542435503-956c469947f6?w=600&h=400&fit=crop',
            'pptx' => 'https://images.unsplash.com/photo-1542435503-956c469947f6?w=600&h=400&fit=crop',
        ];
        
        return $placeholders[$ext] ?? 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=600&h=400&fit=crop';
    }
}