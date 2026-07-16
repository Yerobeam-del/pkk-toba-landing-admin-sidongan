<?php

namespace App\Http\Controllers\Sidongan;

use App\Http\Controllers\Controller;
use App\Models\ActivityReport;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /**
     * Display a listing of reports waiting for verification.
     */
    public function index(Request $request)
    {
        $user = auth()->guard('sidongan')->user();
        
        // Cek akses (Hanya Ketua)
        if (!$user->hasSidonganRole('ketua')) {
            abort(403, 'Akses ditolak');
        }
        
        // 1. Query Dasar
        $query = \App\Models\ActivityReport::with(['document', 'creator']);
        
        // 2. Filter Pencarian
        if ($request->filled('search')) {
            $query->where('kegiatan_nama', 'like', '%' . $request->search . '%');
        }
        
        // 3. Filter Status - HANYA jika ada parameter status DAN tidak kosong
        if ($request->has('status') && $request->filled('status')) {
            $query->where('status', $request->status);
        }
        // Jika status tidak ada atau kosong (Semua Status), jangan filter
        
        // 4. Eksekusi Query
        $documents = $query->latest()->paginate($request->get('per_page', 10));
        
        // 5. Append query parameters ke pagination links
        $documents->appends($request->except('page'));
        
        // 6. Kirim ke View
        return view('sidongan.verifikasi.index', compact('documents'));
    }
        
    /**
     * Show verification form for a specific report.
     */
    public function form($id)
    {
        $user = auth()->guard('sidongan')->user();
        
        if (!$user->hasSidonganRole('ketua')) {
            abort(403, 'Akses ditolak');
        }
        
        // ✅ SIMPAN URL SEBELUMNYA DI SESSION
        $previousUrl = url()->previous();
        
        // Hanya simpan jika bukan dari form verifikasi itu sendiri
        if ($previousUrl && 
            !str_contains($previousUrl, '/verifikasi/form') &&
            !str_contains($previousUrl, '/verifikasi-print')) {
            session(['verifikasi_form_back_url' => $previousUrl]);
        }
        
        $report = ActivityReport::with(['document', 'creator'])->findOrFail($id);
        
        return view('sidongan.verifikasi.form', compact('report'));
    }
    
    public function store(Request $request, $id)
    {
        $user = auth()->guard('sidongan')->user();
        
        if (!$user->hasSidonganRole('ketua')) {
            abort(403, 'Akses ditolak');
        }
        
        $validated = $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan_verifikasi' => 'nullable|string|max:500',
        ]);
        
        $report = ActivityReport::with('document.creator')->findOrFail($id);
        
        // Update status laporan
        $report->update([
            'status' => $validated['status'],
            'catatan_verifikasi' => $validated['catatan_verifikasi'] ?? null,
            'verified_by' => $user->id,
            'verified_at' => now(),
        ]);
        
        if ($report->document) {
            // Gunakan method updateCorrectStatus() yang sudah kita buat
            $newStatus = $report->document->updateCorrectStatus();
            
            $notifMessage = "";
            $notifTitle = "";
            
            if ($validated['status'] === 'disetujui') {
                if ($newStatus === 'selesai') {
                    $notifMessage = "Laporan kegiatan untuk surat {$report->document->agenda_number} telah disetujui. Surat selesai.";
                    $notifTitle = "Laporan Disetujui";
                } else {
                    $notifMessage = "Laporan kegiatan untuk surat {$report->document->agenda_number} telah disetujui. Menunggu laporan dari yang lain.";
                    $notifTitle = "Laporan Disetujui";
                }
            } else if ($validated['status'] === 'ditolak') {
                $catatan = $validated['catatan_verifikasi'] ? " Catatan: \"{$validated['catatan_verifikasi']}\"" : '';
                $notifMessage = "Laporan kegiatan untuk surat {$report->document->agenda_number} ditolak. Silakan perbaiki dan buat laporan baru.{$catatan}";
                $notifTitle = "Laporan Ditolak";
            }
            
            // KIRIM NOTIFIKASI KE PEMBUAT LAPORAN
            if (isset($notifMessage) && $report->created_by) {
                \App\Models\Notification::create([
                    'user_id' => $report->created_by,
                    'type' => 'laporan_verifikasi',
                    'title' => $notifTitle,
                    'message' => $notifMessage,
                    'related_id' => $report->document->id,
                    'related_type' => 'document',
                ]);
            }
        }
        
        $pesan = $validated['status'] === 'disetujui' 
            ? 'Laporan berhasil disetujui!' 
            : 'Laporan ditolak. Pembuat laporan akan diberi notifikasi.';
        
        return redirect()->route('sidongan.verifikasi')
            ->with('success', $pesan);
    }
}