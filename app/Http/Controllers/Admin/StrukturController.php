<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pokja;
use App\Models\StrukturMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StrukturController extends Controller
{
    protected $sortMap = [
        'Ketua Pembina' => 1,
        'Ketua TP PKK' => 2,
        'Staf Ahli 1' => 3,
        'Staf Ahli 2' => 4,
        'Sekretaris' => 5,
        'Bendahara' => 6,
        'Ketua I' => 7,
        'Ketua II' => 8,
        'Ketua III' => 9,
        'Ketua IV' => 10,
        'Ketua' => 11,
        'Wakil Ketua' => 12,
        'Sekretaris Pokja' => 13,
        'Anggota' => 14
    ];

    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $currentTab = request('tab', 'pengurus');

        // 1. Pengurus Inti Pagination
        $pengurusInti = StrukturMember::whereNull('pokja_id')
            ->where(function($q) use ($search) {
                if ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('position', 'like', "%{$search}%");
                }
            })
            ->orderByRaw("CASE position
                WHEN 'Ketua Pembina' THEN 1
                WHEN 'Ketua TP PKK' THEN 2
                WHEN 'Staf Ahli 1' THEN 3
                WHEN 'Staf Ahli 2' THEN 4
                WHEN 'Sekretaris' THEN 5
                WHEN 'Bendahara' THEN 6
                WHEN 'Ketua I' THEN 7
                WHEN 'Ketua II' THEN 8
                WHEN 'Ketua III' THEN 9
                WHEN 'Ketua IV' THEN 10
                ELSE 99 END")
            ->paginate($perPage, ['*'], 'page_pengurus')
            ->appends(['tab' => 'pengurus', 'search' => $search, 'per_page' => $perPage]);

        // 2. POKJA Paginations (TAMBAHKAN ->appends() di sini)
        $pokja1 = StrukturMember::where('pokja_id', 1)
            ->where(function($q) use ($search) {
                if ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('position', 'like', "%{$search}%");
                }
            })
            ->orderBy('sort_order')
            ->paginate($perPage, ['*'], 'page_pokja1')
            ->appends(['tab' => 'pokja1', 'search' => $search, 'per_page' => $perPage]);

        $pokja2 = StrukturMember::where('pokja_id', 2)
            ->where(function($q) use ($search) {
                if ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('position', 'like', "%{$search}%");
                }
            })
            ->orderBy('sort_order')
            ->paginate($perPage, ['*'], 'page_pokja2')
            ->appends(['tab' => 'pokja2', 'search' => $search, 'per_page' => $perPage]);

        $pokja3 = StrukturMember::where('pokja_id', 3)
            ->where(function($q) use ($search) {
                if ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('position', 'like', "%{$search}%");
                }
            })
            ->orderBy('sort_order')
            ->paginate($perPage, ['*'], 'page_pokja3')
            ->appends(['tab' => 'pokja3', 'search' => $search, 'per_page' => $perPage]);

        $pokja4 = StrukturMember::where('pokja_id', 4)
            ->where(function($q) use ($search) {
                if ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('position', 'like', "%{$search}%");
                }
            })
            ->orderBy('sort_order')
            ->paginate($perPage, ['*'], 'page_pokja4')
            ->appends(['tab' => 'pokja4', 'search' => $search, 'per_page' => $perPage]);

        // Hitung total untuk badge
        $pengurusCount = StrukturMember::whereNull('pokja_id')->count();
        $pokja1Count = StrukturMember::where('pokja_id', 1)->count();
        $pokja2Count = StrukturMember::where('pokja_id', 2)->count();
        $pokja3Count = StrukturMember::where('pokja_id', 3)->count();
        $pokja4Count = StrukturMember::where('pokja_id', 4)->count();

        return view('admin.struktur.index', compact(
            'pengurusInti', 'pokja1', 'pokja2', 'pokja3', 'pokja4',
            'pengurusCount', 'pokja1Count', 'pokja2Count', 'pokja3Count', 'pokja4Count'
        ));
    }

    // Method baru untuk AJAX
    public function getTabData($tab)
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');

        $columns = [
            [
                'key' => 'photo_path',
                'label' => 'Foto',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>',
                'type' => 'image',
                'initial_key' => 'name'
            ],
            [
                'key' => 'name',
                'label' => 'Nama',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
            ],
            [
                'key' => 'position',
                'label' => 'Jabatan',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>',
                'type' => 'callback',
                'callback' => function($item, $value) {
                    return '<span class="position-badge" style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;background:rgba(13,148,166,0.1);color:var(--primary-dark);border:1px solid rgba(13,148,166,0.2)">' . $value . '</span>';
                }
            ],
        ];

        if ($tab === 'pengurus') {
            $data = StrukturMember::whereNull('pokja_id')
                ->where(function($query) use ($search) {
                    if ($search) {
                        $query->where('name', 'like', "%{$search}%")
                              ->orWhere('position', 'like', "%{$search}%");
                    }
                })
                ->orderByRaw("CASE position
                    WHEN 'Ketua Pembina' THEN 1
                    WHEN 'Ketua TP PKK' THEN 2
                    WHEN 'Staf Ahli 1' THEN 3
                    WHEN 'Staf Ahli 2' THEN 4
                    WHEN 'Sekretaris' THEN 5
                    WHEN 'Bendahara' THEN 6
                    WHEN 'Ketua I' THEN 7
                    WHEN 'Ketua II' THEN 8
                    WHEN 'Ketua III' THEN 9
                    WHEN 'Ketua IV' THEN 10
                    ELSE 99 END")
                ->paginate($perPage);

            $emptyMessage = 'Belum ada data pengurus inti.';

        } else {
            $pokjaNum = str_replace('pokja', '', $tab);
            $data = StrukturMember::where('pokja_id', $pokjaNum)
                ->where(function($query) use ($search) {
                    if ($search) {
                        $query->where('name', 'like', "%{$search}%")
                              ->orWhere('position', 'like', "%{$search}%");
                    }
                })
                ->orderBy('sort_order')
                ->paginate($perPage);

            $emptyMessage = "Belum ada anggota di Pokja {$pokjaNum}.";
        }

        return view('admin.struktur.tab-content', [
            'data' => $data,
            'columns' => $columns,
            'emptyMessage' => $emptyMessage,
            'editRoute' => 'admin.struktur.edit',
            'deleteRoute' => 'admin.struktur.destroy',
            'actions' => ['edit', 'delete']
        ])->render();
    }

    public function create()
    {
        return view('admin.struktur.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'group' => 'required|in:pengurus,pokja1,pokja2,pokja3,pokja4',
            'position' => 'required|string|max:50',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'photo' => 'nullable|image|max:2048',
            'cropped_photo' => 'nullable|string',
        ]);

        // === HANDLE PHOTO UPLOAD (FIXED LOGIC) ===
        $photoPath = null;

        // 1. Prioritaskan cropped photo (base64)
        if (!empty($validated['cropped_photo'])) {
            $photoPath = $this->saveCroppedPhoto($validated['cropped_photo']);
        }
        // 2. Jika tidak ada cropped, cek regular file upload
        elseif ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $photoPath = $request->file('photo')->store('struktur', 'public');
        }
        // ==========================================

        // Map group to pokja_id
        $pokjaId = $validated['group'] === 'pengurus' ? null : (int)str_replace('pokja', '', $validated['group']);

        // Auto sort_order
        $sortOrder = $this->sortMap[$validated['position']] ?? 99;

        // Normalisasi jabatan
        $position = $validated['position'];
        if ($pokjaId && $position === 'Sekretaris') {
            $position = 'Sekretaris Pokja';
        }

        // Create dengan photo_path yang sudah diproses
        StrukturMember::create([
            'pokja_id' => $pokjaId,
            'position' => $position,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $sortOrder,
            'photo_path' => $photoPath, // <-- INI YANG SEBELUMNYA HILANG
        ]);

        return redirect()->route('admin.struktur.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit(StrukturMember $struktur)
    {
        return view('admin.struktur.edit', compact('struktur'));
    }

    public function update(Request $request, StrukturMember $struktur)
    {
        $validated = $request->validate([
            'group' => 'required|in:pengurus,pokja1,pokja2,pokja3,pokja4',
            'position' => 'required|string|max:50',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'photo' => 'nullable|image|max:2048',
            'cropped_photo' => 'nullable|string',
        ]);

        // === HANDLE PHOTO UPDATE (FIXED LOGIC) ===
        $photoPath = $struktur->photo_path; // Keep existing by default

        // 1. Jika ada cropped photo baru
        if (!empty($validated['cropped_photo'])) {
            // Hapus foto lama
            if ($photoPath) Storage::disk('public')->delete($photoPath);
            $photoPath = $this->saveCroppedPhoto($validated['cropped_photo']);
        }
        // 2. Jika ada regular file upload baru
        elseif ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            // Hapus foto lama
            if ($photoPath) Storage::disk('public')->delete($photoPath);
            $photoPath = $request->file('photo')->store('struktur', 'public');
        }
        // ==========================================

        $pokjaId = $validated['group'] === 'pengurus' ? null : (int)str_replace('pokja', '', $validated['group']);
        $sortOrder = $this->sortMap[$validated['position']] ?? 99;

        $position = $validated['position'];
        if ($pokjaId && $position === 'Sekretaris') {
            $position = 'Sekretaris Pokja';
        }

        $struktur->update([
            'pokja_id' => $pokjaId,
            'position' => $position,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $sortOrder,
            'photo_path' => $photoPath, // <-- INI YANG SEBELUMNYA HILANG
        ]);

        return redirect()->route('admin.struktur.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy(StrukturMember $struktur)
    {
        if ($struktur->photo_path) Storage::disk('public')->delete($struktur->photo_path);
        $struktur->delete();
        return redirect()->route('admin.struktur.index')->with('success', 'Data berhasil dihapus.');
    }

    /**
     * Helper: Simpan foto base64 hasil crop
     */
    private function saveCroppedPhoto($base64Image)
    {
        try {
            // Remove data:image/jpeg;base64, prefix
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $base64Image = substr($base64Image, strlen($type[0]));
            }

            // Decode base64
            $imageData = base64_decode($base64Image);

            if ($imageData === false) {
                Log::error('Failed to decode base64 image');
                return null;
            }

            // Generate unique filename
            $imageName = 'struktur_' . time() . '_' . uniqid() . '.jpg';
            $path = 'struktur/' . $imageName;

            // Save to storage
            Storage::disk('public')->put($path, $imageData);

            Log::info('Cropped photo saved: ' . $path);
            return $path;

        } catch (\Exception $e) {
            Log::error('Error saving cropped photo: ' . $e->getMessage());
            return null;
        }
    }
}
