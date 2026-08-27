/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/sidongan/documents/edit.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


    // ==========================================
    // BULAN ROMAWI (sama dengan create.blade.php)
    // ==========================================
    const BULAN_ROMAWI = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

    // ==========================================
    // UPDATE AGENDA PREVIEW DI EDIT
    // ==========================================
    function updateAgendaPreviewEdit() {
        const agendaDate = document.getElementById('agenda_date_edit').value;
        const docDate = document.getElementById('document_date').value;
        const previewInput = document.getElementById('preview_agenda_edit');
        const hiddenInput = document.getElementById('agenda_number_edit_input');

        // Pakai agenda_date, fallback ke document_date
        const tanggalDasar = agendaDate || docDate;

        if (!tanggalDasar) {
            return; // Biarkan value existing
        }

        // Ambil nomor urut dari nomor agenda yang sudah ada
        const existingAgenda = previewInput.value;
        let sequence = 'XXX';
        if (existingAgenda && existingAgenda !== 'Belum ada') {
            const parts = existingAgenda.split('/');
            if (parts.length > 0) {
                sequence = parts[0];
            }
        }

        const dateObj = new Date(tanggalDasar + 'T00:00:00');
        const bulan = BULAN_ROMAWI[dateObj.getMonth()];
        const tahun = dateObj.getFullYear();
        previewInput.value = sequence + '/SM/PKK-T/' + bulan + '/' + tahun;
        if (hiddenInput) hiddenInput.value = previewInput.value;
    }

    // ==========================================
    // DOUBLE-CLICK TO EDIT AGENDA NUMBER
    // ==========================================
    function enableEditAgenda(input) {
        // Hanya aktifkan jika input readonly
        if (!input.readOnly) return;

        // Ubah jadi bisa diedit
        input.readOnly = false;
        input.style.cursor = 'text';
        input.style.background = '#fff';
        input.style.borderColor = '#3b82f6';
        input.style.boxShadow = '0 0 0 3px rgba(59,130,246,0.1)';
        input.focus();
        input.select();

        // Saat selesai edit (blur atau Enter), simpan dan kembalikan readonly
        const finishEditing = function() {
            input.readOnly = true;
            input.style.cursor = 'not-allowed';
            input.style.background = '#f8fafc';
            input.style.borderColor = '#e2e8f0';
            input.style.boxShadow = 'none';

            // Update hidden input
            const hiddenInput = document.getElementById('agenda_number_edit_input');
            if (hiddenInput) hiddenInput.value = input.value;
        };

        input.addEventListener('blur', finishEditing, { once: true });
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                input.blur();
            }
        }, { once: true });
    }

    // Panggil preview saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        updateAgendaPreviewEdit();
    });

    // ==========================================
    // VALIDATE DATES
    // ==========================================
    function validateDates() {
        const documentDate = document.getElementById('document_date').value;
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
                    Toast.warning('Tanggal surat tidak boleh lebih dari hari ini!');
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
                    Toast.warning('Tanggal surat tidak boleh lebih dari hari ini!');
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
                    Toast.warning(validation.message);
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
                    Toast.warning(validation.message);
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
        const form = event.target.closest('form');
        Toast.confirm(
            'Lampiran file pada surat ini akan dihapus.',
            { title: 'Hapus Lampiran?', confirmText: 'Ya, Hapus', cancelText: 'Batal', type: 'danger' }
        ).then(function (setuju) {
            if (!setuju) return;
            document.getElementById('deleteFileInput').value = '1';
            form.submit();
        });
    }



// ============================================================
// Dikembangkan oleh Institut Teknologi Del
// EVENT WIRING (menggantikan onchange/ondblclick/onclick inline)
// ============================================================
const docDateEdit = document.getElementById('document_date');
const agdDateEdit = document.getElementById('agenda_date_edit');
if (docDateEdit) {
    docDateEdit.addEventListener('change', function () {
        validateDates();
        updateAgendaPreviewEdit();
    });
}
if (agdDateEdit) {
    agdDateEdit.addEventListener('change', function () {
        updateAgendaPreviewEdit();
    });
}

const previewEdit = document.getElementById('preview_agenda_edit');
if (previewEdit) {
    previewEdit.addEventListener('dblclick', function () {
        enableEditAgenda(this);
    });
}

document.addEventListener('click', function (event) {
    const changeBtn = event.target.closest('[data-action="change-file"]');
    if (changeBtn) {
        changeFile();
        return;
    }
    const delBtn = event.target.closest('[data-action="confirm-delete-file"]');
    if (delBtn) {
        confirmDeleteFile(event);
    }
});

const editForm = document.getElementById('editForm');
if (editForm) {
    editForm.addEventListener('submit', function (e) {
        if (!validateForm()) {
            e.preventDefault();
        }
    });
}
