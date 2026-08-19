/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Halaman Arsip SIDONGAN — pencarian otomatis, filter cepat,
 * reset filter/sorting, dan navigasi sortir tabel.
 * URL dasar dibaca dari atribut data-base-url pada #filterForm.
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
(function () {
    'use strict';

    const filterForm = document.getElementById('filterForm');

    // ===============================
    // Pencarian otomatis (debounce)
    // ===============================
    const searchInput = document.getElementById('searchInput');
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
    // Reset filter & sorting
    // ===============================
    function resetFilters() {
        if (!filterForm) return;
        const baseUrl = filterForm.getAttribute('data-base-url');
        if (baseUrl) {
            window.location.href = baseUrl;
        } else {
            filterForm.reset();
            filterForm.submit();
        }
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
        const target = event.target.closest('[data-action]');
        if (!target) return;

        const action = target.getAttribute('data-action');
        if (action === 'reset-filters') {
            resetFilters();
        } else if (action === 'reset-sorting') {
            resetSorting();
        }
    });

    document.addEventListener('click', function (event) {
        const th = event.target.closest('th[data-sort-url]');
        if (th) {
            window.location.href = th.getAttribute('data-sort-url');
        }
    });

    // ===============================
    // Auto-submit saat filter berubah
    // ===============================
    if (filterForm) {
        filterForm.addEventListener('change', function (event) {
            const name = event.target && event.target.name;
            const auto = ['per_page', 'year', 'filter_month', 'date_from', 'date_to'];
            if (name && auto.indexOf(name) !== -1) {
                filterForm.submit();
            }
        });
    }
})();
