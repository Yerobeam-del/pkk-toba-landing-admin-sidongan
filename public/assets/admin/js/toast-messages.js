/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Menampilkan pesan flash session (success/error/warning/info)
 * lewat Toast. Nilai dibaca dari atribut data-* pada <body>
 * yang diisi oleh Blade — jadi tidak ada JS inline.
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;

    const messages = [
        ['data-session-success', 'success'],
        ['data-session-error', 'error'],
        ['data-session-warning', 'warning'],
        ['data-session-info', 'info'],
    ];

    messages.forEach(([attr, type]) => {
        const value = body.getAttribute(attr);
        if (value && typeof Toast !== 'undefined') {
            Toast[type](value);
        }
    });
});
