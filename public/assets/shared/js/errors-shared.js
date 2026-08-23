/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS bersama untuk semua halaman error (401-503)
 * ============================================================ */

// Delegasi untuk tombol aksi halaman error
document.querySelectorAll('[data-error-action]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var action = btn.getAttribute('data-error-action');
        if (action === 'back') {
            if (window.history.length <= 1) {
                window.location.href = '/';
            } else {
                window.history.back();
            }
        } else if (action === 'reload') {
            window.location.reload();
        }
    });
});

// Auto-detect dark mode dari localStorage atau system preference
(function() {
    var isDark = localStorage.getItem('darkMode') === 'true' ||
                 (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches);
    if (isDark) {
        document.body.classList.add('dark-mode');
    }
})();
