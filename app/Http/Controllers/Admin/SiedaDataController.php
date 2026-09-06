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
use Illuminate\Database\QueryException;
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
            'cascade' => [
                ['table' => 'catatan_kelahiran_kematian', 'columns' => ['id_warga_ibu', 'id_warga_suami']],
                ['table' => 'tp_pkk_kegiatan_warga', 'columns' => ['nik', 'id_warga', 'id_penduduk']],
                ['table' => 'tp_pkk_kader_dasawisma', 'columns' => ['nik', 'id_warga', 'id_kader']],
                ['table' => 'tp_pkk_kegiatan_penduduk', 'columns' => ['nik', 'id_warga', 'id_penduduk']],
                ['table' => 'tp_pkk_anggota_keluarga', 'columns' => ['nik']],
            ],
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
            'cascade' => [
                ['table' => 'catatan_kelahiran_kematian', 'columns' => ['no_kk']],
                ['table' => 'tp_pkk_anggota_keluarga', 'columns' => ['no_kk']],
                ['table' => 'tp_pkk_dasawisma_keluarga', 'columns' => ['no_kk']],
            ],
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
            'cascade' => [
                ['table' => 'catatan_kelahiran_kematian', 'columns' => ['id_group_dasawisma', 'id_kelompok_dasawisma']],
                ['table' => 'tp_pkk_kader_dasawisma', 'columns' => ['id_group_dasawisma', 'id_kelompok_dasawisma', 'id_dasawisma']],
            ],
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

        try {
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
        } catch (QueryException $e) {
            return $this->handleSiedaConnectionError($e);
        }

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

        try {
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
        } catch (QueryException $e) {
            return $this->handleSiedaConnectionError($e, $config['label']);
        }

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

        try {
            $model = $config['model'];
            $primaryKey = $model::primaryKey();
            $item = $model::where($primaryKey, $id)->firstOrFail();
        } catch (QueryException $e) {
            return $this->handleSiedaConnectionError($e, $config['label']);
        }

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

        $sieda = DB::connection('sieda');
        $sieda->beginTransaction();
        try {
            // Hapus dulu tabel anak yang mereferensikan tabel modul (foreign key),
            // agar penghapusan tidak diblokir constraint MySQL (SQLSTATE 23000/1451).
            // Urutan mengikuti dependensi tabel di database SIEDA (db_sieda_app).
            $cascadeCounts = $this->deleteRelatedRecords(
                $sieda,
                $config['cascade'],
                $model,
                $model::primaryKey()
            );

            $model::query()->delete(); // Model tanpa SoftDeletes → hard-delete permanen
            $sieda->commit();

            Log::warning('[SiedaData] HAPUS SEMUA data', [
                'module' => $module,
                'total' => $count,
                'cascade' => $cascadeCounts,
                'by' => auth()->id(),
            ]);

            return back()->with('success', number_format($count) . ' data ' . $config['label'] . ' beserta data terkait berhasil dihapus permanen dari database SIEDA.');
        } catch (\Exception $e) {
            $sieda->rollBack();
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

        $sieda = DB::connection('sieda');
        $sieda->beginTransaction();
        try {
            $cascadeCounts = $this->deleteRelatedRecords(
                $sieda,
                $config['cascade'],
                $model,
                $primaryKey,
                $id
            );
            $item->delete(); // Model tanpa SoftDeletes → ini hard-delete permanen
            $sieda->commit();

            Log::warning('[SiedaData] HARD DELETE permanen', [
                'module' => $module,
                'id' => $id,
                'cascade' => $cascadeCounts,
                'by' => auth()->id(),
            ]);

            return back()->with('success', 'Data berhasil dihapus permanen.');
        } catch (\Exception $e) {
            $sieda->rollBack();
            Log::error('[SiedaData] Force delete gagal', [
                'module' => $module,
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Gagal menghapus data. Silakan coba lagi.');
        }
    }

    /**
     * Hapus record terkait hanya jika kolom relasinya cocok dengan modul target.
     *
     * Jangan pernah menghapus seluruh tabel cascade: modul SIEDA berbagi tabel
     * relasi, sehingga delete-all pada satu modul tidak boleh menyapu data modul
     * lain. Relasi yang belum dikenal sengaja dilewati (dan dicatat) agar gagal
     * aman, bukan menghapus terlalu banyak data.
     */
    private function deleteRelatedRecords($connection, array $relations, string $model, string $primaryKey, ?string $id = null): array
    {
        $counts = [];
        $targetSubquery = $model::query()->select($primaryKey);

        foreach ($relations as $relation) {
            $table = $relation['table'];
            $availableColumns = $connection->getSchemaBuilder()->getColumnListing($table);
            $columns = array_values(array_intersect($relation['columns'], $availableColumns));

            if ($columns === []) {
                $counts[$table] = 0;
                Log::warning('[SiedaData] Relasi cascade dilewati karena kolom tidak ditemukan', [
                    'table' => $table,
                    'expected_columns' => $relation['columns'],
                ]);
                continue;
            }

            $query = $connection->table($table)->where(function ($query) use ($columns, $targetSubquery, $id) {
                foreach ($columns as $column) {
                    if ($id === null) {
                        $query->orWhereIn($column, $targetSubquery);
                    } else {
                        $query->orWhere($column, $id);
                    }
                }
            });

            $counts[$table] = $query->delete();
        }

        return $counts;
    }

    /**
     * Resolve modul berdasarkan slug
     */
    private function resolveModule(string $module): ?array
    {
        return self::MODULES[$module] ?? null;
    }

    /**
     * Tampilkan halaman ramah saat database SIEDA tidak dapat dihubungi
     */
    private function handleSiedaConnectionError(\Throwable $e, string $context = ''): \Illuminate\View\View
    {
        Log::error('[SiedaData] Database SIEDA tidak terhubung', [
            'error' => $e->getMessage(),
            'context' => $context,
        ]);

        $message = $e instanceof QueryException
            ? 'SQLSTATE: ' . $e->getCode() . ' — ' . class_basename($e)
            : $e->getMessage();

        return view('admin.sieda-data.connection-error', compact('message'));
    }

    /**
     * Pastikan hanya super admin yang bisa mengelola data ini
     *
     * Fitur ini adalah operasi berisiko tinggi yang seharusnya terbatas
     * untuk administrator tingkat atas. Status Super Admin dicek lewat
     * satu pintu: User::isSuperAdmin() (lihat app/Models/User.php).
     */
    private function authorizeSuperAdmin(): void
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk Super Admin.');
        }
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
