console.log('Main JS Loaded');

document.addEventListener('DOMContentLoaded', function() {

    // ==========================================
    // 1. FLOATING BUTTON HANDLER (Fix: Tanpa Delay - Jejak Langsung Hilang)
    // ==========================================
    const floatingBtn = document.querySelector('.floating-app-btn');
    const floatingTrigger = document.querySelector('.floating-trigger');
    const floatingMenu = document.querySelector('.floating-menu');

    if (floatingTrigger && floatingMenu) {
        let isMenuOpen = false;

        floatingTrigger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            isMenuOpen = !isMenuOpen;

            if (isMenuOpen) {
                // Buka menu
                floatingMenu.classList.add('open');
                floatingTrigger.classList.add('open');
                if (floatingBtn) floatingBtn.classList.add('open');
                floatingMenu.style.pointerEvents = 'auto'; // Aktifkan klik segera
            } else {
                // Tutup menu - JEJAK LANGSUNG HILANG TANPA DELAY
                floatingMenu.classList.remove('open');
                floatingTrigger.classList.remove('open');
                if (floatingBtn) floatingBtn.classList.remove('open');
                floatingMenu.style.pointerEvents = 'none'; // Nonaktifkan klik SEGERA
            }
        });

        // Tutup menu saat klik di luar
        document.addEventListener('click', function(e) {
            if (isMenuOpen && floatingBtn && !floatingBtn.contains(e.target)) {
                isMenuOpen = false;
                floatingMenu.classList.remove('open');
                floatingTrigger.classList.remove('open');
                if (floatingBtn) floatingBtn.classList.remove('open');
                floatingMenu.style.pointerEvents = 'none'; // Nonaktifkan klik SEGERA
            }
        });
    }

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
        const newsCard = e.target.closest('.news-card, .news-card-full, .news-card-link, .news-related-card');
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
