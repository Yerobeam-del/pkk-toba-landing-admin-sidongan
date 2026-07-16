@extends('sidongan.layouts.app')
@section('title', 'Edit Dokumen - SIDONGAN')

@section('content')
@php
    $currentUser = auth()->guard('sidongan')->user();
    $today = date('Y-m-d');
@endphp

<style>
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-slide-in {
        animation: slideIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        opacity: 0;
    }
    
    @media (max-width: 968px) {
        .form-grid {
            grid-template-columns: 1fr !important;
        }
        
        .form-footer {
            flex-direction: column !important;
        }
        
        .form-footer button,
        .form-footer a {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div style="padding: 0 1.5rem;">
    {{-- Header --}}
    <div style="background: linear-gradient(135deg, #0891b2, #14b8a6); padding: 1.5rem 2rem; border-radius: 1rem; margin-bottom: 1.5rem; color: white; box-shadow: 0 4px 20px rgba(8, 145, 178, 0.2);" class="animate-slide-in">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 3rem; height: 3rem; background: rgba(255, 255, 255, 0.25); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <i class="fas fa-edit" style="font-size: 1.5rem; color: white;"></i>
                </div>
                <div>
                    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0 0 0.25rem 0;">Edit Dokumen</h1>
                    <p style="font-size: 0.875rem; opacity: 0.95; margin: 0;">Update informasi dokumen</p>
                </div>
            </div>
            <a href="{{ route('sidongan.documents.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.25rem; background: rgba(255,255,255,0.25); color: white; text-decoration: none; border-radius: 0.5rem; font-weight: 600; transition: all 0.25s ease; backdrop-filter: blur(4px); border: 1px solid rgba(255, 255, 255, 0.3);" onmouseover="this.style.background='rgba(255,255,255,0.35)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.background='rgba(255,255,255,0.25)'; this.style.transform='translateY(0)'">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    {{-- Error Alert Box --}}
    @if($errors->any())
    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem; padding: 1rem 1.25rem; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.1);" class="animate-slide-in">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
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
                <li style="margin-bottom: 0.25rem;">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('sidongan.documents.update', $document) }}" method="POST" enctype="multipart/form-data" id="editForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="delete_file" id="deleteFileInput" value="0">

        <div style="background: white; border-radius: 0.75rem; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; overflow: hidden;" class="animate-slide-in">
            {{-- Form Header --}}
            <div style="padding: 1.25rem 1.5rem; background: linear-gradient(135deg, #f0fdf4, #dcfce7); border-bottom: 2px solid #86efac;">
                <h2 style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-file-alt" style="color: #059669;"></i>
                    Informasi Dokumen
                </h2>
            </div>

            <div style="padding: 1.5rem;">
                <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    {{-- Kolom Kiri: Data Surat --}}
                    <div style="display: grid; gap: 1.25rem;">
                        <h3 style="font-size: 0.9rem; font-weight: 600; color: #334155; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-envelope" style="color: #3b82f6; font-size: 0.875rem;"></i>
                            Data Surat
                        </h3>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 500; color: #475569; margin-bottom: 0.375rem;">Pengirim <span style="color: #ef4444;">*</span></label>
                            <input type="text" name="sender" value="{{ old('sender', $document->sender) }}" required 
                                style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid {{ $errors->has('sender') ? '#ef4444' : '#e2e8f0' }}; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s;" 
                                onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1)'" 
                                onblur="this.style.borderColor='{{ $errors->has('sender') ? '#ef4444' : '#e2e8f0' }}'; this.style.boxShadow='none'">
                            @error('sender')
                                <div style="display: flex; align-items: center; gap: 0.375rem; margin-top: 0.375rem; padding: 0.5rem 0.75rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.375rem;">
                                    <i class="fas fa-exclamation-circle" style="color: #dc2626; font-size: 0.875rem;"></i>
                                    <span style="font-size: 0.8rem; color: #991b1b; font-weight: 500;">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 500; color: #475569; margin-bottom: 0.375rem;">Nomor Surat <span style="color: #ef4444;">*</span></label>
                            <input type="text" name="document_number" value="{{ old('document_number', $document->document_number) }}" required 
                                style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid {{ $errors->has('document_number') ? '#ef4444' : '#e2e8f0' }}; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s;" 
                                onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1)'" 
                                onblur="this.style.borderColor='{{ $errors->has('document_number') ? '#ef4444' : '#e2e8f0' }}'; this.style.boxShadow='none'">
                            @error('document_number')
                                <div style="display: flex; align-items: center; gap: 0.375rem; margin-top: 0.375rem; padding: 0.5rem 0.75rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.375rem;">
                                    <i class="fas fa-exclamation-circle" style="color: #dc2626; font-size: 0.875rem;"></i>
                                    <span style="font-size: 0.8rem; color: #991b1b; font-weight: 500;">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 500; color: #475569; margin-bottom: 0.375rem;">Tanggal Surat <span style="color: #ef4444;">*</span></label>
                            <input type="date" name="document_date" id="document_date" value="{{ old('document_date', $document->document_date?->format('Y-m-d')) }}" required onchange="validateDates()"
                                style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid {{ $errors->has('document_date') ? '#ef4444' : '#e2e8f0' }}; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s;" 
                                onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1)'" 
                                onblur="this.style.borderColor='{{ $errors->has('document_date') ? '#ef4444' : '#e2e8f0' }}'; this.style.boxShadow='none'">
                            @error('document_date')
                                <div style="display: flex; align-items: center; gap: 0.375rem; margin-top: 0.375rem; padding: 0.5rem 0.75rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.375rem;">
                                    <i class="fas fa-exclamation-circle" style="color: #dc2626; font-size: 0.875rem;"></i>
                                    <span style="font-size: 0.8rem; color: #991b1b; font-weight: 500;">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 500; color: #475569; margin-bottom: 0.375rem;">Perihal <span style="color: #ef4444;">*</span></label>
                            <input type="text" name="subject" value="{{ old('subject', $document->subject) }}" required 
                                style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid {{ $errors->has('subject') ? '#ef4444' : '#e2e8f0' }}; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s;" 
                                onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1)'" 
                                onblur="this.style.borderColor='{{ $errors->has('subject') ? '#ef4444' : '#e2e8f0' }}'; this.style.boxShadow='none'">
                            @error('subject')
                                <div style="display: flex; align-items: center; gap: 0.375rem; margin-top: 0.375rem; padding: 0.5rem 0.75rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.375rem;">
                                    <i class="fas fa-exclamation-circle" style="color: #dc2626; font-size: 0.875rem;"></i>
                                    <span style="font-size: 0.8rem; color: #991b1b; font-weight: 500;">{{ $message }}</span>
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
                            <label style="display: block; font-size: 0.8rem; font-weight: 500; color: #475569; margin-bottom: 0.375rem;">Nomor Agenda</label>
                            <input type="text" value="{{ $document->agenda_number ?? 'Belum ada' }}" readonly 
                                style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; background: #f8fafc; color: #64748b; cursor: not-allowed; font-family: monospace;">
                            <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">Nomor agenda tidak dapat diubah</p>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 500; color: #475569; margin-bottom: 0.375rem;">Saran Sekretaris</label>
                            <textarea name="suggestion" rows="4" 
                                style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid {{ $errors->has('suggestion') ? '#ef4444' : '#e2e8f0' }}; border-radius: 0.5rem; font-size: 0.875rem; resize: vertical; transition: all 0.2s;" 
                                onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1)'" 
                                onblur="this.style.borderColor='{{ $errors->has('suggestion') ? '#ef4444' : '#e2e8f0' }}'; this.style.boxShadow='none'">{{ old('suggestion', $document->suggestion) }}</textarea>
                            @error('suggestion')
                                <div style="display: flex; align-items: center; gap: 0.375rem; margin-top: 0.375rem; padding: 0.5rem 0.75rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.375rem;">
                                    <i class="fas fa-exclamation-circle" style="color: #dc2626; font-size: 0.875rem;"></i>
                                    <span style="font-size: 0.8rem; color: #991b1b; font-weight: 500;">{{ $message }}</span>
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
                            <i class="fas fa-file-pdf" style="color: #ef4444; font-size: 1.25rem;"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <p style="font-size: 0.875rem; font-weight: 600; color: #0f172a; margin: 0 0 0.125rem 0;">{{ $document->file_name }}</p>
                            <p style="font-size: 0.75rem; color: #64748b; margin: 0;">{{ $document->file_size ? round($document->file_size / 1024, 2) . ' KB' : 'File saat ini' }}</p>
                        </div>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.4rem 0.8rem; background: #dbeafe; color: #2563eb; text-decoration: none; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600;" onmouseover="this.style.background='#bfdbfe'" onmouseout="this.style.background='#dbeafe'">
                                <i class="fas fa-external-link-alt"></i> Buka
                            </a>
                            <button type="button" onclick="confirmDeleteFile(event)" style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.4rem 0.8rem; background: #fee2e2; color: #ef4444; border: none; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;" onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'">
                                <i class="fas fa-trash-alt"></i> Hapus
                            </button>
                        </div>
                    </div>
                    @endif

                    {{-- Upload Area untuk Ganti File --}}
                    <div style="position: relative;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 500; color: #475569; margin-bottom: 0.375rem;">
                            @if($document->file_path) Ganti File (Opsional) @else Upload Surat (PDF/Gambar) @endif
                        </label>
                        <div id="dropZone" style="border: 2px dashed {{ $errors->has('file') ? '#ef4444' : '#e2e8f0' }}; border-radius: 0.5rem; padding: 2rem; text-align: center; cursor: pointer; transition: all 0.2s; min-height: 150px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                            <div id="uploadPlaceholder">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: #94a3b8; margin-bottom: 0.5rem;"></i>
                                <p style="font-size: 0.875rem; color: #64748b; margin: 0 0 0.25rem 0; font-weight: 500;">Klik untuk memilih file atau seret file ke sini</p>
                                <p style="font-size: 0.75rem; color: #94a3b8; margin: 0;">PDF, JPG, PNG, DOC, DOCX (Maks. 5MB)</p>
                            </div>
                            <div id="filePreview" style="display: none; width: 100%;">
                                <div style="background: #f0fdf4; border: 2px solid #10b981; border-radius: 0.5rem; padding: 1rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div id="fileIcon" style="width: 36px; height: 36px; background: white; border-radius: 0.375rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <i class="fas fa-file-pdf" style="color: #ef4444; font-size: 1.25rem;"></i>
                                        </div>
                                        <div style="flex: 1; text-align: left; overflow: hidden;">
                                            <p id="fileName" style="font-size: 0.8rem; font-weight: 600; color: #0f172a; margin: 0 0 0.125rem 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">nama_file.pdf</p>
                                            <p id="fileSize" style="font-size: 0.7rem; color: #64748b; margin: 0;">0 KB</p>
                                        </div>
                                        <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.5rem; background: #10b981; color: white; border-radius: 9999px; font-size: 0.7rem; font-weight: 600;">
                                            <i class="fas fa-check" style="margin-right: 0.2rem; font-size: 0.6rem;"></i> Siap
                                        </span>
                                    </div>
                                </div>
                                <button type="button" onclick="changeFile()" style="margin-top: 0.5rem; padding: 0.4rem 0.8rem; background: white; border: 1px solid #e2e8f0; color: #64748b; border-radius: 0.375rem; font-size: 0.75rem; cursor: pointer;">
                                    <i class="fas fa-sync-alt" style="margin-right: 0.25rem;"></i> Ganti File
                                </button>
                            </div>
                            <input type="file" name="file" id="fileInput" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="display: none;">
                        </div>
                        @error('file')
                            <div style="display: flex; align-items: center; gap: 0.375rem; margin-top: 0.5rem; padding: 0.5rem 0.75rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.375rem;">
                                <i class="fas fa-exclamation-circle" style="color: #dc2626; font-size: 0.875rem;"></i>
                                <span style="font-size: 0.8rem; color: #991b1b; font-weight: 500;">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="form-footer" style="margin-top: 2rem; padding-top: 1.5rem; border-top: 2px solid #f1f5f9; display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <a href="{{ route('sidongan.documents.index') }}" style="padding: 0.75rem 1.5rem; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" onclick="return validateForm()" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(59,130,246,0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // ==========================================
    // VALIDATE DATES
    // ==========================================
    function validateDates() {
        const documentDate = document.getElementById('document_date').value;
        const dateError = document.getElementById('dateError');
        const dateInput = document.getElementById('document_date');
        
        if (documentDate) {
            const docDate = new Date(documentDate);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            // Cek: Tanggal surat tidak boleh lebih dari hari ini
            if (docDate > today) {
                if (typeof Toast !== 'undefined') {
                    Toast.error('Tanggal surat tidak boleh lebih dari hari ini!');
                } else {
                    alert('Tanggal surat tidak boleh lebih dari hari ini!');
                }
                dateInput.style.borderColor = '#ef4444';
                return false;
            }
            
            dateInput.style.borderColor = '#e2e8f0';
            return true;
        }
        return true;
    }

    // ==========================================
    // VALIDATE FORM
    // ==========================================
    function validateForm() {
        // Validasi tanggal surat tidak boleh lebih dari hari ini
        const documentDate = document.getElementById('document_date').value;
        if (documentDate) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const docDate = new Date(documentDate);
            
            if (docDate > today) {
                if (typeof Toast !== 'undefined') {
                    Toast.error('Tanggal surat tidak boleh lebih dari hari ini!');
                } else {
                    alert('Tanggal surat tidak boleh lebih dari hari ini!');
                }
                document.getElementById('document_date').focus();
                document.getElementById('document_date').style.borderColor = '#ef4444';
                return false;
            }
        }
        
        return true;
    }

    // ==========================================
    // FILE VALIDATION FUNCTION
    // ==========================================
    function validateFile(file) {
        const allowedTypes = [
            'application/pdf',
            'image/jpeg',
            'image/jpg',
            'image/png',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        const allowedExtensions = ['.pdf', '.jpg', '.jpeg', '.png', '.doc', '.docx'];
        const maxFileSize = 5 * 1024 * 1024; // 5MB
        
        // Cek ukuran file
        if (file.size > maxFileSize) {
            const sizeInMB = (file.size / 1024 / 1024).toFixed(2);
            return {
                valid: false,
                message: `Ukuran file terlalu besar (${sizeInMB}MB). Maksimal 5MB.`
            };
        }
        
        // Cek tipe file (MIME type)
        if (!allowedTypes.includes(file.type)) {
            return {
                valid: false,
                message: `Format file "${file.name}" tidak diizinkan. Hanya PDF, gambar (JPG, PNG), dan dokumen Word yang diperbolehkan.`
            };
        }
        
        // Cek ekstensi file (double check)
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
        if (!allowedExtensions.includes(fileExtension)) {
            return {
                valid: false,
                message: `Ekstensi file "${fileExtension}" tidak diizinkan. Hanya .pdf, .jpg, .jpeg, .png, .doc, .docx yang diperbolehkan.`
            };
        }
        
        return { valid: true, message: '' };
    }

    // ==========================================
    // DRAG & DROP & PREVIEW LOGIC
    // ==========================================
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const fileIcon = document.getElementById('fileIcon');

    dropZone.addEventListener('click', () => fileInput.click());
    
    dropZone.addEventListener('dragover', (e) => { 
        e.preventDefault(); 
        dropZone.style.borderColor = '#3b82f6'; 
        dropZone.style.background = '#eff6ff'; 
    });
    
    dropZone.addEventListener('dragleave', () => { 
        dropZone.style.borderColor = '#e2e8f0'; 
        dropZone.style.background = 'white'; 
    });
    
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault(); 
        dropZone.style.borderColor = '#e2e8f0'; 
        dropZone.style.background = 'white';
        
        if(e.dataTransfer.files.length > 0) {
            const file = e.dataTransfer.files[0];
            const validation = validateFile(file);
            
            if (!validation.valid) {
                if (typeof Toast !== 'undefined') {
                    Toast.error(validation.message);
                } else {
                    alert(validation.message);
                }
                fileInput.value = '';
                return;
            }
            
            fileInput.files = e.dataTransfer.files;
            showFilePreview(file);
        }
    });
    
    fileInput.addEventListener('change', (e) => { 
        if (e.target.files.length > 0) {
            const file = e.target.files[0];
            const validation = validateFile(file);
            
            if (!validation.valid) {
                if (typeof Toast !== 'undefined') {
                    Toast.error(validation.message);
                } else {
                    alert(validation.message);
                }
                e.target.value = '';
                return;
            }
            
            showFilePreview(file);
        }
    });

    function showFilePreview(file) {
        uploadPlaceholder.style.display = 'none'; 
        filePreview.style.display = 'block';
        fileName.textContent = file.name;
        
        const sizeInKB = (file.size / 1024).toFixed(2);
        fileSize.textContent = sizeInKB >= 1024 ? `${(file.size / 1024 / 1024).toFixed(2)} MB` : `${sizeInKB} KB`;
        
        const iconElement = fileIcon.querySelector('i');
        if (file.type === 'application/pdf') { 
            iconElement.className = 'fas fa-file-pdf'; 
            iconElement.style.color = '#ef4444'; 
        } else if (file.type.startsWith('image/')) { 
            iconElement.className = 'fas fa-file-image'; 
            iconElement.style.color = '#10b981'; 
        } else if (file.type.includes('word') || file.name.endsWith('.doc') || file.name.endsWith('.docx')) { 
            iconElement.className = 'fas fa-file-word'; 
            iconElement.style.color = '#3b82f6'; 
        } else { 
            iconElement.className = 'fas fa-file'; 
            iconElement.style.color = '#64748b'; 
        }
        
        dropZone.style.borderColor = '#10b981'; 
        dropZone.style.background = '#f0fdf4';
    }

    function changeFile() {
        uploadPlaceholder.style.display = 'block'; 
        filePreview.style.display = 'none'; 
        fileInput.value = '';
        dropZone.style.borderColor = '#e2e8f0'; 
        dropZone.style.background = 'white';
        setTimeout(() => fileInput.click(), 100);
    }

    // ==========================================
    // HAPUS FILE LOGIC
    // ==========================================
    function confirmDeleteFile(event) {
        event.stopPropagation();
        if (confirm('Apakah Anda yakin ingin menghapus lampiran file ini?')) {
            document.getElementById('deleteFileInput').value = '1';
            event.target.closest('form').submit();
        }
    }
</script>
@endsection