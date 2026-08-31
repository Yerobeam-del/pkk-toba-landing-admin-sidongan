/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Form Struktur (edit) — dipisah dari HTML.
 * Data dibaca dari data-* pada #editForm.
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
let existingPhotoUrl = document.getElementById('editForm')?.dataset.existingPhoto || '';

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

// Handle photo upload (NEW file)
function handlePhotoUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    if (file.size > 2 * 1024 * 1024) {
        Toast.error('Ukuran foto terlalu besar. Maksimal 2MB.');
        event.target.value = '';
        return;
    }
    
    originalFile = file;
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('photoPreview');
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

// Open crop modal - FIXED to handle existing photo
function openCropModal() {
    console.log('Opening crop modal...');
    console.log('Existing photo URL:', existingPhotoUrl);
    console.log('Original file:', originalFile);
    
    // Check if there's a photo to crop
    const hasExistingPhoto = existingPhotoUrl && existingPhotoUrl !== '#' && existingPhotoUrl !== '';
    const hasNewFile = originalFile !== null;
    
    if (!hasExistingPhoto && !hasNewFile) {
        Toast.warning('Silakan upload foto terlebih dahulu.');
        return;
    }
    
    if (typeof Cropper === 'undefined') {
        Toast.error('Cropper.js belum ter-load. Silakan refresh halaman.');
        console.error('Cropper.js is not loaded!');
        return;
    }
    
    // Determine which image to use
    if (hasNewFile && originalFile) {
        // Use newly uploaded file
        const reader = new FileReader();
        reader.onload = function(e) {
            initializeCropper(e.target.result);
        };
        reader.readAsDataURL(originalFile);
    } else if (hasExistingPhoto) {
        // Use existing photo from server
        initializeCropper(existingPhotoUrl);
    }
}

// Initialize cropper (helper function)
function initializeCropper(imageSrc) {
    const cropImage = document.getElementById('cropImage');
    cropImage.src = imageSrc;
    
    // Show modal
    document.getElementById('cropModal').style.display = 'flex';
    
    // Destroy existing cropper
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    
    // Initialize cropper after image loads
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
            console.log('Cropper initialized successfully');
        } catch (error) {
            console.error('Error initializing cropper:', error);
            Toast.error('Gagal menginisialisasi crop tool.');
        }
    };
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
        Toast.warning('Crop tool belum siap. Silakan coba lagi.');
        return;
    }
    
    try {
        const canvas = cropper.getCroppedCanvas({
            width: 400,
            height: 400,
            imageSmoothingQuality: 'high',
            fillColor: '#fff'
        });
        
        const croppedDataUrl = canvas.toDataURL('image/jpeg', 0.9);
        
        // Set to preview
        document.getElementById('photoPreview').src = croppedDataUrl;
        document.getElementById('croppedPhoto').value = croppedDataUrl;
        
        // Update text to show it's been cropped
        const preview = document.getElementById('photoPreview');
        const textDiv = preview.nextElementSibling;
        textDiv.querySelector('div:first-child').textContent = 'Foto dipilih (sudah di-crop)';
        textDiv.querySelector('div:last-child').textContent = 'Klik untuk crop ulang';
        
        Toast.success('Foto berhasil di-crop!');
        
        closeCropModal();
    } catch (error) {
        console.error('Error applying crop:', error);
        Toast.error('Gagal menerapkan crop. Silakan coba lagi.');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Edit page loaded');
    console.log('Current group:', document.getElementById('editForm')?.dataset.currentGroup);
    console.log('Current position:', document.getElementById('editForm')?.dataset.currentPosition);
    
    // 1. Set group & populate positions
    const groupSelect = document.getElementById('groupSelect');
    const currentGroup = document.getElementById('editForm')?.dataset.currentGroup || null;
    const currentPosition = document.getElementById('editForm')?.dataset.currentPosition || null;
    
    if (currentGroup) {
        groupSelect.value = currentGroup;
        updatePositions();
        
        // 2. Set selected position AFTER options are populated
        setTimeout(() => {
            const posSelect = document.getElementById('positionSelect');
            if (currentPosition) {
                // Handle special case for Sekretaris Pokja
                let positionToSelect = currentPosition;
                if (currentGroup !== 'pengurus' && currentPosition === 'Sekretaris Pokja') {
                    positionToSelect = 'Sekretaris';
                }
                
                // Find and select the option
                const optionExists = Array.from(posSelect.options).some(opt => opt.value === positionToSelect);
                if (optionExists) {
                    posSelect.value = positionToSelect;
                    console.log('Position set to:', positionToSelect);
                } else {
                    console.warn('Position option not found:', positionToSelect);
                }
            }
        }, 100);
    }
});


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
