<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class DocumentPreviewService
{
    /**
     * Generate preview image dari dokumen
     */
    public function generatePreview($filePath, $fileType, $fileName)
    {
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (!file_exists($fullPath)) {
            return null;
        }
        
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        try {
            switch ($extension) {
                case 'pdf':
                    return $this->generatePdfPreview($fullPath, $filePath);
                
                case 'doc':
                case 'docx':
                    return $this->generateDocPreview($fullPath, $filePath, $extension);
                
                case 'xls':
                case 'xlsx':
                    return $this->generateExcelPreview($fullPath, $filePath, $extension);
                
                case 'ppt':
                case 'pptx':
                    return $this->generatePptPreview($fullPath, $filePath, $extension);
                
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                    return $this->generateImagePreview($fullPath, $filePath);
                
                default:
                    return $this->getDefaultPlaceholder($extension);
            }
        } catch (\Exception $e) {
            \Log::error('Preview generation error: ' . $e->getMessage());
            return $this->getDefaultPlaceholder($extension);
        }
    }
    
    /**
     * Generate thumbnail dari PDF (halaman pertama)
     */
    private function generatePdfPreview($filePath, $originalPath)
    {
        // Gunakan Ghostscript atau pdftoppm jika tersedia
        // Fallback: generate placeholder dengan teks
        return $this->generateTextPlaceholder('PDF', $originalPath);
    }
    
    /**
     * Generate preview untuk DOC/DOCX
     */
    private function generateDocPreview($filePath, $originalPath, $ext)
    {
        // Untuk DOCX, bisa extract text content
        if ($ext === 'docx') {
            $zip = new \ZipArchive;
            if ($zip->open($filePath) === TRUE) {
                $content = $zip->getFromName('word/document.xml');
                $zip->close();
                
                if ($content) {
                    // Extract text dari XML
                    $text = strip_tags(str_replace(['<w:', '</w:'], ['<', '</'], $content));
                    $text = substr($text, 0, 100);
                    return $this->generateTextPlaceholder('DOCX: ' . $text, $originalPath);
                }
            }
        }
        
        return $this->generateTextPlaceholder('DOC', $originalPath);
    }
    
    /**
     * Generate preview untuk Excel
     */
    private function generateExcelPreview($filePath, $originalPath, $ext)
    {
        return $this->generateTextPlaceholder('EXCEL', $originalPath);
    }
    
    /**
     * Generate preview untuk PowerPoint
     */
    private function generatePptPreview($filePath, $originalPath, $ext)
    {
        return $this->generateTextPlaceholder('POWERPOINT', $originalPath);
    }
    
    /**
     * Generate thumbnail untuk image
     */
    private function generateImagePreview($filePath, $originalPath)
    {
        $thumbnailPath = 'previews/' . uniqid() . '_thumb.jpg';
        $thumbnailFullPath = storage_path('app/public/' . $thumbnailPath);
        
        // Buat directory jika belum ada
        $dir = dirname($thumbnailFullPath);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        
        // Resize image
        $img = Image::make($filePath);
        $img->fit(600, 400);
        $img->save($thumbnailFullPath);
        
        return $thumbnailPath;
    }
    
    /**
     * Generate placeholder dengan teks
     */
    private function generateTextPlaceholder($text, $originalPath)
    {
        // Buat image placeholder dengan GD
        $width = 600;
        $height = 400;
        $img = imagecreatetruecolor($width, $height);
        
        // Warna background (putih)
        $white = imagecolorallocate($img, 255, 255, 255);
        $blue = imagecolorallocate($img, 37, 99, 235);
        $gray = imagecolorallocate($img, 100, 116, 139);
        
        imagefill($img, 0, 0, $white);
        
        // Tambahkan teks
        $text = strtoupper($text);
        $fontSize = 5;
        $textWidth = imagefontwidth($fontSize) * strlen($text);
        $textHeight = imagefontheight($fontSize);
        
        // Teks utama
        imagestring($img, $fontSize, ($width - $textWidth) / 2, $height / 2 - $textHeight / 2, $text, $blue);
        
        // Tambahkan icon dokumen
        $iconText = '📄';
        
        // Simpan
        $thumbnailPath = 'previews/' . uniqid() . '_thumb.jpg';
        $thumbnailFullPath = storage_path('app/public/' . $thumbnailPath);
        
        $dir = dirname($thumbnailFullPath);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        
        imagejpeg($img, $thumbnailFullPath, 80);
        imagedestroy($img);
        
        return $thumbnailPath;
    }
    
    /**
     * Default placeholder
     */
    private function getDefaultPlaceholder($ext)
    {
        return null;
    }
}