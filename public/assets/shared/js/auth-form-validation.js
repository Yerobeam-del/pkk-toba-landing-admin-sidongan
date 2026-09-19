/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Validasi form realtime untuk halaman auth (register, reset
 * password). Opt-in: pasang atribut data-validate pada <form>.
 *
 * Aturan yang dijalankan:
 *   1. Format email — semua input[type=email] yang terlihat.
 *   2. Format telepon — semua input[type=tel]: digit, spasi, +, -, ( )
 *      saja; minimal 9 digit setelah normalisasi.
 *   3. Kekuatan password — input ber-atribut data-password-strength
 *      minimal mencapai level "cukup" (skor >= 2, panjang >= 8).
 *   4. Kecocokan konfirmasi — input ber-atribut data-confirm-password
 *      harus sama dengan input sumber (data-password-source atau
 *      input bernama "password" pada form yang sama).
 *
 * Submit diblokir selama masih ada pelanggaran; pesan error tampil
 * inline di bawah field dan hilang sendiri saat diperbaiki.
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
(function () {
    'use strict';

    var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

    // Telepon: digit, spasi, +, -, (, ) saja — validasi karakter dan
    // panjang dilakukan terpisah agar pesan errornya spesifik.
    var PHONE_CHARS_RE = /^[0-9+\-() ]+$/;

    // Skor kekuatan — selaras dengan checkPasswordStrength() di
    // auth-reset-password.js (panjang + variasi karakter).
    function strengthScore(password) {
        var score = 0;
        if (password.length >= 8) score++;
        if (password.length >= 12) score++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
        if (/\d/.test(password)) score++;
        if (/[^a-zA-Z0-9]/.test(password)) score++;
        return Math.min(score, 4);
    }

    function setError(input, message) {
        var field = input.closest('.ob-field') || input.parentElement;
        if (!field) return;

        var existing = field.querySelector('.ob-field-error');
        if (message) {
            input.classList.add('ob-input--error');
            input.setAttribute('aria-invalid', 'true');
            if (!existing) {
                existing = document.createElement('div');
                existing.className = 'ob-field-error';
                existing.setAttribute('role', 'alert');
                field.appendChild(existing);
            }
            // Ikon peringatan SVG, bukan karakter unicode
            existing.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;flex:none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg> ' + message;
        } else {
            input.classList.remove('ob-input--error');
            input.removeAttribute('aria-invalid');
            if (existing) existing.remove();
        }
    }

    // ---- Validator per aturan ----------------------------------------

    function validateEmailInput(input, silent) {
        var value = input.value.trim();
        if (value === '' || input.disabled || input.readOnly || input.type !== 'email') {
            if (!silent) setError(input, '');
            return true;
        }
        if (!EMAIL_RE.test(value)) {
            if (!silent) setError(input, 'Format email tidak valid — contoh: nama@pkk-toba.id');
            return false;
        }
        setError(input, '');
        return true;
    }

    function validateStrengthInput(input, silent) {
        var value = input.value;
        if (value === '') {
            if (!silent) setError(input, '');
            return true;
        }
        if (value.length < 8) {
            if (!silent) setError(input, 'Password minimal 8 karakter');
            return false;
        }
        if (strengthScore(value) < 2) {
            if (!silent) setError(input, 'Password terlalu lemah — campurkan huruf besar/kecil atau angka');
            return false;
        }
        setError(input, '');
        return true;
    }

    function validateConfirmInput(input, silent) {
        var form = input.form || input.closest('form');
        var sourceSel = input.getAttribute('data-confirm-password');
        var source = (sourceSel && form.querySelector(sourceSel)) ||
                     (form && form.querySelector('[data-password-source]')) ||
                     (form && form.querySelector('input[name="password"]'));
        if (!source || input.value === '') {
            // Kosong → biarkan validasi required native; bersihkan error lama
            if (!silent) setError(input, '');
            return true;
        }
        if (input.value !== source.value) {
            if (!silent) setError(input, 'Konfirmasi password belum cocok');
            return false;
        }
        setError(input, '');
        return true;
    }

    // ---- Pemasangan per form -----------------------------------------

    function digitCount(value) {
        return (value.match(/\d/g) || []).length;
    }

    function validateTelInput(input, silent) {
        var value = input.value.trim();
        if (value === '' || input.disabled || input.readOnly || input.type !== 'tel') {
            if (!silent) setError(input, '');
            return true;
        }
        if (!PHONE_CHARS_RE.test(value)) {
            if (!silent) setError(input, 'Nomor hanya boleh berisi angka, spasi, +, -, dan tanda kurung');
            return false;
        }
        if (digitCount(value) < 9) {
            if (!silent) setError(input, 'Nomor telepon minimal 9 digit — contoh: 81234567890');
            return false;
        }
        setError(input, '');
        return true;
    }

    function wireForm(form) {
        var emailInputs = Array.prototype.filter.call(
            form.querySelectorAll('input[type="email"]'),
            function (el) { return el.type === 'email'; }
        );
        var telInputs = Array.prototype.filter.call(
            form.querySelectorAll('input[type="tel"]'),
            function (el) { return el.type === 'tel'; }
        );
        var strengthInputs = form.querySelectorAll('[data-password-strength]');
        var confirmInputs = form.querySelectorAll('[data-confirm-password]');

        function revalidateConfirmSources() {
            // Password berubah → cek ulang konfirmasi yang sudah terisi
            confirmInputs.forEach(function (ci) {
                if (ci.value !== '') validateConfirmInput(ci);
            });
        }

        emailInputs.forEach(function (input) {
            input.addEventListener('input', function () { validateEmailInput(input); });
            input.addEventListener('blur', function () { validateEmailInput(input); });
        });

        telInputs.forEach(function (input) {
            input.addEventListener('input', function () { validateTelInput(input); });
            input.addEventListener('blur', function () { validateTelInput(input); });
        });

        Array.prototype.forEach.call(strengthInputs, function (input) {
            input.addEventListener('input', function () {
                validateStrengthInput(input);
                revalidateConfirmSources();
            });
        });

        Array.prototype.forEach.call(confirmInputs, function (input) {
            input.addEventListener('input', function () { validateConfirmInput(input); });
            input.addEventListener('blur', function () { validateConfirmInput(input); });
        });

        form.addEventListener('submit', function (e) {
            var firstInvalid = null;
            var fail = function (input) {
                if (!firstInvalid) firstInvalid = input;
                return false;
            };

            emailInputs.forEach(function (input) {
                if (!validateEmailInput(input) && !firstInvalid) firstInvalid = input;
            });
            telInputs.forEach(function (input) {
                if (!validateTelInput(input) && !firstInvalid) firstInvalid = input;
            });
            Array.prototype.forEach.call(strengthInputs, function (input) {
                if (!validateStrengthInput(input) && !firstInvalid) firstInvalid = input;
            });
            Array.prototype.forEach.call(confirmInputs, function (input) {
                if (!validateConfirmInput(input) && !firstInvalid) firstInvalid = input;
            });

            if (firstInvalid) {
                e.preventDefault();
                firstInvalid.focus();
                return false;
            }
            return true;
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form[data-validate]').forEach(wireForm);
    });
})();
