<?php

namespace App\Http\Controllers\Sidongan;

use App\Http\Controllers\Controller;
use App\Models\DocumentTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index()
    {
        $tags = DocumentTag::withCount('documents')
            ->orderBy('name')
            ->get();
        
        return view('sidongan-admin.tags.index', compact('tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sidongan_tags,name',
        ]);

        DocumentTag::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return redirect()->route('sidongan.admin.tags.index')
            ->with('success', 'Tag berhasil ditambahkan!');
    }

    public function update(Request $request, DocumentTag $tag)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sidongan_tags,name,' . $tag->id,
        ]);

        $tag->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return redirect()->route('sidongan.admin.tags.index')
            ->with('success', 'Tag berhasil diperbarui!');
    }

    public function destroy(DocumentTag $tag)
    {
        // Detach from all documents before deleting
        $tag->documents()->detach();
        $tag->delete();

        return redirect()->route('sidongan.admin.tags.index')
            ->with('success', 'Tag berhasil dihapus!');
    }
}