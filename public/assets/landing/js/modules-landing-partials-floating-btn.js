/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/modules/landing/partials/floating-btn.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


function toggleFloatingMenu(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    const btn = document.getElementById('floatingAppBtn');
    const menu = document.getElementById('floatingMenu');
    if (!btn || !menu) return;

    const isOpen = btn.classList.contains('open');

    if (isOpen) {
        // Tutup menu
        btn.classList.remove('open');
        // Nonaktifkan pointer events setelah animasi selesai (misal 300ms)
        setTimeout(() => {
            if (!btn.classList.contains('open')) {
                menu.style.pointerEvents = 'none';
            }
        }, 300);
    } else {
        // Buka menu
        btn.classList.add('open');
        // Aktifkan pointer events segera agar bisa diklik
        menu.style.pointerEvents = 'auto';
    }
}

// Close menu when clicking outside
document.addEventListener('click', function(e) {
    const btn = document.getElementById('floatingAppBtn');
    const menu = document.getElementById('floatingMenu');

    if (btn && menu && !btn.contains(e.target)) {
        btn.classList.remove('open');
        setTimeout(() => {
            if (!btn.classList.contains('open')) {
                menu.style.pointerEvents = 'none';
            }
        }, 300);
    }
});

// Close menu on scroll (opsional, untuk UX mobile yang lebih baik)
let scrollTimeout;
window.addEventListener('scroll', function() {
    const btn = document.getElementById('floatingAppBtn');
    const menu = document.getElementById('floatingMenu');
    if (btn && menu && btn.classList.contains('open')) {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
            btn.classList.remove('open');
            menu.style.pointerEvents = 'none';
        }, 100);
    }
}, { passive: true });


