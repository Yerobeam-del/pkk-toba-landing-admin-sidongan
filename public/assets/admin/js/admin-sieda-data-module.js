/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/admin/sieda-data/module.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


// Konfirmasi Hapus Semua Data (pola fitur cleanup di halaman lain)
document.querySelectorAll('.delete-all-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const title = this.dataset.title;
        const message = this.dataset.message;
        Toast.confirm(message, {
            title: title,
            confirmText: 'Ya, Hapus Semua',
            cancelText: 'Batal',
            type: 'danger'
        }).then((confirmed) => {
            if (confirmed) this.submit();
        });
    });
});


