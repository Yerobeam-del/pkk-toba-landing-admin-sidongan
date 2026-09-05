/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/modules/landing/sections/page-desa.blade.php
 *
 * Semua load data, render kartu, pagination, filter, dan pencarian
 * ditangani oleh desa-handler.js (dimuat dari layout) — termasuk
 * visibilitas loading/grid/empty/pagination lewat renderDesaPage().
 * File ini hanya memastikan render terpicu ulang saat halaman desa
 * diaktifkan lewat navigasi SPA.
 * ============================================================ */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', () => {
        const page = document.getElementById('page-desa');
        if (!page) return;

        const observer = new MutationObserver(() => {
            if (page.classList.contains('active') && typeof populateDesa === 'function') {
                populateDesa();
            }
        });

        observer.observe(page, { attributes: true, attributeFilter: ['class'] });
    });
})();
