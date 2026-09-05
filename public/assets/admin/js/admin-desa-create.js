/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/admin/desa/create.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dropdown kecamatan & desa diambil dari endpoint
 * admin.desa.sieda-wilayah — hanya wilayah yang sudah terisi
 * datanya di database SIEDA yang boleh dipilih. Desa yang sudah
 * pernah didaftarkan dinonaktifkan agar tidak dobel.
 * ============================================================ */


const kecamatanSelect = document.getElementById('kecamatanSelect');
const desaSelect = document.getElementById('desaSelect');
const desaNameInput = document.getElementById('desaNameInput');
const desaError = document.getElementById('desaError');
const desaHelp = document.getElementById('desaHelp');

// 1. Load wilayah (kecamatan + desa) yang sudah terisi datanya di SIEDA
async function loadSiedaWilayah() {
    try {
        const res = await fetch('/admin/desa/sieda-wilayah');
        const json = await res.json();

        if (!json.success) throw new Error(json.message || 'Gagal memuat data wilayah SIEDA');
        if (!Array.isArray(json.data)) throw new Error('Format data tidak valid');

        const kecamatanDenganData = json.data.filter(k => (k.desas || []).length > 0);

        if (kecamatanDenganData.length === 0) {
            kecamatanSelect.innerHTML = '<option value="">Belum ada data desa di SIEDA</option>';
            desaSelect.innerHTML = '<option value="">Tidak ada desa yang bisa ditambahkan</option>';
            if (desaHelp) {
                desaHelp.textContent = 'Kecamatan/desa hanya bisa dipilih setelah datanya terisi di aplikasi SIEDA.';
            }
            return;
        }

        kecamatanSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';

        kecamatanDenganData.forEach(k => {
            const opt = document.createElement('option');
            opt.value = k.id ?? '';
            opt.dataset.wilayahCode = k.kode;
            opt.textContent = k.nama;
            kecamatanSelect.appendChild(opt);
        });

        // Simpan daftar desa per kode kecamatan untuk dropdown berantai.
        // Opsi desa yang sudah terdaftar dinonaktifkan (kecuali di form Edit).
        window.siedaDesaCache = {};
        kecamatanDenganData.forEach(k => {
            window.siedaDesaCache[k.kode] = k.desas || [];
        });

    } catch (error) {
        console.error('Error loading wilayah SIEDA:', error);
        kecamatanSelect.innerHTML = '<option value="">Gagal memuat kecamatan</option>';
        if (desaError) {
            desaError.style.display = 'block';
            desaError.textContent = 'Gagal: ' + error.message;
        }
    }
}

// 2. Isi dropdown desa sesuai kecamatan terpilih
function populateDesaOptions(wilayahCode) {
    if (!desaSelect) return;
    desaNameInput.value = '';
    desaError.style.display = 'none';
    desaError.textContent = '';
    desaHelp.style.display = 'block';

    if (!wilayahCode || !window.siedaDesaCache || !window.siedaDesaCache[wilayahCode]) {
        desaSelect.innerHTML = '<option value="">Pilih Kecamatan Terlebih Dahulu</option>';
        desaSelect.disabled = true;
        desaSelect.style.opacity = '0.6';
        desaSelect.style.cursor = 'not-allowed';
        return;
    }

    const desas = window.siedaDesaCache[wilayahCode];
    desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
    desaSelect.disabled = false;
    desaSelect.style.opacity = '1';
    desaSelect.style.cursor = 'pointer';

    let tersedia = 0;
    desas.forEach(d => {
        const opt = document.createElement('option');
        opt.value = d.kode;
        opt.textContent = d.sudah_terdaftar ? `${d.nama} (sudah ditambahkan)` : d.nama;
        opt.dataset.nama = d.nama;
        opt.disabled = !!d.sudah_terdaftar;
        desaSelect.appendChild(opt);
        if (!d.sudah_terdaftar) tersedia++;
    });

    if (tersedia === 0) {
        desaSelect.value = '';
        if (desaHelp) desaHelp.textContent = 'Semua desa di kecamatan ini sudah ditambahkan.';
    } else {
        if (desaHelp) desaHelp.textContent = `${tersedia} desa tersedia (data dari SIEDA)`;
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    await loadSiedaWilayah();

    // Preselect kecamatan bila datang dari filter halaman index
    // (tombol "Tambah Desa" mengirim ?kecamatan=<id>).
    const preselectId = new URLSearchParams(window.location.search).get('kecamatan');
    if (preselectId && kecamatanSelect) {
        const opt = [...kecamatanSelect.options].find(o => o.value === String(preselectId));
        if (opt) {
            kecamatanSelect.value = opt.value;
            populateDesaOptions(opt.dataset.wilayahCode);
        }
    }
});

// 3. Kecamatan berubah → isi ulang dropdown desa
kecamatanSelect.addEventListener('change', function() {
    populateDesaOptions(this.value ? String(this.selectedOptions[0].dataset.wilayahCode) : '');
});

// 4. Update hidden input when desa is selected
desaSelect.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    desaNameInput.value = this.value && !selected.disabled ? selected.getAttribute('data-nama') || selected.textContent : '';
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

// Checkbox animation handler — sadar tema (dark mode pakai warna lain)
function updateCheckboxStyle(boxId, checkId, isChecked) {
    const box = document.getElementById(boxId);
    const check = document.getElementById(checkId);
    const isDark = document.documentElement.classList.contains('dark-mode');

    if (!box || !check) return;

    if (isChecked) {
        box.style.background = 'linear-gradient(135deg, #14b8a6, #0d9488)';
        box.style.borderColor = '#14b8a6';
        box.style.boxShadow = '0 2px 8px rgba(20,184,166,0.3)';
        check.style.opacity = '1';
        check.style.transform = 'scale(1)';
    } else {
        box.style.background = isDark ? '#1e293b' : '#fff';
        box.style.borderColor = isDark ? '#475569' : '#cbd5e1';
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

    // Saat tema berubah (toggle dark mode manual), segarkan ulang warna kotak
    new MutationObserver(() => {
        updateCheckboxStyle('isActiveBox', 'isActiveCheck', isActiveCheckbox.checked);
    }).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
}


