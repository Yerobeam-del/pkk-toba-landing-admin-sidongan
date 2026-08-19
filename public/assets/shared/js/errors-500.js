/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/errors/500.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */

        // Delegasi untuk tombol aksi halaman error
        // (diekstrak dari atribut inline onclick)
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
    