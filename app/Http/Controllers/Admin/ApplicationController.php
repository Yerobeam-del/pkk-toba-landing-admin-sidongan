<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $currentTab = request('tab', 'all');

        // Query dasar dengan search
        $baseQuery = Application::query()->where(function($q) use ($search) {
            if ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            }
        });

        // 1. Semua Aplikasi (dengan pagination)
        $allApps = (clone $baseQuery)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->paginate($perPage, ['*'], 'page_all');

        // 2. Aplikasi Aktif (dengan pagination)
        $activeApps = (clone $baseQuery)
            ->where('status', 'active')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->paginate($perPage, ['*'], 'page_active');

        // 3. Maintenance (dengan pagination)
        $maintenanceApps = (clone $baseQuery)
            ->where('status', 'maintenance')
            ->orderBy('sort_order')
            ->paginate($perPage, ['*'], 'page_maintenance');

        // 4. Pengembangan (dengan pagination)
        $developmentApps = (clone $baseQuery)
            ->where('status', 'development')
            ->orderBy('sort_order')
            ->paginate($perPage, ['*'], 'page_development');

        // Hitung statistik untuk badge dan cards
        $stats = [
            'total' => Application::count(),
            'active' => Application::where('status', 'active')->where('is_active', true)->count(),
            'maintenance' => Application::where('status', 'maintenance')->count(),
            'development' => Application::where('status', 'development')->count(),
            'show_in_beranda' => Application::where('is_active', true)
                ->where('status', 'active')
                ->where('show_in_quick_access', true)
                ->count(),
            'show_in_footer' => Application::where('is_active', true)
                ->where('status', 'active')
                ->where('show_in_footer', true)
                ->count(),
            'show_in_floating' => Application::where('is_active', true)
                ->where('status', 'active')
                ->where('show_in_floating', true)
                ->count(),
        ];

        return view('admin.aplikasi.index', compact(
            'allApps', 'activeApps', 'maintenanceApps', 'developmentApps',
            'stats', 'currentTab', 'search', 'perPage'
        ));
    }

    public function create()
    {
        $maxSortOrder = Application::max('sort_order') ?? 0;
        $nextSortOrder = $maxSortOrder + 1;
        return view('admin.aplikasi.create', compact('nextSortOrder'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:50',
            'category' => 'required|in:layanan,aplikasi',
            'description' => 'required|string|max:1000',
            'features' => 'nullable|array|min:2|max:5',
            'features.*' => 'string|max:255',
            'status' => 'required|in:active,maintenance,development',
            'url' => 'nullable|url',
            'icon' => 'nullable|image|max:2048',
            'sort_order' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
            'show_in_floating' => 'nullable|boolean',
            'show_in_footer' => 'nullable|boolean',
            'show_in_quick_access' => 'nullable|boolean',
        ]);

        // Konversi short_name ke UPPERCASE
        $validated['short_name'] = strtoupper(trim($validated['short_name']));

        // Handle checkbox boolean
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['show_in_floating'] = $request->has('show_in_floating') ? 1 : 0;
        $validated['show_in_footer'] = $request->has('show_in_footer') ? 1 : 0;

        // Validasi maksimal 2 aplikasi di Beranda
        if ($request->has('show_in_quick_access')) {
            $currentBerandaCount = Application::where('show_in_quick_access', true)
                ->where('is_active', true)
                ->where('status', 'active')
                ->count();
            if ($currentBerandaCount >= 2) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['show_in_quick_access' => 'Maksimal hanya 2 aplikasi yang bisa tampil di Beranda.']);
            }
            $validated['show_in_quick_access'] = 1;
        } else {
            $validated['show_in_quick_access'] = 0;
        }

        // Auto-generate sort_order jika tidak diisi atau status development
        if (empty($validated['sort_order']) || $validated['status'] === 'development') {
            $validated['sort_order'] = Application::max('sort_order') + 1;
        }

        // Handle icon upload
        if ($request->hasFile('icon')) {
            $validated['icon'] = $request->file('icon')->store('applications', 'public');
        }

        // Handle status logic
        if ($validated['status'] === 'development') {
            $validated['url'] = '#';
            $validated['is_active'] = 0;
        }

        Application::create($validated);
        return redirect()->route('admin.aplikasi.index')->with('success', 'Aplikasi berhasil ditambahkan!');
    }

    public function edit(Application $aplikasi)
    {
        return view('admin.aplikasi.edit', compact('aplikasi'));
    }

    public function update(Request $request, Application $aplikasi)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'short_name' => 'required|string|max:20|unique:applications,short_name,' . $aplikasi->id,
            'description' => 'nullable|string|max:1000',
            'url' => 'nullable|url|max:255',
            'category' => 'required|in:layanan,aplikasi',
            'status' => 'required|in:active,maintenance,development',
            'features' => 'nullable|array|min:2|max:5',
            'features.*' => 'required|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'icon' => 'nullable|image|max:2048',
            'show_in_floating' => 'nullable|boolean',
            'show_in_footer' => 'nullable|boolean',
            'show_in_quick_access' => 'nullable|boolean',
        ]);

        // Konversi short_name ke UPPERCASE
        $validated['short_name'] = strtoupper(trim($validated['short_name']));

        // Handle checkbox boolean
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['show_in_floating'] = $request->has('show_in_floating') ? 1 : 0;
        $validated['show_in_footer'] = $request->has('show_in_footer') ? 1 : 0;

        // Validasi maksimal 2 aplikasi di Beranda (kecuali aplikasi yang sedang diedit)
        if ($request->has('show_in_quick_access')) {
            if (!$aplikasi->show_in_quick_access) {
                $currentBerandaCount = Application::where('show_in_quick_access', true)
                    ->where('is_active', true)
                    ->where('status', 'active')
                    ->where('id', '!=', $aplikasi->id)
                    ->count();
                if ($currentBerandaCount >= 2) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['show_in_quick_access' => 'Maksimal hanya 2 aplikasi yang bisa tampil di Beranda.']);
                }
            }
            $validated['show_in_quick_access'] = 1;
        } else {
            $validated['show_in_quick_access'] = 0;
        }

        // Handle icon upload (UPDATE)
        if ($request->hasFile('icon')) {
            if ($aplikasi->icon) {
                Storage::disk('public')->delete($aplikasi->icon);
            }
            $validated['icon'] = $request->file('icon')->store('applications', 'public');
        }

        $aplikasi->update($validated);
        return redirect()->route('admin.aplikasi.index')->with('success', 'Aplikasi berhasil diperbarui.');
    }

    public function destroy(Application $aplikasi)
    {
        if ($aplikasi->icon) {
            Storage::disk('public')->delete($aplikasi->icon);
        }
        $aplikasi->delete();
        return redirect()->route('admin.aplikasi.index')->with('success', 'Aplikasi berhasil dihapus.');
    }
}
