/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Lapor Kegiatan (Buat) — upload multi-file, dropdown wilayah
 * bertingkat, dan umpan balik durasi kegiatan.
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */

    // Multi-File Upload Logic
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const filePreview = document.getElementById('filePreview');
    const fileListContainer = document.getElementById('fileListContainer');
    const fileCounter = document.getElementById('fileCounter');
    const counterText = document.getElementById('counterText');
    
    const MAX_FILES = 10;
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    
    let selectedFiles = [];
    
    // Click dropzone to open file picker
    if (dropZone && fileInput) {
        dropZone.addEventListener('click', (e) => {
            if (e.target.closest('#filePreview button')) return;
            fileInput.click();
        });
        
        // Drag events
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#0891b2';
            dropZone.style.background = '#f0f9ff';
        });
        
        dropZone.addEventListener('dragleave', () => {
            dropZone.style.borderColor = '#e2e8f0';
            dropZone.style.background = '#f8fafc';
        });
        
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#e2e8f0';
            dropZone.style.background = '#f8fafc';
            
            if (e.dataTransfer.files.length > 0) {
                handleFiles(e.dataTransfer.files);
            }
        });
        
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFiles(e.target.files);
            }
        });
    }
    
    function handleFiles(newFiles) {
        const remainingSlots = MAX_FILES - selectedFiles.length;
        
        if (newFiles.length > remainingSlots) {
            Toast.error(`Maksimal hanya ${MAX_FILES} foto. Anda sudah memilih ${selectedFiles.length} foto.`);
            return;
        }
        
        Array.from(newFiles).forEach(file => {
            // Validate file
            const validation = validateFile(file);
            if (!validation.valid) {
                Toast.error(validation.message);
                return;
            }
            
            selectedFiles.push(file);
        });
        
        updateFileDisplay();
    }
    
    function validateFile(file) {
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/heic'];
        const allowedExtensions = ['.jpg', '.jpeg', '.png', '.heic'];
        
        // Check file size
        if (file.size > MAX_FILE_SIZE) {
            const sizeInMB = (file.size / 1024 / 1024).toFixed(2);
            return {
                valid: false,
                message: `Ukuran file "${file.name}" terlalu besar (${sizeInMB}MB). Maksimal 5MB.`
            };
        }
        
        // Check file type
        if (!allowedTypes.includes(file.type)) {
            return {
                valid: false,
                message: `Format file "${file.name}" tidak diizinkan. Hanya JPG, PNG, dan HEIC yang diperbolehkan.`
            };
        }
        
        // Check extension
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
        if (!allowedExtensions.includes(fileExtension)) {
            return {
                valid: false,
                message: `Ekstensi file "${fileExtension}" tidak diizinkan.`
            };
        }
        
        return { valid: true, message: '' };
    }
    
    function updateFileDisplay() {
        if (selectedFiles.length === 0) {
            uploadPlaceholder.style.display = 'block';
            filePreview.style.display = 'none';
            fileCounter.style.display = 'none';
            return;
        }
        
        uploadPlaceholder.style.display = 'none';
        filePreview.style.display = 'block';
        fileCounter.style.display = 'block';
        
        // Update counter
        counterText.textContent = `${selectedFiles.length} dari ${MAX_FILES} foto dipilih`;
        
        // Sembunyikan tombol "Tambah Foto" jika sudah 10
        const addMoreBtn = document.getElementById('addMoreBtn');
        if (addMoreBtn) {
            if (selectedFiles.length >= MAX_FILES) {
                addMoreBtn.style.display = 'none';
            } else {
                addMoreBtn.style.display = 'inline-flex';
            }
        }
        
        // Render file list
        renderFileList();
        
        // Update file input
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        fileInput.files = dataTransfer.files;
    }
    
    function renderFileList() {
        fileListContainer.innerHTML = '';
        
        selectedFiles.forEach((file, index) => {
            const fileItem = createFileItem(file, index);
            fileListContainer.appendChild(fileItem);
        });
    }
    
    function createFileItem(file, index) {
        const div = document.createElement('div');
        div.style.cssText = 'background: #f0fdf4; border: 2px solid #10b981; border-radius: 0.5rem; padding: 1rem; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 1rem;';
        
        const fileSize = (file.size / 1024).toFixed(2);
        const sizeText = fileSize >= 1024 ? `${(fileSize / 1024).toFixed(2)} MB` : `${fileSize} KB`;
        
        div.innerHTML = `
            <div style="width: 48px; height: 48px; background: white; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-file-image" style="color: #10b981; font-size: 1.5rem;"></i>
            </div>
            <div class="u-a87">
                <p style="font-size: 0.9rem; font-weight: 600; color: #0f172a; margin: 0 0 0.25rem 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    ${index + 1}. ${file.name}
                </p>
                <p class="u-text-xs-muted-flat">${sizeText}</p>
            </div>
            <div class="u-shrink-0">
                <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.375rem 0.875rem; background: #10b981; color: white; border-radius: 9999px; font-size: 0.8rem; font-weight: 600;">
                    <i class="fas fa-check" style="font-size: 0.7rem;"></i>
                    Siap
                </span>
            </div>
            <button type="button" data-remove-index="${index}" style="flex-shrink: 0; width: 2rem; height: 2rem; background: #fee2e2; color: #ef4444; border: none; border-radius: 0.375rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center;" >
                <i class="fas fa-times u-text-sm"></i>
            </button>
        `;
        
        return div;
    }
    
    function removeFile(index) {
        selectedFiles.splice(index, 1);
        updateFileDisplay();
    }
    
    function changeFiles() {
        // Reset semua file dan pilih file baru
        selectedFiles = [];
        fileInput.value = '';
        updateFileDisplay();
        setTimeout(() => {
            fileInput.click();
        }, 150);
    }

    function addMoreFiles() {
        // Buka file picker tanpa menghapus file yang sudah ada
        fileInput.click();
    }

    // ==========================================
    // WILAYAH DROPDOWN LOGIC
    // ==========================================
    
    const provinsiSelect = document.getElementById('provinsiSelect');
    const kabupatenSelect = document.getElementById('kabupatenSelect');
    const kecamatanSelect = document.getElementById('kecamatanSelect');
    const kelurahanSelect = document.getElementById('kelurahanSelect');
    
    // Load Provinsi saat halaman dimuat
    async function loadProvinsi() {
        try {
            const response = await fetch('/api/v1/wilayah/provinces');
            const result = await response.json();
            
            if (result.success && result.data) {
                provinsiSelect.innerHTML = '<option value="">Pilih Provinsi</option>';
                result.data.forEach(prov => {
                    const option = document.createElement('option');
                        option.value = prov.name;
                        option.textContent = prov.name;
                        option.dataset.code = prov.code;
                    provinsiSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading provinsi:', error);
            provinsiSelect.innerHTML = '<option value="">Gagal memuat data</option>';
        }
    }
    
    // Load Kabupaten saat Provinsi dipilih
    async function loadKabupaten(provinceCode) {
        if (!provinceCode) {
            kabupatenSelect.innerHTML = '<option value="">Pilih provinsi terlebih dahulu</option>';
            return;
        }
        
        kabupatenSelect.innerHTML = '<option value="">Memuat data...</option>';
        
        try {
            const response = await fetch(`/api/v1/wilayah/regencies/${provinceCode}`);
            const result = await response.json();
            
            if (result.success && result.data) {
                kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
                result.data.forEach(kab => {
                    const option = document.createElement('option');
                        option.value = kab.name;
                        option.textContent = kab.name;
                        option.dataset.code = kab.code;
                    kabupatenSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading kabupaten:', error);
            kabupatenSelect.innerHTML = '<option value="">Gagal memuat data</option>';
        }
    }
    
    // Load Kecamatan saat Kabupaten dipilih
    async function loadKecamatan(regencyCode) {
        if (!regencyCode) {
            kecamatanSelect.innerHTML = '<option value="">Pilih kabupaten/kota terlebih dahulu</option>';
            return;
        }
        
        kecamatanSelect.innerHTML = '<option value="">Memuat data...</option>';
        
        try {
            const response = await fetch(`/api/v1/wilayah/districts/${regencyCode}`);
            const result = await response.json();
            
            if (result.success && result.data) {
                kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                result.data.forEach(kec => {
                    const option = document.createElement('option');
                        option.value = kec.name;
                        option.textContent = kec.name;
                        option.dataset.code = kec.code;
                    kecamatanSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading kecamatan:', error);
            kecamatanSelect.innerHTML = '<option value="">Gagal memuat data</option>';
        }
    }
    
    // Load Kelurahan saat Kecamatan dipilih
    async function loadKelurahan(districtCode) {
        if (!districtCode) {
            kelurahanSelect.innerHTML = '<option value="">Pilih kecamatan terlebih dahulu</option>';
            return;
        }
        
        kelurahanSelect.innerHTML = '<option value="">Memuat data...</option>';
        
        try {
            const response = await fetch(`/api/v1/wilayah/villages/${districtCode}`);
            const result = await response.json();
            
            if (result.success && result.data) {
                kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
                result.data.forEach(kel => {
                    const option = document.createElement('option');
                        option.value = kel.name;         
                        option.textContent = kel.name;
                        option.dataset.code = kel.code;  
                    kelurahanSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading kelurahan:', error);
            kelurahanSelect.innerHTML = '<option value="">Gagal memuat data</option>';
        }
    }
    
    // Event Listeners
    if (provinsiSelect) {
        provinsiSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const code = selectedOption ? selectedOption.dataset.code : '';
            loadKabupaten(code); // Kirim KODE, bukan nama
            // Reset kecamatan dan kelurahan
            kecamatanSelect.innerHTML = '<option value="">Pilih kabupaten/kota terlebih dahulu</option>';
            kelurahanSelect.innerHTML = '<option value="">Pilih kecamatan terlebih dahulu</option>';
        });
    }
    
    if (kabupatenSelect) {
        kabupatenSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const code = selectedOption ? selectedOption.dataset.code : '';
            loadKecamatan(code); // Kirim KODE, bukan nama
            // Reset kelurahan
            kelurahanSelect.innerHTML = '<option value="">Pilih kecamatan terlebih dahulu</option>';
        });
    }
    
    if (kecamatanSelect) {
        kecamatanSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const code = selectedOption ? selectedOption.dataset.code : '';
            loadKelurahan(code); // Kirim KODE, bukan nama
        });
    }
    
    // ==========================================
    // PRA-PILIH WILAYAH TERSIMPAN (revisi setelah ditolak)
    // ==========================================
    // Dropdown wilayah diisi bertingkat lewat API, jadi nilai laporan sebelumnya
    // hanya bisa dipilih setelah tiap tingkat selesai dimuat. Rantai await di
    // bawah menjaga urutannya: provinsi -> kabupaten -> kecamatan -> kelurahan.
    // Tanpa laporan sebelumnya, blok ini hanya memuat provinsi lalu berhenti.
    (async function praPilihWilayah() {
        const formWilayah = document.getElementById('laporanForm');
        const tersimpan = JSON.parse(
            (formWilayah && formWilayah.getAttribute('data-wilayah-tersimpan')) || '{}'
        );

        // Pilih nilai pada <select>, lalu kembalikan kode wilayahnya untuk tingkat berikutnya
        const pilih = (select, nilai) => {
            if (!select || !nilai) return null;
            select.value = nilai;
            const opsi = select.options[select.selectedIndex];
            return opsi ? opsi.dataset.code : null;
        };

        await loadProvinsi();
        const kodeProv = pilih(provinsiSelect, tersimpan.provinsi);
        if (!kodeProv) return;

        await loadKabupaten(kodeProv);
        const kodeKab = pilih(kabupatenSelect, tersimpan.kabupaten);
        if (!kodeKab) return;

        await loadKecamatan(kodeKab);
        const kodeKec = pilih(kecamatanSelect, tersimpan.kecamatan);
        if (!kodeKec) return;

        await loadKelurahan(kodeKec);
        pilih(kelurahanSelect, tersimpan.kelurahan);
    })();

    // ==========================================
    // WAKTU KEGIATAN: umpan balik durasi & urutan jam
    // ==========================================
    // Validasi server (end_time after:start_time) sudah ada, tapi baru terlihat
    // setelah form dikirim — padahal di halaman ini pengguna sudah mengunggah foto
    // lebih dulu. Umpan balik di bawah muncul begitu kedua jam terisi.
    (function () {
        const jamMulai = document.getElementById('startTime');
        const jamSelesai = document.getElementById('endTime');
        const info = document.getElementById('durasiKegiatan');
        if (!jamMulai || !jamSelesai || !info) return;

        const keMenit = (nilai) => {
            const [j, m] = (nilai || '').split(':').map(Number);
            return Number.isFinite(j) && Number.isFinite(m) ? j * 60 + m : null;
        };

        const tampilkan = (teks, jenis) => {
            const gaya = {
                baik:      { background: '#f0fdfa', color: '#0f766e', border: '1px solid #99f6e4' },
                peringatan:{ background: '#fef2f2', color: '#b91c1c', border: '1px solid #fecaca' },
            }[jenis];
            info.textContent = teks;
            info.style.display = 'block';
            info.style.background = gaya.background;
            info.style.color = gaya.color;
            info.style.border = gaya.border;
        };

        const perbarui = () => {
            const mulai = keMenit(jamMulai.value);
            const selesai = keMenit(jamSelesai.value);

            // Batasi pilihan jam selesai lewat atribut min (didukung peramban modern)
            jamSelesai.min = jamMulai.value || '';

            if (mulai === null || selesai === null) {
                info.style.display = 'none';
                jamSelesai.setCustomValidity('');
                return;
            }

            if (selesai <= mulai) {
                tampilkan('Jam selesai harus lebih besar dari jam mulai.', 'peringatan');
                // Cegah pengiriman form sebelum diperbaiki
                jamSelesai.setCustomValidity('Jam selesai harus lebih besar dari jam mulai.');
                return;
            }

            jamSelesai.setCustomValidity('');
            const total = selesai - mulai;
            const jam = Math.floor(total / 60);
            const menit = total % 60;
            const bagian = [];
            if (jam) bagian.push(jam + ' jam');
            if (menit) bagian.push(menit + ' menit');
            tampilkan('Durasi kegiatan: ' + bagian.join(' ') + '.', 'baik');
        };

        jamMulai.addEventListener('input', perbarui);
        jamSelesai.addEventListener('input', perbarui);
        perbarui(); // jalankan sekali untuk nilai hasil old() setelah validasi gagal
    })();

// ==========================================
// DELEGATION (menggantikan onclick inline)
// ==========================================
document.addEventListener('click', function (event) {
    // Tombol "Tambah Foto"
    const addBtn = event.target.closest('[data-action="add-more"]');
    if (addBtn) {
        addMoreFiles();
        return;
    }
    // Tombol "Ganti File"
    const changeBtn = event.target.closest('[data-action="change-files"]');
    if (changeBtn) {
        changeFiles();
        return;
    }
    // Tombol hapus satu file (dari daftar)
    const removeBtn = event.target.closest('[data-remove-index]');
    if (removeBtn) {
        removeFile(parseInt(removeBtn.getAttribute('data-remove-index'), 10));
    }
});

// ==========================================
// FOCUS RING (menggantikan onfocus/onblur inline)
// ==========================================
document.addEventListener('focusin', function (e) {
    const t = e.target;
    if (t && t.matches && t.matches('input[name="kegiatan_nama"], input[name="kegiatan_tanggal"], #startTime, #endTime, select[id$="Select"], textarea[name="alamat_lengkap"], textarea[name="deskripsi"]')) {
        t.classList.add('u-field-focused');
    }
});
document.addEventListener('focusout', function (e) {
    const t = e.target;
    if (t && t.matches && t.matches('input[name="kegiatan_nama"], input[name="kegiatan_tanggal"], #startTime, #endTime, select[id$="Select"], textarea[name="alamat_lengkap"], textarea[name="deskripsi"]')) {
        t.classList.remove('u-field-focused');
    }
});