/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Buat Surat Masuk Baru — preview nomor agenda real-time,
 * validasi tanggal, validasi file, dan drag & drop upload.
 * Nilai awal nomor urut dibaca dari data-preview-sequence
 * pada input #preview_agenda yang diisi oleh Blade.
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
(function () {
    'use strict';

    const previewInput = document.getElementById('preview_agenda');
    let currentSequence = previewInput ? previewInput.getAttribute('data-preview-sequence') : '';

    // ==========================================
    // UPDATE AGENDA PREVIEW (Real-time)
    // ==========================================
    const BULAN_ROMAWI = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

    function updateAgendaPreview() {
        const agendaDate = document.getElementById('agenda_date').value;
        const previewInputEl = document.getElementById('preview_agenda');
        const previewNote = document.getElementById('preview_agenda_note');
        const hiddenInput = document.getElementById('agenda_number_input');

        if (!agendaDate) {
            // Fallback ke tanggal hari ini
            const today = new Date();
            const bulan = BULAN_ROMAWI[today.getMonth()];
            const tahun = today.getFullYear();
            previewInputEl.value = currentSequence + '/SM/PKK-T/' + bulan + '/' + tahun;
            previewNote.innerHTML = 'Nomor urut &#10095; Surat Masuk &#10095; PKK Toba &#10095; ' + bulan + ' &#10095; ' + tahun;
            if (hiddenInput) hiddenInput.value = previewInputEl.value;
            return;
        }

        const dateObj = new Date(agendaDate + 'T00:00:00');
        const bulan = BULAN_ROMAWI[dateObj.getMonth()];
        const tahun = dateObj.getFullYear();
        previewInputEl.value = currentSequence + '/SM/PKK-T/' + bulan + '/' + tahun;
        previewNote.innerHTML = 'Nomor urut &#10095; Surat Masuk &#10095; PKK Toba &#10095; ' + bulan + ' &#10095; ' + tahun;
        if (hiddenInput) hiddenInput.value = previewInputEl.value;
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
        const finishEditing = function () {
            input.readOnly = true;
            input.style.cursor = 'not-allowed';
            input.style.background = '#f8fafc';
            input.style.borderColor = '#e2e8f0';
            input.style.boxShadow = 'none';

            // Update hidden input
            const hiddenInput = document.getElementById('agenda_number_input');
            if (hiddenInput) hiddenInput.value = input.value;
        };

        input.addEventListener('blur', finishEditing, { once: true });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                input.blur();
            }
        }, { once: true });
    }

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
            Toast.error('Saran Sekretaris harus diisi!');
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
                Toast.error('Tanggal diterima tidak boleh lebih dari hari ini!');
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
                message: 'Ukuran file terlalu besar (' + sizeInMB + 'MB). Maksimal 5MB.'
            };
        }

        // Cek tipe file (MIME type)
        if (!allowedTypes.includes(file.type)) {
            return {
                valid: false,
                message: 'Format file "' + file.name + '" tidak diizinkan. Hanya PDF dan gambar (JPG, PNG) yang diperbolehkan.'
            };
        }

        // Cek ekstensi file (double check)
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
        if (!allowedExtensions.includes(fileExtension)) {
            return {
                valid: false,
                message: 'Ekstensi file "' + fileExtension + '" tidak diizinkan. Hanya .pdf, .jpg, .jpeg, .png yang diperbolehkan.'
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

    function showFilePreview(file) {
        if (uploadPlaceholder) uploadPlaceholder.style.display = 'none';
        if (filePreview) filePreview.style.display = 'block';

        if (fileName) fileName.textContent = file.name;

        const sizeInKB = (file.size / 1024).toFixed(2);
        const sizeInMB = (file.size / 1024 / 1024).toFixed(2);
        if (fileSize) fileSize.textContent = sizeInKB >= 1024 ? sizeInMB + ' MB' : sizeInKB + ' KB';

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

    if (dropZone && fileInput) {
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

            if (e.dataTransfer.files.length > 0) {
                const file = e.dataTransfer.files[0];
                const validation = validateFile(file);

                if (!validation.valid) {
                    Toast.error(validation.message);
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
                    Toast.error(validation.message);
                    e.target.value = '';
                    return;
                }

                showFilePreview(file);
            }

            isChangingFile = false;
        });
    }

    // ==========================================
    // EVENT WIRING (menggantikan onclick/onchange/ondblclick inline)
    // ==========================================

    // Perubahan tanggal → perbarui preview & validasi
    const documentDate = document.getElementById('document_date');
    const agendaDate = document.getElementById('agenda_date');
    if (documentDate) {
        documentDate.addEventListener('change', () => {
            validateDates();
            updateAgendaPreview();
        });
    }
    if (agendaDate) {
        agendaDate.addEventListener('change', () => {
            updateAgendaPreview();
            validateDates();
        });
    }

    // Double-click nomor agenda → edit
    if (previewInput) {
        previewInput.addEventListener('dblclick', function () {
            enableEditAgenda(this);
        });
    }

    // Tombol "Ganti File"
    document.addEventListener('click', function (event) {
        const btn = event.target.closest('[data-action="change-file"]');
        if (btn) {
            changeFile();
        }
    });

    // Validasi form saat submit
    const mainForm = document.getElementById('mainForm');
    if (mainForm) {
        mainForm.addEventListener('submit', function (e) {
            if (!validateForm()) {
                e.preventDefault();
            }
        });
    }

    // Panggil sekali saat halaman dimuat untuk sync preview
    document.addEventListener('DOMContentLoaded', function () {
        updateAgendaPreview();
    });
})();
