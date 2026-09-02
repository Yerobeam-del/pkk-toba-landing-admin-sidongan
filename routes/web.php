<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Landing\NewsController;
use App\Http\Controllers\Sidongan\AdminDocumentController;
use App\Http\Controllers\Sidongan\CategoryController;
use App\Http\Controllers\Sidongan\TagController;

// ================= GLOBAL API ROUTES =================
Route::get('/api/test-connection', function () {
    return response()->json([
        'success' => true,
        'message' => 'API pkk-toba berhasil terhubung',
        'timestamp' => now()->toDateTimeString()
    ]);
});

Route::get('/api/v1/health', function () {
    return response()->json(['status' => 'ok']);
});

// ================= API WILAYAH (GLOBAL, LINTAS SUBDOMAIN) =================
// Sumber datanya tabel `wilayah` di database, BUKAN API wilayah.id, supaya
// tidak ikut bermasalah saat layanan luar itu terganggu.
//
// Sengaja didaftarkan di luar grup domain: SIDONGAN berjalan di
// sidongan.tobakab.go.id dan memanggil endpoint ini secara relatif. Ketika route
// masih berada di dalam grup tp-pkk.tobakab.go.id, panggilan dari SIDONGAN menghasilkan
// 404 sehingga dropdown wilayah menampilkan "Gagal memuat data".

Route::get('/api/v1/wilayah/provinces', function () {
    try {
        $provinces = \App\Models\Wilayah::where('kode', 'like', '__')->where('kode', 'not like', '%.%')->orderBy('nama')->get(['kode', 'nama'])->map(fn($p) => ['code' => $p->kode, 'name' => $p->nama]);
        return response()->json(['success' => true, 'data' => $provinces]);
    } catch (\Exception $e) { return response()->json(['success' => false, 'message' => $e->getMessage()], 500); }
});
Route::get('/api/v1/wilayah/regencies/{provinceCode}', function ($provinceCode) {
    try {
        $regencies = \App\Models\Wilayah::where('kode', 'like', $provinceCode . '.%')->where('kode', 'not like', '%.__.%')->where('kode', 'not like', '%.__.__.%')->orderBy('nama')->get(['kode', 'nama'])->map(fn($r) => ['code' => $r->kode, 'name' => $r->nama]);
        return response()->json(['success' => true, 'data' => $regencies]);
    } catch (\Exception $e) { return response()->json(['success' => false, 'message' => $e->getMessage()], 500); }
});
Route::get('/api/v1/wilayah/districts/{regencyCode}', function ($regencyCode) {
    try {
        $districts = \App\Models\Wilayah::where('kode', 'like', $regencyCode . '.%')->where('kode', 'not like', '%.__.__.%')->orderBy('nama')->get(['kode', 'nama'])->map(fn($d) => ['code' => $d->kode, 'name' => $d->nama]);
        return response()->json(['success' => true, 'data' => $districts]);
    } catch (\Exception $e) { return response()->json(['success' => false, 'message' => $e->getMessage()], 500); }
});
Route::get('/api/v1/wilayah/villages/{districtCode}', function ($districtCode) {
    try {
        $villages = \App\Models\Wilayah::where('kode', 'like', $districtCode . '.%')->orderBy('nama')->get(['kode', 'nama'])->map(fn($v) => ['code' => $v->kode, 'name' => $v->nama]);
        return response()->json(['success' => true, 'data' => $villages]);
    } catch (\Exception $e) { return response()->json(['success' => false, 'message' => $e->getMessage()], 500); }
});

