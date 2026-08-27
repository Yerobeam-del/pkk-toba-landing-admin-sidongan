<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Http\Controllers\Sidongan;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\DocumentTag;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminDocumentController extends Controller
{
    /**
     * Cek apakah user boleh mengakses dokumen ini.
     * - Ketua: boleh akses semua
     * - Sekretaris: hanya dokumen yang dibuatnya
     * - Role lain: hanya dokumen berjalan yang didisposisi ke role mereka
     */
    private function authorizeDocumentAccess(Document $document): void
    {
        $user = auth()->guard('sidongan')->user();
        if (!$user) {
            abort(403, 'Akses ditolak.');
        }

        if ($user->hasSidonganRole('ketua')) {
            return; // Ketua boleh akses semua
        }

        if ($user->hasSidonganRole('sekretaris')) {
            if ($document->created_by === $user->id) return;
            abort(403, 'Anda tidak berhak mengakses surat ini.');
        }

        // Role lain: cek apakah dokumen didisposisi ke role mereka
        $dispo = is_string($document->disposisi_data)
            ? json_decode($document->disposisi_data, true)
            : $document->disposisi_data;
        $targetRoles = $dispo['target_roles'] ?? [];

        if (in_array($user->sidongan_role, $targetRoles)) {
            return;
        }

        abort(403, 'Anda tidak berhak mengakses surat ini.');
    }

    /**
     * Dashboard SIDONGAN (Stats + Recent Documents + Notifications)
     */
    public function dashboard()
    {
        $user = auth()->guard('sidongan')->user();

        if (!$user) {
            return redirect()->route('sidongan.login');
        }
        
        // Build base query berdasarkan role
        $baseQuery = Document::query();
        if ($user->hasSidonganRole('sekretaris')) {
            $baseQuery->where('created_by', $user->id);
        } elseif (!$user->hasSidonganRole('ketua')) {
            $userRole = $user->sidongan_role;
            $baseQuery->where('status', 'berjalan')
                ->whereJsonContains('disposisi_data->target_roles', $userRole);
        }
        
        // OPTIMIZED: 1 query untuk semua stats via selectRaw
        $stats = (clone $baseQuery)
            ->selectRaw(
                "COUNT(*) as total, " .
                "SUM(CASE WHEN status = 'berjalan' THEN 1 ELSE 0 END) as berjalan, " .
                "SUM(CASE WHEN status IN ('menunggu_disposisi','menunggu_verifikasi') THEN 1 ELSE 0 END) as menunggu, " .
                "SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as selesai, " .
                "SUM(CASE WHEN status = 'diarsipkan' THEN 1 ELSE 0 END) as diarsipkan"
            )
            ->first();
        
        // Recent Documents
        $recentDocuments = (clone $baseQuery)
            ->with(['creator', 'activityReports' => function($q) {
                $q->with('creator')->latest();
            }])
            ->orderByRaw("
                CASE 
                    WHEN status = 'menunggu_disposisi' THEN 1
                    WHEN status = 'berjalan' THEN 2
                    WHEN status = 'menunggu_verifikasi' THEN 3
                    WHEN status = 'selesai' THEN 4
                    WHEN status = 'diarsipkan' THEN 5
                    ELSE 6
                END
            ")
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Notifications (1 query gabungan)
        $notifications = Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->latest()
            ->take(5)
            ->get();

        $unreadCount = $notifications->count();

        // Activity log: surat + laporan terbaru
        $recentActivity = collect();
        $recentDocs = Document::with('creator')
            ->latest('created_at')
            ->take(5)
            ->get()
            ->map(fn($d) => [
                'type' => 'document',
                'title' => 'Surat baru: ' . ($d->subject ?? $d->title),
                'user' => $d->creator->name ?? 'System',
                'time' => $d->created_at,
                'icon' => 'fa-envelope',
                'color' => 'blue',
            ]);
        $recentReports = \App\Models\ActivityReport::with('creator')
            ->latest('created_at')
            ->take(5)
            ->get()
            ->map(fn($r) => [
                'type' => 'laporan',
                'title' => 'Laporan: ' . ($r->kegiatan_nama ?? '-'),
                'user' => $r->creator->name ?? 'System',
                'time' => $r->created_at,
                'icon' => 'fa-clipboard-list',
                'color' => 'green',
            ]);
        $recentActivity = $recentDocs->concat($recentReports)
            ->sortByDesc('time')
            ->take(10)
            ->values();

        return view('sidongan.dashboard.index', [
            'totalSurat' => $stats->total ?? 0,
            'sedangBerjalan' => $stats->berjalan ?? 0,
            'menungguProses' => $stats->menunggu ?? 0,
            'selesai' => $stats->selesai ?? 0,
            'diarsipkan' => $stats->diarsipkan ?? 0,
            'recentDocuments' => $recentDocuments,
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'recentActivity' => $recentActivity,
        ]);
    }

    /**
     * List Documents (Table View)
     */
    public function index(Request $request)
    {
        $user = auth()->guard('sidongan')->user();
        
        // 1. Buat Query Dasar dengan role-based filtering
        $query = Document::with(['category', 'creator', 'activityReports' => function($q) {
            $q->with('creator')->latest();
        }]);
        
        // Sekretaris hanya lihat surat yang dibuatnya
        if ($user && $user->hasSidonganRole('sekretaris')) {
            $query->where('created_by', $user->id);
        } elseif ($user && !$user->hasSidonganRole('ketua')) {
            // Role lain hanya lihat surat berjalan yang didisposisi ke mereka
            $userRole = $user->sidongan_role;
            $query->where('status', 'berjalan')
                ->whereJsonContains('disposisi_data->target_roles', $userRole);
        }
        
        // Hitung Total Dokumen
        $totalDocuments = (clone $query)->count();
        
        // Hitung Stats
        $statSelesai = (clone $query)->where('status', 'selesai')->count();
        $statBerjalan = (clone $query)->where('status', 'berjalan')->count();
        $statMenungguDisposisi = (clone $query)->where('status', 'menunggu_disposisi')->count();
        $statMenungguVerifikasi = (clone $query)->where('status', 'menunggu_verifikasi')->count();
        
        // 2. Filter Pencarian
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        
        // 3. Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // 4. Filter Kategori
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        // 5. Filter Tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('document_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('document_date', '<=', $request->date_to);
        }
        
        // 6. Filter Bulan
        if ($request->filled('filter_month')) {
            $query->whereMonth('document_date', $request->filter_month);
        }
        
        // 7. Filter Tahun
        if ($request->filled('filter_year')) {
            $query->whereYear('document_date', $request->filter_year);
        }

        // 8. SORTING
        $sortField = $request->get('sort', null);
        $sortDirection = $request->get('direction', 'desc');
        
        if ($sortField) {
            $allowedSorts = ['id', 'agenda_number', 'subject', 'document_number', 'document_date', 'status', 'created_at'];
            if (!in_array($sortField, $allowedSorts)) {
                $sortField = 'created_at';
            }
            if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
                $sortDirection = 'desc';
            }
            $query->orderBy($sortField, $sortDirection);
        } else {
            // DEFAULT: Sorting berdasarkan prioritas status
            $query->orderByRaw("
                CASE 
                    WHEN status = 'menunggu_disposisi' THEN 1
                    WHEN status = 'berjalan' AND EXISTS (
                        SELECT 1 FROM activity_reports 
                        WHERE activity_reports.document_id = sidongan_documents.id 
                        AND activity_reports.status = 'menunggu_verifikasi'
                    ) THEN 2
                    WHEN status = 'berjalan' THEN 3
                    WHEN status = 'selesai' THEN 4
                    WHEN status = 'diarsipkan' THEN 5
                    ELSE 6
                END
            ");
            $query->orderBy('created_at', 'desc');
        }

        // 9. PAGINATION
        $perPage = $request->get('per_page', 10);
        $allowedPerPages = [5, 10, 15, 25, 50];
        if (!in_array($perPage, $allowedPerPages)) {
            $perPage = 10;
        }

        $documents = $query->paginate($perPage)->withQueryString();
        $categories = DocumentCategory::where('is_active', true)->orderBy('name')->get();
        
        // Ambil tahun unik untuk filter tahun (kompatibel MySQL & SQLite)
        $availableYears = Document::whereNotNull('document_date')
            ->orderBy('document_date', 'desc')
            ->pluck('document_date')
            ->map(fn($d) => $d->format('Y'))
            ->unique()
            ->values()
            ->filter();

        return view('sidongan.documents.index', [
            'documents' => $documents,
            'categories' => $categories,
            'totalDocuments' => $totalDocuments,
            'statSelesai' => $statSelesai,
            'statBerjalan' => $statBerjalan,
            'statMenungguDisposisi' => $statMenungguDisposisi,
            'statMenungguVerifikasi' => $statMenungguVerifikasi,
            'currentPerPage' => $perPage,
            'allowedPerPages' => $allowedPerPages,
            'currentSort' => $sortField,
            'currentDirection' => $sortDirection,
            'availableYears' => $availableYears,
        ]);
    }

    public function create()
    {
        $categories = DocumentCategory::where('is_active', true)->orderBy('name')->get();
        return view('sidongan.documents.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // SAFETY CHECK: Pastikan user login via guard sidongan
        $user = auth()->guard('sidongan')->user();
        // Note: toast via with() already used in success/error redirects below
        
        if (!$user) {
            \Log::error('SIDONGAN Store: User not authenticated');
            return redirect()->route('sidongan.login')
                ->with('error', 'Session expired. Silakan login ulang.')
                ->withErrors(['auth' => 'Session expired. Silakan login ulang.']);
        }
        
        // Validasi input dengan custom messages
        $validated = $request->validate([
            // Data Pengirim
            'sender' => 'required|string|max:255',
            'document_number' => 'required|string|max:100',
            'document_date' => 'required|date',
            'subject' => 'required|string|max:255',
            
            // Data Agenda (otomatis, tapi bisa di-override)
            'agenda_number' => 'nullable|string|max:100|unique:sidongan_documents,agenda_number',
            'agenda_date' => 'nullable|date',
            
            // Saran Sekretaris
            'suggestion' => 'required|string',
            
            // Upload File
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120', // Max 5MB
            
            // Kategori (opsional)
            'category_id' => 'nullable|exists:sidongan_categories,id',
        ], [
            // Custom error messages dalam bahasa Indonesia
            'sender.required' => 'Nama pengirim surat harus diisi',
            'sender.max' => 'Nama pengirim maksimal 255 karakter',
            
            'document_number.required' => 'Nomor surat harus diisi',
            'document_number.max' => 'Nomor surat maksimal 100 karakter',
            
            'document_date.required' => 'Tanggal surat harus diisi',
            'document_date.date' => 'Format tanggal surat tidak valid',
            
            'subject.required' => 'Perihal surat harus diisi',
            'subject.max' => 'Perihal surat maksimal 255 karakter',
            
            'suggestion.required' => 'Saran atau catatan untuk Ketua PKK harus diisi',
            
            'file.required' => 'File surat harus diupload',
            'file.file' => 'File yang diupload tidak valid',
            'file.mimes' => 'Format file harus: PDF, JPG, JPEG, PNG, DOC, atau DOCX',
            'file.max' => 'Ukuran file maksimal 5MB',
            
            'category_id.exists' => 'Kategori yang dipilih tidak valid',
        ]);

        // Handle upload file
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('sidongan/documents', $filename, 'public');
        } else {
            return back()->with('error', 'File surat wajib diupload.')
                ->withErrors(['file' => 'File surat wajib diupload.'])
                ->withInput();
        }

        // Tentukan tanggal dasar untuk nomor agenda
        // Prioritaskan: agenda_date (tanggal diterima) → document_date (tanggal surat)
        $tanggalAgenda = $validated['agenda_date'] ?? $validated['document_date'];

        // Buat dokumen baru
        $document = Document::create([
            'title' => $validated['subject'],
            'description' => $validated['suggestion'],
            'sender' => $validated['sender'],
            'document_number' => $validated['document_number'],
            'agenda_number' => $validated['agenda_number'] ?? Document::generateAgendaNumber($tanggalAgenda),
            'agenda_date' => $validated['agenda_date'] ?? $validated['document_date'],
            'document_date' => $validated['document_date'],
            'subject' => $validated['subject'],
            'suggestion' => $validated['suggestion'],
            'status' => 'menunggu_disposisi',
            'category_id' => $validated['category_id'] ?? null,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'is_public' => false,
            'created_by' => $user->id,
        ]);

        // NOTIFICATION: Buat notifikasi untuk semua user dengan role 'ketua'
        $ketuaUsers = User::where('sidongan_role', 'ketua')->get();

        foreach ($ketuaUsers as $ketua) {
            Notification::create([
                'user_id' => $ketua->id,
                'type' => 'document.created',
                'title' => 'Surat Masuk Baru',
                // FORMAT BARU: Sesuai contoh Anda
                'message' => "Surat baru dengan No. Agenda {$document->agenda_number} menunggu disposisi",
                'related_id' => $document->id,
                'related_type' => Document::class,
            ]);
        }

        // Activity Log
        \App\Models\AdminActivityLog::log('created', 'surat', $document->id, [
            'agenda_number' => $document->agenda_number,
            'subject' => $document->subject,
        ], $user->id);

        return redirect()->route('sidongan.documents.index')
            ->with('success', 'Surat masuk berhasil disimpan dan dikirim ke Ketua untuk disposisi!');
    }

    public function edit(Document $document)
    {
        $this->authorizeDocumentAccess($document);
        $categories = DocumentCategory::where('is_active', true)->orderBy('name')->get();
        return view('sidongan.documents.edit', compact('document', 'categories'));
    }

    public function update(Request $request, Document $document)
    {
        $this->authorizeDocumentAccess($document);
        $user = auth()->guard('sidongan')->user();

        // 1. HANDLE HAPUS FILE
        if ($request->has('delete_file') && $request->delete_file == '1') {
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            
            $document->update([
                'file_path' => null,
                'file_name' => null,
                'file_type' => null,
                'file_size' => 0,
            ]);

            \App\Models\AdminActivityLog::log('updated', 'surat', $document->id, [
            'action' => 'file_deleted',
        ], auth()->guard('sidongan')->id());
        return back()->with('success', 'File berhasil dihapus!');
        }

        // 2. Handle archive
        if ($request->has('archive') && $request->archive === '1') {
            if (!$user || !$user->hasSidonganRole('sekretaris')) {
                abort(403, 'Akses ditolak.');
            }
            
            $document->update([
                'status' => 'diarsipkan',
                'updated_by' => $user->id,
            ]);
            
            \App\Models\AdminActivityLog::log('updated', 'surat', $document->id, [
                'action' => 'archived_from_update',
            ], $user->id);
            return redirect()->route('sidongan.documents.show', $document)
                ->with('success', 'Surat berhasil diarsipkan!');
        }

        // 3. VALIDASI
        $validated = $request->validate([
            'sender' => 'required|string|max:255',
            'document_number' => 'required|string|max:100',
            'document_date' => 'required|date',
            'agenda_date' => 'nullable|date',
            'subject' => 'required|string|max:255',
            'suggestion' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'category_id' => 'nullable|exists:sidongan_categories,id',
        ]);

        // 4. HANDLE UPLOAD FILE BARU
        if ($request->hasFile('file')) {
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            
            $file = $request->file('file');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('sidongan/documents', $filename, 'public');
            
            $document->update([
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        // 5. UPDATE TEXT FIELDS
        $document->update([
            'sender' => $validated['sender'],
            'document_number' => $validated['document_number'],
            'document_date' => $validated['document_date'],
            'agenda_date' => $validated['agenda_date'] ?? $document->agenda_date,
            'subject' => $validated['subject'],
            'suggestion' => $validated['suggestion'] ?? $document->suggestion,
        ]);

        \App\Models\AdminActivityLog::log('updated', 'surat', $document->id, [
            'agenda_number' => $document->agenda_number,
            'subject' => $document->subject,
        ], $user->id);
        return redirect()->route('sidongan.documents.index')->with('success', 'Dokumen berhasil diperbarui!');
    }

    public function destroy(Document $document)
    {
        $user = auth()->guard('sidongan')->user();
        if (!$user || !$user->hasSidonganRole('sekretaris')) {
            abort(403, 'Akses ditolak.');
        }

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }
        $agendaNum = $document->agenda_number;
        $docId = $document->id;
        $document->delete();
        \App\Models\AdminActivityLog::log('deleted', 'surat', $docId, [
            'agenda_number' => $agendaNum,
        ], auth()->guard('sidongan')->id());
        return redirect()->route('sidongan.documents.index')->with('success', 'Dokumen berhasil dihapus!');
    }

    public function download(Document $document)
    {
        $this->authorizeDocumentAccess($document);
        if (!$document->file_path) {
            abort(404, 'File tidak ditemukan.');
        }
        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    public function show(Request $request, Document $document)
    {
        $this->authorizeDocumentAccess($document);

        // Helper function untuk cek apakah URL adalah Form Disposisi
        $isDisposisiForm = function($url) {
            return preg_match('#/disposisi/\d+#', $url) === 1;
        };

        // PRIORITAS 1: URL parameter 'from'
        if ($request->has('from')) {
            $fromUrl = $request->get('from');
            if (!$isDisposisiForm($fromUrl) && 
                !str_contains($fromUrl, '/disposisi-print') &&
                !str_contains($fromUrl, '/create') &&
                !str_contains($fromUrl, '/edit')) {
                session(['document_back_url' => $fromUrl]);
            }
        } 
        // PRIORITAS 2: previousUrl
        else {
            $previousUrl = url()->previous();
            $currentUrl = url()->current();
            
            if ($previousUrl && 
                $previousUrl !== $currentUrl && 
                !$isDisposisiForm($previousUrl) &&
                !str_contains($previousUrl, '/disposisi-print') &&
                !str_contains($previousUrl, '/create') &&
                !str_contains($previousUrl, '/edit')) {
                session(['document_back_url' => $previousUrl]);
            }
        }
        
        $document->load(['creator', 'category', 'tags']);
        
        $activityReports = \App\Models\ActivityReport::where('document_id', $document->id)
            ->with(['creator'])
            ->latest()
            ->get();
        
        return view('sidongan.documents.show', compact('document', 'activityReports'));
    }

    /**
     * Mark notification as read + AUTO DELETE (AJAX)
     */
    public function markNotificationAsRead($id)
    {
        $user = auth()->guard('sidongan')->user();
        
        // Cari notifikasi milik user ini
        $notification = Notification::where('user_id', $user->id)->findOrFail($id);
        
        // ✅ LANGSUNG HAPUS (karena sudah "dibaca")
        $notification->delete();
        
        return response()->json([
            'success' => true, 
            'message' => 'Notifikasi dihapus'
        ]);
    }

    /**
     * Halaman Disposisi Surat (untuk Ketua PKK)
     */
    public function disposisi(Request $request)
    {
        $user = auth()->guard('sidongan')->user();
        
        if (!$user->hasSidonganRole('ketua')) {
            abort(403, 'Akses ditolak');
        }
        
        // 1. Query Dasar - hanya surat menunggu disposisi
        $query = Document::with(['category', 'creator'])
            ->where('status', 'menunggu_disposisi');
        
        // 2. Filter Pencarian
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('subject', 'like', '%' . $request->search . '%')
                ->orWhere('agenda_number', 'like', '%' . $request->search . '%')
                ->orWhere('document_number', 'like', '%' . $request->search . '%')
                ->orWhere('sender', 'like', '%' . $request->search . '%');
            });
        }
        
        // 3. Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'agenda':
                $query->orderBy('agenda_number', 'asc');
                break;
            default: // latest
                $query->orderBy('created_at', 'desc');
        }
        
        // 4. Pagination
        $perPage = $request->get('per_page', 10);
        $allowedPerPages = [5, 10, 15, 25, 50];
        if (!in_array($perPage, $allowedPerPages)) {
            $perPage = 10;
        }
        
        $documents = $query->paginate($perPage)->withQueryString();
        
        return view('sidongan.disposisi.index', compact('documents'));
    }

    /**
     * Form Disposisi
     */
    public function showDisposisiForm(Request $request, Document $document)
    {
        $user = auth()->guard('sidongan')->user();
        
        if (!$user->hasSidonganRole('ketua')) {
            abort(403, 'Akses ditolak');
        }
        
        // ✅ PRIORITAS 1: URL parameter 'from'
        if ($request->has('from')) {
            $fromUrl = $request->get('from');
            if (!str_contains($fromUrl, '/disposisi/form') &&
                !str_contains($fromUrl, '/disposisi-print')) {
                session(['disposisi_form_back_url' => $fromUrl]);
            }
        }
        // ✅ PRIORITAS 2: previousUrl
        else {
            $previousUrl = url()->previous();
            
            if ($previousUrl && 
                !str_contains($previousUrl, '/disposisi/form') &&
                !str_contains($previousUrl, '/disposisi-print')) {
                session(['disposisi_form_back_url' => $previousUrl]);
            }
        }
        
        $roles = User::getSidonganRoles();
        unset($roles['ketua']);
        unset($roles['sekretaris']);
        
        return view('sidongan.disposisi.form', compact('document', 'roles'));
    }

    /**
     * Store Disposisi
     */
    public function storeDisposisi(Request $request, Document $document)
    {
        $user = auth()->guard('sidongan')->user();
        
        if (!$user->hasSidonganRole('ketua')) {
            abort(403, 'Akses ditolak');
        }
        
        $validated = $request->validate([
            'target_roles' => 'required|array|min:1',
            'target_roles.*' => 'in:bendahara,pengurus_1,pengurus_2,pengurus_3,pengurus_4,sekretaris,staf_ahli_1,staf_ahli_2',
            'action' => 'required|string',
            'custom_action' => 'nullable|string|max:255',
            'comment' => 'nullable|string',
        ]);
        
        $finalAction = $validated['action'];
        if ($validated['action'] === 'Lainnya') {
            if (empty(trim($validated['custom_action'] ?? ''))) {
                return back()->with('error', 'Tindakan/Instruksi lainnya wajib diisi.')
                ->withErrors(['custom_action' => 'Tindakan/Instruksi lainnya wajib diisi.'])
                ->withInput();
            }
            $finalAction = trim($validated['custom_action']);
        }
        
        // Format string yang eksplisit untuk disposed_at
        $document->update([
            'status' => 'berjalan',
            'disposisi_data' => [
                'target_roles' => $validated['target_roles'],
                'action' => $finalAction,
                'action_type' => $validated['action'],
                'comment' => $validated['comment'] ?? null,
                'disposed_by' => $user->id,
                'disposed_at' => now()->toDateTimeString(), // Format: Y-m-d H:i:s
            ]
        ]);
        
        $rolesMap = [
            'sekretaris' => 'Sekretaris PKK',
            'bendahara' => 'Bendahara PKK',
            'staf_ahli_1' => 'Staf Ahli I',
            'staf_ahli_2' => 'Staf Ahli II',
            'pengurus_1' => 'Ketua Pengurus I',
            'pengurus_2' => 'Ketua Pengurus II',
            'pengurus_3' => 'Ketua Pengurus III',
            'pengurus_4' => 'Ketua Pengurus IV',
        ];

        $notificationCount = 0;
        $errors = [];

        foreach ($validated['target_roles'] as $role) {
            $targetUsers = \App\Models\User::where('sidongan_role', $role)->get();
            
            \Log::info("Disposisi: Mencari user dengan role '{$role}', ditemukan {$targetUsers->count()} user");
            
            if ($targetUsers->isEmpty()) {
                $errors[] = "Tidak ada user dengan role '{$rolesMap[$role]}'";
                continue;
            }
            
            foreach ($targetUsers as $targetUser) {
                try {
                    \Log::info("Disposisi: Membuat notifikasi untuk user ID {$targetUser->id} ({$targetUser->name})");
                    
                    $notification = \App\Models\Notification::create([
                        'user_id' => $targetUser->id,
                        'type' => 'disposisi.received',
                        'title' => 'Disposisi Baru',
                        'message' => "Anda menerima disposisi dari Ketua PKK untuk surat No. Agenda {$document->agenda_number} - {$document->subject}. Tindakan: {$finalAction}",
                        'related_id' => $document->id,
                        'related_type' => 'document',
                        'read_at' => null,
                    ]);
                    
                    \Log::info("Disposisi: Notifikasi berhasil dibuat dengan ID {$notification->id}");
                    $notificationCount++;
                    
                } catch (\Exception $e) {
                    \Log::error("Disposisi: Gagal membuat notifikasi untuk user {$targetUser->name}: " . $e->getMessage());
                    $errors[] = "Gagal mengirim notifikasi ke {$targetUser->name}";
                }
            }
        }

        // Notifikasi ke Sekretaris
        $sekretarisUsers = \App\Models\User::where('sidongan_role', 'sekretaris')->get();
        foreach ($sekretarisUsers as $sekretaris) {
            try {
                \App\Models\Notification::create([
                    'user_id' => $sekretaris->id,
                    'type' => 'disposisi.processed',
                    'title' => 'Disposisi Selesai',
                    'message' => "Surat No. Agenda {$document->agenda_number} telah didisposisi oleh Ketua PKK ke: " . implode(', ', array_map(fn($r) => $rolesMap[$r] ?? $r, $validated['target_roles'])),
                    'related_id' => $document->id,
                    'related_type' => 'document',
                    'read_at' => null,
                ]);
                $notificationCount++;
            } catch (\Exception $e) {
                \Log::error("Disposisi: Gagal membuat notifikasi ke sekretaris: " . $e->getMessage());
            }
        }
        
        \Log::info("Disposisi: Total {$notificationCount} notifikasi dikirim untuk surat {$document->agenda_number}");

        \App\Models\AdminActivityLog::log('updated', 'surat', $document->id, [
            'action' => 'disposisi',
            'target_roles' => $validated['target_roles'],
            'action_type' => $finalAction,
        ], $user->id);
        
        $message = "Disposisi berhasil! {$notificationCount} notifikasi terkirim.";
        $type = 'success';
        
        if (!empty($errors)) {
            $message .= " PERHATIAN: " . implode(', ', $errors);
            $type = 'warning';
        }
        
        return redirect()->route('sidongan.disposisi')
            ->with($type, $message);
    }

    /**
     * Halaman Arsip Surat
     */
    public function arsip()
    {
        $user = auth()->guard('sidongan')->user();
        
        // HANYA ambil surat yang sudah diarsipkan (status = 'diarsipkan')
        $query = Document::with(['category', 'creator', 'activityReports' => function($q) {
            $q->with('creator')->latest();
        }])
        ->where('status', 'diarsipkan');
        
        // Filter
        if (request('search')) {
            $query->search(request('search'));
        }
        if (request('category')) {
            $query->where('category_id', request('category'));
        }
        if (request('year')) {
            $query->whereYear('document_date', request('year'));
        }
        
        $documents = $query->latest()->paginate(15);
        
        // Stats
        $totalArsip = (clone $query)->count();
        $arsipBulanIni = (clone $query)->whereMonth('created_at', now()->month)->count();
        $arsipTahunIni = (clone $query)->whereYear('created_at', now()->year)->count();
        
        $categories = DocumentCategory::where('is_active', true)->orderBy('name')->get();
        
        return view('sidongan.arsip.index', compact(
            'documents', 
            'totalArsip', 
            'arsipBulanIni', 
            'arsipTahunIni',
            'categories'
        ));
    }

    /**
     * Archive document (hanya untuk Sekretaris)
     */
    public function archive(Document $document)
    {
        $user = auth()->guard('sidongan')->user();
        
        // Cek akses - hanya Sekretaris yang bisa archive
        if (!$user || !$user->hasSidonganRole('sekretaris')) {
            abort(403, 'Akses ditolak. Hanya Sekretaris yang dapat mengarsipkan surat.');
        }
        
        // Surat bisa diarsipkan dari status apapun
        
        // Update status menjadi diarsipkan
        $document->update([
            'status' => 'diarsipkan',
            'updated_by' => $user->id,
        ]);
        
        // Buat notifikasi ke Ketua
        $ketuaUsers = User::where('sidongan_role', 'ketua')->get();
        foreach ($ketuaUsers as $ketua) {
            \App\Models\Notification::create([
                'user_id' => $ketua->id,
                'type' => 'document.archived',
                'title' => 'Surat Diarsipkan',
                'message' => "Surat {$document->agenda_number} berhasil diarsipkan oleh Sekretaris.",
                'related_id' => $document->id,                'related_type' => Document::class,
            ]);
        }

        \App\Models\AdminActivityLog::log('updated', 'surat', $document->id, [
            'action' => 'archived',
        ], $user->id);
        return redirect()->route('sidongan.documents.show', $document)
            ->with('success', 'Surat berhasil diarsipkan!');
    }

    /**
     * Bulk Archive - Arsipkan beberapa surat sekaligus
     */
    public function bulkArchive(Request $request)
    {
        $user = auth()->guard('sidongan')->user();

        if (!$user || !$user->hasSidonganRole('sekretaris')) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:sidongan_documents,id',
        ]);

        $ids = $request->ids;
        $count = 0;

        Document::whereIn('id', $ids)->each(function($doc) use ($user, &$count) {
            if ($doc->status !== 'diarsipkan') {
                $doc->update([
                    'status' => 'diarsipkan',
                    'updated_by' => $user->id,
                ]);

                \App\Models\AdminActivityLog::log('updated', 'surat', $doc->id, [
                    'action' => 'bulk_archived',
                ], $user->id);

                $count++;
            }
        });

        return response()->json([
            'success' => true,
            'message' => "{$count} surat berhasil diarsipkan.",
            'count' => $count,
        ]);
    }

    /**
     * Bulk Delete - Hapus beberapa surat sekaligus
     */
    public function bulkDelete(Request $request)
    {
        $user = auth()->guard('sidongan')->user();

        if (!$user || !$user->hasSidonganRole('sekretaris')) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:sidongan_documents,id',
        ]);

        $ids = $request->ids;
        $count = 0;

        Document::whereIn('id', $ids)->each(function($doc) use ($user, &$count) {
            if (Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }

            \App\Models\AdminActivityLog::log('deleted', 'surat', $doc->id, [
                'agenda_number' => $doc->agenda_number,
                'action' => 'bulk_deleted',
            ], $user->id);

            $doc->delete();
            $count++;
        });

        return response()->json([
            'success' => true,
            'message' => "{$count} surat berhasil dihapus.",
            'count' => $count,
        ]);
    }

    /**
     * Print Lembar Disposisi
     */
    public function printDisposisi(Document $document)
    {
        $this->authorizeDocumentAccess($document);
        return view('sidongan.documents.disposisi-print', compact('document'));
    }

    /**
     * Halaman Notifikasi - HANYA tampilkan yang belum dibaca
     */
    public function notifications()
    {
        $user = auth()->guard('sidongan')->user();
        
        // ✅ HANYA ambil notifikasi yang BELUM dibaca (read_at = null)
        $notifications = Notification::where('user_id', $user->id)
            ->whereNull('read_at')  // ← Filter hanya yang belum dibaca
            ->latest()
            ->paginate(15);

        // Count = total yang ditampilkan (karena hanya unread)
        $unreadCount = $notifications->total();

        return view('sidongan.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark ALL notifications as read + AUTO DELETE ALL
     */
    public function markAllNotificationsAsRead()
    {
        $user = auth()->guard('sidongan')->user();
        
        if ($user) {
            // ✅ HAPUS SEMUA notifikasi user ini yang belum dibaca
            $count = Notification::where('user_id', $user->id)
                ->whereNull('read_at')  // Hanya yang belum dibaca
                ->delete();             // Langsung hapus
            
            return response()->json([
                'success' => true,
                'message' => "{$count} notifikasi dihapus",
                'count' => $count
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'User tidak ditemukan'
        ], 404);
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
