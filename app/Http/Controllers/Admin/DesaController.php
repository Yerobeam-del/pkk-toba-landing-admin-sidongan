<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\Kecamatan;
use App\Services\WilayahIndonesiaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DesaController extends Controller
{
    protected $wilayahService;
    
    public function __construct(WilayahIndonesiaService $wilayahService)
    {
        $this->wilayahService = $wilayahService;
    }
    
    public function index()
    {
        // Auto-sync on first load or if empty
        if (Kecamatan::count() === 0) {
            $this->wilayahService->syncKecamatansToba();
        }
        
        $kecamatans = Kecamatan::with(['desas' => function($q) {
            $q->orderBy('sort_order');
        }])->orderBy('name')->get();
        
        return view('admin.desa.index', compact('kecamatans'));
    }

    public function create()
    {
        $kecamatans = Kecamatan::orderBy('name')->get();
        $selectedKecamatan = request('kecamatan');
        return view('admin.desa.create', compact('kecamatans', 'selectedKecamatan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kecamatan_id' => 'required|exists:kecamatans,id',
            'desa_code'    => 'required|string',   // Kode dari API
            'desa_name'    => 'required|string|max:100', // Nama dari API
            'population'   => 'nullable|integer|min:0',
            'households'   => 'nullable|integer|min:0',
            'sort_order'   => 'nullable|integer|min:0',
            'is_active'    => 'boolean',
            'image'        => 'nullable|image|max:2048',
        ]);

        $validated['name'] = $validated['desa_name'];
        $validated['kode_wilayah'] = $validated['desa_code'];
        unset($validated['desa_code'], $validated['desa_name']); // Bersihkan sebelum save
        
        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('desa', 'public');
        }

        \App\Models\Desa::create($validated);
        return redirect()->route('admin.desa.index')->with('success', 'Desa berhasil ditambahkan.');
    }

    public function edit(Desa $desa)
    {
        $kecamatans = Kecamatan::orderBy('name')->get();
        return view('admin.desa.edit', compact('desa', 'kecamatans'));
    }

    public function update(Request $request, \App\Models\Desa $desa)
    {
        $validated = $request->validate([
            'kecamatan_id' => 'required|exists:kecamatans,id',
            'desa_code'    => 'required|string',
            'desa_name'    => 'required|string|max:100',
            'population'   => 'nullable|integer|min:0',
            'households'   => 'nullable|integer|min:0',
            'sort_order'   => 'nullable|integer|min:0',
            'is_active'    => 'boolean',
            'image'        => 'nullable|image|max:2048',
        ]);

        $validated['name'] = $validated['desa_name'];
        $validated['kode_wilayah'] = $validated['desa_code'];
        unset($validated['desa_code'], $validated['desa_name']);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            if ($desa->image) \Storage::disk('public')->delete($desa->image);
            $validated['image'] = $request->file('image')->store('desa', 'public');
        }

        $desa->update($validated);
        return redirect()->route('admin.desa.index')->with('success', 'Desa berhasil diperbarui.');
    }

    public function getMaxSortOrder()
    {
        $maxSortOrder = \App\Models\Desa::max('sort_order') ?? 0;
        
        return response()->json([
            'success' => true,
            'data' => [
                'max_sort_order' => $maxSortOrder
            ]
        ]);
    }

    public function destroy(Desa $desa)
    {
        try {
            if ($desa->image) {
                Storage::disk('public')->delete($desa->image);
            }
            
            $desa->delete();
            
            // Check if request is AJAX/Fetch
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Desa berhasil dihapus'
                ]);
            }
            
            return redirect()->route('admin.desa.index')->with('success', 'Desa berhasil dihapus.');
        } catch (\Exception $e) {
            // Check if request is AJAX/Fetch
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus desa: ' . $e->getMessage()
                ], 422);
            }
            
            return redirect()->route('admin.desa.index')->with('error', 'Gagal menghapus desa.');
        }
    }
}