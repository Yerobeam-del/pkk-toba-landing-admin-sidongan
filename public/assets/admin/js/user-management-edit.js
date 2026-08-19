/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Edit User — dipisah dari HTML (user-management/edit.blade.php)
 * Data awal dibaca dari nilai select di halaman.
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


// ==========================================
// CUSTOM CHECKBOX HANDLER
// ==========================================
function initCustomCheckboxes() {
    const checkboxes = document.querySelectorAll('.custom-checkbox-input');

    checkboxes.forEach(checkbox => {
        const label = checkbox.closest('.custom-checkbox-label');
        const box = label.querySelector('.custom-checkbox-box');
        const check = label.querySelector('.custom-checkbox-check');

        // Set initial state (penting untuk edit - checkbox yang sudah checked)
        updateCustomCheckbox(box, check, checkbox.checked);

        // On change
        checkbox.addEventListener('change', function() {
            updateCustomCheckbox(box, check, this.checked);
        });
    });
}

function updateCustomCheckbox(box, check, isChecked) {
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

// ==========================================
// EMAIL GENERATOR
// ==========================================
function updateEmail() {
    const username = document.getElementById('email_username').value.trim();
    const fullEmail = document.getElementById('email_full');
    fullEmail.value = username ? username + '@pkk-toba.id' : '';
}

// ==========================================
// TOGGLE PERMISSION SECTION
// ==========================================
function togglePermissionSection() {
    const roleSelect = document.getElementById('roleSelect');
    const permissionSection = document.getElementById('permissionSection');
    const selectedOption = roleSelect.options[roleSelect.selectedIndex];
    const roleName = selectedOption ? selectedOption.text.toLowerCase() : '';

    if (roleName.includes('anggota')) {
        permissionSection.style.display = 'block';
    } else {
        permissionSection.style.display = 'none';
        // Uncheck semua permission untuk administrator
        const checkboxes = document.querySelectorAll('#permissionSection input[type="checkbox"]');
        checkboxes.forEach(cb => {
            cb.checked = false;
            const label = cb.closest('.custom-checkbox-label');
            const box = label.querySelector('.custom-checkbox-box');
            const check = label.querySelector('.custom-checkbox-check');
            updateCustomCheckbox(box, check, false);
        });
    }
}

// ==========================================
// INITIALIZATION
// ==========================================
document.getElementById('email_username').addEventListener('input', updateEmail);

document.addEventListener('DOMContentLoaded', function() {
    // Init email
    updateEmail();

    // Init permission section
    togglePermissionSection();

    // Init custom checkboxes
    initCustomCheckboxes();

    // SIDONGAN Role Logic
    const checkboxes = document.querySelectorAll('input[name="applications[]"]');
    const sidonganRoleSection = document.getElementById('sidonganRoleSection');
    const sidonganRoleSelect = document.getElementById('sidonganRole');

    function checkSidonganStatus() {
        let isSidonganChecked = false;

        checkboxes.forEach(checkbox => {
            const appShort = (checkbox.dataset.appShort || '').toLowerCase();
            if (appShort === 'sidongan' && checkbox.checked) {
                isSidonganChecked = true;
            }
        });

        if (isSidonganChecked) {
            sidonganRoleSection.style.display = 'block';
            if (sidonganRoleSelect) sidonganRoleSelect.required = true;
        } else {
            sidonganRoleSection.style.display = 'none';
            if (sidonganRoleSelect) {
                sidonganRoleSelect.required = false;
                sidonganRoleSelect.value = '';
            }
        }
    }

    // SIEDA Role Logic
    const siedaRoleSection = document.getElementById('siedaRoleSection');
    const siedaRoleSelect = document.getElementById('siedaRole');

    function checkSiedaStatus() {
        let isSiedaChecked = false;

        checkboxes.forEach(checkbox => {
            const appShort = (checkbox.dataset.appShort || '').toLowerCase();
            if (appShort === 'sieda' && checkbox.checked) {
                isSiedaChecked = true;
            }
        });

        if (isSiedaChecked) {
            siedaRoleSection.style.display = 'block';
        } else {
            siedaRoleSection.style.display = 'none';
            if (siedaRoleSelect) {
                siedaRoleSelect.value = '';
            }
        }
    }

    // SIEDA Wilayah Access Logic
    const siedaWilayahSection = document.getElementById('siedaWilayahSection');
    const siedaKecamatanSelect = document.getElementById('siedaKecamatan');
    const siedaKelurahanSelect = document.getElementById('siedaKelurahan');
    const kecamatanField = document.getElementById('kecamatanField');
    const kelurahanField = document.getElementById('kelurahanField');

    // Load Kecamatan from API
    async function loadKecamatan() {
        try {
            // Kabupaten Toba code is 12.12
            const response = await fetch('/api/v1/wilayah/districts/12.12');
            const result = await response.json();

            if (result.success && result.data) {
                siedaKecamatanSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                result.data.forEach(kec => {
                    const option = document.createElement('option');
                    option.value = kec.code;
                    option.textContent = kec.name;
                    option.dataset.code = kec.code;
                    siedaKecamatanSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading kecamatan:', error);
            siedaKecamatanSelect.innerHTML = '<option value="">Gagal memuat data</option>';
        }
    }

    // Load Kelurahan based on Kecamatan
    async function loadKelurahan(districtCode) {
        if (!districtCode) {
            siedaKelurahanSelect.innerHTML = '<option value="">-- Pilih Desa/Kelurahan --</option>';
            return;
        }

        try {
            const response = await fetch(`/api/v1/wilayah/villages/${districtCode}`);
            const result = await response.json();

            if (result.success && result.data) {
                siedaKelurahanSelect.innerHTML = '<option value="">-- Pilih Desa/Kelurahan --</option>';
                result.data.forEach(kel => {
                    const option = document.createElement('option');
                    option.value = kel.code;
                    option.textContent = kel.name;
                    siedaKelurahanSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading kelurahan:', error);
            siedaKelurahanSelect.innerHTML = '<option value="">Gagal memuat data</option>';
        }
    }

    // Handle SIEDA Role Change
    function handleSiedaRoleChange() {
        const selectedRole = siedaRoleSelect ? siedaRoleSelect.value : '';

        if (selectedRole === 'operator' || selectedRole === 'kader') {
            siedaWilayahSection.style.display = 'block';
            loadKecamatan();

            if (selectedRole === 'operator') {
                // Operator: hanya pilih kecamatan
                kecamatanField.style.display = 'block';
                kelurahanField.style.display = 'none';
                siedaKecamatanSelect.required = true;
                siedaKelurahanSelect.required = false;
                siedaKelurahanSelect.value = '';
            } else if (selectedRole === 'kader') {
                // Kader: pilih kecamatan dan kelurahan
                kecamatanField.style.display = 'block';
                kelurahanField.style.display = 'block';
                siedaKecamatanSelect.required = true;
                siedaKelurahanSelect.required = true;
            }
        } else {
            siedaWilayahSection.style.display = 'none';
            siedaKecamatanSelect.required = false;
            siedaKelurahanSelect.required = false;
            siedaKecamatanSelect.value = '';
            siedaKelurahanSelect.value = '';
        }
    }

    // Event listener for SIEDA role change
    if (siedaRoleSelect) {
        siedaRoleSelect.addEventListener('change', handleSiedaRoleChange);
    }

    // Event listener for Kecamatan change
    if (siedaKecamatanSelect) {
        siedaKecamatanSelect.addEventListener('change', function() {
            if (siedaRoleSelect && siedaRoleSelect.value === 'kader') {
                loadKelurahan(this.value);
            }
        });
    }

    // Call on initial load if editing
    if (siedaRoleSelect && siedaRoleSelect.value) {
        handleSiedaRoleChange();
    }

    // Pre-fill values for edit
    if (siedaRoleSelect && siedaRoleSelect.value) {
        handleSiedaRoleChange();

        // Pre-fill kecamatan
        const savedKecamatan = document.getElementById('siedaKecamatan')?.value || '';
        if (savedKecamatan && siedaKecamatanSelect) {
            setTimeout(() => {
                siedaKecamatanSelect.value = savedKecamatan;

                // If kader, load kelurahan
                if (siedaRoleSelect.value === 'kader') {
                    loadKelurahan(savedKecamatan).then(() => {
                        const savedKelurahan = document.getElementById('siedaKelurahan')?.value || '';
                        if (savedKelurahan && siedaKelurahanSelect) {
                            siedaKelurahanSelect.value = savedKelurahan;
                        }
                    });
                }
            }, 500);
        }
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', checkSiedaStatus);
    });

    // Initial check
    checkSiedaStatus();

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', checkSidonganStatus);
    });

    checkSidonganStatus();
});


// ============================================================
// Dikembangkan oleh Institut Teknologi Del
// CHANGE DELEGATION (menggantikan onchange inline)
// ============================================================
const roleSelectEdit = document.getElementById('roleSelect');
if (roleSelectEdit) {
    roleSelectEdit.addEventListener('change', togglePermissionSection);
}
