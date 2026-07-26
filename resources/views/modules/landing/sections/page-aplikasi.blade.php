<div class="page active" id="page-aplikasi">
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
                <input type="text" id="globalSearch" class="search-input" placeholder="Cari aplikasi atau layanan (nama panjang/pendek)...">
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
                        <div class="loading-state">Memuat data aplikasi...</div>
                    </div>
                    <button class="nav-btn next-btn" onclick="scrollCarousel('track-aplikasi', 1)">&#10095;</button>
                </div>

                <div class="see-all-wrapper">
                    <button class="btn-see-all-apps" onclick="openModal('modal-aplikasi')">
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
                        <div class="loading-state">Memuat data layanan...</div>
                    </div>
                    <button class="nav-btn next-btn" onclick="scrollCarousel('track-layanan', 1)">&#10095;</button>
                </div>

                <div class="see-all-wrapper">
                    <button class="btn-see-all-apps btn-layanan" onclick="openModal('modal-layanan')">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    let appsData = { aplikasi: [], layanan: [] };
    let autoScrollIntervals = {};

    // 1. Fetch Data dari API
    fetch('/api/v1/applications')
        .then(res => res.json())
        .then(result => {
            if (result.success) {

                appsData.aplikasi = [
                    ...(result.data.aplikasi.active || []),
                    ...(result.data.aplikasi.maintenance || []),
                    ...(result.data.aplikasi.development || [])
                ];

                appsData.layanan = [
                    ...(result.data.layanan.active || []),
                    ...(result.data.layanan.maintenance || []),
                    ...(result.data.layanan.development || [])
                ];

                renderSection('aplikasi', appsData.aplikasi);
                renderSection('layanan', appsData.layanan);

                startAutoScroll('track-aplikasi');
                startAutoScroll('track-layanan');
            }
        })
        .catch(err => console.error('Error loading apps:', err));

    // 2. Fungsi Render
    function renderSection(type, data) {
        const track = document.getElementById(`track-${type}`);
        const grid = document.getElementById(`grid-${type}`);
        const isLayanan = type === 'layanan';

        if (data.length === 0) {
            track.innerHTML = '<div class="loading-state">Tidak ada data tersedia.</div>';
            grid.innerHTML = '<div class="loading-state">Tidak ada data tersedia.</div>';
            return;
        }

        // Render Carousel
        track.innerHTML = data.map(app => createCardHTML(app, isLayanan)).join('');

        // Render Modal Grid
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
        const colorClass = `app-color-${app.color_index || 0}`;

        // Render features jika ada
        const featuresHtml = app.features && app.features.length > 0
            ? `<ul class="app-features">${app.features.slice(0, 4).map(f => `
                <li>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    ${f}
                </li>
            `).join('')}</ul>`
            : '';

        return `
        <div class="app-card-slide ${colorClass}" data-name="${app.name.toLowerCase()}" data-short="${app.short_name.toLowerCase()}">
            <div class="slide-header ${bgClass}">
                <div class="slide-icon ${iconClass}">${iconHtml}</div>
            </div>
            <div class="slide-body">
                <h3 class="slide-name ${nameClass}">${app.short_name}</h3>
                <p class="slide-fullname">${app.name}</p>
                <p class="slide-desc">${app.description || 'Tidak ada deskripsi.'}</p>
                ${featuresHtml}
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
        const colorClass = `app-color-${app.color_index || 0}`;

        return `
        <a href="${app.url && app.url !== '#' ? app.url : '#'}" target="_blank" class="modal-card ${cardClass} ${colorClass}">
            <div class="modal-icon ${iconClass}">${iconHtml}</div>
            <div class="modal-info">
                <h4>${app.short_name}</h4>
                <p>${app.name}</p>
            </div>
        </a>`;
    }

    // 3. Carousel Logic yang Lebih Baik & Tidak Bertabrakan
    window.scrollCarousel = function(trackId, direction) {
        const track = document.getElementById(trackId);
        if (!track) return;

        const card = track.querySelector('.app-card-slide');
        if (!card) return;

        // Hitung lebar card + gap (1.5rem = 24px) secara dinamis
        const scrollAmount = card.offsetWidth + 24;

        // Hentikan auto-scroll sementara agar tidak bertabrakan dengan kontrol user
        resetAutoScroll(trackId);

        track.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
    };

    function startAutoScroll(trackId) {
        const track = document.getElementById(trackId);
        if (!track) return;

        const cards = track.querySelectorAll('.app-card-slide');
        if (cards.length <= 1) return; // Jangan auto-scroll jika hanya 1 item

        // Hapus interval lama jika ada
        if (autoScrollIntervals[trackId]) {
            clearInterval(autoScrollIntervals[trackId]);
        }

        autoScrollIntervals[trackId] = setInterval(() => {
            const card = track.querySelector('.app-card-slide');
            if (!card) return;

            const scrollAmount = card.offsetWidth + 24;
            const maxScroll = track.scrollWidth - track.clientWidth;

            // Jika sudah di akhir, kembali ke awal dengan smooth (Looping)
            if (track.scrollLeft >= maxScroll - 10) {
                track.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            }
        }, 4000); // 4 detik durasi yang nyaman dan masuk akal
    }

    function resetAutoScroll(trackId) {
        if (autoScrollIntervals[trackId]) {
            clearInterval(autoScrollIntervals[trackId]);
        }
        // Mulai lagi setelah 5 detik agar user punya waktu melihat hasil kliknya
        setTimeout(() => {
            startAutoScroll(trackId);
        }, 5000);
    }

    // Pause auto-scroll saat mouse berada di atas carousel (User sedang melihat/mengklik)
    ['track-aplikasi', 'track-layanan'].forEach(trackId => {
        const track = document.getElementById(trackId);
        if (!track) return;

        track.addEventListener('mouseenter', () => {
            if (autoScrollIntervals[trackId]) {
                clearInterval(autoScrollIntervals[trackId]);
            }
        });

        track.addEventListener('mouseleave', () => {
            startAutoScroll(trackId);
        });
    });

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
