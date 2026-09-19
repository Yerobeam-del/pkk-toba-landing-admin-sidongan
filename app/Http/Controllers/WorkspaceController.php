<?php

/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 *
 * Launcher "Pilih Ruang Kerja" — setelah login, user dengan wewenang
 * lebih dari satu area diberi PILIHAN AREA KERJA ADMIN PANEL. Kartu
 * tampil SESUAI ROLE/ASSIGNMENT masing-masing user:
 *
 *   - Admin Panel PKK      : kelola landing page TP-PKK kabupaten
 *                            (berita, pengurus, slider, SK, aplikasi).
 *   - Admin Panel Desa     : kelola landing page desa yang DI-ASSIGN
 *                            di Manajemen Akun (sieda_kelurahan) —
 *                            dijalankan di aplikasi SIEDA via SSO.
 *   - Manajemen Akun       : kelola pengguna & assignment role/desa
 *                            (permission manage-users).
 *   - Manajemen Data SIEDA : kelola database SIEDA (super admin).
 *   - Manajemen Data SIDONGAN : kelola database SIDONGAN (super admin).
 *
 * Aplikasi SIDONGAN (surat-menyurat) BUKAN pilihan ruang kerja — aksesnya
 * murni mengikuti sidongan_role yang ditetapkan di Manajemen Akun.
 *
 * Pilihan disimpan ke users.workspace dan dipakai ulang sebagai tujuan
 * redirect otomatis pada login berikutnya; tetap bisa diganti kapan saja
 * lewat menu "Ganti Ruang Kerja".
 * ============================================================ */
namespace App\Http\Controllers;

