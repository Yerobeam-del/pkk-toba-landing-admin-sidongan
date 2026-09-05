/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/modules/landing/sections/page-desa.blade.php
 *
 * All data loading, card rendering, filtering, and searching
 * is handled by desa-handler.js (loaded from layout).
 * This file only manages toolbar visibility when the desa page
 * becomes active via SPA navigation.
 * ============================================================ */

document.addEventListener('DOMContentLoaded', () => {
    const page = document.getElementById('page-desa');
    if (!page) return;

    // When the desa page becomes visible, ensure toolbar is shown
    const observer = new MutationObserver(() => {
        if (page.classList.contains('active')) {
            const toolbarEl = document.getElementById('desaToolbar');
            const loadingEl = document.getElementById('desa-loading');
            const gridEl = document.getElementById('desaGrid');

            // If data is already loaded (desa-handler populated the grid),
            // just make sure toolbar and grid are visible
            if (gridEl && gridEl.children.length > 0) {
                if (loadingEl) loadingEl.style.display = 'none';
                if (toolbarEl) toolbarEl.style.display = 'block';
                gridEl.style.display = 'grid';
            }
            observer.disconnect();
        }
    });

    observer.observe(page, { attributes: true, attributeFilter: ['class'] });

    // If already active on load, trigger immediately
    if (page.classList.contains('active')) {
        const toolbarEl = document.getElementById('desaToolbar');
        const loadingEl = document.getElementById('desa-loading');
        const gridEl = document.getElementById('desaGrid');
        if (gridEl && gridEl.children.length > 0) {
            if (loadingEl) loadingEl.style.display = 'none';
            if (toolbarEl) toolbarEl.style.display = 'block';
            gridEl.style.display = 'grid';
        }
    }
});
