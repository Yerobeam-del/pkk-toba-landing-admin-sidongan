<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DokumenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Dokumen::query();
        
        // Filter search
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('file_name', 'like', '%' . $search . '%');
            });
        }
        
        // Filter status
        if (request('status')) {
            $query->where('status', request('status'));
        }
        
        $dokumens = $query->orderBy('document_date', 'desc')
                          ->orderBy('sort_order')
                          ->paginate(20);
        
        return view('admin.sk.index', compact('dokumens'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.sk.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:10240',
            'document_date' => 'required|date',
            'status' => 'required|in:draft,published',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $fileSize = $this->formatFileSize($file->getSize());
            $fileType = $file->getMimeType();
            $filePath = $file->store('dokumen', 'public');
            
            $validated['file_path'] = $filePath;
            $validated['file_name'] = $fileName;
            $validated['file_size'] = $fileSize;
            $validated['file_type'] = $fileType;
        }

        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        Dokumen::create($validated);
        
        return redirect()->route('admin.sk.index')->with('success', 'Dokumen berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Dokumen $dokumen)
    {
        // Redirect to file URL for preview
        return redirect($dokumen->file_url);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dokumen $dokumen)
    {
        return view('admin.sk.edit', compact('dokumen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dokumen $dokumen)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:10240',
            'document_date' => 'required|date',
            'status' => 'required|in:draft,published',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('file')) {
            // Hapus file lama
            if ($dokumen->file_path && Storage::disk('public')->exists($dokumen->file_path)) {
                Storage::disk('public')->delete($dokumen->file_path);
            }
            
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $fileSize = $this->formatFileSize($file->getSize());
            $fileType = $file->getMimeType();
            
            $validated['file_path'] = $file->store('dokumen', 'public');
            $validated['file_name'] = $fileName;
            $validated['file_size'] = $fileSize;
            $validated['file_type'] = $fileType;
        }

        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $dokumen->update($validated);
        
        return redirect()->route('admin.sk.index')->with('success', 'Dokumen berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dokumen $dokumen)
    {
        // Hapus file dari storage
        if ($dokumen->file_path && Storage::disk('public')->exists($dokumen->file_path)) {
            Storage::disk('public')->delete($dokumen->file_path);
        }
        
        $dokumen->delete();
        
        return redirect()->route('admin.sk.index')->with('success', 'Dokumen berhasil dihapus.');
    }

    /**
     * Helper: Format file size
     */
    private function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}