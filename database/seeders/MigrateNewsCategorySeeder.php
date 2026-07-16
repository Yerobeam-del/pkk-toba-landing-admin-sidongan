<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class MigrateNewsCategorySeeder extends Seeder
{
    public function run()
    {
        echo "Memulai migrasi kategori berita...\n";
        
        $newsItems = News::whereNull('category_id')->whereNotNull('category')->get();
        
        foreach ($newsItems as $news) {
            $categoryName = trim($news->category);
            
            if (empty($categoryName)) {
                continue;
            }
            
            // Cari atau buat category
            $category = Category::firstOrCreate(
                ['name' => $categoryName],
                ['slug' => \Str::slug($categoryName)]
            );
            
            // Update news dengan category_id
            $news->update(['category_id' => $category->id]);
            
            echo "✓ Updated: {$news->title} → {$category->name}\n";
        }
        
        echo "Selesai! {$newsItems->count()} berita dimigrasi.\n";
    }
}