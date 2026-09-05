/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * admin-shared.js — Fungsi-fungsi JS bersama untuk seluruh Admin Panel
 * Dipakai oleh: user-management, profile, semua form
 * ============================================================ */

// ==========================================
// CUSTOM CHECKBOX HANDLER
// ==========================================
function initCustomCheckboxes(root = document) {
    const checkboxes = root.querySelectorAll('.custom-checkbox-input');
    checkboxes.forEach(checkbox => {
        const label = checkbox.closest('.custom-checkbox-label, .permission-item, .app-item');
        if (!label) return;
        const box = label.querySelector('.custom-checkbox-box');
        const check = label.querySelector('.custom-checkbox-check');
        updateCustomCheckbox(box, check, checkbox.checked);
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
// PASSWORD VISIBILITY TOGGLE
// ==========================================
function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    if (isPassword) {
        btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';
    } else {
        btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
    }
}

// ==========================================
// PASSWORD STRENGTH
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
    if (val.length === 0) { strengthEl.style.display = 'none'; return; }
    strengthEl.style.display = 'block';
    strengthEl.setAttribute('data-strength', checkPasswordStrength(val));
}

// ==========================================
// PASSWORD MATCH CHECKER
// ==========================================
function checkPasswordMatch() {
    const password = document.getElementById('passwordInput');
    const confirm = document.getElementById('passwordConfirmInput');
    const matchEl = document.getElementById('passwordMatch');
    if (!password || !confirm || !matchEl) return;
    if (confirm.value.length === 0) { matchEl.style.display = 'none'; return; }
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
// EMAIL AVAILABILITY CHECK (debounced)
// ==========================================
let emailCheckTimeout = null;

function updateEmailWithCheck() {
    const username = document.getElementById('email_username');
    const fullEmail = document.getElementById('email_full');
    const preview = document.getElementById('email_preview');
    const statusEl = document.getElementById('emailStatus');
    if (!username) return;

    const val = username.value.trim();
    const email = val ? val + '@pkk-toba.id' : '';
    if (fullEmail) fullEmail.value = email;
    if (preview) preview.textContent = email || 'username@pkk-toba.id';

    clearTimeout(emailCheckTimeout);
    if (!statusEl || val.length < 3) {
        if (statusEl) statusEl.style.display = 'none';
        return;
    }

    statusEl.style.display = 'flex';
    statusEl.className = 'email-status email-status--checking';
    statusEl.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> Mengecek...';

    emailCheckTimeout = setTimeout(() => {
        const excludeUserId = statusEl.dataset.excludeUserId || '';
        checkEmailAvailability(email, excludeUserId);
    }, 500);
}

async function checkEmailAvailability(email, excludeUserId) {
    const statusEl = document.getElementById('emailStatus');
    if (!statusEl) return;
    try {
        let url = `/admin/user-management/check-email?email=${encodeURIComponent(email)}`;
        if (excludeUserId) url += `&exclude_user_id=${excludeUserId}`;
        const response = await fetch(url);
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
// FORM SUBMIT LOADING STATE (auto for all POST forms)
// ==========================================
function initFormLoading() {
    document.querySelectorAll('form[method="POST"], form[method="PUT"], form[method="PATCH"], form[method="DELETE"]').forEach(form => {
        // Skip GET forms, logout, and forms that opt-out
        if (form.method === 'GET' || form.hasAttribute('data-no-loading')) return;

        form.addEventListener('submit', function (e) {
            // Get the submit button that was clicked (or the last submit)
            const btn = e.submitter || this.querySelector('[type="submit"]');
            if (!btn || btn.classList.contains('loading')) return;

            btn.classList.add('loading');
            btn.disabled = true;
            const span = btn.querySelector('span');
            if (span) span.textContent = 'Menyimpan...';
        });
    });
}

// ==========================================
// PER-PAGE DROPDOWN: SUBMIT OTOMATIS
// Dropdown "Tampilkan:" di halaman daftar (berita, sk, template, struktur,
// user-management, sieda-data) tidak punya tombol submit — tanpa ini
// pilihan per_page tidak pernah diterapkan sampai user menekan Enter di search.
// ==========================================
function initPerPageAutoSubmit() {
    document.querySelectorAll('select[name="per_page"]').forEach(function (select) {
        select.addEventListener('change', function () {
            if (select.form) select.form.submit();
        });
    });
}

// ==========================================
// INIT (jalankan di DOMContentLoaded)
// ==========================================
document.addEventListener('DOMContentLoaded', function () {
    initCustomCheckboxes();
    initFormLoading();
    initPerPageAutoSubmit();

    // Email check
    const emailUsername = document.getElementById('email_username');
    if (emailUsername) {
        emailUsername.addEventListener('input', updateEmailWithCheck);
        updateEmailWithCheck();
    }

    // Password features
    const passwordInput = document.getElementById('passwordInput');
    const confirmInput = document.getElementById('passwordConfirmInput');
    if (passwordInput) passwordInput.addEventListener('input', updatePasswordStrength);
    if (confirmInput) confirmInput.addEventListener('input', checkPasswordMatch);
});

// ============================================================
// Dikembangkan oleh Institut Teknologi Del
// ============================================================