use App\Models\Desa;
use App\Services\SsoTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkspaceController extends Controller
{
    /** Nilai workspace yang sah di kolom users.workspace. */
    public const VALID = ['pkk', 'desa', 'akun', 'data-sieda', 'data-sidongan', 'sidongan'];

    public function pilih(Request $request)
    {
        $user = Auth::guard('web')->user();

        // Desa milik akun: assignment eksplisit (sieda_desas / sieda_kelurahan)
        // atau seluruh desa untuk pengelola lintas desa. Kartu Admin Panel
        // Desa menyesuaikan: 1 desa → langsung masuk; >1 → langkah pilih desa.
        $desaMilik = $this->desaMilikAkun($user);

        return view('workspace.pilih', [
            'user'          => $user,
            'bisaPkk'       => $this->bisaPkk($user),
            'bisaDesa'      => $desaMilik->isNotEmpty(),
            'bisaAkun'      => $this->bisaKelolaUser($user),
            'bisaDataSieda' => $this->bisaData($user),
            'desaAssign'    => $desaMilik->count() === 1 ? $desaMilik->first()->nama : null,
            'multiDesa'     => $desaMilik->count() > 1,
        ]);
    }

    /**
     * Langkah kedua kartu Admin Panel Desa: pengelola multi-desa memilih
     * desa mana yang landing page-nya mau diatur, lalu masuk ke sana.
     */
    public function pilihDesa(Request $request)
    {
        $user = Auth::guard('web')->user();
        $desaMilik = $this->desaMilikAkun($user);

        abort_unless($desaMilik->count() > 1, 403, 'Akun ini tidak mengelola lebih dari satu desa.');

        return view('workspace.pilih-desa', [
            'user'         => $user,
            'desas'        => $desaMilik->groupBy('kecamatan'),
            'desaTerpilih' => $this->desaTerpilih($user),
        ]);
    }

    public function simpan(Request $request)
    {
        $user = Auth::guard('web')->user();

        $data = $request->validate([
            'workspace'       => 'required|in:' . implode(',', self::VALID),
            'workspace_desa'  => 'nullable|string|max:20',
        ]);

        // Desa terpilih hanya relevan (dan hanya disimpan) untuk area Desa.
        // Satu desa di assignment → kode diisi otomatis; multi-desa → kode
        // wajib salah satu dari daftar desa milik akun.
        if ($data['workspace'] === 'desa') {
            $milik = $this->desaMilikAkun($user);
            $kode  = $data['workspace_desa'] ?? null;

            if ($milik->count() === 1) {
                $kode = $milik->first()->kode;
            } else {
                $sah = $kode && $milik->contains('kode', $kode);
                abort_unless((bool) $sah, 422, 'Desa tidak valid.');
            }

            $user->update(['workspace_desa' => $kode]);
        }

        $user->update(['workspace' => $data['workspace']]);

        return redirect()->to($this->url($data['workspace'], $user));
    }

    /**
     * Login satu klik ke SIDONGAN — kini hanya fallback untuk akun yang
     * masih menyimpan workspace='sidongan' dari perilaku lama (tidak lagi
     * ditampilkan sebagai kartu pilihan ruang kerja).
     * Kredensial tidak diketik ulang — sumber kebenarannya sesi web ini.
     * Syarat sama dengan SidonganAuthenticate: sidongan_role terisi
     * (termasuk 'super_admin').
     */
    public function masukSidongan(Request $request)
    {
        $user = Auth::guard('web')->user();

        abort_unless($user && filled($user->sidongan_role), 403, 'Akun ini tidak memiliki akses SIDONGAN.');

        Auth::guard('sidongan')->login($user);
        $request->session()->regenerate();

        $user->update(['workspace' => 'sidongan']);

        return redirect()->route('sidongan.dashboard');
    }

    /** URL tujuan ruang kerja tertentu. $user dipakai untuk konteks (desa terpilih). */
    public static function url(string $workspace, $user = null): string
    {
        return match ($workspace) {
            'pkk'           => route('admin.dashboard'),
            // Handoff ke SIEDA membawa desa terpilih pengelola lintas desa —
            // SIEDA membuka Admin Panel langsung pada konteks desa itu.
            'desa'          => route('sso.back', array_filter([
                'to'   => 'desa',
                'desa' => $user?->workspace_desa ?? null,
            ])),
            'akun'          => route('admin.user-management.index'),
            'data-sieda'    => route('admin.sieda-data.index'),
            'data-sidongan' => route('admin.sidongan-data.index'),
            'sidongan'      => route('workspace.sidongan'),
            default         => route('workspace.pilih'),
        };
    }

    // ==================== GATE PER AREA ====================

    /** Admin Panel PKK: project ini adalah rumahnya — semua akun PKK. */
    private function bisaPkk($user): bool
    {
        return (bool) $user;
    }

    /** Manajemen Akun: super admin ATAU pemegang permission manage-users. */
    private function bisaKelolaUser($user): bool
    {
        return $user && ($user->isSuperAdmin() || $user->hasPermission('manage-users'));
    }

    /**
     * Manajemen Data (SIEDA/SIDONGAN): hapus permanen data database —
     * super admin saja, konsisten dengan middleware route-nya
     * (permission:manage-users yang efektif hanya dimiliki super admin).
     */
    private function bisaData($user): bool
    {
        return $user && $user->isSuperAdmin();
    }

    // ==================== DESA MILIK AKUN (ASSIGNMENT) ====================

    /**
     * Daftar desa yang BOLEH dikelola akun ini — sumber kebenarannya
     * assignment di Manajemen Akun:
     *
     *   1. sieda_desas (JSON multi-desa) bila terisi — hasil multi-pilih
     *      di form Manajemen Akun; sieda_kelurahan tetap desa utama.
     *   2. sieda_kelurahan saja (perilaku lama, satu desa).
     *   3. Super admin / operator kecamatan TANPA assignment desa →
     *      pengelola lintas desa: semua desa (kecamatan-scoped bila ada).
     *
     * Nama desa & kecamatan dari ref_desa database SIEDA agar konsisten
     * dengan yang dikenal aplikasi SIEDA; fallback ke tabel desas lokal.
     *
     * @return \Illuminate\Support\Collection<int, object{kode:string,nama:string,kecamatan:string}>
     */
    private function desaMilikAkun($user)
    {
        if (!$user) {
            return collect();
        }

        // 1-2. Assignment eksplisit (multi atau satu desa).
        $kodeAssign = collect((array) ($user->sieda_desas ?? []))
            ->filter(fn ($k) => is_string($k) && preg_match('/^12\.12\.\d{2}\.\d{4}$/', $k))
            ->unique()
            ->values();

        if ($kodeAssign->isEmpty() && filled($user->sieda_kelurahan)) {
            $kodeAssign = collect([(string) $user->sieda_kelurahan]);
        }

        if ($kodeAssign->isNotEmpty()) {
            return $this->lengkapiNamaDesa($kodeAssign);
        }

        // 3. Pengelola lintas desa: hanya bila benar-benar punya akses SIEDA
        //    dan tidak di-assign desa mana pun.
        if (!filled($user->sieda_role)) {
            return collect();
        }

        $kodeKecamatan = $user->sieda_kecamatan;

        try {
            $query = \DB::connection('sieda')->table('ref_desa')
                ->select('kode', 'nama')
                ->where('kode', 'like', '12.12.%');

            if (filled($kodeKecamatan)) {
                $query->where('kode', 'like', $kodeKecamatan . '%');
            }

            $daftar = $query->orderBy('nama')->get();

            if ($daftar->isEmpty()) {
                return collect();
            }

            $kecamatan = \DB::connection('sieda')->table('ref_kecamatan')
                ->select('kode', 'nama')
                ->where('kode', 'like', '12.12.%')
                ->pluck('nama', 'kode');

            return $daftar->map(fn ($d) => (object) [
                'kode'      => $d->kode,
                'nama'      => $d->nama,
                'kecamatan' => $kecamatan[substr($d->kode, 0, 8)] ?? '',
            ]);
        } catch (\Throwable $e) {
            // Fallback di bawah.
        }

        // Fallback: tabel desas lokal (nama + kode wilayah).
        $queryLokal = Desa::query()->select('name', 'kode_wilayah');
        if (filled($kodeKecamatan)) {
            $queryLokal->where('kode_wilayah', 'like', $kodeKecamatan . '%');
        }

        return $queryLokal->orderBy('name')->get()
            ->map(fn ($d) => (object) ['kode' => $d->kode_wilayah, 'nama' => $d->name, 'kecamatan' => ''])
            ->filter(fn ($d) => filled($d->kode));
    }

    /**
     * Lengkapi daftar kode assignment dengan nama desa & kecamatan.
     * Desa yang tidak dikenal (kode tidak ada di referensi) dibuang.
     */
    private function lengkapiNamaDesa($kodeList)
    {
        try {
            $rows = \DB::connection('sieda')->table('ref_desa')
                ->select('kode', 'nama')
                ->whereIn('kode', $kodeList->all())
                ->get();

            if ($rows->isNotEmpty()) {
                $kecamatan = \DB::connection('sieda')->table('ref_kecamatan')
                    ->select('kode', 'nama')
                    ->where('kode', 'like', '12.12.%')
                    ->pluck('nama', 'kode');

                // Urutan mengikuti assignment (desa utama di depan).
                return $kodeList->map(function ($kode) use ($rows, $kecamatan) {
                    $row = $rows->firstWhere('kode', $kode);
                    if (!$row) {
                        return null;
                    }

                    return (object) [
                        'kode'      => $row->kode,
                        'nama'      => $row->nama,
                        'kecamatan' => $kecamatan[substr($row->kode, 0, 8)] ?? '',
                    ];
                })->filter()->values();
            }
        } catch (\Throwable $e) {
            // Fallback di bawah.
        }

        // Fallback: tabel desas lokal.
        return $kodeList->map(function ($kode) {
            $nama = Desa::where('kode_wilayah', $kode)->value('name');

            return $nama ? (object) ['kode' => $kode, 'nama' => $nama, 'kecamatan' => ''] : null;
        })->filter()->values();
    }

    /** Desa yang sedang terpilih (tersimpan di users.workspace_desa). */
    private function desaTerpilih($user): ?string
    {
        return $user->workspace_desa ?? null;
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
