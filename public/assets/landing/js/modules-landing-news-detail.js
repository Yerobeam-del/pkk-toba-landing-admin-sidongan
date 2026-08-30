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

        // Lazy load images inside article content (from WYSIWYG editor)
        document.querySelectorAll('.news-detail-content img').forEach(function(img) {
            if (!img.hasAttribute('loading')) {
                img.setAttribute('loading', 'lazy');
            }
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

        // Print button handler
        var printBtn = document.getElementById('newsPrintBtn');
        if (printBtn) {
            printBtn.addEventListener('click', function() {
                var wrapper = document.querySelector('.news-detail-wrapper');
                if (wrapper) {
                    var now = new Date();
                    var options = { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' };
                    wrapper.setAttribute('data-print-date', now.toLocaleDateString('id-ID', options));
                }
                window.print();
            });
        }

        // Reading progress bar
        var progressBar = document.getElementById('newsProgressBar');
        var backToTop = document.getElementById('newsBackToTop');
        if (progressBar || backToTop) {
            window.addEventListener('scroll', function() {
                var scrollTop = window.scrollY;
                var docHeight = document.documentElement.scrollHeight - window.innerHeight;
                var progress = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
                if (progressBar) progressBar.style.width = Math.min(progress, 100) + '%';
                if (backToTop) {
                    if (scrollTop > 400) {
                        backToTop.classList.add('visible');
                    } else {
                        backToTop.classList.remove('visible');
                    }
                }
            }, { passive: true });
        }
        if (backToTop) {
            backToTop.addEventListener('click', function() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
    });


