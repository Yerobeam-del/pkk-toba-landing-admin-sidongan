/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/admin/berita/edit.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


document.addEventListener('DOMContentLoaded', function() {
    // Konfigurasi Alignment
    const alignmentConfig = {
        options: [
            { name: 'left', title: 'Rata Kiri', icon: 'left', value: 'left' },
            { name: 'center', title: 'Rata Tengah', icon: 'center', value: 'center' },
            { name: 'right', title: 'Rata Kanan', icon: 'right', value: 'right' },
            { name: 'justify', title: 'Rata Kiri-Kanan', icon: 'justify', value: 'justify' }
        ]
    };

    ClassicEditor
        .create(document.querySelector('#contentEditor'), {
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'link', '|',
                'bulletedList', 'numberedList', '|',
                'alignment',
                '|',
                'blockQuote', '|',
                'undo', 'redo'
            ],
            
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                ]
            },
            
            alignment: alignmentConfig,
            height: 500,
            allowedContent: true
        })
        .then(editor => {
            window.editor = editor;
        })
        .catch(error => {
            console.error('❌ CKEditor 5 error:', error);
            console.error('Stack:', error.stack);
        });
});

// Character Counter untuk Excerpt
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('excerptInput');
    const counter = document.getElementById('excerptCounter');
    const maxLength = 160;

    function updateCounter() {
        const currentLength = textarea.value.length;
        counter.textContent = `${currentLength}/${maxLength}`;

        if (currentLength >= maxLength) {
            counter.style.color = '#ef4444';
        } else if (currentLength >= maxLength * 0.85) {
            counter.style.color = '#f59e0b';
        } else {
            counter.style.color = 'var(--text-muted, #6b7280)';
        }
    }

    textarea.addEventListener('input', updateCounter);
    updateCounter();
});

// Image Preview Functionality
document.getElementById('imageInput')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        if (file.size > 2 * 1024 * 1024) {
            Toast.warning('Ukuran gambar terlalu besar. Maksimal 2MB.');
            e.target.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const existingImg = document.getElementById('existingImage');
            if (existingImg) existingImg.style.display = 'none';
            
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('newImagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

// Konfirmasi hapus gambar berita — Toast.confirm (konsisten dengan halaman lain).
// Delegasi submit form (bukan onsubmit inline) agar tetap jalan walau tombol diklik
// atau form disubmit via keyboard (Enter).
document.addEventListener('submit', function(e) {
    const form = e.target.closest('form.delete-image-form');
    if (!form) return;
    e.preventDefault();
    Toast.confirm(
        'Gambar berita ini akan dihapus secara permanen.',
        {
            title: 'Hapus Gambar?',
            confirmText: 'Ya, Hapus',
            cancelText: 'Batal',
            type: 'danger'
        }
    ).then(function(confirmed) {
        if (confirmed) form.submit();
    });
});

// Checkbox Animation Handler
function updateCheckboxStyle(boxId, checkId, isChecked) {
    const box = document.getElementById(boxId);
    const check = document.getElementById(checkId);
    
    if (!box || !check) return;
    
    if (isChecked) {
        box.style.background = 'linear-gradient(135deg, #14b8a6, #0d9488)';
        box.style.borderColor = '#14b8a6';
        box.style.boxShadow = '0 2px 8px rgba(20,184,166,0.3)';
        check.style.opacity = '1';
        check.style.transform = 'scale(1)';
    } else {
        box.style.background = '#fff';
        box.style.borderColor = '#cbd5e1';
        box.style.boxShadow = 'none';
        check.style.opacity = '0';
        check.style.transform = 'scale(0.5)';
    }
}

// Initialize checkbox state
document.addEventListener('DOMContentLoaded', function() {
    const isPublishedCheckbox = document.getElementById('isPublished');
    if (isPublishedCheckbox) {
        updateCheckboxStyle('isPublishedBox', 'isPublishedCheck', isPublishedCheckbox.checked);
        
        isPublishedCheckbox.addEventListener('change', function() {
            updateCheckboxStyle('isPublishedBox', 'isPublishedCheck', this.checked);
        });
    }
});


