/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/admin/struktur/create.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


// Animation
const style = document.createElement('style');
style.textContent = `
    @keyframes modalSlideUp {
        from { opacity: 0; transform: translateY(20px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
`;
document.head.appendChild(style);

// Cropper variables
let cropper = null;
let originalFile = null;
let isSubmitting = false;

const positions = {
    pengurus: [
        'Ketua Pembina', 
        'Ketua TP PKK', 
        'Staf Ahli 1', 
        'Staf Ahli 2', 
        'Sekretaris', 
        'Bendahara', 
        'Ketua I', 
        'Ketua II', 
        'Ketua III', 
        'Ketua IV'
    ],
    pokja1: ['Ketua', 'Wakil Ketua', 'Sekretaris', 'Anggota'],
    pokja2: ['Ketua', 'Wakil Ketua', 'Sekretaris', 'Anggota'],
    pokja3: ['Ketua', 'Wakil Ketua', 'Sekretaris', 'Anggota'],
    pokja4: ['Ketua', 'Wakil Ketua', 'Sekretaris', 'Anggota']
};

function updatePositions() {
    const group = document.getElementById('groupSelect').value;
    const posSelect = document.getElementById('positionSelect');
    posSelect.innerHTML = '<option value="">-- Pilih Jabatan --</option>';
    if (group && positions[group]) {
        positions[group].forEach(pos => {
            const opt = document.createElement('option');
            opt.value = pos;
            opt.textContent = pos;
            posSelect.appendChild(opt);
        });
    }
}

// Handle photo upload
function handlePhotoUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    if (file.size > 2 * 1024 * 1024) {
        if (typeof Toast !== 'undefined') {
            Toast.error('Ukuran foto terlalu besar. Maksimal 2MB.');
        } else {
            Toast.warning('Ukuran foto terlalu besar. Maksimal 2MB.');
        }
        event.target.value = '';
        return;
    }
    
    originalFile = file;
    const preview = document.getElementById('photoPreview');
    const reader = new FileReader();
    reader.onload = function(e) {
        preview.src = e.target.result;
        preview.style.display = 'block';
        
        // Update text
        const textDiv = preview.nextElementSibling;
        textDiv.querySelector('div:first-child').textContent = 'Foto dipilih';
        textDiv.querySelector('div:last-child').textContent = 'Klik foto untuk atur crop';
        
        // Add remove button if not exists
        if (!document.querySelector('button[onclick="removePhoto()"]')) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.onclick = removePhoto;
            btn.style.cssText = 'margin-left:auto;background:#fef2f2;color:#ef4444;border:none;padding:0.5rem 1rem;border-radius:6px;cursor:pointer;font-size:0.85rem;font-weight:600;transition:all 0.2s';
            btn.textContent = 'Hapus';
            btn.onmouseover = function() { this.style.background='#fee2e2'; this.style.transform='translateY(-2px)'; };
            btn.onmouseout = function() { this.style.background='#fef2f2'; this.style.transform='translateY(0)'; };
            textDiv.parentElement.appendChild(btn);
        }
    };
    reader.readAsDataURL(file);
}

// Remove photo
function removePhoto() {
    document.getElementById('photoInput').value = '';
    const preview = document.getElementById('photoPreview');
    preview.style.display = 'none';
    preview.src = '#';
    document.getElementById('croppedPhoto').value = '';
    originalFile = null;
    
    // Reset text
    const textDiv = preview.nextElementSibling;
    textDiv.querySelector('div:first-child').textContent = 'Belum ada foto';
    textDiv.querySelector('div:last-child').textContent = 'Klik foto untuk atur crop';
    
    // Remove button
    const btn = document.querySelector('button[onclick="removePhoto()"]');
    if (btn) btn.remove();
    
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
}

// Open crop modal
function openCropModal() {
    if (!originalFile) {
        if (typeof Toast !== 'undefined') {
            Toast.warning('Silakan upload foto terlebih dahulu.');
        } else {
            Toast.warning('Silakan upload foto terlebih dahulu.');
        }
        return;
    }
    
    if (typeof Cropper === 'undefined') {
        if (typeof Toast !== 'undefined') {
            Toast.error('Cropper.js belum ter-load. Silakan refresh halaman.');
        } else {
            Toast.warning('Cropper.js belum ter-load. Silakan refresh halaman.');
        }
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        const cropImage = document.getElementById('cropImage');
        cropImage.src = e.target.result;
        
        document.getElementById('cropModal').style.display = 'flex';
        
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        
        cropImage.onload = function() {
            try {
                cropper = new Cropper(cropImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.8,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                    minContainerWidth: 300,
                    minContainerHeight: 300
                });
            } catch (error) {
                console.error('Error initializing cropper:', error);
                if (typeof Toast !== 'undefined') {
                    Toast.error('Gagal menginisialisasi crop tool.');
                } else {
                    Toast.error('Gagal menginisialisasi crop tool.');
                }
            }
        };
    };
    reader.readAsDataURL(originalFile);
}

