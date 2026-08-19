{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('sidongan.layouts.app')
@section('title', 'Buat Surat Masuk Baru - SIDONGAN')

@section('content')
@php
    // Preview nomor agenda berikutnya — hitung dari agenda_date (default hari ini)
    $today = date('Y-m-d');
    $previewDate = old('agenda_date', $today);
    $previewAgenda = \App\Models\Document::generateAgendaNumber($previewDate);
    // Simpan nomor urut untuk JavaScript
    $previewSequence = explode('/', $previewAgenda)[0];
@endphp

    <link rel="stylesheet" href="{{ asset('assets/sidongan/css/sidongan-documents-create.css') }}">


<div class="form-container u-px-6">
    {{-- Page Header --}}
    <div class="animate-slide-in u-mb-6">
        <h1 class="u-h2-slate">Buat Surat Masuk Baru</h1>
        <p class="u-text-muted-lead">Isi formulir berikut untuk membuat surat masuk baru</p>
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

    {{-- Two Column Form Layout --}}
    <div class="form-columns animate-slide-in">
        {{-- Left Column - Data Surat --}}
        <div class="u-a78">
            <form action="{{ route('sidongan.documents.store') }}" method="POST" enctype="multipart/form-data" id="mainForm">
                @csrf
                
                {{-- Form Header --}}
                <div class="form-header" style="padding: 1.25rem 1.5rem; background: #f0fdf4; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-file-signature" style="color: #059669; font-size: 1.25rem;"></i>
                    <div>
                        <h2 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0;">Data Surat</h2>
                        <p style="font-size: 0.8rem; color: #64748b; margin: 0;">Informasi surat dari pengirim</p>
                    </div>
                </div>

                <div class="form-content u-p-6">
                    {{-- Data Pengirim Surat --}}
                    <div class="u-mb-6">
                        <h3 class="u-section-title-slate">
                            <i class="fas fa-user" style="color: #3b82f6; font-size: 0.875rem;"></i>
                            Data Pengirim Surat
                        </h3>
                        <div class="form-grid">
                            <div>
                                <label class="u-field-note">Pengirim Surat <span class="u-text-danger">*</span></label>
                                <input class="u-a24" type="text" name="sender" id="sender" placeholder="Contoh: Bupati Toba" required value="{{ old('sender') }}">
                                @error('sender')
                                    <div class="u-inline-alert-danger">
                                        <i class="fas fa-exclamation-circle u-error-msg"></i>
                                        <span class="u-text-danger-soft">{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                            <div>
                                <label class="u-field-note">Tanggal Surat <span class="u-text-danger">*</span></label>
                                <input class="u-a24" type="date" name="document_date" id="document_date" required value="{{ old('document_date') }}">
                                @error('document_date')
                                    <div class="u-inline-alert-danger">
                                        <i class="fas fa-exclamation-circle u-error-msg"></i>
                                        <span class="u-text-danger-soft">{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                            <div>
                                <label class="u-field-note">Nomor Surat <span class="u-text-danger">*</span></label>
                                <input class="u-a24" type="text" name="document_number" placeholder="Contoh: 123/SK/2024" required value="{{ old('document_number') }}">
                                @error('document_number')
                                    <div class="u-inline-alert-danger">
                                        <i class="fas fa-exclamation-circle u-error-msg"></i>
                                        <span class="u-text-danger-soft">{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                            <div>
                                <label class="u-field-note">Perihal <span class="u-text-danger">*</span></label>
                                <input class="u-a24" type="text" name="subject" placeholder="Perihal surat" required value="{{ old('subject') }}">
                                @error('subject')
                                    <div class="u-inline-alert-danger">
                                        <i class="fas fa-exclamation-circle u-error-msg"></i>
                                        <span class="u-text-danger-soft">{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Data Agenda (Otomatis) --}}
                    <div style="background: #eff6ff; border-radius: 0.5rem; padding: 1rem;">
                        <h3 class="u-section-title-slate">
                            <i class="fas fa-clipboard-list" style="color: #2563eb; font-size: 0.875rem;"></i>
                            Data Agenda (Otomatis)
                        </h3>
                        <div class="form-grid">
                            <div>
                                <label class="u-field-note">Nomor Agenda</label>
                                <input type="text" id="preview_agenda" value="{{ $previewAgenda }}" data-preview-sequence="{{ $previewSequence }}" readonly 
                                    style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; background: #f8fafc; color: #1e293b; cursor: not-allowed; font-family: monospace; font-weight: 600; letter-spacing: 0.5px;">
                                <input type="hidden" name="agenda_number" id="agenda_number_input" value="{{ $previewAgenda }}">
                                <small id="preview_agenda_note" style="color: #94a3b8; display: block; margin-top: 0.25rem; font-size: 0.7rem;">
                                    Nomor urut &#10095; Surat Masuk &#10095; PKK Toba &#10095; {{ collect(['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'])->get((int)date('n')-1) }} &#10095; {{ date('Y') }}
                                </small>
                            </div>
                            <div>
                                <label class="u-field-note">Tanggal Diterima <span class="u-text-danger">*</span></label>
                                <input class="u-a24" type="date" name="agenda_date" id="agenda_date" required value="{{ old('agenda_date', $today) }}" 
                                    max="{{ $today }}">
                                <small id="dateError" style="color: #ef4444; display: none; margin-top: 0.25rem; font-size: 0.75rem;"></small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Right Column - Saran & Upload --}}
        <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; overflow: hidden; display: flex; flex-direction: column;">
            <div style="padding: 1.5rem; flex: 1;">
                {{-- Saran Sekretaris --}}
                <div class="u-mb-6">
                    <h3 class="u-section-title-slate">
                        <i class="fas fa-comment-alt" style="color: #8b5cf6; font-size: 0.875rem;"></i>
                        Saran Sekretaris <span class="u-text-danger">*</span>
                    </h3>
                    <textarea name="suggestion" id="suggestion" form="mainForm" rows="4" placeholder="Masukkan saran atau catatan untuk Ketua PKK..." required 
                        style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s; resize: vertical;">{{ old('suggestion') }}</textarea>
                    @error('suggestion')
                        <div class="u-inline-alert-danger">
                            <i class="fas fa-exclamation-circle u-error-msg"></i>
                            <span class="u-text-danger-soft">{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                {{-- Upload Surat --}}
                <div class="u-mb-6">
                    <h3 class="u-section-title-slate">
                        <i class="fas fa-upload" style="color: #10b981; font-size: 0.875rem;"></i>
                        Upload Surat <span class="u-text-danger">*</span>
                    </h3>
                    <div id="dropZone" style="border: 2px dashed #e2e8f0; border-radius: 0.5rem; padding: 2rem; text-align: center; cursor: pointer; transition: all 0.2s; min-height: 200px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                        
                        {{-- Default State --}}
                        <div id="uploadPlaceholder">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: #94a3b8; margin-bottom: 1rem;"></i>
                            <p style="font-size: 0.9rem; color: #475569; margin: 0 0 0.5rem 0; font-weight: 600;">Klik atau seret file ke sini</p>
                            <p class="u-text-xs-muted-flat2">PDF, JPG, PNG (Maks. 5MB)</p>
                        </div>

                        {{-- File Preview State --}}
                        <div id="filePreview" style="display: none; width: 100%;">
                            <div style="background: #f0fdf4; border: 2px solid #10b981; border-radius: 0.5rem; padding: 1rem;">
                                <div class="u-a85">
                                    <div id="fileIcon" style="width: 40px; height: 40px; background: white; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="fas fa-file-pdf u-text-danger-lg"></i>
                                    </div>
                                    <div class="u-a87">
                                        <p id="fileName" style="font-size: 0.85rem; font-weight: 600; color: #0f172a; margin: 0 0 0.25rem 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">nama_file.pdf</p>
                                        <p id="fileSize" style="font-size: 0.7rem; color: #64748b; margin: 0;">0 KB</p>
                                    </div>
                                    <div class="u-shrink-0">
                                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.65rem; background: #10b981; color: white; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
                                            <i class="fas fa-check" style="font-size: 0.65rem;"></i>
                                            Siap
                                        </span>
                                    </div>
                                </div>
                                <div style="background: white; border-radius: 0.25rem; padding: 0.2rem;">
                                    <div style="height: 3px; background: #10b981; border-radius: 0.25rem; width: 100%;"></div>
                                </div>
                            </div>
                            <button type="button" class="btn-change-file" data-action="change-file">
                                <i class="fas fa-sync-alt u-mr-1"></i>
                                Ganti File
                            </button>
                        </div>

                        <input class="u-hidden" type="file" name="file" id="fileInput" form="mainForm" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                    @error('file')
                        <div style="display: flex; align-items: center; gap: 0.375rem; margin-top: 0.5rem; padding: 0.5rem 0.75rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.375rem;">
                            <i class="fas fa-exclamation-circle u-error-msg"></i>
                            <span class="u-text-danger-soft">{{ $message }}</span>
                        </div>
                    @enderror
                </div>
            </div>

            {{-- Footer Actions --}}
            <div class="form-footer" style="padding: 1rem 1.5rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 0.75rem;">
                <a href="{{ route('sidongan.documents.index') }}" class="btn-cancel">
                    Batal
                </a>
                <button type="submit" form="mainForm" class="btn-submit-primary">
                    <i class="fas fa-paper-plane"></i>
                    Simpan & Kirim
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('assets/sidongan/js/documents-create.js') }}"></script>
@endpush
@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
