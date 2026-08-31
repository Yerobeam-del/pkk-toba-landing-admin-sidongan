/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Form Aplikasi (create/edit) — dipisah dari HTML.
 * Dipakai oleh admin/aplikasi/create.blade.php & edit.blade.php
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */

document.addEventListener('DOMContentLoaded', () => {
    // ---------- Preview icon saat dipilih (halaman create) ----------
    const iconInput = document.getElementById('iconInput');
    if (iconInput) {
        iconInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (ev) {
                    const img = document.getElementById('previewImg');
                    const preview = document.getElementById('iconPreview');
                    if (img) img.src = ev.target.result;
                    if (preview) preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // ---------- Auto-convert short_name ke UPPERCASE ----------
    const shortNameInput = document.getElementById('shortNameInput');
    if (shortNameInput) {
        shortNameInput.addEventListener('input', function () {
            const cursorPosition = this.selectionStart;
            const oldValue = this.value;
            this.value = this.value.toUpperCase();
            const offset = this.value.length - oldValue.length;
            this.setSelectionRange(cursorPosition + offset, cursorPosition + offset);
        });
    }

    // ---------- Karakter counter Deskripsi ----------
    const descriptionTextarea = document.getElementById('description');
    const currentCharsSpan = document.getElementById('currentChars');
    const charWarning = document.getElementById('charWarning');
    const charCount = document.getElementById('charCount');

    function updateCharCounter() {
        if (!descriptionTextarea) return;
        const currentLength = descriptionTextarea.value.length;
        const maxLength = 1000;

        if (currentCharsSpan) currentCharsSpan.textContent = currentLength;

        if (!charCount || !charWarning) return;

        if (currentLength >= 1000) {
            charCount.style.color = '#ef4444';
            charWarning.style.display = 'block';
            charWarning.innerHTML = '<strong>Peringatan:</strong> Deskripsi mencapai batas maksimal';
        } else if (currentLength >= 950) {
            charCount.style.color = '#ef4444';
            charWarning.style.display = 'block';
            charWarning.innerHTML = '<strong>Peringatan:</strong> Deskripsi hampir mencapai batas maksimal (' + (maxLength - currentLength) + ' karakter lagi)';
        } else if (currentLength >= 900) {
            charCount.style.color = '#d97706';
            charWarning.style.display = 'block';
            charWarning.innerHTML = '<strong>Peringatan:</strong> Deskripsi hampir mencapai batas maksimal (' + (maxLength - currentLength) + ' karakter lagi)';
        } else if (currentLength >= 800) {
            charCount.style.color = '#f59e0b';
            charWarning.style.display = 'none';
        } else {
            charCount.style.color = 'var(--text-muted)';
            charWarning.style.display = 'none';
        }
    }

    if (descriptionTextarea) {
        updateCharCounter();
        descriptionTextarea.addEventListener('input', updateCharCounter);
    }

    // ---------- Kelola poin fitur ----------
    const featuresContainer = document.getElementById('features-container');
    const featuresWarning = document.getElementById('features-warning');
    const addFeatureBtn = document.getElementById('add-feature-btn');

    function updateDeleteButtons() {
        if (!featuresContainer) return;
        const items = featuresContainer.querySelectorAll('.feature-item');

        items.forEach(item => {
            const deleteBtn = item.querySelector('[data-remove-feature]');
            if (!deleteBtn) return;
            if (items.length <= 2) {
                deleteBtn.disabled = true;
                deleteBtn.style.opacity = '0.4';
                deleteBtn.style.cursor = 'not-allowed';
            } else {
                deleteBtn.disabled = false;
                deleteBtn.style.opacity = '1';
                deleteBtn.style.cursor = 'pointer';
            }
        });

        if (addFeatureBtn) {
            addFeatureBtn.style.display = items.length >= 5 ? 'none' : 'inline-flex';
        }
    }

    function addFeature() {
        if (!featuresContainer) return;
        const currentCount = featuresContainer.querySelectorAll('.feature-item').length;

        if (currentCount >= 5) {
            if (featuresWarning) featuresWarning.style.display = 'block';
            return;
        }
        if (featuresWarning) featuresWarning.style.display = 'none';

        const div = document.createElement('div');
        div.className = 'feature-item';
        div.style.cssText = 'display:flex;gap:0.75rem;margin-bottom:0.75rem';
        div.innerHTML = `
            <input type="text" name="features[]" class="form-control" placeholder="Masukkan poin fitur" required>
            <button type="button" class="btn u-delete-btn" data-remove-feature title="Hapus poin">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    <line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>
                </svg>
            </button>
        `;
        featuresContainer.appendChild(div);
        updateDeleteButtons();
    }

    if (addFeatureBtn) {
        addFeatureBtn.addEventListener('click', addFeature);
    }

    if (featuresContainer) {
        // Delegasi: tombol hapus poin fitur (termasuk yang dibuat dinamis)
        featuresContainer.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-remove-feature]');
            if (!btn) return;

            const currentCount = featuresContainer.querySelectorAll('.feature-item').length;
            if (currentCount <= 2) {
                Toast.warning('Minimal harus ada 2 poin fitur');
                return;
            }
            btn.closest('.feature-item').remove();
            updateDeleteButtons();
        });
    }

    // ---------- Status aplikasi → aktif/nonaktif field URL ----------
    function toggleUrlField(status) {
        const urlField = document.getElementById('urlField');
        if (!urlField) return;
        const urlInput = urlField.querySelector('input');

        if (status === 'development') {
            urlField.style.opacity = '0.5';
            if (urlInput) {
                urlInput.disabled = true;
                urlInput.value = '#';
                urlInput.removeAttribute('required');
            }
        } else {
            urlField.style.opacity = '1';
            if (urlInput) {
                urlInput.disabled = false;
                if (urlInput.value === '#') urlInput.value = '';
            }
        }
    }

    const statusSelect = document.querySelector('select[name="status"]');
    if (statusSelect) {
        toggleUrlField(statusSelect.value);
        statusSelect.addEventListener('change', function () {
            toggleUrlField(this.value);
        });
    }

    // ---------- Kotak centang animasi ----------
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

    const isActiveCheckbox = document.getElementById('isActive');
    if (isActiveCheckbox) {
        updateCheckboxStyle('isActiveBox', 'isActiveCheck', isActiveCheckbox.checked);
        isActiveCheckbox.addEventListener('change', function () {
            updateCheckboxStyle('isActiveBox', 'isActiveCheck', this.checked);
        });
    }

    // ---------- Checkbox visibility (Floating/Footer/Beranda) ----------
    document.querySelectorAll('.vis-checkbox').forEach(function (checkbox) {
        const box = checkbox.parentElement.querySelector('.vis-check-box');
        if (!box) return;

        function updateVisStyle() {
            const svg = box.querySelector('svg');
            if (checkbox.checked) {
                box.style.background = 'linear-gradient(135deg, #14b8a6, #0d9488)';
                box.style.borderColor = '#14b8a6';
                if (svg) {
                    svg.style.opacity = '1';
                    svg.style.transform = 'scale(1)';
                }
            } else {
                box.style.background = '#fff';
                box.style.borderColor = '#cbd5e1';
                if (svg) {
                    svg.style.opacity = '0';
                    svg.style.transform = 'scale(0.5)';
                }
            }
        }

        updateVisStyle();
        checkbox.addEventListener('change', updateVisStyle);
    });

    // Inisialisasi tombol hapus setelah DOM siap
    updateDeleteButtons();
});