// Close crop modal
function closeCropModal() {
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    document.getElementById('cropModal').style.display = 'none';
}

// Rotate image
function rotateImage(degrees) {
    if (cropper) cropper.rotate(degrees);
}

// Reset crop
function resetCrop() {
    if (cropper) cropper.reset();
}

// Apply crop
function applyCrop() {
    if (!cropper) {
        if (typeof Toast !== 'undefined') {
            Toast.warning('Crop tool belum siap. Silakan coba lagi.');
        } else {
            Toast.warning('Crop tool belum siap. Silakan coba lagi.');
        }
        return;
    }
    
    try {
        const canvas = cropper.getCroppedCanvas({
            width: 400,
            height: 400,
            imageSmoothingQuality: 'high',
            fillColor: '#fff'
        });
        
        if (!canvas) {
            if (typeof Toast !== 'undefined') {
                Toast.error('Gagal membuat hasil crop.');
            } else {
                Toast.error('Gagal membuat hasil crop.');
            }
            return;
        }
        
        const croppedDataUrl = canvas.toDataURL('image/jpeg', 0.9);
        document.getElementById('photoPreview').src = croppedDataUrl;
        document.getElementById('croppedPhoto').value = croppedDataUrl;
        
        // Update text to show it's been cropped
        const preview = document.getElementById('photoPreview');
        const textDiv = preview.nextElementSibling;
        textDiv.querySelector('div:first-child').textContent = 'Foto dipilih (sudah di-crop)';
        textDiv.querySelector('div:last-child').textContent = 'Klik untuk crop ulang';
        
        if (typeof Toast !== 'undefined') {
            Toast.success('Foto berhasil di-crop!');
        }
        
        closeCropModal();
    } catch (error) {
        console.error('Error applying crop:', error);
        if (typeof Toast !== 'undefined') {
            Toast.error('Gagal menerapkan crop. Silakan coba lagi.');
        } else {
            Toast.error('Gagal menerapkan crop. Silakan coba lagi.');
        }
    }
}

// AUTO-CROP saat form submit jika user belum crop
document.getElementById('mainForm').addEventListener('submit', function(e) {
    // Jika sudah submitting, lanjutkan
    if (isSubmitting) return true;
    
    const croppedPhoto = document.getElementById('croppedPhoto').value;
    
    // Jika sudah ada cropped photo, lanjutkan submit
    if (croppedPhoto) {
        return true;
    }
    
    // Jika ada file tapi belum di-crop, auto-crop
    if (originalFile) {
        e.preventDefault();
        isSubmitting = true;
        
        const form = this;
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> Memproses...';
        
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                // Buat canvas untuk auto-crop (center square)
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                
                canvas.width = 400;
                canvas.height = 400;
                
                // Hitung crop area (center square)
                const size = Math.min(img.width, img.height);
                const x = (img.width - size) / 2;
                const y = (img.height - size) / 2;
                
                // Draw cropped image
                ctx.fillStyle = '#fff';
                ctx.fillRect(0, 0, 400, 400);
                ctx.drawImage(img, x, y, size, size, 0, 0, 400, 400);
                
                // Convert to base64
                const croppedDataUrl = canvas.toDataURL('image/jpeg', 0.9);
                document.getElementById('croppedPhoto').value = croppedDataUrl;
                
                // Submit form setelah auto-crop
                setTimeout(() => form.submit(), 100);
            };
            img.src = e.target.result;
        };
        
        reader.readAsDataURL(originalFile);
        return false;
    }
    
    // Jika tidak ada foto, lanjutkan submit
    return true;
});

// Add spin animation
const spinStyle = document.createElement('style');
spinStyle.textContent = `
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(spinStyle);



// ============================================================
// Dikembangkan oleh Institut Teknologi Del
// DELEGATION (menggantikan onclick/onchange inline)
// ============================================================
document.addEventListener('click', function (event) {
    const target = event.target;
    if (target.closest('[data-action="open-crop"]')) {
        openCropModal();
        return;
    }
    if (target.closest('[data-action="close-crop"]')) {
        closeCropModal();
        return;
    }
    if (target.closest('[data-action="rotate-crop"]')) {
        const deg = parseInt((target.closest('[data-action="rotate-crop"]').getAttribute('data-deg') || '0'), 10);
        rotateImage(deg);
        return;
    }
    if (target.closest('[data-action="reset-crop"]')) {
        resetCrop();
        return;
    }
    if (target.closest('[data-action="apply-crop"]')) {
        applyCrop();
        return;
    }
    if (target.closest('[data-action="remove-photo"]')) {
        removePhoto();
    }
});

const groupSelect = document.getElementById('groupSelect');
if (groupSelect) {
    groupSelect.addEventListener('change', updatePositions);
}
const photoInput = document.getElementById('photoInput');
if (photoInput) {
    photoInput.addEventListener('change', function (event) {
        handlePhotoUpload(event);
    });
}
