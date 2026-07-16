<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{
    public function index()
    {
        $query = Template::query();
        if (request('search')) {
            $s = request('search');
            $query->where(fn($q) => $q->where('name', 'like', "%$s%")->orWhere('file_name', 'like', "%$s%"));
        }
        if (request('status')) $query->where('status', request('status'));
        
        $templates = $query->orderBy('upload_date', 'desc')->orderBy('sort_order')->paginate(20);
        return view('admin.template.index', compact('templates'));
    }

    public function create() { return view('admin.template.create'); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:10240',
            'upload_date' => 'required|date',
            'status' => 'required|in:draft,published',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $validated['file_path'] = $file->store('templates', 'public');
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $this->formatSize($file->getSize());
            $validated['file_type'] = $file->getMimeType();
        }
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        Template::create($validated);
        return redirect()->route('admin.template.index')->with('success', 'Template berhasil ditambahkan.');
    }

    public function edit(Template $template) { return view('admin.template.edit', compact('template')); }

    public function update(Request $request, Template $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:10240',
            'upload_date' => 'required|date',
            'status' => 'required|in:draft,published',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('file')) {
            if ($template->file_path && Storage::disk('public')->exists($template->file_path)) {
                Storage::disk('public')->delete($template->file_path);
            }
            $file = $request->file('file');
            $validated['file_path'] = $file->store('templates', 'public');
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $this->formatSize($file->getSize());
            $validated['file_type'] = $file->getMimeType();
        }
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $template->update($validated);
        return redirect()->route('admin.template.index')->with('success', 'Template berhasil diperbarui.');
    }

    public function destroy(Template $template)
    {
        if ($template->file_path && Storage::disk('public')->exists($template->file_path)) {
            Storage::disk('public')->delete($template->file_path);
        }
        $template->delete();
        return redirect()->route('admin.template.index')->with('success', 'Template berhasil dihapus.');
    }

    private function formatSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        return round($bytes / pow(1024, $pow), 2) . ' ' . $units[$pow];
    }
}