<?php

namespace App\Http\Controllers\Sidongan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ActivityReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->guard('sidongan')->user();
        $role = $user->sidongan_role;
        
        // AUTO-UPDATE: Cek dokumen yang perlu update status
        $documentsToCheck = \App\Models\Document::whereIn('sidongan_documents.status', ['berjalan', 'menunggu_verifikasi', 'selesai'])
            ->whereNotNull('disposisi_data')
            ->get();
        
        foreach ($documentsToCheck as $doc) {
            $doc->updateCorrectStatus();
        }
        
        // QUERY DASAR: Cari dokumen yang didisposisi ke role user ini
        // EXCLUDE: Surat yang sudah diarsipkan atau menunggu disposisi
        $query = \App\Models\Document::where('disposisi_data', 'LIKE', '%' . $role . '%')
            ->whereNotIn('sidongan_documents.status', ['diarsipkan', 'menunggu_disposisi'])
            ->with(['creator']);
        
        // FILTER SEARCH
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('sidongan_documents.subject', 'like', "%{$search}%")
                  ->orWhere('sidongan_documents.title', 'like', "%{$search}%")
                  ->orWhere('sidongan_documents.agenda_number', 'like', "%{$search}%");
            });
        }
        
        // FILTER STATUS
        if ($request->filled('status')) {
            $status = $request->status;
            
            if ($status === 'draft') {
                // Perlu Dilaporkan = dokumen yang BELUM ada laporan dari user ini
                $query->whereDoesntHave('activityReports', function($q) use ($user) {
                    $q->where('created_by', $user->id);
                });
            } else {
                // Status lain = filter berdasarkan activity_reports dengan status tertentu
                $query->whereHas('activityReports', function($q) use ($user, $status) {
                    $q->where('created_by', $user->id)
                      ->where('activity_reports.status', $status);
                });
            }
        }
        
        // ORDER BY STATUS PRIORITY
        $query->leftJoin('activity_reports', function($join) use ($user) {
            $join->on('sidongan_documents.id', '=', 'activity_reports.document_id')
                 ->where('activity_reports.created_by', '=', $user->id);
        })
        ->select('sidongan_documents.*') // PENTING: Mencegah ambiguitas kolom
        ->orderByRaw("
            CASE 
                WHEN activity_reports.id IS NULL THEN 1  -- Perlu Dilaporkan (Paling Atas)
                WHEN activity_reports.status = 'ditolak' THEN 2  -- Perlu Revisi
                WHEN activity_reports.status = 'menunggu_verifikasi' THEN 3  -- Menunggu Verifikasi
                WHEN activity_reports.status = 'disetujui' THEN 4  -- Disetujui
                ELSE 5  -- Selesai/Lainnya (Paling Bawah)
            END
        ")
        ->orderBy('sidongan_documents.created_at', 'desc'); // Urutkan berdasarkan tanggal untuk status yang sama
        
        // PAGINATION (Hapus latest() agar tidak menimpa orderBy di atas)
        $perPage = $request->per_page ?? 10;
        $documents = $query->paginate($perPage)->withQueryString();
        
        // STATS
        $totalLaporan = \App\Models\ActivityReport::where('created_by', $user->id)->count();
        $menungguVerifikasi = \App\Models\ActivityReport::where('created_by', $user->id)->where('status', 'menunggu_verifikasi')->count();
        $disetujui = \App\Models\ActivityReport::where('created_by', $user->id)->where('status', 'disetujui')->count();
        $ditolak = \App\Models\ActivityReport::where('created_by', $user->id)->where('status', 'ditolak')->count();
        
        // Perlu Dilaporkan = dokumen yang didisposisi ke user ini tapi BELUM ada laporan
        $perluDilaporkan = \App\Models\Document::where('disposisi_data', 'LIKE', '%' . $role . '%')
            ->whereNotIn('sidongan_documents.status', ['diarsipkan', 'menunggu_disposisi'])
            ->whereDoesntHave('activityReports', function($q) use ($user) {
                $q->where('created_by', $user->id);
            })
            ->count();
        
        return view('sidongan.lapor-kegiatan.index', compact(
            'user', 'documents', 'totalLaporan', 'menungguVerifikasi',
            'disetujui', 'ditolak', 'perluDilaporkan'
        ));
    }

    public function create($document_id = null)
    {
        $document = null;
        
        if ($document_id) {
            $document = \App\Models\Document::findOrFail($document_id);
            $user = auth()->guard('sidongan')->user();

            // VALIDASI: Surat yang sudah diarsipkan tidak bisa dilaporkan
            if ($document->status === 'diarsipkan') {
                return redirect()->route('sidongan.lapor_kegiatan.index')
                    ->with('error', 'Surat yang sudah diarsipkan tidak dapat dilaporkan.');
            }

            // VALIDASI: Surat yang menunggu disposisi tidak bisa dilaporkan
            if ($document->status === 'menunggu_disposisi') {
                return redirect()->route('sidongan.lapor_kegiatan.index')
                    ->with('error', 'Surat ini belum didisposisi oleh Ketua PKK.');
            }

            $dispo = is_string($document->disposisi_data)
                ? json_decode($document->disposisi_data, true)
                : $document->disposisi_data;

            $targetRoles = $dispo['target_roles'] ?? [];

            if (!in_array($user->sidongan_role, $targetRoles)) {
                abort(403, 'Anda tidak berhak membuat laporan untuk surat ini.');
            }
        }
        
        return view('sidongan.lapor-kegiatan.create', compact('document'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_id' => 'required|exists:sidongan_documents,id',
            'kegiatan_nama' => 'required|string|max:255',
            'kegiatan_tanggal' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'provinsi' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kelurahan' => 'required|string|max:255',
            'alamat_lengkap' => 'required|string',
            'deskripsi' => 'required|string',
            'fotos.*' => 'nullable|file|mimes:jpg,jpeg,png,heic|max:5120',
        ], [
            'end_time.after' => 'Jam selesai harus lebih besar dari jam mulai.',
            'fotos.*.mimes' => 'File foto harus berformat JPG, JPEG, PNG, atau HEIC.',
            'fotos.*.max' => 'Ukuran foto maksimal 5MB.',
        ]);
        
        $document = \App\Models\Document::find($validated['document_id']);
        $user = auth()->guard('sidongan')->user();
        
        $dispo = is_string($document->disposisi_data)
            ? json_decode($document->disposisi_data, true)
            : $document->disposisi_data;
        
        $targetRoles = $dispo['target_roles'] ?? [];
        
        if (!in_array($user->sidongan_role, $targetRoles)) {
            return back()->withErrors(['document_id' => 'Gagal: Anda tidak berhak melapor untuk surat ini.'])->withInput();
        }
        
        // Handle upload foto
        $fotoPaths = [];
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                $filename = time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();
                $path = $foto->storeAs('activity-reports', $filename, 'public');
                $fotoPaths[] = $path;
            }
        }
        
        // CEK APAKAH ADA LAPORAN YANG DITOLAK SEBELUMNYA
        $rejectedReport = \App\Models\ActivityReport::where('document_id', $validated['document_id'])
            ->where('created_by', $user->id)
            ->where('status', 'ditolak')
            ->first();
        
        if ($rejectedReport) {
            // UPDATE LAPORAN YANG DITOLAK
            
            // Hapus foto lama dari storage
            $oldFotos = json_decode($rejectedReport->fotos, true) ?? [];
            foreach ($oldFotos as $oldFoto) {
                if (Storage::disk('public')->exists($oldFoto)) {
                    Storage::disk('public')->delete($oldFoto);
                }
            }
            
            // Update laporan dengan data baru
            $rejectedReport->update([
                'kegiatan_nama' => $validated['kegiatan_nama'],
                'kegiatan_tanggal' => $validated['kegiatan_tanggal'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'provinsi' => $validated['provinsi'],
                'kabupaten' => $validated['kabupaten'],
                'kecamatan' => $validated['kecamatan'],
                'kelurahan' => $validated['kelurahan'],
                'alamat_lengkap' => $validated['alamat_lengkap'],
                'deskripsi' => $validated['deskripsi'],
                'fotos' => json_encode($fotoPaths),
                'status' => 'menunggu_verifikasi',
                'catatan_verifikasi' => null,
                'verified_by' => null,
                'verified_at' => null,
            ]);
            
            $report = $rejectedReport;
            $action = 'updated';
            
        } else {
            // BUAT LAPORAN BARU
            $report = \App\Models\ActivityReport::create([
                'document_id' => $validated['document_id'],
                'kegiatan_nama' => $validated['kegiatan_nama'],
                'kegiatan_tanggal' => $validated['kegiatan_tanggal'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'provinsi' => $validated['provinsi'],
                'kabupaten' => $validated['kabupaten'],
                'kecamatan' => $validated['kecamatan'],
                'kelurahan' => $validated['kelurahan'],
                'alamat_lengkap' => $validated['alamat_lengkap'],
                'deskripsi' => $validated['deskripsi'],
                'fotos' => json_encode($fotoPaths),
                'status' => 'menunggu_verifikasi',
                'created_by' => auth()->guard('sidongan')->id(),
            ]);
            
            $action = 'created';
        }
        
        // UPDATE STATUS DOKUMEN DENGAN LOGIC YANG BENAR
        if ($document) {
            $newStatus = $document->updateCorrectStatus();
            
            // Kirim notifikasi ke Ketua
            $ketuaUsers = \App\Models\User::where('sidongan_role', 'ketua')->get();
            foreach ($ketuaUsers as $ketua) {
                $message = $action === 'updated' 
                    ? "Laporan kegiatan untuk surat No. Agenda {$document->agenda_number} telah diperbarui oleh {$user->name} dan menunggu verifikasi ulang."
                    : "Laporan kegiatan untuk surat No. Agenda {$document->agenda_number} telah dikirim oleh {$user->name} dan menunggu verifikasi.";
                
                \App\Models\Notification::create([
                    'user_id' => $ketua->id,
                    'type' => 'laporan.submitted',
                    'title' => $action === 'updated' ? 'Laporan Diperbarui' : 'Laporan Kegiatan Baru',
                    'message' => $message,
                    'related_id' => $document->id,
                    'related_type' => 'document',
                ]);
            }
        }
        
        $successMessage = $action === 'updated' 
            ? 'Laporan kegiatan berhasil diperbarui dan dikirim ulang untuk verifikasi!'
            : 'Laporan kegiatan berhasil dikirim untuk verifikasi!';
        
        return redirect()->route('sidongan.lapor_kegiatan.index')
            ->with('success', $successMessage);
    }

    public function show(string $id)
    {
        $previousUrl = url()->previous();
        $currentUrl = url()->current();
        
        // Helper function untuk cek apakah URL adalah Form Verifikasi
        $isVerifikasiForm = function($url) {
            // Match pattern: /verifikasi/form/{angka} (Form Verifikasi)
            return preg_match('#/verifikasi/form/\d+#', $url) === 1;
        };
        
        // Validasi: URL sebelumnya harus valid dan bukan halaman yang tidak diinginkan
        if ($previousUrl && 
            $previousUrl !== $currentUrl && 
            !str_contains($previousUrl, '/create') &&
            !str_contains($previousUrl, '/edit') &&
            !$isVerifikasiForm($previousUrl)) {  // ← Hanya tolak Form Verifikasi, bukan list
            session(['detail_laporan_back_url' => $previousUrl]);
        }
        
        $report = \App\Models\ActivityReport::with(['document', 'creator'])->findOrFail($id);
        return view('sidongan.lapor-kegiatan.show', compact('report'));
    }

    public function edit(string $id)
    {
        $report = \App\Models\ActivityReport::findOrFail($id);
        $user = auth()->guard('sidongan')->user();
        
        if ($report->created_by !== $user->id) {
            abort(403, 'Anda tidak berhak mengedit laporan ini.');
        }
        
        // HANYA blokir jika sudah disetujui. Status 'ditolak' BOLEH diedit untuk revisi.
        if ($report->status === 'disetujui') {
            return back()->with('error', 'Laporan yang sudah disetujui tidak dapat diedit.');
        }
        
        return view('sidongan.lapor-kegiatan.edit', compact('report'));
    }

    public function update(Request $request, string $id)
    {
        $report = \App\Models\ActivityReport::findOrFail($id);
        $user = auth()->guard('sidongan')->user();
        
        if ($report->created_by !== $user->id) {
            abort(403, 'Anda tidak berhak mengupdate laporan ini.');
        }
        
        // HANYA blokir jika sudah disetujui. Status 'ditolak' BOLEH diupdate untuk revisi.
        if ($report->status === 'disetujui') {
            return back()->with('error', 'Laporan yang sudah disetujui tidak dapat diupdate.');
        }
        
        $validated = $request->validate([
            'kegiatan_nama' => 'required|string|max:255',
            'kegiatan_tanggal' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'provinsi' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kelurahan' => 'required|string|max:255',
            'alamat_lengkap' => 'required|string',
            'deskripsi' => 'required|string',
            'fotos.*' => 'nullable|file|mimes:jpg,jpeg,png,heic|max:5120',
        ], [
            'end_time.after' => 'Jam selesai harus lebih besar dari jam mulai.',
        ]);
        
        // Handle upload foto baru
        $fotoPaths = [];
        if ($request->hasFile('fotos')) {
            $oldFotos = json_decode($report->fotos, true) ?? [];
            foreach ($oldFotos as $oldFoto) {
                if (Storage::disk('public')->exists($oldFoto)) {
                    Storage::disk('public')->delete($oldFoto);
                }
            }
            
            foreach ($request->file('fotos') as $foto) {
                $filename = time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();
                $path = $foto->storeAs('activity-reports', $filename, 'public');
                $fotoPaths[] = $path;
            }
        } else {
            $fotoPaths = json_decode($report->fotos, true) ?? [];
        }
        
        $report->update([
            'kegiatan_nama' => $validated['kegiatan_nama'],
            'kegiatan_tanggal' => $validated['kegiatan_tanggal'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'provinsi' => $validated['provinsi'],
            'kabupaten' => $validated['kabupaten'],
            'kecamatan' => $validated['kecamatan'],
            'kelurahan' => $validated['kelurahan'],
            'alamat_lengkap' => $validated['alamat_lengkap'],
            'deskripsi' => $validated['deskripsi'],
            'fotos' => json_encode($fotoPaths),
            'status' => 'menunggu_verifikasi', // Reset status agar masuk antrean verifikasi ulang
            'catatan_verifikasi' => null,      // Hapus catatan penolakan lama
            'verified_by' => null,             // Reset verifier
            'verified_at' => null,             // Reset waktu verifikasi
        ]);
        
        // Update status dokumen induk agar kembali ke 'berjalan' atau 'menunggu_verifikasi'
        $document = $report->document;
        if ($document) {
            $document->updateCorrectStatus();
        }
        
        return redirect()->route('sidongan.lapor_kegiatan.show', $report->id)
            ->with('success', 'Laporan kegiatan berhasil diperbarui dan dikirim ulang untuk verifikasi!');
    }

    public function destroy(string $id)
    {
        $report = \App\Models\ActivityReport::findOrFail($id);
        $user = auth()->guard('sidongan')->user();
        
        if ($report->created_by !== $user->id) {
            return back()->with('error', 'Anda tidak berhak menghapus laporan ini.');
        }
        
        if (in_array($report->status, ['disetujui', 'ditolak'])) {
            return back()->with('error', 'Laporan yang sudah diverifikasi tidak dapat dihapus.');
        }
        
        // Hapus foto dari storage
        $fotos = json_decode($report->fotos, true) ?? [];
        foreach ($fotos as $foto) {
            if (Storage::disk('public')->exists($foto)) {
                Storage::disk('public')->delete($foto);
            }
        }
        
        // Hapus laporan
        $documentId = $report->document_id;
        $report->delete();
        
        // UPDATE STATUS DOKUMEN SETELAH HAPUS LAPORAN
        $document = \App\Models\Document::find($documentId);
        if ($document) {
            $document->updateCorrectStatus();
        }
        
        return redirect()->route('sidongan.lapor_kegiatan.index')
            ->with('success', 'Laporan kegiatan berhasil dihapus!');
    }
}