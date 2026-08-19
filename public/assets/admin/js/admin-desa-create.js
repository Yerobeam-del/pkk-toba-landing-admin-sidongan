/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/admin/desa/create.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


const kecamatanSelect = document.getElementById('kecamatanSelect');
const desaSelect = document.getElementById('desaSelect');
const desaNameInput = document.getElementById('desaNameInput');
const desaError = document.getElementById('desaError');
const desaHelp = document.getElementById('desaHelp');
const sortOrderInput = document.getElementById('sortOrderInput');

// 1. Load Max Sort Order
async function loadMaxSortOrder() {
    try {
        const res = await fetch('/api/v1/desas/max-sort-order');
        const json = await res.json();
        
        if (json.success) {
            const maxOrder = json.data?.max_sort_order ?? 0;
            sortOrderInput.value = maxOrder + 1;
        } else {
            sortOrderInput.value = 1;
        }
    } catch (error) {
        console.error('Error loading max sort order:', error);
        sortOrderInput.value = 1;
    }
}

// 2. Load Kecamatan from our API
document.addEventListener('DOMContentLoaded', async () => {
    await loadMaxSortOrder();
    
    try {
        const res = await fetch('/api/v1/kecamatans');
        const json = await res.json();
        
        if (!json.success) throw new Error(json.message || 'Gagal memuat kecamatan');
        if (!json.data || json.data.length === 0) throw new Error('Data kecamatan kosong');
        
        kecamatanSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        
        json.data.forEach(k => {
            const opt = document.createElement('option');
            opt.value = k.id;
            opt.dataset.wilayahCode = k.code;
            opt.textContent = k.name;
            kecamatanSelect.appendChild(opt);
        });
        
    } catch (error) {
        console.error('Error loading kecamatan:', error);
        kecamatanSelect.innerHTML = '<option value="">Gagal memuat kecamatan</option>';
    }
});

// 3. Load Desa when Kecamatan changes
kecamatanSelect.addEventListener('change', async function() {
    const selectedOption = this.options[this.selectedIndex];
    const wilayahCode = selectedOption.dataset.wilayahCode;
    const kecName = selectedOption.textContent;
    
    desaSelect.innerHTML = '<option value="">Memuat data desa...</option>';
    desaSelect.disabled = true;
    desaSelect.style.opacity = '0.6';
    desaSelect.style.cursor = 'not-allowed';
    desaNameInput.value = '';
    desaError.style.display = 'none';
    desaError.textContent = '';
    desaHelp.style.display = 'block';
    desaHelp.textContent = 'Data desa otomatis dari API wilayah.id';
    
    if (!wilayahCode) {
        desaSelect.innerHTML = '<option value="">Pilih Kecamatan Terlebih Dahulu</option>';
        return;
    }
    
    try {
        const proxyUrl = `/api/v1/wilayah/proxy/desa/${wilayahCode}`;
        const res = await fetch(proxyUrl);
        const json = await res.json();
        
        if (!json.success) {
            throw new Error(json.message || 'Gagal mengambil data desa');
        }
        
        if (!Array.isArray(json.data)) {
            throw new Error('Format data tidak valid');
        }
        
        if (json.data.length === 0) {
            desaSelect.innerHTML = '<option value="">Tidak ada desa di kecamatan ini</option>';
            desaHelp.style.display = 'none';
            return;
        }
        
        desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
        desaSelect.disabled = false;
        desaSelect.style.opacity = '1';
        desaSelect.style.cursor = 'pointer';
        
        json.data.forEach(d => {
            const opt = document.createElement('option');
            opt.value = d.code;
            opt.textContent = d.name;
            desaSelect.appendChild(opt);
        });
        
        desaHelp.textContent = `${json.data.length} desa ditemukan di ${kecName}`;
        
    } catch (error) {
        console.error('Error loading desa:', error);
        desaSelect.innerHTML = '<option value="">Gagal memuat desa</option>';
        desaSelect.disabled = true;
        desaSelect.style.opacity = '0.6';
        
        desaError.style.display = 'block';
        desaError.textContent = 'Gagal: ' + error.message;
        desaHelp.style.display = 'none';
    }
});

// 4. Update hidden input when desa is selected
desaSelect.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    desaNameInput.value = this.value ? selected.textContent : '';
});

// 5. Image preview
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
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

// Checkbox animation handler
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
const isActiveCheckbox = document.getElementById('isActive');
if (isActiveCheckbox) {
    updateCheckboxStyle('isActiveBox', 'isActiveCheck', isActiveCheckbox.checked);
    
    isActiveCheckbox.addEventListener('change', function() {
        updateCheckboxStyle('isActiveBox', 'isActiveCheck', this.checked);
    });
}


