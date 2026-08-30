<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsImage;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BeritaController extends Controller
{
    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $currentTab = request('tab', 'all');

        // Base query dengan pencarian (judul, ringkasan, atau kategori)
        $baseQuery = News::query()->where(function($q) use ($search) {
            if ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('excerpt', 'like', "%{$search}%")
                ->orWhere('category', 'like', "%{$search}%");
            }
        });

        // 1. Semua Berita (dengan pagination)
        $allNews = (clone $baseQuery)
            ->orderBy('is_published', 'desc')
            ->latest('created_at')
            ->paginate($perPage, ['*'], 'page_all');

        // 2. Berita Dipublikasi (dengan pagination)
        $publishedNews = (clone $baseQuery)
            ->where('is_published', true)
            ->latest('published_at')
            ->paginate($perPage, ['*'], 'page_published');

        // 3. Berita Draft (dengan pagination)
        $draftNews = (clone $baseQuery)
            ->where('is_published', false)
            ->latest('created_at')
            ->paginate($perPage, ['*'], 'page_draft');

        // Hitung statistik untuk badge
        $stats = [
            'total' => News::count(),
            'published' => News::where('is_published', true)->count(),
            'draft' => News::where('is_published', false)->count(),
        ];

        return view('admin.berita.index', compact(
            'allNews', 'publishedNews', 'draftNews',
            'stats', 'currentTab', 'search', 'perPage'
        ));
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
            'author_type' => 'required|in:account,manual',
            'author' => 'required_if:author_type,manual|nullable|string|max:255',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'published_at' => 'nullable|date',
            'is_published' => 'nullable|boolean',
        ]);

        // Handle author: jika tipe 'account', ambil nama dari user yang login
        if ($validated['author_type'] === 'account') {
            $validated['author'] = $request->user()->name;
        }

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

        $news = News::create($validated);

        // Handle multiple images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('news', 'public');
                $news->images()->create([
                    'image_path' => $path,
                    'sort_order' => $index,
                ]);
            }
        }
        AdminActivityLog::log('created', 'berita', null, ['title' => $validated['title'] ?? '']);
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
            'author_type' => 'required|in:account,manual',
            'author' => 'required_if:author_type,manual|nullable|string|max:255',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'published_at' => 'nullable|date',
            'image' => 'nullable|image|max:2048',
            'is_published' => 'nullable|boolean',
        ]);

        // Handle author: jika tipe 'account', ambil nama dari user yang login
        if ($validated['author_type'] === 'account') {
            $validated['author'] = $request->user()->name;
        }

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

        // Handle new multiple images
        if ($request->hasFile('images')) {
            $maxOrder = $beritum->images()->max('sort_order') ?? 0;
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('news', 'public');
                $beritum->images()->create([
                    'image_path' => $path,
                    'sort_order' => $maxOrder + $index + 1,
                ]);
            }
        }
        AdminActivityLog::log('updated', 'berita', $beritum->id, ['title' => $beritum->title ?? '']);
        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil diperbarui.');
    }

    public function deleteImage(News $beritum, NewsImage $image)
    {
        if ($image->news_id !== $beritum->id) {
            abort(404);
        }

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return redirect()->route('admin.berita.edit', $beritum)->with('success', 'Gambar berhasil dihapus.');
    }

    public function destroy(News $beritum)
    {
        // Delete all related images
        foreach ($beritum->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        $beritum->images()->delete();

        if ($beritum->image_path) {
            Storage::disk('public')->delete($beritum->image_path);
        }
        AdminActivityLog::log('deleted', 'berita', $beritum->id, ['title' => $beritum->title ?? '']);
        $beritum->delete();
        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil dihapus.');
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
