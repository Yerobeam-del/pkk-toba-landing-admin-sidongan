/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/admin/partials/table.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


if (typeof window.confirmDeleteItem === 'undefined') {
    window.confirmDeleteItem = function(id, name) {
        Toast.confirm(
            `Data <strong>"${name}"</strong> akan dihapus secara permanen.`,
            {
                title: 'Hapus Data?',
                confirmText: 'Ya, Hapus',
                cancelText: 'Batal',
                type: 'danger'
            }
        ).then(function(confirmed) {
            if (confirmed) {
                const form = document.getElementById('delete-form-' + id);
                if (form) form.submit();
            }
        });
    };
}



// ============================================================
// Dikembangkan oleh Institut Teknologi Del
// DELEGATION (menggantikan onclick inline)
// ============================================================
document.addEventListener('click', function (event) {
    const delBtn = event.target.closest('[data-delete-item]');
    if (delBtn) {
        window.confirmDeleteItem(
            delBtn.getAttribute('data-delete-item'),
            delBtn.getAttribute('data-delete-title') || ''
        );
    }
});
