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

document.querySelectorAll('.profile-tab').forEach(function(tab) {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.profile-tab').forEach(function(t) { t.classList.remove('active'); });
        document.querySelectorAll('.tab-content').forEach(function(tc) { tc.classList.remove('active'); });
        this.classList.add('active');
        document.getElementById('tab-' + this.dataset.tab).classList.add('active');
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

    function checkPasswordStrength(password) {
        const bars = document.querySelectorAll('#passwordStrength .bar');
        const hint = document.getElementById('passwordHint');
        let strength = 0;
        if (!bars.length || !hint) return;

        bars.forEach(bar => { bar.className = 'bar'; });

        if (password.length === 0) {
            hint.textContent = '';
            return;
        }
        if (password.length >= 8) strength++;
        if (password.length >= 12) strength++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        if (/\d/.test(password)) strength++;
        if (/[^a-zA-Z0-9]/.test(password)) strength++;
        strength = Math.min(strength, 4);

        for (let i = 0; i < bars.length; i++) {
            if (i < strength) {
                let level = 'weak';
                if (strength >= 4) level = 'strong';
                else if (strength >= 2) level = 'medium';
                bars[i].className = 'bar active ' + level;
            }
        }

        if (strength <= 1) hint.textContent = 'Password lemah';
        else if (strength <= 2) hint.textContent = 'Password cukup';
        else if (strength <= 3) hint.textContent = 'Password baik';
        else hint.textContent = '\u2713 Password kuat';
    }

    function checkPasswordMatch(value) {
        const password = document.getElementById('password');
        const hint = document.getElementById('passwordMatchHint');
        if (!password || !hint) return;
        if (value === '') {
            hint.textContent = '';
            return;
        }
        hint.textContent = value === password.value ? '\u2713 Password cocok' : 'Password tidak cocok';
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
