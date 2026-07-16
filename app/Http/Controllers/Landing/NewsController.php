<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display the news detail page (frontend view)
     */
    public function show(string $slug)
    {
        // Ambil berita berdasarkan slug
        $news = News::published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Ambil berita terkait berdasarkan kategori yang SAMA
        $relatedNews = News::published()
            ->where('id', '!=', $news->id)
            ->where('category', $news->category)
            ->limit(3)
            ->get();

        return view('modules.landing.news-detail', compact('news', 'relatedNews'));
    }
}