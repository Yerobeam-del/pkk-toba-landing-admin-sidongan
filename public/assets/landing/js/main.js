console.log('Main JS Loaded');

document.addEventListener('DOMContentLoaded', function() {

    // ==========================================
    // 1. FLOATING BUTTON
    // ==========================================
    // Handler-nya ada di partial floating-btn.blade.php (toggleFloatingMenu),
    // menyatu dengan markup-nya. Dulu ada handler kedua di sini yang menyimpan
    // state sendiri (isMenuOpen) sehingga bisa desinkron dari DOM: setelah menu
    // tertutup oleh scroll, klik berikutnya justru tidak membuka menu.
    // Jangan tambahkan listener floating button di file ini lagi.

    // ==========================================
    // 2. HERO CTA (Tombol Jelajahi Layanan)
    // ==========================================
    document.addEventListener('click', function(e) {
        const heroCta = e.target.closest('.hero-cta');
        if (heroCta) {
            const href = heroCta.getAttribute('href');
            if (href && href.startsWith('#')) {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        }
    });

    // ==========================================
    // 3. EVENT DELEGATION GLOBAL (Fix: Seluruh Area Kartu Bisa Diklik)
    // ==========================================
    document.addEventListener('click', function(e) {
        // A. News Cards (Berita Terkini)
        const newsCard = e.target.closest('.news-card, .news-card-full, .news-card-link, .news-related-card, .news-card-home');
        if (newsCard && newsCard.tagName !== 'A') {
            const link = newsCard.querySelector('a') || newsCard.getAttribute('data-link');
            if (link) {
                e.preventDefault();
                window.location.href = typeof link === 'string' ? link : link.href;
            }
        }

        // B. App Cards & Quick Access (Aplikasi Aktif & Akses Cepat)
        const appCard = e.target.closest('.app-card-home, .coming-home-card, .quick-access-card, .quick-access-item');
        if (appCard && appCard.tagName !== 'A') {
            const link = appCard.querySelector('a') || appCard.getAttribute('data-link');
            if (link) {
                e.preventDefault();
                window.location.href = typeof link === 'string' ? link : link.href;
            }
        }
    });
});
