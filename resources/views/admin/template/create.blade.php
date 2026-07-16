@extends('admin.layouts.app')
@section('title', 'Tambah Template')
@section('page-title', 'Tambah Template Baru')

@section('content')
<style>
/* Responsive untuk Mobile */
@media (max-width: 768px) {
    .template-header {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 1rem !important;
    }
    
    .template-header h1 {
        font-size: 1.25rem !important;
    }
    
    .template-header .btn {
        width: 100% !important;
        justify-content: center !important;
    }
    
    .form-grid-2 {
        grid-template-columns: 1fr !important;
    }
    
    .status-options {
        flex-direction: column !important;
    }
    
    .status-options label {
        width: 100% !important;
    }
}
</style>

{{-- Header --}}
<div class="template-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;gap:1rem">
    <div style="flex:1;min-width:0">
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--text-dark);margin:0 0 0.25rem 0">Tambah Template</h1>
        <p style="color:var(--text-muted);margin:0;font-size:0.9rem">Unggah template dokumen baru untuk PKK</p>
    </div>
    <a href="{{ route('admin.template.index') }}" class="btn" style="background:#f8fafc;color:var(--text-dark);white-space:nowrap;flex-shrink:0">← Kembali</a>
</div>

{{-- Form Card --}}
<div class="card">
    <form action="{{ route('admin.template.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        {{-- Template Name --}}
        <div style="margin-bottom:1.5rem">
            <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Nama Template *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Contoh: Template Laporan Tahunan PKK 2024">
            <small style="color:var(--text-muted);display:block;margin-top:0.4rem;font-size:0.8rem">Judul template yang akan ditampilkan</small>
        </div>

        {{-- File Upload --}}
        <div style="margin-bottom:1.5rem">
            <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">File Template *</label>
            <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt" required id="fileInput">
            <small style="color:var(--text-muted);display:block;margin-top:0.4rem;font-size:0.8rem">
                Format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT • Maksimal: 10 MB
            </small>
            
            {{-- File Preview --}}
            <div id="filePreview" style="margin-top:1rem;display:none">
                <div style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1rem;background:#f8fafc;border-radius:10px">
                    <div style="width:40px;height:40px;border-radius:8px;background:rgba(139,92,246,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                    </div>
                    <div style="flex:1;min-width:0">
                        <div id="fileName" style="font-weight:600;color:var(--text-dark);font-size:0.9rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"></div>
                        <div id="fileSize" style="font-size:0.8rem;color:var(--text-muted)"></div>
                    </div>
                    <button type="button" onclick="clearFile()" style="background:#fef2f2;color:#ef4444;border:none;padding:0.4rem 0.75rem;border-radius:6px;cursor:pointer;font-size:0.8rem;display:inline-flex;align-items:center;gap:0.25rem">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Hapus
                    </button>
                </div>
            </div>
        </div>

        {{-- Date & Sort Order --}}
        <div class="form-grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem">
            <div>
                <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Tanggal Upload *</label>
                <input type="date" name="upload_date" class="form-control" value="{{ old('upload_date', date('Y-m-d')) }}" required>
                <small style="color:var(--text-muted);display:block;margin-top:0.4rem;font-size:0.8rem">Tanggal file diunggah</small>
            </div>
            <div>
                <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Urutan Tampil</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
                <small style="color:var(--text-muted);display:block;margin-top:0.4rem;font-size:0.8rem">Semakin kecil, semakin atas tampil</small>
            </div>
        </div>

        {{-- Status Radio Buttons --}}
        <div style="margin-bottom:2rem">
            <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Status *</label>
            <div class="status-options" style="display:flex;gap:1rem;flex-wrap:wrap">
                <label style="display:flex;align-items:center;gap:0.5rem;padding:0.65rem 1rem;background:#f8fafc;border:1px solid rgba(0,0,0,0.06);border-radius:8px;cursor:pointer;transition:all 0.2s" onmouseover="this.style.borderColor='rgba(34,197,94,0.5)'" onmouseout="this.style.borderColor='rgba(0,0,0,0.06)'">
                    <input type="radio" name="status" value="published" {{ old('status', 'published')==='published'?'checked':'' }} style="width:18px;height:18px;cursor:pointer">
                    <span style="display:inline-flex;align-items:center;gap:0.4rem;font-weight:500;font-size:0.9rem">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#166534" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        Published
                    </span>
                </label>
                <label style="display:flex;align-items:center;gap:0.5rem;padding:0.65rem 1rem;background:#f8fafc;border:1px solid rgba(0,0,0,0.06);border-radius:8px;cursor:pointer;transition:all 0.2s" onmouseover="this.style.borderColor='rgba(234,179,8,0.5)'" onmouseout="this.style.borderColor='rgba(0,0,0,0.06)'">
                    <input type="radio" name="status" value="draft" {{ old('status', 'published')==='draft'?'checked':'' }} style="width:18px;height:18px;cursor:pointer">
                    <span style="display:inline-flex;align-items:center;gap:0.4rem;font-weight:500;font-size:0.9rem">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#92400e" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="12" y1="12" x2="16" y2="12"/></svg>
                        Draft
                    </span>
                </label>
            </div>
            <small style="color:var(--text-muted);display:block;margin-top:0.5rem;font-size:0.85rem">
                Draft: hanya terlihat di admin • Published: tampil di website
            </small>
        </div>

        {{-- Action Buttons --}}
        <div style="display:flex;gap:0.75rem;justify-content:flex-end;padding-top:1rem;border-top:1px solid rgba(0,0,0,0.04)">
            <a href="{{ route('admin.template.index') }}" class="btn" style="background:#f8fafc;color:var(--text-dark)">Batal</a>
            <button type="submit" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                Simpan Template
            </button>
        </div>
    </form>
</div>

<script>
// Preview file name & size
const fileInput = document.getElementById('fileInput');
const filePreview = document.getElementById('filePreview');
const fileName = document.getElementById('fileName');
const fileSize = document.getElementById('fileSize');

fileInput?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file size (10MB max)
        if (file.size > 10 * 1024 * 1024) {
            alert('Ukuran file terlalu besar. Maksimal 10MB.');
            e.target.value = '';
            return;
        }
        
        fileName.textContent = file.name;
        
        // Format file size
        const units = ['B', 'KB', 'MB', 'GB'];
        let size = file.size;
        let unitIndex = 0;
        while (size >= 1024 && unitIndex < units.length - 1) {
            size /= 1024;
            unitIndex++;
        }
        fileSize.textContent = size.toFixed(2) + ' ' + units[unitIndex];
        
        filePreview.style.display = 'block';
    }
});

// Clear file selection
function clearFile() {
    fileInput.value = '';
    filePreview.style.display = 'none';
    fileName.textContent = '';
    fileSize.textContent = '';
}
</script>
@endsection