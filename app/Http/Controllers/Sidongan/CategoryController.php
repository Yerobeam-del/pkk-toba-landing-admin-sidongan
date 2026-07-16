<?php

namespace App\Http\Controllers\Sidongan;

use App\Http\Controllers\Controller;
use App\Models\DocumentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = DocumentCategory::withCount('documents')
            ->orderBy('name')
            ->get();
        
        return view('sidongan-admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sidongan_categories,name',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7', // Hex color code
            'is_active' => 'boolean',
        ]);

        DocumentCategory::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'color' => $validated['color'] ?? '#14b8a6',
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('sidongan.admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function update(Request $request, DocumentCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sidongan_categories,name,' . $category->id,
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'color' => $validated['color'] ?? '#14b8a6',
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('sidongan.admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy(DocumentCategory $category)
    {
        // Check if category has documents
        if ($category->documents()->count() > 0) {
            return redirect()->route('sidongan.admin.categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki dokumen!');
        }

        $category->delete();

        return redirect()->route('sidongan.admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus!');
    }
}