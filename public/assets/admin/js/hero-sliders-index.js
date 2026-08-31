/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Index Hero Slider — dipisah dari HTML (hero-sliders/index.blade.php)
 * URL reorder dibaca dari data-reorder-url pada #slidersList.
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


// ==========================================
// DELETE CONFIRMATION
// ==========================================
async function confirmDeleteWithToast(id, name) {
    try {
        const confirmed = await Toast.confirm(
            `Slide <strong>"${name}"</strong> akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.`,
            {
                title: 'Hapus Slide?',
                confirmText: 'Ya, Hapus',
                cancelText: 'Batal',
                type: 'danger'
            }
        );
        if (confirmed) submitDelete(id);
    } catch (error) {
        // Jangan jatuh ke confirm() bawaan: penghapusan dibatalkan dan
        // pengguna diberi tahu lewat Toast agar tampilannya seragam.
        console.error('Error:', error);
        Toast.error('Gagal menampilkan konfirmasi. Silakan coba lagi.');
    }
}

function submitDelete(id) {
    const form = document.getElementById('delete-form-' + id);
    if (form) form.submit();
}

// ==========================================
// MOBILE REORDER FUNCTIONS
// ==========================================
function moveSlideUp(id) {
    const slidersList = document.getElementById('slidersList');
    const items = [...slidersList.querySelectorAll('.slider-item')];
    const currentIndex = items.findIndex(item => item.dataset.id == id);

    if (currentIndex > 0) {
        const currentItem = items[currentIndex];
        const prevItem = items[currentIndex - 1];

        slidersList.insertBefore(currentItem, prevItem);
        updateOrder();
        refreshMobileReorderButtons();
    }
}

function moveSlideDown(id) {
    const slidersList = document.getElementById('slidersList');
    const items = [...slidersList.querySelectorAll('.slider-item')];
    const currentIndex = items.findIndex(item => item.dataset.id == id);

    if (currentIndex < items.length - 1) {
        const currentItem = items[currentIndex];
        const nextItem = items[currentIndex + 1];

        slidersList.insertBefore(nextItem, currentItem);
        updateOrder();
        refreshMobileReorderButtons();
    }
}

function refreshMobileReorderButtons() {
    const slidersList = document.getElementById('slidersList');
    const items = slidersList.querySelectorAll('.slider-item');

    items.forEach((item, index) => {
        const mobileReorder = item.querySelector('.mobile-reorder');
        if (mobileReorder) {
            const buttons = mobileReorder.querySelectorAll('button');
            buttons[0].disabled = index === 0;
            buttons[1].disabled = index === items.length - 1;
        }
    });
}

// ==========================================
// EDIT SLIDER
// ==========================================
function editSlider(id) {
    const item = document.querySelector(`.slider-item[data-id="${id}"]`);
    if (!item) return;

    const form = document.getElementById('editForm');
    form.action = `/admin/hero-sliders/${id}`;

    document.getElementById('editId').value = id;

    const infoText = item.querySelector('.slider-meta').parentElement.textContent;
    document.getElementById('editDuration').value = infoText.match(/(\d+)s/)?.[1] || '5';

    const isActive = infoText.includes('Aktif') && !infoText.includes('Nonaktif');
    document.getElementById('editActive').checked = isActive;
    updateCheckboxStyle('editActiveBox', 'editActiveCheck', isActive);

    const preview = document.getElementById('editImagePreview');
    preview.src = item.querySelector('img').src;
    preview.style.display = 'block';

    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
    document.getElementById('editImagePreview').style.display = 'none';
}

// ==========================================
// CHECKBOX ANIMATION
// ==========================================
function updateCheckboxStyle(boxId, checkId, isChecked) {
    const box = document.getElementById(boxId);
    const check = document.getElementById(checkId);
    if (!box || !check) return;

    if (isChecked) {
        box.classList.add('checked');
    } else {
        box.classList.remove('checked');
    }
}

