<div class="page" id="page-aplikasi">
    <div class="page-header">
        <div class="page-header-content">
            <h1>Aplikasi & Layanan</h1>
            <p>Sistem informasi digital PKK Kabupaten Toba</p>
            <div class="breadcrumb">
                <a onclick="navigateTo('beranda')">Beranda</a>
                <span>/</span>
                <span class="current">Aplikasi</span>
            </div>
        </div>
    </div>

    <section class="apps-landing-section">
        <div class="apps-container">

            {{-- SEARCH BAR --}}
            <div class="search-wrapper">
                <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" id="globalSearch" class="search-input" placeholder="Cari aplikasi atau layanan...">
            </div>

            {{-- SECTION: APLIKASI --}}
            <div class="category-block" id="block-aplikasi">
                <div class="section-head">
                    <span class="badge-app">APLIKASI AKTIF</span>
                    <h2 class="section-title">Sistem yang Tersedia</h2>
                    <p class="section-desc">Akses berbagai aplikasi digital untuk mendukung kinerja PKK</p>
                </div>

                <div class="carousel-wrapper">
                    <button class="nav-btn prev-btn" onclick="scrollCarousel('track-aplikasi', -1)">&#10094;</button>
                    <div class="carousel-track" id="track-aplikasi">
                        {{-- Cards will be injected here by JS --}}
                        <div class="loading-state">Memuat data aplikasi...</div>
                    </div>
                    <button class="nav-btn next-btn" onclick="scrollCarousel('track-aplikasi', 1)">&#10095;</button>
                </div>

                <div class="see-all-wrapper">
                    <button class="btn-see-all" onclick="openModal('modal-aplikasi')">
                        Lihat Semua Aplikasi
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

            <div class="section-divider"></div>

            {{-- SECTION: LAYANAN --}}
            <div class="category-block" id="block-layanan">
                <div class="section-head">
                    <span class="badge-lay">LAYANAN DIGITAL</span>
                    <h2 class="section-title">Layanan Publik</h2>
                    <p class="section-desc">Layanan mandiri yang dapat diakses oleh masyarakat dan pengurus</p>
                </div>

                <div class="carousel-wrapper">
                    <button class="nav-btn prev-btn" onclick="scrollCarousel('track-layanan', -1)">&#10094;</button>
                    <div class="carousel-track" id="track-layanan">
                        {{-- Cards will be injected here by JS --}}
                        <div class="loading-state">Memuat data layanan...</div>
                    </div>
                    <button class="nav-btn next-btn" onclick="scrollCarousel('track-layanan', 1)">&#10095;</button>
                </div>

                <div class="see-all-wrapper">
                    <button class="btn-see-all btn-layanan" onclick="openModal('modal-layanan')">
                        Lihat Semua Layanan
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

        </div>
    </section>

    {{-- MODAL: SEMUA APLIKASI --}}
    <div id="modal-aplikasi" class="modal-overlay" onclick="closeModalOutside(event, 'modal-aplikasi')">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Semua Aplikasi</h3>
                <button class="modal-close" onclick="closeModal('modal-aplikasi')">&times;</button>
            </div>
            <div class="modal-grid" id="grid-aplikasi"></div>
        </div>
    </div>

    {{-- MODAL: SEMUA LAYANAN --}}
    <div id="modal-layanan" class="modal-overlay" onclick="closeModalOutside(event, 'modal-layanan')">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Semua Layanan</h3>
                <button class="modal-close" onclick="closeModal('modal-layanan')">&times;</button>
            </div>
            <div class="modal-grid" id="grid-layanan"></div>
        </div>
    </div>
</div>

