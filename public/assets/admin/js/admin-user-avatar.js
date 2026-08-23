/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS bersama untuk avatar upload + crop (create & edit user)
 * Bergantung pada Cropper.js (sudah di-load di blade)
 * ============================================================ */

let avatarCropper = null;
let avatarOriginalFile = null;

// ==========================================
// PHOTO UPLOAD HANDLER
// ==========================================
function handleAvatarUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    if (file.size > 2 * 1024 * 1024) {
        if (typeof Toast !== 'undefined') {
            Toast.error('Ukuran foto terlalu besar. Maksimal 2MB.');
        }
        event.target.value = '';
        return;
    }

    avatarOriginalFile = file;
    const preview = document.getElementById('photoPreview');
    const placeholder = document.getElementById('avatarPlaceholder');
    const avatarText = document.getElementById('avatarText');
    const removeBtn = document.getElementById('removePhotoBtn');

    const reader = new FileReader();
    reader.onload = function (e) {
        preview.src = e.target.result;
        preview.style.display = 'block';
        if (placeholder) placeholder.style.display = 'none';
        if (removeBtn) removeBtn.style.display = 'flex';

        if (avatarText) {
            avatarText.querySelector('.avatar-text-primary').textContent = 'Foto dipilih';
            avatarText.querySelector('.avatar-text-secondary').textContent = 'Klik foto untuk atur crop';
        }
    };
    reader.readAsDataURL(file);
}

// ==========================================
// REMOVE PHOTO
// ==========================================
function removeAvatar() {
    document.getElementById('photoInput').value = '';
    const preview = document.getElementById('photoPreview');
    const placeholder = document.getElementById('avatarPlaceholder');
    const avatarText = document.getElementById('avatarText');
    const removeBtn = document.getElementById('removePhotoBtn');
    const croppedPhoto = document.getElementById('croppedPhoto');

    preview.style.display = 'none';
    preview.src = '#';
    if (placeholder) placeholder.style.display = 'flex';
    if (removeBtn) removeBtn.style.display = 'none';
    if (croppedPhoto) croppedPhoto.value = '';
    avatarOriginalFile = null;

    if (avatarText) {
        avatarText.querySelector('.avatar-text-primary').textContent = 'Belum ada foto';
        avatarText.querySelector('.avatar-text-secondary').textContent = 'Format: JPG/PNG, maks 2MB';
    }

    if (avatarCropper) {
        avatarCropper.destroy();
        avatarCropper = null;
    }
}

// ==========================================
// OPEN CROP MODAL
// ==========================================
function openAvatarCropModal() {
    if (!avatarOriginalFile) {
        if (typeof Toast !== 'undefined') {
            Toast.warning('Silakan upload foto terlebih dahulu.');
        }
        return;
    }

    if (typeof Cropper === 'undefined') {
        if (typeof Toast !== 'undefined') {
            Toast.error('Cropper.js belum ter-load. Silakan refresh halaman.');
        }
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        const cropImage = document.getElementById('cropImage');
        cropImage.src = e.target.result;

        document.getElementById('cropModal').style.display = 'flex';

        if (avatarCropper) {
            avatarCropper.destroy();
            avatarCropper = null;
        }

        cropImage.onload = function () {
            try {
                avatarCropper = new Cropper(cropImage, {
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
                }
            }
        };
    };
    reader.readAsDataURL(avatarOriginalFile);
}

// ==========================================
// CLOSE CROP MODAL
// ==========================================
function closeAvatarCropModal() {
    if (avatarCropper) {
        avatarCropper.destroy();
        avatarCropper = null;
    }
    document.getElementById('cropModal').style.display = 'none';
}

// ==========================================
// ROTATE / RESET / APPLY CROP
// ==========================================
function rotateAvatarImage(degrees) {
    if (avatarCropper) avatarCropper.rotate(degrees);
}

function resetAvatarCrop() {
    if (avatarCropper) avatarCropper.reset();
}

function applyAvatarCrop() {
    if (!avatarCropper) {
        if (typeof Toast !== 'undefined') {
            Toast.warning('Crop tool belum siap. Silakan coba lagi.');
        }
        return;
    }

    try {
        const canvas = avatarCropper.getCroppedCanvas({
            width: 400,
            height: 400,
            imageSmoothingQuality: 'high',
            fillColor: '#fff'
        });

        if (!canvas) {
            if (typeof Toast !== 'undefined') {
                Toast.error('Gagal membuat hasil crop.');
            }
            return;
        }

        const croppedDataUrl = canvas.toDataURL('image/jpeg', 0.9);
        document.getElementById('photoPreview').src = croppedDataUrl;
        document.getElementById('croppedPhoto').value = croppedDataUrl;

        const avatarText = document.getElementById('avatarText');
        if (avatarText) {
            avatarText.querySelector('.avatar-text-primary').textContent = 'Foto dipilih (sudah di-crop)';
            avatarText.querySelector('.avatar-text-secondary').textContent = 'Klik untuk crop ulang';
        }

        if (typeof Toast !== 'undefined') {
            Toast.success('Foto berhasil di-crop!');
        }

        closeAvatarCropModal();
    } catch (error) {
        console.error('Error applying crop:', error);
        if (typeof Toast !== 'undefined') {
            Toast.error('Gagal menerapkan crop. Silakan coba lagi.');
        }
    }
}

// ==========================================
// AUTO-CROP on submit (if user didn't crop)
// ==========================================
function autoCropOnSubmit(form) {
    const croppedPhoto = document.getElementById('croppedPhoto');
    if (!croppedPhoto) return true;

    if (croppedPhoto.value) return true;

    if (avatarOriginalFile) {
        return new Promise((resolve) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = new Image();
                img.onload = function () {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    canvas.width = 400;
                    canvas.height = 400;

                    const size = Math.min(img.width, img.height);
                    const x = (img.width - size) / 2;
                    const y = (img.height - size) / 2;

                    ctx.fillStyle = '#fff';
                    ctx.fillRect(0, 0, 400, 400);
                    ctx.drawImage(img, x, y, size, size, 0, 0, 400, 400);

                    croppedPhoto.value = canvas.toDataURL('image/jpeg', 0.9);
                    resolve(true);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(avatarOriginalFile);
        });
    }

    return true;
}

// ==========================================
// EVENT DELEGATION (data-action)
// ==========================================
document.addEventListener('click', function (event) {
    const target = event.target;

    if (target.closest('[data-action="open-crop"]')) {
        openAvatarCropModal();
        return;
    }
    if (target.closest('[data-action="close-crop"]')) {
        closeAvatarCropModal();
        return;
    }
    if (target.closest('[data-action="rotate-crop"]')) {
        const deg = parseInt(target.closest('[data-action="rotate-crop"]').getAttribute('data-deg') || '0', 10);
        rotateAvatarImage(deg);
        return;
    }
    if (target.closest('[data-action="reset-crop"]')) {
        resetAvatarCrop();
        return;
    }
    if (target.closest('[data-action="apply-crop"]')) {
        applyAvatarCrop();
        return;
    }
    if (target.closest('[data-action="remove-photo"]')) {
        removeAvatar();
        return;
    }
});

// ==========================================
// INIT on DOMContentLoaded
// ==========================================
document.addEventListener('DOMContentLoaded', function () {
    const photoInput = document.getElementById('photoInput');
    if (photoInput) {
        photoInput.addEventListener('change', handleAvatarUpload);
    }
});

// ============================================================
// Dikembangkan oleh Institut Teknologi Del
// ============================================================
