@extends('sidongan.layouts.app')
@section('title', 'Buat Surat Masuk Baru - SIDONGAN')

@section('content')
@php
    // Preview nomor agenda berikutnya
    $previewAgenda = \App\Models\Document::generateAgendaNumber();
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
    
    /* Two Column Layout */
    .form-columns {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
    /* Mobile Responsive */
    @media (max-width: 968px) {
        .form-columns {
            grid-template-columns: 1fr;
        }
        
        .form-grid {
            grid-template-columns: 1fr;
        }
        
        .form-container {
            padding: 0 1rem !important;
        }
        
        .form-content {
            padding: 1rem !important;
        }
        
        .form-header {
            padding: 1rem !important;
        }
        
        .form-footer {
            flex-direction: column !important;
        }
        
        .form-footer button,
        .form-footer a {
            width: 100%;
            justify-content: center;
        }
        
        #dropZone {
            padding: 1.5rem !important;
            min-height: 150px !important;
        }
    }
    
    @media (max-width: 480px) {
        .form-container {
            padding: 0 0.75rem !important;
        }
        
        .form-content {
            padding: 0.75rem !important;
        }
        
        h1 {
            font-size: 1.25rem !important;
        }
        
        h2 {
            font-size: 0.9rem !important;
        }
        
        h3 {
            font-size: 0.85rem !important;
        }
    }
</style>

