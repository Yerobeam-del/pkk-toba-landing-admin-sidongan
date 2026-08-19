<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
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
 * Controller ini memungkinkan Super Admin untuk melihat dan menghapus
 * data dari aplikasi SIEDA (database db_sieda_app) secara PERMANEN.
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
            'with' => [],
            // Tabel yang mereferensikan tabel modul (foreign key) — wajib dibersihkan
            // lebih dulu agar penghapusan tidak diblokir constraint.
            'cascade' => ['catatan_kelahiran_kematian', 'tp_pkk_kegiatan_warga', 'tp_pkk_kader_dasawisma', 'tp_pkk_kegiatan_penduduk', 'tp_pkk_anggota_keluarga'],
            'cascade_label' => 'anggota keluarga, kader dasawisma, kegiatan warga, dan catatan ibu & anak',
        ],
        'keluarga' => [
            'model' => Keluarga::class,
            'label' => 'Data Keluarga',
            'id_field' => 'no_kk',
            'id_label' => 'Nomor KK',
            'display_fields' => ['no_kk', 'id_kepala_keluarga', 'id_kelompok_dasawisma', 'config_year'],
            'search_fields' => ['no_kk', 'no_registrasi_keluarga'],
            'with' => ['kepalaKeluarga', 'kelompokDasawisma'],
            'cascade' => ['catatan_kelahiran_kematian', 'tp_pkk_anggota_keluarga', 'tp_pkk_dasawisma_keluarga'],
            'cascade_label' => 'anggota keluarga, catatan ibu & anak, dan data dasawisma keluarga',
        ],
        'anggota-keluarga' => [
            'model' => AnggotaKeluarga::class,
            'label' => 'Anggota Keluarga',
            'id_field' => 'id',
            'id_label' => 'ID Record',
            'display_fields' => ['id', 'no_kk', 'nik'],
            'search_fields' => ['no_kk', 'nik'],
            'with' => [],
            'cascade' => [],
            'cascade_label' => '',
        ],
        'kelompok-dasawisma' => [
            'model' => KelompokDasawisma::class,
            'label' => 'Kelompok Dasawisma',
            'id_field' => 'id',
            'id_label' => 'ID',
            'display_fields' => ['id', 'nama', 'id_dusun', 'kader', 'config_year'],
            'search_fields' => ['nama', 'kader'],
            'with' => ['dusun'],
            'cascade' => ['catatan_kelahiran_kematian', 'tp_pkk_kader_dasawisma'],
            'cascade_label' => 'kader dasawisma dan catatan ibu & anak',
        ],
        'catatan-ibu-anak' => [
            'model' => CatatanKelahiranKematian::class,
            'label' => 'Catatan Ibu & Anak (Kelahiran / Kematian)',
            'id_field' => 'id',
            'id_label' => 'ID',
            'display_fields' => ['id', 'id_warga_ibu', 'status_ibu', 'tanggal_melahirkan', 'config_year'],
            'search_fields' => ['id_warga_ibu', 'nama_bayi', 'nama_meninggal'],
            'with' => [],
            'cascade' => [],
            'cascade_label' => '',
        ],
    ];

    /**
     * Dashboard overview — statistik semua modul
     */
    public function index(Request $request)
    {
        $this->authorizeSuperAdmin();

        $stats = collect(self::MODULES)->map(function ($config, $slug) {
            return [
                'slug' => $slug,
                'label' => $config['label'],
                'total' => $config['model']::count(),
                'aktif' => $config['model']::where('active', 1)->count(),
            ];
        });

        $totalKeseluruhan = $stats->sum('total');
        $totalAktif = $stats->sum('aktif');

        return view('admin.sieda-data.index', compact('stats', 'totalKeseluruhan', 'totalAktif'));
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

        $query = $config['model']::query();

        // Eager load relasi agar data_get di partial tabel tersedia
        if (!empty($config['with'])) {
            $query->with($config['with']);
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
        $totalAktif = $config['model']::where('active', 1)->count();

        return view('admin.sieda-data.module', compact(
            'module', 'config', 'items', 'search', 'perPage',
            'totalCount', 'totalAktif'
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
     * Hapus PERMANEN seluruh data pada satu modul — hanya super admin yang bisa
     *
     * INI OPERASI FINAL. Seluruh record pada tabel modul ini di database SIEDA
     * akan terhapus dan tidak bisa dikembalikan. Harus dikonfirmasi lewat
     * checkbox tersembunyi `confirm` (pola fitur cleanup di SidonganDataController).
     */
    public function deleteAll(Request $request, string $module)
    {
        $this->authorizeSuperAdmin();

        $config = $this->resolveModule($module);
        if (!$config) {
            abort(404, 'Modul tidak ditemukan.');
        }

        $request->validate([
            'confirm' => 'required|accepted',
        ], [
            'confirm.required' => 'Konfirmasi diperlukan untuk menghapus seluruh data.',
            'confirm.accepted' => 'Anda harus menyetujui konfirmasi sebelum menghapus seluruh data.',
        ]);

        $model = $config['model'];
        $count = $model::count();

        if ($count === 0) {
            return back()->with('info', 'Tidak ada data untuk dihapus pada modul ini.');
        }

        DB::beginTransaction();
        try {
            // Hapus dulu tabel anak yang mereferensikan tabel modul (foreign key),
            // agar penghapusan tidak diblokir constraint MySQL (SQLSTATE 23000/1451).
            // Urutan mengikuti dependensi tabel di database SIEDA (db_sieda_app).
            $cascadeCounts = [];
            foreach ($config['cascade'] as $table) {
                $cascadeCounts[$table] = DB::connection('sieda')->table($table)->delete();
            }

            $model::query()->delete(); // Model tanpa SoftDeletes → hard-delete permanen
            DB::commit();

            Log::warning('[SiedaData] HAPUS SEMUA data', [
                'module' => $module,
                'total' => $count,
                'cascade' => $cascadeCounts,
                'by' => auth()->id(),
            ]);

            return back()->with('success', number_format($count) . ' data ' . $config['label'] . ' beserta data terkait berhasil dihapus permanen dari database SIEDA.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[SiedaData] Delete all gagal', [
                'module' => $module,
                'error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Gagal menghapus seluruh data. Silakan coba lagi.');
        }
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
/* Dikembangkan oleh Institut Teknologi Del */
