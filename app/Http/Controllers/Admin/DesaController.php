<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
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

    /**
     * Ambil jumlah penduduk & KK per desa dari database SIEDA (koneksi 'sieda').
     * Normor (angka) tidak lagi diinput manual — semuanya otomatis dari SIEDA.
     *
     * @return array{population: array<string,int>, households: array<string,int>}
     *         keyed by kode_desa.
     */
    private function getStatsFromSieda(): array
    {
        try {
            $sieda = \Illuminate\Support\Facades\DB::connection('sieda');

            $latestYear = $sieda->table('tp_pkk_keluarga')->max('config_year');
            $kkPerDesa = $sieda->table('tp_pkk_keluarga')
                ->where('active', 1)
                ->when($latestYear, fn($q) => $q->where('config_year', $latestYear))
                ->selectRaw('kode_desa, COUNT(*) as total')->groupBy('kode_desa')
                ->pluck('total', 'kode_desa');
            $pendudukPerDesa = $sieda->table('tp_pkk_warga')
                ->where('active', 1)
                ->selectRaw('kode_desa, COUNT(*) as total')->groupBy('kode_desa')
                ->pluck('total', 'kode_desa');

            return [
                'population' => $pendudukPerDesa->map(fn($v) => (int) $v)->all(),
                'households' => $kkPerDesa->map(fn($v) => (int) $v)->all(),
            ];
        } catch (\Throwable $e) {
            // SIEDA tidak terjangkau — biarkan angka 0, jangan gagalkan simpan.
            \Illuminate\Support\Facades\Log::warning('[DesaController] Gagal mengambil statistik dari SIEDA: ' . $e->getMessage());
            return ['population' => [], 'households' => []];
        }
    }

    public function store(Request $request)
    {
        // Hanya gambar yang bisa diinput manual. Angka penduduk/KK otomatis
        // diambil dari database SIEDA berdasarkan kode desa yang dipilih.
        $validated = $request->validate([
            'kecamatan_id' => 'required|exists:kecamatans,id',
            'desa_code'    => 'required|string',   // Kode dari API
            'desa_name'    => 'required|string|max:100', // Nama dari API
            'sort_order'   => 'nullable|integer|min:0',
            'is_active'    => 'boolean',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
        ]);

        $validated['name'] = $validated['desa_name'];
        $validated['kode_wilayah'] = $validated['desa_code'];
        unset($validated['desa_code'], $validated['desa_name']); // Bersihkan sebelum save

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        // Angka dari SIEDA
        $stats = $this->getStatsFromSieda();
        $validated['population'] = $stats['population'][$validated['kode_wilayah']] ?? 0;
        $validated['households'] = $stats['households'][$validated['kode_wilayah']] ?? 0;

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
        // Hanya gambar yang bisa diinput manual. Angka penduduk/KK otomatis
        // diambil dari database SIEDA berdasarkan kode desa yang dipilih.
        $validated = $request->validate([
            'kecamatan_id' => 'required|exists:kecamatans,id',
            'desa_code'    => 'required|string',
            'desa_name'    => 'required|string|max:100',
            'sort_order'   => 'nullable|integer|min:0',
            'is_active'    => 'boolean',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
        ]);

        $validated['name'] = $validated['desa_name'];
        $validated['kode_wilayah'] = $validated['desa_code'];
        unset($validated['desa_code'], $validated['desa_name']);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        // Angka dari SIEDA
        $stats = $this->getStatsFromSieda();
        $validated['population'] = $stats['population'][$validated['kode_wilayah']] ?? 0;
        $validated['households'] = $stats['households'][$validated['kode_wilayah']] ?? 0;

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
/* Dikembangkan oleh Institut Teknologi Del */
