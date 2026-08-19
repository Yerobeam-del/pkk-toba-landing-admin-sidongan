/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/auth/reset-password.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


        // ==========================================
        // TOGGLE PASSWORD VISIBILITY
        // ==========================================
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

        // ==========================================
        // PASSWORD STRENGTH INDICATOR
        // ==========================================
        function checkPasswordStrength(password) {
            const bars = document.querySelectorAll('#passwordStrength .bar');
            const hint = document.getElementById('passwordHint');
            let strength = 0;

            // Reset all bars
            bars.forEach(bar => {
                bar.className = 'bar';
            });

            if (password.length === 0) {
                hint.textContent = '';
                return;
            }

            // Check length
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;

            // Check for mixed characters
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            // Normalize to 0-4
            strength = Math.min(strength, 4);

            // Update bars
            for (let i = 0; i < bars.length; i++) {
                if (i < strength) {
                    let level = 'weak';
                    if (strength >= 4) level = 'strong';
                    else if (strength >= 2) level = 'medium';
                    bars[i].className = 'bar active ' + level;
                }
            }

            // Update hint text
            if (strength <= 1) hint.textContent = 'Password lemah';
            else if (strength <= 2) hint.textContent = 'Password cukup';
            else if (strength <= 3) hint.textContent = 'Password baik';
            else hint.textContent = '✓ Password kuat';
        }
    



        // ==========================================
        // WIRING DELEGASI (diekstrak dari atribut inline)
        // ==========================================
        document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                togglePassword(btn.getAttribute('data-toggle-password'), btn);
            });
        });

        document.querySelectorAll('[data-password-strength]').forEach(function (input) {
            input.addEventListener('input', function () {
                checkPasswordStrength(input.value);
            });
        });
