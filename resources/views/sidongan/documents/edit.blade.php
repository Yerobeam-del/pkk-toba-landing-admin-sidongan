{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('sidongan.layouts.app')
@section('title', 'Edit Surat - SIDONGAN')

@section('content')
@php
    $currentUser = auth()->guard('sidongan')->user();
    $today = date('Y-m-d');
@endphp

    <link rel="stylesheet" href="{{ asset('assets/sidongan/css/sidongan-documents-edit.css') }}">


<div class="sd-page u-px-6">
    {{-- Header --}}
    <div style="background: linear-gradient(135deg, #0891b2, #14b8a6); padding: 1.5rem 2rem; border-radius: 1rem; margin-bottom: 1.5rem; color: white; box-shadow: 0 4px 20px rgba(8, 145, 178, 0.2);" class="animate-slide-in">
        <div class="sd-page-header u-a89">
            <div class="u-flex-center-gap-3">
                <div class="u-icon-badge-sm">
                    <i class="fas fa-edit u-a90"></i>
                </div>
                <div>
                    <h1 class="u-h3">Edit Surat</h1>
                    <p class="u-subtitle-flat">Update informasi surat masuk</p>
                </div>
            </div>
            {{-- Wadah tombol aksi header: aturan mobile bersama ada di kelas sd-header-actions --}}
            <div class="sd-header-actions">
                <a href="{{ route('sidongan.documents.index') }}" class="sd-btn-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Error Alert Box --}}
    @if($errors->any())
    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem; padding: 1rem 1.25rem; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.1);" class="animate-slide-in">
        <div class="u-a85">
            <div style="width: 2.5rem; height: 2.5rem; background: #fee2e2; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-exclamation-triangle" style="color: #dc2626; font-size: 1.125rem;"></i>
            </div>
            <div>
                <h3 style="font-size: 0.95rem; font-weight: 700; color: #991b1b; margin: 0;">Terdapat Kesalahan pada Form</h3>
                <p style="font-size: 0.8rem; color: #b91c1c; margin: 0.25rem 0 0 0;">Silakan perbaiki field yang ditandai di bawah ini</p>
            </div>
        </div>
        <ul style="margin: 0; padding-left: 1.5rem; color: #991b1b; font-size: 0.85rem; line-height: 1.6;">
            @foreach($errors->all() as $error)
                <li class="u-a86">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('sidongan.documents.update', $document) }}" method="POST" enctype="multipart/form-data" id="editForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="delete_file" id="deleteFileInput" value="0">

        <div class="animate-slide-in u-card-white">
            {{-- Form Header --}}
            <div style="padding: 1.25rem 1.5rem; background: linear-gradient(135deg, #f0fdf4, #dcfce7); border-bottom: 2px solid #86efac;">
                <h2 style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-file-alt u-a80"></i>
                    Informasi Surat
                </h2>
            </div>

            <div class="u-p-6">
                <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    {{-- Kolom Kiri: Data Surat --}}
                    <div style="display: grid; gap: 1.25rem;">
                        <h3 style="font-size: 0.9rem; font-weight: 600; color: #334155; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-envelope" style="color: #3b82f6; font-size: 0.875rem;"></i>
                            Data Surat
                        </h3>
                        <div>
                            <label class="u-field-note">Pengirim <span class="u-text-danger">*</span></label>
                            <input type="text" name="sender" value="{{ old('sender', $document->sender) }}" required 
                                style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid {{ $errors->has('sender') ? '#ef4444' : '#e2e8f0' }}; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s;">
                            @error('sender')
                                <div class="u-inline-alert-danger">
                                    <i class="fas fa-exclamation-circle u-error-msg"></i>
                                    <span class="u-text-danger-soft">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                        <div>
                            <label class="u-field-note">Nomor Surat <span class="u-text-danger">*</span></label>
                            <input type="text" name="document_number" value="{{ old('document_number', $document->document_number) }}" required 
                                style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid {{ $errors->has('document_number') ? '#ef4444' : '#e2e8f0' }}; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s;">
                            @error('document_number')
                                <div class="u-inline-alert-danger">
                                    <i class="fas fa-exclamation-circle u-error-msg"></i>
                                    <span class="u-text-danger-soft">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                        <div>
                            <label class="u-field-note">Tanggal Surat <span class="u-text-danger">*</span></label>
                            <input type="date" name="document_date" id="document_date" value="{{ old('document_date', $document->document_date?->format('Y-m-d')) }}" required
                                style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid {{ $errors->has('document_date') ? '#ef4444' : '#e2e8f0' }}; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s;">
                            @error('document_date')
                                <div class="u-inline-alert-danger">
                                    <i class="fas fa-exclamation-circle u-error-msg"></i>
                                    <span class="u-text-danger-soft">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                        <div>
                            <label class="u-field-note">Perihal <span class="u-text-danger">*</span></label>
                            <input type="text" name="subject" value="{{ old('subject', $document->subject) }}" required 
                                style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid {{ $errors->has('subject') ? '#ef4444' : '#e2e8f0' }}; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s;">
                            @error('subject')
                                <div class="u-inline-alert-danger">
                                    <i class="fas fa-exclamation-circle u-error-msg"></i>
                                    <span class="u-text-danger-soft">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Kolom Kanan: Data Agenda --}}
                    <div style="display: grid; gap: 1.25rem;">
                        <h3 style="font-size: 0.9rem; font-weight: 600; color: #334155; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-clipboard-list" style="color: #2563eb; font-size: 0.875rem;"></i>
                            Data Agenda
                        </h3>
                        <div>
                            <label class="u-field-note">Nomor Agenda</label>
                            <input type="text" id="preview_agenda_edit" value="{{ $document->agenda_number ?? 'Belum ada' }}" readonly 
                                style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; background: #f8fafc; color: #1e293b; cursor: not-allowed; font-family: monospace; font-weight: 600;">
                            <input type="hidden" name="agenda_number" id="agenda_number_edit_input" value="{{ $document->agenda_number ?? '' }}">
                            <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">Klik dua kali untuk mengubah nomor agenda</p>
                        </div>
                        <div>
                            <label class="u-field-note">Tanggal Diterima <span class="u-text-danger">*</span></label>
                            <input type="date" name="agenda_date" id="agenda_date_edit" 
                                value="{{ old('agenda_date', $document->agenda_date?->format('Y-m-d') ?? $document->document_date?->format('Y-m-d') ?? date('Y-m-d')) }}" 
                                max="{{ date('Y-m-d') }}"
                                style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid {{ $errors->has('agenda_date') ? '#ef4444' : '#e2e8f0' }}; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s;">
                            <small style="color: #94a3b8; display: block; margin-top: 0.25rem; font-size: 0.7rem;">Tanggal surat diterima di Sekretariat</small>
                            @error('agenda_date')
                                <div class="u-inline-alert-danger">
                                    <i class="fas fa-exclamation-circle u-error-msg"></i>
                                    <span class="u-text-danger-soft">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                        <div>
                            <label class="u-field-note">Saran Sekretaris</label>
                            <textarea name="suggestion" rows="4" 
                                style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid {{ $errors->has('suggestion') ? '#ef4444' : '#e2e8f0' }}; border-radius: 0.5rem; font-size: 0.875rem; resize: vertical; transition: all 0.2s;">{{ old('suggestion', $document->suggestion) }}</textarea>
                            @error('suggestion')
                                <div class="u-inline-alert-danger">
                                    <i class="fas fa-exclamation-circle u-error-msg"></i>
                                    <span class="u-text-danger-soft">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Lampiran File --}}
                <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 2px solid #f1f5f9;">
                    <h3 style="font-size: 0.9rem; font-weight: 600; color: #334155; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-paperclip" style="color: #10b981; font-size: 0.875rem;"></i>
                        Lampiran File
                    </h3>

                    {{-- Tampilkan File Saat Ini (Jika Ada) --}}
                    @if($document->file_path)
                    <div id="currentFileCard" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 1rem; transition: all 0.2s;">
                        <div style="width: 3rem; height: 3rem; background: #fee2e2; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-file-pdf u-text-danger-lg"></i>
                        </div>
                        <div class="u-flex-1-min">
                            <p style="font-size: 0.875rem; font-weight: 600; color: #0f172a; margin: 0 0 0.125rem 0;">{{ $document->file_name }}</p>
                            <p class="u-text-xs-muted-flat">{{ $document->file_size ? round($document->file_size / 1024, 2) . ' KB' : 'File saat ini' }}</p>
                        </div>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="sd-file-view">
                                <i class="fas fa-external-link-alt"></i> Buka
                            </a>
                            <button type="button" data-action="confirm-delete-file" style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.4rem 0.8rem; background: #fee2e2; color: #ef4444; border: none; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                                <i class="fas fa-trash-alt"></i> Hapus
                            </button>
                        </div>
                    </div>
                    @endif

                    {{-- Upload Area untuk Ganti File --}}
                    <div class="u-relative">
                        <label class="u-field-note">
                            @if($document->file_path) Ganti File (Opsional) @else Upload Surat (PDF/Gambar) @endif
                        </label>
                        <div id="dropZone" style="border: 2px dashed {{ $errors->has('file') ? '#ef4444' : '#e2e8f0' }}; border-radius: 0.5rem; padding: 2rem; text-align: center; cursor: pointer; transition: all 0.2s; min-height: 150px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                            <div id="uploadPlaceholder">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: #94a3b8; margin-bottom: 0.5rem;"></i>
                                <p style="font-size: 0.875rem; color: #64748b; margin: 0 0 0.25rem 0; font-weight: 500;">Klik untuk memilih file atau seret file ke sini</p>
                                <p class="u-text-xs-muted-flat2">PDF, JPG, PNG, DOC, DOCX (Maks. 5MB)</p>
                            </div>
                            <div id="filePreview" style="display: none; width: 100%;">
                                <div style="background: #f0fdf4; border: 2px solid #10b981; border-radius: 0.5rem; padding: 1rem;">
                                    <div class="u-flex-center-gap-3">
                                        <div id="fileIcon" style="width: 36px; height: 36px; background: white; border-radius: 0.375rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <i class="fas fa-file-pdf u-text-danger-lg"></i>
                                        </div>
                                        <div class="u-a87">
                                            <p id="fileName" style="font-size: 0.8rem; font-weight: 600; color: #0f172a; margin: 0 0 0.125rem 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">nama_file.pdf</p>
                                            <p id="fileSize" style="font-size: 0.7rem; color: #64748b; margin: 0;">0 KB</p>
                                        </div>
                                        <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.5rem; background: #10b981; color: white; border-radius: 9999px; font-size: 0.7rem; font-weight: 600;">
                                            <i class="fas fa-check" style="margin-right: 0.2rem; font-size: 0.6rem;"></i> Siap
                                        </span>
                                    </div>
                                </div>
                                <button type="button" data-action="change-file" style="margin-top: 0.5rem; padding: 0.4rem 0.8rem; background: white; border: 1px solid #e2e8f0; color: #64748b; border-radius: 0.375rem; font-size: 0.75rem; cursor: pointer;">
                                    <i class="fas fa-sync-alt" style="margin-right: 0.25rem;"></i> Ganti File
                                </button>
                            </div>
                            <input class="u-hidden" type="file" name="file" id="fileInput" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        </div>
                        @error('file')
                            <div style="display: flex; align-items: center; gap: 0.375rem; margin-top: 0.5rem; padding: 0.5rem 0.75rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.375rem;">
                                <i class="fas fa-exclamation-circle u-error-msg"></i>
                                <span class="u-text-danger-soft">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="form-footer" style="margin-top: 2rem; padding-top: 1.5rem; border-top: 2px solid #f1f5f9; display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <a href="{{ route('sidongan.documents.index') }}" class="sd-btn-cancel-edit">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit"  style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

    <script src="{{ asset('assets/sidongan/js/sidongan-documents-edit.js') }}"></script>

@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
