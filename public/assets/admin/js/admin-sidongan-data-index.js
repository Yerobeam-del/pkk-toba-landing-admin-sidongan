/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/admin/sidongan-data/index.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


// Konfirmasi Cleanup
document.querySelectorAll('.cleanup-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const title = this.dataset.title;
        const message = this.dataset.message;
        Toast.confirm(message, {
            title: title,
            confirmText: 'Ya, Hapus Sekarang',
            cancelText: 'Batal',
            type: 'danger'
        }).then((confirmed) => {
            if (confirmed) this.submit();
        });
    });
});

// Konfirmasi Delete Single
function confirmDeleteItem(id, name) {
    const message = `Apakah Anda yakin ingin menghapus surat "<strong>${name}</strong>" secara permanen?<br><small style="color:#64748b">File, laporan, dan notifikasi terkait juga akan dihapus.</small>`;
    Toast.confirm(message, {
        title: 'Hapus Permanen',
        confirmText: 'Ya, Hapus',
        cancelText: 'Batal',
        type: 'danger'
    }).then((confirmed) => {
        if (confirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `/admin/sidongan-data/${id}`;
            form.submit();
        }
    });
}