<style>
    /* CSS Khusus Halaman Aplikasi */
    .apps-landing-section { padding: 4rem 2rem; background: var(--bg-light); min-height: 80vh; }
    .apps-container { max-width: 1200px; margin: 0 auto; }

    /* Search Bar */
    .search-wrapper { position: relative; max-width: 600px; margin: 0 auto 3rem auto; }
    .search-icon { position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; color: var(--text-muted); }
    .search-input { width: 100%; padding: 1rem 1.25rem 1rem 3.5rem; border: 2px solid #e2e8f0; border-radius: 50px; font-size: 1rem; font-family: inherit; transition: all 0.3s ease; background: #fff; }
    .search-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(15, 107, 99, 0.1); }

    /* Category Block */
    .category-block { margin-bottom: 2rem; }
    .section-head { text-align: center; margin-bottom: 2.5rem; }
    .badge-app { display: inline-block; padding: 0.4rem 1rem; background: rgba(15, 107, 99, 0.1); color: var(--primary); border-radius: 20px; font-size: 0.75rem; font-weight: 700; letter-spacing: 1px; margin-bottom: 1rem; }
    .badge-lay { display: inline-block; padding: 0.4rem 1rem; background: rgba(214, 158, 46, 0.1); color: #d69e2e; border-radius: 20px; font-size: 0.75rem; font-weight: 700; letter-spacing: 1px; margin-bottom: 1rem; }
    .section-title { font-size: 2rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.5rem; }
    .section-desc { font-size: 1rem; color: var(--text-muted); max-width: 600px; margin: 0 auto; }

    /* Carousel */
    .carousel-wrapper { position: relative; display: flex; align-items: center; gap: 1rem; }
    .carousel-track { display: flex; gap: 1.5rem; overflow-x: auto; scroll-snap-type: x mandatory; scrollbar-width: none; padding: 1rem 0.5rem; flex: 1; scroll-behavior: smooth; }
    .carousel-track::-webkit-scrollbar { display: none; }
    .nav-btn { width: 44px; height: 44px; border-radius: 50%; border: 2px solid #e2e8f0; background: #fff; color: var(--text-dark); font-size: 1.2rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; flex-shrink: 0; z-index: 2; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .nav-btn:hover { background: var(--primary); color: #fff; border-color: var(--primary); transform: scale(1.05); }

    /* App Card in Carousel */
    .app-card-slide { flex: 0 0 320px; scroll-snap-align: start; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border: 1px solid rgba(0,0,0,0.04); transition: transform 0.3s ease, box-shadow 0.3s ease; display: flex; flex-direction: column; }
    .app-card-slide:hover { transform: translateY(-5px); box-shadow: 0 12px 30px rgba(0,0,0,0.1); }
    .app-card-slide.hidden { display: none !important; }
    .slide-header { padding: 2rem; background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%); text-align: center; position: relative; overflow: hidden; }
    .slide-header.layanan-bg { background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); }
    .slide-icon { width: 70px; height: 70px; border-radius: 16px; background: linear-gradient(135deg, var(--primary), var(--primary-light)); display: flex; align-items: center; justify-content: center; margin: 0 auto; box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
    .slide-icon.lay-icon { background: linear-gradient(135deg, #d97706, #f59e0b); }
    .slide-icon img { width: 40px; height: 40px; object-fit: contain; filter: brightness(0) invert(1); }
    .slide-icon span { color: #fff; font-weight: 800; font-size: 1.2rem; }
    .slide-body { padding: 1.5rem; flex: 1; display: flex; flex-direction: column; }
    .slide-name { font-size: 1.25rem; font-weight: 800; color: var(--primary); margin-bottom: 0.25rem; }
    .slide-name.lay-name { color: #d97706; }
    .slide-fullname { font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.75rem; font-weight: 500; }
    .slide-desc { font-size: 0.9rem; color: #4a5568; line-height: 1.5; flex: 1; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    .slide-link { display: block; text-align: center; margin-top: 1rem; padding: 0.75rem; background: var(--primary); color: #fff; text-decoration: none; border-radius: 10px; font-weight: 600; transition: background 0.3s; }
    .slide-link.lay-link { background: #d97706; }
    .slide-link:hover { opacity: 0.9; }

    /* See All Button */
    .see-all-wrapper { text-align: center; margin-top: 2rem; }
    .btn-see-all { display: inline-flex; align-items: center; gap: 8px; padding: 12px 28px; background: var(--primary); color: #fff; border: none; border-radius: 50px; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-family: inherit; }
    .btn-see-all:hover { background: var(--primary-light); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(15, 107, 99, 0.3); }
    .btn-layanan { background: #d97706; }
    .btn-layanan:hover { background: #b45309; box-shadow: 0 6px 20px rgba(217, 119, 6, 0.3); }

    .section-divider { height: 1px; background: #e2e8f0; margin: 3rem 0; }
    .loading-state { width: 100%; text-align: center; padding: 2rem; color: var(--text-muted); }

    /* Modal */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 2rem; }
    .modal-overlay.active { display: flex; }
    .modal-content { background: #fff; border-radius: 20px; width: 100%; max-width: 900px; max-height: 85vh; display: flex; flex-direction: column; box-shadow: 0 25px 50px rgba(0,0,0,0.25); animation: modalSlideUp 0.3s ease; overflow: hidden; }
    @keyframes modalSlideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    .modal-header { padding: 1.5rem 2rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; }
    .modal-header h3 { font-size: 1.25rem; font-weight: 700; color: var(--text-dark); margin: 0; }
    .modal-close { width: 36px; height: 36px; border-radius: 50%; border: none; background: #e2e8f0; font-size: 1.5rem; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease; }
    .modal-close:hover { background: #cbd5e1; color: var(--text-dark); }
    .modal-grid { padding: 2rem; display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem; overflow-y: auto; }
    .modal-card { display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f8fafc; border-radius: 12px; text-decoration: none; color: inherit; border: 1px solid #e2e8f0; transition: all 0.3s ease; }
    .modal-card:hover { background: #fff; border-color: var(--primary); box-shadow: 0 4px 12px rgba(15, 107, 99, 0.1); transform: translateX(5px); }
    .modal-card.lay-card:hover { border-color: #d97706; box-shadow: 0 4px 12px rgba(217, 119, 6, 0.1); }
    .modal-icon { width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, var(--primary), var(--primary-light)); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .modal-icon.lay-icon { background: linear-gradient(135deg, #d97706, #f59e0b); }
    .modal-icon img { width: 24px; height: 24px; object-fit: contain; filter: brightness(0) invert(1); }
    .modal-icon span { color: #fff; font-weight: 800; font-size: 0.9rem; }
    .modal-info h4 { font-size: 0.95rem; font-weight: 700; color: var(--text-dark); margin: 0 0 0.25rem 0; }
    .modal-info p { font-size: 0.8rem; color: var(--text-muted); margin: 0; }

    @media (max-width: 768px) {
        .app-card-slide { flex: 0 0 280px; }
        .nav-btn { display: none; } /* Hide arrows on mobile, rely on swipe */
        .modal-grid { grid-template-columns: 1fr; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let appsData = { aplikasi: [], layanan: [] };
    let autoScrollIntervals = {};

    // 1. Fetch Data
    fetch('/api/v1/applications')
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                appsData.aplikasi = result.data.aplikasi.active || [];
                appsData.layanan = result.data.layanan.active || [];

                renderSection('aplikasi', appsData.aplikasi);
                renderSection('layanan', appsData.layanan);

                startAutoScroll('track-aplikasi');
                startAutoScroll('track-layanan');
            }
        })
        .catch(err => console.error('Error loading apps:', err));

    // 2. Render Function
    function renderSection(type, data) {
        const track = document.getElementById(`track-${type}`);
        const grid = document.getElementById(`grid-${type}`);
        const isLayanan = type === 'layanan';

        if (data.length === 0) {
            track.innerHTML = '<div class="loading-state">Tidak ada data tersedia.</div>';
            grid.innerHTML = '<div class="loading-state">Tidak ada data tersedia.</div>';
            return;
        }

        // Render Carousel (Max 4 items)
        track.innerHTML = data.slice(0, 4).map(app => createCardHTML(app, isLayanan)).join('');

        // Render Modal Grid (All items)
        grid.innerHTML = data.map(app => createModalCardHTML(app, isLayanan)).join('');
    }

    function createCardHTML(app, isLayanan) {
        const iconHtml = app.icon
            ? `<img src="/storage/${app.icon}" alt="${app.short_name}">`
            : `<span>${app.short_name.substring(0, 2)}</span>`;
        const bgClass = isLayanan ? 'layanan-bg' : '';
        const iconClass = isLayanan ? 'lay-icon' : '';
        const nameClass = isLayanan ? 'lay-name' : '';
        const linkClass = isLayanan ? 'lay-link' : '';

        return `
        <div class="app-card-slide" data-name="${app.name.toLowerCase()}" data-short="${app.short_name.toLowerCase()}">
            <div class="slide-header ${bgClass}">
                <div class="slide-icon ${iconClass}">${iconHtml}</div>
            </div>
            <div class="slide-body">
                <h3 class="slide-name ${nameClass}">${app.short_name}</h3>
                <p class="slide-fullname">${app.name}</p>
                <p class="slide-desc">${app.description || 'Tidak ada deskripsi.'}</p>
                <a href="${app.url && app.url !== '#' ? app.url : '#'}" target="_blank" class="slide-link ${linkClass}">Akses ${app.short_name}</a>
            </div>
        </div>`;
    }

    function createModalCardHTML(app, isLayanan) {
        const iconHtml = app.icon
            ? `<img src="/storage/${app.icon}" alt="${app.short_name}">`
            : `<span>${app.short_name.substring(0, 2)}</span>`;
        const iconClass = isLayanan ? 'lay-icon' : '';
        const cardClass = isLayanan ? 'lay-card' : '';

        return `
        <a href="${app.url && app.url !== '#' ? app.url : '#'}" target="_blank" class="modal-card ${cardClass}">
            <div class="modal-icon ${iconClass}">${iconHtml}</div>
            <div class="modal-info">
                <h4>${app.short_name}</h4>
                <p>${app.name}</p>
            </div>
        </a>`;
    }

    // 3. Carousel Logic
    window.scrollCarousel = function(trackId, direction) {
        const track = document.getElementById(trackId);
        const scrollAmount = 340; // Approx card width + gap
        track.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
    };

    function startAutoScroll(trackId) {
        const track = document.getElementById(trackId);
        if (!track) return;

        autoScrollIntervals[trackId] = setInterval(() => {
            if (track.scrollLeft + track.clientWidth >= track.scrollWidth - 10) {
                track.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                window.scrollCarousel(trackId, 1);
            }
        }, 5000);

        track.addEventListener('mouseenter', () => clearInterval(autoScrollIntervals[trackId]));
        track.addEventListener('mouseleave', () => startAutoScroll(trackId));
    }

    // 4. Search Logic
    document.getElementById('globalSearch').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase().trim();
        const allCards = document.querySelectorAll('.app-card-slide');

        allCards.forEach(card => {
            const name = card.getAttribute('data-name');
            const short = card.getAttribute('data-short');
            if (name.includes(term) || short.includes(term)) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    });

    // 5. Modal Logic
    window.openModal = function(modalId) {
        document.getElementById(modalId).classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    window.closeModal = function(modalId) {
        document.getElementById(modalId).classList.remove('active');
        document.body.style.overflow = '';
    };

    window.closeModalOutside = function(event, modalId) {
        if (event.target.id === modalId) {
            window.closeModal(modalId);
        }
    };
});
</script>
