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
        // Pastikan master kecamatan tersedia (sama seperti index) supaya
        // dropdown form punya pasangan id lokal untuk kode wilayah SIEDA.
        if (Kecamatan::count() === 0) {
            $this->wilayahService->syncKecamatansToba();
        }

        $kecamatans = Kecamatan::orderBy('name')->get();
        $selectedKecamatan = request('kecamatan');
        return view('admin.desa.create', compact('kecamatans', 'selectedKecamatan'));
    }

    /**
     * Endpoint dropdown form Tambah/Edit Desa.
     *
     * Kecamatan & desa yang BOLEH dipilih hanyalah yang sudah terisi datanya
     * di database SIEDA (punya data warga/KK). Desa yang sudah pernah
     * ditambahkan admin ditandai sudah_terdaftar = true agar tidak bisa
     * didaftarkan dua kali (opsi dinonaktifkan di form — kecuali desa yang
     * sedang diedit sendiri).
     */
    public function siedaWilayah()
    {
        $snapshot = $this->getSiedaSnapshot();

        // Pasangan id lokal ↔ kode wilayah, dipakai sebagai value <select>
        // kecamatan_id (divalidasi exists:kecamatans,id).
        $kecamatanLokal = Kecamatan::select('id', 'kode_wilayah', 'name')->get()->keyBy('kode_wilayah');

        // Desa yang sudah didaftarkan admin, dikelompokkan per kode kecamatan.
        $terdaftarPerKecamatan = Desa::with('kecamatan:id,kode_wilayah')
            ->get()
            ->filter(fn($d) => !empty($d->kode_wilayah))
            ->groupBy(fn($d) => (string) ($d->kecamatan->kode_wilayah ?? ''));

        $kodeSudahTerdaftar = Desa::pluck('kode_wilayah')->map(fn($k) => (string) $k)->all();

        $data = collect($snapshot['kecamatan'])->map(function ($kec) use ($snapshot, $kecamatanLokal, $terdaftarPerKecamatan, $kodeSudahTerdaftar) {
            $dariSieda = collect($snapshot['desas'])
                ->where('kode_kecamatan', $kec['kode'])
                ->map(fn($d) => [
                    'kode'            => (string) $d['kode'],
                    'nama'            => $d['nama'],
                    'sudah_terdaftar' => in_array((string) $d['kode'], $kodeSudahTerdaftar, true),
                ]);

            // Desa terdaftar yang tidak ada di data SIEDA (mis. data lama)
            // tetap ditampilkan (berstatus terdaftar) supaya form Edit tetap
            // bisa memuat nilai saat ini.
            $ekstra = $terdaftarPerKecamatan->get((string) $kec['kode'], collect())
                ->filter(fn($d) => !$dariSieda->contains('kode', (string) $d->kode_wilayah))
                ->map(fn($d) => [
                    'kode'            => (string) $d->kode_wilayah,
                    'nama'            => $d->name,
                    'sudah_terdaftar' => true,
                ]);

            return [
                'id'    => $kecamatanLokal[$kec['kode']]->id ?? null,
                'kode'  => $kec['kode'],
                'nama'  => $kec['nama'],
                'desas' => $dariSieda->merge($ekstra)
                    ->sortBy('nama', SORT_NATURAL | SORT_FLAG_CASE)
                    ->values()->all(),
            ];
        });

        // Kecamatan lokal yang punya desa terdaftar tapi tidak ada di data
        // SIEDA — tetap disertakan agar form Edit tidak kehilangan pilihan.
        $tambahan = $terdaftarPerKecamatan
            ->keys()
            ->reject(fn($kode) => $kode === '' || $data->contains('kode', $kode))
            ->map(function ($kode) use ($kecamatanLokal, $terdaftarPerKecamatan) {
                return [
                    'id'    => $kecamatanLokal[$kode]->id ?? null,
                    'kode'  => $kode,
                    'nama'  => $kecamatanLokal[$kode]->name ?? $kode,
                    'desas' => $terdaftarPerKecamatan->get($kode)
                        ->map(fn($d) => [
                            'kode'            => (string) $d->kode_wilayah,
                            'nama'            => $d->name,
                            'sudah_terdaftar' => true,
                        ])->values()->all(),
                ];
            });

        return response()->json(['success' => true, 'data' => $data->merge($tambahan)->values()->all()]);
    }

    /**
     * Ambil referensi wilayah + jumlah penduduk & KK per desa dari database
     * SIEDA (koneksi 'sieda') dalam sekali jalan.
     * Normor (angka) tidak lagi diinput manual — semuanya otomatis dari SIEDA.
     *
     * Struktur ref SIEDA: ref_kecamatan(kode, nama) → ref_desa(kode, nama,
     * kode_kecamatan); tp_pkk_warga / tp_pkk_keluarga (kode_desa, active).
     *
     * @return array{
     *   population: array<string,int>, households: array<string,int>,
     *   desas: array<int,array{kode:string,nama:string,kode_kecamatan:string}>,
     *   kecamatan: array<int,array{kode:string,nama:string}>,
     * }
     */
    private function getSiedaSnapshot(): array
    {
        $kosong = ['population' => [], 'households' => [], 'desas' => [], 'kecamatan' => []];

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

            // Hanya desa yang benar-benar punya data (warga/KK) di SIEDA.
            $kodeDenganData = $pendudukPerDesa->keys()->merge($kkPerDesa->keys())->unique()->values();
            $desas = $kodeDenganData->isNotEmpty()
                ? $sieda->table('ref_desa')->whereIn('kode', $kodeDenganData)->orderBy('kode')->get(['kode', 'nama', 'kode_kecamatan'])
                : collect();

            $kecamatans = $desas->isNotEmpty()
                ? $sieda->table('ref_kecamatan')->orderBy('nama')->get(['kode', 'nama'])
                    ->filter(fn($kec) => $desas->contains('kode_kecamatan', $kec->kode))->values()
                : collect();

            return [
                'population' => $pendudukPerDesa->map(fn($v) => (int) $v)->all(),
                'households' => $kkPerDesa->map(fn($v) => (int) $v)->all(),
                'desas'      => $desas->map(fn($d) => ['kode' => $d->kode, 'nama' => $d->nama, 'kode_kecamatan' => $d->kode_kecamatan])->all(),
                'kecamatan'  => $kecamatans->map(fn($k) => ['kode' => $k->kode, 'nama' => $k->nama])->all(),
            ];
        } catch (\Throwable $e) {
            // SIEDA tidak terjangkau — biarkan kosong, jangan gagalkan halaman.
            \Illuminate\Support\Facades\Log::warning('[DesaController] Gagal mengambil data dari SIEDA: ' . $e->getMessage());
            return $kosong;
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
        $stats = $this->getSiedaSnapshot();
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
        $stats = $this->getSiedaSnapshot();
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
