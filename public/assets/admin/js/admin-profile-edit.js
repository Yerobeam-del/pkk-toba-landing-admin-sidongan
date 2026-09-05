/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/admin/profile/edit.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


let cropper = null;

document.getElementById('avatarInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    if (file.size > 2 * 1024 * 1024) { Toast.warning('Ukuran file maksimal 2MB.'); e.target.value = ''; return; }
    if (!file.type.match('image.*')) { Toast.warning('File harus berupa gambar.'); e.target.value = ''; return; }
    const reader = new FileReader();
    reader.onload = function(ev) {
        const img = document.getElementById('cropperImage');
        img.src = ev.target.result;
        document.getElementById('cropperModal').style.display = 'flex';
        setTimeout(function() {
            if (cropper) cropper.destroy();
            cropper = new Cropper(img, { aspectRatio: 1, viewMode: 1, dragMode: 'move', autoCropArea: 0.8, restore: false, guides: true, center: true, cropBoxMovable: true, cropBoxResizable: true, toggleDragModeOnDblclick: false });
        }, 100);
    };
    reader.readAsDataURL(file);
});

function closeCropper() {
    if (cropper) { cropper.destroy(); cropper = null; }
    document.getElementById('cropperModal').style.display = 'none';
    document.getElementById('avatarInput').value = '';
}

function cropAndSave() {
    if (!cropper) return;
    var dataUrl = cropper.getCroppedCanvas({ width: 400, height: 400, fillColor: '#fff', imageSmoothingEnabled: true, imageSmoothingQuality: 'high' }).toDataURL('image/jpeg', 0.9);
    document.getElementById('croppedAvatarBase64').value = dataUrl;
    var preview = document.getElementById('avatarPreview');
    var placeholder = document.getElementById('avatarPlaceholder');
    if (preview) { preview.src = dataUrl; }
    if (placeholder) { placeholder.style.display = 'none'; }
    closeCropper();
}

document.getElementById('profileForm').addEventListener('submit', function() {
    var base64 = document.getElementById('croppedAvatarBase64').value;
    if (base64 && base64.length > 100) {
        document.getElementById('avatarInput').disabled = true;
    }
});

// ============================================================
// TABS — pola WAI-ARIA Tabs (roving tabindex + tombol panah)
// ============================================================
var profileTabs = Array.prototype.slice.call(document.querySelectorAll('.profile-tab'));

function activateTab(tab) {
    profileTabs.forEach(function(t) {
        var selected = t === tab;
        t.classList.toggle('active', selected);
        t.setAttribute('aria-selected', selected ? 'true' : 'false');
        // Roving tabindex: hanya tab aktif yang ada di urutan Tab;
        // sisanya dijangkau lewat tombol panah saat fokus di tablist.
        t.tabIndex = selected ? 0 : -1;
    });
    document.querySelectorAll('.tab-content').forEach(function(tc) { tc.classList.remove('active'); });
    var panel = document.getElementById('tab-' + tab.dataset.tab);
    if (panel) panel.classList.add('active');
}

profileTabs.forEach(function(tab) {
    tab.addEventListener('click', function() { activateTab(tab); });
    tab.addEventListener('keydown', function(e) {
        var idx = profileTabs.indexOf(tab);
        var next = null;
        if (e.key === 'ArrowRight' || e.key === 'ArrowDown') next = profileTabs[(idx + 1) % profileTabs.length];
        else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') next = profileTabs[(idx - 1 + profileTabs.length) % profileTabs.length];
        else if (e.key === 'Home') next = profileTabs[0];
        else if (e.key === 'End') next = profileTabs[profileTabs.length - 1];
        else return;
        e.preventDefault();
        next.focus();
        activateTab(next);
    });
});

document.addEventListener('DOMContentLoaded', function() {
    var msg = document.getElementById('successMsg');
    if (msg) {
        setTimeout(function() {
            msg.style.transition = 'opacity 0.3s, transform 0.3s';
            msg.style.opacity = '0';
            msg.style.transform = 'translateY(-10px)';
            setTimeout(function() { msg.remove(); }, 300);
        }, 5000);
    }
});



// ============================================================
// Dikembangkan oleh Institut Teknologi Del
// EVENT WIRING (menggantikan onclick/oninput inline)
// ============================================================
(function () {
    'use strict';

    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';

        const openIcon = btn.querySelector('[id^="eyeOpen"]');
        const closedIcon = btn.querySelector('[id^="eyeClosed"]');
        if (openIcon) openIcon.style.display = isPassword ? 'none' : 'block';
        if (closedIcon) closedIcon.style.display = isPassword ? 'block' : 'none';
    }

    // Elemen halaman: #strengthFill (bar) & #strengthLabel (teks) — lihat
    // tab "Keamanan" di admin/profile/edit.blade.php.
    function checkPasswordStrength(password) {
        const bar = document.getElementById('strengthFill');
        const label = document.getElementById('strengthLabel');
        if (!bar || !label) return;

        if (password.length === 0) {
            bar.style.width = '0';
            label.textContent = '';
            label.style.color = '';
            return;
        }

        let score = 0;
        if (password.length >= 8) score++;
        if (password.length >= 12) score++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
        if (/\d/.test(password)) score++;
        if (/[^a-zA-Z0-9]/.test(password)) score++;
        score = Math.min(score, 4);

        const levels = {
            1: ['25%', '#ef4444', 'Password lemah'],
            2: ['50%', '#f59e0b', 'Password cukup'],
            3: ['75%', '#eab308', 'Password baik'],
            4: ['100%', '#16a34a', '\u2713 Password kuat'],
        };
        const [width, color, text] = levels[score] || levels[1];

        bar.style.width = width;
        bar.style.background = color;
        label.textContent = text;
        label.style.color = color;
    }

    function checkPasswordMatch(value) {
        const password = document.getElementById('password');
        const msg = document.getElementById('matchMsg');
        if (!password || !msg) return;
        if (value === '') {
            msg.textContent = '';
            return;
        }
        const matched = value === password.value;
        msg.textContent = matched ? '\u2713 Password cocok' : 'Password tidak cocok';
        msg.style.color = matched ? '#16a34a' : '#ef4444';
    }

    document.addEventListener('click', function (event) {
        const target = event.target;
        const avatarBtn = target.closest('[data-action="pick-avatar"]');
        if (avatarBtn) {
            const input = document.getElementById('avatarInput');
            if (input) input.click();
            return;
        }
        const pwBtn = target.closest('[data-action="toggle-password"]');
        if (pwBtn) {
            togglePassword(pwBtn.getAttribute('data-target'), pwBtn);
            return;
        }
        if (target.closest('[data-action="close-cropper"]')) {
            closeCropper();
            return;
        }
        if (target.closest('[data-action="crop-save"]')) {
            cropAndSave();
        }
    });

    document.addEventListener('input', function (event) {
        const target = event.target;
        if (!target) return;
        if (target.id === 'password') checkPasswordStrength(target.value);
        if (target.id === 'password_confirmation') checkPasswordMatch(target.value);
    });
})();
