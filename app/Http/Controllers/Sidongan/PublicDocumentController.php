<?php

namespace App\Http\Controllers\Sidongan;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicDocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::with(['category', 'tags'])
            ->where('status', 'published')
            ->where('is_public', true);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $documents = $query->latest()->paginate(12);
        $categories = DocumentCategory::where('is_active', true)
            ->withCount('documents')
            ->orderBy('name')
            ->get();

        return view('sidongan.index', compact('documents', 'categories'));
    }

    public function show($slug)
    {
        $document = Document::with(['category', 'tags', 'creator'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->where('is_public', true)
            ->firstOrFail();

        return view('sidongan.show', compact('document'));
    }

    public function download($slug)
    {
        $document = Document::where('slug', $slug)
            ->where('status', 'published')
            ->where('is_public', true)
            ->firstOrFail();

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }
}