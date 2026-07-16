<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Health check endpoint
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

// Public API endpoints (tanpa auth)
Route::prefix('v1')->group(function () {
    
    // News endpoints
    Route::get('/news', function () {
        try {
            $news = \App\Models\News::published()
                ->orderBy('published_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'slug' => $item->slug,
                        'title' => $item->title,
                        'category' => $item->category,
                        'excerpt' => $item->excerpt,
                        'content' => $item->content,
                        'image_path' => $item->image_path,
                        'image' => $item->image_path ? asset('storage/' . $item->image_path) : null,
                        'published_at' => $item->published_at,
                        'created_at' => $item->created_at,
                        'date' => $item->published_at?->format('d M Y') ?? $item->created_at->format('d M Y'),
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => $news
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    });
    
    // Single news by slug
    Route::get('/news/{slug}', function ($slug) {
        try {
            $news = \App\Models\News::published()
                ->where('slug', $slug)
                ->firstOrFail();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $news->id,
                    'slug' => $news->slug,
                    'title' => $news->title,
                    'category' => $news->category,
                    'excerpt' => $news->excerpt,
                    'content' => $news->content,
                    'image_path' => $news->image_path,
                    'image' => $news->image_path ? asset('storage/' . $news->image_path) : null,
                    'published_at' => $news->published_at,
                    'date' => $news->published_at?->format('d M Y') ?? $news->created_at->format('d M Y'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Berita tidak ditemukan'
            ], 404);
        }
    });
    
});