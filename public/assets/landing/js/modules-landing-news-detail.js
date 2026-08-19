/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/modules/landing/news-detail.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


    document.addEventListener('DOMContentLoaded', function() {
        // Hapus modal dari DOM
        var modal = document.getElementById('newsModal');
        if (modal && modal.parentNode) {
            modal.parentNode.removeChild(modal);
        }

        // Reset body
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';

        // Lazy load images
        var images = document.querySelectorAll('.news-detail-content img');
        images.forEach(function(img) {
            img.setAttribute('loading', 'lazy');
        });

        // Set active navbar
        document.querySelectorAll('.navbar-links a').forEach(function(link) {
            link.classList.remove('active-link');
            var text = link.textContent.trim().toLowerCase();
            var href = link.getAttribute('href') || '';
            if (text === 'berita' || href.includes('/berita')) {
                link.classList.add('active-link');
            }
        });
    });


