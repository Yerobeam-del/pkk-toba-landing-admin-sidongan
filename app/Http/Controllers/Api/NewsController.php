<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\JsonResponse;

class NewsController extends Controller
{
    public function index(): JsonResponse
    {
        $news = News::published()
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
                    'image' => $item->image_path ? asset('storage/' . $item->image_path) : null,
                    'image_path' => $item->image_path,
                    'date' => $item->published_at?->format('d M Y') ?? $item->created_at->format('d M Y'),
                    'published_at' => $item->published_at,
                    'created_at' => $item->created_at,
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }
    
    public function show(string $slug): JsonResponse
    {
        $news = News::published()->where('slug', $slug)->firstOrFail();
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $news->id,
                'slug' => $news->slug,
                'title' => $news->title,
                'category' => $news->category,
                'excerpt' => $news->excerpt,
                'content' => $news->content,
                'image' => $news->image_path ? asset('storage/' . $news->image_path) : null,
                'date' => $news->published_at?->format('d M Y') ?? $news->created_at->format('d M Y'),
            ]
        ]);
    }
}