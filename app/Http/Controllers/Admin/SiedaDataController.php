<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sieda\AnggotaKeluarga;
use App\Models\Sieda\CatatanKelahiranKematian;
use App\Models\Sieda\Keluarga;
use App\Models\Sieda\KelompokDasawisma;
use App\Models\Sieda\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Manajemen Data SIEDA
 *
 * Controller ini memungkinkan Super Admin untuk melihat, mengelola,
 * dan menghapus PERMANEN data dari aplikasi SIEDA yang menggunakan
 * sistem soft-delete.
 *
 * Perbedaan dengan SIEDA:
 *   - SIEDA (aplikasi operator): hanya soft-delete (active=0)
 *   - Admin Panel (di sini): bisa hard-delete permanen + restore
 *
 * JANGAN tampilkan halaman ini ke role non-super_admin.
 */
class SiedaDataController extends Controller
{
    /**
     * Mapping slug → model + label untuk UI
     */
    private const MODULES = [
        'warga' => [
            'model' => Warga::class,
            'label' => 'Data Warga / Penduduk',
            'id_field' => 'nik',
            'id_label' => 'NIK',
            'display_fields' => ['nik', 'nama', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir'],
            'search_fields' => ['nik', 'nama', 'no_registrasi', 'alamat'],
        ],
        'keluarga' => [
            'model' => Keluarga::class,
            'label' => 'Data Keluarga',
            'id_field' => 'no_kk',
            'id_label' => 'Nomor KK',
            'display_fields' => ['no_kk', 'id_kepala_keluarga', 'id_kelompok_dasawisma', 'config_year'],
            'search_fields' => ['no_kk', 'no_registrasi_keluarga'],
        ],
        'anggota-keluarga' => [
            'model' => AnggotaKeluarga::class,
            'label' => 'Anggota Keluarga',
            'id_field' => 'id',
            'id_label' => 'ID Record',
            'display_fields' => ['id', 'no_kk', 'nik'],
            'search_fields' => ['no_kk', 'nik'],
        ],
        'kelompok-dasawisma' => [
            'model' => KelompokDasawisma::class,
            'label' => 'Kelompok Dasawisma',
            'id_field' => 'id',
            'id_label' => 'ID',
            'display_fields' => ['id', 'nama', 'id_dusun', 'kader', 'config_year'],
            'search_fields' => ['nama', 'kader'],
        ],
        'catatan-ibu-anak' => [
            'model' => CatatanKelahiranKematian::class,
            'label' => 'Catatan Ibu & Anak (Kelahiran / Kematian)',
            'id_field' => 'id',
            'id_label' => 'ID',
            'display_fields' => ['id', 'id_warga_ibu', 'status_ibu', 'tanggal_melahirkan', 'config_year'],
            'search_fields' => ['id_warga_ibu', 'nama_bayi', 'nama_meninggal'],
        ],
    ];

    /**
     * Dashboard overview — statistik semua modul
     */
    public function index(Request $request)
    {
        $this->authorizeSuperAdmin();

        $stats = collect(self::MODULES)->map(function ($config, $slug) {
            $count = $config['model']::count();
            $softDeleted = $config['model']::where('active', 0)->count();
            return [
                'slug' => $slug,
                'label' => $config['label'],
                'total' => $count,
                'aktif' => $count - $softDeleted,
                'terhapus' => $softDeleted,
            ];
        });

        $totalKeseluruhan = $stats->sum('total');
        $totalTerhapus = $stats->sum('terhapus');

        return view('admin.sieda-data.index', compact('stats', 'totalKeseluruhan', 'totalTerhapus'));
    }

    /**
     * List data untuk modul tertentu
     */
    public function showModule(Request $request, string $module)
    {
        $this->authorizeSuperAdmin();

        $config = $this->resolveModule($module);
        if (!$config) {
            abort(404, 'Modul tidak ditemukan.');
        }

        $perPage = min((int) $request->input('per_page', 25), 100);
        $search = $request->input('search', '');
        $filterStatus = $request->input('status', 'aktif'); // 'aktif' | 'terhapus'

        $query = $config['model']::query();

        // Filter status (aktif / terhapus)
        if ($filterStatus === 'terhapus') {
            $query->where('active', 0);
        }

        // Search
        if ($search && !empty($config['search_fields'])) {
            $query->where(function ($q) use ($config, $search) {
                foreach ($config['search_fields'] as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        $items = $query->orderBy('updated_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Statistik cepat untuk header
        $totalCount = $config['model']::count();
        $softDeletedCount = $config['model']::where('active', 0)->count();

        return view('admin.sieda-data.module', compact(
            'module', 'config', 'items', 'search', 'filterStatus', 'perPage',
            'totalCount', 'softDeletedCount'
        ));
    }

    /**
     * Detail satu record — lihat data lengkap
     */
    public function showRecord(string $module, string $id)
    {
        $this->authorizeSuperAdmin();

        $config = $this->resolveModule($module);
        if (!$config) {
            abort(404, 'Modul tidak ditemukan.');
        }

        $model = $config['model'];
        $primaryKey = $model::primaryKey();
        $item = $model::where($primaryKey, $id)->firstOrFail();

        return view('admin.sieda-data.show', compact('module', 'config', 'item'));
    }

    /**
     * Pulihkan data yang terhapus (active=0 → active=1)
     */
    public function restore(Request $request, string $module, string $id)
    {
        $this->authorizeSuperAdmin();

        $config = $this->resolveModule($module);
        if (!$config) {
            abort(404, 'Modul tidak ditemukan.');
        }

        $model = $config['model'];
        $primaryKey = $model::primaryKey();
        $item = $model::where($primaryKey, $id)->first();

        if (!$item) {
            return back()->with('error', 'Data tidak ditemukan.');
        }

        $item->update(['active' => 1]);

        Log::info('[SiedaData] Restore record', [
            'module' => $module,
            'id' => $id,
            'by' => auth()->id(),
        ]);

        return back()->with('success', 'Data berhasil dipulihkan.');
    }

    /**
     * Hapus PERMANEN (hard delete) — hanya super admin yang bisa
     *
     * INI OPERASI FINAL. Data tidak bisa dikembalikan. Pastikan sudah
     * backup sebelum menjalankan ini di produksi.
     */
    public function forceDelete(Request $request, string $module, string $id)
    {
        $this->authorizeSuperAdmin();

        $config = $this->resolveModule($module);
        if (!$config) {
            abort(404, 'Modul tidak ditemukan.');
        }

        $model = $config['model'];
        $primaryKey = $model::primaryKey();
        $item = $model::where($primaryKey, $id)->firstOrFail();

        DB::beginTransaction();
        try {
            $item->delete(); // Model tanpa SoftDeletes → ini hard-delete permanen
            DB::commit();

            Log::warning('[SiedaData] HARD DELETE permanen', [
                'module' => $module,
                'id' => $id,
                'by' => auth()->id(),
            ]);

            return back()->with('success', 'Data berhasil dihapus permanen.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[SiedaData] Force delete gagal', [
                'module' => $module,
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Gagal menghapus data. Silakan coba lagi.');
        }
    }

    /**
     * Resolve modul berdasarkan slug
     */
    private function resolveModule(string $module): ?array
    {
        return self::MODULES[$module] ?? null;
    }

    /**
     * Pastikan hanya super admin yang bisa mengelola data ini
     *
     * Menggunakan kolom sidongan_role (bukan role/permission generik) karena
     * fitur ini adalah operasi berisiko tinggi yang seharusnya terbatas
     * untuk administrator tingkat atas.
     */
    private function authorizeSuperAdmin(): void
    {
        if (auth()->user()->sidongan_role !== 'super_admin') {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk Super Admin.');
        }
    }
}
