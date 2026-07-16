<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\ActivityReport;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SidonganDataController extends Controller
{
    /**
     * Tampilkan semua data SIDONGAN dengan stats
     */
    public function index(Request $request)
    {
        // Query dasar
        $query = Document::with(['category', 'creator']);
        
        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'LIKE', "%{$search}%")
                  ->orWhere('agenda_number', 'LIKE', "%{$search}%")
                  ->orWhere('document_number', 'LIKE', "%{$search}%")
                  ->orWhere('sender', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('document_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('document_date', '<=', $request->date_to);
        }
        
        // Pagination
        $documents = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        
        // Stats
        $stats = [
            'total' => Document::count(),
            'menunggu_disposisi' => Document::where('status', 'menunggu_disposisi')->count(),
            'berjalan' => Document::where('status', 'berjalan')->count(),
            'menunggu_verifikasi' => Document::where('status', 'menunggu_verifikasi')->count(),
            'selesai' => Document::where('status', 'selesai')->count(),
            'diarsipkan' => Document::where('status', 'diarsipkan')->count(),
            'total_laporan' => ActivityReport::count(),
            'total_notifikasi' => Notification::count(),
        ];
        
        // Ukuran storage
        $storageUsed = 0;
        $documentsForSize = Document::whereNotNull('file_path')->get();
        foreach ($documentsForSize as $doc) {
            if (Storage::disk('public')->exists($doc->file_path)) {
                $storageUsed += Storage::disk('public')->size($doc->file_path);
            }
        }
        $stats['storage_used'] = $this->formatBytes($storageUsed);
        $stats['storage_bytes'] = $storageUsed;
        
        $categories = DocumentCategory::orderBy('name')->get();
        
        return view('admin.sidongan-data.index', compact('documents', 'stats', 'categories'));
    }
    
    /**
     * Proses pembersihan data
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete_archived,delete_completed,delete_old,delete_all_reports,delete_all_notifications',
            'days' => 'nullable|integer|min:1',
            'confirm' => 'required|accepted',
        ]);
        
        $action = $request->action;
        $deletedCount = 0;
        $deletedFiles = 0;
        $message = '';
        
        DB::beginTransaction();
        
        try {
            switch ($action) {
                case 'delete_archived':
                    // Hapus semua surat yang sudah diarsipkan
                    $docs = Document::where('status', 'diarsipkan')->get();
                    foreach ($docs as $doc) {
                        if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                            Storage::disk('public')->delete($doc->file_path);
                            $deletedFiles++;
                        }
                        // Hapus activity reports terkait
                        ActivityReport::where('document_id', $doc->id)->delete();
                        $doc->delete();
                        $deletedCount++;
                    }
                    $message = "{$deletedCount} surat arsip berhasil dihapus ({$deletedFiles} file dihapus dari storage).";
                    break;
                    
                case 'delete_completed':
                    // Hapus semua surat yang sudah selesai
                    $docs = Document::where('status', 'selesai')->get();
                    foreach ($docs as $doc) {
                        if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                            Storage::disk('public')->delete($doc->file_path);
                            $deletedFiles++;
                        }
                        ActivityReport::where('document_id', $doc->id)->delete();
                        $doc->delete();
                        $deletedCount++;
                    }
                    $message = "{$deletedCount} surat selesai berhasil dihapus ({$deletedFiles} file dihapus dari storage).";
                    break;
                    
                case 'delete_old':
                    // Hapus surat berdasarkan umur (hari)
                    $days = $request->days ?? 365;
                    $cutoffDate = now()->subDays($days);
                    
                    $docs = Document::where('created_at', '<', $cutoffDate)
                        ->whereIn('status', ['selesai', 'diarsipkan'])
                        ->get();
                    
                    foreach ($docs as $doc) {
                        if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                            Storage::disk('public')->delete($doc->file_path);
                            $deletedFiles++;
                        }
                        ActivityReport::where('document_id', $doc->id)->delete();
                        $doc->delete();
                        $deletedCount++;
                    }
                    $message = "{$deletedCount} surat lama (>{$days} hari) berhasil dihapus ({$deletedFiles} file dihapus dari storage).";
                    break;
                    
                case 'delete_all_reports':
                    // Hapus semua activity reports
                    $deletedCount = ActivityReport::count();
                    
                    // Hapus file foto laporan
                    $reports = ActivityReport::all();
                    foreach ($reports as $report) {
                        if ($report->fotos) {
                            $fotos = json_decode($report->fotos, true);
                            if (is_array($fotos)) {
                                foreach ($fotos as $foto) {
                                    if (Storage::disk('public')->exists($foto)) {
                                        Storage::disk('public')->delete($foto);
                                        $deletedFiles++;
                                    }
                                }
                            }
                        }
                    }
                    
                    ActivityReport::query()->delete(); // GANTI DARI truncate()
                    $message = "{$deletedCount} laporan kegiatan berhasil dihapus ({$deletedFiles} file foto dihapus).";
                    break;
                    
                case 'delete_all_notifications':
                    // Hapus semua notifikasi
                    $deletedCount = Notification::count();
                    Notification::query()->delete(); // GANTI DARI truncate()
                    $message = "{$deletedCount} notifikasi berhasil dihapus.";
                    break;
            }
            
            DB::commit();
            
            return redirect()->route('admin.sidongan-data.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.sidongan-data.index')
                ->with('error', 'Gagal melakukan pembersihan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail surat dengan timeline
     */
    public function show(Document $document)
    {
        // Load document dengan relasi yang benar
        $document->load(['category', 'creator', 'activityReports.creator']);
        
        // Ambil semua notifikasi terkait
        $notifications = Notification::where('related_id', $document->id)
            ->where('related_type', Document::class)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Stats untuk laporan
        $stats = [
            'total_laporan' => ActivityReport::where('document_id', $document->id)->count(),
        ];
        
        return view('admin.sidongan-data.show', compact('document', 'notifications', 'stats'));
    }

    /**
     * Hapus laporan kegiatan tertentu
     */
    public function deleteReport($reportId)
    {
        try {
            $report = ActivityReport::findOrFail($reportId);
            $documentId = $report->document_id;
            
            // Hapus file foto
            if ($report->fotos) {
                $fotos = json_decode($report->fotos, true);
                if (is_array($fotos)) {
                    foreach ($fotos as $foto) {
                        if (Storage::disk('public')->exists($foto)) {
                            Storage::disk('public')->delete($foto);
                        }
                    }
                }
            }
            
            $report->delete();
            
            return redirect()->back()
                ->with('success', 'Laporan kegiatan berhasil dihapus.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus laporan: ' . $e->getMessage());
        }
    }
    
    /**
     * Hapus satu surat tertentu
     */
    public function destroy(Document $document)
    {
        try {
            // Hapus file
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            
            // Hapus activity reports terkait
            ActivityReport::where('document_id', $document->id)->delete();
            
            // Hapus notifikasi terkait
            Notification::where('related_id', $document->id)
                ->where('related_type', Document::class)
                ->delete();
            
            // Hapus dokumen
            $document->delete();
            
            return redirect()->route('admin.sidongan-data.index')
                ->with('success', 'Surat berhasil dihapus permanen.');
                
        } catch (\Exception $e) {
            return redirect()->route('admin.sidongan-data.index')
                ->with('error', 'Gagal menghapus surat: ' . $e->getMessage());
        }
    }
    
    /**
     * Format bytes ke ukuran yang mudah dibaca
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}