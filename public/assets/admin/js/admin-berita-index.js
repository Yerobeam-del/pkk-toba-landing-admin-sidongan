/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/admin/berita/index.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


// Konfirmasi Hapus (menggunakan ID form dari partial table)
if (typeof window.confirmDeleteItem === 'undefined') {
    window.confirmDeleteItem = function(id, name) {
        Toast.confirm(
            `Berita <strong>\"${name}\"</strong> akan dihapus secara permanen.`,
            {
                title: 'Hapus Berita?',
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


