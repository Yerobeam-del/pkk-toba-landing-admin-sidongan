/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/admin/template/index.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


// Konfirmasi Hapus
if (typeof window.confirmDeleteItem === 'undefined') {
    window.confirmDeleteItem = function(id, name) {
        Toast.confirm(
            `Template <strong>"${name}"</strong> akan dihapus secara permanen.`,
            {
                title: 'Hapus Template?',
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