<div class="form-container" style="padding: 0 1.5rem;">
    {{-- Page Header --}}
    <div style="margin-bottom: 1.5rem;" class="animate-slide-in">
        <h1 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin: 0 0 0.5rem 0; letter-spacing: -0.025em;">Buat Surat Masuk Baru</h1>
        <p style="font-size: 0.95rem; color: #64748b; margin: 0; line-height: 1.6;">Isi formulir berikut untuk membuat surat masuk baru</p>
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

    {{-- Two Column Form Layout --}}
    <div class="form-columns animate-slide-in">
        {{-- Left Column - Data Surat --}}
        <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; overflow: hidden;">
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

                <div class="form-content" style="padding: 1.5rem;">
                    {{-- Data Pengirim Surat --}}
                    <div style="margin-bottom: 1.5rem;">
                        <h3 style="font-size: 0.9rem; font-weight: 600; color: #334155; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-user" style="color: #3b82f6; font-size: 0.875rem;"></i>
                            Data Pengirim Surat
                        </h3>
                        <div class="form-grid">
                            <div>
                                <label style="display: block; font-size: 0.8rem; font-weight: 500; color: #475569; margin-bottom: 0.375rem;">Pengirim Surat <span style="color: #ef4444;">*</span></label>
                                <input type="text" name="sender" id="sender" placeholder="Contoh: Bupati Toba" required value="{{ old('sender') }}"
                                    style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s;" 
                                    onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1)'" 
                                    onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                @error('sender')
                                    <div style="display: flex; align-items: center; gap: 0.375rem; margin-top: 0.375rem; padding: 0.5rem 0.75rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.375rem;">
                                        <i class="fas fa-exclamation-circle" style="color: #dc2626; font-size: 0.875rem;"></i>
                                        <span style="font-size: 0.8rem; color: #991b1b; font-weight: 500;">{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8rem; font-weight: 500; color: #475569; margin-bottom: 0.375rem;">Tanggal Surat <span style="color: #ef4444;">*</span></label>
                                <input type="date" name="document_date" id="document_date" required value="{{ old('document_date') }}" onchange="validateDates()"
                                    style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s;" 
                                    onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1)'" 
                                    onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                @error('document_date')
                                    <div style="display: flex; align-items: center; gap: 0.375rem; margin-top: 0.375rem; padding: 0.5rem 0.75rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.375rem;">
                                        <i class="fas fa-exclamation-circle" style="color: #dc2626; font-size: 0.875rem;"></i>
                                        <span style="font-size: 0.8rem; color: #991b1b; font-weight: 500;">{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8rem; font-weight: 500; color: #475569; margin-bottom: 0.375rem;">Nomor Surat <span style="color: #ef4444;">*</span></label>
                                <input type="text" name="document_number" placeholder="Contoh: 123/SK/2024" required value="{{ old('document_number') }}"
                                    style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s;" 
                                    onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1)'" 
                                    onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                @error('document_number')
                                    <div style="display: flex; align-items: center; gap: 0.375rem; margin-top: 0.375rem; padding: 0.5rem 0.75rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.375rem;">
                                        <i class="fas fa-exclamation-circle" style="color: #dc2626; font-size: 0.875rem;"></i>
                                        <span style="font-size: 0.8rem; color: #991b1b; font-weight: 500;">{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8rem; font-weight: 500; color: #475569; margin-bottom: 0.375rem;">Perihal <span style="color: #ef4444;">*</span></label>
                                <input type="text" name="subject" placeholder="Perihal surat" required value="{{ old('subject') }}"
                                    style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s;" 
                                    onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1)'" 
                                    onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                @error('subject')
                                    <div style="display: flex; align-items: center; gap: 0.375rem; margin-top: 0.375rem; padding: 0.5rem 0.75rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.375rem;">
                                        <i class="fas fa-exclamation-circle" style="color: #dc2626; font-size: 0.875rem;"></i>
                                        <span style="font-size: 0.8rem; color: #991b1b; font-weight: 500;">{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Data Agenda (Otomatis) --}}
                    <div style="background: #eff6ff; border-radius: 0.5rem; padding: 1rem;">
                        <h3 style="font-size: 0.9rem; font-weight: 600; color: #334155; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-clipboard-list" style="color: #2563eb; font-size: 0.875rem;"></i>
                            Data Agenda (Otomatis)
                        </h3>
                        <div class="form-grid">
                            <div>
                                <label style="display: block; font-size: 0.8rem; font-weight: 500; color: #475569; margin-bottom: 0.375rem;">Nomor Agenda</label>
                                <input type="text" value="{{ $previewAgenda }}" readonly 
                                    style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; background: #f8fafc; color: #64748b; cursor: not-allowed; font-family: monospace;">
                                <small style="color: #94a3b8; display: block; margin-top: 0.25rem; font-size: 0.7rem;">Format: No. Urut/SM/PKK-T/Bulan/Tahun</small>
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8rem; font-weight: 500; color: #475569; margin-bottom: 0.375rem;">Tanggal Diterima <span style="color: #ef4444;">*</span></label>
                                <input type="date" name="agenda_date" id="agenda_date" required value="{{ old('agenda_date', $today) }}" 
                                    max="{{ $today }}"
                                    style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s;" 
                                    onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1)'" 
                                    onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
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
                <div style="margin-bottom: 1.5rem;">
                    <h3 style="font-size: 0.9rem; font-weight: 600; color: #334155; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-comment-alt" style="color: #8b5cf6; font-size: 0.875rem;"></i>
                        Saran Sekretaris <span style="color: #ef4444;">*</span>
                    </h3>
                    <textarea name="suggestion" id="suggestion" form="mainForm" rows="4" placeholder="Masukkan saran atau catatan untuk Ketua PKK..." required 
                        style="width: 100%; padding: 0.625rem 0.875rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s; resize: vertical;" 
                        onfocus="this.style.borderColor='#8b5cf6'; this.style.boxShadow='0 0 0 3px rgba(139,92,246,0.1)'" 
                        onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">{{ old('suggestion') }}</textarea>
                    @error('suggestion')
                        <div style="display: flex; align-items: center; gap: 0.375rem; margin-top: 0.375rem; padding: 0.5rem 0.75rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.375rem;">
                            <i class="fas fa-exclamation-circle" style="color: #dc2626; font-size: 0.875rem;"></i>
                            <span style="font-size: 0.8rem; color: #991b1b; font-weight: 500;">{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                {{-- Upload Surat --}}
                <div style="margin-bottom: 1.5rem;">
                    <h3 style="font-size: 0.9rem; font-weight: 600; color: #334155; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-upload" style="color: #10b981; font-size: 0.875rem;"></i>
                        Upload Surat <span style="color: #ef4444;">*</span>
                    </h3>
                    <div id="dropZone" style="border: 2px dashed #e2e8f0; border-radius: 0.5rem; padding: 2rem; text-align: center; cursor: pointer; transition: all 0.2s; min-height: 200px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                        
                        {{-- Default State --}}
                        <div id="uploadPlaceholder">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: #94a3b8; margin-bottom: 1rem;"></i>
                            <p style="font-size: 0.9rem; color: #475569; margin: 0 0 0.5rem 0; font-weight: 600;">Klik atau seret file ke sini</p>
                            <p style="font-size: 0.75rem; color: #94a3b8; margin: 0;">PDF, JPG, PNG (Maks. 5MB)</p>
                        </div>

                        {{-- File Preview State --}}
                        <div id="filePreview" style="display: none; width: 100%;">
                            <div style="background: #f0fdf4; border: 2px solid #10b981; border-radius: 0.5rem; padding: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                                    <div id="fileIcon" style="width: 40px; height: 40px; background: white; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="fas fa-file-pdf" style="color: #ef4444; font-size: 1.25rem;"></i>
                                    </div>
                                    <div style="flex: 1; text-align: left; overflow: hidden;">
                                        <p id="fileName" style="font-size: 0.85rem; font-weight: 600; color: #0f172a; margin: 0 0 0.25rem 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">nama_file.pdf</p>
                                        <p id="fileSize" style="font-size: 0.7rem; color: #64748b; margin: 0;">0 KB</p>
                                    </div>
                                    <div style="flex-shrink: 0;">
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
                            <button type="button" onclick="changeFile()" style="margin-top: 0.5rem; padding: 0.4rem 0.75rem; background: white; border: 1px solid #e2e8f0; color: #64748b; border-radius: 0.375rem; font-size: 0.75rem; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc'; this.style.color='#334155'" onmouseout="this.style.background='white'; this.style.color='#64748b'">
                                <i class="fas fa-sync-alt" style="margin-right: 0.35rem;"></i>
                                Ganti File
                            </button>
                        </div>

                        <input type="file" name="file" id="fileInput" form="mainForm" accept=".pdf,.jpg,.jpeg,.png" required style="display: none;">
                    </div>
                    @error('file')
                        <div style="display: flex; align-items: center; gap: 0.375rem; margin-top: 0.5rem; padding: 0.5rem 0.75rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.375rem;">
                            <i class="fas fa-exclamation-circle" style="color: #dc2626; font-size: 0.875rem;"></i>
                            <span style="font-size: 0.8rem; color: #991b1b; font-weight: 500;">{{ $message }}</span>
                        </div>
                    @enderror
                </div>
            </div>

            {{-- Footer Actions --}}
            <div class="form-footer" style="padding: 1rem 1.5rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 0.75rem;">
                <a href="{{ route('sidongan.documents.index') }}" style="padding: 0.625rem 1.25rem; background: #f1f5f9; color: #475569; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; text-decoration: none; transition: all 0.2s; border: 1px solid #e2e8f0;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                    Batal
                </a>
                <button type="submit" form="mainForm" onclick="return validateForm()" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.25rem; background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 8px rgba(59,130,246,0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                    <i class="fas fa-paper-plane"></i>
                    Simpan & Kirim
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // ==========================================
    // VALIDATE DATES
    // ==========================================
    function validateDates() {
        const documentDate = document.getElementById('document_date').value;
        const agendaDate = document.getElementById('agenda_date').value;
        const dateError = document.getElementById('dateError');
        const agendaInput = document.getElementById('agenda_date');
        
        // Dapatkan tanggal hari ini (format YYYY-MM-DD)
        const today = new Date();
        const todayStr = today.toISOString().split('T')[0];
        
        if (documentDate && agendaDate) {
            const docDate = new Date(documentDate);
            const agdDate = new Date(agendaDate);
            const todayDate = new Date(todayStr);
            
            // Cek 1: Tanggal diterima tidak boleh lebih tua dari tanggal surat
            if (agdDate < docDate) {
                dateError.textContent = 'Tanggal diterima tidak boleh lebih tua dari tanggal surat';
                dateError.style.display = 'block';
                agendaInput.style.borderColor = '#ef4444';
                return false;
            }
            
            // Cek 2: Tanggal diterima tidak boleh lebih dari hari ini
            if (agdDate > todayDate) {
                dateError.textContent = 'Tanggal diterima tidak boleh lebih dari hari ini';
                dateError.style.display = 'block';
                agendaInput.style.borderColor = '#ef4444';
                return false;
            }
            
            // Jika semua validasi lolos
            dateError.style.display = 'none';
            agendaInput.style.borderColor = '#e2e8f0';
            return true;
        }
        return true;
    }

    // ==========================================
    // VALIDATE FORM
    // ==========================================
    function validateForm() {
        const suggestion = document.getElementById('suggestion').value.trim();
        
        if (!suggestion) {
            if (typeof Toast !== 'undefined') {
                Toast.error('Saran Sekretaris harus diisi!');
            } else {
                alert('Saran Sekretaris harus diisi!');
            }
            document.getElementById('suggestion').focus();
            document.getElementById('suggestion').style.borderColor = '#ef4444';
            return false;
        }
        
        // Validasi tanggal diterima tidak boleh lebih dari hari ini
        const agendaDate = document.getElementById('agenda_date').value;
        if (agendaDate) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const agdDate = new Date(agendaDate);
            
            if (agdDate > today) {
                if (typeof Toast !== 'undefined') {
                    Toast.error('Tanggal diterima tidak boleh lebih dari hari ini!');
                } else {
                    alert('Tanggal diterima tidak boleh lebih dari hari ini!');
                }
                document.getElementById('agenda_date').focus();
                document.getElementById('agenda_date').style.borderColor = '#ef4444';
                return false;
            }
        }
        
        return validateDates();
    }

    // ==========================================
    // FILE VALIDATION FUNCTION
    // ==========================================
    function validateFile(file) {
        const allowedTypes = [
            'application/pdf',
            'image/jpeg',
            'image/jpg',
            'image/png'
        ];
        
        const allowedExtensions = ['.pdf', '.jpg', '.jpeg', '.png'];
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
                message: `Format file "${file.name}" tidak diizinkan. Hanya PDF dan gambar (JPG, PNG) yang diperbolehkan.`
            };
        }
        
        // Cek ekstensi file (double check)
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
        if (!allowedExtensions.includes(fileExtension)) {
            return {
                valid: false,
                message: `Ekstensi file "${fileExtension}" tidak diizinkan. Hanya .pdf, .jpg, .jpeg, .png yang diperbolehkan.`
            };
        }
        
        return { valid: true, message: '' };
    }

    // ==========================================
    // DRAG & DROP UPLOAD LOGIC
    // ==========================================
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const fileIcon = document.getElementById('fileIcon');
    
    // Flag untuk mencegah multiple clicks
    let isChangingFile = false;

    // Click dropzone to open file picker
    dropZone.addEventListener('click', (e) => {
        if (isChangingFile || e.target.closest('#filePreview') || e.target.closest('button')) {
            return;
        }
        fileInput.click();
    });

    // Drag over effect
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.style.borderColor = '#3b82f6';
        dropZone.style.background = '#eff6ff';
    });

    // Drag leave effect
    dropZone.addEventListener('dragleave', () => {
        dropZone.style.borderColor = '#e2e8f0';
        dropZone.style.background = 'white';
    });

    // Drop file - WITH VALIDATION
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

    // File input change - WITH VALIDATION
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
        
        isChangingFile = false;
    });

    // ==========================================
    // SHOW FILE PREVIEW
    // ==========================================
    function showFilePreview(file) {
        if (uploadPlaceholder) uploadPlaceholder.style.display = 'none';
        if (filePreview) filePreview.style.display = 'block';
        
        if (fileName) fileName.textContent = file.name;
        
        const sizeInKB = (file.size / 1024).toFixed(2);
        const sizeInMB = (file.size / 1024 / 1024).toFixed(2);
        if (fileSize) fileSize.textContent = sizeInKB >= 1024 ? `${sizeInMB} MB` : `${sizeInKB} KB`;
        
        if (fileIcon) {
            const iconElement = fileIcon.querySelector('i');
            if (file.type === 'application/pdf') {
                iconElement.className = 'fas fa-file-pdf';
                iconElement.style.color = '#ef4444';
            } else if (file.type.startsWith('image/')) {
                iconElement.className = 'fas fa-file-image';
                iconElement.style.color = '#10b981';
            } else {
                iconElement.className = 'fas fa-file';
                iconElement.style.color = '#64748b';
            }
        }
        
        dropZone.style.borderColor = '#10b981';
        dropZone.style.background = '#f0fdf4';
    }

    // ==========================================
    // CHANGE FILE BUTTON
    // ==========================================
    function changeFile() {
        isChangingFile = true;
        
        if (uploadPlaceholder) uploadPlaceholder.style.display = 'block';
        if (filePreview) filePreview.style.display = 'none';
        
        fileInput.value = '';
        
        dropZone.style.borderColor = '#e2e8f0';
        dropZone.style.background = 'white';
        
        setTimeout(() => {
            fileInput.click();
        }, 150);
    }
</script>
@endsection