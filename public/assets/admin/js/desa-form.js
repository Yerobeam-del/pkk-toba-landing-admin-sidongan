/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Form Desa (create/edit) — dipisah dari HTML.
 * Data awal dibaca dari data-* pada #desaForm.
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


const form = document.getElementById('desaForm');
const currentKecId = form ? form.dataset.currentKec : null;
const currentDesaCode = form ? form.dataset.currentDesa : null;
const kecamatanSelect = document.getElementById('kecamatanSelect');
const desaSelect = document.getElementById('desaSelect');
const desaNameInput = document.getElementById('desaNameInput');
const desaError = document.getElementById('desaError');
const desaHelp = document.getElementById('desaHelp');

async function loadDesaByKecamatan(kecCode, preselectCode = null) {
    desaSelect.innerHTML = '<option value="">Memuat...</option>';
    desaSelect.disabled = true;
    desaSelect.style.opacity = '0.6';
    desaSelect.style.cursor = 'not-allowed';
    desaError.style.display = 'none';
    
    if (!kecCode) {
        desaSelect.innerHTML = '<option value="">Pilih Kecamatan Terlebih Dahulu</option>';
        return;
    }

    try {
        const proxyUrl = `/api/v1/wilayah/proxy/desa/${kecCode}`;
        const res = await fetch(proxyUrl);
        const json = await res.json();
        
        if (!json.success) {
            throw new Error(json.message || 'Gagal memuat data desa');
        }
        
        if (!Array.isArray(json.data)) {
            throw new Error('Format data tidak valid');
        }
        
        desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
        desaSelect.disabled = false;
        desaSelect.style.opacity = '1';
        desaSelect.style.cursor = 'pointer';
        
        if (json.data.length === 0) {
            desaSelect.innerHTML = '<option value="">Tidak ada desa di kecamatan ini</option>';
            desaHelp.style.display = 'none';
            return;
        }
        
        json.data.forEach(d => {
            const opt = document.createElement('option');
            opt.value = d.code;
            opt.textContent = d.name;
            if (preselectCode && d.code === preselectCode) {
                opt.selected = true;
            }
            desaSelect.appendChild(opt);
        });
        
        if (preselectCode) {
            const selectedOpt = desaSelect.querySelector(`option[value="${preselectCode}"]`);
            if (selectedOpt) {
                desaNameInput.value = selectedOpt.text;
                desaHelp.textContent = `${json.data.length} desa ditemukan`;
            }
        } else {
            desaHelp.textContent = `${json.data.length} desa tersedia`;
        }
        
    } catch (e) {
        console.error('Error loading desa:', e);
        desaSelect.innerHTML = '<option value="">Gagal memuat desa</option>';
        desaSelect.disabled = true;
        desaSelect.style.opacity = '0.6';
        desaError.style.display = 'block';
        desaError.textContent = 'Gagal: ' + e.message;
        desaHelp.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    try {
        const kecRes = await fetch('/api/v1/kecamatans');
        const kecJson = await kecRes.json();
        
        if (!kecJson.success) throw new Error(kecJson.message);
        
        kecamatanSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        let foundKecCode = null;
        
        kecJson.data.forEach(k => {
            const opt = document.createElement('option');
            opt.value = k.id;
            opt.dataset.wilayahCode = k.code;
            opt.textContent = k.name;
            
            if (k.id == currentKecId) {
                opt.selected = true;
                foundKecCode = k.code;
            }
            
            kecamatanSelect.appendChild(opt);
        });
        
        if (foundKecCode) {
            await loadDesaByKecamatan(foundKecCode, currentDesaCode);
        }
        
    } catch (error) {
        console.error('Error initializing form:', error);
        kecamatanSelect.innerHTML = '<option value="">Gagal memuat kecamatan</option>';
    }
});

kecamatanSelect.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const wilayahCode = selectedOption.dataset.wilayahCode;
    loadDesaByKecamatan(wilayahCode);
});

desaSelect.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    desaNameInput.value = this.value ? selected.textContent : '';
});

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
    
    isActiveCheckbox.addEventListener('change', function() {
        updateCheckboxStyle('isActiveBox', 'isActiveCheck', this.checked);
    });
}

