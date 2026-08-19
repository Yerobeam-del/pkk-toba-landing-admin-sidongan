{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('admin.layouts.app')
@section('title', 'Edit Template')
@section('page-title', 'Edit Template')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-template-edit.css') }}">


{{-- Header --}}
<div class="template-header u-header-row">
    <div class="u-flex-1-min">
        <h1 class="u-page-title">Edit Template</h1>
        <p class="u-muted">Perbarui data template dokumen yang sudah ada</p>
    </div>
    <x-admin.back-button :href="route('admin.template.index')" />
</div>

{{-- Form Card --}}
<div class="card">
    <form action="{{ route('admin.template.update', $template) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        
        {{-- Template Name --}}
        <div class="u-mb-6">
            <label class="u-label">Nama Template *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $template->name) }}" required placeholder="Contoh: Template Laporan Tahunan PKK 2024">
            <small class="u-hint">Judul template yang akan ditampilkan</small>
        </div>

        {{-- File Upload --}}
        <div class="u-mb-6">
            <label class="u-label">File Template</label>
            <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt" id="fileInput">
            <small class="u-hint">
                Kosongkan jika tidak ingin mengubah file • Format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT • Max: 10 MB
            </small>
            
            {{-- Current File Info --}}
            <div id="currentFileInfo" style="margin-top:1rem;padding:0.75rem 1rem;background:#f8fafc;border-radius:10px;display:flex;align-items:center;gap:0.75rem">
                <div class="u-a37">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                </div>
                <div class="u-flex-1-min">
                    <div class="u-ellipsis-title">{{ $template->file_name }}</div>
                    <div class="u-text-muted-xs2">{{ $template->file_size }}</div>
                </div>
                <a href="{{ $template->file_url }}" target="_blank" style="color:var(--primary);font-size:0.85rem;text-decoration:none;display:inline-flex;align-items:center;gap:0.25rem">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    Lihat
                </a>
            </div>
            
            {{-- New File Preview (hidden by default) --}}
            <div class="u-a2" id="newFilePreview">
                <div style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1rem;background:#f0fdf4;border-radius:10px">
                    <div style="width:40px;height:40px;border-radius:8px;background:rgba(22,163,74,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                    </div>
                    <div class="u-flex-1-min">
                        <div class="u-ellipsis-title" id="fileName"></div>
                        <div class="u-text-muted-xs2" id="fileSize"></div>
                    </div>
                    <button class="u-a38" type="button" data-action="clear-file">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Ganti
                    </button>
                </div>
            </div>
        </div>

        {{-- Date & Sort Order --}}
        <div class="form-grid-2 u-grid-2">
            <div>
                <label class="u-label">Tanggal Upload *</label>
                <input type="date" name="upload_date" class="form-control" value="{{ old('upload_date', $template->upload_date?->format('Y-m-d')) }}" required>
                <small class="u-hint">Tanggal file diunggah</small>
            </div>
            <div>
                <label class="u-label">Urutan Tampil</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $template->sort_order) }}" min="0">
                <small class="u-hint">Semakin kecil, semakin atas tampil</small>
            </div>
        </div>

        {{-- Status Radio Buttons --}}
        <div class="u-mb-8">
            <label class="u-label">Status *</label>
            <div class="status-options u-a39">
                <label class="u-a5">
                    <input type="radio" name="status" value="published" {{ old('status', $template->status)==='published'?'checked':'' }} style="width:18px;height:18px;cursor:pointer">
                    <span class="u-inline-flex-gap-2-semibold">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#166534" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        Published
                    </span>
                </label>
                <label class="u-a5">
                    <input type="radio" name="status" value="draft" {{ old('status', $template->status)==='draft'?'checked':'' }} style="width:18px;height:18px;cursor:pointer">
                    <span class="u-inline-flex-gap-2-semibold">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#92400e" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="12" y1="12" x2="16" y2="12"/></svg>
                        Draft
                    </span>
                </label>
            </div>
            <small class="u-hint-sm">
                Draft: hanya terlihat di admin • Published: tampil di website
            </small>
        </div>

        {{-- Action Buttons --}}
        <div class="u-form-actions">
            <x-admin.cancel-button :href="route('admin.template.index')" />
            <button type="submit" class="btn btn-primary u-inline-flex-center-gap-2">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                Update Template
            </button>
        </div>
    </form>
    
    {{-- Validation Errors --}}
    @if($errors->any())
    <div class="u-alert-danger-box">
        <strong class="u-label-simple">Periksa kembali input berikut:</strong>
        <ul class="u-list-indent">
            @foreach($errors->all() as $err) <li class="u-mb-1">{{ $err }}</li> @endforeach
        </ul>
    </div>
    @endif
</div>

    <script src="{{ asset('assets/admin/js/admin-template-edit.js') }}"></script>

@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
