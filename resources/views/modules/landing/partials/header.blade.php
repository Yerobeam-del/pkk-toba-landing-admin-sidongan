<nav class="navbar" id="navbar">
    {{-- ... CSS tetap sama ... --}}
    <style>
        /* CSS sama seperti sebelumnya */
        .navbar {
            position: fixed; top: 0; left: 0; right: 0;
            z-index: 1000;
            background: transparent; 
            transition: all 0.3s ease;
        }
        .navbar.scrolled {
            background: rgba(20, 83, 76, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .navbar-inner {
            max-width: 1400px; margin: 0 auto;
            padding: 1rem 2rem;
            display: flex; align-items: center; justify-content: space-between;
            position: relative;
        }
        .navbar-brand { display: flex; align-items: center; gap: 12px; cursor: pointer; z-index: 1002; }
        .navbar-logo { width: 40px; height: 40px; object-fit: contain; }
        .navbar-title { display: flex; flex-direction: column; line-height: 1.2; }
        .navbar-title span:first-child { font-weight: 800; font-size: 1.05rem; color: #fff; letter-spacing: 0.5px; }
        .navbar-title span:last-child { font-size: 0.7rem; color: rgba(255,255,255,0.7); font-weight: 500; }
        .navbar-links { display: flex; align-items: center; gap: 8px; list-style: none; margin: 0; padding: 0; }
        .nav-link {
            color: rgba(255,255,255,0.9); text-decoration: none; font-size: 0.875rem; font-weight: 600;
            padding: 6px 10px; border-radius: 6px; transition: all 0.2s ease; cursor: pointer;
        }
        .nav-link:hover { color: #fff; background: rgba(255,255,255,0.15); }
        .nav-link.active-link { color: #fbbf24; background: rgba(251, 191, 36, 0.15); font-weight: 700; }
        .hamburger { display: none; flex-direction: column; gap: 5px; cursor: pointer; padding: 5px; z-index: 1002; background: none; border: none; }
        .hamburger span { width: 24px; height: 2px; background: #fff; border-radius: 2px; transition: all 0.3s ease; }
        .hamburger.active span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .hamburger.active span:nth-child(2) { opacity: 0; transform: scaleX(0); }
        .hamburger.active span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }
        .mobile-menu {
            position: absolute; top: 100%; left: 0; right: 0;
            background: rgba(20, 83, 76, 0.98);
            backdrop-filter: blur(15px);
            padding: 1rem 2rem 2rem 2rem;
            border-radius: 0 0 16px 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            opacity: 0; visibility: hidden; transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 999;
            display: flex; flex-direction: column; gap: 0.5rem; 
        }
        .mobile-menu.open { opacity: 1; visibility: visible; transform: translateY(0); }
        .mobile-menu .nav-link {
            display: block; width: 100%; padding: 12px 15px;
            border-radius: 8px; border-bottom: 1px solid rgba(255,255,255,0.05);
            font-size: 1rem; text-align: left;
        }
        .mobile-menu .nav-link:hover { background: rgba(255,255,255,0.05); }
        @media (max-width: 1100px) {
            .navbar-links { display: none; }
            .hamburger { display: flex; }
            .navbar-title span:last-child { display: none; }
        }
        @media (min-width: 1101px) {
            .mobile-menu { display: none !important; }
        }
    </style>

    <div class="navbar-inner">
        <div class="navbar-brand" onclick="handleNavClick(event, 'beranda')">
            <img src="{{ asset('assets/landing/images/PKK-Logo.png') }}" alt="Logo" class="navbar-logo">
            <div class="navbar-title">
                <span>PKK KAB. TOBA</span>
                <span>Kabupaten Toba, Sumatera Utara</span>
            </div>
        </div>
        
        {{-- Menu Desktop --}}
        <ul class="navbar-links" id="navLinks">
            <li><a href="{{ route('landing.home') }}" onclick="handleNavClick(event, 'beranda')" class="nav-link" data-page="beranda">Beranda</a></li>
            <li><a href="{{ route('landing.home') }}#struktur" onclick="handleNavClick(event, 'struktur')" class="nav-link" data-page="struktur">Struktur</a></li>
            <li><a href="{{ route('landing.home') }}#aplikasi" onclick="handleNavClick(event, 'aplikasi')" class="nav-link" data-page="aplikasi">Aplikasi</a></li>
            <li><a href="{{ route('landing.home') }}#berita" onclick="handleNavClick(event, 'berita')" class="nav-link" data-page="berita">Berita</a></li>
            {{-- <li><a href="{{ route('landing.home') }}#desa" onclick="handleNavClick(event, 'desa')" class="nav-link" data-page="desa">Desa</a></li> --}}
            <li><a href="{{ route('landing.home') }}#sk" onclick="handleNavClick(event, 'sk')" class="nav-link" data-page="sk">SK & Dokumen</a></li>
            <li><a href="{{ route('landing.home') }}#template" onclick="handleNavClick(event, 'template')" class="nav-link" data-page="template">Template</a></li>
            <li><a href="{{ route('landing.home') }}#tentang" onclick="handleNavClick(event, 'tentang')" class="nav-link" data-page="tentang">Tentang</a></li>
        </ul>

        <button class="hamburger" id="hamburgerBtn" onclick="toggleMobileMenu()">
            <span></span><span></span><span></span>
        </button>
    </div>

    {{-- Menu Mobile --}}
    <div class="mobile-menu" id="mobileMenu">
        <a href="{{ route('landing.home') }}" onclick="handleNavClick(event, 'beranda')" class="nav-link" data-page="beranda">Beranda</a>
        <a href="{{ route('landing.home') }}#struktur" onclick="handleNavClick(event, 'struktur')" class="nav-link" data-page="struktur">Struktur</a>
        <a href="{{ route('landing.home') }}#aplikasi" onclick="handleNavClick(event, 'aplikasi')" class="nav-link" data-page="aplikasi">Aplikasi</a>
        <a href="{{ route('landing.home') }}#berita" onclick="handleNavClick(event, 'berita')" class="nav-link" data-page="berita">Berita</a>
        {{-- <a href="{{ route('landing.home') }}#desa" onclick="handleNavClick(event, 'desa')" class="nav-link" data-page="desa">Desa</a> --}}
        <a href="{{ route('landing.home') }}#sk" onclick="handleNavClick(event, 'sk')" class="nav-link" data-page="sk">SK & Dokumen</a>
        <a href="{{ route('landing.home') }}#template" onclick="handleNavClick(event, 'template')" class="nav-link" data-page="template">Template</a>
        <a href="{{ route('landing.home') }}#tentang" onclick="handleNavClick(event, 'tentang')" class="nav-link" data-page="tentang">Tentang</a>
    </div>
</nav>

<script>
    // ==========================================
    // DETECT CURRENT PAGE TYPE
    // ==========================================
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
        
        console.log('🔗 Navigation clicked:', pageId);
        console.log('📍 Is SPA?', isSPA());
        console.log('📍 Is Laravel Route?', isLaravelRoute());
        
        // Tutup mobile menu jika terbuka
        toggleMobileMenu();
        
        // Update active nav
        updateActiveNav(pageId);
        
        if (pageId === 'beranda') {
            // Selalu ke homepage untuk beranda
            window.location.href = '{{ route("landing.home") }}';
        } else if (isSPA()) {
            // Jika di SPA, gunakan SPA navigation
            if (typeof navigateTo === 'function') {
                navigateTo(pageId);
                window.location.hash = pageId;
            } else {
                // Fallback jika navigateTo tidak ada
                window.location.href = '{{ route("landing.home") }}#' + pageId;
            }
        } else {
            // Jika di Laravel route, redirect ke SPA dengan hash
            window.location.href = '{{ route("landing.home") }}#' + pageId;
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
        
        console.log('🎯 Navbar initialized. Current page:', currentPage);
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
</script>