// ======================================================================
// 1. ROUTES KHUSUS DOMAIN: tp-pkk.tobakab.go.id (Landing Page & Admin Panel)
// ======================================================================
Route::domain(config('app.landing_domain'))->group(function () {

    // ================= API: LANDING PAGE =================
    Route::get('/api/v1/news', function (Request $request) {
        try {
            $limit = min(max((int) $request->get('limit', 6), 1), 50);
            $sort = $request->get('sort', 'latest');
            $query = \App\Models\News::published()->reorder();

            switch ($sort) {
                case 'oldest': $query->orderBy('published_at', 'asc'); break;
                case 'title_asc': $query->orderBy('title', 'asc'); break;
                case 'title_desc': $query->orderBy('title', 'desc'); break;
                default: $query->orderBy('published_at', 'desc'); break;
            }

            $news = $query->paginate($limit);
            $formattedData = $news->map(function($item) {
                return [
                    'id' => $item->id, 'slug' => $item->slug, 'title' => $item->title,
                    'category' => $item->category, 'excerpt' => $item->excerpt, 'content' => $item->content,
                    'image_path' => $item->image_path, 'image' => $item->image_path ? asset('storage/' . $item->image_path) : null,
                    'published_at' => $item->published_at, 'created_at' => $item->created_at,
                    'date' => $item->published_at?->translatedFormat('d F Y') ?? $item->created_at->translatedFormat('d F Y'),
                ];
            });

            return response()->json([
                'success' => true, 'data' => $formattedData, 'total' => $news->total(),
                'last_page' => $news->lastPage(), 'current_page' => $news->currentPage(),
                'per_page' => $news->perPage(), 'from' => $news->firstItem(), 'to' => $news->lastItem(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    });

    Route::get('/api/v1/news/{slug}', function ($slug) {
        try {
            $news = \App\Models\News::published()->where('slug', $slug)->firstOrFail();
            return response()->json([
                'success' => true, 'data' => [
                    'id' => $news->id, 'slug' => $news->slug, 'title' => $news->title,
                    'category' => $news->category, 'excerpt' => $news->excerpt, 'content' => $news->content,
                    'image_path' => $news->image_path, 'image' => $news->image_path ? asset('storage/' . $news->image_path) : null,
                    'published_at' => $news->published_at, 'date' => $news->published_at?->translatedFormat('d F Y') ?? $news->created_at->translatedFormat('d F Y'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Berita tidak ditemukan'], 404);
        }
    });

    Route::get('/api/v1/struktur', function () {
        try {
            $pengurusInti = \App\Models\StrukturMember::whereNull('pokja_id')->active()->orderBy('sort_order')->get()
                ->map(fn($m) => ['id' => $m->id, 'name' => $m->name, 'position' => $m->position, 'photo' => $m->photo_path ? asset('storage/' . $m->photo_path) : null, 'description' => $m->description]);
            $pokja = \App\Models\Pokja::active()->with(['members' => fn($q) => $q->active()])->orderBy('sort_order')->get()
                ->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'slug' => $p->slug, 'description' => $p->description, 'members' => $p->members->map(fn($m) => ['id' => $m->id, 'name' => $m->name, 'position' => $m->position, 'photo' => $m->photo_path ? asset('storage/' . $m->photo_path) : null, 'description' => $m->description])]);
            return response()->json(['success' => true, 'data' => ['pengurus_inti' => $pengurusInti, 'pokja' => $pokja]]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    });

    Route::get('/api/v1/applications', function () {
        try {
            // Aplikasi berstatus 'development' selalu di-set is_active = 0 oleh
            // ApplicationController, jadi flag itu hanya relevan untuk status
            // 'active' dan 'maintenance'.
            $allApps = \App\Models\Application::whereIn('category', ['aplikasi', 'layanan'])
                ->where(function ($q) {
                    $q->where(function ($sub) {
                        $sub->whereIn('status', ['active', 'maintenance'])->where('is_active', true);
                    })->orWhere('status', 'development');
                })
                ->orderBy('category')
                ->orderBy('sort_order')
                ->get();

            $mapApp = fn($app) => [
                'id' => $app->id,
                'name' => $app->name,
                'short_name' => $app->short_name,
                'description' => $app->description,
                'url' => $app->url,
                'icon' => $app->icon,
                'color' => $app->effective_color, // Warna pilihan admin, atau default PKK
                'category' => $app->category,
                'status' => $app->status,
                'status_label' => $app->status_label,
                'features' => $app->features ?? [],
                'color_index' => $app->id % 10, // Untuk variasi warna (0-9)
                'show_in_quick_access' => (bool) $app->show_in_quick_access, // Dipakai section beranda
            ];

            $groupedData = [];
            foreach (['aplikasi', 'layanan'] as $category) {
                $byCategory = $allApps->where('category', $category);
                foreach (['active', 'maintenance', 'development'] as $status) {
                    $groupedData[$category][$status] = $byCategory
                        ->where('status', $status)
                        ->map($mapApp)
                        ->values();
                }
            }

            return response()->json(['success' => true, 'data' => $groupedData]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    });

    Route::get('/api/v1/kecamatans', function (App\Services\WilayahIndonesiaService $service) {
        try {
            if (\App\Models\Kecamatan::count() === 0) $service->syncKecamatansToba();
            $kecamatans = \App\Models\Kecamatan::select('id', 'name', 'kode_wilayah')->orderBy('name')->get()->map(fn($k) => ['id' => $k->id, 'name' => $k->name, 'code' => $k->kode_wilayah]);
            return response()->json(['success' => true, 'data' => $kecamatans]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memuat data kecamatan'], 500);
        }
    });

    Route::get('/api/v1/desas', function () {
        try {
            $kecamatans = \App\Models\Kecamatan::with(['activeDesas' => function($q) {
                $q->select('id', 'kecamatan_id', 'name', 'kode_wilayah', 'description', 'image', 'population', 'households', 'sort_order', 'is_active')->orderBy('sort_order');
            }])->orderBy('name')->get()->map(function($kec) {
                return ['id' => $kec->id, 'name' => $kec->name, 'desas' => $kec->activeDesas->map(function($desa) {
                    return ['id' => $desa->id, 'name' => $desa->name, 'kode_wilayah' => $desa->kode_wilayah, 'description' => $desa->description, 'image' => $desa->image ? asset('storage/' . $desa->image) : null, 'population' => $desa->population, 'households' => $desa->households, 'sort_order' => $desa->sort_order, 'is_active' => $desa->is_active];
                })];
            });
            return response()->json(['success' => true, 'data' => $kecamatans]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memuat data desa: ' . $e->getMessage()], 500);
        }
    });

    // Proxy Wilayah.id
    Route::get('/api/v1/wilayah/proxy/provinces', function () {
        try {
            $provinces = cache()->remember('wilayah_provinces', 86400, fn() => Http::timeout(30)->get('https://wilayah.id/api/provinces.json')->json()['data'] ?? []);
            return response()->json(['success' => true, 'data' => $provinces]);
        } catch (\Exception $e) { return response()->json(['success' => false, 'message' => $e->getMessage()], 500); }
    });
    Route::get('/api/v1/wilayah/proxy/regencies/{provinceCode}', function ($provinceCode) {
        try {
            $regencies = cache()->remember('wilayah_regencies_' . $provinceCode, 86400, fn() => Http::timeout(30)->get("https://wilayah.id/api/regencies/{$provinceCode}.json")->json()['data'] ?? []);
            return response()->json(['success' => true, 'data' => $regencies]);
        } catch (\Exception $e) { return response()->json(['success' => false, 'message' => $e->getMessage()], 500); }
    });
    Route::get('/api/v1/wilayah/proxy/districts/{regencyCode}', function ($regencyCode) {
        try {
            $districts = cache()->remember('wilayah_districts_' . $regencyCode, 86400, fn() => Http::timeout(30)->get("https://wilayah.id/api/districts/{$regencyCode}.json")->json()['data'] ?? []);
            return response()->json(['success' => true, 'data' => $districts]);
        } catch (\Exception $e) { return response()->json(['success' => false, 'message' => $e->getMessage()], 500); }
    });
    Route::get('/api/v1/wilayah/proxy/villages/{districtCode}', function ($districtCode) {
        try {
            $villages = cache()->remember('wilayah_villages_' . $districtCode, 86400, fn() => Http::timeout(30)->get("https://wilayah.id/api/villages/{$districtCode}.json")->json()['data'] ?? []);
            return response()->json(['success' => true, 'data' => $villages]);
        } catch (\Exception $e) { return response()->json(['success' => false, 'message' => $e->getMessage()], 500); }
    });

    Route::get('/api/v1/dokumens', function (Request $request) {
        try {
            $perPage = min(max((int) $request->get('per_page', 5), 1), 50);
            $search = $request->get('search', '');
            $query = \App\Models\Dokumen::published();
            if ($search) {
                $searchTerm = strtolower($search);
                $query->where(function($q) use ($searchTerm) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . $searchTerm . '%'])->orWhereRaw('LOWER(file_name) LIKE ?', ['%' . $searchTerm . '%']);
                });
            }
            $dokumens = $query->orderBy('document_date', 'desc')->orderBy('sort_order')->paginate($perPage);
            $formattedData = $dokumens->map(function($doc) {
                return ['id' => $doc->id, 'name' => $doc->name, 'file_name' => $doc->file_name, 'file_size' => $doc->file_size, 'file_url' => $doc->file_url, 'file_type' => $doc->file_type, 'document_date' => $doc->document_date?->format('Y-m-d'), 'formatted_date' => $doc->document_date?->translatedFormat('d F Y'), 'status' => $doc->status];
            });
            return response()->json(['success' => true, 'data' => $formattedData, 'pagination' => ['current_page' => $dokumens->currentPage(), 'last_page' => $dokumens->lastPage(), 'per_page' => $dokumens->perPage(), 'total' => $dokumens->total(), 'from' => $dokumens->firstItem(), 'to' => $dokumens->lastItem()]]);
        } catch (\Exception $e) { return response()->json(['success' => false, 'message' => $e->getMessage()], 500); }
    });

    Route::get('/api/v1/templates', function (Request $request) {
        try {
            $perPage = min(max((int) $request->get('per_page', 6), 1), 50);
            $search = $request->get('search', '');
            $query = \App\Models\Template::published();
            if ($search) {
                $searchTerm = strtolower($search);
                $query->where(function($q) use ($searchTerm) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . $searchTerm . '%'])->orWhereRaw('LOWER(file_name) LIKE ?', ['%' . $searchTerm . '%']);
                });
            }
            $templates = $query->orderBy('upload_date', 'desc')->orderBy('sort_order')->paginate($perPage);
            $formattedData = $templates->map(function($t) {
                return ['id' => $t->id, 'name' => $t->name, 'file_name' => $t->file_name, 'file_size' => $t->file_size, 'file_url' => asset('storage/' . $t->file_path), 'file_path' => $t->file_path, 'file_type' => $t->file_type, 'upload_date' => $t->upload_date?->format('Y-m-d'), 'formatted_date' => $t->upload_date?->translatedFormat('d F Y'), 'status' => $t->status, 'description' => $t->description ?? null];
            });
            return response()->json(['success' => true, 'data' => $formattedData, 'pagination' => ['current_page' => $templates->currentPage(), 'last_page' => $templates->lastPage(), 'per_page' => $templates->perPage(), 'total' => $templates->total(), 'from' => $templates->firstItem(), 'to' => $templates->lastItem()]]);
        } catch (\Exception $e) { return response()->json(['success' => false, 'message' => $e->getMessage()], 500); }
    });

    Route::get('/api/v1/tentang', function () {
        return response()->json(['success' => true, 'data' => \App\Models\TentangKami::getFirst()]);
    });

    Route::get('/api/v1/hero-slider', function () {
        $sliders = \App\Models\HeroSlider::active()->get()->map(fn($s) => ['id' => $s->id, 'image_url' => $s->image_url, 'display_duration' => $s->display_duration ?? 5]);
        $settings = file_exists(storage_path('app/hero_slider_settings.json')) ? json_decode(file_get_contents(storage_path('app/hero_slider_settings.json')), true) : ['auto_play' => true, 'transition_duration' => 500, 'show_arrows' => false, 'show_dots' => true];
        return response()->json(['success' => true, 'data' => $sliders, 'settings' => $settings]);
    });

    // ================= WEB: LANDING PAGE =================
    Route::get('/', function () {
        return view('modules.landing.home');
    })->name('landing.home');

    Route::get('/berita', function () {
        return redirect('/#berita');
    })->name('landing.berita');

    Route::get('/berita/{slug}', [NewsController::class, 'show'])->name('news.show');

    // ================= WEB: ADMIN PANEL =================
    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
        Route::get('/profile/password', [App\Http\Controllers\ProfileController::class, 'password'])->name('profile.password');
        Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');
        Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::prefix('hero-sliders')->name('hero-sliders.')->middleware('permission:manage-hero-slider')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\HeroSliderController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\HeroSliderController::class, 'store'])->name('store');
            Route::put('/{heroSlider}', [App\Http\Controllers\Admin\HeroSliderController::class, 'update'])->name('update');
            Route::delete('/{heroSlider}', [App\Http\Controllers\Admin\HeroSliderController::class, 'destroy'])->name('destroy');
            Route::post('/reorder', [App\Http\Controllers\Admin\HeroSliderController::class, 'updateOrder'])->name('reorder');
            Route::post('/settings', [App\Http\Controllers\Admin\HeroSliderController::class, 'updateSettings'])->name('settings');
        });

        Route::resource('struktur', App\Http\Controllers\Admin\StrukturController::class)->middleware('permission:manage-struktur');
        Route::get('/admin/struktur/tab/{tab}', [App\Http\Controllers\Admin\StrukturController::class, 'getTabData'])->name('admin.struktur.tab');
        Route::resource('aplikasi', App\Http\Controllers\Admin\ApplicationController::class)->middleware('permission:manage-aplikasi');
        Route::resource('berita', App\Http\Controllers\Admin\BeritaController::class)->middleware('permission:manage-berita');
        Route::delete('/berita/{beritum}/image/{image}', [App\Http\Controllers\Admin\BeritaController::class, 'deleteImage'])->name('berita.delete-image');

        Route::prefix('desa')->name('desa.')->middleware('permission:manage-desa')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\DesaController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\DesaController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\DesaController::class, 'store'])->name('store');
            Route::get('/{desa}/edit', [App\Http\Controllers\Admin\DesaController::class, 'edit'])->name('edit');
            Route::put('/{desa}', [App\Http\Controllers\Admin\DesaController::class, 'update'])->name('update');
            Route::delete('/{desa}', [App\Http\Controllers\Admin\DesaController::class, 'destroy'])->name('destroy');
            Route::get('/max-sort-order', [App\Http\Controllers\Admin\DesaController::class, 'getMaxSortOrder'])->name('max-sort-order');
            Route::post('/kecamatan', [App\Http\Controllers\Admin\DesaController::class, 'storeKecamatan'])->name('kecamatan.store');
            Route::put('/kecamatan/{kecamatan}', [App\Http\Controllers\Admin\DesaController::class, 'updateKecamatan'])->name('kecamatan.update');
            Route::delete('/kecamatan/{kecamatan}', [App\Http\Controllers\Admin\DesaController::class, 'destroyKecamatan'])->name('kecamatan.destroy');
        });

        Route::resource('sk', App\Http\Controllers\Admin\DokumenController::class)->parameters(['sk' => 'dokumen'])->middleware('permission:manage-dokumen');
        Route::resource('template', App\Http\Controllers\Admin\TemplateController::class)->middleware('permission:manage-template');

        Route::get('/tentang', [App\Http\Controllers\Admin\TentangKamiController::class, 'index'])->name('tentang.index')->middleware('permission:manage-tentang');
        Route::post('/tentang/update', [App\Http\Controllers\Admin\TentangKamiController::class, 'update'])->name('tentang.update')->middleware('permission:manage-tentang');

        Route::prefix('user-management')->name('user-management.')->middleware('permission:manage-users')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\UserManagementController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\UserManagementController::class, 'store'])->name('store');
            Route::get('/check-email', [App\Http\Controllers\Admin\UserManagementController::class, 'checkEmail'])->name('check-email');
            Route::get('/export', [App\Http\Controllers\Admin\UserManagementController::class, 'export'])->name('export');
            Route::post('/bulk-action', [App\Http\Controllers\Admin\UserManagementController::class, 'bulkAction'])->name('bulk-action');
            Route::get('/desas/{kecamatanKode}', [App\Http\Controllers\Admin\UserManagementController::class, 'getDesas'])->name('desas');
            Route::get('/{user}/edit', [App\Http\Controllers\Admin\UserManagementController::class, 'edit'])->name('edit');
            Route::put('/{user}', [App\Http\Controllers\Admin\UserManagementController::class, 'update'])->name('update');
            Route::delete('/{user}', [App\Http\Controllers\Admin\UserManagementController::class, 'destroy'])->name('destroy');
            Route::post('/{user}/toggle-status', [App\Http\Controllers\Admin\UserManagementController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/{user}/reset-password', [App\Http\Controllers\Admin\UserManagementController::class, 'resetPassword'])->name('reset-password');
            Route::get('/{user}', [App\Http\Controllers\Admin\UserManagementController::class, 'show'])->name('show');
        });

        Route::prefix('sidongan-data')->name('sidongan-data.')->middleware('permission:manage-users')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\SidonganDataController::class, 'index'])->name('index');
            Route::get('/{document}', [App\Http\Controllers\Admin\SidonganDataController::class, 'show'])->name('show');
            Route::post('/cleanup', [App\Http\Controllers\Admin\SidonganDataController::class, 'cleanup'])->name('cleanup');
            Route::delete('/report/{reportId}', [App\Http\Controllers\Admin\SidonganDataController::class, 'deleteReport'])->name('report.delete');
            Route::delete('/{document}', [App\Http\Controllers\Admin\SidonganDataController::class, 'destroy'])->name('destroy');
        });

        // Manajemen Data SIEDA — hapus permanen (per record & seluruh data per modul).
        // Perhatian: middleware permission bersifat defense-in-depth; controller juga
        // memverifikasi sidongan_role === 'super_admin' di dalam authorizeSuperAdmin().
        Route::prefix('sieda-data')->name('sieda-data.')->middleware('permission:manage-users')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\SiedaDataController::class, 'index'])->name('index');
            Route::get('/module/{module}', [App\Http\Controllers\Admin\SiedaDataController::class, 'showModule'])->name('module');
            Route::post('/module/{module}/delete-all', [App\Http\Controllers\Admin\SiedaDataController::class, 'deleteAll'])->name('delete-all');
            Route::get('/module/{module}/{id}', [App\Http\Controllers\Admin\SiedaDataController::class, 'showRecord'])->name('show');
            Route::delete('/module/{module}/{id}', [App\Http\Controllers\Admin\SiedaDataController::class, 'forceDelete'])->name('force-delete');
        });
    });

    // ================= PERSONAL EMAIL SETUP & VERIFICATION =================
    // Flow: setup → store (simpan + kirim email) → notice (cekk email) → verify (klik link) → dashboard
    Route::middleware(['auth'])->prefix('personal-email')->name('personal-email.')->group(function () {
        Route::get('/', [App\Http\Controllers\Auth\PersonalEmailController::class, 'showSetupForm'])->name('setup');
        Route::post('/', [App\Http\Controllers\Auth\PersonalEmailController::class, 'store'])->name('store');
        Route::get('/notice', [App\Http\Controllers\Auth\PersonalEmailController::class, 'showNotice'])->name('notice');
        Route::get('/verify/{id}', [App\Http\Controllers\Auth\PersonalEmailController::class, 'verify'])
            ->middleware('signed') // Validasi signed URL otomatis
            ->name('verify');
        Route::post('/resend', [App\Http\Controllers\Auth\PersonalEmailController::class, 'resend'])->name('resend');
        Route::get('/skip', [App\Http\Controllers\Auth\PersonalEmailController::class, 'skip'])->name('skip');
    });

});

