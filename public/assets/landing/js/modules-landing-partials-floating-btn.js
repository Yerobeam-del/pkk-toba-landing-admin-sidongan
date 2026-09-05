/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/modules/landing/partials/floating-btn.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


function collapseFloatingMenu(btn, menu) {
    btn.classList.remove('open');
    document.body.classList.remove('floating-menu-open');
    // Tahan tombol kembali ke atas tetap tersembunyi selama menu menutup,
    // lalu biarkan dia muncul kembali dengan fade setelah animasi selesai.
    document.body.classList.add('floating-menu-closing');
    setTimeout(() => {
        document.body.classList.remove('floating-menu-closing');
    }, 350);
    // Nonaktifkan pointer events setelah animasi selesai (misal 300ms)
    setTimeout(() => {
        if (!btn.classList.contains('open')) {
            menu.style.pointerEvents = 'none';
        }
    }, 300);
}

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
        collapseFloatingMenu(btn, menu);
    } else {
        // Buka menu
        btn.classList.add('open');
        document.body.classList.add('floating-menu-open');
        // Aktifkan pointer events segera agar bisa diklik
        menu.style.pointerEvents = 'auto';
    }
}

// Close menu when clicking outside
document.addEventListener('click', function(e) {
    const btn = document.getElementById('floatingAppBtn');
    const menu = document.getElementById('floatingMenu');

    if (btn && menu && !btn.contains(e.target)) {
        collapseFloatingMenu(btn, menu);
    }
});

// Close menu on scroll (opsional, untuk UX mobile yang lebih baik)
let scrollTimeout;
window.addEventListener('scroll', function() {
    const btn = document.getElementById('floatingAppBtn');
    const menu = document.getElementById('floatingMenu');
    if (btn && menu && btn.classList.contains('open')) {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => collapseFloatingMenu(btn, menu), 100);
    }
}, { passive: true });


