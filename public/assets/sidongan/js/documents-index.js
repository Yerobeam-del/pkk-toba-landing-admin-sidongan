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

    // ===============================
    // Bulk Actions
    // ===============================
    const selectAll = document.getElementById('selectAll');
    const bulkBar = document.getElementById('bulkActionBar');
    const bulkCount = document.getElementById('bulkSelectedCount');
    const docCheckboxes = document.querySelectorAll('.doc-checkbox');

    function getSelectedIds() {
        return Array.from(document.querySelectorAll('.doc-checkbox:checked')).map(cb => cb.value);
    }

    function updateBulkBar() {
        const count = getSelectedIds().length;
        if (bulkBar) {
            bulkBar.style.display = count > 0 ? 'block' : 'none';
            if (bulkCount) bulkCount.textContent = count;
        }
        if (selectAll) {
            selectAll.checked = count > 0 && count === docCheckboxes.length;
            selectAll.indeterminate = count > 0 && count < docCheckboxes.length;
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            docCheckboxes.forEach(cb => { cb.checked = selectAll.checked; });
            updateBulkBar();
        });
    }

    docCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkBar);
    });

    // Bulk action buttons
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-action]');
        if (!btn) return;
        const action = btn.getAttribute('data-action');

        if (action === 'bulk-cancel') {
            docCheckboxes.forEach(cb => { cb.checked = false; });
            if (selectAll) selectAll.checked = false;
            updateBulkBar();
            return;
        }

        if (action === 'bulk-archive' || action === 'bulk-delete') {
            const ids = getSelectedIds();
            if (ids.length === 0) return;

            const isDelete = action === 'bulk-delete';
            const url = isDelete
                ? '/sidongan/documents/bulk-delete'
                : '/sidongan/documents/bulk-archive';
            const label = isDelete ? 'menghapus' : 'mengarsipkan';
            const countLabel = ids.length + ' surat';

            Toast.confirm('Apakah Anda yakin ingin ' + label + ' <strong>' + countLabel + '</strong>?<br><small style="color:#64748b;">' + (isDelete ? 'Tindakan ini tidak dapat dibatalkan.' : 'Surat akan dipindahkan ke Arsip.') + '</small>', {
                title: 'Konfirmasi ' + (isDelete ? 'Hapus' : 'Arsipkan') + ' Massal',
                confirmText: 'Ya, ' + (isDelete ? 'Hapus' : 'Arsipkan'),
                cancelText: 'Batal',
                type: isDelete ? 'danger' : 'warning'
            }).then((confirmed) => {
                if (!confirmed) return;

                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ ids: ids })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Toast.success(data.message || 'Berhasil!');
                        setTimeout(() => { window.location.reload(); }, 1000);
                    } else {
                        Toast.error(data.message || 'Gagal memproses.');
                        btn.disabled = false;
                        btn.innerHTML = isDelete
                            ? '<i class="fas fa-trash-alt"></i> Hapus'
                            : '<i class="fas fa-archive"></i> Arsipkan';
                    }
                })
                .catch(() => {
                    Toast.error('Terjadi kesalahan jaringan.');
                    btn.disabled = false;
                    btn.innerHTML = isDelete
                        ? '<i class="fas fa-trash-alt"></i> Hapus'
                        : '<i class="fas fa-archive"></i> Arsipkan';
                });
            });
        }
    });
})();
