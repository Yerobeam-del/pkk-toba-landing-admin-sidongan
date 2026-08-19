/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/sidongan-auth/reset-password.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            const openIcon = btn.querySelector('[id^="eyeOpen"]');
            const closedIcon = btn.querySelector('[id^="eyeClosed"]');

            if (openIcon && closedIcon) {
                openIcon.style.display = isPassword ? 'none' : 'block';
                closedIcon.style.display = isPassword ? 'block' : 'none';
            }
        }

        function checkPasswordStrength(password) {
            const bars = document.querySelectorAll('#passwordStrength .bar');
            const hint = document.getElementById('passwordHint');
            let strength = 0;

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
            else hint.textContent = '✓ Password kuat';
        }
    


// ============================================================
// Dikembangkan oleh Institut Teknologi Del
// EVENT WIRING (menggantikan onclick/oninput inline)
// ============================================================
document.addEventListener('click', function (event) {
    const btn = event.target.closest('[data-action="toggle-password"]');
    if (btn) {
        togglePassword(btn.getAttribute('data-target'), btn);
    }
});

document.addEventListener('input', function (event) {
    const target = event.target;
    if (target && target.name === 'password') {
        checkPasswordStrength(target.value);
    }
});
