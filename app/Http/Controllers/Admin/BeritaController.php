<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BeritaController extends Controller
{
    public function index()
    {
        $berita = News::latest()->paginate(10);
        return view('admin.berita.index', compact('berita'));
    }

    public function create()
    {
        return view('admin.berita.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'published_at' => 'nullable|date',
            'is_published' => 'nullable|boolean',
        ]);

        // Menambahkan timestamp untuk menjamin unik
        $slug = Str::slug($validated['title']) . '-' . time() . '-' . rand(1000, 9999);
        $validated['slug'] = $slug;
        
        $validated['is_published'] = $request->boolean('is_published');

        // Handle published_at
        if (!empty($validated['published_at'])) {
            $validated['published_at'] = \Carbon\Carbon::parse($validated['published_at']);
        } elseif ($validated['is_published']) {
            $validated['published_at'] = now();
        }

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('news', 'public');
        }

        News::create($validated);
        
        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil ditambahkan.');
    }

    /**
     * Generate slug yang unik (untuk Create)
     */
    private function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;
        
        // ✅ Gunakan 'News::class' atau 'News' saja, JANGAN '\App\Models\News'
        while (News::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
            
            // Safety break
            if ($counter > 100) {
                $slug = $originalSlug . '-' . uniqid();
                break;
            }
        }
        
        return $slug;
    }

    /**
     * Generate slug yang unik (untuk Update, exclude ID sendiri)
     */
    private function generateUniqueSlugForUpdate($title, $excludeId)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;
        
        while (News::where('slug', $slug)
               ->where('id', '!=', $excludeId)
               ->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
            
            if ($counter > 100) {
                $slug = $originalSlug . '-' . uniqid();
                break;
            }
        }
        
        return $slug;
    }

    public function edit(News $beritum)
    {
        return view('admin.berita.edit', ['berita' => $beritum]);
    }

    public function update(Request $request, News $beritum)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'published_at' => 'nullable|date',
            'image' => 'nullable|image|max:2048',
            'is_published' => 'nullable|boolean',
        ]);

        // ✅ GENERATE SLUG UNIK UNTUK UPDATE
        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $counter = 1;
        
        while (\App\Models\News::where('slug', $slug)
            ->where('id', '!=', $beritum->id)  // Exclude berita yang sedang diedit
            ->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
            
            if ($counter > 1000) {
                $slug = $originalSlug . '-' . time();
                break;
            }
        }
        
        $validated['slug'] = $slug;
        $validated['is_published'] = $request->boolean('is_published');

        // Handle published_at
        if (!empty($validated['published_at'])) {
            $validated['published_at'] = \Carbon\Carbon::parse($validated['published_at']);
        } elseif ($validated['is_published'] && empty($beritum->published_at)) {
            $validated['published_at'] = now();
        }

        if ($request->hasFile('image')) {
            if ($beritum->image_path) {
                Storage::disk('public')->delete($beritum->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('news', 'public');
        }

        $beritum->update($validated);
        
        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(News $beritum)
    {
        if ($beritum->image_path) {
            Storage::disk('public')->delete($beritum->image_path);
        }
        $beritum->delete();
        
        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil dihapus.');
    }
}