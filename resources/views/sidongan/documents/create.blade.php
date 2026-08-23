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
    <div class="sd-error-alert animate-slide-in">
        <div class="u-a85">
            <div class="sd-error-alert-icon">
                <i class="fas fa-exclamation-triangle" style="color: #dc2626; font-size: 1.125rem;"></i>
            </div>
            <div>
                <h3 class="sd-error-alert-title">Terdapat Kesalahan pada Form</h3>
                <p class="sd-error-alert-desc">Silakan perbaiki field yang ditandai di bawah ini</p>
            </div>
        </div>
        <ul class="sd-error-alert-list">
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
                <div class="sd-form-header">
                    <i class="fas fa-file-signature sd-form-header-icon"></i>
                    <div>
                        <h2 class="sd-form-header-title">Data Surat</h2>
                        <p class="sd-form-header-sub">Informasi surat dari pengirim</p>
                    </div>
                </div>

                <div class="form-content u-p-6">
                    {{-- Data Pengirim Surat --}}
                    <div class="u-mb-6">
                        <h3 class="u-section-title-slate">
                            <i class="fas fa-user sd-section-icon sd-section-icon-blue"></i>
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
                    <div class="sd-agenda-box">
                        <h3 class="u-section-title-slate">
                            <i class="fas fa-clipboard-list sd-section-icon sd-section-icon-purple"></i>
                            Data Agenda (Otomatis)
                        </h3>
                        <div class="form-grid">
                            <div>
                                <label class="u-field-note">Nomor Agenda</label>
                                <input type="text" id="preview_agenda" value="{{ $previewAgenda }}" data-preview-sequence="{{ $previewSequence }}" readonly class="sd-agenda-input">
                                <input type="hidden" name="agenda_number" id="agenda_number_input" value="{{ $previewAgenda }}">
                                <small id="preview_agenda_note" class="sd-agenda-note">
                                    Nomor urut &#10095; Surat Masuk &#10095; PKK Toba &#10095; {{ collect(['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'])->get((int)date('n')-1) }} &#10095; {{ date('Y') }}
                                </small>
                            </div>
                            <div>
                                <label class="u-field-note">Tanggal Diterima <span class="u-text-danger">*</span></label>
                                <input class="u-a24" type="date" name="agenda_date" id="agenda_date" required value="{{ old('agenda_date', $today) }}" 
                                    max="{{ $today }}">
                                <small id="dateError" class="sd-agenda-error"></small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Right Column - Saran & Upload --}}
        <div class="sd-right-card">
            <div class="sd-right-card-body">
                {{-- Saran Sekretaris --}}
                <div class="u-mb-6">
                    <h3 class="u-section-title-slate">
                        <i class="fas fa-comment-alt sd-section-icon sd-section-icon-violet"></i>
                        Saran Sekretaris <span class="u-text-danger">*</span>
                    </h3>
                    <textarea name="suggestion" id="suggestion" form="mainForm" rows="4" placeholder="Masukkan saran atau catatan untuk Ketua PKK..." required class="sd-suggestion-textarea">{{ old('suggestion') }}</textarea>
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
                        <i class="fas fa-upload sd-section-icon sd-section-icon-green"></i>
                        Upload Surat <span class="u-text-danger">*</span>
                    </h3>
                    <div id="dropZone" class="sd-dropzone">
                        
                        {{-- Default State --}}
                        <div id="uploadPlaceholder">
                            <i class="fas fa-cloud-upload-alt sd-dropzone-icon"></i>
                            <p class="sd-dropzone-text">Klik atau seret file ke sini</p>
                            <p class="u-text-xs-muted-flat2">PDF, JPG, PNG (Maks. 5MB)</p>
                        </div>

                        {{-- File Preview State --}}
                        <div id="filePreview" class="sd-file-preview">
                            <div class="sd-file-preview-card">
                                <div class="u-a85">
                                    <div id="fileIcon" class="sd-file-icon-box">
                                        <i class="fas fa-file-pdf u-text-danger-lg"></i>
                                    </div>
                                    <div class="u-a87">
                                        <p id="fileName" class="sd-file-name">nama_file.pdf</p>
                                        <p id="fileSize" class="sd-file-size">0 KB</p>
                                    </div>
                                    <div class="u-shrink-0">
                                        <span class="sd-file-badge">
                                            <i class="fas fa-check" style="font-size: 0.65rem;"></i>
                                            Siap
                                        </span>
                                    </div>
                                </div>
                                <div class="sd-file-progress-bg">
                                    <div class="sd-file-progress-bar"></div>
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
            <div class="sd-form-footer">
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
