// ============================================================
// Dikembangkan oleh Institut Teknologi Del
// NAVBAR — LANDING HEADER
// ============================================================

// DETECT CURRENT PAGE TYPE
function isSPA() {
    return document.getElementById('page-beranda') !== null;
}

function getCurrentPath() {
    return window.location.pathname;
}

function isLaravelRoute() {
    const path = getCurrentPath();
    // Cek apakah sedang di route Laravel (bukan homepage)
    return path !== '/' && !path.includes('#');
}

// ==========================================
// NAVIGATION HANDLER - UNIVERSAL
// ==========================================
function handleNavClick(event, pageId) {
    event.preventDefault();

    const homeUrl = document.getElementById('navbar').getAttribute('data-home-url');

    // Tutup mobile menu jika terbuka tanpa menyalakan ulang
    closeMobileMenu();

    // Update active nav
    updateActiveNav(pageId);

    if (pageId === 'beranda') {
        // Selalu ke homepage untuk beranda
        window.location.href = homeUrl;
    } else if (isSPA()) {
        // Jika di SPA, gunakan SPA navigation
        if (typeof navigateTo === 'function') {
            navigateTo(pageId);
            window.location.hash = pageId;
        } else {
            // Fallback jika navigateTo tidak ada
            window.location.href = homeUrl + '#' + pageId;
        }
    } else {
        // Jika di Laravel route, redirect ke SPA dengan hash
        window.location.href = homeUrl + '#' + pageId;
    }

    return false;
}

// ==========================================
// MOBILE MENU TOGGLE
// ==========================================
function toggleMobileMenu() {
    const btn = document.getElementById('hamburgerBtn');
    const menu = document.getElementById('mobileMenu');
    if (btn) btn.classList.toggle('active');
    if (menu) menu.classList.toggle('open');
}

function closeMobileMenu() {
    const btn = document.getElementById('hamburgerBtn');
    const menu = document.getElementById('mobileMenu');
    if (btn) btn.classList.remove('active');
    if (menu) menu.classList.remove('open');
}

function handleNavBrandClick(event) {
    event.preventDefault();
    closeMobileMenu();
    const homeUrl = document.getElementById('navbar').getAttribute('data-home-url');
    if (window.location.pathname !== homeUrl) {
        window.location.href = homeUrl;
        return false;
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
    return false;
}

// ==========================================
// UPDATE ACTIVE NAV LINK
// ==========================================
function updateActiveNav(pageId) {
    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active-link');
        if (link.getAttribute('data-page') === pageId) {
            link.classList.add('active-link');
        }
    });
}

// ==========================================
// EVENT DELEGATION (menggantikan onclick inline)
// ==========================================
document.addEventListener('click', function (event) {
    const navLink = event.target.closest('.nav-link');
    if (navLink) {
        handleNavClick(event, navLink.getAttribute('data-page'));
        return;
    }
    const brand = event.target.closest('.navbar-brand');
    if (brand) {
        handleNavBrandClick(event);
        return;
    }
    if (event.target.closest('#hamburgerBtn')) {
        toggleMobileMenu();
    }
});

// ==========================================
// SCROLL EFFECT
// ==========================================
window.addEventListener('scroll', () => {
    const nav = document.getElementById('navbar');
    if (nav) {
        if (window.scrollY > 50) nav.classList.add('scrolled');
        else nav.classList.remove('scrolled');
    }
});

// ==========================================
// INIT ON LOAD
// ==========================================
document.addEventListener('DOMContentLoaded', () => {
    // Determine active page from hash or path
    const hash = window.location.hash.replace('#', '');
    const path = getCurrentPath();

    let currentPage = 'beranda';

    if (hash) {
        currentPage = hash;
    } else if (path.includes('/berita')) {
        currentPage = 'berita';
    } else if (path.includes('/sk')) {
        currentPage = 'sk';
    } else if (path.includes('/template')) {
        currentPage = 'template';
    } else if (path.includes('/desa')) {
        currentPage = 'desa';
    }

    updateActiveNav(currentPage);

    // Apply scroll effect
    if (window.scrollY > 50) {
        const nav = document.getElementById('navbar');
        if (nav) nav.classList.add('scrolled');
    }
});

// ==========================================
// HANDLE BROWSER BACK/FORWARD
// ==========================================
window.addEventListener('popstate', () => {
    const hash = window.location.hash.replace('#', '');
    const path = getCurrentPath();

    let currentPage = 'beranda';

    if (hash) {
        currentPage = hash;
    } else if (path.includes('/berita')) {
        currentPage = 'berita';
    }

    updateActiveNav(currentPage);

    // If in SPA, navigate to page
    if (isSPA() && typeof navigateTo === 'function' && hash) {
        navigateTo(hash);
    }
});