// ======================================================================
// 2. ONBOARDING — STANDALONE (works from any login source)
// ======================================================================
Route::middleware(['web'])->group(function () {
    Route::get('/onboarding', [App\Http\Controllers\OnboardingController::class, 'show'])->name('onboarding');
    Route::post('/onboarding', [App\Http\Controllers\OnboardingController::class, 'store'])->name('onboarding.store');
    Route::get('/onboarding/skip', [App\Http\Controllers\OnboardingController::class, 'skip'])->name('onboarding.skip');
});

// ======================================================================
// 3. ROUTES KHUSUS DOMAIN: sidongan.tobakab.go.id (Aplikasi SIDONGAN)
// ======================================================================
Route::domain(config('app.sidongan_domain'))->group(function () {

    // ================= API: SIDONGAN PUBLIC =================
    Route::get('/api/v1/sidongan/documents', function () {
        try {
            $documents = \App\Models\Document::published()->with(['category', 'tags'])->orderBy('document_date', 'desc')->orderBy('created_at', 'desc')->paginate(12)
                ->through(function($doc) {
                    return ['id' => $doc->id, 'title' => $doc->title, 'slug' => $doc->slug, 'description' => $doc->description, 'document_number' => $doc->document_number, 'document_date' => $doc->document_date?->format('Y-m-d'), 'formatted_date' => $doc->document_date?->translatedFormat('d F Y'), 'category' => $doc->category ? ['id' => $doc->category->id, 'name' => $doc->category->name, 'color' => $doc->category->color] : null, 'tags' => $doc->tags->pluck('name'), 'file_name' => $doc->file_name, 'file_type' => $doc->file_type, 'file_size' => $doc->file_size, 'formatted_size' => $doc->formatted_size, 'file_url' => $doc->file_url, 'status' => $doc->status, 'is_public' => $doc->is_public, 'created_at' => $doc->created_at->format('Y-m-d H:i:s')];
                });
            return response()->json(['success' => true, 'data' => $documents, 'meta' => ['total' => $documents->total(), 'per_page' => $documents->perPage(), 'current_page' => $documents->currentPage(), 'last_page' => $documents->lastPage()]]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    });

    Route::get('/api/v1/sidongan/documents/{slug}', function ($slug) {
        try {
            $document = \App\Models\Document::published()->with(['category', 'tags', 'creator'])->where('slug', $slug)->firstOrFail();
            return response()->json(['success' => true, 'data' => ['id' => $document->id, 'title' => $document->title, 'slug' => $document->slug, 'description' => $document->description, 'document_number' => $document->document_number, 'document_date' => $document->document_date?->format('Y-m-d'), 'formatted_date' => $document->document_date?->translatedFormat('d F Y'), 'category' => $document->category ? ['id' => $document->category->id, 'name' => $document->category->name, 'color' => $document->category->color, 'description' => $document->category->description] : null, 'tags' => $document->tags->map(fn($t) => ['id' => $t->id, 'name' => $t->name, 'slug' => $t->slug]), 'file_name' => $document->file_name, 'file_type' => $document->file_type, 'file_size' => $document->file_size, 'formatted_size' => $document->formatted_size, 'file_url' => $document->file_url, 'metadata' => $document->metadata, 'creator' => $document->creator ? ['id' => $document->creator->id, 'name' => $document->creator->name] : null, 'created_at' => $document->created_at->format('Y-m-d H:i:s'), 'updated_at' => $document->updated_at?->format('Y-m-d H:i:s')]]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Dokumen tidak ditemukan'], 404);
        }
    });

    Route::get('/api/v1/sidongan/categories', function () {
        try {
            $categories = \App\Models\DocumentCategory::where('is_active', true)->withCount('documents')->orderBy('name')->get()->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'slug' => $c->slug, 'description' => $c->description, 'color' => $c->color, 'documents_count' => $c->documents_count]);
            return response()->json(['success' => true, 'data' => $categories]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    });

    Route::get('/api/v1/sidongan/tags', function () {
        try {
            $tags = \App\Models\DocumentTag::orderBy('name')->get()->map(fn($t) => ['id' => $t->id, 'name' => $t->name, 'slug' => $t->slug]);
            return response()->json(['success' => true, 'data' => $tags]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    });

    // ================= WEB: SIDONGAN AUTH =================
    Route::middleware(['sidongan.guest'])->group(function () {
        // Landing page untuk SIDONGAN
        Route::get('/', function () {
            return view('sidongan.landing');
        })->name('sidongan.landing');

        Route::get('/sidongan-login', function () {
            \Illuminate\Support\Facades\Auth::guard('web')->logout();
            if (session()->isStarted()) {
                session()->flush();
                session()->regenerateToken();
            }
            return view('sidongan-auth.login');
        })->name('sidongan.login');

        Route::post('/sidongan-login', [App\Http\Controllers\Sidongan\AuthController::class, 'login'])
            ->middleware('throttle:5,1') // Max 5 attempts per minute
            ->name('sidongan.login.post');

        // ================= SIDONGAN FORGOT PASSWORD =================
        Route::get('/sidongan-forgot-password', [App\Http\Controllers\Sidongan\ForgotPasswordController::class, 'create'])
            ->name('sidongan.password.request');

        Route::post('/sidongan-forgot-password', [App\Http\Controllers\Sidongan\ForgotPasswordController::class, 'store'])
            ->name('sidongan.password.email');

        Route::get('/sidongan-reset-password/{token}', [App\Http\Controllers\Sidongan\ResetPasswordController::class, 'create'])
            ->name('sidongan.password.reset');

        Route::post('/sidongan-reset-password', [App\Http\Controllers\Sidongan\ResetPasswordController::class, 'store'])
            ->name('sidongan.password.store');
    });

    Route::post('/sidongan-logout', [App\Http\Controllers\Sidongan\AuthController::class, 'logout'])->name('sidongan.logout');

    // ================= WEB: SIDONGAN ADMIN =================
    Route::middleware(['sidongan.auth', 'sidongan.profile'])->prefix('sidongan')->name('sidongan.')->group(function () {
        Route::get('/', [App\Http\Controllers\Sidongan\AdminDocumentController::class, 'dashboard'])->name('dashboard');

        Route::get('/documents', [App\Http\Controllers\Sidongan\AdminDocumentController::class, 'index'])->name('documents.index');
        Route::get('/documents/create', [App\Http\Controllers\Sidongan\AdminDocumentController::class, 'create'])->name('documents.create');
        Route::post('/documents', [App\Http\Controllers\Sidongan\AdminDocumentController::class, 'store'])->name('documents.store');
        Route::get('/documents/{document}', [App\Http\Controllers\Sidongan\AdminDocumentController::class, 'show'])->name('documents.show');
        Route::get('/documents/{document}/edit', [App\Http\Controllers\Sidongan\AdminDocumentController::class, 'edit'])->name('documents.edit');
        Route::put('/documents/{document}', [App\Http\Controllers\Sidongan\AdminDocumentController::class, 'update'])->name('documents.update');
        Route::delete('/documents/{document}', [App\Http\Controllers\Sidongan\AdminDocumentController::class, 'destroy'])->name('documents.destroy');
        Route::get('/documents/{document}/download', [App\Http\Controllers\Sidongan\AdminDocumentController::class, 'download'])->name('documents.download');
        Route::get('/documents/{document}/disposisi-print', [App\Http\Controllers\Sidongan\AdminDocumentController::class, 'printDisposisi'])->name('documents.disposisi-print');
        Route::patch('/documents/{document}/archive', [App\Http\Controllers\Sidongan\AdminDocumentController::class, 'archive'])->name('documents.archive');
        Route::post('/documents/bulk-archive', [App\Http\Controllers\Sidongan\AdminDocumentController::class, 'bulkArchive'])->name('documents.bulk-archive');
        Route::post('/documents/bulk-delete', [App\Http\Controllers\Sidongan\AdminDocumentController::class, 'bulkDelete'])->name('documents.bulk-delete');

        Route::get('/disposisi', [App\Http\Controllers\Sidongan\AdminDocumentController::class, 'disposisi'])->name('disposisi');
        Route::get('/disposisi/{document}', [App\Http\Controllers\Sidongan\AdminDocumentController::class, 'showDisposisiForm'])->name('disposisi.form');
        Route::post('/disposisi/{document}', [App\Http\Controllers\Sidongan\AdminDocumentController::class, 'storeDisposisi'])->name('disposisi.store');

        Route::get('/verifikasi', [App\Http\Controllers\Sidongan\VerificationController::class, 'index'])->name('verifikasi');
        Route::get('/verifikasi/{id}/form', [App\Http\Controllers\Sidongan\VerificationController::class, 'form'])->name('verifikasi.form');
        Route::match(['post', 'put'], '/verifikasi/{id}', [App\Http\Controllers\Sidongan\VerificationController::class, 'store'])->name('verifikasi.store');

        Route::get('/arsip', [App\Http\Controllers\Sidongan\AdminDocumentController::class, 'arsip'])->name('arsip');

        Route::get('/lapor-kegiatan', [App\Http\Controllers\Sidongan\ActivityReportController::class, 'index'])->name('lapor_kegiatan.index');
        Route::get('/lapor-kegiatan/create/{document_id?}', [App\Http\Controllers\Sidongan\ActivityReportController::class, 'create'])->name('lapor_kegiatan.create');
        Route::post('/lapor-kegiatan', [App\Http\Controllers\Sidongan\ActivityReportController::class, 'store'])->name('lapor_kegiatan.store');
        Route::get('/lapor-kegiatan/{id}', [App\Http\Controllers\Sidongan\ActivityReportController::class, 'show'])->name('lapor_kegiatan.show');
        Route::get('/lapor-kegiatan/{id}/edit', [App\Http\Controllers\Sidongan\ActivityReportController::class, 'edit'])->name('lapor_kegiatan.edit');
        Route::put('/lapor-kegiatan/{id}', [App\Http\Controllers\Sidongan\ActivityReportController::class, 'update'])->name('lapor_kegiatan.update');
        Route::delete('/lapor-kegiatan/{id}', [App\Http\Controllers\Sidongan\ActivityReportController::class, 'destroy'])->name('lapor_kegiatan.destroy');

        Route::get('/notifications', [App\Http\Controllers\Sidongan\AdminDocumentController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/{id}/read', [App\Http\Controllers\Sidongan\AdminDocumentController::class, 'markNotificationAsRead'])->name('notifications.read');
        Route::post('/notifications/mark-all-read', [App\Http\Controllers\Sidongan\AdminDocumentController::class, 'markAllNotificationsAsRead'])->name('notifications.mark-all-read');

        // Profile (Edit Profil)
        Route::get('/profile', [App\Http\Controllers\Sidongan\ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [App\Http\Controllers\Sidongan\ProfileController::class, 'update'])->name('profile.update');
        Route::get('/profile/password', [App\Http\Controllers\Sidongan\ProfileController::class, 'password'])->name('profile.password');
        Route::put('/profile/password', [App\Http\Controllers\Sidongan\ProfileController::class, 'updatePassword'])->name('profile.password.update');
    });
});

// ================= DEV: SIDONGAN LANDING PREVIEW (hanya environment local) =================
if (app()->environment('local')) {
    Route::get('/dev/sidongan-preview', function () {
        return view('sidongan.landing');
    })->name('dev.sidongan.preview');
}

// ================= AUTH ROUTES (Wajib di paling bawah) =================
require __DIR__.'/auth.php';
/* Dikembangkan oleh Institut Teknologi Del */
