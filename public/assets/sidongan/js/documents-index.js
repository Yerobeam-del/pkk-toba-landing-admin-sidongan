/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Daftar Surat Masuk — pencarian otomatis, filter cepat,
 * konfirmasi hapus/arsipkan, reset filter & sorting.
 * URL dasar dibaca dari atribut data-* pada #filterForm.
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
(function () {
    'use strict';

    const filterForm = document.getElementById('filterForm');
    const searchInput = document.getElementById('searchInput');

    // ===============================
    // Pencarian otomatis (debounce)
    // ===============================
    if (searchInput && filterForm) {
        let searchTimeout;
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterForm.submit();
            }, 500);
        });
    }

    // ===============================
    // Konfirmasi Hapus / Arsipkan
    // ===============================
    function deleteBaseUrl() {
        const form = document.getElementById('deleteForm');
        return form ? form.getAttribute('data-base-url') : '/sidongan/documents';
    }

    function archiveBaseUrl() {
        const form = document.getElementById('archiveForm');
        return form ? form.getAttribute('data-base-url') : '/sidongan/documents';
    }

    function confirmDelete(id, title) {
        Toast.confirm('Apakah Anda yakin ingin menghapus surat "<strong>' + title + '</strong>"?<br><small style="color:#64748b;">Peringatan: Tindakan ini tidak dapat dibatalkan.</small>', {
            title: 'Konfirmasi Hapus Surat',
            confirmText: 'Ya, Hapus',
            cancelText: 'Batal',
            type: 'danger'
        }).then((confirmed) => {
            if (confirmed) {
                const form = document.getElementById('deleteForm');
                form.action = deleteBaseUrl() + '/' + id;
                form.submit();
            }
        });
    }

    function confirmArchive(id, title) {
        Toast.confirm('Apakah Anda yakin ingin mengarsipkan surat "<strong>' + title + '</strong>"?<br><small style="color:#64748b;">Surat yang diarsipkan akan dipindahkan ke menu Arsip Surat.</small>', {
            title: 'Konfirmasi Arsipkan Surat',
            confirmText: 'Ya, Arsipkan',
            cancelText: 'Batal',
            type: 'warning'
        }).then((confirmed) => {
            if (confirmed) {
                const form = document.getElementById('archiveForm');
                form.action = archiveBaseUrl() + '/' + id + '/archive';
                form.submit();
            }
        });
    }

    // ===============================
    // Filter cepat (status)
    // ===============================
    function applyQuickFilter(status) {
        if (!filterForm) return;
        const statusSelect = document.getElementById('statusSelect');
        if (statusSelect) statusSelect.value = status;
        filterForm.submit();
    }

    // ===============================
    // Reset filter & sorting
    // ===============================
    function resetFilters() {
        Toast.confirm('Reset semua filter?', {
            title: 'Konfirmasi Reset',
            confirmText: 'Ya, Reset',
            cancelText: 'Batal',
            type: 'warning'
        }).then((confirmed) => {
            if (confirmed && filterForm) {
                const baseUrl = filterForm.getAttribute('data-base-url');
                window.location.href = baseUrl || '/sidongan/documents';
            }
        });
    }

    function resetSorting() {
        const url = new URL(window.location.href);
        url.searchParams.delete('sort');
        url.searchParams.delete('direction');
        window.location.href = url.toString();
    }

    // ===============================
    // Event Delegation (menggantikan onclick inline)
    // ===============================
    document.addEventListener('click', function (event) {
        // Filter cepat status
        const qf = event.target.closest('[data-filter-status]');
        if (qf) {
            applyQuickFilter(qf.getAttribute('data-filter-status'));
            return;
        }

        // Tombol reset filter / sorting
        const actionBtn = event.target.closest('[data-action]');
        if (actionBtn) {
            const action = actionBtn.getAttribute('data-action');
            if (action === 'reset-filters') resetFilters();
            else if (action === 'reset-sorting') resetSorting();
            return;
        }

        // Kolom sortir tabel
        const th = event.target.closest('th[data-sort-url]');
        if (th) {
            window.location.href = th.getAttribute('data-sort-url');
            return;
        }

        // Tombol hapus
        const del = event.target.closest('[data-delete-id]');
        if (del) {
            confirmDelete(del.getAttribute('data-delete-id'), del.getAttribute('data-delete-title'));
            return;
        }

        // Tombol arsipkan
        const arch = event.target.closest('[data-archive-id]');
        if (arch) {
            confirmArchive(arch.getAttribute('data-archive-id'), arch.getAttribute('data-archive-title'));
        }
    });

    // ===============================
    // Auto-submit saat filter berubah
    // ===============================
    if (filterForm) {
        filterForm.addEventListener('change', function (event) {
            const name = event.target && event.target.name;
            const auto = ['per_page', 'status', 'filter_month', 'filter_year', 'date_from', 'date_to'];
            if (name && auto.indexOf(name) !== -1) {
                filterForm.submit();
            }
        });
    }
})();
