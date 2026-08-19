/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Menampilkan pesan flash session (success/error/warning/info)
 * lewat Toast untuk SIDONGAN. Nilai dibaca dari atribut data-*
 * pada elemen #toast-flash yang diisi oleh Blade.
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('toast-flash');
    if (!el || typeof Toast === 'undefined') return;

    const types = [
        ['data-success', 'success'],
        ['data-error', 'error'],
        ['data-warning', 'warning'],
        ['data-info', 'info'],
    ];

    types.forEach(function (pair) {
        const value = el.getAttribute(pair[0]);
        if (value) {
            Toast[pair[1]](value);
        }
    });
});