// ==========================================
// INITIALIZATION
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    const isActiveCheckbox = document.getElementById('isActive');
    if (isActiveCheckbox) {
        updateCheckboxStyle('isActiveBox', 'isActiveCheck', isActiveCheckbox.checked);
        isActiveCheckbox.addEventListener('change', function() {
            updateCheckboxStyle('isActiveBox', 'isActiveCheck', this.checked);
        });
    }

    const editActiveCheckbox = document.getElementById('editActive');
    if (editActiveCheckbox) {
        editActiveCheckbox.addEventListener('change', function() {
            updateCheckboxStyle('editActiveBox', 'editActiveCheck', this.checked);
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeEditModal();
    });

    addMobileReorderButtons();
    initDragAndDrop();
});

function addMobileReorderButtons() {
    const slidersList = document.getElementById('slidersList');
    const items = slidersList.querySelectorAll('.slider-item');

    items.forEach((item, index) => {
        if (item.querySelector('.mobile-reorder')) return;

        const actionsDiv = item.querySelector('.slider-actions');
        const mobileReorder = document.createElement('div');
        mobileReorder.className = 'mobile-reorder';
        mobileReorder.innerHTML = `
            <button onclick="moveSlideUp(${item.dataset.id})" ${index === 0 ? 'disabled' : ''}>
                Geser ke Atas
            </button>
            <button onclick="moveSlideDown(${item.dataset.id})" ${index === items.length - 1 ? 'disabled' : ''}>
                Geser ke Bawah
            </button>
        `;

        actionsDiv.after(mobileReorder);
    });
}

// ==========================================
// DRAG AND DROP (Desktop Only)
// ==========================================
let draggedItem = null;

function initDragAndDrop() {
    if (window.innerWidth <= 768) return;

    const slidersList = document.getElementById('slidersList');
    if (!slidersList) return;

    const items = slidersList.querySelectorAll('.slider-item');

    items.forEach(item => {
        item.setAttribute('draggable', 'true');

        item.addEventListener('dragstart', function(e) {
            draggedItem = this;
            setTimeout(() => {
                this.classList.add('dragging');
            }, 0);
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', this.dataset.id);
        });

        item.addEventListener('dragend', function(e) {
            this.classList.remove('dragging');
            draggedItem = null;

            document.querySelectorAll('.slider-item').forEach(el => {
                el.classList.remove('drag-over');
            });

            updateOrder();
        });

        item.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';

            if (this === draggedItem) return;

            this.classList.add('drag-over');
        });

        item.addEventListener('dragleave', function(e) {
            this.classList.remove('drag-over');
        });

        item.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();

            if (this === draggedItem) return;

            this.classList.remove('drag-over');

            const rect = this.getBoundingClientRect();
            const midpoint = rect.top + rect.height / 2;

            if (e.clientY < midpoint) {
                slidersList.insertBefore(draggedItem, this);
            } else {
                if (this.nextSibling) {
                    slidersList.insertBefore(draggedItem, this.nextSibling);
                } else {
                    slidersList.appendChild(draggedItem);
                }
            }
        });
    });

    slidersList.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
    });

    slidersList.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();

        if (draggedItem && e.target === this) {
            this.appendChild(draggedItem);
            updateOrder();
        }
    });
}

async function updateOrder() {
    const order = [...document.querySelectorAll('.slider-item')].map(el => el.dataset.id);

    try {
        const response = await fetch(document.getElementById('slidersList').dataset.reorderUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]') || {}).content || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ order })
        });

        if (response.ok) {
            console.log('✅ Order updated successfully');
        } else {
            console.error('❌ Failed to update order');
        }
    } catch (error) {
        console.error('❌ Error updating order:', error);
    }
}


// ============================================================
// Dikembangkan oleh Institut Teknologi Del
// DELEGATION (menggantikan onclick inline)
// ============================================================
document.addEventListener('click', function (event) {
    const editBtn = event.target.closest('[data-edit-slider]');
    if (editBtn) {
        editSlider(parseInt(editBtn.getAttribute('data-edit-slider'), 10));
        return;
    }
    const delBtn = event.target.closest('[data-delete-slider]');
    if (delBtn) {
        confirmDeleteWithToast(
            delBtn.getAttribute('data-delete-slider'),
            delBtn.getAttribute('data-delete-slider-title') || ''
        );
        return;
    }
    if (event.target.closest('[data-action="close-edit-modal"]')) {
        closeEditModal();
    }
});
