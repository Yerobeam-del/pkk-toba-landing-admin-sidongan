/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/admin/user-management/create.blade.php
 * ============================================================ */

// ==========================================
// CUSTOM CHECKBOX HANDLER
// ==========================================
function initCustomCheckboxes() {
    const checkboxes = document.querySelectorAll('.custom-checkbox-input');

    checkboxes.forEach(checkbox => {
        const label = checkbox.closest('.custom-checkbox-label, .permission-item, .app-item');
        if (!label) return;

        const box = label.querySelector('.custom-checkbox-box');
        const check = label.querySelector('.custom-checkbox-check');

        // Set initial state
        updateCustomCheckbox(box, check, checkbox.checked);

        // On change
        checkbox.addEventListener('change', function () {
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
// EMAIL GENERATOR + PREVIEW
// ==========================================
let emailCheckTimeout = null;

function updateEmail() {
    const username = document.getElementById('email_username').value.trim();
    const fullEmail = document.getElementById('email_full');
    const preview = document.getElementById('email_preview');

    const email = username ? username + '@pkk-toba.id' : '';
    fullEmail.value = email;
    preview.textContent = email || 'username@pkk-toba.id';

    // Debounced availability check
    clearTimeout(emailCheckTimeout);
    const statusEl = document.getElementById('emailStatus');
    if (!statusEl) return;

    if (username.length < 3) {
        statusEl.style.display = 'none';
        return;
    }

    // Show checking state
    statusEl.style.display = 'flex';
    statusEl.className = 'email-status email-status--checking';
    statusEl.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> Mengecek...';

    emailCheckTimeout = setTimeout(() => {
        checkEmailAvailability(email);
    }, 500);
}

async function checkEmailAvailability(email) {
    const statusEl = document.getElementById('emailStatus');
    if (!statusEl) return;

    try {
        const response = await fetch(`/admin/user-management/check-email?email=${encodeURIComponent(email)}`);
        const result = await response.json();

        statusEl.style.display = 'flex';

        if (result.available) {
            statusEl.className = 'email-status email-status--available';
            statusEl.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> ' + result.message;
        } else {
            statusEl.className = 'email-status email-status--taken';
            statusEl.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> ' + result.message;
        }
    } catch (error) {
        statusEl.style.display = 'none';
    }
}

// ==========================================
// GENERATE RANDOM PASSWORD
// ==========================================
function generateRandomPassword() {
    const length = 16;
    const uppercase = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    const lowercase = 'abcdefghjkmnpqrstuvwxyz';
    const numbers = '23456789';
    const symbols = '!@#$%^&*()-_=+';

    // Pastikan ada minimal 1 dari setiap tipe
    let password = '';
    password += uppercase[Math.floor(Math.random() * uppercase.length)];
    password += lowercase[Math.floor(Math.random() * lowercase.length)];
    password += numbers[Math.floor(Math.random() * numbers.length)];
    password += symbols[Math.floor(Math.random() * symbols.length)];

    // Isi sisa panjang dengan campuran semua karakter
    const allChars = uppercase + lowercase + numbers + symbols;
    for (let i = password.length; i < length; i++) {
        password += allChars[Math.floor(Math.random() * allChars.length)];
    }

    // Acak urutan karakter
    password = password.split('').sort(() => Math.random() - 0.5).join('');

    // Isi ke field password & confirm
    const passwordInput = document.getElementById('passwordInput');
    const confirmInput = document.getElementById('passwordConfirmInput');
    if (passwordInput) {
        passwordInput.value = password;
        passwordInput.type = 'text';
        // Trigger events
        passwordInput.dispatchEvent(new Event('input'));
    }
    if (confirmInput) {
        confirmInput.value = password;
        confirmInput.type = 'text';
        confirmInput.dispatchEvent(new Event('input'));
    }

    // Copy to clipboard
    navigator.clipboard.writeText(password).then(() => {
        // Tampilkan feedback pada tombol
        const btn = document.querySelector('.generate-password-btn');
        if (btn) {
            const originalHTML = btn.innerHTML;
            btn.classList.add('copied');
            btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Tersalin ke Clipboard!';
            setTimeout(() => {
                btn.classList.remove('copied');
                btn.innerHTML = originalHTML;
            }, 2000);
        }
        Toast.success('Password berhasil dibuat & disalin ke clipboard!');
    }).catch(() => {
        Toast.success('Password berhasil dibuat! Silakan copy manual.');
    });
}

// ==========================================
// PASSWORD VISIBILITY TOGGLE
// ==========================================
function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;

    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';

    // Update icon
    if (isPassword) {
        btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';
    } else {
        btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
    }
}

// ==========================================
// PASSWORD STRENGTH INDICATOR
// ==========================================
function checkPasswordStrength(password) {
    let score = 0;
    if (password.length >= 8) score++;
    if (password.length >= 12) score++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
    if (/\d/.test(password)) score++;
    if (/[^a-zA-Z0-9]/.test(password)) score++;

    if (score <= 2) return 'weak';
    if (score <= 3) return 'medium';
    return 'strong';
}

function updatePasswordStrength() {
    const passwordInput = document.getElementById('passwordInput');
    const strengthEl = document.getElementById('passwordStrength');
    if (!passwordInput || !strengthEl) return;

    const val = passwordInput.value;
    if (val.length === 0) {
        strengthEl.style.display = 'none';
        return;
    }

    strengthEl.style.display = 'block';
    const strength = checkPasswordStrength(val);
    strengthEl.setAttribute('data-strength', strength);
}

// ==========================================
// PASSWORD MATCH CHECKER
// ==========================================
function checkPasswordMatch() {
    const password = document.getElementById('passwordInput');
    const confirm = document.getElementById('passwordConfirmInput');
    const matchEl = document.getElementById('passwordMatch');

    if (!password || !confirm || !matchEl) return;

    if (confirm.value.length === 0) {
        matchEl.style.display = 'none';
        return;
    }

    matchEl.style.display = 'flex';
    if (password.value === confirm.value) {
        matchEl.className = 'password-match match';
        matchEl.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Cocok';
    } else {
        matchEl.className = 'password-match no-match';
        matchEl.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Tidak cocok';
    }
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
        const checkboxes = document.querySelectorAll('#permissionSection input[type="checkbox"]');
        checkboxes.forEach(cb => {
            cb.checked = false;
            const label = cb.closest('.permission-item');
            if (!label) return;
            const box = label.querySelector('.custom-checkbox-box');
            const check = label.querySelector('.custom-checkbox-check');
            updateCustomCheckbox(box, check, false);
        });
    }
}

// ==========================================
// FORM SUBMIT LOADING STATE
// ==========================================
function initFormSubmit() {
    const form = document.getElementById('createUserForm');
    const submitBtn = document.getElementById('submitBtn');
    if (!form || !submitBtn) return;

    form.addEventListener('submit', function () {
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        submitBtn.querySelector('span').textContent = 'Menyimpan...';
    });
}

// ==========================================
// INITIALIZATION
// ==========================================
document.addEventListener('DOMContentLoaded', function () {
    // Init email
    const emailUsername = document.getElementById('email_username');
    if (emailUsername) {
        emailUsername.addEventListener('input', updateEmail);
        updateEmail();
    }

    // Init password features
    const passwordInput = document.getElementById('passwordInput');
    const confirmInput = document.getElementById('passwordConfirmInput');
    if (passwordInput) passwordInput.addEventListener('input', updatePasswordStrength);
    if (confirmInput) confirmInput.addEventListener('input', checkPasswordMatch);

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
            if (siedaRoleSelect) siedaRoleSelect.required = true;
        } else {
            siedaRoleSection.style.display = 'none';
            if (siedaRoleSelect) {
                siedaRoleSelect.required = false;
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
                kecamatanField.style.display = 'block';
                kelurahanField.style.display = 'none';
                siedaKecamatanSelect.required = true;
                siedaKelurahanSelect.required = false;
                siedaKelurahanSelect.value = '';
            } else if (selectedRole === 'kader') {
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
        siedaKecamatanSelect.addEventListener('change', function () {
            if (siedaRoleSelect && siedaRoleSelect.value === 'kader') {
                loadKelurahan(this.value);
            }
        });
    }

    // Call on initial load if editing
    if (siedaRoleSelect && siedaRoleSelect.value) {
        handleSiedaRoleChange();
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

    // Init role select change handler
    const roleSelectCreate = document.getElementById('roleSelect');
    if (roleSelectCreate) {
        roleSelectCreate.addEventListener('change', togglePermissionSection);
    }

    // Init form submit loading
    initFormSubmit();
});

// ============================================================
// Dikembangkan oleh Institut Teknologi Del
// ============================================================
